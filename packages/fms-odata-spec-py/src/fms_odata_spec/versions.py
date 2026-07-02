"""FileMaker Server version identifiers and feature flags.

Mirrors ``src/versions.ts`` from ``@fms-odata/spec-ts``.

@see docs/12-version-deltas.md
"""

from __future__ import annotations

from dataclasses import dataclass
from typing import Dict, Literal, Optional

__all__ = [
    "FMVersionMajor",
    "FMVersionStatus",
    "ODataProtocolVersion",
    "FM_VERSION_NAMES",
    "ODATA_PROTOCOL_VERSION",
    "ODATA_CONFORMANCE_LEVEL",
    "DEFAULT_PAGE_SIZE",
    "FMFeatureFlags",
    "FMQueryOptionFlags",
    "FMVersionInfo",
    "FM_VERSION_MATRIX",
    "has_feature",
    "has_query_option",
    "min_version_for_feature",
]

#: FileMaker Server version major numbers.
FMVersionMajor = Literal["20", "21", "22", "26", "future"]

#: Version status.
FMVersionStatus = Literal["supported", "current", "future"]

#: OData protocol version for a specific FileMaker Server version.
ODataProtocolVersion = Literal["4.0", "4.01"]

#: Human-readable version names.
FM_VERSION_NAMES: Dict[FMVersionMajor, str] = {
    "20": "Claris FileMaker 2023",
    "21": "Claris FileMaker 2024",
    "22": "Claris FileMaker 2025",
    "26": "Claris FileMaker 2026",
    "future": "Future / next",
}

#: OData protocol version implemented by FileMaker.
#:
#: v20.x implements OData 4.0. v21.x onward implements partial OData 4.01
#: at intermediate conformance level with some exceptions.
ODATA_PROTOCOL_VERSION: Literal["4.01"] = "4.01"

#: OData conformance level.
ODATA_CONFORMANCE_LEVEL: Literal["intermediate"] = "intermediate"

#: Default server-driven page size (records per page).
DEFAULT_PAGE_SIZE: int = 10_000


@dataclass(frozen=True)
class FMFeatureFlags:
    """Feature flags for a specific FileMaker Server version."""

    service_document: bool
    metadata: bool
    database_listing: bool
    table_listing: bool
    record_crud: bool
    record_references: bool
    cross_join: bool
    batch: bool
    scripts: bool
    scripts_by_fmsid: bool
    script_listing: bool
    container_binary_upload: bool
    container_base64_upload: bool
    container_download: bool
    schema_modification: bool
    webhooks: bool
    webhook_query_headers: bool
    apply_aggregation: bool
    type_casting: bool
    parameterized_filters: bool
    simplified_query_syntax: bool
    nested_queries: bool
    batch_preference_inheritance: bool
    metadata_filtering: bool
    immutable_id_urls: bool
    fm_comment: bool
    ai_annotation: bool
    computed_annotation: bool
    server_version_annotation: bool
    enriched_fm_comment: bool
    auth_basic: bool
    auth_oauth: bool


@dataclass(frozen=True)
class FMQueryOptionFlags:
    """Query option availability for a specific version."""

    filter: bool
    select: bool
    orderby: bool
    top: bool
    skip: bool
    expand: bool
    count: bool
    apply: bool
    search: bool
    compute: bool


@dataclass(frozen=True)
class FMVersionInfo:
    """Complete version descriptor."""

    major: FMVersionMajor
    name: str
    release_year: Optional[int]
    internal_version: str
    status: FMVersionStatus
    odata_protocol_version: ODataProtocolVersion
    features: FMFeatureFlags
    query_options: FMQueryOptionFlags


