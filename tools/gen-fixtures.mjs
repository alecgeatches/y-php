import fs from 'node:fs'
import path from 'node:path'
import { fileURLToPath } from 'node:url'
import * as encoding from '../../yjs/node_modules/lib0/encoding.js'
import * as decoding from '../../yjs/node_modules/lib0/decoding.js'
import * as prng from '../../yjs/node_modules/lib0/prng.js'
import * as Y from '../../yjs/src/index.js'

const __dirname = path.dirname(fileURLToPath(import.meta.url))
const fixturesDir = path.join(__dirname, '..', 'tests', 'fixtures')

const hex = buf => Buffer.from(buf.buffer, buf.byteOffset, buf.byteLength).toString('hex')

const descriptor = value => {
  if (value === undefined) {
    return { type: 'undefined' }
  }
  if (value === null) {
    return { type: 'null' }
  }
  if (typeof value === 'bigint') {
    return { type: 'bigint', value: value.toString() }
  }
  if (typeof value === 'number') {
    if (Number.isNaN(value)) {
      return { type: 'number', value: 'NaN' }
    }
    if (value === Infinity) {
      return { type: 'number', value: 'Infinity' }
    }
    if (value === -Infinity) {
      return { type: 'number', value: '-Infinity' }
    }
    if (Object.is(value, -0)) {
      return { type: 'number', value: '-0' }
    }
    return { type: 'number', value }
  }
  if (typeof value === 'string') {
    return { type: 'string', value }
  }
  if (typeof value === 'boolean') {
    return { type: 'boolean', value }
  }
  if (value instanceof Uint8Array) {
    return { type: 'uint8array', value: Array.from(value) }
  }
  if (Array.isArray(value)) {
    return { type: 'array', value: value.map(descriptor) }
  }
  return { type: 'object', value: Object.keys(value).map(key => [key, descriptor(value[key])]) }
}

const materialize = desc => {
  switch (desc.type) {
    case 'undefined':
      return undefined
    case 'null':
      return null
    case 'bigint':
      return BigInt(desc.value)
    case 'number':
      if (desc.value === 'NaN') return Number.NaN
      if (desc.value === 'Infinity') return Infinity
      if (desc.value === '-Infinity') return -Infinity
      if (desc.value === '-0') return -0
      return desc.value
    case 'string':
      return desc.value
    case 'boolean':
      return desc.value
    case 'uint8array':
      return new Uint8Array(desc.value)
    case 'array':
      return desc.value.map(materialize)
    case 'object': {
      const obj = {}
      for (const [key, value] of desc.value) {
        obj[key] = materialize(value)
      }
      return obj
    }
  }
  throw new Error(`Unknown descriptor type: ${desc.type}`)
}

const encodeCase = (writer, input) => {
  const value = materialize(input)
  const encoder = encoding.createEncoder()
  switch (writer) {
    case 'writeVarUint':
      encoding.writeVarUint(encoder, value)
      break
    case 'writeVarInt':
      encoding.writeVarInt(encoder, value)
      break
    case 'writeVarString':
      encoding.writeVarString(encoder, value)
      break
    case 'writeFloat32':
      encoding.writeFloat32(encoder, value)
      break
    case 'writeFloat64':
      encoding.writeFloat64(encoder, value)
      break
    case 'writeBigInt64':
      encoding.writeBigInt64(encoder, value)
      break
    case 'writeUint8Array':
      encoding.writeUint8Array(encoder, value)
      break
    case 'writeVarUint8Array':
      encoding.writeVarUint8Array(encoder, value)
      break
    case 'writeAny':
      encoding.writeAny(encoder, value)
      break
    default:
      throw new Error(`Unknown writer: ${writer}`)
  }

  const bytes = encoding.toUint8Array(encoder)
  const decoder = decoding.createDecoder(bytes)
  let decoded
  switch (writer) {
    case 'writeVarUint':
      decoded = decoding.readVarUint(decoder)
      break
    case 'writeVarInt':
      decoded = decoding.readVarInt(decoder)
      break
    case 'writeVarString':
      decoded = decoding.readVarString(decoder)
      break
    case 'writeFloat32':
      decoded = decoding.readFloat32(decoder)
      break
    case 'writeFloat64':
      decoded = decoding.readFloat64(decoder)
      break
    case 'writeBigInt64':
      decoded = decoding.readBigInt64(decoder)
      break
    case 'writeUint8Array':
      decoded = decoding.readUint8Array(decoder, value.byteLength)
      break
    case 'writeVarUint8Array':
      decoded = decoding.readVarUint8Array(decoder)
      break
    case 'writeAny':
      decoded = decoding.readAny(decoder)
      break
  }

  return {
    name: `${writer} ${JSON.stringify(input)}`,
    writer,
    input,
    decoded: descriptor(decoded),
    hex: hex(bytes)
  }
}

const number = value => ({ type: 'number', value })
const specialNumber = value => ({ type: 'number', value })
const string = value => ({ type: 'string', value })
const bool = value => ({ type: 'boolean', value })
const nil = () => ({ type: 'null' })
const undef = () => ({ type: 'undefined' })
const bigint = value => ({ type: 'bigint', value })
const uint8array = value => ({ type: 'uint8array', value })
const array = value => ({ type: 'array', value })
const object = value => ({ type: 'object', value })

