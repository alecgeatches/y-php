<?php
/**
 * YTextEvent public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Types;

/**
 * YTextEvent API stub for the Yjs port red baseline.
 */
class YTextEvent extends \Yjs\Utils\YEvent {
	use \Yjs\NotImplementedTrait;

	/**
	 * @param mixed ...$args Constructor arguments.
	 */
	public function __construct( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}
}
