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
 * @param Utils\StructStore $store  Struct store.
 * @param int               $client Client id.
 * @return int
 */
function getState( Utils\StructStore $store, int $client ): int {
	if ( ! array_key_exists( $client, $store->clients ) ) {
		return 0;
	}
	$structs    = $store->clients[ $client ];
	$lastStruct = $structs[ count( $structs ) - 1 ];
	return $lastStruct->id->clock + $lastStruct->length;
}

/**
 * @param Utils\StructStore $store Struct store.
 * @return array<int,int>
 */
function getStateVector( Utils\StructStore $store ): array {
	$sm = array();
	foreach ( $store->clients as $client => $structs ) {
		$struct        = $structs[ count( $structs ) - 1 ];
		$sm[ $client ] = $struct->id->clock + $struct->length;
	}
	return $sm;
}

/**
 * @param Utils\StructStore $store Struct store.
 * @return void
 */
function integrityCheck( Utils\StructStore $store ): void {
	foreach ( $store->clients as $structs ) {
		for ( $i = 1, $len = count( $structs ); $i < $len; $i++ ) {
			$left  = $structs[ $i - 1 ];
			$right = $structs[ $i ];
			if ( $left->id->clock + $left->length !== $right->id->clock ) {
				throw new \RuntimeException( 'StructStore failed integrity check' );
			}
		}
	}
}

/**
 * @param Utils\StructStore      $store  Struct store.
 * @param Structs\AbstractStruct $struct Struct.
 * @return void
 */
