<?php
/**
 * Translated Yjs TestConnector stub.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Tests\Support;

use Yjs\NotImplemented;
use Yjs\Protocols\Sync;

/**
 * PHP port of tests/testHelper.js TestConnector for the M1 red baseline.
 */
class TestConnector {
	/**
	 * @param mixed $gen PRNG.
	 */
	public function __construct( $gen ) {
		unset( $gen );
	}

	/**
	 * @param int $clientID Client id.
	 * @return TestYInstance
	 */
	public function createY( int $clientID ): TestYInstance {
		return new TestYInstance( $this, $clientID );
	}

	/**
	 * @return void
	 */
	public function flushRandomMessage() {
		Sync::readSyncMessage();
	}

	/**
	 * @return void
	 */
	public function flushAllMessages() {
		Sync::readSyncMessage();
	}

	/**
	 * @return void
	 */
	public function reconnectAll() {
		Sync::writeSyncStep1();
	}

	/**
	 * @return void
	 */
	public function disconnectAll() {
		// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		throw new NotImplemented( __METHOD__ . ' is not implemented in y-php milestone 1.' );
	}

	/**
	 * @return void
	 */
	public function syncAll() {
		Sync::writeSyncStep1();
	}

	/**
	 * @return void
	 */
	public function disconnectRandom() {
		// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		throw new NotImplemented( __METHOD__ . ' is not implemented in y-php milestone 1.' );
	}

	/**
	 * @return void
	 */
	public function reconnectRandom() {
		Sync::writeSyncStep1();
	}
}
