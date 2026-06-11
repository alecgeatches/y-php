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
 * @param Utils\RelativePosition $rpos Relative position.
 * @return \stdClass
 */
function relativePositionToJSON( Utils\RelativePosition $rpos ): \stdClass {
	$json = new \stdClass();
	if ( null !== $rpos->type ) {
		$json->type = $rpos->type;
	}
	if ( null !== $rpos->tname ) {
		$json->tname = $rpos->tname;
	}
	if ( null !== $rpos->item ) {
		$json->item = $rpos->item;
	}
	$json->assoc = $rpos->assoc;
	return $json;
}

/**
 * @param mixed $json JSON-like position object.
 * @return Utils\RelativePosition
 */
function createRelativePositionFromJSON( $json ): Utils\RelativePosition {
	$type  = relativePositionJsonHas( $json, 'type' ) ? relativePositionJsonId( relativePositionJsonGet( $json, 'type' ) ) : null;
	$tname = relativePositionJsonHas( $json, 'tname' ) ? (string) relativePositionJsonGet( $json, 'tname' ) : null;
	$item  = relativePositionJsonHas( $json, 'item' ) ? relativePositionJsonId( relativePositionJsonGet( $json, 'item' ) ) : null;
	$assoc = relativePositionJsonHas( $json, 'assoc' ) ? (int) relativePositionJsonGet( $json, 'assoc' ) : 0;
	return new Utils\RelativePosition( $type, $tname, $item, $assoc );
}

/**
 * @param mixed  $json JSON-like value.
 * @param string $key  Key.
 * @return bool
 */
function relativePositionJsonHas( $json, string $key ): bool {
	return is_array( $json ) ? array_key_exists( $key, $json ) : ( $json instanceof \stdClass && property_exists( $json, $key ) );
}

/**
 * @param mixed  $json JSON-like value.
 * @param string $key  Key.
 * @return mixed
 */
function relativePositionJsonGet( $json, string $key ) {
	return is_array( $json ) ? $json[ $key ] : $json->{$key};
}

/**
 * @param mixed $json JSON-like ID.
 * @return Utils\ID|null
 */
function relativePositionJsonId( $json ): ?Utils\ID {
	if ( null === $json ) {
		return null;
	}
	return createID( (int) relativePositionJsonGet( $json, 'client' ), (int) relativePositionJsonGet( $json, 'clock' ) );
}

/**
 * @param Types\AbstractType $type  Type.
 * @param Utils\ID|null      $item  Item id.
 * @param int                $assoc Association.
 * @return Utils\RelativePosition
 */
function createRelativePosition( Types\AbstractType $type, ?Utils\ID $item, int $assoc = 0 ): Utils\RelativePosition {
	$typeid = null;
	$tname  = null;
	if ( null === $type->_item ) {
		$tname = findRootTypeKey( $type );
	} else {
		$typeid = createID( $type->_item->id->client, $type->_item->id->clock );
	}
	return new Utils\RelativePosition( $typeid, $tname, $item, $assoc );
}

/**
 * @param Types\AbstractType $type  Type.
 * @param int                $index Absolute index.
 * @param int                $assoc Association.
 * @return Utils\RelativePosition
 */
function createRelativePositionFromTypeIndex( Types\AbstractType $type, int $index, int $assoc = 0 ): Utils\RelativePosition {
	$t = $type->_start;
	if ( $assoc < 0 ) {
		if ( 0 === $index ) {
			return createRelativePosition( $type, null, $assoc );
		}
		--$index;
	}
	while ( null !== $t ) {
		if ( ! $t->deleted && $t->countable ) {
			if ( $t->length > $index ) {
				return createRelativePosition( $type, createID( $t->id->client, $t->id->clock + $index ), $assoc );
			}
			$index -= $t->length;
		}
		if ( null === $t->right && $assoc < 0 ) {
			return createRelativePosition( $type, $t->lastId, $assoc );
		}
		$t = $t->right;
	}
	return createRelativePosition( $type, null, $assoc );
}

/**
 * @param Lib0\Encoder           $encoder Encoder.
 * @param Utils\RelativePosition $rpos    Relative position.
 * @return Lib0\Encoder
 */
function writeRelativePosition( Lib0\Encoder $encoder, Utils\RelativePosition $rpos ): Lib0\Encoder {
	if ( null !== $rpos->item ) {
		Lib0\Encoding::writeVarUint( $encoder, 0 );
		writeID( $encoder, $rpos->item );
	} elseif ( null !== $rpos->tname ) {
		Lib0\Encoding::writeUint8( $encoder, 1 );
		Lib0\Encoding::writeVarString( $encoder, $rpos->tname );
	} elseif ( null !== $rpos->type ) {
		Lib0\Encoding::writeUint8( $encoder, 2 );
		writeID( $encoder, $rpos->type );
	} else {
		Lib0\Error::unexpectedCase();
	}
	Lib0\Encoding::writeVarInt( $encoder, $rpos->assoc );
	return $encoder;
}

/**
 * @param Utils\RelativePosition $rpos Relative position.
 * @return Lib0\Buffer
 */
function encodeRelativePosition( Utils\RelativePosition $rpos ): Lib0\Buffer {
	$encoder = Lib0\Encoding::createEncoder();
	writeRelativePosition( $encoder, $rpos );
	return Lib0\Encoding::toUint8Array( $encoder );
}

/**
 * @param Lib0\Decoder $decoder Decoder.
 * @return Utils\RelativePosition
 */
function readRelativePosition( Lib0\Decoder $decoder ): Utils\RelativePosition {
	$type   = null;
	$tname  = null;
	$itemID = null;
	switch ( Lib0\Decoding::readVarUint( $decoder ) ) {
		case 0:
			$itemID = readID( $decoder );
			break;
		case 1:
			$tname = Lib0\Decoding::readVarString( $decoder );
			break;
		case 2:
			$type = readID( $decoder );
			break;
	}
	$assoc = Lib0\Decoding::hasContent( $decoder ) ? (int) Lib0\Decoding::readVarInt( $decoder ) : 0;
	return new Utils\RelativePosition( $type, $tname, $itemID, $assoc );
}

/**
 * @param Lib0\Buffer $uint8Array Encoded relative position.
 * @return Utils\RelativePosition
 */
function decodeRelativePosition( Lib0\Buffer $uint8Array ): Utils\RelativePosition {
	return readRelativePosition( Lib0\Decoding::createDecoder( $uint8Array ) );
}

/**
 * @param Utils\StructStore $store Store.
 * @param Utils\ID          $id    Item id.
 * @return array{item:Structs\AbstractStruct,diff:int}
 */
function getItemWithOffset( Utils\StructStore $store, Utils\ID $id ): array {
	$item = getItem( $store, $id );
	return array(
		'item' => $item,
		'diff' => $id->clock - $item->id->clock,
	);
}

/**
 * @param Utils\RelativePosition $rpos                   Relative position.
 * @param Utils\Doc              $doc                    Document.
 * @param bool                   $followUndoneDeletions  Whether to follow redone items.
 * @return Utils\AbsolutePosition|null
 */
function createAbsolutePositionFromRelativePosition( Utils\RelativePosition $rpos, Utils\Doc $doc, bool $followUndoneDeletions = true ): ?Utils\AbsolutePosition {
	$store   = $doc->store;
	$rightID = $rpos->item;
	$typeID  = $rpos->type;
	$tname   = $rpos->tname;
	$assoc   = $rpos->assoc;
	$type    = null;
	$index   = 0;
	if ( null !== $rightID ) {
		if ( getState( $store, $rightID->client ) <= $rightID->clock ) {
			return null;
		}
		$res   = $followUndoneDeletions ? followRedone( $store, $rightID ) : getItemWithOffset( $store, $rightID );
		$right = $res['item'];
		if ( ! $right instanceof Structs\Item ) {
			return null;
		}
		$type = $right->parent;
		if ( null === $type->_item || ! $type->_item->deleted ) {
			$index = ( $right->deleted || ! $right->countable ) ? 0 : ( $res['diff'] + ( $assoc >= 0 ? 0 : 1 ) );
			$n     = $right->left;
			while ( null !== $n ) {
				if ( ! $n->deleted && $n->countable ) {
					$index += $n->length;
				}
				$n = $n->left;
			}
		}
	} else {
		if ( null !== $tname ) {
			$type = $doc->get( $tname );
		} elseif ( null !== $typeID ) {
			if ( getState( $store, $typeID->client ) <= $typeID->clock ) {
				return null;
			}
			$res  = $followUndoneDeletions ? followRedone( $store, $typeID ) : array( 'item' => getItem( $store, $typeID ) );
			$item = $res['item'];
			if ( $item instanceof Structs\Item && $item->content instanceof Structs\ContentType ) {
				$type = $item->content->type;
			} else {
				return null;
			}
		} else {
			Lib0\Error::unexpectedCase();
		}
		$index = $assoc >= 0 ? $type->_length : 0;
	}
	return new Utils\AbsolutePosition( $type, $index, $rpos->assoc );
}

/**
 * @param Utils\RelativePosition|null $a Left position.
 * @param Utils\RelativePosition|null $b Right position.
 * @return bool
 */
