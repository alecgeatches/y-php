# M2 — Structured data: Doc + YMap + YArray

The keystone milestone. It builds the entire CRDT core (everything except the rich-text/XML specifics) and the first real interop. Largest milestone — `Item` (~816 lines) and `AbstractType` (~985 lines) are the hard parts. Consider sub-decomposing.

## Goal

A working `Doc` with `YMap` and `YArray`, plus `applyUpdate` / `encodeStateAsUpdate` / `encodeStateVector`, converging across simulated clients and byte-matching JS.

## Prerequisites

M0 (primitives) and M1 (stubs + translated tests + harness).

## Implement in dependency order

```
ID (createID, compareIDs)
  → UpdateEncoderV1 / UpdateDecoderV1        (struct-aware wrappers over Lib0\Encoding)
    → StructStore
      → DeleteSet
        → AbstractStruct → GC, Skip
          → Content* (String, JSON, Any, Binary, Embed, Format, Deleted, Type, Doc)
            → Item                            (the keystone)
              → AbstractType + EventHandler + YEvent
                → Transaction
                  → Doc
                    → Utils\Encoding          (readStructs, applyUpdate, encodeStateAsUpdate,
                                                encodeStateVector, integrateStructs, pending handling)
                      → YArray, YMap
                        → y-protocols/sync subset (writeSyncStep1, readSyncMessage, writeUpdate)
```

Source files: [yjs/src/utils/ID.js](../../../yjs/src/utils/ID.js), [UpdateEncoder.js](../../../yjs/src/utils/UpdateEncoder.js), [UpdateDecoder.js](../../../yjs/src/utils/UpdateDecoder.js), [StructStore.js](../../../yjs/src/utils/StructStore.js), [DeleteSet.js](../../../yjs/src/utils/DeleteSet.js), [structs/](../../../yjs/src/structs/), [types/AbstractType.js](../../../yjs/src/types/AbstractType.js), [Transaction.js](../../../yjs/src/utils/Transaction.js), [Doc.js](../../../yjs/src/utils/Doc.js), [utils/encoding.js](../../../yjs/src/utils/encoding.js), [YArray.js](../../../yjs/src/types/YArray.js), [YMap.js](../../../yjs/src/types/YMap.js).

The `y-protocols/sync` subset is needed because `tests/Support` convergence (`compare(users)`) exchanges sync messages between `TestYInstance`s.

## Tests to turn green

- `tests/Unit/DocTest`, `MapTest`, `ArrayTest` — unit **and** fuzz (`applyRandomTests`).
- `tests/Conformance/` — Map/Array fixtures decode and re-encode byte-identically to JS; seed-replayed fuzz scenarios converge to the same bytes as JS.

## Gotchas

- **`Item` integration** is the algorithmic heart — the conflict resolution and the search-marker optimization in `AbstractType`/`Item`. Mirror the JS control flow precisely; do not "simplify."
- **`ContentAny`** exercises `writeAny`'s full type matrix — re-confirm hazards 3/4/5 hold end-to-end here.
- **State vector** ordering (clients sorted) must match JS exactly or `encodeStateVector` bytes diverge.
- This is where byte-compat bugs from M0 will first show up under real load. Lean on the conformance layer to localize them.

## Exit criterion

`doc`, `y-map`, `y-array` unit + fuzz tests green; their fixtures byte-match real JS in both directions; multi-user convergence matches JS bytes for many seeds.
