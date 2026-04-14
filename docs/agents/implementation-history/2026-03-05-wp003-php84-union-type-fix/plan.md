# Plan

## Summary

WP-003 of the `2026-03-05-synthesis-strategic-rework` plan was executed under the false premise that the project targets PHP >= 7.4. The project in fact requires PHP >= 8.4 (`composer.json`). WP-003 "corrected" the `api-surface.md` entry for `ClassCache::findClassesInFolder()` by removing a native-looking union type and replacing it with PHP 7.4-style separate `@param` / `@phpstan-param` docblock annotations. That result is internally consistent with the current source (which happens to use an untyped `$folder` parameter), but it is now inconsistent with the PHP 8.4 project baseline and with the recorded technical debt item that explicitly said "add a native union type hint once PHP 8 is the minimum".

This plan:
1. Adds a native `string|FolderInfo` union type to the untyped `$folder` parameter in `ClassCache::findClassesInFolder()`.
2. Updates `api-surface.md` to document the native union type signature.
3. Documents in `constraints.md` that PHP 8.4 native union types are valid and preferred over docblock-only annotations in new and modified code.

## Architectural Context

### Affected Source File

**`src/Mailcode/ClassCache.php`** — static utility class for dynamic class discovery.

Current signature (line 30):
```php
/**
 * @param string|FolderInfo $folder
 * @param bool $recursive
 * @param class-string|null $instanceOf
 * @return class-string[]
 */
public static function findClassesInFolder($folder, bool $recursive=false, ?string $instanceOf=null) : array
```

`$folder` is untyped. The body immediately passes it to `FolderInfo::factory($folder)`, which itself accepts `string|FolderInfo`. A native union type is immediately applicable.

### Affected Manifest File

**`docs/agents/project-manifest/api-surface.md`** — lines 428–440 (the `ClassCache` block), currently documenting:

```php
/** @param string|FolderInfo $folder */
/** @phpstan-param class-string|null $instanceOf */
public static function findClassesInFolder(
    $folder,
    bool $recursive = false,
    ?string $instanceOf = null
): array; // class-string[]
```

This split-docblock form is a PHP 7.4 workaround pattern and should be replaced with a proper PHP 8 signature.

### Affected Manifest Constraints File

**`docs/agents/project-manifest/constraints.md`** — contains typing rules. Currently has no explicit statement on PHP 8 native union types being allowed.

### PHP 8.4 Baseline Confirmation

- `composer.json`: `"php": ">=8.4"`
- `tech-stack.md` (just corrected): `Minimum Version: >= 8.4`
- `AGENTS.md` (just corrected): `PHP >= 8.4`

## Approach / Architecture

Three targeted changes:

1. **Source fix** (`ClassCache.php`): Add `string|FolderInfo` as a native union type to `$folder`. Remove the now-redundant `@param string|FolderInfo $folder` docblock line (the `@param bool`, `@param class-string|null`, and `@return` lines stay). Keep `@phpstan-param class-string|null $instanceOf` because `?string` is the native type but PHPStan needs the `class-string` refinement.

2. **Manifest fix** (`api-surface.md`): Update the `ClassCache::findClassesInFolder()` documented signature to use the native union type, removing the split docblock annotations.

3. **Constraints update** (`constraints.md`): Add a rule stating that PHP 8.4 native union types are valid and preferred over `@param`-only docblock annotations in all new and modified code.

## Rationale

