# 13 — Quirks and Workarounds

This document captures real-world behaviors of the FileMaker OData API that differ from the official documentation or from standard OData expectations. These quirks were observed in the downstream wrapper repositories (`fms-odata-mcp`, `fms-odata-js`, `fms-odata-py`, `fms-odata-php`, and `fms-odata-webhooks`) and in production usage.

## URL encoding quirks

### Commas must not be percent-encoded

**Behavior**: FileMaker rejects `%2C` (percent-encoded comma) in `$select`, `$orderby`, and `$expand` query options.

**Workaround**: After URL-encoding query option values, un-encode commas. Most HTTP client libraries percent-encode commas by default; they must be explicitly preserved.

```javascript
// Wrong: $select=Company%2CWebsite
// Right: $select=Company,Website
```

### Dollar signs must not be percent-encoded

**Behavior**: FileMaker rejects `%24` (percent-encoded dollar sign) in system query option names.

**Workaround**: System query option names (`$filter`, `$select`, etc.) must retain their literal `$` character.

```javascript
// Wrong: %24filter=...
// Right: $filter=...
```

### Single quotes in string keys must be doubled

**Behavior**: OData requires single quotes inside string literals to be doubled.

**Workaround**: Escape single quotes as `''` in string key values.

```
// For the name O'Brien:
GET /Contacts('O''Brien')
```

### Spaces should be `%20`, not `+`

**Behavior**: FileMaker expects standard percent-encoding for spaces (`%20`), not the `+` form used in form-urlencoded encoding.

**Workaround**: Use `%20` for spaces in URLs.

## `/$count` suffix not supported

**Behavior**: The standard OData `/$count` suffix (e.g., `GET /Contacts/$count`) returns HTTP 400 Bad Request.

**Workaround**: Use the inline query option form: `GET /Contacts?$count=true`. To get only the count without records, combine with `$top=0`: `GET /Contacts?$count=true&$top=0`.

## Container download Accept header quirk

**Behavior**: Sending `Accept: application/octet-stream` for a container field download returns the stored filename as `text/plain`, NOT the binary data.

**Workaround**: Use `Accept: */*` (or omit the Accept header) for container downloads. Any value other than `application/octet-stream` returns the actual binary data.

## Auto-generated filename for container uploads

**Behavior**: When uploading binary container data without specifying a filename in `Content-Disposition`, FileMaker generates `Untitled.png` regardless of the actual content type. The binary data is correct; only the filename is wrong.

**Workaround**: Always pass an explicit filename in the `Content-Disposition` header.

```
Content-Disposition: inline; filename=myphoto.jpg
```

## HTTP Basic auth required (not Data API tokens)

**Behavior**: The FileMaker Data API (`/fmi/data/v1/`) uses a bearer token obtained via `POST /auth`. That token does NOT work with the OData API.

**Workaround**: OData requires HTTP Basic auth (FileMaker Server) or OAuth Bearer token (FileMaker Cloud). Do not attempt to reuse Data API tokens.

## Self-signed TLS certificates

**Behavior**: FileMaker Server deployments on LANs commonly use self-signed SSL certificates. Standard HTTP clients reject these by default.

**Workaround**: In development, configure the HTTP client to skip TLS verification. In Node.js, set `NODE_TLS_REJECT_UNAUTHORIZED=0` (never use in production). In Axios, use a custom `httpsAgent` with `rejectUnauthorized: false`. Always use proper certificates in production.

## Batch request GET ordering bug

**Behavior**: On some FileMaker Server versions (observed in 21.x and some later versions), GET operations placed before changesets in a batch request cause issues:
- GET operations before changesets may not be processed correctly.
- Multiple consecutive GETs may result in the last one being silently dropped.

**Workaround**: Place all changesets first, then GET operations at the end of the batch. Limit to one GET operation at the end, or accept potential loss of results.

## Date/time formatting

**Behavior**: FileMaker returns timestamps in UTC ISO-8601 format, with or without milliseconds depending on the version and field configuration.

**Workaround**: Parse both formats (with and without milliseconds). When sending dates, use ISO-8601 without milliseconds for maximum compatibility.

## OData-Version headers

**Behavior**: The OData spec mandates `OData-Version: 4.0` and `OData-MaxVersion: 4.0` headers. In practice, FileMaker Server 2026 accepts requests without them.

**Workaround**: Always send both headers per spec for correctness and forward compatibility, even though they're currently optional.

## Field name quoting

