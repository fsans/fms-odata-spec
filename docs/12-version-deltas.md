# 12 — Version Deltas

This document tracks differences in the OData API across FileMaker Server versions. The spec covers Claris FileMaker 2023 (v20.x) through Claris FileMaker 2026 (v26.x, current) and reserves a section for future changes.

## Version timeline

| Version | Release name | OData protocol | Status |
|---------|-------------|-----------------|--------|
| 20.x | Claris FileMaker 2023 | OData 4.0 | Supported |
| 21.x | Claris FileMaker 2024 | OData 4.01 (partial) | Supported |
| 22.x | Claris FileMaker 2025 | OData 4.01 (partial) | Supported |
| 26.x | Claris FileMaker 2026 | OData 4.01 (partial) | Current (primary reference) |
| Future | — | — | Reserved |

Note: Claris uses year-based naming (2023, 2024, 2025, 2026). The internal version numbers (20.x, 21.x, 22.x, 26.x) are detected from the `$metadata` product version annotation. Claris jumped from 22 to 26 to align with the calendar year — there is no 23.x/24.x/25.x.

## Claris FileMaker 2023 (20.x) — baseline

This is the baseline supported version. All features present here are available in all subsequent versions.

### Supported features

- Service document and `$metadata`
- Database listing, table listing
- Record CRUD (GET, POST, PATCH, PUT, DELETE)
- Query options: `$filter`, `$select`, `$orderby`, `$top`, `$skip`, `$expand`, `$count`
- Batch requests (`$batch`)
- Script execution (`Script.<name>`)
- Container field upload (binary and base64)
- Container field download (`/$value`)
- Record references (`$ref`)
- HTTP Basic authentication
- JSON and Atom/XML response formats
- Standard `Prefer` header values
- FileMaker-specific `Prefer` header values (`fmodata.basic-timestamp`, `fmodata.gmtoffset`, `fmodata.entity-ids`)
- Schema modification (create/delete tables, fields, indexes)
- Server-driven paging with `@odata.nextLink` (default page size: 10,000 records)
- Manual pagination via `$top` and `$skip`
- `Prefer: odata.maxpagesize=N` header to reduce page size

### Not supported in 20.x

- `$search` query option
- Lambda operators (`any`, `all`)
- `fractionalseconds()`, `isof()`, geo functions
- `$apply` query option
- `$compute` query option
- `/$count` suffix form
- `$orderby` with built-in functions
- Webhooks
- Type casting
- Parameterized filter expressions
- Simplified query syntax (no `$` prefix)
- AI annotations
- Schema comments (`FMComment`)
- Script execution by FMSID
- Immutable ID URL forms (`FMTID:`, `FMFID:`, `FMSID:`)
- OAuth identity provider login

## Claris FileMaker 2024 (21.x)

### OData 4.01 transition

FileMaker Server 2024 (v21.1) transitioned to **partial OData 4.01** at **intermediate conformance level, with some exceptions**. OData 4.01 was designed by OASIS to allow partial adoption, and Claris selectively integrated high-utility features rather than rewriting for full compliance.

The URL version segment remains `v4` for all versions.

### Added

- **Simplified Query Syntax**: system query options no longer require `$` prefix (e.g., `select` or `filter` instead of `$select` or `$filter`)
- **Type casting** in filter and select expressions (e.g., `/Edm.String`, `/Edm.Int32`, `/Edm.Date`)
- **Parameterized filter expressions**: placeholders for data variables in `$filter` (e.g., `$filter=Name eq @p1&@p1=Test`)
- **Advanced query nesting**: nesting within `$select`, `$expand`, and `$crossjoin`
- **Batch preference inheritance**: headers set at batch request level cascade to individual operations inside the batch
- **Script execution by FMSID** (`Script.FMSID:<id>`): run scripts by internal ID instead of name
- **Schema comments** (`FMComment` annotation): table and field comments from the Manage Database dialog exposed in `$metadata`
- **AI annotations** (`AIAnnotation` annotation): additional field metadata for AI/LLM integrations
- **OAuth identity provider login**: external identity provider support via OAuth flow
- **PSoS from OData scripts**: scripts invoked via OData can now use Perform Script on Server

### Key limitations

