<?php
/**
 * Ported doc.tests.js tests.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Yjs\Lib0\Buffer;
use Yjs\Types\YMap;
use Yjs\Types\YXmlText;
use Yjs\Utils\Doc;

/**
 * Tests ported from yjs/tests/doc.tests.js for the M2.4 runtime surface.
 */
final class DocTest extends TestCase {
	/**
	 * Source: yjs/tests/doc.tests.js::testAfterTransactionRecursion.
	 *
	 * @return void
	 */
	public function testAfterTransactionRecursion(): void {
		$ydoc  = new Doc();
		$yxml  = $ydoc->getXmlFragment( '' );
		$calls = 0;

		$ydoc->on(
			'afterTransaction',
			static function ( $tr ) use ( $yxml, &$calls ): void {
				if ( 'test' === $tr->origin ) {
					$yxml->toJSON();
					++$calls;
				}
			}
		);
		$ydoc->transact(
			static function () use ( $yxml ): void {
				for ( $i = 0; $i < 256; $i++ ) {
					$yxml->push( array( new YXmlText( 'a' ) ) );
				}
			},
			'test'
		);

		self::assertSame( 1, $calls );
	}

	/**
	 * Source: yjs/tests/doc.tests.js::testOriginInTransaction.
	 *
	 * @return void
	 */
	public function testOriginInTransaction(): void {
		self::markTestSkipped( 'Requires YText snapshot/toDelta cleanup transactions from later milestones.' );
	}

	/**
	 * Source: yjs/tests/doc.tests.js::testClientIdDuplicateChange.
	 *
	 * @return void
	 */
	public function testClientIdDuplicateChange(): void {
		$doc1           = new Doc();
		$doc1->clientID = 0;
		$doc2           = new Doc();
		$doc2->clientID = 0;

		self::assertSame( $doc1->clientID, $doc2->clientID );
		$doc1->getArray( 'a' )->insert( 0, array( 1, 2 ) );
		\Yjs\applyUpdate( $doc2, \Yjs\encodeStateAsUpdate( $doc1 ) );

		self::assertNotSame( $doc1->clientID, $doc2->clientID );
	}

	/**
	 * Source: yjs/tests/doc.tests.js::testGetTypeEmptyId.
	 *
	 * @return void
	 */
	public function testGetTypeEmptyId(): void {
		$doc1 = new Doc();
		$doc1->getText( '' )->insert( 0, 'h' );
		$doc1->getText()->insert( 1, 'i' );

		$doc2 = new Doc();
		\Yjs\applyUpdate( $doc2, \Yjs\encodeStateAsUpdate( $doc1 ) );

		self::assertSame( 'hi', $doc2->getText()->toString() );
		self::assertSame( 'hi', $doc2->getText( '' )->toString() );
	}

	/**
	 * Source: yjs/tests/doc.tests.js::testToJSON.
	 *
	 * @return void
	 */
	public function testToJSON(): void {
		$doc = new Doc();
		self::assertEquals( new \stdClass(), $doc->toJSON(), 'doc.toJSON yields empty object' );

		$arr = $doc->getArray( 'array' );
		$arr->push( array( 'test1' ) );

		$map = $doc->getMap( 'map' );
		$map->set( 'k1', 'v1' );
		$map2 = new YMap();
		$map->set( 'k2', $map2 );
		$map2->set( 'm2k1', 'm2v1' );

		$expected = (object) array(
			'array' => array( 'test1' ),
			'map'   => (object) array(
				'k1' => 'v1',
				'k2' => (object) array(
					'm2k1' => 'm2v1',
				),
			),
		);
		self::assertEquals( $expected, $doc->toJSON() );
	}

	/**
	 * Source: yjs/tests/doc.tests.js::testSubdoc.
	 *
	 * @return void
	 */
	public function testSubdoc(): void {
		$doc = new Doc();
		$doc->load();

		$event   = null;
		$toGuids = static function ( array $docs ): array {
			return array_map(
				static fn ( Doc $subdoc ): string => $subdoc->guid,
				$docs
			);
		};
		$doc->on(
			'subdocs',
			static function ( array $subdocs ) use ( &$event, $toGuids ): void {
				$event = array(
					$toGuids( $subdocs['added'] ),
					$toGuids( $subdocs['removed'] ),
					$toGuids( $subdocs['loaded'] ),
				);
			}
		);

		$subdocs = $doc->getMap( 'mysubdocs' );
		$docA    = new Doc( array( 'guid' => 'a' ) );
		$docA->load();
		$subdocs->set( 'a', $docA );
		self::assertSame( array( array( 'a' ), array(), array( 'a' ) ), $event );

		$event = null;
		$subdocs->get( 'a' )->load();
		self::assertNull( $event );

		$event = null;
		$subdocs->get( 'a' )->destroy();
		self::assertSame( array( array( 'a' ), array( 'a' ), array() ), $event );
		$subdocs->get( 'a' )->load();
		self::assertSame( array( array(), array(), array( 'a' ) ), $event );

		$subdocs->set(
			'b',
			new Doc(
				array(
					'guid'       => 'a',
					'shouldLoad' => false,
				)
			)
		);
		self::assertSame( array( array( 'a' ), array(), array() ), $event );
		$subdocs->get( 'b' )->load();
		self::assertSame( array( array(), array(), array( 'a' ) ), $event );

		$docC = new Doc( array( 'guid' => 'c' ) );
		$docC->load();
		$subdocs->set( 'c', $docC );
		self::assertSame( array( array( 'c' ), array(), array( 'c' ) ), $event );
		self::assertSame( array( 'a', 'c' ), $doc->getSubdocGuids() );

		$doc2  = new Doc();
		$event = null;
		self::assertSame( array(), iterator_to_array( $doc2->getSubdocs(), false ) );
		$doc2->on(
			'subdocs',
			static function ( array $subdocs ) use ( &$event, $toGuids ): void {
				$event = array(
					$toGuids( $subdocs['added'] ),
					$toGuids( $subdocs['removed'] ),
					$toGuids( $subdocs['loaded'] ),
				);
			}
		);

		\Yjs\applyUpdate( $doc2, \Yjs\encodeStateAsUpdate( $doc ) );
		self::assertSame( array( array( 'a', 'a', 'c' ), array(), array() ), $event );

		$doc2->getMap( 'mysubdocs' )->get( 'a' )->load();
		self::assertSame( array( array(), array(), array( 'a' ) ), $event );
		self::assertSame( array( 'a', 'c' ), $doc2->getSubdocGuids() );

		$doc2->getMap( 'mysubdocs' )->delete( 'a' );
		self::assertSame( array( array(), array( 'a' ), array() ), $event );
		self::assertSame( array( 'a', 'c' ), $doc2->getSubdocGuids() );
	}

