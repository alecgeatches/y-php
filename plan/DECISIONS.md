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

### DEC-0006 — Build root is `y-php/`
- **Milestone:** M0
- **Status:** accepted
- **Decision:** Treat `y-php/` as the Composer package root. Implementation files live under `y-php/src`, tests under `y-php/tests`, tools under `y-php/tools`; run `composer test`, `composer lint`, `composer lint:fix`, and `node tools/gen-fixtures.mjs` from `y-php/`.
- **Why:** The parent workspace contains sibling JS repositories used as source references. Running patches or build commands from the parent can accidentally create parent-level `src/`, `tests/`, or `tools/` directories outside the PHP package.
- **Affects:** all milestones and all local build/test instructions.

### DEC-0007 — Buffer carrier is `Yjs\Lib0\Buffer`
- **Milestone:** M0
- **Status:** accepted
- **Decision:** Use `Yjs\Lib0\Buffer` as the one wrapped `Uint8Array` representation. It stores bytes as a PHP binary `string` and exposes `fromBinaryString()`, `fromByteArray()`, `toBinaryString()`, `toByteArray()`, `byteLength()`, `get()`, `set()`, `slice()`, base64/base64url/hex helpers, and `copyUint8Array()`.
- **Why:** The wire format is byte-oriented, and PHP strings are the most direct binary carrier. The wrapper keeps later code from mixing raw PHP strings and logical text accidentally.
- **Affects:** all update encoders/decoders, content binary payloads, fixture readers, sync/update tests.

### DEC-0008 — JavaScript 32-bit helpers live in `Yjs\Lib0\Binary`
- **Milestone:** M0
- **Status:** accepted
- **Decision:** Implement JavaScript-style bit coercion in `Yjs\Lib0\Binary`: `toUint32()`, `toInt32()`, `unsignedRightShift()` for `>>>`, plus `shiftLeft32()`, `or32()`, and `xor32()` for PRNG and future clock/client-id logic.
- **Why:** PHP integers are 64-bit and `>>` is arithmetic. Yjs/lib0 relies on JavaScript 32-bit bitwise coercion and unsigned right shift for deterministic encoding and PRNG output.
- **Affects:** PRNG, update codecs, IDs/clocks, any code porting JS bitwise operators.

### DEC-0009 — `undefined` uses a singleton carrier
- **Milestone:** M0
- **Status:** accepted
- **Decision:** Decode lib0 `writeAny` tag `127` to `Yjs\Lib0\UndefinedValue::getInstance()`. `Encoding::writeAny()` recognizes that singleton and writes tag `127`; PHP `null` remains tag `126`.
- **Why:** PHP has no native `undefined`, but lib0 distinguishes `undefined` from `null` on the wire. A singleton preserves the distinction without overloading `null`.
- **Affects:** `writeAny`/`readAny`, JSON-like payloads, future map/text attribute handling.

### DEC-0010 — BigInt64 uses a decimal-string carrier
- **Milestone:** M0
- **Status:** accepted
- **Decision:** Represent lib0 bigint values with `Yjs\Lib0\BigInt64`, storing a signed decimal string. `Encoding::writeBigInt64()` accepts `BigInt64`, `int`, or decimal `string`; `Decoding::readBigInt64()` returns `BigInt64`. Conversion to bytes is big-endian signed 64-bit two's-complement.
- **Why:** PHP 7.4 integers are platform 64-bit here, but a decimal-string carrier avoids crashing or losing the exact edge values when decoding `-9223372036854775808` and keeps the representation explicit for later agents.
- **Affects:** `writeAny`/`readAny`, any future binary fixture or awareness payload containing bigint.

### DEC-0011 — Floats use big-endian pack codes
- **Milestone:** M0
- **Status:** accepted
- **Decision:** Use `pack('G', $num)` / `unpack('G', ...)` for `writeFloat32`/`readFloat32` and `pack('E', $num)` / `unpack('E', ...)` for `writeFloat64`/`readFloat64`.
- **Why:** lib0 writes floats with `DataView` littleEndian `false`, i.e. network/big-endian byte order. Native-order PHP pack codes would break byte parity across platforms.
- **Affects:** all lib0 primitive encoding, `writeAny` number dispatch, fixtures.

### DEC-0012 — PRNG class names mirror lib0
- **Milestone:** M0
- **Status:** accepted
- **Decision:** Port lib0 PRNG as `Yjs\Lib0\Xorshift32`, `Yjs\Lib0\Xoroshiro128plus`, and static helper module `Yjs\Lib0\Prng`. `Prng::create()` returns `Xoroshiro128plus`.
- **Why:** Later fuzz/convergence tests depend on fixed seeds producing the same scenario as JS. Keeping the lib0 class names makes source comparison and fixture diagnosis direct.
- **Affects:** all fuzz tests and any generated randomized test data in later milestones.

