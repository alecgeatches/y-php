# Decisions Log

A living record of cross-cutting choices made while porting. **Read this before writing code; append to it when you make a decision a later milestone could depend on.** This is the glue that keeps independently-run agents byte-compatible — if another agent would otherwise have to guess what you did, it belongs here.

## What to log

- Shared primitives and their names (the byte buffer, `>>>` helper, float pack codes).
- Representation choices (how `undefined` decodes, how binary is carried).
- Any **deviation from the JS source**, with the reason.
- Tests skipped or adapted, with the reason.
- Anything you spent more than a few minutes deciding.

Do **not** log routine ports that follow the JS source faithfully — only the choices.

## Entry format

```
### DEC-NNNN — <short title>
- **Milestone:** M_
- **Status:** accepted | superseded by DEC-MMMM
- **Decision:** what was chosen, concretely (name the class/method/constant).
- **Why:** the reasoning, especially any JS behavior being matched.
- **Affects:** which later milestones or modules must follow this.
```

Use the next free `DEC-NNNN` number. Never edit a prior entry's decision; if it changes, add a new entry and mark the old one `superseded by DEC-MMMM`.

---

## Locked at planning time

### DEC-0001 — Target platform and packaging
- **Milestone:** planning
- **Status:** accepted
- **Decision:** PHP 7.4+ floor, Composer package, PSR-4 namespace `Yjs\`. lib0 ported under `Yjs\Lib0`.
- **Why:** PHP 7.4 is the floor for the WordPress versions we deploy on; Composer + PSR-4 keeps it consumable as a standard library.
- **Affects:** all milestones.

### DEC-0002 — Byte-exact parity is the contract
- **Milestone:** planning
- **Status:** accepted
- **Decision:** PHP encoder output must be byte-identical to JS for the same logical operation. Mirror the JS source rather than refactoring it.
- **Why:** Full wire-interop with live JS clients is the project's reason to exist.
- **Affects:** all milestones, especially every encoder/decoder.

### DEC-0003 — V2 encoding deferred
- **Milestone:** planning
- **Status:** accepted
- **Decision:** Implement V1 update encoding throughout; defer `UpdateEncoderV2`/`DecoderV2` and V1↔V2 conversion to M7. The sync protocol used by the convergence harness is V1-only.
- **Why:** Reduces scope of the critical path; V2 only matters if a consumer requires that wire format.
- **Affects:** M2–M6 implement V1 only; M7 revisits.

### DEC-0004 — PHP 7.4 language baseline
- **Milestone:** planning
- **Status:** accepted
- **Decision:** Write to the 7.4 feature set: no enums (use `final class` + `const`), no `readonly`, no constructor promotion, no native union types (use PHPDoc), no `match`/named-args/nullsafe/first-class-callable/`mixed`. Typed properties and arrow functions are allowed.
- **Why:** Keeps the library runnable on the PHP 7.4 floor set in DEC-0001 ([WP/PHP compatibility table](https://make.wordpress.org/core/handbook/references/php-compatibility-and-wordpress-versions/)).
- **Affects:** every milestone — all produced PHP. CONTEXT "PHP language baseline" governs the specifics.

### DEC-0005 — WPCS minus naming sniffs; camelCase API; PSR-4 filenames
- **Milestone:** planning
- **Status:** accepted
- **Decision:** Produced PHP must pass **WordPress Coding Standards** via PHPCS, but exclude `WordPress.NamingConventions.ValidFunctionName`, `…ValidVariableName`, and `WordPress.Files.FileName`. Keep Yjs's **camelCase** method/property names and **PSR-4** filenames (`Item.php`). All other WPCS rules (Yoda, spacing, security) apply. Config lives in `phpcs.xml.dist`; gate with `composer lint`.
- **Why:** WPCS naming requires snake_case methods and `class-*.php` files, which collides with the prime directive of mirroring the Yjs API 1:1 (DEC-0002). Keeping camelCase preserves fidelity to the JS source and API familiarity for Yjs users while still getting WPCS's formatting/security value.
- **Affects:** every milestone; the M0 tooling setup; the public API surface.

<!-- New entries below this line -->