- The project has required PHP >= 8.4 since at least version 3.5.3. Native union types have been available since PHP 8.0.
- Keeping the untyped `$folder` creates a PHPStan workaround that is now unnecessary — the `@phpstan-param class-string|null $instanceOf` annotation only exists because `?string` is weaker. With a properly typed parameter, documentation is self-evident.
- Documenting `api-surface.md` with native types means agents and developers reading the manifest get the true signature, not a downgraded representation.
- Adding an explicit constraint prevents future agents (or this agent's successors) from repeating the PHP 7.4 docblock workaround mistake.

## Detailed Steps

### Step 1 — Update `ClassCache.php`

File: `src/Mailcode/ClassCache.php`

1. Change the `$folder` parameter from untyped to `string|FolderInfo`:
   ```php
   // Before
   public static function findClassesInFolder($folder, bool $recursive=false, ?string $instanceOf=null) : array
   
   // After
   public static function findClassesInFolder(string|FolderInfo $folder, bool $recursive=false, ?string $instanceOf=null) : array
   ```

2. Remove the now-redundant `@param string|FolderInfo $folder` docblock line (the union type is self-documenting in the signature).

3. Keep the `@phpstan-param class-string|null $instanceOf` annotation — it refines `?string` to `class-string|null`, which PHPStan cannot infer from the native type alone.

### Step 2 — Update `api-surface.md`

File: `docs/agents/project-manifest/api-surface.md`

Replace the `ClassCache` block (currently lines 428–440) with the native-type form:

```php
class ClassCache
{
    /** @phpstan-param class-string|null $instanceOf */
    public static function findClassesInFolder(
        string|FolderInfo $folder,
        bool $recursive = false,
        ?string $instanceOf = null
    ): array; // class-string[]
}
```

### Step 3 — Update `constraints.md`

File: `docs/agents/project-manifest/constraints.md`

Add to the **Strict Typing** or **Code Style** section:

> **PHP 8.4 type system:** The project requires PHP >= 8.4. Native union types (`string|FolderInfo`), intersection types, and `never` are available and preferred over `@param`-only docblock annotations for all new and modified code. Use `@phpstan-param` annotations only when PHPStan requires a refinement that the native type cannot express (e.g., `class-string` narrowing within a `string` type).

## Dependencies

- None. All three changes are independent edits to different files.

## Required Components

| Component | File | Change Type |
|---|---|---|
| ClassCache source | `src/Mailcode/ClassCache.php` | Add native union type to `$folder`; remove redundant `@param` docblock line |
| API surface manifest | `docs/agents/project-manifest/api-surface.md` | Update `ClassCache` block to native-type form |
| Constraints manifest | `docs/agents/project-manifest/constraints.md` | Add PHP 8.4 union-type guidance |

## Assumptions

- `FolderInfo::factory()` (the only consumer of `$folder` inside the method body) already accepts `string|FolderInfo`. The existing source confirms this.
- No other call sites pass a value to `findClassesInFolder()` that is not already a `string` or `FolderInfo` instance. PHPStan at level 9 will catch any call-site violations immediately after the type is added.
- No other parameters across `api-surface.md` are affected by the PHP 7.4 docblock workaround pattern — this plan addresses only the confirmed instance. A broader audit is out of scope.

## Constraints

- PHP >= 8.4 is the minimum runtime; native union types are fully supported.
- PHPStan level 9 must remain clean after the change.
- All 519 tests must continue to pass.
- `declare(strict_types=1)` must remain in `ClassCache.php`.

## Out of Scope

- Broader `api-surface.md` audit for other PHP 7-style docblock patterns.
- Adding native types to any other source files.
- Removing the `@phpstan-param class-string|null $instanceOf` annotation (it provides value).

## Acceptance Criteria

- [ ] `ClassCache::findClassesInFolder()` has a native `string|FolderInfo` union type on `$folder` in source.
- [ ] The redundant `@param string|FolderInfo $folder` docblock line is removed from `ClassCache.php`.
- [ ] `api-surface.md` documents the method with the native union type signature (no split `@param` docblock).
- [ ] `constraints.md` contains a rule permitting and preferring PHP 8.4 native union types.
- [ ] `composer analyze` → 0 errors at PHPStan level 9.
- [ ] `composer test` → all 519 tests pass, 0 warnings.

## Testing Strategy

- Run `composer analyze` after Step 1 to confirm PHPStan is happy with the native union type at the call site inside `findClassesInFolder()` and at all external call sites.
- Run `composer test` after Step 1 to confirm no runtime regressions.
- Steps 2 and 3 are documentation-only; a final `composer test` pass after all steps confirms no accidental source edits occurred.

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| **External call sites pass a type that is neither `string` nor `FolderInfo`** | PHPStan level 9 will immediately surface any such call site as an error; fix before committing. |
| **`FolderInfo` import missing in `ClassCache.php`** | It is already imported (`use AppUtils\FileHelper\FolderInfo;` at line 12) — confirmed in source. |
