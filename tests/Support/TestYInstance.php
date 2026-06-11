<?php
/**
 * Translated Yjs TestYInstance stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Tests\Support;

use Yjs\NotImplemented;
use Yjs\Protocols\Sync;
use Yjs\Utils\Doc;

/**
 * PHP port of tests/testHelper.js TestYInstance for the M1 red baseline.
 */
class TestYInstance extends Doc {
	/**
	 * @param TestConnector $testConnector Test connector.
	 * @param int           $clientID      Client id.
	 */
	public function __construct( TestConnector $testConnector, int $clientID ) {
		unset( $testConnector, $clientID );
		parent::__construct();
	}

	/**
	 * @return void
	 */
	public function disconnect(): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		throw new NotImplemented( __METHOD__ . ' is not implemented in y-php milestone 1.' );
	}

	/**
	 * @return void
	 */
	public function connect(): void {
		Sync::writeSyncStep1();
	}

	/**
	 * @param mixed $message      Message bytes.
	 * @param mixed $remoteClient Remote client.
	 * @return void
	 */
	public function _receive( $message, $remoteClient ): void {
		unset( $message, $remoteClient );
		// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		throw new NotImplemented( __METHOD__ . ' is not implemented in y-php milestone 1.' );
	}
}