**Behavior**: Field names with special characters (spaces, underscores) must be enclosed in double-quotation marks in `$filter` and `$orderby`.

**Workaround**: Auto-quote field names containing spaces or special characters. On Claris 2026+, use FMFID instead of field names to avoid quoting issues entirely.

## Non-ASCII field names

**Behavior**: Non-ASCII field names (e.g., accented characters) can cause encoding issues in `$filter` expressions.

**Workaround**: On Claris 2026+, resolve non-ASCII field names to their FMFID (from `$metadata`) and use the ID in queries. On older versions, ensure proper UTF-8 encoding and double-quote the field name.

## Empty POST body for scripts without parameters

**Behavior**: When running a script without a parameter, the POST body must be completely empty. Some HTTP clients add a default `Content-Type: application/json` header even for empty bodies, which can cause issues.

**Workaround**: Explicitly omit the body and set `Content-Type` appropriately. Some clients may need to send `Content-Length: 0`.

## Script names with special characters

**Behavior**: OData does not support script names with special characters (`@`, `&`, `/`) or names beginning with a number.

**Workaround**: On Claris 2026+, use `Script.FMSID:<id>` instead of the script name. On older versions, rename the script to avoid special characters.

## Global fields are read-only

**Behavior**: FileMaker global fields cannot be updated via OData. Attempting to set a global field in a create or update operation is silently ignored or causes an error.

**Workaround**: Do not include global fields in create/update payloads. If global field values need to change, use a script.

## Repeating field syntax

**Behavior**: Repeating field values are accessed by specifying the repetition number in brackets.

**Workaround**: Use `FieldName[repetition]` syntax in both reads and writes:
- Read: `$select=QuarterlyTotals[1],QuarterlyTotals[2]`
- Write: `{"QuarterlyTotals[4]": 100}`

## Edm.Stream vs Edm.Binary for containers

**Behavior**: Different FileMaker Server versions emit different Edm types for container fields in `$metadata`:
- Some versions: `Edm.Stream`
- Some versions: `Edm.Binary`

**Workaround**: Metadata parsers must accept both types as indicating a container field.

## Content-Disposition format for non-ASCII filenames

**Behavior**: FileMaker accepts both unquoted and RFC 5987 formats for `Content-Disposition` filenames.

**Workaround**: For ASCII filenames, use: `Content-Disposition: inline; filename=name.png`. For non-ASCII filenames, use RFC 5987: `Content-Disposition: inline; filename*=UTF-8''<percent-encoded>`. Some clients send both forms; FileMaker accepts either.

## Script result envelope shape

**Behavior**: The script response uses a nested envelope, not flat string fields. The response is:

```json
{
  "scriptResult": {
    "code": 0,
    "resultParameter": "Hello World"
  }
}
```

The `scriptResult` field is an **object** (not a string) containing:
- `code` (number): Exit Script code (0 = success, non-zero = error)
- `resultParameter` (string): Text result from the Exit Script step

There is no top-level `scriptError` field — the error code lives inside `scriptResult.code`.

**Workaround**: Parse `scriptResult.code` and `scriptResult.resultParameter` from the nested object. Do not use `String()` on `scriptResult` — it will produce `"[object Object]"`. See [docs/06-scripts.md](06-scripts.md) for the full response specification.

## URL length limit

**Behavior**: The maximum URL length is influenced by OS, web server, and browser differences. Excessively long URLs (especially with complex `$filter` expressions) may fail.

**Workaround**: Limit URLs to 2,000 characters for cross-platform safety. For very complex filters, consider using batch requests or server-side scripts.

## `$apply` aggregate() parser bug (Claris 2026)

**Behavior**: On FileMaker Server 2026 (v26), the `$apply` parser fails to parse `aggregate(...)` transformation expressions, returning:

```json
{"error": {"code": "-1002", "message": "Error: parse failure in URL at: ')'"}}
```

This occurs regardless of URL encoding for parentheses or the specific aggregation function used (`sum`, `countdistinct`, etc.). The `$metadata` document advertises support for both `aggregate` and `groupby` transformations via the `Org.OData.Aggregation.V1.ApplySupportedDefaults` annotation, but only `groupby` works in practice.

**Workaround**: Use `groupby((fields))` without aggregation instead of `aggregate(...)`. For counting records, use `$count=true` instead of `aggregate($count as total)`. This bug may be fixed in a future FMS point release.

## Parentheses must not be percent-encoded in `$apply`

