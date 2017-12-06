<?php
/**
 * ObjectCollectionTest file
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.Test.Case.Utility
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('ObjectCollection', 'Utility');
App::uses('CakeEvent', 'Event');

/**
 * A generic object class
 */
class GenericObject {

/**
 * Constructor
 *
 * @param GenericObjectCollection $collection A collection.
 * @param array $settings Settings.
 */
	public function __construct(GenericObjectCollection $collection, $settings = []) {
		$this->_Collection = $collection;
		$this->settings = $settings;
	}

}

/**
 * First Extension of Generic Object
 */
class FirstGenericObject extends GenericObject {

/**
 * A generic callback
 *
 * @return void
 */
	public function callback() {
	}

}

/**
 * Second Extension of Generic Object
 */
class SecondGenericObject extends GenericObject {

/**
 * @return void
 */
	public function callback() {
	}

}

/**
 * Third Extension of Generic Object
 */
class ThirdGenericObject extends GenericObject {

/**
 * @return void
 */
	public function callback() {
	}

}

/**
 * A collection of Generic objects
 */
class GenericObjectCollection extends ObjectCollection {

/**
 * Loads a generic object
 *
 * @param string $object Object name
 * @param array $settings Settings array
 * @return array List of loaded objects
 */
	public function load($object, $settings = []) {
		list(, $name) = pluginSplit($object);
		if (isset($this->_loaded[$name])) {
			return $this->_loaded[$name];
		}
		$objectClass = $name . 'GenericObject';
		$this->_loaded[$name] = new $objectClass($this, $settings);
		$enable = isset($settings['enabled']) ? $settings['enabled'] : true;
		if ($enable === true) {
			$this->enable($name);
		}
		return $this->_loaded[$name];
	}

/**
 * Helper method for adding/overwriting enabled objects including
 * settings
 *
 * @param string $name Name of the object
 * @param Object $object The object to use
 * @param array $settings Settings to apply for the object
 * @return array Loaded objects
 */
	public function setObject($name, $object, $settings = []) {
		$this->_loaded[$name] = $object;
		if (isset($settings['priority'])) {
			$this->setPriority($name, $settings['priority']);
		}
		$enable = isset($settings['enabled']) ? $settings['enabled'] : true;
		if ($enable === true) {
			$this->enable($name);
		}
		return $this->_loaded;
	}

}

class ObjectCollectionTest extends CakeTestCase {

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Objects = new GenericObjectCollection();
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Objects);
	}

/**
 * test triggering callbacks on loaded helpers
 *
 * @return void
 */
	public function testLoad() {
		$result = $this->Objects->load('First');
		$this->assertInstanceOf('FirstGenericObject', $result);
		$this->assertInstanceOf('FirstGenericObject', $this->Objects->First);

		$result = $this->Objects->loaded();
		$this->assertEquals(['First'], $result, 'loaded() results are wrong.');

		$this->assertTrue($this->Objects->enabled('First'));

		$result = $this->Objects->load('First');
		$this->assertSame($result, $this->Objects->First);
	}

/**
 * test unload()
 *
 * @return void
 */
	public function testUnload() {
		$this->Objects->load('First');
		$this->Objects->load('Second');

		$result = $this->Objects->loaded();
		$this->assertEquals(['First', 'Second'], $result, 'loaded objects are wrong');

		$this->Objects->unload('First');
		$this->assertFalse(isset($this->Objects->First));
		$this->assertTrue(isset($this->Objects->Second));

		$result = $this->Objects->loaded();
		$this->assertEquals(['Second'], $result, 'loaded objects are wrong');

		$result = $this->Objects->loaded();
		$this->assertEquals(['Second'], $result, 'enabled objects are wrong');
	}

/**
 * Tests set()
 *
 * @return void
 */
	public function testSet() {
		$this->Objects->load('First');

		$result = $this->Objects->loaded();
		$this->assertEquals(['First'], $result, 'loaded objects are wrong');

		$result = $this->Objects->set('First', new SecondGenericObject($this->Objects));
		$this->assertInstanceOf('SecondGenericObject', $result['First'], 'set failed');

		$result = $this->Objects->set('Second', new SecondGenericObject($this->Objects));
		$this->assertInstanceOf('SecondGenericObject', $result['Second'], 'set failed');

		$this->assertEquals(2, count($result));
	}

