<?php
/**
 * YXmlEvent public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Types;

/**
 * YXmlEvent API stub for the Yjs port red baseline.
 */
class YXmlEvent extends \Yjs\Utils\YEvent {
	use \Yjs\NotImplementedTrait;

	/**
	 * @param mixed ...$args Constructor arguments.
	 */
	public function __construct( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}
}
