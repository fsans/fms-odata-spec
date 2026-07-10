# AGENTS.md

Guidance for AI agents (Devin, Claude, Cursor, etc.) working on this repository.

## Project type

This is a **specification repository**, not a runnable application. It contains:

1. Markdown documentation (`docs/`) — the human-readable spec.
2. A JSON capability manifest (`schema/fms-odata-capabilities.json`) — machine-readable.
3. A TypeScript types package (`packages/fms-odata-spec-ts/`) — shared types for downstream libraries (npm: `@fms-odata/spec-ts`).
4. A Python types package (`packages/fms-odata-spec-py/`) — the same surface as the TS package, as stdlib dataclasses (PyPI: `fms-odata-spec`).
5. A PHP types package (`packages/fms-odata-spec-php/`) — the same surface as the TS package, as readonly DTOs and string-backed enums (Packagist: `fsans/fms-odata-spec-php`).

## Key conventions

- **No emojis** in any generated markdown or code.
- **No runtime code** — the TS package is types-only (no implementation logic). The Python package is types + pure helpers only (no HTTP client, no validation framework). The PHP package is types + pure helpers only (no HTTP client, no validation framework, no runtime Composer dependencies).
- **Source of truth**: official Claris OData docs (<https://help.claris.com/en/odata-guide/>) + observed behavior from the two reference repos in `_research/` (gitignored).
- **Version naming**: use "Claris 2023" (v20.x), "Claris 2024" (v21.x), "Claris 2025" (v22.x), "Claris 2026" (v26.x, current). Version 19.x is dropped and no longer supported. Never guess future version numbers.
- **OData protocol version**: FileMaker implements OData 4.0 for v20.x, and partial OData 4.01 from v21.1 onward, at intermediate conformance level with some exceptions. The URL version segment remains `v4` for all versions. The 4.01 features added in v21.1 include: simplified query syntax (no `$` prefix required), argument parameterization, advanced query nesting, data type casting, and batch preference inheritance.
- **URL pattern**: `https://host/fmi/odata/v4/<database>/<resource>` — version segment is always `v4`.

## Branching model (Git Flow)

- **`main`**: Stable releases only. Every commit is a merge from `develop` and is tagged with an annotated version tag.
- **`develop`**: Active development branch. All work lands here first.
- Feature branches (optional): branch off `develop`, merge back into `develop`.

**When making changes:**

1. Commit work to `develop`.
2. When ready for release, merge `develop` into `main` (fast-forward or merge commit).
3. Tag the release on `main` with an annotated tag: `git tag -a vMAJOR.MINOR.PATCH -m "..."`.
4. Push everything: `git push origin develop main --tags`.

**Tag rules:**

- Semantic versioning: `vMAJOR.MINOR.PATCH`.
- Annotated tags only (`git tag -a`), never lightweight tags.
- Tags only on `main`, never on `develop`.
- If `@fms-odata/spec-ts` package version changes, the tag must match it.

## When updating the spec for a new FileMaker Server release

1. Read the official Claris OData docs for the new version.
2. Update `docs/12-version-deltas.md` with a new section for the release.
3. Update `docs/01-conformance.md` if conformance level or supported/unsupported features changed.
4. Update `docs/13-quirks.md` if new quirks or bug fixes were observed.
5. Update `schema/fms-odata-capabilities.json` with new version entry and feature flags.
6. Update `packages/fms-odata-spec-ts/src/versions.ts` with the new version constant and feature matrix.
7. Update `packages/fms-odata-spec-py/src/fms_odata_spec/versions.py` with the same version constant and feature matrix (mirror the TS package).
8. Update `packages/fms-odata-spec-php/src/Versions/Versions.php` with the same version constant and feature matrix (mirror the TS package).
9. Update `README.md` version table if a new version was added.

## File structure rules

- `_research/` is gitignored — never commit cloned repos.
- `docs/` files are numbered (`00-`, `01-`, ...) for reading order.
- `packages/fms-odata-spec-ts/` is a publishable npm package (`@fms-odata/spec-ts`).
- `packages/fms-odata-spec-py/` is a publishable Python package (`fms-odata-spec` on PyPI), versioned independently of the TS package. It is NOT part of any npm workspace glob.
- `packages/fms-odata-spec-php/` is a publishable PHP package (`fsans/fms-odata-spec-php` on Packagist), versioned independently of the TS and Python packages. It is NOT part of any npm workspace glob.
- `schema/` contains only the single capabilities JSON manifest.

## Verification

There is no build step for the docs. For the TS package:

```bash
cd packages/fms-odata-spec-ts
npm install
npm run build      # tsc --emitDeclarationOnly
npm run typecheck  # tsc --noEmit
```

The JSON manifest can be validated with any JSON schema validator. No formal JSON Schema is provided yet — the manifest is self-describing.

For the Python package:

```bash
cd packages/fms-odata-spec-py
python -m venv .venv
source .venv/bin/activate        # Windows: .venv\Scripts\activate
pip install -e ".[dev]"
pytest                           # 173 tests
python -m build                  # sdist + wheel into dist/
```

For the PHP package (run from the repo root, where the Packagist `composer.json` lives):

```bash
composer install
composer test        # PHPUnit (283 tests)
composer analyse     # PHPStan level max
composer check       # tests + static analysis
```

## PHP package publishing (Packagist)

Packagist only reads `composer.json` from the repository root (default
branch). The PHP package source lives in `packages/fms-odata-spec-php/`,
but a root-level `composer.json` exists with PSR-4 autoload paths pointing
into that subdirectory. To release:

1. Ensure the work is merged to `main` and `develop`.
2. Tag the merge commit on `main` with a bare version number (no `v` prefix,
   no `php-` prefix):
   ```bash
   git tag -a 2.0.1 -m "PHP package 2.0.1 — ..."
   ```
3. Push:
   ```bash
   git push origin main --tags
   ```
4. On Packagist, the repo URL `https://github.com/fsans/fms-odata-spec` is
   registered. Packagist reads the root `composer.json` on `main` and
   resolves versions from bare `MAJOR.MINOR.PATCH` tags.

**Tag rules for PHP:**

- Bare `MAJOR.MINOR.PATCH` (e.g. `2.0.1`), not `php-v2.0.1`.
- Annotated tags only (`git tag -a`).
- Tags on `main`, same as the spec/TS tags.
- Distinct from the TS `vMAJOR.MINOR.PATCH` and Python `py-vMAJOR.MINOR.PATCH`
  tags (which use prefixes).

## TODO — fms-odata-spec-py v2.0.0 release blockers

> **IMPORTANT — read this before cutting the Python v2.0.0 release.**
> These items were intentionally deferred during the initial port and MUST be
> addressed before publishing `fms-odata-spec` 2.0.0 to PyPI. Do NOT silently
> drop them; either complete them or explicitly move them to a later milestone.

1. **PyPI publishing workflow** — `.github/workflows/py-publish.yml` exists
   and triggers on `py-v*` tag pushes, runs tests, builds, and uploads to PyPI
   via `twine upload` using a `PYPI_API_TOKEN` repository secret. **Remaining
   steps to enable it:**
   - Create a PyPI API token at <https://pypi.org/manage/account/token/> (the
     first upload creates the `fms-odata-spec` project; an account-scoped token
     works for the first upload, a project-scoped token afterwards).
   - Add it as the GitHub repository secret `PYPI_API_TOKEN` (Settings →
     Secrets and variables → Actions → New repository secret).
   - Create a `pypi` environment (Settings → Environments → New environment)
     — the workflow references `environment: pypi` for protection rules
     (optional but recommended: require manual approval, restrict to `main`).
   - Trigger by pushing the `py-v2.0.0` tag (or a new tag for the next
     release). Note the older `py-v0.1.0` tag did not trigger the workflow
     because the workflow file did not exist at its push time.
   Tag scheme is `py-vX.Y.Z` (distinct from the TS `vMAJOR.MINOR.PATCH` tags).

