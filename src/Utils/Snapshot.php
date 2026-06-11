<?php
/**
 * Snapshot public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Utils;

/**
 * Snapshot API stub for the Yjs port red baseline.
 */
class Snapshot {
	use \Yjs\NotImplementedTrait;

	/**
	 * @param mixed ...$args Constructor arguments.
	 */
	public function __construct( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}
}
