<?php
/**
 * YText public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Types;

/**
 * YText API stub for the Yjs port red baseline.
 */
class YText extends AbstractType {
	use \Yjs\NotImplementedTrait;

	/**
	 * @var string
	 */
	public string $_pending;

	/**
	 * @var bool
	 */
	public bool $_hasFormatting;

	/**
	 * @param string $string Initial text.
	 */
	public function __construct( string $string = '' ) {
		parent::__construct();
		$this->_pending       = $string;
		$this->_searchMarker  = array();
		$this->_hasFormatting = false;
	}

	/**
	 * @return YText
	 */
	public function _copy(): YText {
		return new YText();
	}

	/**
	 * @param object                 $y    Y document.
	 * @param \Yjs\Structs\Item|null $item Item.
	 * @return void
	 */
	public function _integrate( object $y, ?\Yjs\Structs\Item $item ): void {
		parent::_integrate( $y, $item );
		if ( '' !== $this->_pending ) {
			$this->insert( 0, $this->_pending );
			$this->_pending = '';
		}
	}

	public function clone(): YText {
		return new YText( $this->toString() );
	}

	public function _callObserver( $transaction, array $parentSubs ): void {
		parent::_callObserver( $transaction, $parentSubs );
		\Yjs\callTypeObservers( $this, $transaction, new \Yjs\Utils\YEvent( $this, $transaction ) );
	}

	public function toString(): string {
		$out = '';
		for ( $item = $this->_start; null !== $item; $item = $item->right ) {
			if ( ! $item->deleted && $item->content instanceof \Yjs\Structs\ContentString ) {
				$out .= $item->content->str;
			}
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
	public function applyDelta( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function toDelta( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	public function insert( int $index, string $text, array $attributes = array() ): void {
		unset( $attributes );
		if ( null !== $this->doc ) {
			\Yjs\transact(
				$this->doc,
				function ( $transaction ) use ( $index, $text ): void {
					\Yjs\typeListInsertText( $transaction, $this, $index, $text );
				}
			);
		} else {
			$this->_pending = substr( $this->_pending, 0, $index ) . $text . substr( $this->_pending, $index );
		}
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function insertEmbed( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	public function delete( int $index, int $length ): void {
		if ( null !== $this->doc ) {
			\Yjs\transact(
				$this->doc,
				function ( $transaction ) use ( $index, $length ): void {
					\Yjs\typeListDelete( $transaction, $this, $index, $length );
				}
			);
		} else {
			$this->_pending = substr( $this->_pending, 0, $index ) . substr( $this->_pending, $index + $length );
		}
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function format( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function removeAttribute( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function setAttribute( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getAttribute( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getAttributes( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed $encoder Encoder.
	 * @return void
	 */
	public function _write( $encoder ): void {
		$encoder->writeTypeRef( 2 );
	}
}