2. ~~**LICENSE bundling**~~ — **DONE.** The root `LICENSE` is copied into
   `packages/fms-odata-spec-py/LICENSE` and declared as a wheel artifact and
   sdist include in `pyproject.toml`. Verified the LICENSE ships in both the
   wheel (`dist-info/licenses/LICENSE`) and the sdist.

3. **CHANGELOG** — no `CHANGELOG.md` exists for the Python package (the TS
   package has none either, so this is consistent). For semver discipline on
   PyPI, add at least a `packages/fms-odata-spec-py/CHANGELOG.md` with the
   2.0.0 entry before publishing.

4. **`ODataEntity[T]` ergonomics review** — the wrapping-dataclass approach
   means callers access `envelope.entity.field` rather than `envelope.field`.
   This is the one place the Python API is noticeably less ergonomic than the
   TS intersection type. Before 2.0.0, decide whether to keep it as-is (and
   document it loudly) or add a `from_dict` constructor / `Mapping`-backed
   variant. Changing it after 2.0.0 is a breaking change.

5. **CI matrix Python 3.14** — `py-ci.yml` matrix tops out at 3.13. Python 3.14
   is released; add `"3.14"` to the matrix once
   `actions/setup-python` ships a stable 3.14 on the runners.

6. **Async token-refresh helper** — `FMOAuthAuthConfig.on_unauthorized` is typed
   but nothing invokes it (this package is types + pure helpers only). This
   matches the TS package, but document it explicitly in the Python README so
   downstream consumers know they must wire it up themselves.

7. **Shared schema source** — if a genuinely shared schema/spec source (JSON
   Schema, OpenAPI, or OData CSDL) is introduced later, both language packages
   should be generated from or validated against it. This was deferred per the
   original task instructions ("ask before moving anything"). Revisit before
   the two packages drift apart in content.

## Branch state of the Python package

- Initial port landed on branch `feature/py-spec` (branched from `develop`).
- Merge `feature/py-spec` into `develop` via a PR once reviewed.
- The Python package version (`2.0.0`) and the TS package version (`2.0.0`)
  were bumped together for the v2.0.0 spec overhaul, but they remain
  independently versioned/published and are not required to lockstep in
  future releases unless explicitly asked.
