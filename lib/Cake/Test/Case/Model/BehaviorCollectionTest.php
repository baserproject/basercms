<?php
/**
 * BehaviorTest file
 *
 * Long description for behavior.test.php
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Test.Case.Model
 * @since         1.2
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppModel', 'Model');

require_once dirname(__FILE__) . DS . 'models.php';

/**
 * TestBehavior class
 *
 * @package       Cake.Test.Case.Model
 */
class TestBehavior extends ModelBehavior {

/**
 * mapMethods property
 *
 * @var array
 */
	public $mapMethods = ['/test(\w+)/' => 'testMethod', '/look for\s+(.+)/' => 'speakEnglish'];

/**
 * setup method
 *
 * @param Model $model
 * @param array $config
 * @return void
 */
	public function setup(Model $model, $config = []) {
		parent::setup($model, $config);
		if (isset($config['mangle'])) {
			$config['mangle'] .= ' mangled';
		}
		$this->settings[$model->alias] = array_merge(['beforeFind' => 'on', 'afterFind' => 'off'], $config);
	}

/**
 * beforeFind method
 *
 * @param Model $model
 * @param array $query
 * @return void
 */
	public function beforeFind(Model $model, $query) {
		$settings = $this->settings[$model->alias];
		if (!isset($settings['beforeFind']) || $settings['beforeFind'] === 'off') {
			return parent::beforeFind($model, $query);
		}
		switch ($settings['beforeFind']) {
			case 'on':
				return false;
			case 'test':
				return null;
			case 'modify':
				$query['fields'] = [$model->alias . '.id', $model->alias . '.name', $model->alias . '.mytime'];
				$query['recursive'] = -1;
				return $query;
		}
	}

/**
 * afterFind method
 *
 * @param Model $model
 * @param array $results
 * @param bool $primary
 * @return void
 */
	public function afterFind(Model $model, $results, $primary = false) {
		$settings = $this->settings[$model->alias];
		if (!isset($settings['afterFind']) || $settings['afterFind'] === 'off') {
			return parent::afterFind($model, $results, $primary);
		}
		switch ($settings['afterFind']) {
			case 'on':
				return [];
			case 'test':
				return true;
			case 'test2':
				return null;
			case 'modify':
				return Hash::extract($results, "{n}.{$model->alias}");
		}
	}

/**
 * beforeSave method
 *
 * @param Model $model Model using this behavior
 * @param array $options Options passed from Model::save().
 * @return mixed False if the operation should abort. Any other result will continue.
 * @see Model::save()
 */
	public function beforeSave(Model $model, $options = []) {
		$settings = $this->settings[$model->alias];
		if (!isset($settings['beforeSave']) || $settings['beforeSave'] === 'off') {
			return parent::beforeSave($model, $options);
		}
		switch ($settings['beforeSave']) {
			case 'on':
				return false;
			case 'test':
				return true;
			case 'modify':
				$model->data[$model->alias]['name'] .= ' modified before';
				return true;
		}
	}

/**
 * afterSave method
 *
 * @param Model $model
 * @param bool $created
 * @param array $options Options passed from Model::save().
 * @return void
 */
	public function afterSave(Model $model, $created, $options = []) {
		$settings = $this->settings[$model->alias];
		if (!isset($settings['afterSave']) || $settings['afterSave'] === 'off') {
			return parent::afterSave($model, $created, $options);
		}
		$string = 'modified after';
		if ($created) {
			$string .= ' on create';
		}
		switch ($settings['afterSave']) {
			case 'on':
				$model->data[$model->alias]['aftersave'] = $string;
				break;
			case 'test':
				unset($model->data[$model->alias]['name']);
				break;
			case 'test2':
				return false;
			case 'modify':
				$model->data[$model->alias]['name'] .= ' ' . $string;
				break;
		}
	}

/**
 * beforeValidate Callback
 *
 * @param Model $Model Model invalidFields was called on.
 * @param array $options Options passed from Model::save().
 * @return bool
 * @see Model::save()
 */
	public function beforeValidate(Model $model, $options = []) {
		$settings = $this->settings[$model->alias];
		if (!isset($settings['validate']) || $settings['validate'] === 'off') {
			return parent::beforeValidate($model, $options);
		}
		switch ($settings['validate']) {
			case 'on':
				$model->invalidate('name');
				return true;
			case 'test':
				return null;
			case 'whitelist':
				$this->_addToWhitelist($model, ['name']);
				return true;
			case 'stop':
				$model->invalidate('name');
				return false;
		}
	}

/**
 * afterValidate method
 *
 * @param Model $model
 * @param bool $cascade
 * @return void
 */
	public function afterValidate(Model $model) {
		$settings = $this->settings[$model->alias];
		if (!isset($settings['afterValidate']) || $settings['afterValidate'] === 'off') {
			return parent::afterValidate($model);
		}
		switch ($settings['afterValidate']) {
			case 'on':
				return false;
			case 'test':
				$model->data = ['foo'];
				return true;
		}
	}

/**
 * beforeDelete method
 *
 * @param Model $model
 * @param bool $cascade
 * @return void
 */
	public function beforeDelete(Model $model, $cascade = true) {
		$settings = $this->settings[$model->alias];
		if (!isset($settings['beforeDelete']) || $settings['beforeDelete'] === 'off') {
			return parent::beforeDelete($model, $cascade);
		}
		switch ($settings['beforeDelete']) {
			case 'on':
				return false;
			case 'test':
				return null;
			case 'test2':
				echo 'beforeDelete success';
				if ($cascade) {
					echo ' (cascading) ';
				}
				return true;
		}
	}

/**
 * afterDelete method
 *
 * @param Model $model
 * @return void
 */
	public function afterDelete(Model $model) {
		$settings = $this->settings[$model->alias];
		if (!isset($settings['afterDelete']) || $settings['afterDelete'] === 'off') {
			return parent::afterDelete($model);
		}
		switch ($settings['afterDelete']) {
			case 'on':
				echo 'afterDelete success';
				break;
		}
	}

/**
 * onError method
 *
 * @param Model $model
 * @return void
 */
	public function onError(Model $model, $error) {
		$settings = $this->settings[$model->alias];
		if (!isset($settings['onError']) || $settings['onError'] === 'off') {
			return parent::onError($model, $error);
		}
		echo "onError trigger success";
	}

/**
 * beforeTest method
 *
 * @param Model $model
 * @return void
 */
	public function beforeTest(Model $model) {
		if (!isset($model->beforeTestResult)) {
			$model->beforeTestResult = [];
		}
		$model->beforeTestResult[] = strtolower(get_class($this));
		return strtolower(get_class($this));
	}

/**
 * testMethod method
 *
 * @param Model $model
 * @param bool $param
 * @return void
 */
	public function testMethod(Model $model, $param = true) {
		if ($param === true) {
			return 'working';
		}
	}

/**
 * testData method
 *
 * @param Model $model
 * @return void
 */
	public function testData(Model $model) {
		if (!isset($model->data['Apple']['field'])) {
			return false;
		}
		$model->data['Apple']['field_2'] = true;
		return true;
	}

/**
 * validateField method
 *
 * @param Model $model
 * @param string|array $field
 * @return void
 */
	public function validateField(Model $model, $field) {
		return current($field) === 'Orange';
	}

/**
 * speakEnglish method
 *
 * @param Model $model
 * @param string $method
 * @param string $query
 * @return void
 */
	public function speakEnglish(Model $model, $method, $query) {
		$method = preg_replace('/look for\s+/', 'Item.name = \'', $method);
		$query = preg_replace('/^in\s+/', 'Location.name = \'', $query);
		return $method . '\' AND ' . $query . '\'';
	}

}