/**
 * creates mock classes for testing
 *
 * @return void
 */
	protected function _makeMockClasses() {
		$this->FirstGenericObject = $this->getMock('FirstGenericObject', [], [], '', false);
		$this->SecondGenericObject = $this->getMock('SecondGenericObject', [], [], '', false);
		$this->ThirdGenericObject = $this->getMock('ThirdGenericObject', [], [], '', false);
	}

/**
 * test triggering callbacks.
 *
 * @return void
 */
	public function testTrigger() {
		$this->_makeMockClasses();
		$this->Objects->setObject('TriggerMockFirst', $this->FirstGenericObject);
		$this->Objects->setObject('TriggerMockSecond', $this->SecondGenericObject);

		$this->Objects->TriggerMockFirst->expects($this->once())
			->method('callback')
			->will($this->returnValue(true));
		$this->Objects->TriggerMockSecond->expects($this->once())
			->method('callback')
			->will($this->returnValue(true));

		$this->assertTrue($this->Objects->trigger('callback'));
	}

/**
 * test trigger and disabled objects
 *
 * @return void
 */
	public function testTriggerWithDisabledObjects() {
		$this->_makeMockClasses();
		$this->Objects->setObject('TriggerMockFirst', $this->FirstGenericObject);
		$this->Objects->setObject('TriggerMockSecond', $this->SecondGenericObject, ['enabled' => false]);

		$this->Objects->TriggerMockFirst->expects($this->once())
			->method('callback')
			->will($this->returnValue(true));
		$this->Objects->TriggerMockSecond->expects($this->never())
			->method('callback')
			->will($this->returnValue(true));

		$this->assertTrue($this->Objects->trigger('callback', []));
	}

