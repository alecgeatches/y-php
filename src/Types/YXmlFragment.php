<?php
/**
 * YXmlFragment public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Types;

/**
 * YXmlFragment API stub for the Yjs port red baseline.
 */
class YXmlFragment extends AbstractType {
	use \Yjs\NotImplementedTrait;

	/**
	 * @var array<int,mixed>|null
	 */
	public ?array $_prelimContent;

	public function __construct() {
		parent::__construct();
		$this->_prelimContent = array();
	}

	/**
	 * @param object                 $y    Y document.
	 * @param \Yjs\Structs\Item|null $item Item.
	 * @return void
	 */
	public function _integrate( object $y, ?\Yjs\Structs\Item $item ): void {
		parent::_integrate( $y, $item );
		$this->_prelimContent = null;
	}

	/**
	 * @return YXmlFragment
	 */
	public function _copy(): YXmlFragment {
		return new YXmlFragment();
	}

	public function clone(): YXmlFragment {
		$fragment = new YXmlFragment();
		$fragment->insert(
			0,
			array_map(
				static fn ( $el ) => $el instanceof AbstractType ? $el->clone() : $el,
				$this->toArray()
			)
		);
		return $fragment;
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function createTreeWalker( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function querySelector( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function querySelectorAll( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	public function _callObserver( $transaction, array $parentSubs ): void {
		parent::_callObserver( $transaction, $parentSubs );
		\Yjs\callTypeObservers( $this, $transaction, new \Yjs\Utils\YEvent( $this, $transaction ) );
	}

	public function toString(): string {
		$out = '';
		foreach ( $this->toArray() as $child ) {
			$out .= is_object( $child ) && method_exists( $child, 'toString' ) ? $child->toString() : (string) $child;
		}
		return $out;
	}

	public function toJSON(): string {
		return $this->toString();
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function toDOM( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	public function insert( int $index, array $content ): void {
		if ( null !== $this->doc ) {
			\Yjs\transact(
				$this->doc,
				function ( $transaction ) use ( $index, $content ): void {
					\Yjs\typeListInsertGenerics( $transaction, $this, $index, $content );
				}
			);
		} else {
			array_splice( $this->_prelimContent, $index, 0, $content );
		}
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function insertAfter( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	public function delete( int $index, int $length = 1 ): void {
		if ( null !== $this->doc ) {
			\Yjs\transact(
				$this->doc,
				function ( $transaction ) use ( $index, $length ): void {
					\Yjs\typeListDelete( $transaction, $this, $index, $length );
				}
			);
		} else {
			array_splice( $this->_prelimContent, $index, $length );
		}
	}

	public function toArray(): array {
		return \Yjs\typeListToArray( $this );
	}

	public function push( array $content ): void {
		$this->insert( $this->_length, $content );
	}

	public function unshift( array $content ): void {
		$this->insert( 0, $content );
	}

	public function get( int $index ) {
		return \Yjs\typeListGet( $this, $index );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function slice( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function forEach( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed $encoder Encoder.
	 * @return void
	 */
	public function _write( $encoder ): void {
		$encoder->writeTypeRef( 4 );
	}
}