def _flags(
    *,
    service_document: bool,
    metadata: bool,
    database_listing: bool,
    table_listing: bool,
    record_crud: bool,
    record_references: bool,
    cross_join: bool,
    batch: bool,
    scripts: bool,
    scripts_by_fmsid: bool,
    script_listing: bool,
    container_binary_upload: bool,
    container_base64_upload: bool,
    container_download: bool,
    schema_modification: bool,
    webhooks: bool,
    webhook_query_headers: bool,
    apply_aggregation: bool,
    type_casting: bool,
    parameterized_filters: bool,
    simplified_query_syntax: bool,
    nested_queries: bool,
    batch_preference_inheritance: bool,
    metadata_filtering: bool,
    immutable_id_urls: bool,
    fm_comment: bool,
    ai_annotation: bool,
    computed_annotation: bool,
    server_version_annotation: bool,
    enriched_fm_comment: bool,
    auth_basic: bool,
    auth_oauth: bool,
) -> FMFeatureFlags:
    return FMFeatureFlags(
        service_document=service_document,
        metadata=metadata,
        database_listing=database_listing,
        table_listing=table_listing,
        record_crud=record_crud,
        record_references=record_references,
        cross_join=cross_join,
        batch=batch,
        scripts=scripts,
        scripts_by_fmsid=scripts_by_fmsid,
        script_listing=script_listing,
        container_binary_upload=container_binary_upload,
        container_base64_upload=container_base64_upload,
        container_download=container_download,
        schema_modification=schema_modification,
        webhooks=webhooks,
        webhook_query_headers=webhook_query_headers,
        apply_aggregation=apply_aggregation,
        type_casting=type_casting,
        parameterized_filters=parameterized_filters,
        simplified_query_syntax=simplified_query_syntax,
        nested_queries=nested_queries,
        batch_preference_inheritance=batch_preference_inheritance,
        metadata_filtering=metadata_filtering,
        immutable_id_urls=immutable_id_urls,
        fm_comment=fm_comment,
        ai_annotation=ai_annotation,
        computed_annotation=computed_annotation,
        server_version_annotation=server_version_annotation,
        enriched_fm_comment=enriched_fm_comment,
        auth_basic=auth_basic,
        auth_oauth=auth_oauth,
    )


def _qopts(
    *,
    filter_: bool,
    select: bool,
    orderby: bool,
    top: bool,
    skip: bool,
    expand: bool,
    count: bool,
    apply: bool,
    search: bool = False,
    compute: bool = False,
) -> FMQueryOptionFlags:
    return FMQueryOptionFlags(
        filter=filter_,
        select=select,
        orderby=orderby,
        top=top,
        skip=skip,
        expand=expand,
        count=count,
        apply=apply,
        search=search,
        compute=compute,
    )


