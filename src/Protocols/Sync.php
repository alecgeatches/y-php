<?php
/**
 * Sync protocol stubs.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Protocols;

use Yjs\NotImplemented;

/**
 * Subset of y-protocols/sync required by the translated test helper.
 */
final class Sync {
	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function writeSyncStep1( ...$args ) {
		unset( $args );
		// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		throw new NotImplemented( __METHOD__ . ' is not implemented in y-php milestone 1.' );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function readSyncMessage( ...$args ) {
		unset( $args );
		// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		throw new NotImplemented( __METHOD__ . ' is not implemented in y-php milestone 1.' );
	}

	/**
	 * @param mixed ...$args Arguments.
	 * @return void
	 */
	public static function writeUpdate( ...$args ) {
		unset( $args );
		// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		throw new NotImplemented( __METHOD__ . ' is not implemented in y-php milestone 1.' );
	}
}
