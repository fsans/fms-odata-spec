# Changelog

All notable changes to `@fms-odata/spec-ts` are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
  `{ rowIDs: Array<string | number> }`; an empty body is rejected by FMS with
  a JSON syntax error. An empty `rowIDs` array is valid and triggers the
  webhook for all pending records.

### Added

- `WebhookInvokeParams` type: `{ rowIDs: Array<string | number> }` for the
  `Webhook.Invoke` request body.
- `WebhookCreateResult` type: `{ webhookResult: { webhookID: number } }`,
  the shape FMS returns from `Webhook.Add`.
- `webhookID?: number` field on `WebhookData` (the integer primary key FMS
  uses in responses). The legacy `id?: string` field is retained for backward
  compatibility.
- Optional `id?: number` parameter on `webhookPath(database, operation, id?)`
  to express the `Webhook.{op}({id})` URL pattern.

### Changed

- `WebhookOperation` type: `'Remove'` → `'Delete'`.

### Verified

- All corrections confirmed by curl against FileMaker Server 26.0.1 and
  cross-referenced with the official Claris OData guide
  (https://help.claris.com/en/odata-guide/content/webhook-options.html) and
  the reference implementation at
  https://github.com/fsans/fms-odata-webhooks.

## [2.0.0] - 2026-06-30

### Changed

- Spec overhaul: dropped FileMaker 19.x support; minimum version is now
  Claris 2023 (v20.x).
- OData protocol version documented as 4.0 for v20.x and partial 4.01 from
  v21.1 onward (intermediate conformance level with exceptions).
- Corrected feature history across versions (Claris 2023/2024/2025/2026).
- Added Claris 2026 (v26.x) as the current version.

### Added

- Version constants and feature matrix for v20, v21, v22, v26.
- Endpoint descriptors (`FM_ENDPOINTS`) covering discovery, metadata, query,
  CRUD, batch, scripts, containers, schema (DDL), and webhooks.
- Authentication types (Basic + OAuth bearer).
- Query option, metadata annotation, container, batch, and error types.
- Webhook types (`WebhookCreateParams`, `WebhookData`, `WebhookOperation`,
  `webhookPath`).
- Schema modification (DDL) types (`CreateTableParams`, `AddFieldsParams`,
  `FMFieldDefinition`, `parse_field_type`).

### Removed

- FileMaker 19.x version support and associated feature flags.

[2.0.1]: https://github.com/fsans/fms-odata-spec/releases/tag/v2.0.1
[2.0.0]: https://github.com/fsans/fms-odata-spec/releases/tag/v2.0.0
