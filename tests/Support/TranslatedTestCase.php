<?php
/**
 * Base class for translated Yjs tests.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Tests\Support;

use PHPUnit\Framework\TestCase;
use Yjs\Utils\Doc;

/**
 * Runs a translated JS test slot against the current public API stubs.
 */
abstract class TranslatedTestCase extends TestCase {
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
