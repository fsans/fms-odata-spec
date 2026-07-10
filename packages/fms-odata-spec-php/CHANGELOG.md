# Changelog

All notable changes to this package are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.1] - 2026-07-10

### Added

- First PHP mirror of the FileMaker OData specification, parity with
  `@fms-odata/spec-ts` 2.0.1 and `fms-odata-spec` 2.0.1 (Python).
- All public domains ported: versions, authentication, endpoints, query
  options, metadata, scripts, containers, batch, webhooks, schema
  modification (DDL), and errors.
- String-backed enums for all closed literal sets (FMVersionMajor,
  FMVersionStatus, ODataProtocolVersion, FMAuthScheme, HttpMethod,
  EndpointCategory, query options and operators, filter function groups,
  SortDirection, AggregateFunction, BatchOpType, ContainerBinaryMimeType,
  ContainerEncoding, ScriptScope, WebhookOperation, FMFieldType,
  ImmutableIdType).
- Readonly DTOs for all specification, request, response, and envelope
  models with constructor property promotion.
- PHPStan `@template` generics on ODataCollection, ODataEntity, QueryResult,
  BatchOpResult, and BatchHandle.
- Wire serialization via `toODataArray()` / `fromODataArray()` preserving
  exact OData/FileMaker keys including `@odata.context`, `webhookID`,
  `rowIDs`, `tableName`, `notifySchemaChanges`, `externalSecurePath`, and
  `global`.
- Pure helper facades: Versions, Auth, Endpoints, QueryLiterals, Metadata,
  Scripts, Containers, Batch, Webhooks, Schema, Errors.
- Version feature/query-option matrix matching the TS `FM_VERSION_MATRIX`
  exactly, including the 2.0.1 corrections for DDL and webhook endpoints.
- Four-strategy $metadata version detection (regex-based, no ext-dom
  required).
- PHPUnit test suite covering every public enum case, version matrix
  entries, feature/query helpers, auth encoding (ASCII and Unicode), token
  normalization, endpoint completeness and version filtering, DDL/webhook
  metadata corrections, literal escaping and all supported literal types,
  metadata version-detection strategies, script paths/bodies/response
  fallbacks, all five container MIME signatures, ASCII/non-ASCII content
  disposition, base64 round trips, boundary prefix and uniqueness, webhook
  paths and serialization round trips, schema serialization and field-type
  parsing, and exception inheritance and statuses.
- Reflection-based public API test verifying all expected public
  classes/enums autoload and `Package::VERSION`.
- PHPStan static analysis at level max (no baseline).
- CI workflow covering PHP 8.2 through 8.5.

### PHP-specific modeling choices

- Enums use idiomatic uppercase case identifiers; `->value` returns exact
  wire values.
- `ODataEntity<T>` uses a wrapper shape (`$entity` property) instead of the
  TS intersection type.
- `BatchHandle<T>` uses an opaque `mixed $handle` instead of a Promise.
- `ScriptOptions` uses an optional `?object $cancelToken` instead of
  `AbortSignal`.
- `FMODataError::$odataCode` (string) avoids the type conflict with
  `Exception::$code` (int).
- `FMFieldDefinition::$global` maps to the wire key `global`.
- Composer versions come from VCS tags; `Package::VERSION` exposes `2.0.1`.
