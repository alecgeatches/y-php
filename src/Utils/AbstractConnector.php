<?php
/**
 * AbstractConnector public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Utils;

/**
 * AbstractConnector API stub for the Yjs port red baseline.
 */
class AbstractConnector extends \Yjs\Lib0\Observable {
	use \Yjs\NotImplementedTrait;

	/**
	 * @param mixed ...$args Constructor arguments.
	 */
	public function __construct( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @return void
	 */
	public function destroy(): void {
		$this->notImplemented( __METHOD__ );
	}
}