function addStruct( Utils\StructStore $store, Structs\AbstractStruct $struct ): void {
	$client = $struct->id->client;
	if ( ! array_key_exists( $client, $store->clients ) ) {
		$store->clients[ $client ] = array();
	} else {
		$structs    = $store->clients[ $client ];
		$lastStruct = $structs[ count( $structs ) - 1 ];
		if ( $lastStruct->id->clock + $lastStruct->length !== $struct->id->clock ) {
			Lib0\Error::unexpectedCase();
		}
	}
	$store->clients[ $client ][] = $struct;
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
 * @return Utils\DeleteSet
 */
function createDeleteSet(): Utils\DeleteSet {
	return new Utils\DeleteSet();
}

/**
 * @param Utils\StructStore $ss Struct store.
 * @return Utils\DeleteSet
 */
function createDeleteSetFromStructStore( Utils\StructStore $ss ): Utils\DeleteSet {
	$ds = createDeleteSet();
	foreach ( $ss->clients as $client => $structs ) {
		$dsitems = array();
		for ( $i = 0, $len = count( $structs ); $i < $len; $i++ ) {
			$struct = $structs[ $i ];
			if ( $struct->deleted ) {
				$clock  = $struct->id->clock;
				$delLen = $struct->length;
				while ( $i + 1 < $len && $structs[ $i + 1 ]->deleted ) {
					++$i;
					$delLen += $structs[ $i ]->length;
				}
				$dsitems[] = new Utils\DeleteItem( $clock, $delLen );
			}
		}
		if ( 0 < count( $dsitems ) ) {
			$ds->clients[ $client ] = $dsitems;
		}
	}
	return $ds;
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
 * @param array<int,Structs\AbstractStruct> $structs Structs.
 * @param int                               $clock   Clock.
 * @return int
 */
function findIndexSS( array $structs, int $clock ): int {
	$left     = 0;
	$right    = count( $structs ) - 1;
	$mid      = $structs[ $right ];
	$midclock = $mid->id->clock;
	if ( $midclock === $clock ) {
		return $right;
	}
	$midindex = Lib0\Math::floor( ( $clock / ( $midclock + $mid->length - 1 ) ) * $right );
	while ( $left <= $right ) {
		$mid      = $structs[ $midindex ];
		$midclock = $mid->id->clock;
		if ( $midclock <= $clock ) {
			if ( $clock < $midclock + $mid->length ) {
				return $midindex;
			}
			$left = $midindex + 1;
		} else {
			$right = $midindex - 1;
		}
		$midindex = Lib0\Math::floor( ( $left + $right ) / 2 );
	}
	Lib0\Error::unexpectedCase();
	return 0;
}

/**
 * @param Utils\StructStore $store Struct store.
 * @param Utils\ID          $id    ID.
 * @return Structs\AbstractStruct
 */
function find( Utils\StructStore $store, Utils\ID $id ): Structs\AbstractStruct {
	$structs = $store->clients[ $id->client ];
	return $structs[ findIndexSS( $structs, $id->clock ) ];
}

/**
 * @param Utils\StructStore $store Struct store.
 * @param Utils\ID          $id    ID.
 * @return Structs\Item
 */
function getItem( Utils\StructStore $store, Utils\ID $id ): Structs\Item {
	$item = find( $store, $id );
	if ( ! $item instanceof Structs\Item ) {
		Lib0\Error::unexpectedCase();
	}
	return $item;
}

/**
 * @param mixed                             $transaction Transaction.
 * @param array<int,Structs\AbstractStruct> $structs     Structs.
 * @param int                               $clock       Clock.
 * @return int
 */
function findIndexCleanStart( $transaction, array &$structs, int $clock ): int {
	$index  = findIndexSS( $structs, $clock );
	$struct = $structs[ $index ];
	if ( $struct->id->clock < $clock && $struct instanceof Structs\Item ) {
		array_splice( $structs, $index + 1, 0, array( splitItem( $transaction, $struct, $clock - $struct->id->clock ) ) );
		return $index + 1;
	}
	return $index;
}

/**
 * @param mixed    $transaction Transaction.
 * @param Utils\ID $id          ID.
 * @return Structs\Item
 */
function getItemCleanStart( $transaction, Utils\ID $id ): Structs\Item {
	$structs =& $transaction->doc->store->clients[ $id->client ];
	$item    = $structs[ findIndexCleanStart( $transaction, $structs, $id->clock ) ];
	if ( ! $item instanceof Structs\Item ) {
		Lib0\Error::unexpectedCase();
	}
	return $item;
}

/**
 * @param mixed             $transaction Transaction.
 * @param Utils\StructStore $store       Struct store.
 * @param Utils\ID          $id          ID.
 * @return Structs\Item
 */
function getItemCleanEnd( $transaction, Utils\StructStore $store, Utils\ID $id ): Structs\Item {
	$structs =& $store->clients[ $id->client ];
	$index   = findIndexSS( $structs, $id->clock );
	$struct  = $structs[ $index ];
	if ( $id->clock !== $struct->id->clock + $struct->length - 1 && ! $struct instanceof Structs\GC ) {
		array_splice( $structs, $index + 1, 0, array( splitItem( $transaction, $struct, $id->clock - $struct->id->clock + 1 ) ) );
	}
	if ( ! $struct instanceof Structs\Item ) {
		Lib0\Error::unexpectedCase();
	}
	return $struct;
}

/**
 * @param Utils\StructStore      $store     Struct store.
 * @param Structs\AbstractStruct $struct    Old struct.
 * @param Structs\AbstractStruct $newStruct New struct.
 * @return void
 */
function replaceStruct( Utils\StructStore $store, Structs\AbstractStruct $struct, Structs\AbstractStruct $newStruct ): void {
	$structs =& $store->clients[ $struct->id->client ];
	$structs[ findIndexSS( $structs, $struct->id->clock ) ] = $newStruct;
}

/**
 * @param mixed                             $transaction Transaction.
 * @param array<int,Structs\AbstractStruct> $structs     Structs.
 * @param int                               $clockStart  Inclusive start.
 * @param int                               $len         Length.
 * @param callable                          $f           Callback.
 * @return void
 */
function iterateStructs( $transaction, array &$structs, int $clockStart, int $len, callable $f ): void {
	if ( 0 === $len ) {
		return;
	}
	$clockEnd = $clockStart + $len;
	$index    = findIndexCleanStart( $transaction, $structs, $clockStart );
	do {
		$struct = $structs[ $index++ ];
		if ( $clockEnd < $struct->id->clock + $struct->length ) {
			findIndexCleanStart( $transaction, $structs, $clockEnd );
		}
		$f( $struct );
		$structCount = count( $structs );
	} while ( $index < $structCount && $structs[ $index ]->id->clock < $clockEnd );
}

/**
 * @param mixed        $transaction Transaction.
 * @param Structs\Item $leftItem    Left item.
 * @param int          $diff        Split diff.
 * @return Structs\Item
 */
function splitItem( $transaction, Structs\Item $leftItem, int $diff ): Structs\Item {
	$client    = $leftItem->id->client;
	$clock     = $leftItem->id->clock;
	$rightItem = new Structs\Item(
		createID( $client, $clock + $diff ),
		$leftItem,
		createID( $client, $clock + $diff - 1 ),
		$leftItem->right,
		$leftItem->rightOrigin,
		$leftItem->parent,
		$leftItem->parentSub,
		$leftItem->content->splice( $diff )
	);
	if ( $leftItem->deleted ) {
		$rightItem->markDeleted();
	}
	if ( $leftItem->keep ) {
		$rightItem->keep = true;
	}
	if ( null !== $leftItem->redone ) {
		$rightItem->redone = createID( $leftItem->redone->client, $leftItem->redone->clock + $diff );
	}
	$leftItem->right = $rightItem;
	if ( null !== $rightItem->right ) {
		$rightItem->right->left = $rightItem;
	}
	if ( ! isset( $transaction->_mergeStructs ) || ! is_array( $transaction->_mergeStructs ) ) {
		$transaction->_mergeStructs = array();
	}
	$transaction->_mergeStructs[] = $rightItem;
	if ( null !== $rightItem->parentSub && null === $rightItem->right && is_object( $rightItem->parent ) && isset( $rightItem->parent->_map ) && is_array( $rightItem->parent->_map ) ) {
		$rightItem->parent->_map[ $rightItem->parentSub ] = $rightItem;
	}
	$leftItem->length = $diff;
	return $rightItem;
}

/**
 * @param object $decoder Decoder.
 * @param int    $info    Item info byte.
 * @return object
 */
function readItemContent( object $decoder, int $info ): object {
	switch ( $info & Lib0\Binary::BITS5 ) {
		case 1:
			return readContentDeleted( $decoder );
		case 2:
			return readContentJSON( $decoder );
		case 3:
			return readContentBinary( $decoder );
		case 4:
			return readContentString( $decoder );
		case 5:
			return readContentEmbed( $decoder );
		case 6:
			return readContentFormat( $decoder );
		case 7:
			return readContentType( $decoder );
		case 8:
			return readContentAny( $decoder );
		case 9:
			return readContentDoc( $decoder );
	}
	Lib0\Error::unexpectedCase();
}

/**
 * @param object $decoder Decoder.
 * @return Structs\ContentDeleted
 */
function readContentDeleted( object $decoder ): Structs\ContentDeleted {
	return new Structs\ContentDeleted( $decoder->readLen() );
}

/**
 * @param object $decoder Decoder.
 * @return Structs\ContentJSON
 */
function readContentJSON( object $decoder ): Structs\ContentJSON {
	$len = $decoder->readLen();
	$arr = array();
	for ( $i = 0; $i < $len; $i++ ) {
		$value = $decoder->readString();
		if ( 'undefined' === $value ) {
			$arr[] = Lib0\UndefinedValue::getInstance();
		} else {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.json_decode_json_decode
			$decoded = json_decode( $value );
			if ( JSON_ERROR_NONE !== json_last_error() ) {
				throw Lib0\Error::create( 'Unexpected JSON decode failure.' );
			}
			$arr[] = $decoded;
		}
	}
	return new Structs\ContentJSON( $arr );
}

/**
 * @param object $decoder Decoder.
 * @return Structs\ContentBinary
 */
function readContentBinary( object $decoder ): Structs\ContentBinary {
	return new Structs\ContentBinary( $decoder->readBuf() );
}

/**
 * @param object $decoder Decoder.
 * @return Structs\ContentString
 */
function readContentString( object $decoder ): Structs\ContentString {
	return new Structs\ContentString( $decoder->readString() );
}

/**
 * @param object $decoder Decoder.
 * @return Structs\ContentEmbed
 */
function readContentEmbed( object $decoder ): Structs\ContentEmbed {
	return new Structs\ContentEmbed( $decoder->readJSON() );
}

/**
 * @param object $decoder Decoder.
 * @return Structs\ContentFormat
 */
function readContentFormat( object $decoder ): Structs\ContentFormat {
	return new Structs\ContentFormat( $decoder->readKey(), $decoder->readJSON() );
}

/**
 * @param object $decoder Decoder.
 * @return Structs\ContentType
 */
function readContentType( object $decoder ): Structs\ContentType {
	switch ( $decoder->readTypeRef() ) {
		case 0:
			return new Structs\ContentType( readYArray( $decoder ) );
		case 1:
			return new Structs\ContentType( readYMap( $decoder ) );
		case 2:
			return new Structs\ContentType( readYText( $decoder ) );
		case 3:
			return new Structs\ContentType( readYXmlElement( $decoder ) );
		case 4:
			return new Structs\ContentType( readYXmlFragment( $decoder ) );
		case 5:
			return new Structs\ContentType( readYXmlHook( $decoder ) );
		case 6:
			return new Structs\ContentType( readYXmlText( $decoder ) );
	}
	Lib0\Error::unexpectedCase();
}

/**
 * @param object $decoder Decoder.
 * @return Structs\ContentAny
 */
function readContentAny( object $decoder ): Structs\ContentAny {
	$len = $decoder->readLen();
	$arr = array();
	for ( $i = 0; $i < $len; $i++ ) {
		$arr[] = $decoder->readAny();
	}
	return new Structs\ContentAny( $arr );
}

/**
 * @param object $decoder Decoder.
 * @return Structs\ContentDoc
 */
function readContentDoc( object $decoder ): Structs\ContentDoc {
	return new Structs\ContentDoc( Structs\ContentDoc::createDocFromOpts( $decoder->readString(), $decoder->readAny() ) );
}

/**
 * @param object $decoder Decoder.
 * @return Types\YArray
 */
function readYArray( object $decoder ): Types\YArray {
	unset( $decoder );
	return new Types\YArray();
}

/**
 * @param object $decoder Decoder.
 * @return Types\YMap
 */
function readYMap( object $decoder ): Types\YMap {
	unset( $decoder );
	return new Types\YMap();
}

/**
 * @param object $decoder Decoder.
 * @return Types\YText
 */
function readYText( object $decoder ): Types\YText {
	unset( $decoder );
	return new Types\YText();
}

/**
 * @param object $decoder Decoder.
 * @return Types\YXmlElement
 */
function readYXmlElement( object $decoder ): Types\YXmlElement {
	return new Types\YXmlElement( $decoder->readKey() );
}

/**
 * @param object $decoder Decoder.
 * @return Types\YXmlFragment
 */
function readYXmlFragment( object $decoder ): Types\YXmlFragment {
	unset( $decoder );
	return new Types\YXmlFragment();
}

/**
 * @param object $decoder Decoder.
 * @return Types\YXmlHook
 */
function readYXmlHook( object $decoder ): Types\YXmlHook {
	return new Types\YXmlHook( $decoder->readKey() );
}

/**
 * @param object $decoder Decoder.
 * @return Types\YXmlText
 */
function readYXmlText( object $decoder ): Types\YXmlText {
	unset( $decoder );
	return new Types\YXmlText();
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
 * @param mixed           $transaction Transaction.
 * @param Utils\DeleteSet $ds          Delete set.
 * @param callable        $f           Callback.
 * @return void
 */
function iterateDeletedStructs( $transaction, Utils\DeleteSet $ds, callable $f ): void {
	foreach ( $ds->clients as $clientid => $deletes ) {
		if ( array_key_exists( $clientid, $transaction->doc->store->clients ) ) {
			$structs    =& $transaction->doc->store->clients[ $clientid ];
			$lastStruct = $structs[ count( $structs ) - 1 ];
			$clockState = $lastStruct->id->clock + $lastStruct->length;
			for ( $i = 0, $len = count( $deletes ); $i < $len && $deletes[ $i ]->clock < $clockState; $i++ ) {
				$del = $deletes[ $i ];
				iterateStructs( $transaction, $structs, $del->clock, $del->len, $f );
			}
			unset( $structs );
		}
	}
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
 * @param array<int,Utils\DeleteItem> $dis   Delete items.
 * @param int                         $clock Clock.
 * @return int|null
 */
function findIndexDS( array $dis, int $clock ): ?int {
	$left  = 0;
	$right = count( $dis ) - 1;
	while ( $left <= $right ) {
		$midindex = Lib0\Math::floor( ( $left + $right ) / 2 );
		$mid      = $dis[ $midindex ];
		$midclock = $mid->clock;
		if ( $midclock <= $clock ) {
			if ( $clock < $midclock + $mid->len ) {
				return $midindex;
			}
			$left = $midindex + 1;
		} else {
			$right = $midindex - 1;
		}
	}
	return null;
}

/**
 * @param Utils\DeleteSet $ds Delete set.
 * @param Utils\ID        $id ID.
 * @return bool
 */
function isDeleted( Utils\DeleteSet $ds, Utils\ID $id ): bool {
	return array_key_exists( $id->client, $ds->clients ) && null !== findIndexDS( $ds->clients[ $id->client ], $id->clock );
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
 * @param Utils\DeleteSet $ds Delete set.
 * @return void
 */
function sortAndMergeDeleteSet( Utils\DeleteSet $ds ): void {
	foreach ( $ds->clients as $client => $dels ) {
		usort(
			$dels,
			static fn ( Utils\DeleteItem $a, Utils\DeleteItem $b ): int => $a->clock <=> $b->clock
		);

		$count = count( $dels );
		if ( 0 === $count ) {
			$ds->clients[ $client ] = $dels;
			continue;
		}

		for ( $i = 1, $j = 1; $i < $count; $i++ ) {
			$left  = $dels[ $j - 1 ];
			$right = $dels[ $i ];
			if ( $left->clock + $left->len >= $right->clock ) {
				$dels[ $j - 1 ] = new Utils\DeleteItem( $left->clock, Lib0\Math::max( $left->len, $right->clock + $right->len - $left->clock ) );
			} else {
				if ( $j < $i ) {
					$dels[ $j ] = $right;
				}
				++$j;
			}
		}
		$ds->clients[ $client ] = array_slice( $dels, 0, $j );
	}
}

/**
 * @param Utils\DeleteSet $ds     Delete set.
 * @param int             $client Client id.
 * @param int             $clock  Clock.
 * @param int             $length Length.
 * @return void
 */
function addToDeleteSet( Utils\DeleteSet $ds, int $client, int $clock, int $length ): void {
	if ( ! array_key_exists( $client, $ds->clients ) ) {
		$ds->clients[ $client ] = array();
	}
	$ds->clients[ $client ][] = new Utils\DeleteItem( $clock, $length );
}

/**
 * @param object          $encoder Encoder.
 * @param Utils\DeleteSet $ds      Delete set.
 * @return void
 */
function writeDeleteSet( object $encoder, Utils\DeleteSet $ds ): void {
	Lib0\Encoding::writeVarUint( $encoder->restEncoder, count( $ds->clients ) );

	$entries = array();
	foreach ( $ds->clients as $client => $dsitems ) {
		$entries[] = array( (int) $client, $dsitems );
	}
	usort(
		$entries,
		static fn ( array $a, array $b ): int => $b[0] <=> $a[0]
	);

	foreach ( $entries as $entry ) {
		$client  = $entry[0];
		$dsitems = $entry[1];
		$encoder->resetDsCurVal();
		Lib0\Encoding::writeVarUint( $encoder->restEncoder, $client );
		$len = count( $dsitems );
		Lib0\Encoding::writeVarUint( $encoder->restEncoder, $len );
		for ( $i = 0; $i < $len; $i++ ) {
			$item = $dsitems[ $i ];
			$encoder->writeDsClock( $item->clock );
			$encoder->writeDsLen( $item->len );
		}
	}
}

/**
 * @param object $decoder Decoder.
 * @return Utils\DeleteSet
 */
function readDeleteSet( object $decoder ): Utils\DeleteSet {
	$ds         = new Utils\DeleteSet();
	$numClients = Lib0\Decoding::readVarUint( $decoder->restDecoder );
	for ( $i = 0; $i < $numClients; $i++ ) {
		$decoder->resetDsCurVal();
		$client          = Lib0\Decoding::readVarUint( $decoder->restDecoder );
		$numberOfDeletes = Lib0\Decoding::readVarUint( $decoder->restDecoder );
		if ( 0 < $numberOfDeletes ) {
			if ( ! array_key_exists( $client, $ds->clients ) ) {
				$ds->clients[ $client ] = array();
			}
			for ( $deleteIndex = 0; $deleteIndex < $numberOfDeletes; $deleteIndex++ ) {
				$ds->clients[ $client ][] = new Utils\DeleteItem( $decoder->readDsClock(), $decoder->readDsLen() );
			}
		}
	}
	return $ds;
}

/**
 * @param array<int,Utils\DeleteSet> $dss Delete sets.
 * @return Utils\DeleteSet
 */
function mergeDeleteSets( array $dss ): Utils\DeleteSet {
	$merged = new Utils\DeleteSet();
	for ( $dssI = 0, $dssLen = count( $dss ); $dssI < $dssLen; $dssI++ ) {
		foreach ( $dss[ $dssI ]->clients as $client => $delsLeft ) {
			if ( ! array_key_exists( $client, $merged->clients ) ) {
				$dels = array_values( $delsLeft );
				for ( $i = $dssI + 1; $i < $dssLen; $i++ ) {
					if ( array_key_exists( $client, $dss[ $i ]->clients ) ) {
						array_push( $dels, ...$dss[ $i ]->clients[ $client ] );
					}
				}
				$merged->clients[ $client ] = $dels;
			}
		}
	}
	sortAndMergeDeleteSet( $merged );
	return $merged;
}

/**
 * @param Utils\DeleteSet $ds1 First delete set.
 * @param Utils\DeleteSet $ds2 Second delete set.
 * @return bool
 */
function equalDeleteSets( Utils\DeleteSet $ds1, Utils\DeleteSet $ds2 ): bool {
	if ( count( $ds1->clients ) !== count( $ds2->clients ) ) {
		return false;
	}
	foreach ( $ds1->clients as $client => $deleteItems1 ) {
		if ( ! array_key_exists( $client, $ds2->clients ) ) {
			return false;
		}
		$deleteItems2 = $ds2->clients[ $client ];
		if ( count( $deleteItems1 ) !== count( $deleteItems2 ) ) {
			return false;
		}
		for ( $i = 0, $len = count( $deleteItems1 ); $i < $len; $i++ ) {
			$di1 = $deleteItems1[ $i ];
			$di2 = $deleteItems2[ $i ];
			if ( $di1->clock !== $di2->clock || $di1->len !== $di2->len ) {
				return false;
			}
		}
	}
	return true;
}

/**
 * @param mixed ...$args Arguments.
 * @return void
 */
function snapshotContainsUpdate( ...$args ) {
	unset( $args );
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}
