<?php
/**
 * Shared not-implemented helpers.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs;

/**
 * Throws the common milestone exception from stubbed APIs.
 */
trait NotImplementedTrait {
	/**
	 * @param string $symbol API symbol.
	 * @return void
	 *
	 * @throws NotImplemented Always thrown for M1 stubs.
	 */
	protected function notImplemented( string $symbol ): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		throw new NotImplemented( $symbol . ' is not implemented in y-php milestone 1.' );
	}

	/**
	 * @param string $symbol API symbol.
	 * @return void
	 *
	 * @throws NotImplemented Always thrown for M1 stubs.
	 */
	protected static function notImplementedStatic( string $symbol ): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		throw new NotImplemented( $symbol . ' is not implemented in y-php milestone 1.' );
	}

	/**
	 * @param string           $name      Method name.
	 * @param array<int,mixed> $arguments Arguments.
	 * @return void
	 *
	 * @throws NotImplemented Always thrown for M1 stubs.
	 */
	public function __call( string $name, array $arguments ) {
		unset( $arguments );
		$this->notImplemented( static::class . '::' . $name );
	}

	/**
	 * @param string           $name      Method name.
	 * @param array<int,mixed> $arguments Arguments.
	 * @return void
	 *
	 * @throws NotImplemented Always thrown for M1 stubs.
	 */
	public static function __callStatic( string $name, array $arguments ) {
		unset( $arguments );
		static::notImplementedStatic( static::class . '::' . $name );
	}
}