### DEC-0013 — M0 PHPCS gate keeps WPCS but excludes doc/name noise
- **Milestone:** M0
- **Status:** accepted
- **Decision:** `phpcs.xml.dist` uses `WordPress` + `PHPCompatibilityWP` with DEC-0005 exclusions, and additionally excludes `Generic.Commenting.DocComment.MissingShort`, `Squiz.Commenting.FunctionComment.Missing`, `Squiz.Commenting.FunctionCommentThrowTag.Missing`, and `Universal.NamingConventions.NoReservedKeywordParameterNames`. The Composer package is `phpcompatibility/phpcompatibility-wp`.
- **Why:** The first M0 lint run showed docblock boilerplate and reserved JS parameter names dominating the signal. Excluding those keeps the lint gate focused on formatting, compatibility, and security while preserving camelCase/lib0 naming. Packagist exposes the PHPCompatibilityWP ruleset package as `phpcompatibility/phpcompatibility-wp`; the unhyphenated name is not installable.
- **Affects:** all PHP code and future Composer installs/lint runs.

### DEC-0014 — Public API stubs use module namespaces plus top-level aliases
- **Milestone:** M1
- **Status:** accepted
- **Decision:** Stub classes live in the planned module namespaces (`Yjs\Utils\Doc`, `Yjs\Types\YArray`, `Yjs\Structs\Item`, etc.) and `src/aliases.php` registers top-level aliases such as `Yjs\Doc`, `Yjs\YArray`, and `Yjs\ContentString`. Free-function exports live as namespace functions in `src/functions.php`; `Yjs\Yjs` is a static facade that forwards to those functions.
- **Why:** The directory layout from CONTEXT stays PSR-4-compliant while top-level aliases keep the public API close to the JS export names. PHP cannot safely mirror every JS alias literally (notably `Array`), so later milestones should use the `Y*` class names already used internally by Yjs.
- **Affects:** all later class implementations, public API docs, translated tests, and any consumer code.

### DEC-0015 — M1 test translation is a named red baseline
- **Milestone:** M1
- **Status:** accepted
- **Decision:** Each exported `yjs/tests/*.tests.js` test is represented by one PHPUnit method with the same test name and source reference. Until behavior milestones replace the bodies, the translated methods enter the public `Doc` stub and fail with `Yjs\NotImplemented`. `testFailsObjectManipulationInDevMode` is explicitly skipped because it relies on JS dev-mode `Object.freeze`, which has no PHP array/object equivalent.
- **Why:** M1's exit criterion is a suite that compiles and fails for the right reason, not behavioral green tests. Keeping the method names/source references creates stable slots for later agents to port each body without rediscovering the JS test inventory.
- **Affects:** all later milestone test ports; the skipped dev-mode freeze test should remain skipped unless a PHP-specific invariant is defined.

### DEC-0016 — WPCS underscore-method sniff is excluded for JS parity
- **Milestone:** M1
- **Status:** accepted
- **Decision:** `phpcs.xml.dist` now excludes `PSR2.Methods.MethodDeclaration.Underscore` in addition to the M0 naming exclusions.
- **Why:** Yjs exposes and internally relies on underscore-prefixed methods such as `_integrate`, `_copy`, `_write`, `_callObserver`, and the test helper `_receive`. Renaming them for style would make source comparison harder and drift from the JS API shape.
- **Affects:** all type/struct ports and translated test helper code that mirrors underscore-prefixed JS methods.

### DEC-0017 — Yjs scenario fixtures use deterministic client IDs
- **Milestone:** M1
- **Status:** accepted
- **Decision:** `tools/gen-fixtures.mjs` writes `tests/fixtures/yjs-scenarios.json` with `json`, `updateHex`, `stateVectorHex`, and `snapshotHex` captured from real `yjs/src/index.js` scenarios. Each scenario pins `doc.clientID` to a deterministic integer before applying operations.
- **Why:** Real Yjs `Doc` client IDs are random by default; pinning them keeps regenerated fixtures byte-stable while preserving the exact JS encoder output for representative array, map, and text operations.
- **Affects:** future fixture-driven byte-parity tests and any new generated Yjs scenario fixtures.

### DEC-0018 — V1 update codecs keep the JS base-class split
- **Milestone:** M2.1
- **Status:** accepted
- **Decision:** Add `Yjs\Utils\DSEncoderV1` and `Yjs\Utils\DSDecoderV1` as concrete base classes with public `restEncoder`/`restDecoder` properties. `Yjs\Utils\UpdateEncoderV1` and `Yjs\Utils\UpdateDecoderV1` extend those classes and write/read directly through the already byte-verified lib0 primitives. `UpdateEncoderV2`/`UpdateDecoderV2` remain M1 stubs.
- **Why:** JS keeps delete-set and update codecs in a class hierarchy, and later modules perform constructor/class-shape-sensitive work. Preserving the V1 base-class shape now avoids changing delete-set, snapshot, and update code later.
- **Affects:** M2.2 delete sets, M2.3 structs/content, M2.4 update integration, M7 V2 codecs.

