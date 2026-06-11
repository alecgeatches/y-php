<?php
/**
 * YXmlElement public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Types;

/**
 * YXmlElement API stub for the Yjs port red baseline.
 */
class YXmlElement extends YXmlFragment {
	use \Yjs\NotImplementedTrait;

	/**
	 * @var string
	 */
	public string $nodeName;

	/**
	 * @var array<string,mixed>|null
	 */
	public ?array $_prelimAttrs;

	/**
	 * @param string $nodeName Node name.
	 */
	public function __construct( string $nodeName = 'UNDEFINED' ) {
		parent::__construct();
		$this->nodeName     = $nodeName;
		$this->_prelimAttrs = array();
	}

	/**
	 * @param object                 $y    Y document.
	 * @param \Yjs\Structs\Item|null $item Item.
	 * @return void
	 */
	public function _integrate( object $y, ?\Yjs\Structs\Item $item ): void {
		parent::_integrate( $y, $item );
		$this->_prelimAttrs = null;
	}

	/**
	 * @return YXmlElement
	 */
	public function _copy(): YXmlElement {
		return new YXmlElement( $this->nodeName );
	}

	/**
	 * @return YXmlElement
	 */
	public function clone(): YXmlElement {
		$element = new YXmlElement( $this->nodeName );
		$element->insert(
			0,
			array_map(
				static fn ( $el ) => $el instanceof AbstractType ? $el->clone() : $el,
				$this->toArray()
			)
		);
		return $element;
	}

	public function toString(): string {
		return '<' . $this->nodeName . '>' . parent::toString() . '</' . $this->nodeName . '>';
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function removeAttribute( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function setAttribute( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getAttribute( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function hasAttribute( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getAttributes( ...$args ) {
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
		$encoder->writeTypeRef( 3 );
		$encoder->writeKey( $this->nodeName );
	}
}
