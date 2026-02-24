# AGENTS.md — Mailcode

> **Operating manual for AI agents.** Read this before touching any code.
> Last updated: 2026-02-24

---

## 1. Project Manifest — Start Here!

The Project Manifest is the **single source of truth** for this codebase. Consult it before reading implementation code.

**Location:** `docs/agents/project-manifest/`

| File | Description |
|------|-------------|
| [README.md](docs/agents/project-manifest/README.md) | Project purpose, scope, and manifest index |
| [tech-stack.md](docs/agents/project-manifest/tech-stack.md) | PHP runtime, all dependencies, architectural patterns, build & quality tools |
| [file-tree.md](docs/agents/project-manifest/file-tree.md) | Annotated directory structure for the entire project |
| [api-surface.md](docs/agents/project-manifest/api-surface.md) | Public constructors, properties, and method signatures for all major classes |
| [data-flows.md](docs/agents/project-manifest/data-flows.md) | Step-by-step interaction paths (parse, safeguard, translate, factory, pre-process) |
| [constraints.md](docs/agents/project-manifest/constraints.md) | Naming conventions, strict-typing rules, error handling, non-obvious gotchas |

### Quick-Start Ingestion Path

Follow this sequence at the start of every agent session:

1. **Read `README.md`** — Understand what Mailcode is and what it does.
2. **Read `tech-stack.md`** — Know the runtime, dependencies, and architectural patterns.
3. **Read `constraints.md`** — Internalize naming conventions, error handling, and gotchas before writing any code.
4. **Read `file-tree.md`** — Build a mental map of where things live.
5. **Reference `api-surface.md`** — Look up exact signatures before calling or implementing methods.
6. **Reference `data-flows.md`** — Confirm the correct pipeline before modifying a flow.

---

## 2. Manifest Maintenance Rules

When code changes, keep the manifest in sync. The table below maps change types to the documents that must be updated.

| Change Made | Documents to Update |
|-------------|---------------------|
| New command added (`{xyz}`) | `api-surface.md`, `file-tree.md` |
| New translator syntax or translation class added | `api-surface.md`, `file-tree.md`, `constraints.md` (translation coverage table) |
| New public method on any major class | `api-surface.md` |
| New dependency added or removed | `tech-stack.md` |
| Directory added, renamed, or removed | `file-tree.md` |
| New architectural pattern introduced | `tech-stack.md` |
| New naming convention or gotcha discovered | `constraints.md` |
| New key data flow path introduced | `data-flows.md` |
| Build tool or quality tool changed | `tech-stack.md` |
| New localization file added | `tech-stack.md` (Localization table) |
| Version bumped | `tech-stack.md` (Package Identity table), `changelog.md` |

---

## 3. Efficiency Rules — Search Smart

Never scan source files to answer a question that the manifest already answers.

- **Finding a file or directory?** Check `file-tree.md` FIRST.
- **Looking up a method signature?** Check `api-surface.md` FIRST.
- **Understanding a dependency or pattern?** Check `tech-stack.md` FIRST.
- **Tracing how a feature works end-to-end?** Check `data-flows.md` FIRST.
- **Unsure about a naming rule or edge case?** Check `constraints.md` FIRST.
- **Only then** read source files to fill gaps the manifest does not cover.

---

## 4. Failure Protocol & Decision Matrix

| Scenario | Action | Priority |
|----------|--------|----------|
| Ambiguous requirement | Use the most restrictive interpretation; flag the ambiguity in your response | MUST |
| Manifest contradicts code | Trust the manifest; flag the code location for correction | MUST |
| Missing manifest documentation | Flag the gap explicitly; do NOT invent or assume facts | MUST |
| Cache folder not configured | Always set via `Mailcode::setCacheFolder(FolderInfo::factory(...))` before any library use | MUST |
| Adding a new command | Follow the `Mailcode_Commands_Command_*` underscore naming convention; add paired `Interfaces/Commands/Validation/` and `Traits/Commands/Validation/` if introducing a new capability | MUST |
| Adding a translation class | Place it in `Translator/Syntax/{SyntaxName}/`; it will be auto-discovered by `ClassCache` | MUST |
| Validation error vs. exception | Validation errors go into `Mailcode_Collection` (not thrown); throw `Mailcode_Exception` only for internal/programming errors | MUST |
| Replacing one formatting replacer | Replacers are mutually exclusive — remove the existing one before applying a new one | MUST |
| HubL translation missing for a command | HubL coverage is intentionally partial; do not add a stub — flag it and confirm with the maintainer | SHOULD |
| PHPStan level drops below 9 | Restore compliance before committing; the project is clean at level 9 | MUST |
| Untested code path | Add a test recommendation alongside the implementation | SHOULD |

---

## 5. Composer Scripts

Use these scripts via `composer run <script>` from the project root.

| Script | Command | Purpose |
|--------|---------|---------|
| `analyze` | `composer analyze` | Run PHPStan static analysis |
| `analyze-save` | `composer analyze-save` | Run PHPStan and save results to `phpstan-result.txt` |
| `analyze-clear` | `composer analyze-clear` | Clear PHPStan result cache |
| `test` | `composer test` | Run the full PHPUnit test suite |
| `test-file` | `composer test-file` | Run PHPUnit without progress output (for file-level use) |
| `test-suite` | `composer test-suite -- <SuiteName>` | Run a specific test suite by name |
| `test-filter` | `composer test-filter -- <pattern>` | Run tests matching a name filter |
| `test-group` | `composer test-group -- <group>` | Run tests belonging to a specific group |

PHPStan is configured via `phpstan.neon` in the project root (includes `tests/phpstan/config.neon`).

---

## 6. Project Stats

| Item | Value |
|------|-------|
| **Language / Runtime** | PHP >= 7.4 (tested through 8.4) |
| **Architecture Pattern** | Registry + Factory + Strategy + Pipeline + Placeholder/Safeguard |
| **Package Manager** | Composer (`composer.json`) |
| **Test Framework** | PHPUnit >= 9.6 (`phpunit.xml`, suites under `tests/testsuites/`) |
| **Static Analysis** | PHPStan >= 1.10, level 9 (`tests/phpstan/config.neon`) |
| **Build Tool** | Makefile |
| **Strict Typing** | `declare(strict_types=1)` in all source files |
| **Namespace Convention** | Underscore-delimited (`Mailcode_Component_Sub`) for most classes; namespaced (`Mailcode\...`) for newer code |
| **Current Version** | 3.5.3 |
| **License** | MIT |