/**
 * Test2Behavior class
 *
 * @package       Cake.Test.Case.Model
 */
class Test2Behavior extends TestBehavior {

	public $mapMethods = ['/mappingRobot(\w+)/' => 'mapped'];

	public function resolveMethod(Model $model, $stuff) {
	}

	public function mapped(Model $model, $method, $query) {
	}

}

/**
 * Test3Behavior class
 *
 * @package       Cake.Test.Case.Model
 */
class Test3Behavior extends TestBehavior {
}

/**
 * Test4Behavior class
 *
 * @package       Cake.Test.Case.Model
 */
class Test4Behavior extends ModelBehavior {

	public function setup(Model $model, $config = null) {
		$model->bindModel(
			['hasMany' => ['Comment']]
		);
	}

}

/**
 * Test5Behavior class
 *
 * @package       Cake.Test.Case.Model
 */
class Test5Behavior extends ModelBehavior {

	public function setup(Model $model, $config = null) {
		$model->bindModel(
			['belongsTo' => ['User']]
		);
	}

}

/**
 * Test6Behavior class
 *
 * @package       Cake.Test.Case.Model
 */
class Test6Behavior extends ModelBehavior {

	public function setup(Model $model, $config = null) {
		$model->bindModel(
			['hasAndBelongsToMany' => ['Tag']]
		);
	}

}

/**
 * Test7Behavior class
 *
 * @package       Cake.Test.Case.Model
 */
class Test7Behavior extends ModelBehavior {

	public function setup(Model $model, $config = null) {
		$model->bindModel(
			['hasOne' => ['Attachment']]
		);
	}

}

/**
 * Extended TestBehavior
 */
class TestAliasBehavior extends TestBehavior {
}

/**
 * FirstBehavior
 */
class FirstBehavior extends ModelBehavior {

	public function beforeFind(Model $model, $query = []) {
		$model->called[] = get_class($this);
		return $query;
	}

}

/**
 * SecondBehavior
 */
class SecondBehavior extends FirstBehavior {
}

/**
 * ThirdBehavior
 */
class ThirdBehavior extends FirstBehavior {
}

/**
 * Orangutan Model
 */
class Orangutan extends Monkey {

	public $called = [];

}

/**
 * BehaviorCollection class
 *
 * @package       Cake.Test.Case.Model
 */