#: Feature flag matrix across all supported versions.
#: Import this to programmatically check feature availability.
FM_VERSION_MATRIX: Dict[FMVersionMajor, FMVersionInfo] = {
    "20": FMVersionInfo(
        major="20",
        name="Claris FileMaker 2023",
        release_year=2023,
        internal_version="20.x",
        status="supported",
        odata_protocol_version="4.0",
        features=_flags(
            service_document=True, metadata=True, database_listing=True, table_listing=True,
            record_crud=True, record_references=True, cross_join=True, batch=True,
            scripts=True, scripts_by_fmsid=False, script_listing=False,
            container_binary_upload=True, container_base64_upload=True, container_download=True,
            schema_modification=True, webhooks=False, webhook_query_headers=False,
            apply_aggregation=False, type_casting=False, parameterized_filters=False,
            simplified_query_syntax=False, nested_queries=False, batch_preference_inheritance=False,
            metadata_filtering=False, immutable_id_urls=False,
            fm_comment=False, ai_annotation=False, computed_annotation=False,
            server_version_annotation=False, enriched_fm_comment=False,
            auth_basic=True, auth_oauth=False,
        ),
        query_options=_qopts(
            filter_=True, select=True, orderby=True, top=True, skip=True,
            expand=True, count=True, apply=False,
        ),
    ),
    "21": FMVersionInfo(
        major="21",
        name="Claris FileMaker 2024",
        release_year=2024,
        internal_version="21.x",
        status="supported",
        odata_protocol_version="4.01",
        features=_flags(
            service_document=True, metadata=True, database_listing=True, table_listing=True,
            record_crud=True, record_references=True, cross_join=True, batch=True,
            scripts=True, scripts_by_fmsid=True, script_listing=False,
            container_binary_upload=True, container_base64_upload=True, container_download=True,
            schema_modification=True, webhooks=False, webhook_query_headers=False,
            apply_aggregation=False, type_casting=True, parameterized_filters=True,
            simplified_query_syntax=True, nested_queries=True, batch_preference_inheritance=True,
            metadata_filtering=False, immutable_id_urls=False,
            fm_comment=True, ai_annotation=True, computed_annotation=False,
            server_version_annotation=False, enriched_fm_comment=False,
            auth_basic=True, auth_oauth=True,
        ),
        query_options=_qopts(
            filter_=True, select=True, orderby=True, top=True, skip=True,
            expand=True, count=True, apply=False,
        ),
    ),
    "22": FMVersionInfo(
        major="22",
        name="Claris FileMaker 2025",
        release_year=2025,
        internal_version="22.x",
        status="supported",
        odata_protocol_version="4.01",
        features=_flags(
            service_document=True, metadata=True, database_listing=True, table_listing=True,
            record_crud=True, record_references=True, cross_join=True, batch=True,
            scripts=True, scripts_by_fmsid=True, script_listing=False,
            container_binary_upload=True, container_base64_upload=True, container_download=True,
            schema_modification=True, webhooks=True, webhook_query_headers=True,
            apply_aggregation=True, type_casting=True, parameterized_filters=True,
            simplified_query_syntax=True, nested_queries=True, batch_preference_inheritance=True,
            metadata_filtering=True, immutable_id_urls=False,
            fm_comment=True, ai_annotation=True, computed_annotation=False,
            server_version_annotation=False, enriched_fm_comment=False,
            auth_basic=True, auth_oauth=True,
        ),
        query_options=_qopts(
            filter_=True, select=True, orderby=True, top=True, skip=True,
            expand=True, count=True, apply=True,
        ),
    ),
    "26": FMVersionInfo(
        major="26",
        name="Claris FileMaker 2026",
        release_year=2026,
        internal_version="26.x",
        status="current",
        odata_protocol_version="4.01",
        features=_flags(
            service_document=True, metadata=True, database_listing=True, table_listing=True,
            record_crud=True, record_references=True, cross_join=True, batch=True,
            scripts=True, scripts_by_fmsid=True, script_listing=True,
            container_binary_upload=True, container_base64_upload=True, container_download=True,
            schema_modification=True, webhooks=True, webhook_query_headers=True,
            apply_aggregation=True, type_casting=True, parameterized_filters=True,
            simplified_query_syntax=True, nested_queries=True, batch_preference_inheritance=True,
            metadata_filtering=True, immutable_id_urls=True,
            fm_comment=True, ai_annotation=True, computed_annotation=True,
            server_version_annotation=True, enriched_fm_comment=True,
            auth_basic=True, auth_oauth=True,
        ),
        query_options=_qopts(
            filter_=True, select=True, orderby=True, top=True, skip=True,
            expand=True, count=True, apply=True,
        ),
    ),
    "future": FMVersionInfo(
        major="future",
        name="Future / next",
        release_year=None,
        internal_version="unknown",
        status="future",
        odata_protocol_version="4.01",
        features=_flags(
            service_document=True, metadata=True, database_listing=True, table_listing=True,
            record_crud=True, record_references=True, cross_join=True, batch=True,
            scripts=True, scripts_by_fmsid=True, script_listing=True,
            container_binary_upload=True, container_base64_upload=True, container_download=True,
            schema_modification=True, webhooks=True, webhook_query_headers=True,
            apply_aggregation=True, type_casting=True, parameterized_filters=True,
            simplified_query_syntax=True, nested_queries=True, batch_preference_inheritance=True,
            metadata_filtering=True, immutable_id_urls=True,
            fm_comment=True, ai_annotation=True, computed_annotation=True,
            server_version_annotation=True, enriched_fm_comment=True,
            auth_basic=True, auth_oauth=True,
        ),
        query_options=_qopts(
            filter_=True, select=True, orderby=True, top=True, skip=True,
            expand=True, count=True, apply=True,
        ),
    ),
}

#: Ordered list of concrete (non-future) versions, oldest first.
_VERSION_ORDER = ("20", "21", "22", "26")


def has_feature(version: FMVersionMajor, feature: str) -> bool:
    """Check if a feature is available in a given version.

    ``feature`` is the snake_case name of a field on :class:`FMFeatureFlags`
    (e.g. ``"webhooks"``, ``"scripts_by_fmsid"``).
    """
    info = FM_VERSION_MATRIX.get(version)
    if info is None:
        return False
    return getattr(info.features, feature, False)


def has_query_option(version: FMVersionMajor, option: str) -> bool:
    """Check if a query option is available in a given version.

    ``option`` is the snake_case name of a field on :class:`FMQueryOptionFlags`
    (e.g. ``"filter"``, ``"apply"``). The leading ``$`` from the OData option
    name is dropped in the Python field name.
    """
    info = FM_VERSION_MATRIX.get(version)
    if info is None:
        return False
    return getattr(info.query_options, option, False)


def min_version_for_feature(feature: str) -> Optional[FMVersionMajor]:
    """Get the minimum version that supports a given feature, or ``None``."""
    for v in _VERSION_ORDER:
        if getattr(FM_VERSION_MATRIX[v].features, feature, False):
            return v
    return None