- **Parameterization is `$filter`-only**: cannot parameterize field names in `$select` or aggregation arguments in `$apply`
- **`@odata.count` vs `@count` toggle**: in full OData 4.01, `@odata.count` was replaced by `@count`. FileMaker lets you pass version headers to toggle between 4.0 and 4.01 behavior to avoid breaking legacy integrations.
- **No layout engine**: unlike the Data API, OData bypasses the layout schema layer entirely, interacting directly with base Table Occurrences on the relationship graph.
- **Missing broad function evaluations in `$select`**: the community has noted that lacking broad function evaluations in `$select` prevents OData from fully replacing ODBC for some use cases.

### Changed

- No breaking changes from 20.x baseline.

## Claris FileMaker 2025 (22.x)

### Added

- **`$apply` query option** (v22.0): server-side aggregation with `aggregate()` and `groupby()`
  - Aggregate functions: `sum`, `min`, `max`, `average`, `countdistinct`
  - `AS` aliasing for computed fields and grouping
  - Aggregation works in `$select` only, not in `$filter`
- **Webhooks** (v22.0.4): `Webhook.Add`, `Webhook.Remove`, `Webhook.Get`, `Webhook.GetAll`, `Webhook.Invoke`
  - Subscribe to record changes (create/update/delete) and table schema changes
  - `queryHeaders` parameter controls how webhook payload is generated
- **Metadata filtering** (v22.0.4): request specific metadata subsets instead of entire schema, dramatically reducing response times for large databases
- **`Prefer: fmodata.include-specialcolumns`** header (v22.0.4): returns `ROWID`/`ROWMODID` system fields without explicit `$select`
- **Container filename uploads**: improved MIME type mapping and filename preservation
- **Value list support** in metadata

### Changed

- **ROWID format change**: `@odata.id` changed from `Contacts(numeric)` to `Contacts('string')` format. Clients that parse ROWID values must update URL construction logic.
- Webhook `headers` parameter deprecated in favor of `endpointHeaders` (legacy alias still supported).
- Metadata may emit `Edm.Stream` or `Edm.Binary` for container fields (both should be accepted by parsers).

### Known issues

- Batch GET-before-changeset ordering bug may persist in some 22.x point releases.

## Claris FileMaker 2026 (26.x) — current

### Added

- **Immutable ID URL forms**:
  - Tables: `FMTID:<id>` (replaces table name in URL paths)
  - Fields: `FMFID` can be used in `$select`, `$orderby`, `$filter` (replaces field name)
  - Note: `FMSID` for scripts was introduced in v21.1 but became a recommended best practice in v26 due to the Claris AI Server launch
- **`Computed` annotation**: metadata annotation flagging calculation/auto-entered fields
- **ServerVersion** metadata annotation: additional version detection annotation (alongside `Org.OData.Core.V1.ProductVersion`)
- **Enriched FMComment**: enhanced table and field comments in `$metadata`
- **Script listing via metadata**: scripts listable with FMSID, parameter types, and return types from `$metadata`
- **Webhook `maxFailedAttempts` parameter**: configurable retry limit
- **Webhook notification errors logged** to `fmodata.log`
- **`ROWID: -1`** consistently returned when a table is truncated or all records deleted

### Changed

- Non-ASCII field names can be automatically resolved to FMFID to avoid encoding issues in `$filter`.
- Metadata parsing may include additional annotations for AI model integration.

### Recommended practices for 2026+

- Use FMSID for script invocation instead of script names (survives renames).
- Use FMTID and FMFID for stable integrations (survives table/field renames).
- Leverage AIAnnotation for AI-powered tooling (e.g., MCP servers can use it to describe fields to AI agents).
- Use FMComment for human-readable field documentation in tooling.

### Known issues

- **`$apply` aggregate() parser bug**: `aggregate(...)` transformation expressions fail with `parse failure in URL at: ')'` (error code -1002). The `$metadata` advertises support for `aggregate` via `ApplySupportedDefaults`, but only `groupby((fields))` works in practice. See [docs/13-quirks.md](13-quirks.md) for details.
- **Single-field `groupby` failure**: `groupby((single_field))` may fail with error 8309. Include at least two fields in the groupby clause.
- **Missing `ProductVersion` annotation**: Some FMS v26 deployments do not include the `Org.OData.Core.V1.ProductVersion` annotation in `$metadata`, causing version detection to return `null`. The `ServerVersion` annotation (also v26+) may be present as an alternative. See [docs/05-metadata.md](05-metadata.md) for multi-strategy version detection.

## Future / next