class BehaviorCollectionTest extends CakeTestCase {

/**
 * fixtures property
 *
 * @var array
 */
	public $fixtures = [
		'core.apple', 'core.sample', 'core.article', 'core.user', 'core.comment',
		'core.attachment', 'core.tag', 'core.articles_tag', 'core.translate',
		'core.device'
	];

/**
 * Test load() with enabled => false
 *
 * @return void
 */
	public function testLoadDisabled() {
		$Apple = new Apple();
		$this->assertSame([], $Apple->Behaviors->loaded());

		$Apple->Behaviors->load('Translate', ['enabled' => false]);
		$this->assertTrue($Apple->Behaviors->loaded('Translate'));
		$this->assertFalse($Apple->Behaviors->enabled('Translate'));
	}

/**
 * Tests loading aliased behaviors
 *
 * @return void
 */
	public function testLoadAlias() {
		$Apple = new Apple();
		$this->assertSame([], $Apple->Behaviors->loaded());

		$Apple->Behaviors->load('Test', ['className' => 'TestAlias', 'somesetting' => true]);
		$this->assertSame(['Test'], $Apple->Behaviors->loaded());
		$this->assertInstanceOf('TestAliasBehavior', $Apple->Behaviors->Test);
		$this->assertTrue($Apple->Behaviors->Test->settings['Apple']['somesetting']);

		$this->assertEquals('working', $Apple->Behaviors->Test->testMethod($Apple, true));
		$this->assertEquals('working', $Apple->testMethod(true));
		$this->assertEquals('working', $Apple->Behaviors->dispatchMethod($Apple, 'testMethod'));

		App::build(['Plugin' => [CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS]]);
		CakePlugin::load('TestPlugin');
		$this->assertTrue($Apple->Behaviors->load('SomeOther', ['className' => 'TestPlugin.TestPluginPersisterOne']));
		$this->assertInstanceOf('TestPluginPersisterOneBehavior', $Apple->Behaviors->SomeOther);

		$result = $Apple->Behaviors->loaded();
		$this->assertEquals(['Test', 'SomeOther'], $result, 'loaded() results are wrong.');
		App::build();
		CakePlugin::unload();
	}

/**
 * testBehaviorBinding method
 *
 * @return void
 */
	public function testBehaviorBinding() {
		$Apple = new Apple();
		$this->assertSame([], $Apple->Behaviors->loaded());

		$Apple->Behaviors->load('Test', ['key' => 'value']);
		$this->assertSame(['Test'], $Apple->Behaviors->loaded());
		$this->assertEquals('testbehavior', strtolower(get_class($Apple->Behaviors->Test)));
		$expected = ['beforeFind' => 'on', 'afterFind' => 'off', 'key' => 'value'];
		$this->assertEquals($expected, $Apple->Behaviors->Test->settings['Apple']);
		$this->assertEquals(['priority', 'Apple'], array_keys($Apple->Behaviors->Test->settings));

		$this->assertSame($Apple->Sample->Behaviors->loaded(), []);
		$Apple->Sample->Behaviors->load('Test', ['key2' => 'value2']);
		$this->assertSame($Apple->Sample->Behaviors->loaded(), ['Test']);
		$this->assertEquals(['beforeFind' => 'on', 'afterFind' => 'off', 'key2' => 'value2'], $Apple->Sample->Behaviors->Test->settings['Sample']);

		$this->assertEquals(['priority', 'Apple', 'Sample'], array_keys($Apple->Behaviors->Test->settings));
		$this->assertSame(
			$Apple->Sample->Behaviors->Test->settings,
			$Apple->Behaviors->Test->settings
		);
		$this->assertNotSame($Apple->Behaviors->Test->settings['Apple'], $Apple->Sample->Behaviors->Test->settings['Sample']);

		$Apple->Behaviors->load('Test', ['key2' => 'value2', 'key3' => 'value3', 'beforeFind' => 'off']);
		$Apple->Sample->Behaviors->load('Test', ['key' => 'value', 'key3' => 'value3', 'beforeFind' => 'off']);
		$this->assertEquals(['beforeFind' => 'off', 'afterFind' => 'off', 'key' => 'value', 'key2' => 'value2', 'key3' => 'value3'], $Apple->Behaviors->Test->settings['Apple']);
		$this->assertEquals($Apple->Behaviors->Test->settings['Apple'], $Apple->Sample->Behaviors->Test->settings['Sample']);

		$this->assertFalse(isset($Apple->Child->Behaviors->Test));
		$Apple->Child->Behaviors->load('Test', ['key' => 'value', 'key2' => 'value2', 'key3' => 'value3', 'beforeFind' => 'off']);
		$this->assertEquals($Apple->Child->Behaviors->Test->settings['Child'], $Apple->Sample->Behaviors->Test->settings['Sample']);

		$this->assertFalse(isset($Apple->Parent->Behaviors->Test));
		$Apple->Parent->Behaviors->load('Test', ['key' => 'value', 'key2' => 'value2', 'key3' => 'value3', 'beforeFind' => 'off']);
		$this->assertEquals($Apple->Parent->Behaviors->Test->settings['Parent'], $Apple->Sample->Behaviors->Test->settings['Sample']);

		$Apple->Parent->Behaviors->load('Test', ['key' => 'value', 'key2' => 'value', 'key3' => 'value', 'beforeFind' => 'off']);
		$this->assertNotEquals($Apple->Parent->Behaviors->Test->settings['Parent'], $Apple->Sample->Behaviors->Test->settings['Sample']);

		$Apple->Behaviors->load('Plugin.Test', ['key' => 'new value']);
		$expected = [
			'beforeFind' => 'off', 'afterFind' => 'off', 'key' => 'new value',
			'key2' => 'value2', 'key3' => 'value3'
		];
		$this->assertEquals($expected, $Apple->Behaviors->Test->settings['Apple']);

		$current = $Apple->Behaviors->Test->settings['Apple'];
		$expected = array_merge($current, ['mangle' => 'trigger mangled']);
		$Apple->Behaviors->load('Test', ['mangle' => 'trigger']);
		$this->assertEquals($expected, $Apple->Behaviors->Test->settings['Apple']);

		$Apple->Behaviors->load('Test');
		$expected = array_merge($current, ['mangle' => 'trigger mangled mangled']);

		$this->assertEquals($expected, $Apple->Behaviors->Test->settings['Apple']);
		$Apple->Behaviors->load('Test', ['mangle' => 'trigger']);
		$expected = array_merge($current, ['mangle' => 'trigger mangled']);
		$this->assertEquals($expected, $Apple->Behaviors->Test->settings['Apple']);
	}

/**
 * test that attach()/detach() works with plugin.banana
 *
 * @return void
 */
	public function testDetachWithPluginNames() {
		$Apple = new Apple();
		$Apple->Behaviors->load('Plugin.Test');
		$this->assertTrue(isset($Apple->Behaviors->Test), 'Missing behavior');
		$this->assertEquals(['Test'], $Apple->Behaviors->loaded());

		$Apple->Behaviors->unload('Plugin.Test');
		$this->assertEquals([], $Apple->Behaviors->loaded());

		$Apple->Behaviors->load('Plugin.Test');
		$this->assertTrue(isset($Apple->Behaviors->Test), 'Missing behavior');
		$this->assertEquals(['Test'], $Apple->Behaviors->loaded());

		$Apple->Behaviors->unload('Test');
		$this->assertEquals([], $Apple->Behaviors->loaded());
	}

/**
 * test that attaching a non existent Behavior triggers a cake error.
 *
 * @expectedException MissingBehaviorException
 * @return void
 */
	public function testInvalidBehaviorCausingCakeError() {
		$Apple = new Apple();
		$Apple->Behaviors->load('NoSuchBehavior');
	}

/**
 * testBehaviorToggling method
 *
 * @return void
 */
	public function testBehaviorToggling() {
		$Apple = new Apple();
		$this->assertSame($Apple->Behaviors->enabled(), []);

		$Apple->Behaviors->init('Apple', ['Test' => ['key' => 'value']]);
		$this->assertSame($Apple->Behaviors->enabled(), ['Test']);

		$Apple->Behaviors->disable('Test');
		$this->assertSame(['Test'], $Apple->Behaviors->loaded());
		$this->assertSame($Apple->Behaviors->enabled(), []);

		$Apple->Sample->Behaviors->load('Test');
		$this->assertTrue($Apple->Sample->Behaviors->enabled('Test'));
		$this->assertSame($Apple->Behaviors->enabled(), []);

		$Apple->Behaviors->enable('Test');
		$this->assertTrue($Apple->Behaviors->loaded('Test'));
		$this->assertSame($Apple->Behaviors->enabled(), ['Test']);

		$Apple->Behaviors->disable('Test');
		$this->assertSame($Apple->Behaviors->enabled(), []);
		$Apple->Behaviors->load('Test', ['enabled' => true]);
		$this->assertSame($Apple->Behaviors->enabled(), ['Test']);
		$Apple->Behaviors->load('Test', ['enabled' => false]);
		$this->assertSame($Apple->Behaviors->enabled(), []);
		$Apple->Behaviors->unload('Test');
		$this->assertSame($Apple->Behaviors->enabled(), []);
	}

/**
 * testBehaviorFindCallbacks method
 *
 * @return void
 */
	public function testBehaviorFindCallbacks() {
		$this->skipIf($this->db instanceof Sqlserver, 'This test is not compatible with SQL Server.');

		$Apple = new Apple();
		$expected = $Apple->find('all');

		$Apple->Behaviors->load('Test');
		$this->assertNull($Apple->find('all'));

		$Apple->Behaviors->load('Test', ['beforeFind' => 'off']);
		$this->assertSame($expected, $Apple->find('all'));

		$Apple->Behaviors->load('Test', ['beforeFind' => 'test']);
		$this->assertSame($expected, $Apple->find('all'));

		$Apple->Behaviors->load('Test', ['beforeFind' => 'modify']);
		$expected2 = [
			['Apple' => ['id' => '1', 'name' => 'Red Apple 1', 'mytime' => '22:57:17']],
			['Apple' => ['id' => '2', 'name' => 'Bright Red Apple', 'mytime' => '22:57:17']],
			['Apple' => ['id' => '3', 'name' => 'green blue', 'mytime' => '22:57:17']]
		];
		$result = $Apple->find('all', ['conditions' => ['Apple.id <' => '4']]);
		$this->assertEquals($expected2, $result);

		$Apple->Behaviors->disable('Test');
		$result = $Apple->find('all');
		$this->assertEquals($expected, $result);

		$Apple->Behaviors->load('Test', ['beforeFind' => 'off', 'afterFind' => 'on']);
		$this->assertSame($Apple->find('all'), []);

		$Apple->Behaviors->load('Test', ['afterFind' => 'off']);
		$this->assertEquals($expected, $Apple->find('all'));

		$Apple->Behaviors->load('Test', ['afterFind' => 'test']);
		$this->assertEquals($expected, $Apple->find('all'));

		$Apple->Behaviors->load('Test', ['afterFind' => 'test2']);
		$this->assertEquals($expected, $Apple->find('all'));

		$Apple->Behaviors->load('Test', ['afterFind' => 'modify']);
		$expected = [
			['id' => '1', 'apple_id' => '2', 'color' => 'Red 1', 'name' => 'Red Apple 1', 'created' => '2006-11-22 10:38:58', 'date' => '1951-01-04', 'modified' => '2006-12-01 13:31:26', 'mytime' => '22:57:17'],
			['id' => '2', 'apple_id' => '1', 'color' => 'Bright Red 1', 'name' => 'Bright Red Apple', 'created' => '2006-11-22 10:43:13', 'date' => '2014-01-01', 'modified' => '2006-11-30 18:38:10', 'mytime' => '22:57:17'],
			['id' => '3', 'apple_id' => '2', 'color' => 'blue green', 'name' => 'green blue', 'created' => '2006-12-25 05:13:36', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:23:24', 'mytime' => '22:57:17'],
			['id' => '4', 'apple_id' => '2', 'color' => 'Blue Green', 'name' => 'Test Name', 'created' => '2006-12-25 05:23:36', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:23:36', 'mytime' => '22:57:17'],
			['id' => '5', 'apple_id' => '5', 'color' => 'Green', 'name' => 'Blue Green', 'created' => '2006-12-25 05:24:06', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:29:16', 'mytime' => '22:57:17'],
			['id' => '6', 'apple_id' => '4', 'color' => 'My new appleOrange', 'name' => 'My new apple', 'created' => '2006-12-25 05:29:39', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:29:39', 'mytime' => '22:57:17'],
			['id' => '7', 'apple_id' => '6', 'color' => 'Some wierd color', 'name' => 'Some odd color', 'created' => '2006-12-25 05:34:21', 'date' => '2006-12-25', 'modified' => '2006-12-25 05:34:21', 'mytime' => '22:57:17']
		];
		$this->assertEquals($expected, $Apple->find('all'));
	}

/**
 * testBehaviorHasManyFindCallbacks method
 *
 * @return void
 */
	public function testBehaviorHasManyFindCallbacks() {
		$Apple = new Apple();
		$Apple->unbindModel(['hasOne' => ['Sample'], 'belongsTo' => ['Parent']], false);
		$expected = $Apple->find('all');

		$Apple->unbindModel(['hasMany' => ['Child']]);
		$wellBehaved = $Apple->find('all');
		$Apple->Child->Behaviors->load('Test', ['afterFind' => 'modify']);
		$Apple->unbindModel(['hasMany' => ['Child']]);
		$this->assertSame($Apple->find('all'), $wellBehaved);

		$Apple->Child->Behaviors->load('Test', ['before' => 'off']);
		$this->assertSame($expected, $Apple->find('all'));

		$Apple->Child->Behaviors->load('Test', ['before' => 'test']);
		$this->assertSame($expected, $Apple->find('all'));

		$Apple->Child->Behaviors->load('Test', ['before' => 'modify']);
		$result = $Apple->find('all', ['fields' => ['Apple.id'], 'conditions' => ['Apple.id <' => '4']]);

		$Apple->Child->Behaviors->disable('Test');
		$result = $Apple->find('all');
		$this->assertEquals($expected, $result);

		$Apple->Child->Behaviors->load('Test', ['before' => 'off', 'after' => 'on']);

		$Apple->Child->Behaviors->load('Test', ['after' => 'off']);
		$this->assertEquals($expected, $Apple->find('all'));

		$Apple->Child->Behaviors->load('Test', ['after' => 'test']);
		$this->assertEquals($expected, $Apple->find('all'));

		$Apple->Child->Behaviors->load('Test', ['after' => 'test2']);
		$this->assertEquals($expected, $Apple->find('all'));
	}

/**
 * testBehaviorHasOneFindCallbacks method
 *
 * @return void
 */
	public function testBehaviorHasOneFindCallbacks() {
		$Apple = new Apple();
		$Apple->unbindModel(['hasMany' => ['Child'], 'belongsTo' => ['Parent']], false);
		$expected = $Apple->find('all');

		$Apple->unbindModel(['hasOne' => ['Sample']]);
		$wellBehaved = $Apple->find('all');
		$Apple->Sample->Behaviors->load('Test');
		$Apple->unbindModel(['hasOne' => ['Sample']]);
		$this->assertSame($Apple->find('all'), $wellBehaved);

		$Apple->Sample->Behaviors->load('Test', ['before' => 'off']);
		$this->assertSame($expected, $Apple->find('all'));

		$Apple->Sample->Behaviors->load('Test', ['before' => 'test']);
		$this->assertSame($expected, $Apple->find('all'));

		$Apple->Sample->Behaviors->disable('Test');
		$result = $Apple->find('all');
		$this->assertEquals($expected, $result);

		$Apple->Sample->Behaviors->load('Test', ['after' => 'off']);
		$this->assertEquals($expected, $Apple->find('all'));

		$Apple->Sample->Behaviors->load('Test', ['after' => 'test']);
		$this->assertEquals($expected, $Apple->find('all'));

		$Apple->Sample->Behaviors->load('Test', ['after' => 'test2']);
		$this->assertEquals($expected, $Apple->find('all'));
	}

/**
 * testBehaviorBelongsToFindCallbacks method
 *
 * @return void
 */
	public function testBehaviorBelongsToFindCallbacks() {
		$this->skipIf($this->db instanceof Sqlserver, 'This test is not compatible with SQL Server.');

		$conditions = ['order' => 'Apple.id ASC'];
		$Apple = new Apple();
		$Apple->unbindModel(['hasMany' => ['Child'], 'hasOne' => ['Sample']], false);
		$expected = $Apple->find('all', $conditions);

		$Apple->unbindModel(['belongsTo' => ['Parent']]);
		$wellBehaved = $Apple->find('all', $conditions);
		$Apple->Parent->Behaviors->load('Test');
		$Apple->unbindModel(['belongsTo' => ['Parent']]);
		$this->assertSame($Apple->find('all', $conditions), $wellBehaved);

		$Apple->Parent->Behaviors->load('Test', ['before' => 'off']);
		$this->assertSame($expected, $Apple->find('all', $conditions));

		$Apple->Parent->Behaviors->load('Test', ['before' => 'test']);
		$this->assertSame($expected, $Apple->find('all', $conditions));

		$Apple->Parent->Behaviors->load('Test', ['before' => 'modify']);
		$expected2 = [
			[
				'Apple' => ['id' => 1],
				'Parent' => ['id' => 2, 'name' => 'Bright Red Apple', 'mytime' => '22:57:17']],
			[
				'Apple' => ['id' => 2],
				'Parent' => ['id' => 1, 'name' => 'Red Apple 1', 'mytime' => '22:57:17']],
			[
				'Apple' => ['id' => 3],
				'Parent' => ['id' => 2, 'name' => 'Bright Red Apple', 'mytime' => '22:57:17']]
		];
		$result2 = $Apple->find('all', [
			'fields' => ['Apple.id', 'Parent.id', 'Parent.name', 'Parent.mytime'],
			'conditions' => ['Apple.id <' => '4'],
			'order' => 'Apple.id ASC',
		]);
		$this->assertEquals($expected2, $result2);

		$Apple->Parent->Behaviors->disable('Test');
		$result = $Apple->find('all', $conditions);
		$this->assertEquals($expected, $result);

		$Apple->Parent->Behaviors->load('Test', ['after' => 'off']);
		$this->assertEquals($expected, $Apple->find('all', $conditions));

		$Apple->Parent->Behaviors->load('Test', ['after' => 'test']);
		$this->assertEquals($expected, $Apple->find('all', $conditions));

		$Apple->Parent->Behaviors->load('Test', ['after' => 'test2']);
		$this->assertEquals($expected, $Apple->find('all', $conditions));
	}

/**
 * testBehaviorSaveCallbacks method
 *
 * @return void
 */
	public function testBehaviorSaveCallbacks() {
		$Sample = new Sample();
		$record = ['Sample' => ['apple_id' => 6, 'name' => 'sample99']];

		$Sample->Behaviors->load('Test', ['beforeSave' => 'on']);
		$Sample->create();
		$this->assertSame(false, $Sample->save($record));

		$Sample->Behaviors->load('Test', ['beforeSave' => 'off']);
		$Sample->create();
		$result = $Sample->save($record);
		$expected = $record;
		$expected['Sample']['id'] = $Sample->id;
		$this->assertSame($expected, $result);

		$Sample->Behaviors->load('Test', ['beforeSave' => 'test']);
		$Sample->create();
		$result = $Sample->save($record);
		$expected = $record;
		$expected['Sample']['id'] = $Sample->id;
		$this->assertSame($expected, $result);

		$Sample->Behaviors->load('Test', ['beforeSave' => 'modify']);
		$expected = Hash::insert($record, 'Sample.name', 'sample99 modified before');
		$Sample->create();
		$result = $Sample->save($record);
		$expected['Sample']['id'] = $Sample->id;
		$this->assertSame($expected, $result);

		$Sample->Behaviors->disable('Test');
		$this->assertSame($record, $Sample->save($record));

		$Sample->Behaviors->load('Test', ['beforeSave' => 'off', 'afterSave' => 'on']);
		$expected = Hash::merge($record, ['Sample' => ['aftersave' => 'modified after on create']]);
		$Sample->create();
		$result = $Sample->save($record);
		$expected['Sample']['id'] = $Sample->id;
		$this->assertEquals($expected, $result);

		$Sample->Behaviors->load('Test', ['beforeSave' => 'modify', 'afterSave' => 'modify']);
		$expected = Hash::merge($record, ['Sample' => ['name' => 'sample99 modified before modified after on create']]);
		$Sample->create();
		$result = $Sample->save($record);
		$expected['Sample']['id'] = $Sample->id;
		$this->assertSame($expected, $result);

		$Sample->Behaviors->load('Test', ['beforeSave' => 'off', 'afterSave' => 'test']);
		$Sample->create();
		$expected = $record;
		unset($expected['Sample']['name']);
		$result = $Sample->save($record);
		$expected['Sample']['id'] = $Sample->id;
		$this->assertSame($expected, $result);

		$Sample->Behaviors->load('Test', ['afterSave' => 'test2']);
		$Sample->create();
		$expected = $record;
		$result = $Sample->save($record);
		$expected['Sample']['id'] = $Sample->id;
		$this->assertSame($expected, $result);

		$Sample->Behaviors->load('Test', ['beforeFind' => 'off', 'afterFind' => 'off']);
		$Sample->recursive = -1;
		$record2 = $Sample->read(null, 1);

		$Sample->Behaviors->load('Test', ['afterSave' => 'on']);
		$expected = Hash::merge($record2, ['Sample' => ['aftersave' => 'modified after']]);
		$Sample->create();
		$this->assertSame($expected, $Sample->save($record2));

		$Sample->Behaviors->load('Test', ['afterSave' => 'modify']);
		$expected = Hash::merge($record2, ['Sample' => ['name' => 'sample1 modified after']]);
		$Sample->create();
		$this->assertSame($expected, $Sample->save($record2));
	}

/**
 * testBehaviorDeleteCallbacks method
 *
 * @return void
 */
	public function testBehaviorDeleteCallbacks() {
		$Apple = new Apple();

		$Apple->Behaviors->load('Test', ['beforeFind' => 'off', 'beforeDelete' => 'off']);
		$this->assertTrue($Apple->delete(6));

		$Apple->Behaviors->load('Test', ['beforeDelete' => 'on']);
		$this->assertFalse($Apple->delete(4));

		$Apple->Behaviors->load('Test', ['beforeDelete' => 'test2']);

		ob_start();
		$results = $Apple->delete(4);
		$this->assertSame(trim(ob_get_clean()), 'beforeDelete success (cascading)');
		$this->assertTrue($results);

		ob_start();
		$results = $Apple->delete(3, false);
		$this->assertSame(trim(ob_get_clean()), 'beforeDelete success');
		$this->assertTrue($results);

		$Apple->Behaviors->load('Test', ['beforeDelete' => 'off', 'afterDelete' => 'on']);
		ob_start();
		$results = $Apple->delete(2, false);
		$this->assertSame(trim(ob_get_clean()), 'afterDelete success');
		$this->assertTrue($results);
	}

/**
 * testBehaviorOnErrorCallback method
 *
 * @return void
 */
	public function testBehaviorOnErrorCallback() {
		$Apple = new Apple();

		$Apple->Behaviors->load('Test', ['beforeFind' => 'off', 'onError' => 'on']);
		ob_start();
		$Apple->Behaviors->Test->onError($Apple, '');
		$this->assertSame(trim(ob_get_clean()), 'onError trigger success');
	}

/**
 * testBehaviorValidateCallback method
 *
 * @return void
 */
	public function testBehaviorValidateCallback() {
		$Apple = new Apple();

		$Apple->Behaviors->load('Test');
		$this->assertTrue($Apple->validates());

		$Apple->Behaviors->load('Test', ['validate' => 'on']);
		$this->assertFalse($Apple->validates());
		$this->assertSame($Apple->validationErrors, ['name' => [true]]);

		$Apple->Behaviors->load('Test', ['validate' => 'stop']);
		$this->assertFalse($Apple->validates());
		$this->assertSame($Apple->validationErrors, ['name' => [true, true]]);

		$Apple->Behaviors->load('Test', ['validate' => 'whitelist']);
		$Apple->validates();
		$this->assertSame($Apple->whitelist, []);

		$Apple->whitelist = ['unknown'];
		$Apple->validates();
		$this->assertSame($Apple->whitelist, ['unknown', 'name']);
	}

/**
 * testBehaviorValidateAfterCallback method
 *
 * @return void
 */
	public function testBehaviorValidateAfterCallback() {
		$Apple = new Apple();

		$Apple->Behaviors->load('Test');
		$this->assertTrue($Apple->validates());

		$Apple->Behaviors->load('Test', ['afterValidate' => 'on']);
		$this->assertTrue($Apple->validates());
		$this->assertSame($Apple->validationErrors, []);

		$Apple->Behaviors->load('Test', ['afterValidate' => 'test']);
		$Apple->data = ['bar'];
		$Apple->validates();
		$this->assertEquals(['foo'], $Apple->data);
	}

/**
 * testBehaviorValidateMethods method
 *
 * @return void
 */
	public function testBehaviorValidateMethods() {
		$Apple = new Apple();
		$Apple->Behaviors->load('Test');
		$Apple->validate['color'] = 'validateField';

		$result = $Apple->save(['name' => 'Genetically Modified Apple', 'color' => 'Orange']);
		$this->assertEquals(['name', 'color', 'modified', 'created', 'id'], array_keys($result['Apple']));

		$Apple->create();
		$result = $Apple->save(['name' => 'Regular Apple', 'color' => 'Red']);
		$this->assertFalse($result);
	}

/**
 * testBehaviorMethodDispatching method
 *
 * @return void
 */
	public function testBehaviorMethodDispatching() {
		$Apple = new Apple();
		$Apple->Behaviors->load('Test');

		$expected = 'working';
		$this->assertEquals($expected, $Apple->testMethod());
		$this->assertEquals($expected, $Apple->Behaviors->dispatchMethod($Apple, 'testMethod'));

		$result = $Apple->Behaviors->dispatchMethod($Apple, 'wtf');
		$this->assertEquals(['unhandled'], $result);

		$result = $Apple->{'look for the remote'}('in the couch');
		$expected = "Item.name = 'the remote' AND Location.name = 'the couch'";
		$this->assertEquals($expected, $result);

		$result = $Apple->{'look for THE REMOTE'}('in the couch');
		$expected = "Item.name = 'THE REMOTE' AND Location.name = 'the couch'";
		$this->assertEquals($expected, $result, 'Mapped method was lowercased.');
	}

/**
 * testBehaviorMethodDispatchingWithData method
 *
 * @return void
 */
	public function testBehaviorMethodDispatchingWithData() {
		$Apple = new Apple();
		$Apple->Behaviors->load('Test');

		$Apple->set('field', 'value');
		$this->assertTrue($Apple->testData());
		$this->assertTrue($Apple->data['Apple']['field_2']);

		$this->assertTrue($Apple->testData('one', 'two', 'three', 'four', 'five', 'six'));
	}

/**
 * undocumented function
 *
 * @return void
 */
	public function testBindModelCallsInBehaviors() {
		// hasMany
		$Article = new Article();
		$Article->unbindModel(['hasMany' => ['Comment']]);
		$result = $Article->find('first');
		$this->assertFalse(array_key_exists('Comment', $result));

		$Article->Behaviors->load('Test4');
		$result = $Article->find('first');
		$this->assertTrue(array_key_exists('Comment', $result));

		// belongsTo
		$Article->unbindModel(['belongsTo' => ['User']]);
		$result = $Article->find('first');
		$this->assertFalse(array_key_exists('User', $result));

		$Article->Behaviors->load('Test5');
		$result = $Article->find('first');
		$this->assertTrue(array_key_exists('User', $result));

		// hasAndBelongsToMany
		$Article->unbindModel(['hasAndBelongsToMany' => ['Tag']]);
		$result = $Article->find('first');
		$this->assertFalse(array_key_exists('Tag', $result));

		$Article->Behaviors->load('Test6');
		$result = $Article->find('first');
		$this->assertTrue(array_key_exists('Comment', $result));

		// hasOne
		$Comment = new Comment();
		$Comment->unbindModel(['hasOne' => ['Attachment']]);
		$result = $Comment->find('first');
		$this->assertFalse(array_key_exists('Attachment', $result));

		$Comment->Behaviors->load('Test7');
		$result = $Comment->find('first');
		$this->assertTrue(array_key_exists('Attachment', $result));
	}

/**
 * Test attach and detaching
 *
 * @return void
 */
	public function testBehaviorAttachAndDetach() {
		$Sample = new Sample();
		$Sample->actsAs = ['Test3' => ['bar'], 'Test2' => ['foo', 'bar']];
		$Sample->Behaviors->init($Sample->alias, $Sample->actsAs);
		$Sample->Behaviors->load('Test2');
		$Sample->Behaviors->unload('Test3');

		$Sample->Behaviors->trigger('beforeTest', [&$Sample]);
	}

/**
 * test that hasMethod works with basic functions.
 *
 * @return void
 */
	public function testHasMethodBasic() {
		new Sample();
		$Collection = new BehaviorCollection();
		$Collection->init('Sample', ['Test', 'Test2']);

		$this->assertTrue($Collection->hasMethod('testMethod'));
		$this->assertTrue($Collection->hasMethod('resolveMethod'));

		$this->assertFalse($Collection->hasMethod('No method'));
	}

/**
 * test that hasMethod works with mapped methods.
 *
 * @return void
 */
	public function testHasMethodMappedMethods() {
		new Sample();
		$Collection = new BehaviorCollection();
		$Collection->init('Sample', ['Test', 'Test2']);

		$this->assertTrue($Collection->hasMethod('look for the remote in the couch'));
		$this->assertTrue($Collection->hasMethod('mappingRobotOnTheRoof'));
	}

/**
 * test hasMethod returning a 'callback'
 *
 * @return void
 */
	public function testHasMethodAsCallback() {
		new Sample();
		$Collection = new BehaviorCollection();
		$Collection->init('Sample', ['Test', 'Test2']);

		$result = $Collection->hasMethod('testMethod', true);
		$expected = ['Test', 'testMethod'];
		$this->assertEquals($expected, $result);

		$result = $Collection->hasMethod('resolveMethod', true);
		$expected = ['Test2', 'resolveMethod'];
		$this->assertEquals($expected, $result);

		$result = $Collection->hasMethod('mappingRobotOnTheRoof', true);
		$expected = ['Test2', 'mapped', 'mappingRobotOnTheRoof'];
		$this->assertEquals($expected, $result);
	}

/**
 * Test that behavior priority
 *
 * @return void
 */
	public function testBehaviorOrderCallbacks() {
		$model = ClassRegistry::init('Orangutan');
		$model->Behaviors->init('Orangutan', [
			'Second' => ['priority' => 9],
			'Third',
			'First' => ['priority' => 8],
		]);

		$this->assertEmpty($model->called);

		$model->find('first');
		$expected = [
			'FirstBehavior',
			'SecondBehavior',
			'ThirdBehavior',
		];
		$this->assertEquals($expected, $model->called);

		$model->called = [];
		$model->Behaviors->load('Third', ['priority' => 1]);

		$model->find('first');
		$expected = [
			'ThirdBehavior',
			'FirstBehavior',
			'SecondBehavior'
		];
		$this->assertEquals($expected, $model->called);

		$model->called = [];
		$model->Behaviors->load('First');

		$model->find('first');
		$expected = [
			'ThirdBehavior',
			'SecondBehavior',
			'FirstBehavior'
		];
		$this->assertEquals($expected, $model->called);

		$model->called = [];
		$model->Behaviors->unload('Third');

		$model->find('first');
		$expected = [
			'SecondBehavior',
			'FirstBehavior'
		];
		$this->assertEquals($expected, $model->called);

		$model->called = [];
		$model->Behaviors->disable('Second');

		$model->find('first');
		$expected = [
			'FirstBehavior'
		];
		$this->assertEquals($expected, $model->called);

		$model->called = [];
		$model->Behaviors->enable('Second');

		$model->find('first');
		$expected = [
			'SecondBehavior',
			'FirstBehavior'
		];
		$this->assertEquals($expected, $model->called);
	}

}
