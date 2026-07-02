"""Tests for fms_odata_spec.endpoints."""

from __future__ import annotations

from fms_odata_spec.endpoints import (
    FM_ENDPOINTS,
    FMEndpoint,
    get_endpoint,
    get_endpoints_for_version,
)


def test_endpoints_table_is_nonempty() -> None:
    assert len(FM_ENDPOINTS) >= 20


def test_every_endpoint_has_required_fields() -> None:
    for e in FM_ENDPOINTS:
        assert e.id
        assert e.method in ("GET", "POST", "PATCH", "PUT", "DELETE")
        assert e.path.startswith("/")
        assert e.description
        assert e.min_version in ("20", "21", "22", "26")
        assert e.category in (
            "discovery", "metadata", "query", "crud", "batch",
            "scripts", "containers", "schema", "webhooks",
        )


def test_get_endpoint_returns_match() -> None:
    e = get_endpoint("getRecords")
    assert e is not None
    assert e.method == "GET"
    assert e.path == "/{database}/{table}"


def test_get_endpoint_returns_none_for_unknown() -> None:
    assert get_endpoint("nope") is None


def test_get_endpoints_for_version_20_excludes_webhooks() -> None:
    eps = get_endpoints_for_version("20")
    ids = {e.id for e in eps}
    assert "getRecords" in ids
    assert "createWebhook" not in ids
    assert "runScriptById" not in ids


def test_get_endpoints_for_version_21_includes_script_by_id() -> None:
    eps = get_endpoints_for_version("21")
    ids = {e.id for e in eps}
    assert "runScriptById" in ids
    assert "createWebhook" not in ids


def test_get_endpoints_for_version_26_includes_all() -> None:
    eps = get_endpoints_for_version("26")
    ids = {e.id for e in eps}
    assert "runScriptById" in ids
    assert "createWebhook" in ids


def test_get_endpoints_for_unknown_version_returns_all() -> None:
    eps = get_endpoints_for_version("99")  # type: ignore[arg-type]
    assert len(eps) == len(FM_ENDPOINTS)


def test_endpoint_is_frozen() -> None:
    import pytest

    e = get_endpoint("getRecords")
    assert e is not None
    with pytest.raises(Exception):
        e.method = "POST"  # type: ignore[misc]


def test_create_record_has_also_methods_put() -> None:
    e = get_endpoint("createRecord")
    assert e is not None
    assert "PUT" in e.also_methods
    assert e.content_type == "application/json"


# --- DDL endpoint URL format (slash-separated, not key-parentheses) ---------

def test_add_fields_uses_slash_path() -> None:
    e = get_endpoint("addFields")
    assert e is not None
    assert e.method == "PATCH"
    assert e.path == "/{database}/FileMaker_Tables/{tableName}"
    assert "'" not in e.path


def test_delete_table_uses_slash_path() -> None:
    e = get_endpoint("deleteTable")
    assert e is not None
    assert e.method == "DELETE"
    assert e.path == "/{database}/FileMaker_Tables/{tableName}"


def test_delete_field_uses_slash_path() -> None:
    e = get_endpoint("deleteField")
    assert e is not None
    assert e.path == "/{database}/FileMaker_Tables/{tableName}/{fieldName}"


def test_create_index_uses_table_in_path() -> None:
    e = get_endpoint("createIndex")
    assert e is not None
    assert e.method == "POST"
    assert e.path == "/{database}/FileMaker_Indexes/{tableName}"


def test_delete_index_uses_table_and_field_in_path() -> None:
    e = get_endpoint("deleteIndex")
    assert e is not None
    assert e.method == "DELETE"
    assert e.path == "/{database}/FileMaker_Indexes/{tableName}/{fieldName}"


def test_create_table_unchanged() -> None:
    e = get_endpoint("createTable")
    assert e is not None
    assert e.method == "POST"
    assert e.path == "/{database}/FileMaker_Tables"


# --- Webhook endpoint methods and URL patterns -------------------------------

def test_get_all_webhooks_is_get() -> None:
    e = get_endpoint("getAllWebhooks")
    assert e is not None
    assert e.method == "GET"
    assert e.path == "/{database}/Webhook.GetAll"


def test_get_webhook_is_get_with_id_in_path() -> None:
    e = get_endpoint("getWebhook")
    assert e is not None
    assert e.method == "GET"
    assert e.path == "/{database}/Webhook.Get({webhookId})"


def test_delete_webhook_uses_delete_not_remove() -> None:
    e = get_endpoint("deleteWebhook")
    assert e is not None
    assert e.method == "POST"
    assert e.path == "/{database}/Webhook.Delete({webhookId})"
    assert "Remove" not in e.path


def test_invoke_webhook_has_id_in_path() -> None:
    e = get_endpoint("invokeWebhook")
    assert e is not None
    assert e.method == "POST"
    assert e.path == "/{database}/Webhook.Invoke({webhookId})"


def test_create_webhook_unchanged() -> None:
    e = get_endpoint("createWebhook")
    assert e is not None
    assert e.method == "POST"
    assert e.path == "/{database}/Webhook.Add"
