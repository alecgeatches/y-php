<?php
/**
 * YEvent public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Utils;

/**
 * Event object describing changes on a Yjs type.
 */
class YEvent {
	/**
	 * @var \Yjs\Types\AbstractType
	 */
	public \Yjs\Types\AbstractType $target;

	/**
	 * @var \Yjs\Types\AbstractType
	 */
	public \Yjs\Types\AbstractType $currentTarget;

	/**
	 * @var Transaction
	 */
	public Transaction $transaction;

	/**
	 * @var mixed
	 */
	public $_changes = null;

	/**
	 * @var mixed
	 */
	public $_keys = null;

	/**
	 * @var mixed
	 */
	public $_delta = null;

	/**
	 * @var array<int,string|int>|null
	 */
	public ?array $_path = null;

	/**
	 * @param \Yjs\Types\AbstractType $target      Changed type.
	 * @param Transaction             $transaction Transaction.
	 */
	public function __construct( \Yjs\Types\AbstractType $target, Transaction $transaction ) {
		$this->target        = $target;
		$this->currentTarget = $target;
		$this->transaction   = $transaction;
	}

	/**
	 * @param string $name Property name.
	 * @return mixed
	 */
	public function __get( string $name ) {
		if ( 'path' === $name ) {
			if ( null === $this->_path ) {
				$this->_path = \Yjs\getPathTo( $this->currentTarget, $this->target );
			}
			return $this->_path;
		}
		if ( 'delta' === $name ) {
			$changes = $this->__get( 'changes' );
			return $changes['delta'];
		}
		if ( 'changes' === $name ) {
			return $this->computeChanges();
		}
		if ( 'keys' === $name ) {
			return $this->computeKeys();
		}
		return null;
	}

	/**
	 * @param \Yjs\Structs\AbstractStruct $struct Struct.
	 * @return bool
	 */
	public function deletes( \Yjs\Structs\AbstractStruct $struct ): bool {
		return \Yjs\isDeleted( $this->transaction->deleteSet, $struct->id );
	}

	/**
	 * @param \Yjs\Structs\AbstractStruct $struct Struct.
	 * @return bool
	 */
	public function adds( \Yjs\Structs\AbstractStruct $struct ): bool {
		return $struct->id->clock >= ( $this->transaction->beforeState[ $struct->id->client ] ?? 0 );
	}

	/**
	 * @return array<int,string|int>
	 */
	public function getPath(): array {
		return $this->__get( 'path' );
	}

	/**
	 * @return array<string,mixed>
	 */
	private function computeChanges(): array {
		if ( null === $this->_changes ) {
			$this->_changes = array(
				'added'   => array(),
				'deleted' => array(),
				'delta'   => array(),
				'keys'    => $this->computeKeys(),
			);
		}
		return $this->_changes;
	}

	/**
	 * @return array<string,array<string,mixed>>
	 */
	private function computeKeys(): array {
		if ( null === $this->_keys ) {
			$this->_keys = array();
		}
		return $this->_keys;
	}
}
