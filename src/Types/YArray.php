<?php
/**
 * YArray public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Types;

/**
 * YArray API stub for the Yjs port red baseline.
 */
class YArray extends AbstractType implements \IteratorAggregate {
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
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function from( ...$args ) {
		unset( $args );
		static::notImplementedStatic( __METHOD__ );
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
	 * @return YArray
	 */
	public function _copy(): YArray {
		return new YArray();
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
	public function insert( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function push( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function unshift( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function delete( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function get( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function toArray( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
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
	public function toJSON( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function map( ...$args ) {
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
		$encoder->writeTypeRef( 0 );
	}

	/**
	 * @return \Traversable
	 */
	public function getIterator(): \Traversable {
		$this->notImplemented( __METHOD__ );
		return new \EmptyIterator();
	}
}