function compareRelativePositions( ?Utils\RelativePosition $a, ?Utils\RelativePosition $b ): bool {
	return $a === $b || (
		null !== $a &&
		null !== $b &&
		$a->tname === $b->tname &&
		compareIDs( $a->item, $b->item ) &&
		compareIDs( $a->type, $b->type ) &&
		$a->assoc === $b->assoc
	);
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
 * @param mixed $a Left attribute.
 * @param mixed $b Right attribute.
 * @return bool
 */
function equalAttrs( $a, $b ): bool {
	if ( $a === $b ) {
		return true;
	}
	if ( null === $a || null === $b ) {
		return false;
	}
	if ( ( is_array( $a ) || $a instanceof \stdClass ) && ( is_array( $b ) || $b instanceof \stdClass ) ) {
		$left  = Lib0\Obj::toArray( $a );
		$right = Lib0\Obj::toArray( $b );
		if ( count( $left ) !== count( $right ) ) {
			return false;
		}
		foreach ( $left as $key => $value ) {
			if ( ! array_key_exists( $key, $right ) || $right[ $key ] !== $value ) {
				return false;
			}
		}
		return true;
	}
	return false;
}

/**
 * @param array<string,mixed>   $currentAttributes Current attributes.
 * @param Structs\ContentFormat $format            Format item content.
 * @return void
 */
function updateCurrentAttributes( array &$currentAttributes, Structs\ContentFormat $format ): void {
	if ( null === $format->value ) {
		unset( $currentAttributes[ $format->key ] );
	} else {
		$currentAttributes[ $format->key ] = $format->value;
	}
}

/**
 * @param mixed                      $transaction Transaction.
 * @param Types\ItemTextListPosition $pos Position.
 * @param int                        $count       Count.
 * @return Types\ItemTextListPosition
 */
function findNextPosition( $transaction, Types\ItemTextListPosition $pos, int $count ): Types\ItemTextListPosition {
	while ( null !== $pos->right && $count > 0 ) {
		if ( $pos->right->content instanceof Structs\ContentFormat ) {
			if ( ! $pos->right->deleted ) {
				updateCurrentAttributes( $pos->currentAttributes, $pos->right->content );
			}
		} elseif ( ! $pos->right->deleted ) {
			if ( $count < $pos->right->length ) {
				getItemCleanStart( $transaction, createID( $pos->right->id->client, $pos->right->id->clock + $count ) );
			}
			$pos->index += $pos->right->length;
			$count      -= $pos->right->length;
		}
		$pos->left  = $pos->right;
		$pos->right = $pos->right->right;
	}
	return $pos;
}

/**
 * @param mixed              $transaction     Transaction.
 * @param Types\AbstractType $parent          Parent type.
 * @param int                $index           Index.
 * @param bool               $useSearchMarker Whether to use search markers.
 * @return Types\ItemTextListPosition
 */
function findPosition( $transaction, Types\AbstractType $parent, int $index, bool $useSearchMarker ): Types\ItemTextListPosition {
	$currentAttributes = array();
	$marker            = $useSearchMarker ? findMarker( $parent, $index ) : null;
	if ( null !== $marker ) {
		return findNextPosition( $transaction, new Types\ItemTextListPosition( $marker->p->left, $marker->p, $marker->index, $currentAttributes ), $index - $marker->index );
	}
	return findNextPosition( $transaction, new Types\ItemTextListPosition( null, $parent->_start, 0, $currentAttributes ), $index );
}

/**
 * @param Types\ItemTextListPosition $currPos    Current position.
 * @param array<string,mixed>        $attributes Attributes.
 * @return void
 */
function minimizeAttributeChanges( Types\ItemTextListPosition $currPos, array $attributes ): void {
	while ( true ) {
		if ( null === $currPos->right ) {
			break;
		}
		if ( $currPos->right->deleted ) {
			$currPos->forward();
			continue;
		}
		if ( $currPos->right->content instanceof Structs\ContentFormat ) {
			$key = $currPos->right->content->key;
			$val = array_key_exists( $key, $attributes ) && null !== $attributes[ $key ] ? $attributes[ $key ] : null;
			if ( equalAttrs( $val, $currPos->right->content->value ) ) {
				$currPos->forward();
				continue;
			}
		}
		break;
	}
}

/**
 * @param mixed                      $transaction Transaction.
 * @param Types\AbstractType         $parent      Parent type.
 * @param Types\ItemTextListPosition $currPos     Current position.
 * @param array<string,mixed>        $attributes  Attributes.
 * @return array<string,mixed>
 */
function insertAttributes( $transaction, Types\AbstractType $parent, Types\ItemTextListPosition $currPos, array $attributes ): array {
	$doc               = $transaction->doc;
	$ownClientId       = $doc->clientID;
	$negatedAttributes = array();
	foreach ( $attributes as $key => $val ) {
		$currentVal = array_key_exists( $key, $currPos->currentAttributes ) ? $currPos->currentAttributes[ $key ] : null;
		if ( ! equalAttrs( $currentVal, $val ) ) {
			$negatedAttributes[ (string) $key ] = $currentVal;
			$left                               = $currPos->left;
			$right                              = $currPos->right;
			$currPos->right                     = new Structs\Item(
				createID( $ownClientId, getState( $doc->store, $ownClientId ) ),
				$left,
				null !== $left ? $left->lastId : null,
				$right,
				null !== $right ? $right->id : null,
				$parent,
				null,
				new Structs\ContentFormat( (string) $key, $val )
			);
			$currPos->right->integrate( $transaction, 0 );
			$currPos->forward();
		}
	}
	return $negatedAttributes;
}

/**
 * @param mixed                      $transaction       Transaction.
 * @param Types\AbstractType         $parent            Parent type.
 * @param Types\ItemTextListPosition $currPos           Current position.
 * @param array<string,mixed>        $negatedAttributes Negated attributes.
 * @return void
 */
function insertNegatedAttributes( $transaction, Types\AbstractType $parent, Types\ItemTextListPosition $currPos, array $negatedAttributes ): void {
	while (
		null !== $currPos->right && (
			$currPos->right->deleted ||
			(
				$currPos->right->content instanceof Structs\ContentFormat &&
				array_key_exists( $currPos->right->content->key, $negatedAttributes ) &&
				equalAttrs( $negatedAttributes[ $currPos->right->content->key ], $currPos->right->content->value )
			)
		)
	) {
		if ( ! $currPos->right->deleted && $currPos->right->content instanceof Structs\ContentFormat ) {
			unset( $negatedAttributes[ $currPos->right->content->key ] );
		}
		$currPos->forward();
	}

	$doc         = $transaction->doc;
	$ownClientId = $doc->clientID;
	foreach ( $negatedAttributes as $key => $val ) {
		$left       = $currPos->left;
		$right      = $currPos->right;
		$nextFormat = new Structs\Item(
			createID( $ownClientId, getState( $doc->store, $ownClientId ) ),
			$left,
			null !== $left ? $left->lastId : null,
			$right,
			null !== $right ? $right->id : null,
			$parent,
			null,
			new Structs\ContentFormat( (string) $key, $val )
		);
		$nextFormat->integrate( $transaction, 0 );
		$currPos->right = $nextFormat;
		$currPos->forward();
	}
}

/**
 * @param mixed                      $transaction Transaction.
 * @param Types\AbstractType         $parent      Parent type.
 * @param Types\ItemTextListPosition $currPos     Current position.
 * @param mixed                      $text        Text/embed/type content.
 * @param array<string,mixed>        $attributes  Attributes.
 * @return void
 */
function insertText( $transaction, Types\AbstractType $parent, Types\ItemTextListPosition $currPos, $text, array $attributes ): void {
	foreach ( $currPos->currentAttributes as $key => $_val ) {
		if ( ! array_key_exists( $key, $attributes ) ) {
			$attributes[ $key ] = null;
		}
	}

	$doc         = $transaction->doc;
	$ownClientId = $doc->clientID;
	minimizeAttributeChanges( $currPos, $attributes );
	$negatedAttributes = insertAttributes( $transaction, $parent, $currPos, $attributes );
	if ( is_string( $text ) ) {
		$content = new Structs\ContentString( $text );
	} elseif ( $text instanceof Types\AbstractType ) {
		$content = new Structs\ContentType( $text );
	} else {
		$content = new Structs\ContentEmbed( $text );
	}

	$left  = $currPos->left;
	$right = $currPos->right;
	$index = $currPos->index;
	if ( null !== $parent->_searchMarker ) {
		updateMarkerChanges( $parent->_searchMarker, $currPos->index, $content->getLength() );
	}
	$right = new Structs\Item(
		createID( $ownClientId, getState( $doc->store, $ownClientId ) ),
		$left,
		null !== $left ? $left->lastId : null,
		$right,
		null !== $right ? $right->id : null,
		$parent,
		null,
		$content
	);
	$right->integrate( $transaction, 0 );
	$currPos->right = $right;
	$currPos->index = $index;
	$currPos->forward();
	insertNegatedAttributes( $transaction, $parent, $currPos, $negatedAttributes );
}

/**
 * @param mixed                      $transaction Transaction.
 * @param Types\AbstractType         $parent      Parent type.
 * @param Types\ItemTextListPosition $currPos     Current position.
 * @param int                        $length      Length.
 * @param array<string,mixed>        $attributes  Attributes.
 * @return void
 */
function formatText( $transaction, Types\AbstractType $parent, Types\ItemTextListPosition $currPos, int $length, array $attributes ): void {
	$doc         = $transaction->doc;
	$ownClientId = $doc->clientID;
	minimizeAttributeChanges( $currPos, $attributes );
	$negatedAttributes = insertAttributes( $transaction, $parent, $currPos, $attributes );

	while (
		null !== $currPos->right &&
		(
				$length > 0 ||
				(
					! empty( $negatedAttributes ) &&
					( $currPos->right->deleted || $currPos->right->content instanceof Structs\ContentFormat )
				)
		)
	) {
		if ( ! $currPos->right->deleted ) {
			if ( $currPos->right->content instanceof Structs\ContentFormat ) {
				$key         = $currPos->right->content->key;
				$value       = $currPos->right->content->value;
				$attrDefined = array_key_exists( $key, $attributes );
				if ( $attrDefined ) {
					$attr = $attributes[ $key ];
					if ( equalAttrs( $attr, $value ) ) {
						unset( $negatedAttributes[ $key ] );
					} else {
						if ( 0 === $length ) {
							break;
						}
						$negatedAttributes[ $key ] = $value;
					}
					$currPos->right->delete( $transaction );
				} else {
					$currPos->currentAttributes[ $key ] = $value;
				}
			} else {
				if ( $length < $currPos->right->length ) {
					getItemCleanStart( $transaction, createID( $currPos->right->id->client, $currPos->right->id->clock + $length ) );
				}
				$length -= $currPos->right->length;
			}
		}
		$currPos->forward();
	}

	if ( $length > 0 ) {
		$newlines       = str_repeat( "\n", $length );
		$currPos->right = new Structs\Item(
			createID( $ownClientId, getState( $doc->store, $ownClientId ) ),
			$currPos->left,
			null !== $currPos->left ? $currPos->left->lastId : null,
			$currPos->right,
			null !== $currPos->right ? $currPos->right->id : null,
			$parent,
			null,
			new Structs\ContentString( $newlines )
		);
		$currPos->right->integrate( $transaction, 0 );
		$currPos->forward();
	}
	insertNegatedAttributes( $transaction, $parent, $currPos, $negatedAttributes );
}

/**
 * @param mixed                      $transaction Transaction.
 * @param Types\ItemTextListPosition $currPos     Current position.
 * @param int                        $length      Delete length.
 * @return Types\ItemTextListPosition
 */
function deleteText( $transaction, Types\ItemTextListPosition $currPos, int $length ): Types\ItemTextListPosition {
	$startLength = $length;
	$startAttrs  = $currPos->currentAttributes;
	$start       = $currPos->right;
	while ( $length > 0 && null !== $currPos->right ) {
		if ( ! $currPos->right->deleted ) {
			$content = $currPos->right->content;
			if ( $content instanceof Structs\ContentType || $content instanceof Structs\ContentEmbed || $content instanceof Structs\ContentString ) {
				if ( $length < $currPos->right->length ) {
					getItemCleanStart( $transaction, createID( $currPos->right->id->client, $currPos->right->id->clock + $length ) );
				}
				$length -= $currPos->right->length;
				$currPos->right->delete( $transaction );
			}
		}
		$currPos->forward();
	}
	if ( null !== $start ) {
		cleanupFormattingGap( $transaction, $start, $currPos->right, $startAttrs, $currPos->currentAttributes );
	}
	$anchor = $currPos->left ?? $currPos->right;
	if ( null !== $anchor && null !== $anchor->parent->_searchMarker ) {
		updateMarkerChanges( $anchor->parent->_searchMarker, $currPos->index, -$startLength + $length );
	}
	return $currPos;
}

/**
 * @param mixed               $transaction     Transaction.
 * @param Structs\Item        $start           Start item.
 * @param Structs\Item|null   $curr            End item.
 * @param array<string,mixed> $startAttributes Start attributes.
 * @param array<string,mixed> $currAttributes  Current attributes.
 * @return int
 */
function cleanupFormattingGap( $transaction, Structs\Item $start, ?Structs\Item $curr, array $startAttributes, array &$currAttributes ): int {
	$end        = $start;
	$endFormats = array();
	while ( null !== $end && ( ! $end->countable || $end->deleted ) ) {
		if ( ! $end->deleted && $end->content instanceof Structs\ContentFormat ) {
			$endFormats[ $end->content->key ] = $end->content;
		}
		$end = $end->right;
	}

	$cleanups    = 0;
	$reachedCurr = false;
	while ( $start !== $end ) {
		if ( $curr === $start ) {
			$reachedCurr = true;
		}
		if ( ! $start->deleted && $start->content instanceof Structs\ContentFormat ) {
			$key            = $start->content->key;
			$value          = $start->content->value;
			$startAttrValue = array_key_exists( $key, $startAttributes ) ? $startAttributes[ $key ] : null;
			if ( ( $endFormats[ $key ] ?? null ) !== $start->content || $startAttrValue === $value ) {
				$start->delete( $transaction );
				++$cleanups;
				$currAttrValue = array_key_exists( $key, $currAttributes ) ? $currAttributes[ $key ] : null;
				if ( ! $reachedCurr && $currAttrValue === $value && $startAttrValue !== $value ) {
					if ( null === $startAttrValue ) {
						unset( $currAttributes[ $key ] );
					} else {
						$currAttributes[ $key ] = $startAttrValue;
					}
				}
			}
			if ( ! $reachedCurr && ! $start->deleted ) {
				updateCurrentAttributes( $currAttributes, $start->content );
			}
		}
		$start = $start->right;
	}
	return $cleanups;
}

/**
 * @param mixed             $transaction Transaction.
 * @param Structs\Item|null $item        Item.
 * @return void
 */
function cleanupContextlessFormattingGap( $transaction, ?Structs\Item $item ): void {
	while ( null !== $item && null !== $item->right && ( $item->right->deleted || ! $item->right->countable ) ) {
		$item = $item->right;
	}
	$attrs = array();
	while ( null !== $item && ( $item->deleted || ! $item->countable ) ) {
		if ( ! $item->deleted && $item->content instanceof Structs\ContentFormat ) {
			$key = $item->content->key;
			if ( array_key_exists( $key, $attrs ) ) {
				$item->delete( $transaction );
			} else {
				$attrs[ $key ] = true;
			}
		}
		$item = $item->left;
	}
}

/**
 * @param Types\YText $type Text type.
 * @return int
 */
function cleanupYTextFormatting( Types\YText $type ): int {
	$res = 0;
	if ( null === $type->doc ) {
		return $res;
	}
	transact(
		$type->doc,
		function ( Utils\Transaction $transaction ) use ( $type, &$res ): void {
			$start             = $type->_start;
			$end               = $type->_start;
			$startAttributes   = array();
			$currentAttributes = $startAttributes;
			while ( null !== $end ) {
				if ( false === $end->deleted ) {
					if ( $end->content instanceof Structs\ContentFormat ) {
						updateCurrentAttributes( $currentAttributes, $end->content );
					} elseif ( null !== $start ) {
						$res            += cleanupFormattingGap( $transaction, $start, $end, $startAttributes, $currentAttributes );
						$startAttributes = $currentAttributes;
						$start           = $end;
					}
				}
				$end = $end->right;
			}
		}
	);
	return $res;
}

/**
 * @param Utils\Transaction $transaction Transaction.
 * @return void
 */
function cleanupYTextAfterTransaction( Utils\Transaction $transaction ): void {
	$needFullCleanup = new \SplObjectStorage();
	$doc             = $transaction->doc;
	foreach ( $transaction->afterState as $client => $afterClock ) {
		$clock = $transaction->beforeState[ $client ] ?? 0;
		if ( $afterClock === $clock || ! array_key_exists( $client, $doc->store->clients ) ) {
			continue;
		}
		$structs =& $doc->store->clients[ $client ];
		iterateStructs(
			$transaction,
			$structs,
			$clock,
			$afterClock,
			static function ( $item ) use ( $needFullCleanup ): void {
				if ( $item instanceof Structs\Item && ! $item->deleted && $item->content instanceof Structs\ContentFormat && $item->parent instanceof Types\YText ) {
					$needFullCleanup->attach( $item->parent );
				}
			}
		);
		unset( $structs );
	}

	transact(
		$doc,
		function ( Utils\Transaction $t ) use ( $transaction, $needFullCleanup ): void {
			iterateDeletedStructs(
				$transaction,
				$transaction->deleteSet,
				static function ( $item ) use ( $t, $needFullCleanup ): void {
					if ( ! $item instanceof Structs\Item || ! $item->parent instanceof Types\YText || ! $item->parent->_hasFormatting || $needFullCleanup->contains( $item->parent ) ) {
						return;
					}
					$parent = $item->parent;
					if ( $item->content instanceof Structs\ContentFormat ) {
						$needFullCleanup->attach( $parent );
					} else {
						cleanupContextlessFormattingGap( $t, $item );
					}
				}
			);
			foreach ( $needFullCleanup as $yText ) {
				cleanupYTextFormatting( $yText );
			}
		}
	);
}

/**
 * @param Utils\DeleteSet $ds Delete set.
 * @param array<int,int>  $sm State map.
 * @return Utils\Snapshot
 */
function createSnapshot( Utils\DeleteSet $ds, array $sm ): Utils\Snapshot {
	return new Utils\Snapshot( $ds, $sm );
}

/**
 * @return Utils\Snapshot
 */
function emptySnapshot(): Utils\Snapshot {
	return createSnapshot( createDeleteSet(), array() );
}

/**
 * @param Utils\Doc $doc Document.
 * @return Utils\Snapshot
 */
function snapshot( Utils\Doc $doc ): Utils\Snapshot {
	return createSnapshot( createDeleteSetFromStructStore( $doc->store ), getStateVector( $doc->store ) );
}

/**
 * @param Structs\Item        $item     Item.
 * @param Utils\Snapshot|null $snapshot Snapshot.
 * @return bool
 */
function isVisible( Structs\Item $item, ?Utils\Snapshot $snapshot = null ): bool {
	return null === $snapshot ? ! $item->deleted : array_key_exists( $item->id->client, $snapshot->sv ) && ( $snapshot->sv[ $item->id->client ] ?? 0 ) > $item->id->clock && ! isDeleted( $snapshot->ds, $item->id );
}

/**
 * @param Utils\Transaction $transaction Transaction.
 * @param Utils\Snapshot    $snapshot    Snapshot.
 * @return void
 */
function splitSnapshotAffectedStructs( Utils\Transaction $transaction, Utils\Snapshot $snapshot ): void {
	if ( ! array_key_exists( 'splitSnapshotAffectedStructs', $transaction->meta ) || ! $transaction->meta['splitSnapshotAffectedStructs'] instanceof \SplObjectStorage ) {
		$transaction->meta['splitSnapshotAffectedStructs'] = new \SplObjectStorage();
	}
	$meta = $transaction->meta['splitSnapshotAffectedStructs'];
	if ( $meta->contains( $snapshot ) ) {
		return;
	}
	$store = $transaction->doc->store;
	foreach ( $snapshot->sv as $client => $clock ) {
		if ( $clock < getState( $store, (int) $client ) ) {
			getItemCleanStart( $transaction, createID( (int) $client, $clock ) );
		}
	}
	iterateDeletedStructs(
		$transaction,
		$snapshot->ds,
		static function (): void {}
	);
	$meta->attach( $snapshot );
}

/**
 * @param mixed  $op  Delta operation.
 * @param string $key Operation key.
 * @return bool
 */
function opHas( $op, string $key ): bool {
	return is_array( $op ) ? array_key_exists( $key, $op ) : ( $op instanceof \stdClass && property_exists( $op, $key ) );
}

/**
 * @param mixed  $op      Delta operation.
 * @param string $key     Operation key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function opGet( $op, string $key, $default = null ) {
	if ( is_array( $op ) ) {
		return array_key_exists( $key, $op ) ? $op[ $key ] : $default;
	}
	if ( $op instanceof \stdClass && property_exists( $op, $key ) ) {
		return $op->{$key};
	}
	return $default;
}

/**
 * @param mixed  $attr Attribute value.
 * @param string $key  Key.
 * @return mixed
 */
function changeAttrGet( $attr, string $key ) {
	if ( is_array( $attr ) ) {
		return $attr[ $key ] ?? null;
	}
	if ( $attr instanceof \stdClass && property_exists( $attr, $key ) ) {
		return $attr->{$key};
	}
	return null;
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
 * @param mixed $value Value.
 * @return string
 */
function xmlStringifyValue( $value ): string {
	if ( $value instanceof Lib0\UndefinedValue ) {
		return 'undefined';
	}
	if ( null === $value ) {
		return 'null';
	}
	if ( true === $value ) {
		return 'true';
	}
	if ( false === $value ) {
		return 'false';
	}
	if ( is_int( $value ) ) {
		return (string) $value;
	}
	if ( is_float( $value ) ) {
		if ( is_nan( $value ) ) {
			return 'NaN';
		}
		if ( is_infinite( $value ) ) {
			return $value > 0 ? 'Infinity' : '-Infinity';
		}
		return (string) $value;
	}
	if ( is_string( $value ) ) {
		return $value;
	}
	if ( $value instanceof Lib0\Buffer ) {
		return implode( ',', $value->toByteArray() );
	}
	if ( is_array( $value ) ) {
		if ( ! isListArray( $value ) ) {
			return '[object Object]';
		}
		return implode(
			',',
			array_map(
				static function ( $item ): string {
					return ( null === $item || $item instanceof Lib0\UndefinedValue ) ? '' : xmlStringifyValue( $item );
				},
				$value
			)
		);
	}
	return '[object Object]';
}

/**
 * @param array<mixed> $value Value.
 * @return bool
 */
function isListArray( array $value ): bool {
	$index = 0;
	foreach ( array_keys( $value ) as $key ) {
		if ( $key !== $index++ ) {
			return false;
		}
	}
	return true;
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
	$cs = array();
	for ( $n = $type->_start; null !== $n; $n = $n->right ) {
		if ( $n->countable && isVisible( $n, $snapshot ) ) {
			array_push( $cs, ...$n->content->getContent() );
		}
	}
	return $cs;
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
 * @param Types\AbstractType $parent   Parent type.
 * @param string             $key      Key.
 * @param Utils\Snapshot     $snapshot Snapshot.
 * @return mixed
 */
function typeMapGetSnapshot( Types\AbstractType $parent, string $key, Utils\Snapshot $snapshot ) {
	$v = $parent->_map[ $key ] ?? null;
	while ( null !== $v && ( ! isVisible( $v, $snapshot ) || null === $v->parentSub || $v->parentSub !== $key ) ) {
		$v = $v->left;
	}
	return null === $v ? Lib0\UndefinedValue::getInstance() : $v->content->getContent()[ $v->length - 1 ];
}

/**
 * @param Types\AbstractType $parent   Parent type.
 * @param Utils\Snapshot     $snapshot Snapshot.
 * @return array<string,mixed>
 */
function typeMapGetAllSnapshot( Types\AbstractType $parent, Utils\Snapshot $snapshot ): array {
	$res = array();
	foreach ( $parent->_map as $key => $_value ) {
		$value = typeMapGetSnapshot( $parent, (string) $key, $snapshot );
		if ( ! $value instanceof Lib0\UndefinedValue ) {
			$res[ (string) $key ] = $value;
		}
	}
	return $res;
}

/**
 * @param Utils\Doc      $originDoc Origin document.
 * @param Utils\Snapshot $snapshot  Snapshot.
 * @param Utils\Doc|null $newDoc    Optional target document.
 * @return Utils\Doc
 */
function createDocFromSnapshot( Utils\Doc $originDoc, Utils\Snapshot $snapshot, ?Utils\Doc $newDoc = null ): Utils\Doc {
	if ( $originDoc->gc ) {
		throw new \RuntimeException( 'Garbage-collection must be disabled in `originDoc`!' );
	}
	$newDoc  = $newDoc ?? new Utils\Doc();
	$encoder = new Utils\UpdateEncoderV1();
	$sv      = $snapshot->sv;
	$ds      = $snapshot->ds;
	$originDoc->transact(
		function ( Utils\Transaction $transaction ) use ( $originDoc, $sv, $ds, $encoder ): void {
			$size = 0;
			foreach ( $sv as $clock ) {
				if ( $clock > 0 ) {
					++$size;
				}
			}
			Lib0\Encoding::writeVarUint( $encoder->restEncoder, $size );
			foreach ( $sv as $client => $clock ) {
				$client = (int) $client;
				if ( 0 === $clock ) {
					continue;
				}
				if ( $clock < getState( $originDoc->store, $client ) ) {
					getItemCleanStart( $transaction, createID( $client, $clock ) );
				}
				$structs         = $originDoc->store->clients[ $client ] ?? array();
				$lastStructIndex = findIndexSS( $structs, $clock - 1 );
				Lib0\Encoding::writeVarUint( $encoder->restEncoder, $lastStructIndex + 1 );
				$encoder->writeClient( $client );
				Lib0\Encoding::writeVarUint( $encoder->restEncoder, 0 );
				for ( $i = 0; $i <= $lastStructIndex; $i++ ) {
					$structs[ $i ]->write( $encoder, 0 );
				}
			}
			writeDeleteSet( $encoder, $ds );
		}
	);
	applyUpdate( $newDoc, $encoder->toUint8Array(), 'snapshot' );
	return $newDoc;
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
 * @param Lib0\Buffer $buf Snapshot bytes.
 * @return Utils\Snapshot
 */
function decodeSnapshot( Lib0\Buffer $buf ): Utils\Snapshot {
	return decodeSnapshotV2( $buf, new Utils\DSDecoderV1( Lib0\Decoding::createDecoder( $buf ) ) );
}

/**
 * @param Utils\Snapshot $snapshot Snapshot.
 * @return Lib0\Buffer
 */
function encodeSnapshot( Utils\Snapshot $snapshot ): Lib0\Buffer {
	return encodeSnapshotV2( $snapshot, new Utils\DSEncoderV1() );
}

/**
 * @param Lib0\Buffer $buf     Snapshot bytes.
 * @param object|null $decoder Delete-set decoder.
 * @return Utils\Snapshot
 */
function decodeSnapshotV2( Lib0\Buffer $buf, ?object $decoder = null ): Utils\Snapshot {
	$decoder = $decoder ?? new Utils\DSDecoderV1( Lib0\Decoding::createDecoder( $buf ) );
	return new Utils\Snapshot( readDeleteSet( $decoder ), readStateVector( $decoder ) );
}

/**
 * @param Utils\Snapshot $snapshot Snapshot.
 * @param object|null    $encoder  Delete-set encoder.
 * @return Lib0\Buffer
 */
function encodeSnapshotV2( Utils\Snapshot $snapshot, ?object $encoder = null ): Lib0\Buffer {
	$encoder = $encoder ?? new Utils\DSEncoderV1();
	writeDeleteSet( $encoder, $snapshot->ds );
	writeStateVector( $encoder, $snapshot->sv );
	return $encoder->toUint8Array();
}

/**
 * @param object $decoder Update decoder.
 * @return array<int,Structs\AbstractStruct>
 */
function lazyStructReaderStructs( object $decoder ): array {
	$structs           = array();
	$numOfStateUpdates = Lib0\Decoding::readVarUint( $decoder->restDecoder );
	for ( $i = 0; $i < $numOfStateUpdates; $i++ ) {
		$numberOfStructs = Lib0\Decoding::readVarUint( $decoder->restDecoder );
		$client          = $decoder->readClient();
		$clock           = Lib0\Decoding::readVarUint( $decoder->restDecoder );
		for ( $j = 0; $j < $numberOfStructs; $j++ ) {
			$info = $decoder->readInfo();
			if ( 10 === $info ) {
				$len       = Lib0\Decoding::readVarUint( $decoder->restDecoder );
				$structs[] = new Structs\Skip( createID( $client, $clock ), $len );
				$clock    += $len;
			} elseif ( ( Lib0\Binary::BITS5 & $info ) !== 0 ) {
				$cantCopyParentInfo = ( $info & ( Lib0\Binary::BIT7 | Lib0\Binary::BIT8 ) ) === 0;
				$struct             = new Structs\Item(
					createID( $client, $clock ),
					null,
					( $info & Lib0\Binary::BIT8 ) === Lib0\Binary::BIT8 ? $decoder->readLeftID() : null,
					null,
					( $info & Lib0\Binary::BIT7 ) === Lib0\Binary::BIT7 ? $decoder->readRightID() : null,
					$cantCopyParentInfo ? ( $decoder->readParentInfo() ? $decoder->readString() : $decoder->readLeftID() ) : null,
					$cantCopyParentInfo && ( $info & Lib0\Binary::BIT6 ) === Lib0\Binary::BIT6 ? $decoder->readString() : null,
					readItemContent( $decoder, $info )
				);
				$structs[]          = $struct;
				$clock             += $struct->length;
			} else {
				$len       = $decoder->readLen();
				$structs[] = new Structs\GC( createID( $client, $clock ), $len );
				$clock    += $len;
			}
		}
	}
	return $structs;
}

/**
 * @param Lib0\Buffer $update Update.
 * @return void
 */
function logUpdate( Lib0\Buffer $update ): void {
	logUpdateV2( $update, Utils\UpdateDecoderV1::class );
}

/**
 * @param Lib0\Buffer $update   Update.
 * @param string      $YDecoder Decoder class.
 * @return void
 */
function logUpdateV2( Lib0\Buffer $update, string $YDecoder = Utils\UpdateDecoderV1::class ): void {
	decodeUpdateV2( $update, $YDecoder );
}

/**
 * @param Lib0\Buffer $update Update.
 * @return array{structs:array<int,Structs\AbstractStruct>,ds:Utils\DeleteSet}
 */
function decodeUpdate( Lib0\Buffer $update ): array {
	return decodeUpdateV2( $update, Utils\UpdateDecoderV1::class );
}

/**
 * @param Lib0\Buffer $update   Update.
 * @param string      $YDecoder Decoder class.
 * @return array{structs:array<int,Structs\AbstractStruct>,ds:Utils\DeleteSet}
 */
function decodeUpdateV2( Lib0\Buffer $update, string $YDecoder = Utils\UpdateDecoderV1::class ): array {
	$updateDecoder = new $YDecoder( Lib0\Decoding::createDecoder( $update ) );
	$lazyDecoder   = new Utils\LazyStructReader( $updateDecoder, false );
	$structs       = array();
	for ( $curr = $lazyDecoder->curr; null !== $curr; $curr = $lazyDecoder->next() ) {
		$structs[] = $curr;
	}
	return array(
		'structs' => $structs,
		'ds'      => readDeleteSet( $updateDecoder ),
	);
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
 * @param Types\AbstractType $parent Parent type.
 * @param Structs\Item|null  $child  Child item.
 * @return bool
 */
function isParentOf( Types\AbstractType $parent, ?Structs\Item $child ): bool {
	while ( null !== $child ) {
		if ( $child->parent === $parent ) {
			return true;
		}
		$childParent = $child->parent;
		$child       = $childParent instanceof Types\AbstractType ? $childParent->_item : null;
	}
	return false;
}

/**
 * @param Utils\StructStore $store Store.
 * @param Utils\ID          $id    ID.
 * @return array{item:Structs\AbstractStruct,diff:int}
 */
function followRedone( Utils\StructStore $store, Utils\ID $id ): array {
	$nextID = $id;
	$diff   = 0;
	do {
		if ( $diff > 0 ) {
			$nextID = createID( $nextID->client, $nextID->clock + $diff );
		}
		$item   = getItem( $store, $nextID );
		$diff   = $nextID->clock - $item->id->clock;
		$nextID = $item instanceof Structs\Item ? $item->redone : null;
	} while ( null !== $nextID && $item instanceof Structs\Item );
	return array(
		'item' => $item,
		'diff' => $diff,
	);
}

/**
 * @param Structs\Item|null $item Item.
 * @param bool              $keep Keep flag.
 * @return void
 */
function keepItem( ?Structs\Item $item, bool $keep ): void {
	while ( null !== $item && $item->keep !== $keep ) {
		$item->keep = $keep;
		$parent     = $item->parent;
		$item       = $parent instanceof Types\AbstractType ? $parent->_item : null;
	}
}

/**
 * @param array<int,Utils\StackItem> $stack Stack.
 * @param Utils\ID                   $id    ID.
 * @return bool
 */
function isDeletedByUndoStack( array $stack, Utils\ID $id ): bool {
	foreach ( $stack as $stackItem ) {
		if ( isDeleted( $stackItem->deletions, $id ) ) {
			return true;
		}
	}
	return false;
}

/**
 * @param Utils\Transaction $transaction            Transaction.
 * @param Structs\Item      $item                   Item to redo.
 * @param \SplObjectStorage $redoitems              Items to redo.
 * @param Utils\DeleteSet   $itemsToDelete          Insertions delete set.
 * @param bool              $ignoreRemoteMapChanges Whether to ignore remote map changes.
 * @param Utils\UndoManager $um                     Undo manager.
 * @return Structs\Item|null
 */
function redoItem( Utils\Transaction $transaction, Structs\Item $item, \SplObjectStorage $redoitems, Utils\DeleteSet $itemsToDelete, bool $ignoreRemoteMapChanges, Utils\UndoManager $um ): ?Structs\Item {
	$doc         = $transaction->doc;
	$store       = $doc->store;
	$ownClientID = $doc->clientID;
	$redone      = $item->redone;
	if ( null !== $redone ) {
		return getItemCleanStart( $transaction, $redone );
	}
	$parentItem = $item->parent instanceof Types\AbstractType ? $item->parent->_item : null;
	$left       = null;
	$right      = null;

	if ( null !== $parentItem && true === $parentItem->deleted ) {
		if ( null === $parentItem->redone && ( ! $redoitems->contains( $parentItem ) || null === redoItem( $transaction, $parentItem, $redoitems, $itemsToDelete, $ignoreRemoteMapChanges, $um ) ) ) {
			return null;
		}
		while ( null !== $parentItem->redone ) {
			$parentItem = getItemCleanStart( $transaction, $parentItem->redone );
		}
	}
	$parentType = null === $parentItem ? $item->parent : $parentItem->content->type;

	if ( null === $item->parentSub ) {
		$left  = $item->left;
		$right = $item;
		while ( null !== $left ) {
			$leftTrace = $left;
			while ( null !== $leftTrace && $leftTrace->parent instanceof Types\AbstractType && $leftTrace->parent->_item !== $parentItem ) {
				$leftTrace = null === $leftTrace->redone ? null : getItemCleanStart( $transaction, $leftTrace->redone );
			}
			if ( null !== $leftTrace && $leftTrace->parent instanceof Types\AbstractType && $leftTrace->parent->_item === $parentItem ) {
				$left = $leftTrace;
				break;
			}
			$left = $left->left;
		}
		while ( null !== $right ) {
			$rightTrace = $right;
			while ( null !== $rightTrace && $rightTrace->parent instanceof Types\AbstractType && $rightTrace->parent->_item !== $parentItem ) {
				$rightTrace = null === $rightTrace->redone ? null : getItemCleanStart( $transaction, $rightTrace->redone );
			}
			if ( null !== $rightTrace && $rightTrace->parent instanceof Types\AbstractType && $rightTrace->parent->_item === $parentItem ) {
				$right = $rightTrace;
				break;
			}
			$right = $right->right;
		}
	} else {
		$right = null;
		if ( null !== $item->right && ! $ignoreRemoteMapChanges ) {
			$left = $item;
			while (
				null !== $left &&
				null !== $left->right &&
				(
					null !== $left->right->redone ||
					isDeleted( $itemsToDelete, $left->right->id ) ||
					isDeletedByUndoStack( $um->undoStack, $left->right->id ) ||
					isDeletedByUndoStack( $um->redoStack, $left->right->id )
				)
			) {
				$left = $left->right;
				while ( null !== $left->redone ) {
					$left = getItemCleanStart( $transaction, $left->redone );
				}
			}
			if ( null !== $left && null !== $left->right ) {
				return null;
			}
		} else {
			$left = $parentType->_map[ $item->parentSub ] ?? null;
		}
		if ( null !== $left && $left->parent instanceof Types\AbstractType && $left->parent->_item !== $parentItem ) {
			$left = $parentType->_map[ $item->parentSub ] ?? null;
		}
	}

	$nextClock    = getState( $store, $ownClientID );
	$nextId       = createID( $ownClientID, $nextClock );
	$redoneItem   = new Structs\Item(
		$nextId,
		$left,
		null !== $left ? $left->lastId : null,
		$right,
		null !== $right ? $right->id : null,
		$parentType,
		$item->parentSub,
		$item->content->copy()
	);
	$item->redone = $nextId;
	keepItem( $redoneItem, true );
	$redoneItem->integrate( $transaction, 0 );
	return $redoneItem;
}

/**
 * @param Utils\Snapshot $snap1 Left snapshot.
 * @param Utils\Snapshot $snap2 Right snapshot.
 * @return bool
 */
function equalSnapshots( Utils\Snapshot $snap1, Utils\Snapshot $snap2 ): bool {
	if ( count( $snap1->sv ) !== count( $snap2->sv ) || count( $snap1->ds->clients ) !== count( $snap2->ds->clients ) ) {
		return false;
	}
	foreach ( $snap1->sv as $key => $value ) {
		if ( ! array_key_exists( $key, $snap2->sv ) || $snap2->sv[ $key ] !== $value ) {
			return false;
		}
	}
	foreach ( $snap1->ds->clients as $client => $dsitems1 ) {
		$dsitems2 = $snap2->ds->clients[ $client ] ?? array();
		if ( count( $dsitems1 ) !== count( $dsitems2 ) ) {
			return false;
		}
		for ( $i = 0, $len = count( $dsitems1 ); $i < $len; $i++ ) {
			if ( $dsitems1[ $i ]->clock !== $dsitems2[ $i ]->clock || $dsitems1[ $i ]->len !== $dsitems2[ $i ]->len ) {
				return false;
			}
		}
	}
	return true;
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
			$fs[] = static function () use ( $transaction ): void {
				if ( $transaction->_needFormattingCleanup ) {
					cleanupYTextAfterTransaction( $transaction );
				}
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
 * @param array<int,Lib0\Buffer> $updates Updates.
 * @return Lib0\Buffer
 */
function mergeUpdates( array $updates ): Lib0\Buffer {
	return mergeUpdatesV2( $updates, Utils\UpdateDecoderV1::class, Utils\UpdateEncoderV1::class );
}

/**
 * @param array<int,Lib0\Buffer> $updates  Updates.
 * @param string                 $YDecoder Decoder class.
 * @param string                 $YEncoder Encoder class.
 * @return Lib0\Buffer
 */
function mergeUpdatesV2( array $updates, string $YDecoder = Utils\UpdateDecoderV1::class, string $YEncoder = Utils\UpdateEncoderV1::class ): Lib0\Buffer {
	if ( 1 === count( $updates ) ) {
		return $updates[0];
	}
	$updateDecoders     = array_map(
		static fn ( Lib0\Buffer $update ) => new $YDecoder( Lib0\Decoding::createDecoder( $update ) ),
		$updates
	);
	$lazyStructDecoders = array_map(
		static fn ( object $decoder ): Utils\LazyStructReader => new Utils\LazyStructReader( $decoder, true ),
		$updateDecoders
	);
	$currWrite          = null;
	$updateEncoder      = new $YEncoder();
	$lazyStructEncoder  = new Utils\LazyStructWriter( $updateEncoder );

	while ( true ) {
		$lazyStructDecoders = array_values(
			array_filter(
				$lazyStructDecoders,
				static fn ( Utils\LazyStructReader $dec ): bool => null !== $dec->curr
			)
		);
		usort(
			$lazyStructDecoders,
			static function ( Utils\LazyStructReader $dec1, Utils\LazyStructReader $dec2 ): int {
				if ( $dec1->curr->id->client === $dec2->curr->id->client ) {
					$clockDiff = $dec1->curr->id->clock - $dec2->curr->id->clock;
					if ( 0 === $clockDiff ) {
						if ( get_class( $dec1->curr ) === get_class( $dec2->curr ) ) {
							return 0;
						}
						return $dec1->curr instanceof Structs\Skip ? 1 : -1;
					}
					return $clockDiff;
				}
				return $dec2->curr->id->client - $dec1->curr->id->client;
			}
		);
		if ( 0 === count( $lazyStructDecoders ) ) {
			break;
		}
		$currDecoder = $lazyStructDecoders[0];
		$firstClient = $currDecoder->curr->id->client;
		if ( null !== $currWrite ) {
			$curr     = $currDecoder->curr;
			$iterated = false;
			while (
				null !== $curr &&
				$curr->id->clock + $curr->length <= $currWrite['struct']->id->clock + $currWrite['struct']->length &&
				$curr->id->client >= $currWrite['struct']->id->client
			) {
				$curr     = $currDecoder->next();
				$iterated = true;
			}
			if (
				null === $curr ||
				$curr->id->client !== $firstClient ||
				( $iterated && $curr->id->clock > $currWrite['struct']->id->clock + $currWrite['struct']->length )
			) {
				continue;
			}

			if ( $firstClient !== $currWrite['struct']->id->client ) {
				writeStructToLazyStructWriter( $lazyStructEncoder, $currWrite['struct'], $currWrite['offset'] );
				$currWrite = array(
					'struct' => $curr,
					'offset' => 0,
				);
				$currDecoder->next();
			} elseif ( $currWrite['struct']->id->clock + $currWrite['struct']->length < $curr->id->clock ) {
				if ( $currWrite['struct'] instanceof Structs\Skip ) {
					$currWrite['struct']->length = $curr->id->clock + $curr->length - $currWrite['struct']->id->clock;
				} else {
					writeStructToLazyStructWriter( $lazyStructEncoder, $currWrite['struct'], $currWrite['offset'] );
					$diff      = $curr->id->clock - $currWrite['struct']->id->clock - $currWrite['struct']->length;
					$struct    = new Structs\Skip( createID( $firstClient, $currWrite['struct']->id->clock + $currWrite['struct']->length ), $diff );
					$currWrite = array(
						'struct' => $struct,
						'offset' => 0,
					);
				}
			} else {
				$diff = $currWrite['struct']->id->clock + $currWrite['struct']->length - $curr->id->clock;
				if ( $diff > 0 ) {
					if ( $currWrite['struct'] instanceof Structs\Skip ) {
						$currWrite['struct']->length -= $diff;
					} else {
						$curr = sliceStruct( $curr, $diff );
					}
				}
				if ( ! $currWrite['struct']->mergeWith( $curr ) ) {
					writeStructToLazyStructWriter( $lazyStructEncoder, $currWrite['struct'], $currWrite['offset'] );
					$currWrite = array(
						'struct' => $curr,
						'offset' => 0,
					);
					$currDecoder->next();
				}
			}
		} else {
			$currWrite = array(
				'struct' => $currDecoder->curr,
				'offset' => 0,
			);
			$currDecoder->next();
		}
		for ( $next = $currDecoder->curr; null !== $next && $next->id->client === $firstClient && $next->id->clock === $currWrite['struct']->id->clock + $currWrite['struct']->length && ! $next instanceof Structs\Skip; $next = $currDecoder->next() ) {
			writeStructToLazyStructWriter( $lazyStructEncoder, $currWrite['struct'], $currWrite['offset'] );
			$currWrite = array(
				'struct' => $next,
				'offset' => 0,
			);
		}
	}
	if ( null !== $currWrite ) {
		writeStructToLazyStructWriter( $lazyStructEncoder, $currWrite['struct'], $currWrite['offset'] );
	}
	finishLazyStructWriting( $lazyStructEncoder );
	$dss = array_map(
		static fn ( object $decoder ): Utils\DeleteSet => readDeleteSet( $decoder ),
		$updateDecoders
	);
	writeDeleteSet( $updateEncoder, mergeDeleteSets( $dss ) );
	return $updateEncoder->toUint8Array();
}

/**
 * @param Lib0\Buffer $update Update.
 * @return array{from:array<int,int>,to:array<int,int>}
 */
function parseUpdateMeta( Lib0\Buffer $update ): array {
	return parseUpdateMetaV2( $update, Utils\UpdateDecoderV1::class );
}

/**
 * @param Lib0\Buffer $update   Update.
 * @param string      $YDecoder Decoder class.
 * @return array{from:array<int,int>,to:array<int,int>}
 */
function parseUpdateMetaV2( Lib0\Buffer $update, string $YDecoder = Utils\UpdateDecoderV1::class ): array {
	$from          = array();
	$to            = array();
	$updateDecoder = new Utils\LazyStructReader( new $YDecoder( Lib0\Decoding::createDecoder( $update ) ), false );
	$curr          = $updateDecoder->curr;
	if ( null !== $curr ) {
		$currClient          = $curr->id->client;
		$currClock           = $curr->id->clock;
		$from[ $currClient ] = $currClock;
		for ( ; null !== $curr; $curr = $updateDecoder->next() ) {
			if ( $currClient !== $curr->id->client ) {
				$to[ $currClient ]         = $currClock;
				$from[ $curr->id->client ] = $curr->id->clock;
				$currClient                = $curr->id->client;
			}
			$currClock = $curr->id->clock + $curr->length;
		}
		$to[ $currClient ] = $currClock;
	}
	return array(
		'from' => $from,
		'to'   => $to,
	);
}

/**
 * @param Lib0\Buffer $update Update.
 * @return Lib0\Buffer
 */
function encodeStateVectorFromUpdate( Lib0\Buffer $update ): Lib0\Buffer {
	return encodeStateVectorFromUpdateV2( $update, Utils\DSEncoderV1::class, Utils\UpdateDecoderV1::class );
}

/**
 * @param Lib0\Buffer $update   Update.
 * @param string      $YEncoder Encoder class.
 * @param string      $YDecoder Decoder class.
 * @return Lib0\Buffer
 */
function encodeStateVectorFromUpdateV2( Lib0\Buffer $update, string $YEncoder = Utils\DSEncoderV1::class, string $YDecoder = Utils\UpdateDecoderV1::class ): Lib0\Buffer {
	$encoder       = new $YEncoder();
	$updateDecoder = new Utils\LazyStructReader( new $YDecoder( Lib0\Decoding::createDecoder( $update ) ), false );
	$curr          = $updateDecoder->curr;
	if ( null !== $curr ) {
		$size         = 0;
		$currClient   = $curr->id->client;
		$stopCounting = 0 !== $curr->id->clock;
		$currClock    = $stopCounting ? 0 : $curr->id->clock + $curr->length;
		for ( ; null !== $curr; $curr = $updateDecoder->next() ) {
			if ( $currClient !== $curr->id->client ) {
				if ( 0 !== $currClock ) {
					++$size;
					Lib0\Encoding::writeVarUint( $encoder->restEncoder, $currClient );
					Lib0\Encoding::writeVarUint( $encoder->restEncoder, $currClock );
				}
				$currClient   = $curr->id->client;
				$currClock    = 0;
				$stopCounting = 0 !== $curr->id->clock;
			}
			if ( $curr instanceof Structs\Skip ) {
				$stopCounting = true;
			}
			if ( ! $stopCounting ) {
				$currClock = $curr->id->clock + $curr->length;
			}
		}
		if ( 0 !== $currClock ) {
			++$size;
			Lib0\Encoding::writeVarUint( $encoder->restEncoder, $currClient );
			Lib0\Encoding::writeVarUint( $encoder->restEncoder, $currClock );
		}
		$enc = Lib0\Encoding::createEncoder();
		Lib0\Encoding::writeVarUint( $enc, $size );
		Lib0\Encoding::writeBinaryEncoder( $enc, $encoder->restEncoder );
		$encoder->restEncoder = $enc;
		return $encoder->toUint8Array();
	}
	Lib0\Encoding::writeVarUint( $encoder->restEncoder, 0 );
	return $encoder->toUint8Array();
}

/**
 * @param Lib0\Buffer $update Update.
 * @param Lib0\Buffer $sv     State vector.
 * @return Lib0\Buffer
 */
function diffUpdate( Lib0\Buffer $update, Lib0\Buffer $sv ): Lib0\Buffer {
	return diffUpdateV2( $update, $sv, Utils\UpdateDecoderV1::class, Utils\UpdateEncoderV1::class );
}

/**
 * @param Lib0\Buffer $update   Update.
 * @param Lib0\Buffer $sv       State vector.
 * @param string      $YDecoder Decoder class.
 * @param string      $YEncoder Encoder class.
 * @return Lib0\Buffer
 */
function diffUpdateV2( Lib0\Buffer $update, Lib0\Buffer $sv, string $YDecoder = Utils\UpdateDecoderV1::class, string $YEncoder = Utils\UpdateEncoderV1::class ): Lib0\Buffer {
	$state            = decodeStateVector( $sv );
	$encoder          = new $YEncoder();
	$lazyStructWriter = new Utils\LazyStructWriter( $encoder );
	$decoder          = new $YDecoder( Lib0\Decoding::createDecoder( $update ) );
	$reader           = new Utils\LazyStructReader( $decoder, false );
	while ( null !== $reader->curr ) {
		$curr       = $reader->curr;
		$currClient = $curr->id->client;
		$svClock    = $state[ $currClient ] ?? 0;
		if ( $reader->curr instanceof Structs\Skip ) {
			$reader->next();
			continue;
		}
		if ( $curr->id->clock + $curr->length > $svClock ) {
			writeStructToLazyStructWriter( $lazyStructWriter, $curr, (int) Lib0\Math::max( $svClock - $curr->id->clock, 0 ) );
			$reader->next();
			while ( null !== $reader->curr && $reader->curr->id->client === $currClient ) {
				writeStructToLazyStructWriter( $lazyStructWriter, $reader->curr, 0 );
				$reader->next();
			}
		} else {
			while ( null !== $reader->curr && $reader->curr->id->client === $currClient && $reader->curr->id->clock + $reader->curr->length <= $svClock ) {
				$reader->next();
			}
		}
	}
	finishLazyStructWriting( $lazyStructWriter );
	writeDeleteSet( $encoder, readDeleteSet( $decoder ) );
	return $encoder->toUint8Array();
}

/**
 * @param Structs\AbstractStruct $left Struct.
 * @param int                    $diff Split offset.
 * @return Structs\AbstractStruct
 */
function sliceStruct( Structs\AbstractStruct $left, int $diff ): Structs\AbstractStruct {
	if ( $left instanceof Structs\GC ) {
		return new Structs\GC( createID( $left->id->client, $left->id->clock + $diff ), $left->length - $diff );
	}
	if ( $left instanceof Structs\Skip ) {
		return new Structs\Skip( createID( $left->id->client, $left->id->clock + $diff ), $left->length - $diff );
	}
	if ( $left instanceof Structs\Item ) {
		return new Structs\Item(
			createID( $left->id->client, $left->id->clock + $diff ),
			null,
			createID( $left->id->client, $left->id->clock + $diff - 1 ),
			null,
			$left->rightOrigin,
			$left->parent,
			$left->parentSub,
			$left->content->splice( $diff )
		);
	}
	Lib0\Error::unexpectedCase();
}

/**
 * @param Utils\LazyStructWriter $lazyWriter Lazy writer.
 * @return void
 */
function flushLazyStructWriter( Utils\LazyStructWriter $lazyWriter ): void {
	if ( $lazyWriter->written > 0 ) {
		$lazyWriter->clientStructs[]      = array(
			'written'     => $lazyWriter->written,
			'restEncoder' => Lib0\Encoding::toUint8Array( $lazyWriter->encoder->restEncoder ),
		);
		$lazyWriter->encoder->restEncoder = Lib0\Encoding::createEncoder();
		$lazyWriter->written              = 0;
	}
}

/**
 * @param Utils\LazyStructWriter $lazyWriter Lazy writer.
 * @param Structs\AbstractStruct $struct     Struct.
 * @param int                    $offset     Offset.
 * @return void
 */
function writeStructToLazyStructWriter( Utils\LazyStructWriter $lazyWriter, Structs\AbstractStruct $struct, int $offset ): void {
	if ( $lazyWriter->written > 0 && $lazyWriter->currClient !== $struct->id->client ) {
		flushLazyStructWriter( $lazyWriter );
	}
	if ( 0 === $lazyWriter->written ) {
		$lazyWriter->currClient = $struct->id->client;
		$lazyWriter->encoder->writeClient( $struct->id->client );
		Lib0\Encoding::writeVarUint( $lazyWriter->encoder->restEncoder, $struct->id->clock + $offset );
	}
	$struct->write( $lazyWriter->encoder, $offset );
	++$lazyWriter->written;
}

/**
 * @param Utils\LazyStructWriter $lazyWriter Lazy writer.
 * @return void
 */
function finishLazyStructWriting( Utils\LazyStructWriter $lazyWriter ): void {
	flushLazyStructWriter( $lazyWriter );
	$restEncoder = $lazyWriter->encoder->restEncoder;
	Lib0\Encoding::writeVarUint( $restEncoder, count( $lazyWriter->clientStructs ) );
	foreach ( $lazyWriter->clientStructs as $partStructs ) {
		Lib0\Encoding::writeVarUint( $restEncoder, $partStructs['written'] );
		Lib0\Encoding::writeUint8Array( $restEncoder, $partStructs['restEncoder'] );
	}
}

/**
 * @param Lib0\Buffer $update           Update.
 * @param callable    $blockTransformer Transformer.
 * @param string      $YDecoder         Decoder class.
 * @param string      $YEncoder         Encoder class.
 * @return Lib0\Buffer
 */
function convertUpdateFormat( Lib0\Buffer $update, callable $blockTransformer, string $YDecoder, string $YEncoder ): Lib0\Buffer {
	$updateDecoder = new $YDecoder( Lib0\Decoding::createDecoder( $update ) );
	$lazyDecoder   = new Utils\LazyStructReader( $updateDecoder, false );
	$updateEncoder = new $YEncoder();
	$lazyWriter    = new Utils\LazyStructWriter( $updateEncoder );
	for ( $curr = $lazyDecoder->curr; null !== $curr; $curr = $lazyDecoder->next() ) {
		writeStructToLazyStructWriter( $lazyWriter, $blockTransformer( $curr ), 0 );
	}
	finishLazyStructWriting( $lazyWriter );
	writeDeleteSet( $updateEncoder, readDeleteSet( $updateDecoder ) );
	return $updateEncoder->toUint8Array();
}

/**
 * @param Lib0\Buffer $update Update.
 * @return Lib0\Buffer
 */
function convertUpdateFormatV1ToV2( Lib0\Buffer $update ): Lib0\Buffer {
	return convertUpdateFormat( $update, static fn ( $block ) => $block, Utils\UpdateDecoderV1::class, Utils\UpdateEncoderV1::class );
}

/**
 * @param Lib0\Buffer $update Update.
 * @return Lib0\Buffer
 */
function convertUpdateFormatV2ToV1( Lib0\Buffer $update ): Lib0\Buffer {
	return convertUpdateFormat( $update, static fn ( $block ) => $block, Utils\UpdateDecoderV1::class, Utils\UpdateEncoderV1::class );
}

/**
 * @param Lib0\Buffer              $update Update.
 * @param array<string,mixed>|null $opts   Obfuscator options.
 * @return Lib0\Buffer
 */
function obfuscateUpdate( Lib0\Buffer $update, ?array $opts = null ): Lib0\Buffer {
	return convertUpdateFormat( $update, createObfuscator( $opts ?? array() ), Utils\UpdateDecoderV1::class, Utils\UpdateEncoderV1::class );
}

/**
 * @param Lib0\Buffer              $update Update.
 * @param array<string,mixed>|null $opts   Obfuscator options.
 * @return Lib0\Buffer
 */
function obfuscateUpdateV2( Lib0\Buffer $update, ?array $opts = null ): Lib0\Buffer {
	return obfuscateUpdate( $update, $opts );
}

/**
 * @param array<string,mixed> $opts Options.
 * @return callable
 */
function createObfuscator( array $opts ): callable {
	$formatting           = array_key_exists( 'formatting', $opts ) ? (bool) $opts['formatting'] : true;
	$subdocs              = array_key_exists( 'subdocs', $opts ) ? (bool) $opts['subdocs'] : true;
	$yxml                 = array_key_exists( 'yxml', $opts ) ? (bool) $opts['yxml'] : true;
	$i                    = 0;
	$mapKeyCache          = array();
	$nodeNameCache        = array();
	$formattingKeyCache   = array();
	$formattingValueCache = array( 'N;' => null );
	return static function ( $block ) use ( &$i, &$mapKeyCache, &$nodeNameCache, &$formattingKeyCache, &$formattingValueCache, $formatting, $subdocs, $yxml ) {
		if ( $block instanceof Structs\GC || $block instanceof Structs\Skip ) {
			return $block;
		}
		if ( ! $block instanceof Structs\Item ) {
			Lib0\Error::unexpectedCase();
		}
		$content = $block->content;
		if ( $content instanceof Structs\ContentType ) {
			if ( $yxml ) {
				$type = $content->type;
				if ( $type instanceof Types\YXmlElement ) {
					$type->nodeName = obfuscatorSetIfUndefined( $nodeNameCache, $type->nodeName, 'node-' . $i );
				}
				if ( $type instanceof Types\YXmlHook ) {
					$type->hookName = obfuscatorSetIfUndefined( $nodeNameCache, $type->hookName, 'hook-' . $i );
				}
			}
		} elseif ( $content instanceof Structs\ContentAny ) {
			$content->arr = array_map( static fn (): int => $i, $content->arr );
		} elseif ( $content instanceof Structs\ContentBinary ) {
			$content->content = Lib0\Buffer::fromByteArray( array( $i ) );
		} elseif ( $content instanceof Structs\ContentDoc ) {
			if ( $subdocs ) {
				$content->opts      = new \stdClass();
				$content->doc->guid = (string) $i;
			}
		} elseif ( $content instanceof Structs\ContentEmbed ) {
			$content->embed = new \stdClass();
		} elseif ( $content instanceof Structs\ContentFormat ) {
			if ( $formatting ) {
				$content->key = obfuscatorSetIfUndefined( $formattingKeyCache, $content->key, (string) $i );
				$valueKey     = obfuscatorValueKey( $content->value );
				if ( ! array_key_exists( $valueKey, $formattingValueCache ) ) {
					$formattingValueCache[ $valueKey ] = (object) array( 'i' => $i );
				}
				$content->value = $formattingValueCache[ $valueKey ];
			}
		} elseif ( $content instanceof Structs\ContentJSON ) {
			$content->arr = array_map( static fn (): int => $i, $content->arr );
		} elseif ( $content instanceof Structs\ContentString ) {
			$content->str = str_repeat( (string) ( $i % 10 ), $content->getLength() );
		} elseif ( ! $content instanceof Structs\ContentDeleted ) {
			Lib0\Error::unexpectedCase();
		}
		if ( null !== $block->parentSub ) {
			$block->parentSub = obfuscatorSetIfUndefined( $mapKeyCache, $block->parentSub, (string) $i );
		}
		++$i;
		return $block;
	};
}

/**
 * @param array<string,string> $cache Cache.
 * @param string               $key   Key.
 * @param string               $value Value.
 * @return string
 */
function obfuscatorSetIfUndefined( array &$cache, string $key, string $value ): string {
	if ( ! array_key_exists( $key, $cache ) ) {
		$cache[ $key ] = $value;
	}
	return $cache[ $key ];
}

/**
 * @param mixed $value Value.
 * @return string
 */
function obfuscatorValueKey( $value ): string {
	if ( is_object( $value ) ) {
		return 'o:' . spl_object_hash( $value );
	}
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		return serialize( $value );
}

/**
 * @param Utils\Transaction $tr        Transaction.
 * @param Utils\UndoManager $um        Undo manager.
 * @param Utils\StackItem   $stackItem Stack item.
 * @return void
 */
function clearUndoManagerStackItem( Utils\Transaction $tr, Utils\UndoManager $um, Utils\StackItem $stackItem ): void {
	iterateDeletedStructs(
		$tr,
		$stackItem->deletions,
		static function ( $item ) use ( $tr, $um ): void {
			if ( $item instanceof Structs\Item && $um->scopeContainsItem( $tr->doc, $item ) ) {
				keepItem( $item, false );
			}
		}
	);
}

/**
 * @param Utils\UndoManager          $undoManager Undo manager.
 * @param array<int,Utils\StackItem> $stack       Stack, passed by reference.
 * @param string                     $eventType   Event type.
 * @return Utils\StackItem|null
 */
function popStackItem( Utils\UndoManager $undoManager, array &$stack, string $eventType ): ?Utils\StackItem {
	$_tr = null;
	$doc = $undoManager->doc;
		transact(
			$doc,
			function ( Utils\Transaction $transaction ) use ( $undoManager, &$stack, &$_tr ): void {
				while ( null === $undoManager->currStackItem ) {
					$stackItem = array_pop( $stack );
					if ( null === $stackItem ) {
						break;
					}
					$store           = $transaction->doc->store;
					$itemsToRedo     = new \SplObjectStorage();
					$itemsToDelete   = array();
					$performedChange = false;

					iterateDeletedStructs(
						$transaction,
						$stackItem->insertions,
						static function ( $struct ) use ( $transaction, $store, $undoManager, &$itemsToDelete ): void {
							if ( $struct instanceof Structs\Item ) {
								if ( null !== $struct->redone ) {
									$res = followRedone( $store, $struct->id );
									if ( $res['diff'] > 0 ) {
										$res['item'] = getItemCleanStart( $transaction, createID( $res['item']->id->client, $res['item']->id->clock + $res['diff'] ) );
									}
									$struct = $res['item'];
								}
								if ( $struct instanceof Structs\Item && ! $struct->deleted && $undoManager->scopeContainsItem( $transaction->doc, $struct ) ) {
									$itemsToDelete[] = $struct;
								}
							}
						}
					);

					iterateDeletedStructs(
						$transaction,
						$stackItem->deletions,
						static function ( $struct ) use ( $transaction, $undoManager, $stackItem, $itemsToRedo ): void {
							if (
							$struct instanceof Structs\Item &&
							$undoManager->scopeContainsItem( $transaction->doc, $struct ) &&
							! isDeleted( $stackItem->insertions, $struct->id )
							) {
								$itemsToRedo->attach( $struct );
							}
						}
					);

					foreach ( $itemsToRedo as $struct ) {
						$performedChange = null !== redoItem( $transaction, $struct, $itemsToRedo, $stackItem->insertions, $undoManager->ignoreRemoteMapChanges, $undoManager ) || $performedChange;
					}
					for ( $i = count( $itemsToDelete ) - 1; $i >= 0; $i-- ) {
						$item = $itemsToDelete[ $i ];
						if ( ( $undoManager->deleteFilter )( $item ) ) {
							$item->delete( $transaction );
							$performedChange = true;
						}
					}
					$undoManager->currStackItem = $performedChange ? $stackItem : null;
				}
				foreach ( $transaction->changed as $type ) {
					$subProps = $transaction->changed[ $type ];
					if ( in_array( null, $subProps, true ) && $type->_searchMarker ) {
						$type->_searchMarker = array();
					}
				}
				$_tr = $transaction;
			},
			$undoManager
		);
	$res = $undoManager->currStackItem;
	if ( null !== $res && null !== $_tr ) {
		$undoManager->currStackItem = null;
		$undoManager->emit(
			'stack-item-popped',
			array(
				array(
					'stackItem'          => $res,
					'type'               => $eventType,
					'changedParentTypes' => $_tr->changedParentTypes,
					'origin'             => $undoManager,
				),
				$undoManager,
			)
		);
	}
	return $res;
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
 * @param Utils\Snapshot $snapshot Snapshot.
 * @param Lib0\Buffer    $update   Update.
 * @param string         $YDecoder Decoder class.
 * @return bool
 */
function snapshotContainsUpdateV2( Utils\Snapshot $snapshot, Lib0\Buffer $update, string $YDecoder = Utils\UpdateDecoderV1::class ): bool {
	$updateDecoder = new $YDecoder( Lib0\Decoding::createDecoder( $update ) );
	$lazyDecoder   = new Utils\LazyStructReader( $updateDecoder, false );
	for ( $curr = $lazyDecoder->curr; null !== $curr; $curr = $lazyDecoder->next() ) {
		if ( ( $snapshot->sv[ $curr->id->client ] ?? 0 ) < $curr->id->clock + $curr->length ) {
			return false;
		}
	}
	$mergedDS = mergeDeleteSets( array( $snapshot->ds, readDeleteSet( $updateDecoder ) ) );
	return equalDeleteSets( $snapshot->ds, $mergedDS );
}

/**
 * @param Utils\Snapshot $snapshot Snapshot.
 * @param Lib0\Buffer    $update   Update.
 * @return bool
 */
function snapshotContainsUpdate( Utils\Snapshot $snapshot, Lib0\Buffer $update ): bool {
	return snapshotContainsUpdateV2( $snapshot, $update, Utils\UpdateDecoderV1::class );
}