This section is reserved for announced or upcoming changes. As of this spec version, no specific future changes have been officially announced beyond what is in Claris 2026.

### Speculative / under consideration

- `$search` query option support
- Lambda operators (`any`, `all`)
- Asynchronous request support
- Delta query (`$delta`)
- Improved batch request parsing (fix for GET ordering bug)
- Full OData 4.01 compliance (currently partial)

These are not confirmed and should not be relied upon.

## Feature availability matrix

| Feature | 2023 (20.x) | 2024 (21.x) | 2025 (22.x) | 2026 (26.x) |
|---------|-------------|-------------|-------------|-------------|
| Core OData (CRUD, query, metadata) | Yes | Yes | Yes | Yes |
| Batch requests | Yes | Yes | Yes | Yes |
| Script execution (by name) | Yes | Yes | Yes | Yes |
| Script execution (by FMSID) | No | Yes | Yes | Yes |
| Container upload (binary) | Yes | Yes | Yes | Yes |
| Container upload (base64) | Yes | Yes | Yes | Yes |
| Container download | Yes | Yes | Yes | Yes |
| HTTP Basic auth | Yes | Yes | Yes | Yes |
| OAuth identity provider | No | Yes | Yes | Yes |
| Webhooks | No | No | Yes (v22.0.4) | Yes |
| Webhook `queryHeaders` | No | No | Yes (v22.0.4) | Yes |
| `$apply` (aggregation) | No | No | Yes | Yes |
| Type casting | No | Yes | Yes | Yes |
| Parameterized filters | No | Yes | Yes | Yes |
| Simplified query syntax (no `$`) | No | Yes | Yes | Yes |
| Nested queries | No | Yes | Yes | Yes |
| Batch preference inheritance | No | Yes | Yes | Yes |
| Metadata filtering | No | No | Yes (v22.0.4) | Yes |
| Schema comments (FMComment) | No | Yes | Yes | Yes |
| AI annotations (AIAnnotation) | No | Yes | Yes | Yes |
| Computed annotation | No | No | No | Yes |
| Script listing via metadata | No | No | No | Yes |
| Immutable IDs in URLs (FMTID, FMFID) | No | No | No | Yes |
| ServerVersion annotation | No | No | No | Yes |
| Enriched FMComment | No | No | No | Yes |
| `$search` | No | No | No | No |
| Lambda operators | No | No | No | No |
| `/$count` suffix | No | No | No | No |
| `$compute` | No | No | No | No |

## Paging

FileMaker Server uses **server-driven paging** with a default page size of **10,000 records**. When a query returns more records than the page size, the response includes an `@odata.nextLink` URL at the bottom of the JSON payload for fetching the next batch.

### Manual pagination

Override the server-driven paging with `$top` and `$skip`:

```http
GET /Contacts?$top=10000&$skip=10000   # second batch of 10,000
```

### Custom page size

Use the `Prefer: odata.maxpagesize=N` header to reduce the chunk size below 10,000:

```http
GET /Contacts
Prefer: odata.maxpagesize=1000
```

Note: FileMaker's Data API defaults to 100 records per page — do not confuse with OData's 10,000.

## Version detection

Downstream libraries can detect the FileMaker Server version by:

1. Fetching `$metadata`.
2. Parsing the version from the XML using a multi-strategy approach (see `docs/05-metadata.md` for full details):
   - **1a**: `Org.OData.Core.V1.ProductVersion` annotation with `String` attribute (all versions)
   - **1b**: Same as 1a but with reversed attribute order (`String` before `Term`)
   - **1c**: `ServerVersion` annotation (Claris 2026+, e.g. `"OData Engine 26.0.1"`)
   - **1d**: Same as 1c but with reversed attribute order
   - **2**: Generic fallback — any annotation term containing "Version" with a value >= 17
3. Extracting `major.minor.patch` from the version string (ignoring build suffix, e.g. `"21.1.2.500"` -> `21.1.2`).
4. Mapping the major version number (20, 21, 22, 26) to a feature availability matrix.
5. Returning `null` if no strategy yields a parseable version — tools should proceed with a warning, never a hard error.

The `@fms-odata/spec-ts` package provides `parseServerVersion(metadataXml)` which implements this detection logic. The Python package (`fms-odata-spec`) provides the same function as `parse_server_version()`, and the PHP package (`fsans/fms-odata-spec-php`) provides it as `Metadata::parseServerVersion()`.
