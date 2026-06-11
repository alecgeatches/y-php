<?php
/**
 * String helpers.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Lib0;

/**
 * Port of the lib0/string.js helpers needed by M0.
 */
final class Str {
	/**
	 * @param int $code Character code.
	 * @return string
	 */
	public static function fromCharCode( int $code ): string {
		return chr( $code );
	}

	/**
	 * @param int $code Code point.
	 * @return string
	 */
	public static function fromCodePoint( int $code ): string {
		if ( $code <= 0x7F ) {
			return chr( $code );
		}
		if ( function_exists( 'mb_chr' ) ) {
			return mb_chr( $code, 'UTF-8' );
		}
		return html_entity_decode( '&#' . $code . ';', ENT_NOQUOTES, 'UTF-8' );
	}

	/**
	 * @param string $string UTF-8 string.
	 * @return int
	 */
	public static function utf8ByteLength( string $string ): int {
		return strlen( $string );
	}

	/**
	 * @param string $string UTF-8 string.
	 * @return Buffer
	 */
	public static function encodeUtf8( string $string ): Buffer {
		return Buffer::fromBinaryString( $string );
	}

	/**
	 * @param Buffer $buffer UTF-8 bytes.
	 * @return string
	 */
	public static function decodeUtf8( Buffer $buffer ): string {
		return $buffer->toBinaryString();
	}

	/**
	 * @param string $source Source string.
	 * @param int    $index  Starting offset.
	 * @param int    $remove Number of bytes to remove.
	 * @param string $insert Inserted string.
	 * @return string
	 */
	public static function splice( string $source, int $index, int $remove, string $insert = '' ): string {
		return substr( $source, 0, $index ) . $insert . substr( $source, $index + $remove );
	}

	/**
	 * @param string $source Source string.
	 * @param int    $count Repeat count.
	 * @return string
	 */
	public static function repeat( string $source, int $count ): string {
		return str_repeat( $source, $count );
	}
}
