# fms-odata-spec-php

Shared PHP types and spec definitions for the Claris FileMaker Server OData API.

This package mirrors the public API concepts, constants, models, feature
matrices, exceptions, and pure helper behavior of the companion TypeScript
and Python packages:

- **TypeScript**: [`@fms-odata/spec-ts`](https://www.npmjs.com/package/@fms-odata/spec-ts)
- **Python**: [`fms-odata-spec`](https://pypi.org/project/fms-odata-spec/)

It is a **specification/types library**, not an HTTP client. There is no
runtime dependency, no HTTP client, no validation framework, and no async
framework. All models are `final readonly class` DTOs or string-backed enums.

## Requirements

- PHP 8.2 or later

## Installation

```bash
composer require fsans/fms-odata-spec-php
```

> The package is not yet published to Packagist. Until it is, install from
> source or a VCS tag.

## Zero runtime dependencies

The `require` section of `composer.json` contains only `php: ^8.2`. There
are no Composer runtime dependencies. Development dependencies (PHPUnit,
PHPStan) are in `require-dev`.

## Module inventory

| Domain | Key types | Helper class |
| ------ | --------- | ------------ |
| Versions | `FMVersionMajor`, `FMVersionStatus`, `ODataProtocolVersion`, `FMFeatureFlags`, `FMQueryOptionFlags`, `FMVersionInfo` | `Versions` |
| Auth | `FMAuthScheme`, `FMBasicAuthConfig`, `FMOAuthAuthConfig`, `FMAuthHeaders` | `Auth` |
| Endpoints | `HttpMethod`, `EndpointCategory`, `FMEndpoint` | `Endpoints` |
| Query | `QueryOption`, `FilterComparisonOp`, `FilterLogicalOp`, `FilterStringFunction`, `FilterDateTimeFunction`, `FilterNumericFunction`, `SortDirection`, `AggregateFunction`, `OrderByClause`, `AggregateExpression`, `GroupByExpression`, `ApplyTransformation`, `QueryParams`, `ODataCollection`, `ODataEntity`, `QueryResult` | `QueryLiterals` |
| Metadata | `FMBooleanAnnotations`, `FMValueAnnotations`, `FMAnnotations`, `EdmProperty`, `EdmEntityType`, `EdmEntitySet`, `EdmAction`, `EdmEnumMember`, `EdmEnumType`, `ODataMetadata`, `ImmutableIdType`, `FMServerVersion` | `Metadata` |
| Scripts | `ScriptScope`, `ScriptOptions`, `ScriptNameId`, `ScriptFmsidId`, `ScriptIdentifier`, `ScriptResultInner`, `ScriptResultEnvelope`, `ScriptResult`, `ScriptDescriptor` | `Scripts` |
| Containers | `ContainerBinaryMimeType`, `ContainerEncoding`, `ContainerUploadInput`, `ContainerDownload`, `FMContainerAnnotations` | `Containers` |
| Batch | `BatchOpType`, `BatchOperation`, `Changeset`, `BatchRequest`, `BatchOpResult`, `BatchResult`, `BatchHandle` | `Batch` |
| Webhooks | `WebhookOperation`, `WebhookCreateParams`, `WebhookData`, `WebhookInvokeParams`, `WebhookCreateResult` | `Webhooks` |
| Schema | `FMFieldType`, `FMFieldDefinition`, `CreateTableParams`, `AddFieldsParams`, `ParsedFieldType` | `Schema` |
| Errors | `ODataErrorDetail`, `ODataErrorInnerError`, `ODataErrorInner`, `ODataErrorBody`, `RequestRef`, `FMODataError`, `FMScriptError`, `FMAuthError`, `FMNotFoundError`, `FMValidationError` | `Errors` |

## Usage examples

### Feature checks

```php
use FmsOData\Spec\Versions\Versions;
use FmsOData\Spec\Versions\FMVersionMajor;

// Check if a feature is available in a version
$hasWebhooks = Versions::hasFeature('22', 'webhooks'); // true
$hasScriptListing = Versions::hasFeature('26', 'scriptListing'); // true

// Check query option availability
$hasApply = Versions::hasQueryOption('22', 'apply'); // true

// Find the minimum version that supports a feature
$minVersion = Versions::minVersionForFeature('webhooks'); // '22'
```

### Basic auth

```php
use FmsOData\Spec\Auth\Auth;
use FmsOData\Spec\Auth\FMAuthHeaders;
use FmsOData\Spec\Versions\ODataProtocolVersion;

$authValue = Auth::basicAuth('admin', 'secret');
$headers = new FMAuthHeaders(
    authorization: $authValue,
    odataVersion: ODataProtocolVersion::V4_01,
    odataMaxVersion: ODataProtocolVersion::V4_01,
);
$wireHeaders = $headers->toArray();
// ['Authorization' => 'Basic YWRtaW46c2VjcmV0', 'OData-Version' => '4.01', 'OData-MaxVersion' => '4.01']
```

### Server version parsing

```php
use FmsOData\Spec\Metadata\Metadata;

$xml = '<Annotation Term="Org.OData.Core.V1.ProductVersion" String="26.0.1.500"/>';
$version = Metadata::parseServerVersion($xml);
// FMServerVersion(major: 26, minor: 0, patch: 1, raw: '26.0.1.500')

$major = Metadata::extractMajorVersionFromMetadata($xml); // '26'
```

### Literal formatting

```php
use FmsOData\Spec\Query\QueryLiterals;

QueryLiterals::formatLiteral("O'Brien"); // "'O''Brien'"
QueryLiterals::formatLiteral(42);        // "42"
QueryLiterals::formatLiteral(true);      // "true"
QueryLiterals::formatLiteral(new \DateTime('2026-06-30 12:34:56', new \DateTimeZone('UTC')));
// "2026-06-30T12:34:56Z"
```

## PHP adaptation notes

- **Enums**: Closed literal sets use PHP 8.1+ string-backed enums. Enum case
  identifiers are idiomatic uppercase (e.g. `FMVersionMajor::V26`), while
  `->value` returns the exact OData/FileMaker wire value (e.g. `'26'`).
- **Readonly DTOs**: All immutable models are `final readonly class` with
  constructor property promotion.
- **PHPStan generics**: `ODataCollection<T>`, `ODataEntity<T>`,
  `QueryResult<T>`, `BatchOpResult<T>`, and `BatchHandle<T>` use PHPDoc
  `@template` annotations for generic type inference.
- **Wrapped `ODataEntity`**: The TS intersection type
  `{ '@odata.context': string; '@odata.etag'?: string } & T` has no PHP
  equivalent. The entity payload is carried in the `$entity` property.
  Callers access fields via `$envelope->entity->field`.
- **Transport-neutral async placeholders**: The TS `Promise<T>` and
  `AbortSignal` have no standard PHP equivalent. `BatchHandle` uses an
  opaque `mixed $handle` and `ScriptOptions` uses an optional `?object`
  cancel token. This package does not act on either.
- **Wire serialization**: DTOs whose PHP property names differ from wire
  keys provide `toODataArray()` (and `fromODataArray()` where parsing
  exists). Exact OData keys are preserved, including `@odata.context`,
  `webhookID`, `rowIDs`, `tableName`, `notifySchemaChanges`,
  `externalSecurePath`, and `global`.

## Independent versioning

This package is versioned independently of the TS and Python packages. The
current specification/API version is exposed through
`FmsOData\Spec\Package::VERSION` (currently `2.0.1`). Composer versions
come from VCS tags, not from `composer.json`.

The planned tag convention is `php-vX.Y.Z` (e.g. `php-v2.0.1`), distinct
from the TS `vMAJOR.MINOR.PATCH` tags and the Python `py-vX.Y.Z` tags.

## Development

```bash
cd packages/fms-odata-spec-php
composer install
composer test           # PHPUnit
composer analyse        # PHPStan level max
composer check          # tests + static analysis
```

## License

MIT -- see [LICENSE](LICENSE).
