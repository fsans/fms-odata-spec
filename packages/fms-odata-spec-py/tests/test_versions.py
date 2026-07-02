"""Tests for fms_odata_spec.versions."""

from __future__ import annotations

import pytest

from fms_odata_spec.versions import (
    DEFAULT_PAGE_SIZE,
    FM_VERSION_MATRIX,
    FM_VERSION_NAMES,
    ODATA_CONFORMANCE_LEVEL,
    ODATA_PROTOCOL_VERSION,
    has_feature,
    has_query_option,
    min_version_for_feature,
)


def test_protocol_version_is_4_01() -> None:
    assert ODATA_PROTOCOL_VERSION == "4.01"


def test_conformance_level_is_intermediate() -> None:
    assert ODATA_CONFORMANCE_LEVEL == "intermediate"


def test_default_page_size_is_10000() -> None:
    assert DEFAULT_PAGE_SIZE == 10_000


def test_version_names_cover_all_majors() -> None:
    assert set(FM_VERSION_NAMES) == {"20", "21", "22", "26", "future"}
    assert FM_VERSION_NAMES["26"] == "Claris FileMaker 2026"
    assert FM_VERSION_NAMES["22"] == "Claris FileMaker 2025"


def test_matrix_has_all_versions() -> None:
    assert set(FM_VERSION_MATRIX) == {"20", "21", "22", "26", "future"}


@pytest.mark.parametrize(
    "version,feature,expected",
    [
        ("20", "webhooks", False),
        ("22", "webhooks", True),
        ("26", "scripts_by_fmsid", True),
        ("20", "scripts_by_fmsid", False),
        ("21", "scripts_by_fmsid", True),
        ("26", "auth_basic", True),
        ("20", "auth_oauth", False),
        ("21", "auth_oauth", True),
        ("future", "ai_annotation", True),
        ("20", "fm_comment", False),
        ("21", "fm_comment", True),
        ("20", "metadata_filtering", False),
        ("22", "metadata_filtering", True),
        ("26", "computed_annotation", True),
        ("22", "computed_annotation", False),
    ],
)
def test_has_feature(version: str, feature: str, expected: bool) -> None:
    assert has_feature(version, feature) is expected  # type: ignore[arg-type]


def test_has_feature_unknown_version_returns_false() -> None:
    assert has_feature("99", "webhooks") is False  # type: ignore[arg-type]


def test_has_feature_unknown_feature_returns_false() -> None:
    assert has_feature("26", "nonexistent_feature") is False


@pytest.mark.parametrize(
    "version,option,expected",
    [
        ("20", "apply", False),
        ("22", "apply", True),
        ("26", "filter", True),
        ("20", "search", False),
        ("26", "compute", False),
    ],
)
def test_has_query_option(version: str, option: str, expected: bool) -> None:
    assert has_query_option(version, option) is expected  # type: ignore[arg-type]


def test_min_version_for_feature() -> None:
    assert min_version_for_feature("webhooks") == "22"
    assert min_version_for_feature("scripts_by_fmsid") == "21"
    assert min_version_for_feature("auth_basic") == "20"
    assert min_version_for_feature("ai_annotation") == "21"
    assert min_version_for_feature("computed_annotation") == "26"
    assert min_version_for_feature("metadata_filtering") == "22"


def test_min_version_for_unknown_feature_returns_none() -> None:
    assert min_version_for_feature("nonexistent") is None


def test_version_info_fields() -> None:
    info = FM_VERSION_MATRIX["26"]
    assert info.major == "26"
    assert info.name == "Claris FileMaker 2026"
    assert info.release_year == 2026
    assert info.status == "current"
    assert info.odata_protocol_version == "4.01"
    assert info.features.scripts_by_fmsid is True
    assert info.query_options.apply is True


def test_v20_uses_odata_4_0() -> None:
    assert FM_VERSION_MATRIX["20"].odata_protocol_version == "4.0"


def test_v21_uses_odata_4_01() -> None:
    assert FM_VERSION_MATRIX["21"].odata_protocol_version == "4.01"


def test_feature_flags_is_frozen() -> None:
    flags = FM_VERSION_MATRIX["20"].features
    with pytest.raises(Exception):
        flags.webhooks = True  # type: ignore[misc]
