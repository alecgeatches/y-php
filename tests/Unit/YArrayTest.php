<?php
/**
 * Translated y-array.tests.js tests.
 *
 * @package Yjs
 */

declare(strict_types=1);

namespace Yjs\Tests\Unit;

use Yjs\Tests\Support\TranslatedTestCase;

/**
 * Translated test slots from yjs/tests/y-array.tests.js.
 */
final class YArrayTest extends TranslatedTestCase {
	/**
	 * Source: yjs/tests/y-array.tests.js::testBasicUpdate
	 *
	 * @return void
	 */
	public function testBasicUpdate(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testBasicUpdate' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testFailsObjectManipulationInDevMode
	 *
	 * @return void
	 */
	public function testFailsObjectManipulationInDevMode(): void {
		$this->markTestSkipped( 'JS dev-mode Object.freeze behavior has no PHP array equivalent; see DEC-0015.' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testSlice
	 *
	 * @return void
	 */
	public function testSlice(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testSlice' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testArrayFrom
	 *
	 * @return void
	 */
	public function testArrayFrom(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testArrayFrom' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testLengthIssue
	 *
	 * @return void
	 */
	public function testLengthIssue(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testLengthIssue' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testLengthIssue2
	 *
	 * @return void
	 */
	public function testLengthIssue2(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testLengthIssue2' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testDeleteInsert
	 *
	 * @return void
	 */
	public function testDeleteInsert(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testDeleteInsert' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testInsertThreeElementsTryRegetProperty
	 *
	 * @return void
	 */
	public function testInsertThreeElementsTryRegetProperty(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testInsertThreeElementsTryRegetProperty' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testConcurrentInsertWithThreeConflicts
	 *
	 * @return void
	 */
	public function testConcurrentInsertWithThreeConflicts(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testConcurrentInsertWithThreeConflicts' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testConcurrentInsertDeleteWithThreeConflicts
	 *
	 * @return void
	 */
	public function testConcurrentInsertDeleteWithThreeConflicts(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testConcurrentInsertDeleteWithThreeConflicts' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testInsertionsInLateSync
	 *
	 * @return void
	 */
	public function testInsertionsInLateSync(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testInsertionsInLateSync' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testDisconnectReallyPreventsSendingMessages
	 *
	 * @return void
	 */
	public function testDisconnectReallyPreventsSendingMessages(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testDisconnectReallyPreventsSendingMessages' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testDeletionsInLateSync
	 *
	 * @return void
	 */
	public function testDeletionsInLateSync(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testDeletionsInLateSync' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testInsertThenMergeDeleteOnSync
	 *
	 * @return void
	 */
	public function testInsertThenMergeDeleteOnSync(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testInsertThenMergeDeleteOnSync' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testInsertAndDeleteEvents
	 *
	 * @return void
	 */
	public function testInsertAndDeleteEvents(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testInsertAndDeleteEvents' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testNestedObserverEvents
	 *
	 * @return void
	 */
	public function testNestedObserverEvents(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testNestedObserverEvents' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testInsertAndDeleteEventsForTypes
	 *
	 * @return void
	 */
	public function testInsertAndDeleteEventsForTypes(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testInsertAndDeleteEventsForTypes' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testObserveDeepEventOrder
	 *
	 * @return void
	 */
	public function testObserveDeepEventOrder(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testObserveDeepEventOrder' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testObservedeepIndexes
	 *
	 * @return void
	 */
	public function testObservedeepIndexes(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testObservedeepIndexes' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testChangeEvent
	 *
	 * @return void
	 */
	public function testChangeEvent(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testChangeEvent' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testInsertAndDeleteEventsForTypes2
	 *
	 * @return void
	 */
	public function testInsertAndDeleteEventsForTypes2(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testInsertAndDeleteEventsForTypes2' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testNewChildDoesNotEmitEventInTransaction
	 *
	 * @return void
	 */
	public function testNewChildDoesNotEmitEventInTransaction(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testNewChildDoesNotEmitEventInTransaction' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testGarbageCollector
	 *
	 * @return void
	 */
	public function testGarbageCollector(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testGarbageCollector' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testEventTargetIsSetCorrectlyOnLocal
	 *
	 * @return void
	 */
	public function testEventTargetIsSetCorrectlyOnLocal(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testEventTargetIsSetCorrectlyOnLocal' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testEventTargetIsSetCorrectlyOnRemote
	 *
	 * @return void
	 */
	public function testEventTargetIsSetCorrectlyOnRemote(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testEventTargetIsSetCorrectlyOnRemote' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testIteratingArrayContainingTypes
	 *
	 * @return void
	 */
	public function testIteratingArrayContainingTypes(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testIteratingArrayContainingTypes' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests6
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests6(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests6' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests40
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests40(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests40' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests42
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests42(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests42' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests43
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests43(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests43' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests44
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests44(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests44' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests45
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests45(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests45' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests46
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests46(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests46' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests300
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests300(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests300' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests400
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests400(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests400' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests500
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests500(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests500' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests600
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests600(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests600' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests1000
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests1000(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests1000' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests1800
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests1800(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests1800' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests3000
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests3000(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests3000' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests5000
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests5000(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests5000' );
	}

	/**
	 * Source: yjs/tests/y-array.tests.js::testRepeatGeneratingYarrayTests30000
	 *
	 * @return void
	 */
	public function testRepeatGeneratingYarrayTests30000(): void {
		$this->runTranslatedTest( 'y-array.tests.js', 'testRepeatGeneratingYarrayTests30000' );
	}
}
