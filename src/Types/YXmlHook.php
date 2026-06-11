<?php
/**
 * YXmlHook public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Types;

/**
 * YXmlHook API stub for the Yjs port red baseline.
 */
class YXmlHook extends YMap {
	use \Yjs\NotImplementedTrait;

	/**
	 * @var string
	 */
	public string $hookName;

	/**
	 * @param string $hookName Hook name.
	 */
	public function __construct( string $hookName ) {
		parent::__construct();
		$this->hookName = $hookName;
	}

	/**
	 * @return YXmlHook
	 */
	public function _copy(): YXmlHook {
		return new YXmlHook( $this->hookName );
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
	public function toDOM( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed $encoder Encoder.
	 * @return void
	 */
	public function _write( $encoder ): void {
		$encoder->writeTypeRef( 5 );
		$encoder->writeKey( $this->hookName );
	}
}
