# 04 — Authentication

The FileMaker OData API requires authentication on every request via the `Authorization` header. The mechanism differs between FileMaker Server (on-premise) and FileMaker Cloud.

## FileMaker Server (on-premise)

### Mechanism: HTTP Basic Auth

FileMaker Server uses standard HTTP Basic authentication with a FileMaker file account (account name + password defined in the hosted database).

### Header format

```http
Authorization: Basic <base64(account:password)>
```

For example, with account `admin` and password `admin`:
- Raw: `admin:admin`
- Base64: `YWRtaW46YWRtaW4=`
- Header: `Authorization: Basic YWRtaW46YWRtaW4=`

### Notes

- The account must have the `fmproExtended` (or equivalent OData) extended privilege enabled in the FileMaker file.
- Credentials are sent on every request — there is no session token or login/logout flow.
- HTTPS is required (OData does not accept plain HTTP connections).
- Self-signed certificates are common in LAN deployments; clients must handle TLS verification accordingly.

## OAuth identity provider (FileMaker Cloud and external providers)

FileMaker Cloud and FileMaker Server 2024+ (v21.x+) support logging in to a database session using an OAuth identity provider (e.g., Google, Microsoft, Amazon).

### Flow

1. Obtain an OAuth token from the identity provider.
2. Use the OAuth token to authenticate to the FileMaker database session.
3. Include the resulting session token in OData calls as a Bearer token.

### Header format

```http
Authorization: Bearer <session-token>
```

### Token lifecycle

1. Authenticate with the OAuth identity provider.
2. Exchange the authorization code for a FileMaker session token.
3. Include the token in the `Authorization: Bearer <token>` header for all OData calls.
4. Tokens are valid for **1 hour**.
5. After expiry, API calls fail with HTTP 401. Re-authenticate to get a new token.

### Notes

- Token refresh is the client's responsibility — the OData API does not auto-refresh.
- HTTPS is required.
- Available from Claris FileMaker 2024 (v21.1+) onward.

See the official Claris documentation for the current list of supported OAuth providers and the exact flow.

## Authentication comparison

| Property | FileMaker Server | FileMaker Cloud |
|----------|-----------------|-----------------|
| Auth scheme | HTTP Basic | OAuth Bearer token |
| Credentials | FileMaker file account | OAuth identity provider |
| Token expiry | No (stateless) | 1 hour |
| Refresh needed | No | Yes (client responsibility) |
| HTTPS required | Yes | Yes |
| Header format | `Authorization: Basic <base64>` | `Authorization: Bearer <token>` |

## Required headers for all requests

Regardless of auth mechanism, these headers should be included:

| Header | Value | Required? |
|--------|-------|-----------|
| `Authorization` | `Basic <base64>` or `Bearer <token>` | Yes |
| `OData-Version` | `4.0` or `4.01` | Recommended |
| `OData-MaxVersion` | `4.0` or `4.01` | Recommended |
| `Accept` | `application/json` (default), `application/atom+xml`, or `text/html` | Optional |
| `Content-Type` | `application/json` (for POST/PATCH/PUT) | Required for write operations |

### Note on OData-Version headers

The OData specification mandates `OData-Version` and `OData-MaxVersion` headers. FileMaker Server 2023 (v20.x) uses `4.0`; FileMaker Server 2024+ (v21.x+) uses `4.01` at partial conformance level. The `@odata.count` vs `@count` behavior can be toggled by passing the appropriate version header. In practice, FileMaker Server accepts requests without these headers, but they should be sent per spec for correctness and forward compatibility.

## What does NOT work

- **FileMaker Data API bearer tokens**: The Data API (`/fmi/data/v1/`) uses a different auth flow (POST to `/auth` to get a bearer token). That token does **not** work with the OData API. OData requires Basic auth (Server) or OAuth Bearer token (Cloud).
- **Session cookies**: OData is stateless — there are no session cookies to maintain.
- **API keys**: There is no API key mechanism in the OData API.

## Wrapper library guidance

Downstream libraries should:

1. Support both Basic auth (Server) and OAuth Bearer token (Cloud) auth schemes.
2. Allow the auth token to be provided as either a static string or a function (for token refresh).
3. Auto-detect the auth scheme from the token format (if it starts with `Basic ` or `Bearer `, use as-is; otherwise prepend the appropriate scheme).
4. Handle 401 responses with a retry/refresh callback for FileMaker Cloud token expiry.
5. Never log or expose credentials in error messages.
6. Support configurable TLS verification (for self-signed certificates in development).
