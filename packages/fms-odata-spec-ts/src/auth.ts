/**
 * Authentication types for the FileMaker OData API.
 *
 * @see docs/04-authentication.md
 */

/** Authentication scheme supported by FileMaker OData. */
export type FMAuthScheme = 'Basic' | 'Bearer';

/** Static auth token string (e.g., "Basic dXNlcjpwYXNz" or "Bearer <token>"). */
export type FMAuthToken = string;

/**
 * Token provider function. Returns the auth header value.
 * Can be async to support token refresh (e.g., OAuth token expiry).
 */
export type FMAuthTokenProvider = () => string | Promise<string>;

/** Configuration for Basic auth (FileMaker Server on-premise). */
export interface FMBasicAuthConfig {
  scheme: 'Basic';
  account: string;
  password: string;
}

/** Configuration for OAuth/Bearer auth (external identity providers, FileMaker Cloud). */
export interface FMOAuthAuthConfig {
  scheme: 'Bearer';
  token: string;
  /** Optional refresh callback invoked on 401 responses. */
  onUnauthorized?: () => Promise<string>;
}

/** Union of auth configurations. */
export type FMAuthConfig = FMBasicAuthConfig | FMOAuthAuthConfig;

/** Standard auth-related headers. */
export interface FMAuthHeaders {
  Authorization: string;
  'OData-Version'?: '4.0' | '4.01';
  'OData-MaxVersion'?: '4.0' | '4.01';
}

/** Build a Basic auth header value from account and password. */
export function basicAuth(account: string, password: string): string {
  const raw = `${account}:${password}`;
  // Works in both Node (Buffer) and browsers (btoa)
  if (typeof Buffer !== 'undefined') {
    return `Basic ${Buffer.from(raw).toString('base64')}`;
  }
  return `Basic ${btoa(raw)}`;
}

/** Build a Bearer auth header value from an OAuth session token. */
export function bearerAuth(token: string): string {
  return `Bearer ${token}`;
}

/** Normalize a token string: if it already has a scheme prefix, use as-is. */
export function normalizeAuthToken(token: string): string {
  if (token.startsWith('Basic ') || token.startsWith('Bearer ')) {
    return token;
  }
  // Default to Bearer for bare tokens (callers should use basicAuth() or bearerAuth() helpers)
  return `Bearer ${token}`;
}