### DEC-0019 — ID helpers are namespaced functions over a value object
- **Milestone:** M2.1
- **Status:** accepted
- **Decision:** `Yjs\Utils\ID` is a simple value object with public integer `client` and `clock` properties. The JS helper functions live as `Yjs\createID()`, `Yjs\compareIDs()`, `Yjs\writeID()`, `Yjs\readID()`, and `Yjs\findRootTypeKey()` in `src/functions.php`; the public `Yjs\Yjs` facade forwards only the helpers exported by `yjs/src/index.js`.
- **Why:** JS modules import these helpers from `ID.js`, while public `index.js` only re-exports `createID`, `compareIDs`, and `findRootTypeKey`. Keeping `writeID`/`readID` as namespace helpers gives later internal ports the same primitive without expanding the facade surface beyond JS.
- **Affects:** relative positions, snapshots, updates, structs, and any code comparing or serializing IDs.

### DEC-0020 — V1 JSON codec mirrors `JSON.stringify` string bytes
- **Milestone:** M2.1
- **Status:** accepted
- **Decision:** `UpdateEncoderV1::writeJSON()` serializes with JS-compatible JSON string bytes: `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`, top-level `UndefinedValue` writes the string `undefined`, undefined array entries become `null`, undefined object properties are omitted, non-finite floats become `null`, negative zero becomes `0`, and `BigInt64` throws. `UpdateDecoderV1::readJSON()` decodes to PHP `stdClass`/arrays/scalars via `json_decode()` and throws on parse errors.
- **Why:** V1 stores JSON by first applying `JSON.stringify()` and then `writeVarString()`. PHP's default `json_encode()` escapes slashes and Unicode differently from JS, which would change update bytes for ordinary embeds.
- **Affects:** ContentEmbed, ContentJSON-adjacent logic, V1 update fixtures, and any later code using `writeJSON`/`readJSON`.

### DEC-0021 — StructStore and DeleteSet maps are ordered PHP arrays
- **Milestone:** M2.2
- **Status:** accepted
- **Decision:** Represent `StructStore::$clients` and `DeleteSet::$clients` as insertion-ordered PHP arrays keyed by integer client id. `getStateVector()` iterates store clients in insertion order, while `writeDeleteSet()` builds a temporary entry list and sorts clients descending by id before writing, matching `Array.from(ds.clients.entries()).sort((a, b) => b[0] - a[0])`.
- **Why:** JS `Map` preserves insertion order, but delete-set encoding deliberately overrides that order for deterministic bytes. PHP arrays preserve insertion order for integer keys and keep the implementation byte-compatible without adding a separate map abstraction.
- **Affects:** state-vector encoding, update encoding, delete-set merge/equality, and any later code that iterates store or delete-set clients.

### DEC-0022 — M2.2 introduces only the minimal struct item surface needed for store splitting
- **Milestone:** M2.2
- **Status:** accepted
- **Decision:** `AbstractStruct`, `Item`, `GC`, and `Skip` now expose real `id`, `length`, and JS-style computed properties needed by `StructStore`/`DeleteSet`; `Yjs\splitItem()` mirrors the JS split logic and expects item content objects to provide `getLength()`, `isCountable()`, and `splice()`. Full item integration, content encoding, and type-parent behavior remain for the later struct/content milestones.
- **Why:** `getItemCleanStart()`, `getItemCleanEnd()`, and `iterateStructs()` cannot be byte- or behavior-tested without actual item splitting, but porting all of `Item.js` and every content class would exceed M2.2. The minimal surface preserves constructor/class shape while leaving unimplemented behavior explicit.
- **Affects:** M2.3 structs/content, M2.4 update integration, delete application, GC, and any test helper that constructs items directly.

### DEC-0023 — Delete-set codec helpers use object parameters for future V2 parity
- **Milestone:** M2.2
- **Status:** accepted
- **Decision:** `Yjs\writeDeleteSet()` and `Yjs\readDeleteSet()` accept native `object` parameters instead of V1-only class typehints. They still exercise `DSEncoderV1`/`DSDecoderV1` today, but the call shape matches JS's `DSEncoderV1 | DSEncoderV2` and `DSDecoderV1 | DSDecoderV2` helpers.
- **Why:** PHP 7.4 has no native union types, and `UpdateEncoderV2`/`UpdateDecoderV2` are deliberately deferred stubs per DEC-0003/DEC-0018. A V1-only typehint would force M7 to change the public/internal helper signature just to add V2 support.
- **Affects:** M7 V2 codecs, snapshot/delete-set encoding, update integration, and any code calling delete-set wire helpers.
