<?php
/**
 * CRDT item struct.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Structs;

use Yjs\Lib0\Binary;
use Yjs\Lib0\Error;
use Yjs\Utils\ID;

/**
 * Partial port of yjs/src/structs/Item.js needed by store/delete-set logic.
 */
class Item extends AbstractStruct {
	/**
	 * @var ID|null
	 */
	public ?ID $origin;

	/**
	 * @var Item|null
	 */
	public ?Item $left;

	/**
	 * @var Item|null
	 */
	public ?Item $right;

	/**
	 * @var ID|null
	 */
	public ?ID $rightOrigin;

	/**
	 * @var mixed
	 */
	public $parent;

	/**
	 * @var string|null
	 */
	public ?string $parentSub;

	/**
	 * @var ID|null
	 */
	public ?ID $redone = null;

	/**
	 * @var object
	 */
	public object $content;

	/**
	 * Bit1: keep; bit2: countable; bit3: deleted; bit4: marker.
	 *
	 * @var int
	 */
	public int $info;

	/**
	 * @param ID          $id          Item id.
	 * @param Item|null   $left        Current left item.
	 * @param ID|null     $origin      Origin id.
	 * @param Item|null   $right       Current right item.
	 * @param ID|null     $rightOrigin Right origin id.
	 * @param mixed       $parent      Parent value.
	 * @param string|null $parentSub   Parent sub key.
	 * @param object      $content     Item content.
	 */
	public function __construct( ID $id, ?Item $left, ?ID $origin, ?Item $right, ?ID $rightOrigin, $parent, ?string $parentSub, object $content ) {
		parent::__construct( $id, $content->getLength() );

		$this->origin      = $origin;
		$this->left        = $left;
		$this->right       = $right;
		$this->rightOrigin = $rightOrigin;
		$this->parent      = $parent;
		$this->parentSub   = $parentSub;
		$this->content     = $content;
		$this->info        = $content->isCountable() ? Binary::BIT2 : 0;
	}

	/**
	 * @param string $name Property name.
	 * @return mixed
	 */
	public function __get( string $name ) {
		switch ( $name ) {
			case 'marker':
				return 0 < ( $this->info & Binary::BIT4 );
			case 'keep':
				return 0 < ( $this->info & Binary::BIT1 );
			case 'countable':
				return 0 < ( $this->info & Binary::BIT2 );
			case 'deleted':
				return $this->getDeleted();
			case 'next':
				return $this->getNext();
			case 'prev':
				return $this->getPrev();
			case 'lastId':
				return 1 === $this->length ? $this->id : \Yjs\createID( $this->id->client, $this->id->clock + $this->length - 1 );
		}

		return parent::__get( $name );
	}

	/**
	 * @param string $name  Property name.
	 * @param mixed  $value Property value.
	 * @return void
	 */
	public function __set( string $name, $value ): void {
		switch ( $name ) {
			case 'marker':
				if ( ( 0 < ( $this->info & Binary::BIT4 ) ) !== (bool) $value ) {
					$this->info ^= Binary::BIT4;
				}
				return;
			case 'keep':
				if ( $this->__get( 'keep' ) !== (bool) $value ) {
					$this->info ^= Binary::BIT1;
				}
				return;
			case 'deleted':
				if ( $this->getDeleted() !== (bool) $value ) {
					$this->info ^= Binary::BIT3;
				}
				return;
		}

		$this->{$name} = $value;
	}

	/**
	 * @return bool
	 */
	protected function getDeleted(): bool {
		return 0 < ( $this->info & Binary::BIT3 );
	}

	/**
	 * @return void
	 */
	public function markDeleted(): void {
		$this->info |= Binary::BIT3;
	}

	/**
	 * @param mixed $transaction Transaction.
	 * @param mixed $store       Store.
	 * @return int|null
	 */
	public function getMissing( $transaction, $store ): ?int {
		unset( $transaction );

		if ( null !== $this->origin && $this->origin->client !== $this->id->client && $this->origin->clock >= \Yjs\getState( $store, $this->origin->client ) ) {
			return $this->origin->client;
		}
		if ( null !== $this->rightOrigin && $this->rightOrigin->client !== $this->id->client && $this->rightOrigin->clock >= \Yjs\getState( $store, $this->rightOrigin->client ) ) {
			return $this->rightOrigin->client;
		}
		if ( $this->parent instanceof ID && $this->id->client !== $this->parent->client && $this->parent->clock >= \Yjs\getState( $store, $this->parent->client ) ) {
			return $this->parent->client;
		}

		Error::methodUnimplemented();
		return null;
	}

	/**
	 * @param mixed $transaction Transaction.
	 * @param int   $offset      Offset.
	 * @return void
	 */
	public function integrate( $transaction, int $offset ): void {
		unset( $transaction, $offset );
		Error::methodUnimplemented();
	}

	/**
	 * @param AbstractStruct $right Struct to merge.
	 * @return bool
	 */
	public function mergeWith( AbstractStruct $right ): bool {
		unset( $right );
		Error::methodUnimplemented();
		return false;
	}

	/**
	 * @param mixed $transaction Transaction.
	 * @return void
	 */
	public function delete( $transaction ): void {
		unset( $transaction );
		if ( ! $this->getDeleted() ) {
			$this->markDeleted();
		}
	}

	/**
	 * @param mixed $store      Store.
	 * @param bool  $parentGCd  Whether parent was garbage-collected.
	 * @return void
	 */
	public function gc( $store, bool $parentGCd ): void {
		unset( $store, $parentGCd );
		Error::methodUnimplemented();
	}

	/**
	 * @param mixed $encoder Encoder.
	 * @param int   $offset  Offset.
	 * @param int   $unused  Unused encoding ref.
	 * @return void
	 */
	public function write( $encoder, int $offset, int $unused = 0 ): void {
		unset( $encoder, $offset, $unused );
		Error::methodUnimplemented();
	}

	/**
	 * @return Item|null
	 */
	private function getNext(): ?Item {
		$item = $this->right;
		while ( null !== $item && $item->deleted ) {
			$item = $item->right;
		}
		return $item;
	}

	/**
	 * @return Item|null
	 */
	private function getPrev(): ?Item {
		$item = $this->left;
		while ( null !== $item && $item->deleted ) {
			$item = $item->left;
		}
		return $item;
	}
}
