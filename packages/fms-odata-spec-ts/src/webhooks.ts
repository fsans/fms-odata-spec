/**
 * Webhook types for the FileMaker OData API.
 *
 * @see docs/09-webhooks.md
 */

/** Parameters for creating a webhook. */
export interface WebhookCreateParams {
  /** URL to receive webhook POST payloads. Required. */
  webhook: string;
  /** Table to monitor for changes. Required. */
  tableName: string;
  /** Headers sent to the endpoint URL (does not affect processing). */
  endpointHeaders?: Record<string, string>;
  /** Legacy alias for endpointHeaders. */
  headers?: Record<string, string>;
  /** Headers controlling how the webhook payload is generated. */
  queryHeaders?: Record<string, string>;
  /** Whether to notify on schema changes. Default: false. */
  notifySchemaChanges?: boolean;
  /** Comma-separated field list to include in payload. Default: "". */
  select?: string;
  /** OData filter expression; only matching records trigger webhook. Default: "". */
  filter?: string;
  /** Max retry attempts (0 = infinite). Default: 0. */
  maxFailedAttempts?: number;
}

/** Webhook data returned by Webhook.Get / Webhook.GetAll.
 *
 * FMS uses `webhookID` (an integer) as the primary key in responses. The
 * optional `id` field is kept for backward compatibility; callers should
 * prefer `webhookID` and map it to `id` if needed.
 */
export interface WebhookData {
  /** Integer id assigned by FMS (primary key in responses). */
  webhookID?: number;
  /** Legacy/optional id field. Prefer `webhookID`. */
  id?: string;
  webhook: string;
  tableName: string;
  endpointHeaders?: Record<string, string>;
  queryHeaders?: Record<string, string>;
  notifySchemaChanges?: boolean;
  select?: string;
  filter?: string;
  maxFailedAttempts?: number;
}

/** Webhook operation types.
 *
 * Note: FMS exposes `Webhook.Delete`, not `Webhook.Remove`.
 */
export type WebhookOperation = 'Add' | 'Delete' | 'Get' | 'GetAll' | 'Invoke';

/** Body for `Webhook.Invoke({id})`.
 *
 * `rowIDs` is required (an empty array is valid and triggers the webhook for
 * all pending records). An absent body is rejected by FMS with a JSON syntax
 * error.
 */
export interface WebhookInvokeParams {
  rowIDs: Array<string | number>;
}

/** Result returned by `Webhook.Add`. */
export interface WebhookCreateResult {
  webhookResult: {
    webhookID: number;
  };
}

/** Build the URL path for a webhook operation.
 *
 * When `id` is provided, it is appended as an OData function argument, e.g.
 * `Webhook.Get(1)`. When omitted, the bare operation path is returned, e.g.
 * `Webhook.GetAll`.
 */
export function webhookPath(
  database: string,
  operation: WebhookOperation,
  id?: number,
): string {
  return id === undefined
    ? `/${database}/Webhook.${operation}`
    : `/${database}/Webhook.${operation}(${id})`;
}
