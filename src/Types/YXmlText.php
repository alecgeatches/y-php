<?php
/**
 * YXmlText public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Types;

/**
 * YXmlText API stub for the Yjs port red baseline.
 */
class YXmlText extends YText {
	use \Yjs\NotImplementedTrait;

	/**
	 * @return YXmlText
	 */
	public function _copy(): YXmlText {
		return new YXmlText();
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
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function toString( ...$args ) {
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
	 * @param mixed $encoder Encoder.
	 * @return void
	 */
	public function _write( $encoder ): void {
		$encoder->writeTypeRef( 6 );
	}
}
