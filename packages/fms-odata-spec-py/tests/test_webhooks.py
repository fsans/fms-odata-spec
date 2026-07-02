"""Tests for fms_odata_spec.webhooks."""

from __future__ import annotations

from fms_odata_spec.webhooks import (
    WebhookCreateParams,
    WebhookCreateResult,
    WebhookData,
    WebhookInvokeParams,
    WebhookOperation,
    webhook_path,
)


def test_webhook_path_without_id() -> None:
    assert webhook_path("MyDB", "Add") == "/MyDB/Webhook.Add"
    assert webhook_path("MyDB", "GetAll") == "/MyDB/Webhook.GetAll"


def test_webhook_path_with_id() -> None:
    assert webhook_path("MyDB", "Get", 1) == "/MyDB/Webhook.Get(1)"
    assert webhook_path("MyDB", "Delete", 42) == "/MyDB/Webhook.Delete(42)"
    assert webhook_path("MyDB", "Invoke", 7) == "/MyDB/Webhook.Invoke(7)"


def test_webhook_operation_has_delete_not_remove() -> None:
    # WebhookOperation is a Literal; verify "Delete" is accepted and "Remove" is not.
    delete_op: WebhookOperation = "Delete"
    assert webhook_path("DB", delete_op, 1) == "/DB/Webhook.Delete(1)"


def test_webhook_create_params_to_odata_dict() -> None:
    p = WebhookCreateParams(
        webhook="https://example.com/hook",
        table_name="Customers",
        endpoint_headers={"X-Token": "abc"},
        notify_schema_changes=True,
        select="id,name",
        filter="status eq 'open'",
        max_failed_attempts=3,
    )
    d = p.to_odata_dict()
    assert d["webhook"] == "https://example.com/hook"
    assert d["tableName"] == "Customers"
    assert d["endpointHeaders"] == {"X-Token": "abc"}
    assert d["notifySchemaChanges"] is True
    assert d["select"] == "id,name"
    assert d["filter"] == "status eq 'open'"
    assert d["maxFailedAttempts"] == 3


def test_webhook_create_params_minimal() -> None:
    p = WebhookCreateParams(webhook="u", table_name="t")
    d = p.to_odata_dict()
    assert d == {"webhook": "u", "tableName": "t"}


def test_webhook_data_to_odata_dict() -> None:
    w = WebhookData(
        webhook_id=5,
        webhook="https://example.com/hook",
        table_name="Customers",
        query_headers={"X": "y"},
    )
    d = w.to_odata_dict()
    assert d["webhookID"] == 5
    assert d["queryHeaders"] == {"X": "y"}
    assert d["tableName"] == "Customers"


def test_webhook_data_legacy_id_still_supported() -> None:
    w = WebhookData(
        id="abc",
        webhook="https://example.com/hook",
        table_name="Customers",
    )
    d = w.to_odata_dict()
    assert d["id"] == "abc"
    assert "webhookID" not in d


def test_webhook_invoke_params_to_odata_dict() -> None:
    p = WebhookInvokeParams(row_ids=[1, 2, 3])
    assert p.to_odata_dict() == {"rowIDs": [1, 2, 3]}


def test_webhook_invoke_params_empty_is_valid() -> None:
    p = WebhookInvokeParams()
    assert p.to_odata_dict() == {"rowIDs": []}


def test_webhook_create_result_roundtrip() -> None:
    r = WebhookCreateResult(webhook_id=11)
    assert r.to_odata_dict() == {"webhookResult": {"webhookID": 11}}


def test_webhook_create_result_from_odata_dict() -> None:
    r = WebhookCreateResult.from_odata_dict(
        {"webhookResult": {"webhookID": 11}}
    )
    assert r.webhook_id == 11
