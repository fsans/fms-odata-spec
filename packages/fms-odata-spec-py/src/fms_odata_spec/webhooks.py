"""Webhook types for the FileMaker OData API.

Mirrors ``src/webhooks.ts`` from ``@fms-odata/spec-ts``.

@see docs/09-webhooks.md
"""

from __future__ import annotations

from dataclasses import dataclass, field
from typing import Dict, List, Literal, Optional, Union

__all__ = [
    "WebhookCreateParams",
    "WebhookData",
    "WebhookOperation",
    "WebhookInvokeParams",
    "WebhookCreateResult",
    "webhook_path",
]


@dataclass
class WebhookCreateParams:
    """Parameters for creating a webhook.

    Field names are snake_case in Python; use :meth:`to_odata_dict` to emit the
    original camelCase wire keys for JSON serialization.
    """

    #: URL to receive webhook POST payloads. Required.
    webhook: str
    #: Table to monitor for changes. Required.
    table_name: str
    #: Headers sent to the endpoint URL (does not affect processing).
    endpoint_headers: Optional[Dict[str, str]] = None
    #: Legacy alias for ``endpoint_headers``.
    headers: Optional[Dict[str, str]] = None
    #: Headers controlling how the webhook payload is generated.
    query_headers: Optional[Dict[str, str]] = None
    #: Whether to notify on schema changes. Default: ``False``.
    notify_schema_changes: Optional[bool] = None
    #: Comma-separated field list to include in payload. Default: ``""``.
    select: Optional[str] = None
    #: OData filter expression; only matching records trigger webhook. Default: ``""``.
    filter: Optional[str] = None
    #: Max retry attempts (0 = infinite). Default: ``0``.
    max_failed_attempts: Optional[int] = None

    def to_odata_dict(self) -> Dict[str, object]:
        """Return the params as a dict with the original OData wire keys."""
        out: Dict[str, object] = {
            "webhook": self.webhook,
            "tableName": self.table_name,
        }
        if self.endpoint_headers is not None:
            out["endpointHeaders"] = self.endpoint_headers
        if self.headers is not None:
            out["headers"] = self.headers
        if self.query_headers is not None:
            out["queryHeaders"] = self.query_headers
        if self.notify_schema_changes is not None:
            out["notifySchemaChanges"] = self.notify_schema_changes
        if self.select is not None:
            out["select"] = self.select
        if self.filter is not None:
            out["filter"] = self.filter
        if self.max_failed_attempts is not None:
            out["maxFailedAttempts"] = self.max_failed_attempts
        return out


@dataclass
class WebhookData:
    """Webhook data returned by Webhook.Get / Webhook.GetAll.

    FMS uses ``webhookID`` (an integer) as the primary key in responses. The
    optional ``id`` field is kept for backward compatibility; callers should
    prefer ``webhookID`` and map it to ``id`` if needed.

    Field names are snake_case in Python; use :meth:`to_odata_dict` to emit the
    original camelCase wire keys for JSON serialization.
    """

    webhook: str
    table_name: str
    #: Integer id assigned by FMS (primary key in responses).
    webhook_id: Optional[int] = None
    #: Legacy/optional id field. Prefer ``webhook_id``.
    id: Optional[str] = None
    endpoint_headers: Optional[Dict[str, str]] = None
    query_headers: Optional[Dict[str, str]] = None
    notify_schema_changes: Optional[bool] = None
    select: Optional[str] = None
    filter: Optional[str] = None
    max_failed_attempts: Optional[int] = None

    def to_odata_dict(self) -> Dict[str, object]:
        """Return the data as a dict with the original OData wire keys."""
        out: Dict[str, object] = {
            "webhook": self.webhook,
            "tableName": self.table_name,
        }
        if self.webhook_id is not None:
            out["webhookID"] = self.webhook_id
        if self.id is not None:
            out["id"] = self.id
        if self.endpoint_headers is not None:
            out["endpointHeaders"] = self.endpoint_headers
        if self.query_headers is not None:
            out["queryHeaders"] = self.query_headers
        if self.notify_schema_changes is not None:
            out["notifySchemaChanges"] = self.notify_schema_changes
        if self.select is not None:
            out["select"] = self.select
        if self.filter is not None:
            out["filter"] = self.filter
        if self.max_failed_attempts is not None:
            out["maxFailedAttempts"] = self.max_failed_attempts
        return out


#: Webhook operation types.
#:
#: Note: FMS exposes ``Webhook.Delete``, not ``Webhook.Remove``.
WebhookOperation = Literal["Add", "Delete", "Get", "GetAll", "Invoke"]


@dataclass
class WebhookInvokeParams:
    """Body for ``Webhook.Invoke({id})``.

    ``row_ids`` is required (an empty list is valid and triggers the webhook
    for all pending records). An absent body is rejected by FMS with a JSON
    syntax error.
    """

    row_ids: List[Union[str, int]] = field(default_factory=list)

    def to_odata_dict(self) -> Dict[str, object]:
        """Return the params as a dict with the original OData wire keys."""
        return {"rowIDs": list(self.row_ids)}


@dataclass
class WebhookCreateResult:
    """Result returned by ``Webhook.Add``."""

    webhook_id: int

    @classmethod
    def from_odata_dict(cls, data: Dict[str, object]) -> "WebhookCreateResult":
        """Build from the raw OData wire dict ``{"webhookResult": {"webhookID": n}}``."""
        result = data.get("webhookResult", {})
        return cls(webhook_id=int(result.get("webhookID", 0)))  # type: ignore[arg-type]

    def to_odata_dict(self) -> Dict[str, object]:
        """Return the result as a dict with the original OData wire keys."""
        return {"webhookResult": {"webhookID": self.webhook_id}}


def webhook_path(
    database: str,
    operation: WebhookOperation,
    id: Optional[int] = None,
) -> str:
    """Build the URL path for a webhook operation.

    When ``id`` is provided, it is appended as an OData function argument, e.g.
    ``Webhook.Get(1)``. When omitted, the bare operation path is returned, e.g.
    ``Webhook.GetAll``.
    """
    if id is None:
        return f"/{database}/Webhook.{operation}"
    return f"/{database}/Webhook.{operation}({id})"
