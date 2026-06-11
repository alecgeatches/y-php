<?php
/**
 * AbstractStruct public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Structs;

/**
 * AbstractStruct API stub for the Yjs port red baseline.
 */
class AbstractStruct {
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
	public function mergeWith( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function write( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function integrate( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}
}
