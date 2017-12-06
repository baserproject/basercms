<?php
/**
 * CakePHP : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright	  Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link		  http://cakephp.org CakePHP Project
 * @package		  Cake.Test.Case.Event
 * @since		  CakePHP v 2.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('CakeEvent', 'Event');
App::uses('CakeEventManager', 'Event');
App::uses('CakeEventListener', 'Event');

/**
 * Mock class used to test event dispatching
 *
 * @package Cake.Test.Case.Event
 */
class CakeEventTestListener {

	public $callStack = [];

/**
 * Test function to be used in event dispatching
 *
 * @return void
 */
	public function listenerFunction() {
		$this->callStack[] = __FUNCTION__;
	}

/**
 * Test function to be used in event dispatching
 *
 * @return void
 */
	public function secondListenerFunction() {
		$this->callStack[] = __FUNCTION__;
	}

/**
 * Auxiliary function to help in stopPropagation testing
 *
 * @param CakeEvent $event
 * @return void
 */
	public function stopListener($event) {
		$event->stopPropagation();
	}

}

/**
 * Mock used for testing the subscriber objects
 *
 * @package Cake.Test.Case.Event
 */
class CustomTestEventListener extends CakeEventTestListener implements CakeEventListener {

	public function implementedEvents() {
		return [
			'fake.event' => 'listenerFunction',
			'another.event' => ['callable' => 'secondListenerFunction', 'passParams' => true],
			'multiple.handlers' => [
				['callable' => 'listenerFunction'],
				['callable' => 'thirdListenerFunction']
			]
		];
	}

/**
 * Test function to be used in event dispatching
 *
 * @return void
 */
	public function thirdListenerFunction() {
		$this->callStack[] = __FUNCTION__;
	}

}

/**
 * Tests the CakeEventManager class functionality
 */