**Behavior**: FileMaker rejects `%28` and `%29` (percent-encoded parentheses) in `$apply` expressions. The `aggregate(...)` and `groupby((...))` syntax requires literal parentheses.

**Workaround**: After URL-encoding query option values, preserve literal `(` and `)` characters. This is analogous to the existing quirk where commas must not be percent-encoded.

```javascript
// Wrong:  $apply=groupby%28%28first_name%2Clast_name%29%29
// Right:  $apply=groupby((first_name,last_name))
```

## Single-field `groupby` may fail on FMS v26

**Behavior**: On FileMaker Server 2026 (v26), `groupby((single_field))` with only one grouping field may fail with:

```json
{"error": {"code": "8309", "message": "All non-aggregated field references must be in the groupby clause."}}
```

**Workaround**: Include at least two fields in the `groupby` clause: `groupby((field1, field2))`. If only one grouping dimension is needed, add a second constant or redundant field.

## `id` field cannot be used in `$filter` comparison operators

**Behavior**: FileMaker's OData parser rejects ALL comparison operators (`eq`, `gt`, `lt`, `ge`, `le`, `ne`) when the left-hand side is the reserved internal `id` field. The error is:

```json
{"error": {"code": "-1002", "message": "Error: syntax error in URL at: ' eq '"}}
```

(or `gt`, `lt`, etc. depending on the operator). This affects every table — the `id` field is a reserved internal FileMaker record identifier and cannot be filtered on. String functions like `contains(id, '...')` also fail with a parse error.

This is NOT a URL encoding issue: spaces inside string literals (e.g. `company eq 'Digital Dreams'`) work correctly with `%20` encoding. The limitation is specific to the `id` field.

**What DOES work**: Filtering on any other field works correctly, including:
- String values with spaces: `company eq 'Digital Dreams'`
- Numeric fields: `row_id eq 1`, `update_unix_time gt 0`
- String functions: `contains(company, 'Digital')`, `startswith(company, 'Digital')`
- Compound filters on non-`id` fields: `company eq 'Digital Dreams' and row_id gt 5`

**Workaround**: Use `row_id`, `uuid`, or any business field instead of `id` for filtering. To retrieve a record by its internal `id`, use a direct GET request to the entity key URL (`GET /Contacts(3275,...)`) rather than a `$filter` expression.

**Observed on**: FileMaker Server 2026 (v26), likely all versions. Verified via `fms-odata-mcp` live testing against the `Contacts` database.

## `$batch` POST returns 204 No Content for creates

**Behavior**: When creating records via OData `$batch` (multipart/mixed), FileMaker returns `HTTP 204 No Content` for each successful POST sub-request within the changeset, with the header `Preference-Applied: return=minimal`. The created record data (including the new record ID) is NOT returned in the batch response.

Adding `Prefer: return=representation` — either at the batch level or inside individual sub-requests — does not change this behavior. FileMaker always returns `204` for batch POSTs.

This differs from standard OData `$batch` behavior, where POST sub-requests in a changeset should return `201 Created` with the created entity body (or at minimum, a `Location` header pointing to the new resource).

**Workaround**: When using `$batch` for creates, the response only indicates success/failure per record — not the created record IDs or data. If you need the created records back (e.g., to know the assigned IDs), use individual (non-batch) POST requests in parallel instead. The `parallel` strategy returns full created records with IDs.

**Observed on**: FileMaker Server 2026 (v26). Verified via `fms-odata-mcp` batch operations live testing.

## `@odata.nextLink` pagination

**Behavior**: FileMaker Server emits `@odata.nextLink` in query responses when the result set exceeds the page size. The default page size is 10,000 records. The next link contains a `$skip` token for the next page.

Key observations:
- The next link URL is absolute (includes the full `https://host/fmi/odata/v4/...` path).
- When no `@odata.nextLink` is present (e.g., when the result set fits in one page), clients must fall back to manual `$skip`-based pagination if more records are needed.
- The `$skip` value in the next link is cumulative (not relative to the current page).
- `$top` controls the page size. If `$top` is not specified, the server defaults to 10,000.

**Workaround**: Pagination helpers should follow `@odata.nextLink` when present, and fall back to incrementing `$skip` by the page size when no next link is provided. Always cap the total records fetched to prevent excessive memory usage on large tables.

**Observed on**: FileMaker Server 2026 (v26). Verified via `fms-odata-mcp` `query_all_records` pagination helper live testing with the `Contacts` database.