/**
 * test that the collectReturn option works.
 *
 * @return void
 */
	public function testTriggerWithCollectReturn() {
		$this->_makeMockClasses();
		$this->Objects->setObject('TriggerMockFirst', $this->FirstGenericObject);
		$this->Objects->setObject('TriggerMockSecond', $this->SecondGenericObject);

		$this->Objects->TriggerMockFirst->expects($this->once())
			->method('callback')
			->will($this->returnValue(['one', 'two']));
		$this->Objects->TriggerMockSecond->expects($this->once())
			->method('callback')
			->will($this->returnValue(['three', 'four']));

		$result = $this->Objects->trigger('callback', [], ['collectReturn' => true]);
		$expected = [
			['one', 'two'],
			['three', 'four']
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test that trigger with break & breakOn works.
 *
 * @return void
 */
	public function testTriggerWithBreak() {
		$this->_makeMockClasses();
		$this->Objects->setObject('TriggerMockFirst', $this->FirstGenericObject);
		$this->Objects->setObject('TriggerMockSecond', $this->SecondGenericObject);

		$this->Objects->TriggerMockFirst->expects($this->once())
			->method('callback')
			->will($this->returnValue(false));
		$this->Objects->TriggerMockSecond->expects($this->never())
			->method('callback');

		$result = $this->Objects->trigger(
			'callback',
			[],
			['break' => true, 'breakOn' => false]
		);
		$this->assertFalse($result);
	}

/**
 * test that trigger with modParams works.
 *
 * @return void
 */
	public function testTriggerWithModParams() {
		$this->_makeMockClasses();
		$this->Objects->setObject('TriggerMockFirst', $this->FirstGenericObject);
		$this->Objects->setObject('TriggerMockSecond', $this->SecondGenericObject);

		$this->Objects->TriggerMockFirst->expects($this->once())
			->method('callback')
			->with(['value'])
			->will($this->returnValue(['new value']));

		$this->Objects->TriggerMockSecond->expects($this->once())
			->method('callback')
			->with(['new value'])
			->will($this->returnValue(['newer value']));

		$result = $this->Objects->trigger(
			'callback',
			[['value']],
			['modParams' => 0]
		);
		$this->assertEquals(['newer value'], $result);
	}

/**
 * test that setting modParams to an index that doesn't exist doesn't cause errors.
 *
 * @expectedException CakeException
 * @return void
 */
	public function testTriggerModParamsInvalidIndex() {
		$this->_makeMockClasses();
		$this->Objects->setObject('TriggerMockFirst', $this->FirstGenericObject);
		$this->Objects->setObject('TriggerMockSecond', $this->SecondGenericObject);

		$this->Objects->TriggerMockFirst->expects($this->never())
			->method('callback');

		$this->Objects->TriggerMockSecond->expects($this->never())
			->method('callback');

		$this->Objects->trigger(
			'callback',
			[['value']],
			['modParams' => 2]
		);
	}

/**
 * test that returning null doesn't modify parameters.
 *
 * @return void
 */
	public function testTriggerModParamsNullIgnored() {
		$this->_makeMockClasses();
		$this->Objects->setObject('TriggerMockFirst', $this->FirstGenericObject);
		$this->Objects->setObject('TriggerMockSecond', $this->SecondGenericObject);

		$this->Objects->TriggerMockFirst->expects($this->once())
			->method('callback')
			->with(['value'])
			->will($this->returnValue(null));

		$this->Objects->TriggerMockSecond->expects($this->once())
			->method('callback')
			->with(['value'])
			->will($this->returnValue(['new value']));

		$result = $this->Objects->trigger(
			'callback',
			[['value']],
			['modParams' => 0]
		);
		$this->assertEquals(['new value'], $result);
	}

/**
 * test order of callbacks triggering based on priority.
 *
 * @return void
 */
	public function testTriggerPriority() {
		$this->_makeMockClasses();
		$this->Objects->setObject('TriggerMockFirst', $this->FirstGenericObject);
		$this->Objects->setObject('TriggerMockSecond', $this->SecondGenericObject, ['priority' => 5]);

		$this->Objects->TriggerMockFirst->expects($this->any())
			->method('callback')
			->will($this->returnValue('1st'));
		$this->Objects->TriggerMockSecond->expects($this->any())
			->method('callback')
			->will($this->returnValue('2nd'));

		$result = $this->Objects->trigger('callback', [], ['collectReturn' => true]);
		$expected = [
			'2nd',
			'1st'
		];
		$this->assertEquals($expected, $result);

		$this->Objects->setObject('TriggerMockThird', $this->ThirdGenericObject, ['priority' => 7]);
		$this->Objects->TriggerMockThird->expects($this->any())
			->method('callback')
			->will($this->returnValue('3rd'));

		$result = $this->Objects->trigger('callback', [], ['collectReturn' => true]);
		$expected = [
			'2nd',
			'3rd',
			'1st'
		];
		$this->assertEquals($expected, $result);

		$this->Objects->disable('TriggerMockFirst');
		$result = $this->Objects->trigger('callback', [], ['collectReturn' => true]);
		$expected = [
			'2nd',
			'3rd'
		];
		$this->assertEquals($expected, $result);

		$this->Objects->enable('TriggerMockFirst');
		$result = $this->Objects->trigger('callback', [], ['collectReturn' => true]);
		$expected = [
			'2nd',
			'3rd',
			'1st'
		];
		$this->assertEquals($expected, $result);

		$this->Objects->disable('TriggerMockThird');
		$result = $this->Objects->trigger('callback', [], ['collectReturn' => true]);
		$expected = [
			'2nd',
			'1st'
		];
		$this->assertEquals($expected, $result);

		$this->Objects->enable('TriggerMockThird', false);
		$result = $this->Objects->trigger('callback', [], ['collectReturn' => true]);
		$expected = [
			'2nd',
			'1st',
			'3rd'
		];
		$this->assertEquals($expected, $result);

		$this->Objects->setPriority('TriggerMockThird', 1);
		$result = $this->Objects->trigger('callback', [], ['collectReturn' => true]);
		$expected = [
			'3rd',
			'2nd',
			'1st'
		];
		$this->assertEquals($expected, $result);

		$this->Objects->disable('TriggerMockThird');
		$this->Objects->setPriority('TriggerMockThird', 11);
		$result = $this->Objects->trigger('callback', [], ['collectReturn' => true]);
		$expected = [
			'2nd',
			'1st'
		];
		$this->assertEquals($expected, $result);

		$this->Objects->enable('TriggerMockThird');
		$result = $this->Objects->trigger('callback', [], ['collectReturn' => true]);
		$expected = [
			'2nd',
			'1st',
			'3rd'
		];
		$this->assertEquals($expected, $result);

		$this->Objects->setPriority('TriggerMockThird');
		$result = $this->Objects->trigger('callback', [], ['collectReturn' => true]);
		$expected = [
			'2nd',
			'1st',
			'3rd'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test normalizeObjectArray
 *
 * @return void
 */
	public function testnormalizeObjectArray() {
		$components = [
			'Html',
			'Foo.Bar' => ['one', 'two'],
			'Something',
			'Banana.Apple' => ['foo' => 'bar']
		];
		$result = ObjectCollection::normalizeObjectArray($components);
		$expected = [
			'Html' => ['class' => 'Html', 'settings' => []],
			'Bar' => ['class' => 'Foo.Bar', 'settings' => ['one', 'two']],
			'Something' => ['class' => 'Something', 'settings' => []],
			'Apple' => ['class' => 'Banana.Apple', 'settings' => ['foo' => 'bar']],
		];
		$this->assertEquals($expected, $result);

		// This is the result after Controller::_mergeVars
		$components = [
			'Html' => null,
			'Foo.Bar' => ['one', 'two'],
			'Something' => null,
			'Banana.Apple' => ['foo' => 'bar']
		];
		$result = ObjectCollection::normalizeObjectArray($components);
		$this->assertEquals($expected, $result);
	}

/**
 * tests that passing an instance of CakeEvent to trigger will prepend the subject to the list of arguments
 *
 * @return void
 * @triggers callback $subjectClass, array('first argument')
 */
	public function testDispatchEventWithSubject() {
		$this->_makeMockClasses();
		$this->Objects->setObject('TriggerMockFirst', $this->FirstGenericObject);
		$this->Objects->setObject('TriggerMockSecond', $this->SecondGenericObject);

		$subjectClass = new Object();
		$this->Objects->TriggerMockFirst->expects($this->once())
			->method('callback')
			->with($subjectClass, 'first argument')
			->will($this->returnValue(true));
		$this->Objects->TriggerMockSecond->expects($this->once())
			->method('callback')
			->with($subjectClass, 'first argument')
			->will($this->returnValue(true));

		$event = new CakeEvent('callback', $subjectClass, ['first argument']);
		$this->assertTrue($this->Objects->trigger($event));
	}

/**
 * tests that passing an instance of CakeEvent to trigger with omitSubject property
 * will NOT prepend the subject to the list of arguments
 *
 * @return void
 * @triggers callback $subjectClass, array('first argument')
 */
	public function testDispatchEventNoSubject() {
		$this->_makeMockClasses();
		$this->Objects->setObject('TriggerMockFirst', $this->FirstGenericObject);
		$this->Objects->setObject('TriggerMockSecond', $this->SecondGenericObject);

		$subjectClass = new Object();
		$this->Objects->TriggerMockFirst->expects($this->once())
			->method('callback')
			->with('first argument')
			->will($this->returnValue(true));
		$this->Objects->TriggerMockSecond->expects($this->once())
			->method('callback')
			->with('first argument')
			->will($this->returnValue(true));

		$event = new CakeEvent('callback', $subjectClass, ['first argument']);
		$event->omitSubject = true;
		$this->assertTrue($this->Objects->trigger($event));
	}

/**
 * test that the various methods ignore plugin prefixes
 *
 * plugin prefixes should be removed consistently as load() will
 * remove them. Furthermore the __get() method does not support
 * names with '.' in them.
 *
 * @return void
 */
	public function testPluginPrefixes() {
		$this->Objects->load('TestPlugin.First');
		$this->assertTrue($this->Objects->loaded('First'));
		$this->assertTrue($this->Objects->loaded('TestPlugin.First'));

		$this->assertTrue($this->Objects->enabled('First'));
		$this->assertTrue($this->Objects->enabled('TestPlugin.First'));

		$this->assertNull($this->Objects->disable('TestPlugin.First'));
		$this->assertFalse($this->Objects->enabled('First'));
		$this->assertFalse($this->Objects->enabled('TestPlugin.First'));

		$this->assertNull($this->Objects->enable('TestPlugin.First'));
		$this->assertTrue($this->Objects->enabled('First'));
		$this->assertTrue($this->Objects->enabled('TestPlugin.First'));
		$this->Objects->setPriority('TestPlugin.First', 1000);

		$result = $this->Objects->prioritize();
		$this->assertEquals(1000, $result['First'][0]);
	}
}
