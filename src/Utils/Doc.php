<?php
/**
 * Doc public API stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Utils;

/**
 * Doc API stub for the Yjs port red baseline.
 */
class Doc extends \Yjs\Lib0\Observable {
	use \Yjs\NotImplementedTrait;

	/**
	 * @var bool
	 */
	public bool $gc;

	/**
	 * @var callable
	 */
	public $gcFilter;

	/**
	 * @var int
	 */
	public int $clientID;

	/**
	 * @var string
	 */
	public string $guid;

	/**
	 * @var string|null
	 */
	public ?string $collectionid;

	/**
	 * @var array<string,\Yjs\Types\AbstractType>
	 */
	public array $share = array();

	/**
	 * @var StructStore
	 */
	public StructStore $store;

	/**
	 * @var mixed
	 */
	public $_transaction = null;

	/**
	 * @var array<int,mixed>
	 */
	public array $_transactionCleanups = array();

	/**
	 * @var array<int,Doc>
	 */
	public array $subdocs = array();

	/**
	 * @var \Yjs\Structs\Item|null
	 */
	public ?\Yjs\Structs\Item $_item = null;

	/**
	 * @var bool
	 */
	public bool $shouldLoad;

	/**
	 * @var bool
	 */
	public bool $autoLoad;

	/**
	 * @var mixed
	 */
	public $meta;

	/**
	 * @var bool
	 */
	public bool $isLoaded = false;

	/**
	 * @var bool
	 */
	public bool $isSynced = false;

	/**
	 * @var bool
	 */
	public bool $isDestroyed = false;

	/**
	 * @param array|\stdClass|null $opts Configuration options.
	 */
	public function __construct( $opts = null ) {
		$options = $this->normalizeOpts( $opts );

		$this->gc           = $options['gc'] ?? true;
		$this->gcFilter     = $options['gcFilter'] ?? static fn (): bool => true;
		$this->clientID     = \Yjs\Lib0\Random::uint32();
		$this->guid         = isset( $options['guid'] ) ? (string) $options['guid'] : self::randomGuid();
		$this->collectionid = isset( $options['collectionid'] ) ? (string) $options['collectionid'] : null;
		$this->store        = new StructStore();
		$this->shouldLoad   = $options['shouldLoad'] ?? true;
		$this->autoLoad     = $options['autoLoad'] ?? false;
		$this->meta         = $options['meta'] ?? null;
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function load( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getSubdocs( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getSubdocGuids( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function transact( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function get( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getArray( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getText( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getMap( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getXmlElement( ...$args ) {
		unset( $args );
		$this->notImplemented( __METHOD__ );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public function getXmlFragment( ...$args ) {
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
	 * @return void
	 */
	public function destroy(): void {
		$this->isDestroyed = true;
		parent::destroy();
	}

	/**
	 * @param array|\stdClass|null $opts Options.
	 * @return array<string,mixed>
	 */
	private function normalizeOpts( $opts ): array {
		if ( null === $opts ) {
			return array();
		}
		if ( $opts instanceof \stdClass ) {
			return get_object_vars( $opts );
		}
		if ( is_array( $opts ) ) {
			return $opts;
		}
		return array();
	}

	/**
	 * @return string
	 */
	private static function randomGuid(): string {
		return sprintf(
			'%08x-%04x-%04x-%04x-%012x',
			random_int( 0, 0xFFFFFFFF ),
			random_int( 0, 0xFFFF ),
			random_int( 0, 0xFFFF ),
			random_int( 0, 0xFFFF ),
			random_int( 0, 0xFFFFFFFFFFFF )
		);
	}
}
