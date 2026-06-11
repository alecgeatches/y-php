<?php
/**
 * YEvent public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Utils;

/**
 * YEvent API stub for the Yjs port red baseline.
 */
class YEvent {
	use \Yjs\NotImplementedTrait;

	/**
	 * @param mixed ...$args Constructor arguments.
	 */
	public function __construct( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function deletes( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function adds( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getPath( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}
}