	/**
	 * Source: yjs/tests/doc.tests.js::testSubdocLoadEdgeCases.
	 *
	 * @return void
	 */
	public function testSubdocLoadEdgeCases(): void {
		self::markTestSkipped( 'Full subdocument load/destroy edge cases are completed with later type milestones.' );
	}

	/**
	 * Source: yjs/tests/doc.tests.js::testSubdocLoadEdgeCasesAutoload.
	 *
	 * @return void
	 */
	public function testSubdocLoadEdgeCasesAutoload(): void {
		self::markTestSkipped( 'Full subdocument autoload edge cases are completed with later type milestones.' );
	}

	/**
	 * Source: yjs/tests/doc.tests.js::testSubdocsUndo.
	 *
	 * @return void
	 */
	public function testSubdocsUndo(): void {
		self::markTestSkipped( 'Requires UndoManager and full XML behavior from later milestones.' );
	}

	/**
	 * Source: yjs/tests/doc.tests.js::testLoadDocsEvent.
	 *
	 * @return void
	 */
	public function testLoadDocsEvent(): void {
		$ydoc = new Doc();
		self::assertFalse( $ydoc->isLoaded );
		$loadedEvent = false;
		$ydoc->on(
			'load',
			static function () use ( &$loadedEvent ): void {
				$loadedEvent = true;
			}
		);
		$ydoc->emit( 'load', array( $ydoc ) );

		self::assertTrue( $loadedEvent );
		self::assertTrue( $ydoc->isLoaded );
	}

	/**
	 * Source: yjs/tests/doc.tests.js::testSyncDocsEvent.
	 *
	 * @return void
	 */
	public function testSyncDocsEvent(): void {
		$ydoc = new Doc();
		self::assertFalse( $ydoc->isLoaded );
		self::assertFalse( $ydoc->isSynced );

		$loadedEvent = false;
		$ydoc->once(
			'load',
			static function () use ( &$loadedEvent ): void {
				$loadedEvent = true;
			}
		);
		$syncedEvent = false;
		$ydoc->once(
			'sync',
			static function ( $isSynced ) use ( &$syncedEvent ): void {
				$syncedEvent = true;
				self::assertTrue( $isSynced );
			}
		);
		$ydoc->emit( 'sync', array( true, $ydoc ) );

		self::assertTrue( $loadedEvent );
		self::assertTrue( $syncedEvent );
		self::assertTrue( $ydoc->isLoaded );
		self::assertTrue( $ydoc->isSynced );

		$loadedEvent2 = false;
		$ydoc->on(
			'load',
			static function () use ( &$loadedEvent2 ): void {
				$loadedEvent2 = true;
			}
		);
		$syncedEvent2 = false;
		$ydoc->on(
			'sync',
			static function ( $isSynced ) use ( &$syncedEvent2 ): void {
				$syncedEvent2 = true;
				self::assertFalse( $isSynced );
			}
		);
		$ydoc->emit( 'sync', array( false, $ydoc ) );

		self::assertFalse( $loadedEvent2 );
		self::assertTrue( $syncedEvent2 );
		self::assertTrue( $ydoc->isLoaded );
		self::assertFalse( $ydoc->isSynced );
	}

	/**
	 * @return void
	 */
	public function testJsFixtureUpdatesRoundTripThroughDoc(): void {
		$fixture = $this->fixture( 'yjs-scenarios.json' );
		foreach ( $fixture['scenarios'] as $scenario ) {
			$doc = new Doc();
			\Yjs\applyUpdate( $doc, Buffer::fromHexString( $scenario['updateHex'] ) );

			self::assertSame( $scenario['stateVectorHex'], \Yjs\encodeStateVector( $doc )->toHexString(), $scenario['name'] . ' state vector' );
			self::assertSame( $scenario['updateHex'], \Yjs\encodeStateAsUpdate( $doc )->toHexString(), $scenario['name'] . ' update' );
		}
	}

	/**
	 * @param string $name Fixture file name.
	 * @return array<string,mixed>
	 */
	private function fixture( string $name ): array {
		$path = dirname( __DIR__ ) . '/fixtures/' . $name;
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$data = json_decode( file_get_contents( $path ), true );
		if ( ! is_array( $data ) ) {
			self::fail( 'Unable to read fixture ' . $name );
		}
		return $data;
	}
}
