<?php
/**
 * Yjs public facade.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs;

/**
 * Static facade mirroring the free-function exports from yjs/src/index.js.
 */
final class Yjs {
	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function getTypeChildren( ...$args ) {
		getTypeChildren( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function createRelativePositionFromTypeIndex( ...$args ) {
		createRelativePositionFromTypeIndex( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function createRelativePositionFromJSON( ...$args ) {
		createRelativePositionFromJSON( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function createAbsolutePositionFromRelativePosition( ...$args ) {
		createAbsolutePositionFromRelativePosition( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function compareRelativePositions( ...$args ) {
		compareRelativePositions( ...$args );
	}

	/**
	 * @param int $client Client id.
	 * @param int $clock  Clock.
	 * @return Utils\ID
	 */
	public static function createID( int $client, int $clock ): Utils\ID {
		return createID( $client, $clock );
	}

	/**
	 * @param Utils\ID|null $a Left ID.
	 * @param Utils\ID|null $b Right ID.
	 * @return bool
	 */
	public static function compareIDs( ?Utils\ID $a, ?Utils\ID $b ): bool {
		return compareIDs( $a, $b );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function getState( ...$args ) {
		getState( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function createSnapshot( ...$args ) {
		createSnapshot( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function createDeleteSet( ...$args ) {
		createDeleteSet( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function createDeleteSetFromStructStore( ...$args ) {
		createDeleteSetFromStructStore( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function cleanupYTextFormatting( ...$args ) {
		cleanupYTextFormatting( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function snapshot( ...$args ) {
		snapshot( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function emptySnapshot( ...$args ) {
		emptySnapshot( ...$args );
	}

	/**
	 * @param object $type AbstractType-like value.
	 * @return string
	 */
	public static function findRootTypeKey( object $type ): string {
		return findRootTypeKey( $type );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function findIndexSS( ...$args ) {
		findIndexSS( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function getItem( ...$args ) {
		getItem( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function getItemCleanStart( ...$args ) {
		getItemCleanStart( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function getItemCleanEnd( ...$args ) {
		getItemCleanEnd( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function typeListToArraySnapshot( ...$args ) {
		typeListToArraySnapshot( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function typeMapGetSnapshot( ...$args ) {
		typeMapGetSnapshot( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function typeMapGetAllSnapshot( ...$args ) {
		typeMapGetAllSnapshot( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function createDocFromSnapshot( ...$args ) {
		createDocFromSnapshot( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function iterateDeletedStructs( ...$args ) {
		iterateDeletedStructs( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function applyUpdate( ...$args ) {
		applyUpdate( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function applyUpdateV2( ...$args ) {
		applyUpdateV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function readUpdate( ...$args ) {
		readUpdate( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function readUpdateV2( ...$args ) {
		readUpdateV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function encodeStateAsUpdate( ...$args ) {
		encodeStateAsUpdate( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function encodeStateAsUpdateV2( ...$args ) {
		encodeStateAsUpdateV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function encodeStateVector( ...$args ) {
		encodeStateVector( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function decodeSnapshot( ...$args ) {
		decodeSnapshot( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function encodeSnapshot( ...$args ) {
		encodeSnapshot( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function decodeSnapshotV2( ...$args ) {
		decodeSnapshotV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function encodeSnapshotV2( ...$args ) {
		encodeSnapshotV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function decodeStateVector( ...$args ) {
		decodeStateVector( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function logUpdate( ...$args ) {
		logUpdate( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function logUpdateV2( ...$args ) {
		logUpdateV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function decodeUpdate( ...$args ) {
		decodeUpdate( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function decodeUpdateV2( ...$args ) {
		decodeUpdateV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function relativePositionToJSON( ...$args ) {
		relativePositionToJSON( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function isDeleted( ...$args ) {
		isDeleted( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function isParentOf( ...$args ) {
		isParentOf( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function equalSnapshots( ...$args ) {
		equalSnapshots( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function tryGc( ...$args ) {
		tryGc( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function transact( ...$args ) {
		transact( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function logType( ...$args ) {
		logType( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function mergeUpdates( ...$args ) {
		mergeUpdates( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function mergeUpdatesV2( ...$args ) {
		mergeUpdatesV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function parseUpdateMeta( ...$args ) {
		parseUpdateMeta( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function parseUpdateMetaV2( ...$args ) {
		parseUpdateMetaV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function encodeStateVectorFromUpdate( ...$args ) {
		encodeStateVectorFromUpdate( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function encodeStateVectorFromUpdateV2( ...$args ) {
		encodeStateVectorFromUpdateV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function encodeRelativePosition( ...$args ) {
		encodeRelativePosition( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function decodeRelativePosition( ...$args ) {
		decodeRelativePosition( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function diffUpdate( ...$args ) {
		diffUpdate( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function diffUpdateV2( ...$args ) {
		diffUpdateV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function convertUpdateFormatV1ToV2( ...$args ) {
		convertUpdateFormatV1ToV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function convertUpdateFormatV2ToV1( ...$args ) {
		convertUpdateFormatV2ToV1( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function obfuscateUpdate( ...$args ) {
		obfuscateUpdate( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function obfuscateUpdateV2( ...$args ) {
		obfuscateUpdateV2( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function equalDeleteSets( ...$args ) {
		equalDeleteSets( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function mergeDeleteSets( ...$args ) {
		mergeDeleteSets( ...$args );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function snapshotContainsUpdate( ...$args ) {
		snapshotContainsUpdate( ...$args );
	}
}
