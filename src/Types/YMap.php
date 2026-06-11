<?php
/**
 * YMap public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Types;

/**
 * YMap API stub for the Yjs port red baseline.
 */
class YMap extends AbstractType implements \IteratorAggregate {
	use \Yjs\NotImplementedTrait;

	/**
	 * @var array<string,mixed>|null
	 */
	public ?array $_prelimContent;

	/**
	 * @param iterable<string,mixed>|null $entries Initial entries.
	 */
	public function __construct( $entries = null ) {
		parent::__construct();
		$this->_prelimContent = array();
		if ( null !== $entries ) {
			foreach ( $entries as $key => $value ) {
				$this->_prelimContent[ (string) $key ] = $value;
			}
		}
	}

	/**
	 * @param object                 $y    Y document.
	 * @param \Yjs\Structs\Item|null $item Item.
	 * @return void
	 */
	public function _integrate( object $y, ?\Yjs\Structs\Item $item ): void {
		parent::_integrate( $y, $item );
		foreach ( $this->_prelimContent ?? array() as $key => $value ) {
			$this->set( (string) $key, $value );
		}
		$this->_prelimContent = null;
	}

	/**
	 * @return YMap
	 */
	public function _copy(): YMap {
		return new YMap();
	}

	public function clone(): YMap {
		$map = new YMap();
		$this->forEach(
			static function ( $value, string $key ) use ( $map ): void {
				$map->set( $key, $value instanceof AbstractType ? $value->clone() : $value );
			}
		);
		return $map;
	}

	public function _callObserver( $transaction, array $parentSubs ): void {
		parent::_callObserver( $transaction, $parentSubs );
		\Yjs\callTypeObservers( $this, $transaction, new \Yjs\Types\YMapEvent( $this, $transaction, $parentSubs ) );
	}

	public function toJSON(): \stdClass {
		$map = new \stdClass();
		foreach ( $this->_map as $key => $item ) {
			if ( ! $item->deleted ) {
				$content     = $item->content->getContent();
				$value       = $content[ $item->length - 1 ];
				$map->{$key} = $value instanceof AbstractType ? $value->toJSON() : $value;
			}
		}
		return $map;
	}

	public function keys(): array {
		return array_keys( \Yjs\typeMapGetAll( $this ) );
	}

	public function values(): array {
		return array_values( \Yjs\typeMapGetAll( $this ) );
	}

	public function entries(): array {
		return \Yjs\createMapIterator( $this );
	}

	public function forEach( callable $f ): void {
		foreach ( $this->_map as $key => $item ) {
			if ( ! $item->deleted ) {
				$content = $item->content->getContent();
				$f( $content[ $item->length - 1 ], (string) $key, $this );
			}
		}
	}

	public function delete( string $key ): void {
		if ( null !== $this->doc ) {
			\Yjs\transact(
				$this->doc,
				function ( $transaction ) use ( $key ): void {
					\Yjs\typeMapDelete( $transaction, $this, $key );
				}
			);
		} else {
			unset( $this->_prelimContent[ $key ] );
		}
	}

	public function set( string $key, $value ) {
		if ( null !== $this->doc ) {
			\Yjs\transact(
				$this->doc,
				function ( $transaction ) use ( $key, $value ): void {
					\Yjs\typeMapSet( $transaction, $this, $key, $value );
				}
			);
		} else {
			$this->_prelimContent[ $key ] = $value;
		}
		return $value;
	}

	public function get( string $key ) {
		return \Yjs\typeMapGet( $this, $key );
	}

	public function has( string $key ): bool {
		return \Yjs\typeMapHas( $this, $key );
	}

	public function clear(): void {
		if ( null !== $this->doc ) {
			\Yjs\transact(
				$this->doc,
				function ( $transaction ): void {
					$this->forEach(
						static function ( $_value, string $key, YMap $map ) use ( $transaction ): void {
							\Yjs\typeMapDelete( $transaction, $map, $key );
						}
					);
				}
			);
		} else {
			$this->_prelimContent = array();
		}
	}

	/**
	 * @param mixed $encoder Encoder.
	 * @return void
	 */
	public function _write( $encoder ): void {
		$encoder->writeTypeRef( 1 );
	}

	/**
	 * @return \Traversable
	 */
	public function getIterator(): \Traversable {
		return new \ArrayIterator( $this->entries() );
	}
}
