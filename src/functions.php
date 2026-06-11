<?php
/**
 * Public namespace function stubs.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs;

/**
 * @param Types\AbstractType $t Type.
 * @return array<int,Structs\Item>
 */
function getTypeChildren( Types\AbstractType $t ): array {
	$arr = array();
	for ( $s = $t->_start; null !== $s; $s = $s->right ) {
		$arr[] = $s;
	}
	return $arr;
}

/**
 * @return int
 */
function nextSearchMarkerTimestamp(): int {
	static $timestamp = 0;
	return $timestamp++;
}

/**
 * @return Utils\EventHandler
 */
function createEventHandler(): Utils\EventHandler {
	return new Utils\EventHandler();
}

/**
 * @param Utils\EventHandler $eventHandler Event handler.
 * @param callable           $f            Listener.
 * @return void
 */
function addEventHandlerListener( Utils\EventHandler $eventHandler, callable $f ): void {
	$eventHandler->l[] = $f;
}

/**
 * @param Utils\EventHandler $eventHandler Event handler.
 * @param callable           $f            Listener.
 * @return void
 */
function removeEventHandlerListener( Utils\EventHandler $eventHandler, callable $f ): void {
	$eventHandler->l = array_values(
		array_filter(
			$eventHandler->l,
			static fn ( callable $g ): bool => $f !== $g
		)
	);
}

/**
 * @param Utils\EventHandler $eventHandler Event handler.
 * @param mixed              $arg0         First argument.
 * @param mixed              $arg1         Second argument.
 * @return void
 */
function callEventHandlerListeners( Utils\EventHandler $eventHandler, $arg0, $arg1 ): void {
	$listeners = $eventHandler->l;
	Lib0\Func::callAll( $listeners, array( $arg0, $arg1 ) );
}

/**
 * @param Types\AbstractType $type        Changed type.
 * @param Utils\Transaction  $transaction Transaction.
 * @param Utils\YEvent       $event       Event.
 * @return void
 */
function callTypeObservers( Types\AbstractType $type, Utils\Transaction $transaction, Utils\YEvent $event ): void {
	$changedType = $type;
	while ( true ) {
		$events                                   = $transaction->changedParentTypes->contains( $type ) ? $transaction->changedParentTypes[ $type ] : array();
		$events[]                                 = $event;
		$transaction->changedParentTypes[ $type ] = $events;
		if ( null === $type->_item ) {
			break;
		}
		$type = $type->_item->parent;
	}
	callEventHandlerListeners( $changedType->_eH, $event, $transaction );
}

/**
 * @param Utils\Transaction  $transaction Transaction.
 * @param Types\AbstractType $type        Changed type.
 * @param string|null        $parentSub   Changed parent key.
 * @return void
 */
