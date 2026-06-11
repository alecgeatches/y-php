<?php
/**
 * Public namespace function stubs.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs;

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function getTypeChildren( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function createRelativePositionFromTypeIndex( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function createRelativePositionFromJSON( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function createAbsolutePositionFromRelativePosition( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function compareRelativePositions( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param int $client Client id.
 * @param int $clock  Clock.
 * @return Utils\ID
 */
function createID( int $client, int $clock ): Utils\ID {
	return new Utils\ID( $client, $clock );
}

/**
 * @param Utils\ID|null $a Left ID.
 * @param Utils\ID|null $b Right ID.
 * @return bool
 */
function compareIDs( ?Utils\ID $a, ?Utils\ID $b ): bool {
	return $a === $b || ( null !== $a && null !== $b && $a->client === $b->client && $a->clock === $b->clock );
}

/**
 * @param Lib0\Encoder $encoder Encoder.
 * @param Utils\ID     $id      ID.
 * @return void
 */
function writeID( Lib0\Encoder $encoder, Utils\ID $id ): void {
	Lib0\Encoding::writeVarUint( $encoder, $id->client );
	Lib0\Encoding::writeVarUint( $encoder, $id->clock );
}

/**
 * @param Lib0\Decoder $decoder Decoder.
 * @return Utils\ID
 */
function readID( Lib0\Decoder $decoder ): Utils\ID {
	return createID(
		Lib0\Decoding::readVarUint( $decoder ),
		Lib0\Decoding::readVarUint( $decoder )
	);
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function getState( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function createSnapshot( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function createDeleteSet( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function createDeleteSetFromStructStore( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function cleanupYTextFormatting( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function snapshot( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function emptySnapshot( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param object $type AbstractType-like value.
 * @return string
 */
function findRootTypeKey( object $type ): string {
	if ( ! isset( $type->doc ) || ! is_object( $type->doc ) || ! isset( $type->doc->share ) ) {
		Lib0\Error::unexpectedCase();
		return '';
	}

	$share = $type->doc->share;
	if ( is_array( $share ) || $share instanceof \Traversable ) {
		foreach ( $share as $key => $value ) {
			if ( $value === $type ) {
				return (string) $key;
			}
		}
	} elseif ( is_object( $share ) ) {
		foreach ( get_object_vars( $share ) as $key => $value ) {
			if ( $value === $type ) {
				return (string) $key;
			}
		}
	}

	Lib0\Error::unexpectedCase();
	return '';
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function findIndexSS( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function getItem( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function getItemCleanStart( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function getItemCleanEnd( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function typeListToArraySnapshot( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function typeMapGetSnapshot( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function typeMapGetAllSnapshot( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function createDocFromSnapshot( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function iterateDeletedStructs( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function applyUpdate( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function applyUpdateV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function readUpdate( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function readUpdateV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function encodeStateAsUpdate( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function encodeStateAsUpdateV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function encodeStateVector( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function decodeSnapshot( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function encodeSnapshot( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function decodeSnapshotV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function encodeSnapshotV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function decodeStateVector( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function logUpdate( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function logUpdateV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function decodeUpdate( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function decodeUpdateV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function relativePositionToJSON( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function isDeleted( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function isParentOf( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function equalSnapshots( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function tryGc( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function transact( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function logType( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function mergeUpdates( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function mergeUpdatesV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function parseUpdateMeta( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function parseUpdateMetaV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function encodeStateVectorFromUpdate( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function encodeStateVectorFromUpdateV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function encodeRelativePosition( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function decodeRelativePosition( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function diffUpdate( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function diffUpdateV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function convertUpdateFormatV1ToV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function convertUpdateFormatV2ToV1( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function obfuscateUpdate( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function obfuscateUpdateV2( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function equalDeleteSets( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function mergeDeleteSets( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function snapshotContainsUpdate( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}