class CakeEventManagerTest extends CakeTestCase {

/**
 * Tests the attach() method for a single event key in multiple queues
 *
 * @return void
 */
	public function testAttachListeners() {
		$manager = new CakeEventManager();
		$manager->attach('fakeFunction', 'fake.event');
		$expected = [
			['callable' => 'fakeFunction', 'passParams' => false]
		];
		$this->assertEquals($expected, $manager->listeners('fake.event'));

		$manager->attach('fakeFunction2', 'fake.event');
		$expected[] = ['callable' => 'fakeFunction2', 'passParams' => false];
		$this->assertEquals($expected, $manager->listeners('fake.event'));

		$manager->attach('inQ5', 'fake.event', ['priority' => 5]);
		$manager->attach('inQ1', 'fake.event', ['priority' => 1]);
		$manager->attach('otherInQ5', 'fake.event', ['priority' => 5]);

		$expected = array_merge(
			[
				['callable' => 'inQ1', 'passParams' => false],
				['callable' => 'inQ5', 'passParams' => false],
				['callable' => 'otherInQ5', 'passParams' => false]
			],
			$expected
		);
		$this->assertEquals($expected, $manager->listeners('fake.event'));
	}

/**
 * Tests the attach() method for multiple event key in multiple queues
 *
 * @return void
 */
	public function testAttachMultipleEventKeys() {
		$manager = new CakeEventManager();
		$manager->attach('fakeFunction', 'fake.event');
		$manager->attach('fakeFunction2', 'another.event');
		$manager->attach('fakeFunction3', 'another.event', ['priority' => 1, 'passParams' => true]);
		$expected = [
			['callable' => 'fakeFunction', 'passParams' => false]
		];
		$this->assertEquals($expected, $manager->listeners('fake.event'));

		$expected = [
			['callable' => 'fakeFunction3', 'passParams' => true],
			['callable' => 'fakeFunction2', 'passParams' => false]
		];
		$this->assertEquals($expected, $manager->listeners('another.event'));
	}

/**
 * Tests detaching an event from a event key queue
 *
 * @return void
 */
	public function testDetach() {
		$manager = new CakeEventManager();
		$manager->attach(['AClass', 'aMethod'], 'fake.event');
		$manager->attach(['AClass', 'anotherMethod'], 'another.event');
		$manager->attach('fakeFunction', 'another.event', ['priority' => 1]);

		$manager->detach(['AClass', 'aMethod'], 'fake.event');
		$this->assertEquals([], $manager->listeners('fake.event'));

		$manager->detach(['AClass', 'anotherMethod'], 'another.event');
		$expected = [
			['callable' => 'fakeFunction', 'passParams' => false]
		];
		$this->assertEquals($expected, $manager->listeners('another.event'));

		$manager->detach('fakeFunction', 'another.event');
		$this->assertEquals([], $manager->listeners('another.event'));
	}

/**
 * Tests detaching an event from all event queues
 *
 * @return void
 */
	public function testDetachFromAll() {
		$manager = new CakeEventManager();
		$manager->attach(['AClass', 'aMethod'], 'fake.event');
		$manager->attach(['AClass', 'aMethod'], 'another.event');
		$manager->attach('fakeFunction', 'another.event', ['priority' => 1]);

		$manager->detach(['AClass', 'aMethod']);
		$expected = [
			['callable' => 'fakeFunction', 'passParams' => false]
		];
		$this->assertEquals($expected, $manager->listeners('another.event'));
		$this->assertEquals([], $manager->listeners('fake.event'));
	}

/**
 * Tests event dispatching
 *
 * @return void
 * @triggers fake.event
 */
	public function testDispatch() {
		$manager = new CakeEventManager();
		$listener = $this->getMock('CakeEventTestListener');
		$anotherListener = $this->getMock('CakeEventTestListener');
		$manager->attach([$listener, 'listenerFunction'], 'fake.event');
		$manager->attach([$anotherListener, 'listenerFunction'], 'fake.event');
		$event = new CakeEvent('fake.event');

		$listener->expects($this->once())->method('listenerFunction')->with($event);
		$anotherListener->expects($this->once())->method('listenerFunction')->with($event);
		$manager->dispatch($event);
	}

/**
 * Tests event dispatching using event key name
 *
 * @return void
 */
	public function testDispatchWithKeyName() {
		$manager = new CakeEventManager();
		$listener = new CakeEventTestListener();
		$manager->attach([$listener, 'listenerFunction'], 'fake.event');
		$event = 'fake.event';
		$manager->dispatch($event);

		$expected = ['listenerFunction'];
		$this->assertEquals($expected, $listener->callStack);
	}

/**
 * Tests event dispatching with a return value
 *
 * @return void
 * @triggers fake.event
 */
	public function testDispatchReturnValue() {
		$this->skipIf(
			version_compare(PHPUnit_Runner_Version::id(), '3.7', '<'),
			'These tests fail in PHPUnit 3.6'
		);
		$manager = new CakeEventManager();
		$listener = $this->getMock('CakeEventTestListener');
		$anotherListener = $this->getMock('CakeEventTestListener');
		$manager->attach([$listener, 'listenerFunction'], 'fake.event');
		$manager->attach([$anotherListener, 'listenerFunction'], 'fake.event');
		$event = new CakeEvent('fake.event');

		$listener->expects($this->at(0))->method('listenerFunction')
			->with($event)
			->will($this->returnValue('something special'));
		$anotherListener->expects($this->at(0))
			->method('listenerFunction')
			->with($event);
		$manager->dispatch($event);
		$this->assertEquals('something special', $event->result);
	}

/**
 * Tests that returning false in a callback stops the event
 *
 * @return void
 * @triggers fake.event
 */
	public function testDispatchFalseStopsEvent() {
		$this->skipIf(
			version_compare(PHPUnit_Runner_Version::id(), '3.7', '<'),
			'These tests fail in PHPUnit 3.6'
		);

		$manager = new CakeEventManager();
		$listener = $this->getMock('CakeEventTestListener');
		$anotherListener = $this->getMock('CakeEventTestListener');
		$manager->attach([$listener, 'listenerFunction'], 'fake.event');
		$manager->attach([$anotherListener, 'listenerFunction'], 'fake.event');
		$event = new CakeEvent('fake.event');

		$listener->expects($this->at(0))->method('listenerFunction')
			->with($event)
			->will($this->returnValue(false));
		$anotherListener->expects($this->never())
			->method('listenerFunction');
		$manager->dispatch($event);
		$this->assertTrue($event->isStopped());
	}

/**
 * Tests event dispatching using priorities
 *
 * @return void
 * @triggers fake.event
 */
	public function testDispatchPrioritized() {
		$manager = new CakeEventManager();
		$listener = new CakeEventTestListener();
		$manager->attach([$listener, 'listenerFunction'], 'fake.event');
		$manager->attach([$listener, 'secondListenerFunction'], 'fake.event', ['priority' => 5]);
		$event = new CakeEvent('fake.event');
		$manager->dispatch($event);

		$expected = ['secondListenerFunction', 'listenerFunction'];
		$this->assertEquals($expected, $listener->callStack);
	}

/**
 * Tests event dispatching with passed params
 *
 * @return void
 * @triggers fake.event $this, array('some' => 'data')
 */
	public function testDispatchPassingParams() {
		$manager = new CakeEventManager();
		$listener = $this->getMock('CakeEventTestListener');
		$anotherListener = $this->getMock('CakeEventTestListener');
		$manager->attach([$listener, 'listenerFunction'], 'fake.event');
		$manager->attach([$anotherListener, 'secondListenerFunction'], 'fake.event', ['passParams' => true]);
		$event = new CakeEvent('fake.event', $this, ['some' => 'data']);

		$listener->expects($this->once())->method('listenerFunction')->with($event);
		$anotherListener->expects($this->once())->method('secondListenerFunction')->with('data');
		$manager->dispatch($event);
	}

/**
 * Tests subscribing a listener object and firing the events it subscribed to
 *
 * @return void
 * @triggers fake.event
 * @triggers another.event $this, array('some' => 'data')
 * @triggers multiple.handlers
 */
	public function testAttachSubscriber() {
		$manager = new CakeEventManager();
		$listener = $this->getMock('CustomTestEventListener', ['secondListenerFunction']);
		$manager->attach($listener);
		$event = new CakeEvent('fake.event');

		$manager->dispatch($event);

		$expected = ['listenerFunction'];
		$this->assertEquals($expected, $listener->callStack);

		$listener->expects($this->at(0))->method('secondListenerFunction')->with('data');
		$event = new CakeEvent('another.event', $this, ['some' => 'data']);
		$manager->dispatch($event);

		$manager = new CakeEventManager();
		$listener = $this->getMock('CustomTestEventListener', ['listenerFunction', 'thirdListenerFunction']);
		$manager->attach($listener);
		$event = new CakeEvent('multiple.handlers');
		$listener->expects($this->once())->method('listenerFunction')->with($event);
		$listener->expects($this->once())->method('thirdListenerFunction')->with($event);
		$manager->dispatch($event);
	}

/**
 * Tests subscribing a listener object and firing the events it subscribed to
 *
 * @return void
 */
	public function testDetachSubscriber() {
		$manager = new CakeEventManager();
		$listener = $this->getMock('CustomTestEventListener', ['secondListenerFunction']);
		$manager->attach($listener);
		$expected = [
			['callable' => [$listener, 'secondListenerFunction'], 'passParams' => true]
		];
		$this->assertEquals($expected, $manager->listeners('another.event'));
		$expected = [
			['callable' => [$listener, 'listenerFunction'], 'passParams' => false]
		];
		$this->assertEquals($expected, $manager->listeners('fake.event'));
		$manager->detach($listener);
		$this->assertEquals([], $manager->listeners('fake.event'));
		$this->assertEquals([], $manager->listeners('another.event'));
	}

/**
 * Tests that it is possible to get/set the manager singleton
 *
 * @return void
 */
	public function testGlobalDispatcherGetter() {
		$this->assertInstanceOf('CakeEventManager', CakeEventManager::instance());
		$manager = new CakeEventManager();

		CakeEventManager::instance($manager);
		$this->assertSame($manager, CakeEventManager::instance());
	}

/**
 * Tests that the global event manager gets the event too from any other manager
 *
 * @return void
 * @triggers fake.event
 */
	public function testDispatchWithGlobal() {
		$generalManager = $this->getMock('CakeEventManager', ['prioritisedListeners']);
		$manager = new CakeEventManager();
		$event = new CakeEvent('fake.event');
		CakeEventManager::instance($generalManager);

		$generalManager->expects($this->once())->method('prioritisedListeners')->with('fake.event');
		$manager->dispatch($event);
		CakeEventManager::instance(new CakeEventManager());
	}

/**
 * Tests that stopping an event will not notify the rest of the listeners
 *
 * @return void
 * @triggers fake.event
 */
	public function testStopPropagation() {
		$generalManager = $this->getMock('CakeEventManager');
		$manager = new CakeEventManager();
		$listener = new CakeEventTestListener();

		CakeEventManager::instance($generalManager);
		$generalManager->expects($this->any())
				->method('prioritisedListeners')
				->with('fake.event')
				->will($this->returnValue([]));

		$manager->attach([$listener, 'listenerFunction'], 'fake.event');
		$manager->attach([$listener, 'stopListener'], 'fake.event', ['priority' => 8]);
		$manager->attach([$listener, 'secondListenerFunction'], 'fake.event', ['priority' => 5]);
		$event = new CakeEvent('fake.event');
		$manager->dispatch($event);

		$expected = ['secondListenerFunction'];
		$this->assertEquals($expected, $listener->callStack);
		CakeEventManager::instance(new CakeEventManager());
	}

/**
 * Tests event dispatching using priorities
 *
 * @return void
 * @triggers fake.event
 */
	public function testDispatchPrioritizedWithGlobal() {
		$generalManager = $this->getMock('CakeEventManager');
		$manager = new CakeEventManager();
		$listener = new CustomTestEventListener();
		$event = new CakeEvent('fake.event');

		CakeEventManager::instance($generalManager);
		$generalManager->expects($this->any())
				->method('prioritisedListeners')
				->with('fake.event')
				->will($this->returnValue(
					[11 => [
						['callable' => [$listener, 'secondListenerFunction'], 'passParams' => false]
					]]
				));

		$manager->attach([$listener, 'listenerFunction'], 'fake.event');
		$manager->attach([$listener, 'thirdListenerFunction'], 'fake.event', ['priority' => 15]);

		$manager->dispatch($event);

		$expected = ['listenerFunction', 'secondListenerFunction', 'thirdListenerFunction'];
		$this->assertEquals($expected, $listener->callStack);
		CakeEventManager::instance(new CakeEventManager());
	}

/**
 * Tests event dispatching using priorities
 *
 * @return void
 * @triggers fake.event
 */
	public function testDispatchGlobalBeforeLocal() {
		$generalManager = $this->getMock('CakeEventManager');
		$manager = new CakeEventManager();
		$listener = new CustomTestEventListener();
		$event = new CakeEvent('fake.event');

		CakeEventManager::instance($generalManager);
		$generalManager->expects($this->any())
				->method('prioritisedListeners')
				->with('fake.event')
				->will($this->returnValue(
					[10 => [
						['callable' => [$listener, 'listenerFunction'], 'passParams' => false]
					]]
				));

		$manager->attach([$listener, 'secondListenerFunction'], 'fake.event');

		$manager->dispatch($event);

		$expected = ['listenerFunction', 'secondListenerFunction'];
		$this->assertEquals($expected, $listener->callStack);
		CakeEventManager::instance(new CakeEventManager());
	}

/**
 * test callback
 */
	public function onMyEvent($event) {
		$event->data['callback'] = 'ok';
	}

/**
 * Tests events dispatched by a local manager can be handled by
 * handler registered in the global event manager
 * @triggers my_event $manager
 */
	public function testDispatchLocalHandledByGlobal() {
		$callback = [$this, 'onMyEvent'];
		CakeEventManager::instance()->attach($callback, 'my_event');
		$manager = new CakeEventManager();
		$event = new CakeEvent('my_event', $manager);
		$manager->dispatch($event);
		$this->assertEquals('ok', $event->data['callback']);
	}

/**
 * Test that events are dispatched properly when there are global and local
 * listeners at the same priority.
 *
 * @return void
 * @triggers fake.event $this
 */
	public function testDispatchWithGlobalAndLocalEvents() {
		$listener = new CustomTestEventListener();
		CakeEventManager::instance()->attach($listener);
		$listener2 = new CakeEventTestListener();
		$manager = new CakeEventManager();
		$manager->attach([$listener2, 'listenerFunction'], 'fake.event');

		$manager->dispatch(new CakeEvent('fake.event', $this));
		$this->assertEquals(['listenerFunction'], $listener->callStack);
		$this->assertEquals(['listenerFunction'], $listener2->callStack);
	}

}