function addChangedTypeToTransaction( Utils\Transaction $transaction, Types\AbstractType $type, ?string $parentSub ): void {
	$item = $type->_item;
	if ( null === $item || ( $item->id->clock < ( $transaction->beforeState[ $item->id->client ] ?? 0 ) && ! $item->deleted ) ) {
		$subs = $transaction->changed->contains( $type ) ? $transaction->changed[ $type ] : array();
		if ( ! in_array( $parentSub, $subs, true ) ) {
			$subs[] = $parentSub;
		}
		$transaction->changed[ $type ] = $subs;
	}
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
 * @return int
 */
function generateNewClientId(): int {
	return Lib0\Random::uint32();
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
 * @param array<int,Types\ArraySearchMarker> $searchMarker Search markers.
 * @param Structs\Item                       $p            Item.
 * @param int                                $index        Index.
 * @return Types\ArraySearchMarker
 */
function markPosition( array &$searchMarker, Structs\Item $p, int $index ): Types\ArraySearchMarker {
	if ( count( $searchMarker ) >= 80 ) {
		$marker = $searchMarker[0];
		foreach ( $searchMarker as $candidate ) {
			if ( $candidate->timestamp < $marker->timestamp ) {
				$marker = $candidate;
			}
		}
		overwriteMarker( $marker, $p, $index );
		return $marker;
	}
	$marker         = new Types\ArraySearchMarker( $p, $index );
	$searchMarker[] = $marker;
	return $marker;
}

/**
 * @param Types\ArraySearchMarker $marker Marker.
 * @param Structs\Item            $p      Item.
 * @param int                     $index  Index.
 * @return void
 */
function overwriteMarker( Types\ArraySearchMarker $marker, Structs\Item $p, int $index ): void {
	$marker->p->marker = false;
	$marker->p         = $p;
	$p->marker         = true;
	$marker->index     = $index;
	$marker->timestamp = nextSearchMarkerTimestamp();
}

/**
 * @param Types\ArraySearchMarker $marker Marker.
 * @return void
 */
function refreshMarkerTimestamp( Types\ArraySearchMarker $marker ): void {
	$marker->timestamp = nextSearchMarkerTimestamp();
}

/**
 * @param Types\AbstractType $yarray Type.
 * @param int                $index  Index.
 * @return Types\ArraySearchMarker|null
 */
function findMarker( Types\AbstractType $yarray, int $index ): ?Types\ArraySearchMarker {
	if ( null === $yarray->_start || 0 === $index || null === $yarray->_searchMarker ) {
		return null;
	}
	$marker = null;
	foreach ( $yarray->_searchMarker as $candidate ) {
		if ( null === $marker || abs( $index - $candidate->index ) < abs( $index - $marker->index ) ) {
			$marker = $candidate;
		}
	}
	$p      = $yarray->_start;
	$pindex = 0;
	if ( null !== $marker ) {
		$p      = $marker->p;
		$pindex = $marker->index;
		refreshMarkerTimestamp( $marker );
	}
	while ( null !== $p->right && $pindex < $index ) {
		if ( ! $p->deleted && $p->countable ) {
			if ( $index < $pindex + $p->length ) {
				break;
			}
			$pindex += $p->length;
		}
		$p = $p->right;
	}
	while ( null !== $p->left && $pindex > $index ) {
		$p = $p->left;
		if ( ! $p->deleted && $p->countable ) {
			$pindex -= $p->length;
		}
	}
	while ( null !== $p->left && $p->left->id->client === $p->id->client && $p->left->id->clock + $p->left->length === $p->id->clock ) {
		$p = $p->left;
		if ( ! $p->deleted && $p->countable ) {
			$pindex -= $p->length;
		}
	}
	if ( null !== $marker && abs( $marker->index - $pindex ) < $p->parent->_length / 80 ) {
		overwriteMarker( $marker, $p, $pindex );
		return $marker;
	}
	return markPosition( $yarray->_searchMarker, $p, $pindex );
}

/**
 * @param array<int,Types\ArraySearchMarker> $searchMarker Search markers.
 * @param int                                $index        Index.
 * @param int                                $len          Change length.
 * @return void
 */
function updateMarkerChanges( array &$searchMarker, int $index, int $len ): void {
	for ( $i = count( $searchMarker ) - 1; $i >= 0; $i-- ) {
		$m = $searchMarker[ $i ];
		if ( 0 < $len ) {
			$p         = $m->p;
			$p->marker = false;
			while ( null !== $p && ( $p->deleted || ! $p->countable ) ) {
				$p = $p->left;
				if ( null !== $p && ! $p->deleted && $p->countable ) {
					$m->index -= $p->length;
				}
			}
			if ( null === $p || true === $p->marker ) {
				array_splice( $searchMarker, $i, 1 );
				continue;
			}
			$m->p      = $p;
			$p->marker = true;
		}
		if ( $index < $m->index || ( 0 < $len && $index === $m->index ) ) {
			$m->index = Lib0\Math::max( $index, $m->index + $len );
		}
	}
}

/**
 * @param Types\AbstractType $type  Type.
 * @param int                $start Start.
 * @param int                $end   End.
 * @return array<int,mixed>
 */
function typeListSlice( Types\AbstractType $type, int $start, int $end ): array {
	if ( $start < 0 ) {
		$start = $type->_length + $start;
	}
	if ( $end < 0 ) {
		$end = $type->_length + $end;
	}
	$len = $end - $start;
	$cs  = array();
	for ( $n = $type->_start; null !== $n && 0 < $len; $n = $n->right ) {
		if ( $n->countable && ! $n->deleted ) {
			$c = $n->content->getContent();
			if ( count( $c ) <= $start ) {
				$start -= count( $c );
			} else {
				for ( $i = $start, $clen = count( $c ); $i < $clen && 0 < $len; $i++ ) {
					$cs[] = $c[ $i ];
					--$len;
				}
				$start = 0;
			}
		}
	}
	return $cs;
}

/**
 * @param Types\AbstractType $type Type.
 * @return array<int,mixed>
 */
function typeListToArray( Types\AbstractType $type ): array {
	$cs = array();
	for ( $n = $type->_start; null !== $n; $n = $n->right ) {
		if ( $n->countable && ! $n->deleted ) {
			array_push( $cs, ...$n->content->getContent() );
		}
	}
	return $cs;
}

/**
 * @param Types\AbstractType $type     Type.
 * @param mixed              $snapshot Snapshot.
 * @return array<int,mixed>
 */
function typeListToArraySnapshot( Types\AbstractType $type, $snapshot ): array {
	unset( $snapshot );
	return typeListToArray( $type );
}

/**
 * @param Types\AbstractType $type Type.
 * @param callable           $f    Callback.
 * @return void
 */
function typeListForEach( Types\AbstractType $type, callable $f ): void {
	$index = 0;
	for ( $n = $type->_start; null !== $n; $n = $n->right ) {
		if ( $n->countable && ! $n->deleted ) {
			foreach ( $n->content->getContent() as $c ) {
				$f( $c, $index++, $type );
			}
		}
	}
}

/**
 * @param Types\AbstractType $type Type.
 * @param callable           $f    Callback.
 * @return array<int,mixed>
 */
function typeListMap( Types\AbstractType $type, callable $f ): array {
	$result = array();
	typeListForEach(
		$type,
		static function ( $c, int $i, Types\AbstractType $parent ) use ( &$result, $f ): void {
			$result[] = $f( $c, $i, $parent );
		}
	);
	return $result;
}

/**
 * @param Types\AbstractType $type Type.
 * @return \Generator<int,mixed>
 */
function typeListCreateIterator( Types\AbstractType $type ): \Generator {
	$n                   = $type->_start;
	$currentContent      = null;
	$currentContentIndex = 0;

	while ( true ) {
		if ( null === $currentContent ) {
			while ( null !== $n && $n->deleted ) {
				$n = $n->right;
			}
			if ( null === $n ) {
				return;
			}
			$currentContent      = $n->content->getContent();
			$currentContentIndex = 0;
			$n                   = $n->right;
		}

		yield $currentContent[ $currentContentIndex++ ];

		if ( count( $currentContent ) <= $currentContentIndex ) {
			$currentContent = null;
		}
	}
}

/**
 * @param Types\AbstractType $type  Type.
 * @param int                $index Index.
 * @return mixed
 */
function typeListGet( Types\AbstractType $type, int $index ) {
	$marker = findMarker( $type, $index );
	$n      = $type->_start;
	if ( null !== $marker ) {
		$n      = $marker->p;
		$index -= $marker->index;
	}
	for ( ; null !== $n; $n = $n->right ) {
		if ( ! $n->deleted && $n->countable ) {
			if ( $index < $n->length ) {
				return $n->content->getContent()[ $index ];
			}
			$index -= $n->length;
		}
	}
	return Lib0\UndefinedValue::getInstance();
}

/**
 * @param mixed $value Value.
 * @return object
 */
function contentForValue( $value ): object {
	if ( $value instanceof Lib0\Buffer ) {
		return new Structs\ContentBinary( $value );
	}
	if ( $value instanceof Utils\Doc ) {
		return new Structs\ContentDoc( $value );
	}
	if ( $value instanceof Types\AbstractType ) {
		return new Structs\ContentType( $value );
	}
	return new Structs\ContentAny( array( $value ) );
}

/**
 * @param mixed              $transaction   Transaction.
 * @param Types\AbstractType $parent        Parent type.
 * @param Structs\Item|null  $referenceItem Reference item.
 * @param array<int,mixed>   $content       Content.
 * @return void
 */
function typeListInsertGenericsAfter( $transaction, Types\AbstractType $parent, ?Structs\Item $referenceItem, array $content ): void {
	$left            = $referenceItem;
	$doc             = $transaction->doc;
	$ownClientId     = $doc->clientID;
	$store           = $doc->store;
	$right           = null === $referenceItem ? $parent->_start : $referenceItem->right;
	$jsonContent     = array();
	$packJsonContent = static function () use ( &$jsonContent, &$left, $right, $parent, $transaction, $ownClientId, $store ): void {
		if ( 0 < count( $jsonContent ) ) {
			$left = new Structs\Item(
				createID( $ownClientId, getState( $store, $ownClientId ) ),
				$left,
				null !== $left ? $left->lastId : null,
				$right,
				null !== $right ? $right->id : null,
				$parent,
				null,
				new Structs\ContentAny( $jsonContent )
			);
			$left->integrate( $transaction, 0 );
			$jsonContent = array();
		}
	};

	foreach ( $content as $c ) {
		if ( null === $c || is_int( $c ) || is_float( $c ) || is_bool( $c ) || is_string( $c ) || is_array( $c ) || $c instanceof \stdClass ) {
			$jsonContent[] = $c;
			continue;
		}
		$packJsonContent();
		$left = new Structs\Item(
			createID( $ownClientId, getState( $store, $ownClientId ) ),
			$left,
			null !== $left ? $left->lastId : null,
			$right,
			null !== $right ? $right->id : null,
			$parent,
			null,
			contentForValue( $c )
		);
		$left->integrate( $transaction, 0 );
	}
	$packJsonContent();
}

/**
 * @return \RuntimeException
 */
function lengthExceeded(): \RuntimeException {
	return Lib0\Error::create( 'Length exceeded!' );
}

/**
 * @param mixed              $transaction Transaction.
 * @param Types\AbstractType $parent      Parent type.
 * @param int                $index       Index.
 * @param array<int,mixed>   $content     Content.
 * @return void
 */
function typeListInsertGenerics( $transaction, Types\AbstractType $parent, int $index, array $content ): void {
	if ( $index > $parent->_length ) {
		throw lengthExceeded();
	}
	if ( 0 === $index ) {
		if ( null !== $parent->_searchMarker ) {
			updateMarkerChanges( $parent->_searchMarker, $index, count( $content ) );
		}
		typeListInsertGenericsAfter( $transaction, $parent, null, $content );
		return;
	}
	$startIndex = $index;
	$marker     = findMarker( $parent, $index );
	$n          = $parent->_start;
	if ( null !== $marker ) {
		$n      = $marker->p;
		$index -= $marker->index;
		if ( 0 === $index ) {
			$n      = $n->prev;
			$index += ( null !== $n && $n->countable && ! $n->deleted ) ? $n->length : 0;
		}
	}
	for ( ; null !== $n; $n = $n->right ) {
		if ( ! $n->deleted && $n->countable ) {
			if ( $index <= $n->length ) {
				if ( $index < $n->length ) {
					getItemCleanStart( $transaction, createID( $n->id->client, $n->id->clock + $index ) );
				}
				break;
			}
			$index -= $n->length;
		}
	}
	if ( null !== $parent->_searchMarker ) {
		updateMarkerChanges( $parent->_searchMarker, $startIndex, count( $content ) );
	}
	typeListInsertGenericsAfter( $transaction, $parent, $n, $content );
}

/**
 * @param mixed              $transaction Transaction.
 * @param Types\AbstractType $parent      Parent type.
 * @param array<int,mixed>   $content     Content.
 * @return void
 */
function typeListPushGenerics( $transaction, Types\AbstractType $parent, array $content ): void {
	$n = $parent->_start;
	while ( null !== $n && null !== $n->right ) {
		$n = $n->right;
	}
	typeListInsertGenericsAfter( $transaction, $parent, $n, $content );
}

/**
 * @param mixed              $transaction Transaction.
 * @param Types\AbstractType $parent      Parent type.
 * @param int                $index       Index.
 * @param string             $text        Text.
 * @return void
 */
function typeListInsertText( $transaction, Types\AbstractType $parent, int $index, string $text ): void {
	if ( '' === $text ) {
		return;
	}
	if ( 0 === $index ) {
		$left  = null;
		$right = $parent->_start;
	} else {
		$marker = findMarker( $parent, $index );
		$n      = $parent->_start;
		if ( null !== $marker ) {
			$n      = $marker->p;
			$index -= $marker->index;
		}
		for ( ; null !== $n; $n = $n->right ) {
			if ( ! $n->deleted && $n->countable ) {
				if ( $index <= $n->length ) {
					if ( $index < $n->length ) {
						getItemCleanStart( $transaction, createID( $n->id->client, $n->id->clock + $index ) );
					}
					break;
				}
				$index -= $n->length;
			}
		}
		$left  = $n;
		$right = null === $n ? null : $n->right;
	}
	$doc  = $transaction->doc;
	$item = new Structs\Item(
		createID( $doc->clientID, getState( $doc->store, $doc->clientID ) ),
		$left,
		null !== $left ? $left->lastId : null,
		$right,
		null !== $right ? $right->id : null,
		$parent,
		null,
		new Structs\ContentString( $text )
	);
	$item->integrate( $transaction, 0 );
}

/**
 * @param mixed              $transaction Transaction.
 * @param Types\AbstractType $parent      Parent type.
 * @param int                $index       Index.
 * @param int                $length      Length.
 * @return void
 */
function typeListDelete( $transaction, Types\AbstractType $parent, int $index, int $length ): void {
	if ( 0 === $length ) {
		return;
	}
	$startIndex  = $index;
	$startLength = $length;
	$marker      = findMarker( $parent, $index );
	$n           = $parent->_start;
	if ( null !== $marker ) {
		$n      = $marker->p;
		$index -= $marker->index;
	}
	for ( ; null !== $n && 0 < $index; $n = $n->right ) {
		if ( ! $n->deleted && $n->countable ) {
			if ( $index < $n->length ) {
				getItemCleanStart( $transaction, createID( $n->id->client, $n->id->clock + $index ) );
			}
			$index -= $n->length;
		}
	}
	while ( 0 < $length && null !== $n ) {
		if ( ! $n->deleted ) {
			if ( $length < $n->length ) {
				getItemCleanStart( $transaction, createID( $n->id->client, $n->id->clock + $length ) );
			}
			$n->delete( $transaction );
			$length -= $n->length;
		}
		$n = $n->right;
	}
	if ( 0 < $length ) {
		throw lengthExceeded();
	}
	if ( null !== $parent->_searchMarker ) {
		updateMarkerChanges( $parent->_searchMarker, $startIndex, -$startLength + $length );
	}
}

/**
 * @param mixed              $transaction Transaction.
 * @param Types\AbstractType $parent      Parent type.
 * @param string             $key         Key.
 * @return void
 */
function typeMapDelete( $transaction, Types\AbstractType $parent, string $key ): void {
	if ( array_key_exists( $key, $parent->_map ) ) {
		$parent->_map[ $key ]->delete( $transaction );
	}
}

/**
 * @param mixed              $transaction Transaction.
 * @param Types\AbstractType $parent      Parent type.
 * @param string             $key         Key.
 * @param mixed              $value       Value.
 * @return void
 */
function typeMapSet( $transaction, Types\AbstractType $parent, string $key, $value ): void {
	$left = $parent->_map[ $key ] ?? null;
	$doc  = $transaction->doc;
	( new Structs\Item(
		createID( $doc->clientID, getState( $doc->store, $doc->clientID ) ),
		$left,
		null !== $left ? $left->lastId : null,
		null,
		null,
		$parent,
		$key,
		contentForValue( $value )
	) )->integrate( $transaction, 0 );
}

/**
 * @param Types\AbstractType $parent Parent type.
 * @param string             $key    Key.
 * @return mixed
 */
function typeMapGet( Types\AbstractType $parent, string $key ) {
	$val = $parent->_map[ $key ] ?? null;
	return null !== $val && ! $val->deleted ? $val->content->getContent()[ $val->length - 1 ] : Lib0\UndefinedValue::getInstance();
}

/**
 * @param Types\AbstractType $parent Parent type.
 * @return array<string,mixed>
 */
function typeMapGetAll( Types\AbstractType $parent ): array {
	$res = array();
	foreach ( $parent->_map as $key => $value ) {
		if ( ! $value->deleted ) {
			$res[ (string) $key ] = $value->content->getContent()[ $value->length - 1 ];
		}
	}
	return $res;
}

/**
 * @param Types\AbstractType $parent Parent type.
 * @param string             $key    Key.
 * @return bool
 */
function typeMapHas( Types\AbstractType $parent, string $key ): bool {
	$val = $parent->_map[ $key ] ?? null;
	return null !== $val && ! $val->deleted;
}

/**
 * @param Types\AbstractType $type Type.
 * @return array<int,array{0:string,1:Structs\Item}>
 */
function createMapIterator( Types\AbstractType $type ): array {
	$entries = array();
	foreach ( $type->_map as $key => $value ) {
		if ( ! $value->deleted ) {
			$entries[] = array( (string) $key, $value );
		}
	}
	return $entries;
}

/**
 * @param Types\AbstractType $parent Parent type.
 * @param Types\AbstractType $child  Child type.
 * @return array<int,string|int>
 */
function getPathTo( Types\AbstractType $parent, Types\AbstractType $child ): array {
	$path = array();
	while ( null !== $child->_item && $child !== $parent ) {
		if ( null !== $child->_item->parentSub ) {
			array_unshift( $path, $child->_item->parentSub );
		} else {
			$i = 0;
			for ( $c = $child->_item->parent->_start; null !== $c; $c = $c->right ) {
				if ( $c === $child->_item ) {
					break;
				}
				if ( ! $c->deleted && $c->countable ) {
					$i += $c->length;
				}
			}
			array_unshift( $path, $i );
		}
		$child = $child->_item->parent;
	}
	return $path;
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
 * @param object                            $encoder Encoder.
 * @param array<int,Structs\AbstractStruct> $structs Structs.
 * @param int                               $client  Client id.
 * @param int                               $clock   Clock.
 * @return void
 */
function writeStructs( object $encoder, array $structs, int $client, int $clock ): void {
	$clock           = Lib0\Math::max( $clock, $structs[0]->id->clock );
	$startNewStructs = findIndexSS( $structs, $clock );
	Lib0\Encoding::writeVarUint( $encoder->restEncoder, count( $structs ) - $startNewStructs );
	$encoder->writeClient( $client );
	Lib0\Encoding::writeVarUint( $encoder->restEncoder, $clock );
	$firstStruct = $structs[ $startNewStructs ];
	$firstStruct->write( $encoder, $clock - $firstStruct->id->clock );
	for ( $i = $startNewStructs + 1, $len = count( $structs ); $i < $len; $i++ ) {
		$structs[ $i ]->write( $encoder, 0 );
	}
}

/**
 * @param object            $encoder Encoder.
 * @param Utils\StructStore $store   Store.
 * @param array<int,int>    $_sm     Target state vector.
 * @return void
 */
function writeClientsStructs( object $encoder, Utils\StructStore $store, array $_sm ): void {
	$sm = array();
	foreach ( $_sm as $client => $clock ) {
		if ( getState( $store, (int) $client ) > $clock ) {
			$sm[ (int) $client ] = $clock;
		}
	}
	foreach ( getStateVector( $store ) as $client => $_clock ) {
		if ( ! array_key_exists( $client, $_sm ) ) {
			$sm[ (int) $client ] = 0;
		}
	}
	Lib0\Encoding::writeVarUint( $encoder->restEncoder, count( $sm ) );
	krsort( $sm, SORT_NUMERIC );
	foreach ( $sm as $client => $clock ) {
		writeStructs( $encoder, $store->clients[ $client ], (int) $client, $clock );
	}
}

/**
 * @param object    $decoder Decoder.
 * @param Utils\Doc $doc     Document.
 * @return array<int,array{i:int,refs:array<int,Structs\AbstractStruct>}>
 */
function readClientsStructRefs( object $decoder, Utils\Doc $doc ): array {
	$clientRefs        = array();
	$numOfStateUpdates = Lib0\Decoding::readVarUint( $decoder->restDecoder );
	for ( $i = 0; $i < $numOfStateUpdates; $i++ ) {
		$numberOfStructs       = Lib0\Decoding::readVarUint( $decoder->restDecoder );
		$refs                  = array();
		$client                = $decoder->readClient();
		$clock                 = Lib0\Decoding::readVarUint( $decoder->restDecoder );
		$clientRefs[ $client ] = array(
			'i'    => 0,
			'refs' => &$refs,
		);
		for ( $j = 0; $j < $numberOfStructs; $j++ ) {
			$info = $decoder->readInfo();
			switch ( Lib0\Binary::BITS5 & $info ) {
				case 0:
					$len    = $decoder->readLen();
					$refs[] = new Structs\GC( createID( $client, $clock ), $len );
					$clock += $len;
					break;
				case 10:
					$len    = Lib0\Decoding::readVarUint( $decoder->restDecoder );
					$refs[] = new Structs\Skip( createID( $client, $clock ), $len );
					$clock += $len;
					break;
				default:
					$cantCopyParentInfo = ( $info & ( Lib0\Binary::BIT7 | Lib0\Binary::BIT8 ) ) === 0;
					$struct             = new Structs\Item(
						createID( $client, $clock ),
						null,
						( $info & Lib0\Binary::BIT8 ) === Lib0\Binary::BIT8 ? $decoder->readLeftID() : null,
						null,
						( $info & Lib0\Binary::BIT7 ) === Lib0\Binary::BIT7 ? $decoder->readRightID() : null,
						$cantCopyParentInfo ? ( $decoder->readParentInfo() ? $doc->get( $decoder->readString() ) : $decoder->readLeftID() ) : null,
						$cantCopyParentInfo && ( $info & Lib0\Binary::BIT6 ) === Lib0\Binary::BIT6 ? $decoder->readString() : null,
						readItemContent( $decoder, $info )
					);
					$refs[]             = $struct;
					$clock             += $struct->length;
			}
		}
		$clientRefs[ $client ]['refs'] = $refs;
		unset( $refs );
	}
	return $clientRefs;
}

/**
 * @param Utils\Transaction                                              $transaction       Transaction.
 * @param Utils\StructStore                                              $store             Store.
 * @param array<int,array{i:int,refs:array<int,Structs\AbstractStruct>}> $clientsStructRefs Struct refs.
 * @return array{update:Lib0\Buffer,missing:array<int,int>}|null
 */
function integrateStructs( Utils\Transaction $transaction, Utils\StructStore $store, array &$clientsStructRefs ): ?array {
	$stack                = array();
	$clientsStructRefsIds = array_keys( $clientsStructRefs );
	sort( $clientsStructRefsIds, SORT_NUMERIC );
	if ( 0 === count( $clientsStructRefsIds ) ) {
		return null;
	}
	$getNextStructTarget = static function () use ( &$clientsStructRefsIds, &$clientsStructRefs ): ?array {
		if ( 0 === count( $clientsStructRefsIds ) ) {
			return null;
		}
			$nextClient        = $clientsStructRefsIds[ count( $clientsStructRefsIds ) - 1 ];
			$nextStructsTarget = $clientsStructRefs[ $nextClient ];
			$nextRefsCount     = count( $nextStructsTarget['refs'] );
		while ( $nextRefsCount === $nextStructsTarget['i'] ) {
			array_pop( $clientsStructRefsIds );
			if ( 0 < count( $clientsStructRefsIds ) ) {
				$nextClient        = $clientsStructRefsIds[ count( $clientsStructRefsIds ) - 1 ];
				$nextStructsTarget = $clientsStructRefs[ $nextClient ];
				$nextRefsCount     = count( $nextStructsTarget['refs'] );
			} else {
				return null;
			}
		}
		return array( 'client' => $nextClient );
	};
	$target              = $getNextStructTarget();
	if ( null === $target ) {
		return null;
	}
	$restStructs      = new Utils\StructStore();
	$missingSV        = array();
	$updateMissingSv  = static function ( int $client, int $clock ) use ( &$missingSV ): void {
		if ( ! array_key_exists( $client, $missingSV ) || $missingSV[ $client ] > $clock ) {
			$missingSV[ $client ] = $clock;
		}
	};
	$nextFromTarget   = static function ( int $client ) use ( &$clientsStructRefs ) {
		$index                             = $clientsStructRefs[ $client ]['i'];
		$clientsStructRefs[ $client ]['i'] = $index + 1;
		return $clientsStructRefs[ $client ]['refs'][ $index ];
	};
	$stackHead        = $nextFromTarget( $target['client'] );
	$state            = array();
	$addStackToRestSS = static function () use ( &$stack, &$clientsStructRefs, &$clientsStructRefsIds, $restStructs ): void {
		foreach ( $stack as $item ) {
			$client = $item->id->client;
			if ( array_key_exists( $client, $clientsStructRefs ) ) {
				--$clientsStructRefs[ $client ]['i'];
				$restStructs->clients[ $client ] = array_slice( $clientsStructRefs[ $client ]['refs'], $clientsStructRefs[ $client ]['i'] );
				unset( $clientsStructRefs[ $client ] );
			} else {
				$restStructs->clients[ $client ] = array( $item );
			}
			$clientsStructRefsIds = array_values(
				array_filter(
					$clientsStructRefsIds,
					static fn ( int $c ): bool => $c !== $client
				)
			);
		}
		$stack = array();
	};

	while ( true ) {
		if ( ! $stackHead instanceof Structs\Skip ) {
			$client = $stackHead->id->client;
			if ( ! array_key_exists( $client, $state ) ) {
				$state[ $client ] = getState( $store, $client );
			}
			$localClock = $state[ $client ];
			$offset     = $localClock - $stackHead->id->clock;
			if ( $offset < 0 ) {
				$stack[] = $stackHead;
				$updateMissingSv( $stackHead->id->client, $stackHead->id->clock - 1 );
				$addStackToRestSS();
			} else {
				$missing = $stackHead->getMissing( $transaction, $store );
				if ( null !== $missing ) {
					$stack[] = $stackHead;
					if ( ! array_key_exists( $missing, $clientsStructRefs ) || count( $clientsStructRefs[ $missing ]['refs'] ) === $clientsStructRefs[ $missing ]['i'] ) {
						$updateMissingSv( $missing, getState( $store, $missing ) );
						$addStackToRestSS();
					} else {
						$stackHead = $nextFromTarget( $missing );
						continue;
					}
				} elseif ( 0 === $offset || $offset < $stackHead->length ) {
					$stackHead->integrate( $transaction, $offset );
					$state[ $stackHead->id->client ] = $stackHead->id->clock + $stackHead->length;
				}
			}
		}
		if ( 0 < count( $stack ) ) {
			$stackHead = array_pop( $stack );
		} elseif ( null !== $target && $clientsStructRefs[ $target['client'] ]['i'] < count( $clientsStructRefs[ $target['client'] ]['refs'] ) ) {
			$stackHead = $nextFromTarget( $target['client'] );
		} else {
			$target = $getNextStructTarget();
			if ( null === $target ) {
				break;
			}
			$stackHead = $nextFromTarget( $target['client'] );
		}
	}
	if ( 0 < count( $restStructs->clients ) ) {
		$encoder = new Utils\UpdateEncoderV1();
		writeClientsStructs( $encoder, $restStructs, array() );
		Lib0\Encoding::writeVarUint( $encoder->restEncoder, 0 );
		return array(
			'missing' => $missingSV,
			'update'  => $encoder->toUint8Array(),
		);
	}
	return null;
}

/**
 * @param object            $encoder     Encoder.
 * @param Utils\Transaction $transaction Transaction.
 * @return void
 */
function writeStructsFromTransaction( object $encoder, Utils\Transaction $transaction ): void {
	writeClientsStructs( $encoder, $transaction->doc->store, $transaction->beforeState );
}

/**
 * @param Utils\Doc   $ydoc              Document.
 * @param Lib0\Buffer $update            Update.
 * @param mixed       $transactionOrigin Origin.
 * @return void
 */
function applyUpdate( Utils\Doc $ydoc, Lib0\Buffer $update, $transactionOrigin = null ): void {
	applyUpdateV2( $ydoc, $update, $transactionOrigin, Utils\UpdateDecoderV1::class );
}

/**
 * @param Utils\Doc   $ydoc              Document.
 * @param Lib0\Buffer $update            Update.
 * @param mixed       $transactionOrigin Origin.
 * @param string      $YDecoder          Decoder class.
 * @return void
 */
function applyUpdateV2( Utils\Doc $ydoc, Lib0\Buffer $update, $transactionOrigin = null, string $YDecoder = Utils\UpdateDecoderV1::class ): void {
	$decoder = Lib0\Decoding::createDecoder( $update );
	readUpdateV2( $decoder, $ydoc, $transactionOrigin, new $YDecoder( $decoder ) );
}

/**
 * @param Lib0\Decoder $decoder           Decoder.
 * @param Utils\Doc    $ydoc              Document.
 * @param mixed        $transactionOrigin Origin.
 * @return void
 */
function readUpdate( Lib0\Decoder $decoder, Utils\Doc $ydoc, $transactionOrigin = null ): void {
	readUpdateV2( $decoder, $ydoc, $transactionOrigin, new Utils\UpdateDecoderV1( $decoder ) );
}

/**
 * @param Lib0\Decoder $decoder           Decoder.
 * @param Utils\Doc    $ydoc              Document.
 * @param mixed        $transactionOrigin Origin.
 * @param object       $structDecoder     Struct decoder.
 * @return void
 */
function readUpdateV2( Lib0\Decoder $decoder, Utils\Doc $ydoc, $transactionOrigin = null, ?object $structDecoder = null ): void {
	$structDecoder = $structDecoder ?? new Utils\UpdateDecoderV1( $decoder );
	transact(
		$ydoc,
		function ( Utils\Transaction $transaction ) use ( $structDecoder ): void {
			$transaction->local    = false;
			$doc                   = $transaction->doc;
			$store                 = $doc->store;
			$ss                    = readClientsStructRefs( $structDecoder, $doc );
			$restStructs           = integrateStructs( $transaction, $store, $ss );
			$store->pendingStructs = $restStructs;
			$dsRest                = readAndApplyDeleteSet( $structDecoder, $transaction, $store );
			$store->pendingDs      = $dsRest;
		},
		$transactionOrigin,
		false
	);
}

/**
 * @param object         $encoder           Encoder.
 * @param Utils\Doc      $doc               Document.
 * @param array<int,int> $targetStateVector Target state vector.
 * @return void
 */
function writeStateAsUpdate( object $encoder, Utils\Doc $doc, array $targetStateVector = array() ): void {
	writeClientsStructs( $encoder, $doc->store, $targetStateVector );
	writeDeleteSet( $encoder, createDeleteSetFromStructStore( $doc->store ) );
}

/**
 * @param Utils\Doc        $doc                      Document.
 * @param Lib0\Buffer|null $encodedTargetStateVector Encoded target state.
 * @param object|null      $encoder                  Encoder.
 * @return Lib0\Buffer
 */
function encodeStateAsUpdateV2( Utils\Doc $doc, ?Lib0\Buffer $encodedTargetStateVector = null, ?object $encoder = null ): Lib0\Buffer {
	$targetStateVector = null === $encodedTargetStateVector ? array() : decodeStateVector( $encodedTargetStateVector );
	$encoder           = $encoder ?? new Utils\UpdateEncoderV1();
	writeStateAsUpdate( $encoder, $doc, $targetStateVector );
	return $encoder->toUint8Array();
}

/**
 * @param Utils\Doc        $doc                      Document.
 * @param Lib0\Buffer|null $encodedTargetStateVector Encoded target state.
 * @return Lib0\Buffer
 */
function encodeStateAsUpdate( Utils\Doc $doc, ?Lib0\Buffer $encodedTargetStateVector = null ): Lib0\Buffer {
	return encodeStateAsUpdateV2( $doc, $encodedTargetStateVector, new Utils\UpdateEncoderV1() );
}

/**
 * @param object $decoder Decoder.
 * @return array<int,int>
 */
function readStateVector( object $decoder ): array {
	$ss       = array();
	$ssLength = Lib0\Decoding::readVarUint( $decoder->restDecoder );
	for ( $i = 0; $i < $ssLength; $i++ ) {
		$client        = Lib0\Decoding::readVarUint( $decoder->restDecoder );
		$clock         = Lib0\Decoding::readVarUint( $decoder->restDecoder );
		$ss[ $client ] = $clock;
	}
	return $ss;
}

/**
 * @param Lib0\Buffer $decodedState Encoded state vector.
 * @return array<int,int>
 */
function decodeStateVector( Lib0\Buffer $decodedState ): array {
	return readStateVector( new Utils\DSDecoderV1( Lib0\Decoding::createDecoder( $decodedState ) ) );
}

/**
 * @param object         $encoder Encoder.
 * @param array<int,int> $sv      State vector.
 * @return object
 */
function writeStateVector( object $encoder, array $sv ): object {
	Lib0\Encoding::writeVarUint( $encoder->restEncoder, count( $sv ) );
	krsort( $sv, SORT_NUMERIC );
	foreach ( $sv as $client => $clock ) {
		Lib0\Encoding::writeVarUint( $encoder->restEncoder, (int) $client );
		Lib0\Encoding::writeVarUint( $encoder->restEncoder, $clock );
	}
	return $encoder;
}

/**
 * @param object    $encoder Encoder.
 * @param Utils\Doc $doc     Document.
 * @return object
 */
function writeDocumentStateVector( object $encoder, Utils\Doc $doc ): object {
	return writeStateVector( $encoder, getStateVector( $doc->store ) );
}

/**
 * @param Utils\Doc|array<int,int> $doc Doc or state vector.
 * @param object|null              $encoder Encoder.
 * @return Lib0\Buffer
 */
function encodeStateVectorV2( $doc, ?object $encoder = null ): Lib0\Buffer {
	$encoder = $encoder ?? new Utils\DSEncoderV1();
	if ( is_array( $doc ) ) {
		writeStateVector( $encoder, $doc );
	} else {
		writeDocumentStateVector( $encoder, $doc );
	}
	return $encoder->toUint8Array();
}

/**
 * @param Utils\Doc|array<int,int> $doc Doc or state vector.
 * @return Lib0\Buffer
 */
function encodeStateVector( $doc ): Lib0\Buffer {
	return encodeStateVectorV2( $doc, new Utils\DSEncoderV1() );
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
 * @param array<int,Structs\AbstractStruct> $structs Structs.
 * @param int                               $pos     Position.
 * @return int
 */
function tryToMergeWithLefts( array &$structs, int $pos ): int {
	$right = $structs[ $pos ];
	$left  = $structs[ $pos - 1 ];
	$i     = $pos;
	while ( $i > 0 ) {
		if ( $left->deleted === $right->deleted && get_class( $left ) === get_class( $right ) ) {
			if ( $left->mergeWith( $right ) ) {
				if ( $right instanceof Structs\Item && null !== $right->parentSub && ( $right->parent->_map[ $right->parentSub ] ?? null ) === $right ) {
					$right->parent->_map[ $right->parentSub ] = $left;
				}
				$right = $left;
				--$i;
				if ( $i > 0 ) {
					$left = $structs[ $i - 1 ];
				}
				continue;
			}
		}
		break;
	}
	$merged = $pos - $i;
	if ( 0 < $merged ) {
		array_splice( $structs, $pos + 1 - $merged, $merged );
	}
	return $merged;
}

/**
 * @param Utils\DeleteSet   $ds       Delete set.
 * @param Utils\StructStore $store    Store.
 * @param callable          $gcFilter GC filter.
 * @return void
 */
function tryGcDeleteSet( Utils\DeleteSet $ds, Utils\StructStore $store, callable $gcFilter ): void {
	foreach ( $ds->clients as $client => $deleteItems ) {
		if ( ! array_key_exists( $client, $store->clients ) ) {
			continue;
		}
		$structs =& $store->clients[ $client ];
		for ( $di = count( $deleteItems ) - 1; $di >= 0; $di-- ) {
			$deleteItem         = $deleteItems[ $di ];
			$endDeleteItemClock = $deleteItem->clock + $deleteItem->len;
			$structCount        = count( $structs );
			for ( $si = findIndexSS( $structs, $deleteItem->clock ); $si < $structCount && $structs[ $si ]->id->clock < $endDeleteItemClock; $si++ ) {
				$struct = $structs[ $si ];
				if ( $deleteItem->clock + $deleteItem->len <= $struct->id->clock ) {
					break;
				}
				if ( $struct instanceof Structs\Item && $struct->deleted && ! $struct->keep && $gcFilter( $struct ) ) {
					$struct->gc( $store, false );
				}
			}
		}
		unset( $structs );
	}
}

/**
 * @param Utils\DeleteSet   $ds    Delete set.
 * @param Utils\StructStore $store Store.
 * @return void
 */
function tryMergeDeleteSet( Utils\DeleteSet $ds, Utils\StructStore $store ): void {
	foreach ( $ds->clients as $client => $deleteItems ) {
		if ( ! array_key_exists( $client, $store->clients ) ) {
			continue;
		}
		$structs =& $store->clients[ $client ];
		for ( $di = count( $deleteItems ) - 1; $di >= 0; $di-- ) {
			$deleteItem            = $deleteItems[ $di ];
			$mostRightIndexToCheck = Lib0\Math::min( count( $structs ) - 1, 1 + findIndexSS( $structs, $deleteItem->clock + $deleteItem->len - 1 ) );
			for ( $si = $mostRightIndexToCheck; $si > 0 && $structs[ $si ]->id->clock >= $deleteItem->clock; ) {
				$si -= 1 + tryToMergeWithLefts( $structs, $si );
			}
		}
		unset( $structs );
	}
}

/**
 * @param Utils\DeleteSet   $ds       Delete set.
 * @param Utils\StructStore $store    Store.
 * @param callable          $gcFilter GC filter.
 * @return void
 */
function tryGc( Utils\DeleteSet $ds, Utils\StructStore $store, callable $gcFilter ): void {
	tryGcDeleteSet( $ds, $store, $gcFilter );
	tryMergeDeleteSet( $ds, $store );
}

/**
 * @param object            $encoder     Encoder.
 * @param Utils\Transaction $transaction Transaction.
 * @return bool
 */
function writeUpdateMessageFromTransaction( object $encoder, Utils\Transaction $transaction ): bool {
	$changed = 0 < count( $transaction->deleteSet->clients );
	foreach ( $transaction->afterState as $client => $clock ) {
		if ( ( $transaction->beforeState[ $client ] ?? null ) !== $clock ) {
			$changed = true;
			break;
		}
	}
	if ( ! $changed ) {
		return false;
	}
	sortAndMergeDeleteSet( $transaction->deleteSet );
	writeStructsFromTransaction( $encoder, $transaction );
	writeDeleteSet( $encoder, $transaction->deleteSet );
	return true;
}

/**
 * @param \SplObjectStorage $storage Object storage.
 * @return int
 */
function objectStorageCount( \SplObjectStorage $storage ): int {
	$count = 0;
	foreach ( $storage as $_object ) {
		++$count;
	}
	return $count;
}

/**
 * @param \SplObjectStorage $storage Object storage.
 * @return array<int,Utils\Doc>
 */
function objectStorageToArray( \SplObjectStorage $storage ): array {
	$array = array();
	foreach ( $storage as $object ) {
		$array[] = $object;
	}
	return $array;
}

/**
 * @param array<int,Utils\Transaction> $transactionCleanups Transactions.
 * @param int                          $i                   Index.
 * @return void
 */
function cleanupTransactions( array &$transactionCleanups, int $i ): void {
	if ( $i >= count( $transactionCleanups ) ) {
		return;
	}
	$transaction  = $transactionCleanups[ $i ];
	$doc          = $transaction->doc;
	$store        = $doc->store;
	$ds           = $transaction->deleteSet;
	$mergeStructs = $transaction->_mergeStructs;
	try {
		sortAndMergeDeleteSet( $ds );
		$transaction->afterState = getStateVector( $transaction->doc->store );
		$doc->emit( 'beforeObserverCalls', array( $transaction, $doc ) );
		$fs = array();
		foreach ( $transaction->changed as $itemtype ) {
			$subs = $transaction->changed[ $itemtype ];
			$fs[] = static function () use ( $itemtype, $transaction, $subs ): void {
				if ( null === $itemtype->_item || ! $itemtype->_item->deleted ) {
					$itemtype->_callObserver( $transaction, $subs );
				}
			};
		}
		$fs[] = static function () use ( &$fs, $transaction, $doc ): void {
			foreach ( $transaction->changedParentTypes as $type ) {
				$events = $transaction->changedParentTypes[ $type ];
				if ( 0 < count( $type->_dEH->l ) && ( null === $type->_item || ! $type->_item->deleted ) ) {
					$events = array_values(
						array_filter(
							$events,
							static fn ( Utils\YEvent $event ): bool => null === $event->target->_item || ! $event->target->_item->deleted
						)
					);
					foreach ( $events as $event ) {
						$event->currentTarget = $type;
						$event->_path         = null;
					}
					usort(
						$events,
						static fn ( Utils\YEvent $a, Utils\YEvent $b ): int => count( $a->path ) <=> count( $b->path )
					);
					if ( 0 < count( $events ) ) {
						$fs[] = static function () use ( $type, $events, $transaction ): void {
							callEventHandlerListeners( $type->_dEH, $events, $transaction );
						};
					}
				}
			}
			$fs[] = static function () use ( $doc, $transaction ): void {
				$doc->emit( 'afterTransaction', array( $transaction, $doc ) );
			};
		};
		Lib0\Func::callAll( $fs, array() );
	} finally {
		if ( $doc->gc ) {
			tryGcDeleteSet( $ds, $store, $doc->gcFilter );
		}
		tryMergeDeleteSet( $ds, $store );
		foreach ( $transaction->afterState as $client => $clock ) {
			$beforeClock = $transaction->beforeState[ $client ] ?? 0;
			if ( $beforeClock !== $clock && array_key_exists( $client, $store->clients ) ) {
				$structs        =& $store->clients[ $client ];
				$firstChangePos = Lib0\Math::max( findIndexSS( $structs, $beforeClock ), 1 );
				for ( $j = count( $structs ) - 1; $j >= $firstChangePos; ) {
					$j -= 1 + tryToMergeWithLefts( $structs, $j );
				}
				unset( $structs );
			}
		}
		for ( $j = count( $mergeStructs ) - 1; $j >= 0; $j-- ) {
			$client = $mergeStructs[ $j ]->id->client;
			$clock  = $mergeStructs[ $j ]->id->clock;
			if ( ! array_key_exists( $client, $store->clients ) ) {
				continue;
			}
			$structs           =& $store->clients[ $client ];
			$replacedStructPos = findIndexSS( $structs, $clock );
			if ( $replacedStructPos + 1 < count( $structs ) && tryToMergeWithLefts( $structs, $replacedStructPos + 1 ) > 1 ) {
				unset( $structs );
				continue;
			}
			if ( $replacedStructPos > 0 ) {
				tryToMergeWithLefts( $structs, $replacedStructPos );
			}
			unset( $structs );
		}
		if ( ! $transaction->local && ( $transaction->afterState[ $doc->clientID ] ?? null ) !== ( $transaction->beforeState[ $doc->clientID ] ?? null ) ) {
			$doc->clientID = generateNewClientId();
		}
		$doc->emit( 'afterTransactionCleanup', array( $transaction, $doc ) );
		$encoder = new Utils\UpdateEncoderV1();
		if ( writeUpdateMessageFromTransaction( $encoder, $transaction ) ) {
			$doc->emit( 'update', array( $encoder->toUint8Array(), $transaction->origin, $doc, $transaction ) );
		}
		if ( 0 < objectStorageCount( $transaction->subdocsAdded ) || 0 < objectStorageCount( $transaction->subdocsRemoved ) || 0 < objectStorageCount( $transaction->subdocsLoaded ) ) {
			foreach ( $transaction->subdocsAdded as $subdoc ) {
				$subdoc->clientID = $doc->clientID;
				if ( null === $subdoc->collectionid ) {
					$subdoc->collectionid = $doc->collectionid;
				}
				$doc->subdocs->attach( $subdoc );
			}
			foreach ( $transaction->subdocsRemoved as $subdoc ) {
				$doc->subdocs->detach( $subdoc );
			}
			$doc->emit(
				'subdocs',
				array(
					array(
						'loaded'  => objectStorageToArray( $transaction->subdocsLoaded ),
						'added'   => objectStorageToArray( $transaction->subdocsAdded ),
						'removed' => objectStorageToArray( $transaction->subdocsRemoved ),
					),
					$doc,
					$transaction,
				)
			);
			foreach ( $transaction->subdocsRemoved as $subdoc ) {
				$subdoc->destroy();
			}
		}
		if ( count( $transactionCleanups ) <= $i + 1 ) {
			$doc->_transactionCleanups = array();
			$doc->emit( 'afterAllTransactions', array( $doc, $transactionCleanups ) );
		} else {
			cleanupTransactions( $transactionCleanups, $i + 1 );
		}
	}
}

/**
 * @param Utils\Doc $doc    Document.
 * @param callable  $f      Callback.
 * @param mixed     $origin Origin.
 * @param bool      $local  Whether transaction is local.
 * @return mixed
 */
function transact( Utils\Doc $doc, callable $f, $origin = null, bool $local = true ) {
	$transactionCleanups =& $doc->_transactionCleanups;
	$initialCall         = false;
	$result              = null;
	if ( null === $doc->_transaction ) {
		$initialCall           = true;
		$doc->_transaction     = new Utils\Transaction( $doc, $origin, $local );
		$transactionCleanups[] = $doc->_transaction;
		if ( 1 === count( $transactionCleanups ) ) {
			$doc->emit( 'beforeAllTransactions', array( $doc ) );
		}
		$doc->emit( 'beforeTransaction', array( $doc->_transaction, $doc ) );
	}
	try {
		$result = $f( $doc->_transaction );
	} finally {
		if ( $initialCall ) {
			$finishCleanup     = $doc->_transaction === $transactionCleanups[0];
			$doc->_transaction = null;
			if ( $finishCleanup ) {
				cleanupTransactions( $transactionCleanups, 0 );
			}
		}
	}
	return $result;
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
 * @param object            $decoder     Decoder.
 * @param Utils\Transaction $transaction Transaction.
 * @param Utils\StructStore $store       Store.
 * @return Lib0\Buffer|null
 */
function readAndApplyDeleteSet( object $decoder, Utils\Transaction $transaction, Utils\StructStore $store ): ?Lib0\Buffer {
	$unappliedDS = new Utils\DeleteSet();
	$numClients  = Lib0\Decoding::readVarUint( $decoder->restDecoder );
	for ( $i = 0; $i < $numClients; $i++ ) {
		$decoder->resetDsCurVal();
		$client          = Lib0\Decoding::readVarUint( $decoder->restDecoder );
		$numberOfDeletes = Lib0\Decoding::readVarUint( $decoder->restDecoder );
		$structs         = $store->clients[ $client ] ?? array();
		$state           = getState( $store, $client );
		for ( $j = 0; $j < $numberOfDeletes; $j++ ) {
			$clock    = $decoder->readDsClock();
			$clockEnd = $clock + $decoder->readDsLen();
			if ( $clock < $state ) {
				if ( $state < $clockEnd ) {
					addToDeleteSet( $unappliedDS, $client, $state, $clockEnd - $state );
				}
				if ( ! array_key_exists( $client, $store->clients ) ) {
					continue;
				}
				$structs =& $store->clients[ $client ];
				$index   = findIndexSS( $structs, $clock );
				$struct  = $structs[ $index ];
				if ( ! $struct->deleted && $struct->id->clock < $clock && $struct instanceof Structs\Item ) {
					array_splice( $structs, $index + 1, 0, array( splitItem( $transaction, $struct, $clock - $struct->id->clock ) ) );
					++$index;
				}
				$structCount = count( $structs );
				while ( $index < $structCount ) {
					$struct = $structs[ $index++ ];
					if ( $struct->id->clock < $clockEnd ) {
						if ( ! $struct->deleted && $struct instanceof Structs\Item ) {
							if ( $clockEnd < $struct->id->clock + $struct->length ) {
								array_splice( $structs, $index, 0, array( splitItem( $transaction, $struct, $clockEnd - $struct->id->clock ) ) );
								++$structCount;
							}
							$struct->delete( $transaction );
						}
					} else {
						break;
					}
				}
				unset( $structs );
			} else {
				addToDeleteSet( $unappliedDS, $client, $clock, $clockEnd - $clock );
			}
		}
	}
	if ( 0 < count( $unappliedDS->clients ) ) {
		$ds = new Utils\UpdateEncoderV1();
		Lib0\Encoding::writeVarUint( $ds->restEncoder, 0 );
		writeDeleteSet( $ds, $unappliedDS );
		return $ds->toUint8Array();
	}
	return null;
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
