# Changelog

All notable changes to `fms-odata-spec` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

This package mirrors `@fms-odata/spec-ts` and is versioned independently; the
2.0.1 release ships the same DDL/webhook corrections as the TypeScript
package.

## [2.0.1] - 2026-07-02

### Fixed

- **DDL endpoint URL format.** `addFields`, `deleteTable`, `deleteField`,
  `createIndex`, and `deleteIndex` now use slash-separated paths
  (`FileMaker_Tables/{tableName}`) instead of OData key-parentheses
  (`FileMaker_Tables('{tableName}')`), which FileMaker Server rejects with
  error `-1010` ("The entity name must be specified in the URL").
- **`createIndex` body shape.** The table is now a URL path segment
  (`FileMaker_Indexes/{tableName}`) and the body is
  `{ indexName: <fieldName> }`. The previous form (`POST /FileMaker_Indexes`
  with the table only in the body) returned error `-1003` ("Unsupported OData
  operation").
- **`deleteIndex` URL.** Now `FileMaker_Indexes/{tableName}/{fieldName}`
  instead of `FileMaker_Indexes('{indexName}')`.
- **Webhook HTTP methods.** `getAllWebhooks` and `getWebhook` are now `GET`
  (were incorrectly `POST`).
- **Webhook id in URL path.** `getWebhook`, `deleteWebhook`, and
  `invokeWebhook` now carry the webhook id as an OData function argument in
  the URL (`Webhook.Get({webhookId})`), not in the request body.
- **Webhook delete endpoint name.** Renamed `Webhook.Remove` →
  `Webhook.Delete` (FMS does not expose `Webhook.Remove`).
- **`Webhook.Invoke` body.** Now documented as requiring
  `{ rowIDs: [...] }`; an empty body is rejected by FMS with a JSON syntax
  error. An empty `rowIDs` list is valid and triggers the webhook for all
  pending records.

### Added

- `WebhookInvokeParams` dataclass: `{ rowIDs: List[Union[str, int]] }` for the
  `Webhook.Invoke` request body, with `to_odata_dict()` emitting the
  camelCase wire key `rowIDs`.
- `WebhookCreateResult` dataclass: mirrors the
  `{ webhookResult: { webhookID: n } }` shape returned by `Webhook.Add`, with
  `from_odata_dict()` / `to_odata_dict()` round-trip helpers.
- `webhook_id: Optional[int]` field on `WebhookData` (the integer primary key
  FMS uses in responses, emitted as `webhookID`). The legacy
  `id: Optional[str]` field is retained for backward compatibility.
- Optional `id: Optional[int] = None` parameter on
  `webhook_path(database, operation, id=None)` to express the
  `Webhook.{op}({id})` URL pattern.
- New tests asserting the corrected DDL/webhook endpoint paths and methods,
  the `Delete` (not `Remove`) operation, `WebhookInvokeParams` /
  `WebhookCreateResult` round-trips, and the `webhook_id` field. Test count
  rose from 173 to 191.

### Changed

- `WebhookOperation` literal: `"Remove"` → `"Delete"`.
- `WebhookCreateResult`, `WebhookInvokeParams` now re-exported from the
  package root (`fms_odata_spec`).

### Verified

- `pytest`: 191 passed.
- All corrections confirmed by curl against FileMaker Server 26.0.1 and
  cross-referenced with the official Claris OData guide
  (https://help.claris.com/en/odata-guide/content/webhook-options.html) and
  the reference implementation at
  https://github.com/fsans/fms-odata-webhooks.

## [2.0.0] - 2026-06-30

### Changed

- Spec overhaul: dropped FileMaker 19.x support; minimum version is now
  Claris 2023 (v20.x).
- Mirrored the `@fms-odata/spec-ts` 2.0.0 surface as stdlib dataclasses
  (types + pure helpers only; no HTTP client, no validation framework).
- OData protocol version documented as 4.0 for v20.x and partial 4.01 from
  v21.1 onward (intermediate conformance level with exceptions).
- Corrected feature history across versions (Claris 2023/2024/2025/2026).
- Added Claris 2026 (v26.x) as the current version.

### Added

- Version constants and feature matrix for v20, v21, v22, v26.
- Endpoint descriptors (`FM_ENDPOINTS`) covering discovery, metadata, query,
  CRUD, batch, scripts, containers, schema (DDL), and webhooks.
- Authentication types (Basic + OAuth bearer) with `basic_auth` /
  `bearer_auth` helpers.
- Query option, metadata annotation, container, batch, and error types.
- Webhook types (`WebhookCreateParams`, `WebhookData`, `WebhookOperation`,
  `webhook_path`).
- Schema modification (DDL) types (`CreateTableParams`, `AddFieldsParams`,
  `FMFieldDefinition`, `parse_field_type`).
- `ODataEntity[T]` wrapping dataclass for collection responses.
- LICENSE bundled in both wheel (`dist-info/licenses/LICENSE`) and sdist.

### Removed

- FileMaker 19.x version support and associated feature flags.

[2.0.1]: https://github.com/fsans/fms-odata-spec/releases/tag/py-v2.0.1
[2.0.0]: https://github.com/fsans/fms-odata-spec/releases/tag/py-v2.0.0
