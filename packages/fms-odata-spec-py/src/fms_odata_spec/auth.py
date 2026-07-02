"""Authentication types and helpers for the FileMaker OData API.

Mirrors ``src/auth.ts`` from ``@fms-odata/spec-ts``.

@see docs/04-authentication.md
"""

from __future__ import annotations

import base64
from dataclasses import dataclass
from typing import Awaitable, Callable, Dict, Literal, Optional, Union

__all__ = [
    "FMAuthScheme",
    "FMAuthToken",
    "FMAuthTokenProvider",
    "FMBasicAuthConfig",
    "FMOAuthAuthConfig",
    "FMAuthConfig",
    "FMAuthHeaders",
    "basic_auth",
    "bearer_auth",
    "normalize_auth_token",
]

#: Authentication scheme supported by FileMaker OData.
FMAuthScheme = Literal["Basic", "Bearer"]

#: Static auth token string (e.g. ``"Basic dXNlcjpwYXNz"`` or ``"Bearer <token>"``).
FMAuthToken = str

#: Token provider function. Returns the auth header value.
#:
#: May return a coroutine to support token refresh (e.g. OAuth token
#: expiry). The :func:`basic_auth` / :func:`bearer_auth` helpers themselves stay
#: synchronous (they only build header strings), matching the TS helpers which
#: also do not await.
FMAuthTokenProvider = Callable[[], Union[str, Awaitable[str]]]


@dataclass(frozen=True)
class FMBasicAuthConfig:
    """Configuration for Basic auth (FileMaker Server on-premise)."""

    scheme: Literal["Basic"]
    account: str
    password: str

    def __post_init__(self) -> None:
        if self.scheme != "Basic":
            raise ValueError(f"FMBasicAuthConfig.scheme must be 'Basic', got {self.scheme!r}")


@dataclass(frozen=True)
class FMOAuthAuthConfig:
    """Configuration for OAuth/Bearer auth (external identity providers, FileMaker Cloud)."""

    scheme: Literal["Bearer"]
    token: str
    #: Optional refresh callback invoked on 401 responses.
    on_unauthorized: Optional[Callable[[], Awaitable[str]]] = None

    def __post_init__(self) -> None:
        if self.scheme != "Bearer":
            raise ValueError(f"FMOAuthAuthConfig.scheme must be 'Bearer', got {self.scheme!r}")


#: Union of auth configurations, discriminated by the ``scheme`` field.
FMAuthConfig = Union[FMBasicAuthConfig, FMOAuthAuthConfig]


@dataclass
class FMAuthHeaders:
    """Standard auth-related headers."""

    Authorization: str
    OData_Version: Optional[Literal["4.0", "4.01"]] = None
    OData_MaxVersion: Optional[Literal["4.0", "4.01"]] = None

    def to_dict(self) -> Dict[str, str]:
        """Return the headers as a plain dict, dropping unset entries."""
        result: Dict[str, str] = {"Authorization": self.Authorization}
        if self.OData_Version is not None:
            result["OData-Version"] = self.OData_Version
        if self.OData_MaxVersion is not None:
            result["OData-MaxVersion"] = self.OData_MaxVersion
        return result


def basic_auth(account: str, password: str) -> str:
    """Build a Basic auth header value from account and password."""
    raw = f"{account}:{password}".encode("utf-8")
    return f"Basic {base64.b64encode(raw).decode('ascii')}"


def bearer_auth(token: str) -> str:
    """Build a Bearer auth header value from an OAuth session token."""
    return f"Bearer {token}"


def normalize_auth_token(token: str) -> str:
    """Normalize a token string: if it already has a scheme prefix, use as-is."""
    if token.startswith(("Basic ", "Bearer ")):
        return token
    # Default to Bearer for bare tokens (callers should use basic_auth() or
    # bearer_auth() helpers).
    return f"Bearer {token}"
