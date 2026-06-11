<?php
/**
 * AbstractType public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Types;

/**
 * AbstractType API stub for the Yjs port red baseline.
 */
class AbstractType {
	use \Yjs\NotImplementedTrait;

	/**
	 * @var \Yjs\Structs\Item|null
	 */
	public ?\Yjs\Structs\Item $_item = null;

	/**
	 * @var array<string,\Yjs\Structs\Item|null>
	 */
	public array $_map = array();

	/**
	 * @var \Yjs\Structs\Item|null
	 */
	public ?\Yjs\Structs\Item $_start = null;

	/**
	 * @var object|null
	 */
	public ?object $doc = null;

	/**
	 * @var int
	 */
	public int $_length = 0;

	/**
	 * @var mixed
	 */
	public $_eH;

	/**
	 * @var mixed
	 */
	public $_dEH;

	/**
	 * @var array<int,mixed>|null
	 */
	public ?array $_searchMarker = null;

	public function __construct() {}

	/**
	 * @param string $name Property name.
	 * @return mixed
	 */
	public function __get( string $name ) {
		if ( 'parent' === $name ) {
			return null !== $this->_item ? $this->_item->parent : null;
		}
		if ( '_first' === $name ) {
			$item = $this->_start;
			while ( null !== $item && $item->deleted ) {
				$item = $item->right;
			}
			return $item;
		}
		return null;
	}

	/**
	 * @param object                 $y    Y document.
	 * @param \Yjs\Structs\Item|null $item Item.
	 * @return void
	 */
	public function _integrate( object $y, ?\Yjs\Structs\Item $item ): void {
		$this->doc   = $y;
		$this->_item = $item;
	}

	/**
	 * @return AbstractType
	 */
	public function _copy(): AbstractType {
		$this->notImplemented( __METHOD__ );
		return new AbstractType();
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function clone( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed $encoder Encoder.
	 * @return void
	 */
	public function _write( $encoder ): void {
		unset( $encoder );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function _callObserver( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function observe( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function observeDeep( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function unobserve( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function unobserveDeep( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function toJSON( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}
}