const cases = [
  ...[0, 1, 2, 63, 64, 127, 128, 255, 256, 16383, 16384, 4294967295, Number.MAX_SAFE_INTEGER].map(value => encodeCase('writeVarUint', number(value))),
  ...[specialNumber('-0'), number(0), number(1), number(-1), number(63), number(64), number(-64), number(-65), number(2147483647), number(-2147483647)].map(value => encodeCase('writeVarInt', value)),
  ...['', 'hello', 'h\u00e9llo', '\ud83d\ude0a', 'line\nbreak'].map(value => encodeCase('writeVarString', string(value))),
  ...[specialNumber('-0'), number(0), number(1.5), number(-2.25), specialNumber('Infinity'), specialNumber('-Infinity')].map(value => encodeCase('writeFloat32', value)),
  ...[number(Math.PI), number(0.1), number(-123456.789), specialNumber('NaN'), specialNumber('Infinity'), specialNumber('-Infinity')].map(value => encodeCase('writeFloat64', value)),
  ...['0', '1', '-1', '9223372036854775807', '-9223372036854775808'].map(value => encodeCase('writeBigInt64', bigint(value))),
  ...[[0, 1, 2, 255], [], [128, 129, 130]].map(value => encodeCase('writeUint8Array', uint8array(value))),
  ...[[0, 1, 2, 255], [], [128, 129, 130]].map(value => encodeCase('writeVarUint8Array', uint8array(value))),
  ...[
    undef(),
    nil(),
    bool(true),
    bool(false),
    string('any-string'),
    string('snow \u2603'),
    specialNumber('-0'),
    number(42),
    number(-42),
    number(2147483647),
    number(2147483648),
    number(2147483649),
    number(1.5),
    number(0.1),
    specialNumber('NaN'),
    bigint('-9223372036854775808'),
    uint8array([5, 6, 7]),
    array([number(1), string('two'), nil(), undef(), uint8array([9])]),
    object([
      ['alpha', number(1)],
      ['beta', array([bool(true), string('ok')])],
      ['gamma', object([['nested', nil()]])]
    ]),
    object([])
  ].map(value => encodeCase('writeAny', value))
]

const makePrngFixture = () => {
  const seed = 1337
  const nextGen = prng.create(seed)
  const next = Array.from({ length: 12 }, () => nextGen.next())

  const helperGen = prng.create(seed)
  const helperOps = [
    { op: 'bool', value: prng.bool(helperGen) },
    { op: 'int32', args: [-100, 100], value: prng.int32(helperGen, -100, 100) },
    { op: 'uint32', args: [0, 100000], value: prng.uint32(helperGen, 0, 100000) },
    { op: 'word', args: [3, 8], value: prng.word(helperGen, 3, 8) },
    { op: 'oneOf', args: [['red', 'green', 'blue', 'gold']], value: prng.oneOf(helperGen, ['red', 'green', 'blue', 'gold']) },
    { op: 'int32', args: [-2147483648, 2147483647], value: prng.int32(helperGen, -2147483648, 2147483647) },
    { op: 'uint32', args: [-2147483648, 2147483647], value: prng.uint32(helperGen, -2147483648, 2147483647) }
  ]

  return { seed, next, helperOps }
}

const captureYjsScenario = (name, clientID, apply) => {
  const doc = new Y.Doc({ guid: `y-php-${name}` })
  doc.clientID = clientID
  apply(doc)

  return {
    name,
    json: doc.toJSON(),
    updateHex: hex(Y.encodeStateAsUpdate(doc)),
    stateVectorHex: hex(Y.encodeStateVector(doc)),
    snapshotHex: hex(Y.encodeSnapshot(Y.snapshot(doc)))
  }
}

const makeYjsScenarioFixtures = () => ({
  source: 'yjs/src/index.js',
  scenarios: [
    captureYjsScenario('array-basic', 1, doc => {
      doc.getArray('array').insert(0, ['hi', 1, true])
    }),
    captureYjsScenario('map-nested-array', 2, doc => {
      const map = doc.getMap('map')
      const array = new Y.Array()
      map.set('name', 'Ada')
      map.set('items', array)
      array.insert(0, [1, 2, 3])
    }),
    captureYjsScenario('text-format', 3, doc => {
      const text = doc.getText('text')
      text.insert(0, 'hello')
      text.format(0, 5, { bold: true })
    })
  ]
})

fs.mkdirSync(fixturesDir, { recursive: true })
fs.writeFileSync(
  path.join(fixturesDir, 'encoding-primitives.json'),
  `${JSON.stringify({ cases }, null, 2)}\n`
)
fs.writeFileSync(
  path.join(fixturesDir, 'prng.json'),
  `${JSON.stringify(makePrngFixture(), null, 2)}\n`
)
fs.writeFileSync(
  path.join(fixturesDir, 'yjs-scenarios.json'),
  `${JSON.stringify(makeYjsScenarioFixtures(), null, 2)}\n`
)
