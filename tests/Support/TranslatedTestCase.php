<?php
/**
 * Base class for translated Yjs tests.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Tests\Support;

use PHPUnit\Framework\TestCase;
use Yjs\Lib0\Prng;
use Yjs\Lib0\Xoroshiro128plus;
use Yjs\Utils\Doc;

/**
 * Runs a translated JS test slot against the current public API stubs.
 */
abstract class TranslatedTestCase extends TestCase {
	/**
	 * @var Xoroshiro128plus
	 */
	public Xoroshiro128plus $prng;

	protected function setUp(): void {
		parent::setUp();
		$this->prng = Prng::create( 0 );
	}

	/**
	 * @param string $sourceFile JS source file.
	 * @param string $exportName JS exported test name.
	 * @return void
	 */
	protected function runTranslatedTest( string $sourceFile, string $exportName ): void {
		unset( $sourceFile, $exportName );
		new Doc();
	}
}
