<?php
/**
 * Translated Yjs test helper function stubs.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Tests\Support;

use Yjs\NotImplemented;

/**
 * @param mixed             $tc             Test case.
 * @param array<string,int> $conf           Configuration.
 * @param callable|null     $initTestObject Object initializer.
 * @return void
 */
function init( $tc, array $conf = array(), ?callable $initTestObject = null ) {
	unset( $tc, $conf, $initTestObject );
	// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param array<int,mixed> $users Users.
 * @return void
 */
function compare( array $users ): void {
	unset( $users );
	// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}

/**
 * @param mixed            $tc             Test case.
 * @param array<int,mixed> $mods           Random modifications.
 * @param int              $iterations     Iteration count.
 * @param callable|null    $initTestObject Object initializer.
 * @return void
 */
function applyRandomTests( $tc, array $mods, int $iterations, ?callable $initTestObject = null ) {
	unset( $tc, $mods, $iterations, $initTestObject );
	// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
	throw new NotImplemented( __FUNCTION__ . ' is not implemented in y-php milestone 1.' );
}
