<?php
/**
 * TestTaskTest file
 *
 * Test Case for test generation shell task
 *
 * CakePHP :  Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP Project
 * @package       Cake.Test.Case.Console.Command.Task
 * @since         CakePHP v 1.2.0.7726
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('ShellDispatcher', 'Console');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');
App::uses('Shell', 'Console');
App::uses('TestTask', 'Console/Command/Task');
App::uses('TemplateTask', 'Console/Command/Task');
App::uses('Controller', 'Controller');
App::uses('Model', 'Model');

/**
 * Test Article model
 *
 * @package       Cake.Test.Case.Console.Command.Task
 */
class TestTaskArticle extends Model {

/**
 * Table name to use
 *
 * @var string
 */
	public $useTable = 'articles';

/**
 * HasMany Associations
 *
 * @var array
 */
	public $hasMany = [
		'Comment' => [
			'className' => 'TestTask.TestTaskComment',
			'foreignKey' => 'article_id',
		]
	];

/**
 * Has and Belongs To Many Associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = [
		'Tag' => [
			'className' => 'TestTaskTag',
			'joinTable' => 'articles_tags',
			'foreignKey' => 'article_id',
			'associationForeignKey' => 'tag_id'
		]
	];

/**
 * Example public method
 *
 * @return void
 */
	public function doSomething() {
	}

/**
 * Example Secondary public method
 *
 * @return void
 */
	public function doSomethingElse() {
	}

/**
 * Example protected method
 *
 * @return void
 */
	protected function _innerMethod() {
	}

}

/**
 * Tag Testing Model
 *
 * @package       Cake.Test.Case.Console.Command.Task
 */
class TestTaskTag extends Model {

/**
 * Table name
 *
 * @var string
 */
	public $useTable = 'tags';

/**
 * Has and Belongs To Many Associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = [
		'Article' => [
			'className' => 'TestTaskArticle',
			'joinTable' => 'articles_tags',
			'foreignKey' => 'tag_id',
			'associationForeignKey' => 'article_id'
		]
	];
}

/**
 * Simulated plugin
 *
 * @package       Cake.Test.Case.Console.Command.Task
 */
class TestTaskAppModel extends Model {
}

/**
 * Testing AppMode (TaskComment)
 *
 * @package       Cake.Test.Case.Console.Command.Task
 */
class TestTaskComment extends TestTaskAppModel {

/**
 * Table name
 *
 * @var string
 */
	public $useTable = 'comments';

/**
 * Belongs To Associations
 *
 * @var array
 */
	public $belongsTo = [
		'Article' => [
			'className' => 'TestTaskArticle',
			'foreignKey' => 'article_id',
		]
	];
}

/**
 * Test Task Comments Controller
 *
 * @package       Cake.Test.Case.Console.Command.Task
 */
class TestTaskCommentsController extends Controller {

/**
 * Models to use
 *
 * @var array
 */
	public $uses = ['TestTaskComment', 'TestTaskTag'];
}

/**
 * TestTaskTest class
 *
 * @package       Cake.Test.Case.Console.Command.Task
 */
class TestTaskTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var string
 */
	public $fixtures = ['core.article', 'core.comment', 'core.articles_tag', 'core.tag'];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$out = $this->getMock('ConsoleOutput', [], [], '', false);
		$in = $this->getMock('ConsoleInput', [], [], '', false);

		$this->Task = $this->getMock('TestTask',
			['in', 'err', 'createFile', '_stop', 'isLoadableClass'],
			[$out, $out, $in]
		);
		$this->Task->name = 'Test';
		$this->Task->Template = new TemplateTask($out, $out, $in);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Task);
		CakePlugin::unload();
	}

/**
 * Test that file path generation doesn't continuously append paths.
 *
 * @return void
 */
	public function testFilePathGenerationModelRepeated() {
		$this->Task->expects($this->never())->method('err');
		$this->Task->expects($this->never())->method('_stop');

		$file = TESTS . 'Case' . DS . 'Model' . DS . 'MyClassTest.php';

		$this->Task->expects($this->at(1))->method('createFile')
			->with($file, $this->anything());

		$this->Task->expects($this->at(3))->method('createFile')
			->with($file, $this->anything());

		$file = TESTS . 'Case' . DS . 'Controller' . DS . 'CommentsControllerTest.php';
		$this->Task->expects($this->at(5))->method('createFile')
			->with($file, $this->anything());

		$this->Task->bake('Model', 'MyClass');
		$this->Task->bake('Model', 'MyClass');
		$this->Task->bake('Controller', 'Comments');
	}

/**
 * Test that method introspection pulls all relevant non parent class
 * methods into the test case.
 *
 * @return void
 */
	public function testMethodIntrospection() {
		$result = $this->Task->getTestableMethods('TestTaskArticle');
		$expected = ['dosomething', 'dosomethingelse'];
		$this->assertEquals($expected, array_map('strtolower', $result));
	}

/**
 * test that the generation of fixtures works correctly.
 *
 * @return void
 */
	public function testFixtureArrayGenerationFromModel() {
		$subject = ClassRegistry::init('TestTaskArticle');
		$result = $this->Task->generateFixtureList($subject);
		$expected = ['plugin.test_task.test_task_comment', 'app.articles_tags',
			'app.test_task_article', 'app.test_task_tag'];

		$this->assertEquals(sort($expected), sort($result));
	}

/**
 * test that the generation of fixtures works correctly.
 *
 * @return void
 */
	public function testFixtureArrayGenerationFromController() {
		$subject = new TestTaskCommentsController();
		$result = $this->Task->generateFixtureList($subject);
		$expected = ['plugin.test_task.test_task_comment', 'app.articles_tags',
			'app.test_task_article', 'app.test_task_tag'];

		$this->assertEquals(sort($expected), sort($result));
	}

/**
 * test user interaction to get object type
 *
 * @return void
 */
	public function testGetObjectType() {
		$this->Task->expects($this->once())->method('_stop');
		$this->Task->expects($this->at(0))->method('in')->will($this->returnValue('q'));
		$this->Task->expects($this->at(2))->method('in')->will($this->returnValue(2));

		$this->Task->getObjectType();

		$result = $this->Task->getObjectType();
		$this->assertEquals($this->Task->classTypes['Controller'], $result);
	}

/**
 * creating test subjects should clear the registry so the registry is always fresh
 *
 * @return void
 */
	public function testRegistryClearWhenBuildingTestObjects() {
		ClassRegistry::flush();
		$model = ClassRegistry::init('TestTaskComment');
		$model->bindModel([
			'belongsTo' => [
				'Random' => [
					'className' => 'TestTaskArticle',
					'foreignKey' => 'article_id',
				]
			]
		]);
		$keys = ClassRegistry::keys();
		$this->assertTrue(in_array('test_task_comment', $keys));
		$this->Task->buildTestSubject('Model', 'TestTaskComment');

		$keys = ClassRegistry::keys();
		$this->assertFalse(in_array('random', $keys));
	}

/**
 * test that getClassName returns the user choice as a class name.
 *
 * @return void
 */
	public function testGetClassName() {
		$objects = App::objects('model');
		$this->skipIf(empty($objects), 'No models in app.');

		$this->Task->expects($this->at(0))->method('in')->will($this->returnValue('MyCustomClass'));
		$this->Task->expects($this->at(1))->method('in')->will($this->returnValue(1));

		$result = $this->Task->getClassName('Model');
		$this->assertEquals('MyCustomClass', $result);

		$result = $this->Task->getClassName('Model');
		$options = App::objects('model');
		$this->assertEquals($options[0], $result);
	}

/**
 * Test the user interaction for defining additional fixtures.
 *
 * @return void
 */
	public function testGetUserFixtures() {
		$this->Task->expects($this->at(0))->method('in')->will($this->returnValue('y'));
		$this->Task->expects($this->at(1))->method('in')
			->will($this->returnValue('app.pizza, app.topping, app.side_dish'));

		$result = $this->Task->getUserFixtures();
		$expected = ['app.pizza', 'app.topping', 'app.side_dish'];
		$this->assertEquals($expected, $result);
	}

/**
 * test that resolving class names works
 *
 * @return void
 */
	public function testGetRealClassname() {
		$result = $this->Task->getRealClassname('Model', 'Post');
		$this->assertEquals('Post', $result);

		$result = $this->Task->getRealClassname('Controller', 'Posts');
		$this->assertEquals('PostsController', $result);

		$result = $this->Task->getRealClassname('Controller', 'PostsController');
		$this->assertEquals('PostsController', $result);

		$result = $this->Task->getRealClassname('Controller', 'AlertTypes');
		$this->assertEquals('AlertTypesController', $result);

		$result = $this->Task->getRealClassname('Helper', 'Form');
		$this->assertEquals('FormHelper', $result);

		$result = $this->Task->getRealClassname('Helper', 'FormHelper');
		$this->assertEquals('FormHelper', $result);

		$result = $this->Task->getRealClassname('Behavior', 'Containable');
		$this->assertEquals('ContainableBehavior', $result);

		$result = $this->Task->getRealClassname('Behavior', 'ContainableBehavior');
		$this->assertEquals('ContainableBehavior', $result);

		$result = $this->Task->getRealClassname('Component', 'Auth');
		$this->assertEquals('AuthComponent', $result);
	}

/**
 * test baking files. The conditionally run tests are known to fail in PHP4
 * as PHP4 class names are all lower case, breaking the plugin path inflection.
 *
 * @return void
 */
	public function testBakeModelTest() {
		$this->Task->expects($this->once())->method('createFile')->will($this->returnValue(true));
		$this->Task->expects($this->once())->method('isLoadableClass')->will($this->returnValue(true));

		$result = $this->Task->bake('Model', 'TestTaskArticle');

		$this->assertContains("App::uses('TestTaskArticle', 'Model')", $result);
		$this->assertContains('class TestTaskArticleTest extends CakeTestCase', $result);

		$this->assertContains('function setUp()', $result);
		$this->assertContains("\$this->TestTaskArticle = ClassRegistry::init('TestTaskArticle')", $result);

		$this->assertContains('function tearDown()', $result);
		$this->assertContains('unset($this->TestTaskArticle)', $result);

		$this->assertContains('function testDoSomething()', $result);
		$this->assertContains('function testDoSomethingElse()', $result);
		$this->assertContains('$this->markTestIncomplete(\'testDoSomething not implemented.\')', $result);
		$this->assertContains('$this->markTestIncomplete(\'testDoSomethingElse not implemented.\')', $result);

		$this->assertContains("'app.test_task_article'", $result);
		$this->assertContains("'app.test_task_comment'", $result);
		$this->assertContains("'app.test_task_tag'", $result);
		$this->assertContains("'app.articles_tag'", $result);
	}

/**
 * test baking controller test files
 *
 * @return void
 */
	public function testBakeControllerTest() {
		$this->Task->expects($this->once())->method('createFile')->will($this->returnValue(true));
		$this->Task->expects($this->once())->method('isLoadableClass')->will($this->returnValue(true));

		$result = $this->Task->bake('Controller', 'TestTaskComments');

		$this->assertContains("App::uses('TestTaskCommentsController', 'Controller')", $result);
		$this->assertContains('class TestTaskCommentsControllerTest extends ControllerTestCase', $result);

		$this->assertNotContains('function setUp()', $result);
		$this->assertNotContains("\$this->TestTaskComments = new TestTaskCommentsController()", $result);
		$this->assertNotContains("\$this->TestTaskComments->constructClasses()", $result);

		$this->assertNotContains('function tearDown()', $result);
		$this->assertNotContains('unset($this->TestTaskComments)', $result);

		$this->assertContains("'app.test_task_article'", $result);
		$this->assertContains("'app.test_task_comment'", $result);
		$this->assertContains("'app.test_task_tag'", $result);
		$this->assertContains("'app.articles_tag'", $result);
	}

/**
 * test baking component test files,
 *
 * @return void
 */
	public function testBakeComponentTest() {
		$this->Task->expects($this->once())->method('createFile')->will($this->returnValue(true));

		$result = $this->Task->bake('Component', 'Example');

		$this->assertContains("App::uses('Component', 'Controller')", $result);
		$this->assertContains("App::uses('ComponentCollection', 'Controller')", $result);
		$this->assertContains("App::uses('ExampleComponent', 'Controller/Component')", $result);
		$this->assertContains('class ExampleComponentTest extends CakeTestCase', $result);

		$this->assertContains('function setUp()', $result);
		$this->assertContains("\$Collection = new ComponentCollection()", $result);
		$this->assertContains("\$this->Example = new ExampleComponent(\$Collection)", $result);

		$this->assertContains('function tearDown()', $result);
		$this->assertContains('unset($this->Example)', $result);
	}

/**
 * test baking behavior test files,
 *
 * @return void
 */
	public function testBakeBehaviorTest() {
		$this->Task->expects($this->once())->method('createFile')->will($this->returnValue(true));

		$result = $this->Task->bake('Behavior', 'Example');

		$this->assertContains("App::uses('ExampleBehavior', 'Model/Behavior')", $result);
		$this->assertContains('class ExampleBehaviorTest extends CakeTestCase', $result);

		$this->assertContains('function setUp()', $result);
		$this->assertContains("\$this->Example = new ExampleBehavior()", $result);

		$this->assertContains('function tearDown()', $result);
		$this->assertContains('unset($this->Example)', $result);
	}

/**
 * test baking helper test files,
 *
 * @return void
 */
	public function testBakeHelperTest() {
		$this->Task->expects($this->once())->method('createFile')->will($this->returnValue(true));

		$result = $this->Task->bake('Helper', 'Example');

		$this->assertContains("App::uses('ExampleHelper', 'View/Helper')", $result);
		$this->assertContains('class ExampleHelperTest extends CakeTestCase', $result);

		$this->assertContains('function setUp()', $result);
		$this->assertContains("\$View = new View()", $result);
		$this->assertContains("\$this->Example = new ExampleHelper(\$View)", $result);

		$this->assertContains('function tearDown()', $result);
		$this->assertContains('unset($this->Example)', $result);
	}

/**
 * test Constructor generation ensure that constructClasses is called for controllers
 *
 * @return void
 */
	public function testGenerateConstructor() {
		$result = $this->Task->generateConstructor('controller', 'PostsController', null);
		$expected = ['', '', ''];
		$this->assertEquals($expected, $result);

		$result = $this->Task->generateConstructor('model', 'Post', null);
		$expected = ['', "ClassRegistry::init('Post');\n", ''];
		$this->assertEquals($expected, $result);

		$result = $this->Task->generateConstructor('helper', 'FormHelper', null);
		$expected = ["\$View = new View();\n", "new FormHelper(\$View);\n", ''];
		$this->assertEquals($expected, $result);
	}

/**
 * Test generateUses()
 *
 * @return void
 */
	public function testGenerateUses() {
		$result = $this->Task->generateUses('model', 'Model', 'Post');
		$expected = [
			['Post', 'Model']
		];
		$this->assertEquals($expected, $result);

		$result = $this->Task->generateUses('controller', 'Controller', 'PostsController');
		$expected = [
			['PostsController', 'Controller']
		];
		$this->assertEquals($expected, $result);

		$result = $this->Task->generateUses('helper', 'View/Helper', 'FormHelper');
		$expected = [
			['View', 'View'],
			['Helper', 'View'],
			['FormHelper', 'View/Helper'],
		];
		$this->assertEquals($expected, $result);

		$result = $this->Task->generateUses('component', 'Controller/Component', 'AuthComponent');
		$expected = [
			['ComponentCollection', 'Controller'],
			['Component', 'Controller'],
			['AuthComponent', 'Controller/Component']
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test that mock class generation works for the appropriate classes
 *
 * @return void
 */
	public function testMockClassGeneration() {
		$result = $this->Task->hasMockClass('controller');
		$this->assertTrue($result);
	}

/**
 * test bake() with a -plugin param
 *
 * @return void
 */
	public function testBakeWithPlugin() {
		$this->Task->plugin = 'TestTest';

		//fake plugin path
		CakePlugin::load('TestTest', ['path' => APP . 'Plugin' . DS . 'TestTest' . DS]);
		$path = APP . 'Plugin' . DS . 'TestTest' . DS . 'Test' . DS . 'Case' . DS . 'View' . DS . 'Helper' . DS . 'FormHelperTest.php';
		$this->Task->expects($this->once())->method('createFile')
			->with($path, $this->anything());

		$this->Task->bake('Helper', 'Form');
		CakePlugin::unload();
	}

/**
 * test interactive with plugins lists from the plugin
 *
 * @return void
 */
	public function testInteractiveWithPlugin() {
		$testApp = CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS;
		App::build([
			'Plugin' => [$testApp]
		], App::RESET);
		CakePlugin::load('TestPlugin');

		$this->Task->plugin = 'TestPlugin';
		$path = $testApp . 'TestPlugin' . DS . 'Test' . DS . 'Case' . DS . 'View' . DS . 'Helper' . DS . 'OtherHelperTest.php';
		$this->Task->expects($this->any())
			->method('in')
			->will($this->onConsecutiveCalls(
				5, //helper
				1 //OtherHelper
			));

		$this->Task->expects($this->once())
			->method('createFile')
			->with($path, $this->anything());

		$this->Task->stdout->expects($this->at(21))
			->method('write')
			->with('1. OtherHelperHelper');

		$this->Task->execute();
	}

	public static function caseFileNameProvider() {
		return [
			['Model', 'Post', 'Case' . DS . 'Model' . DS . 'PostTest.php'],
			['Helper', 'Form', 'Case' . DS . 'View' . DS . 'Helper' . DS . 'FormHelperTest.php'],
			['Controller', 'Posts', 'Case' . DS . 'Controller' . DS . 'PostsControllerTest.php'],
			['Behavior', 'Containable', 'Case' . DS . 'Model' . DS . 'Behavior' . DS . 'ContainableBehaviorTest.php'],
			['Component', 'Auth', 'Case' . DS . 'Controller' . DS . 'Component' . DS . 'AuthComponentTest.php'],
			['model', 'Post', 'Case' . DS . 'Model' . DS . 'PostTest.php'],
			['helper', 'Form', 'Case' . DS . 'View' . DS . 'Helper' . DS . 'FormHelperTest.php'],
			['controller', 'Posts', 'Case' . DS . 'Controller' . DS . 'PostsControllerTest.php'],
			['behavior', 'Containable', 'Case' . DS . 'Model' . DS . 'Behavior' . DS . 'ContainableBehaviorTest.php'],
			['component', 'Auth', 'Case' . DS . 'Controller' . DS . 'Component' . DS . 'AuthComponentTest.php'],
		];
	}

/**
 * Test filename generation for each type + plugins
 *
 * @dataProvider caseFileNameProvider
 * @return void
 */
	public function testTestCaseFileName($type, $class, $expected) {
		$this->Task->path = DS . 'my' . DS . 'path' . DS . 'tests' . DS;

		$result = $this->Task->testCaseFileName($type, $class);
		$expected = $this->Task->path . $expected;
		$this->assertEquals($expected, $result);
	}

/**
 * Test filename generation for plugins.
 *
 * @return void
 */
	public function testTestCaseFileNamePlugin() {
		$this->Task->path = DS . 'my' . DS . 'path' . DS . 'tests' . DS;

		CakePlugin::load('TestTest', ['path' => APP . 'Plugin' . DS . 'TestTest' . DS]);
		$this->Task->plugin = 'TestTest';
		$result = $this->Task->testCaseFileName('Model', 'Post');
		$expected = APP . 'Plugin' . DS . 'TestTest' . DS . 'Test' . DS . 'Case' . DS . 'Model' . DS . 'PostTest.php';
		$this->assertEquals($expected, $result);
	}

/**
 * test execute with a type defined
 *
 * @return void
 */
	public function testExecuteWithOneArg() {
		$this->Task->args[0] = 'Model';
		$this->Task->expects($this->at(0))->method('in')->will($this->returnValue('TestTaskTag'));
		$this->Task->expects($this->once())->method('isLoadableClass')->will($this->returnValue(true));
		$this->Task->expects($this->once())->method('createFile')
			->with(
				$this->anything(),
				$this->stringContains('class TestTaskTagTest extends CakeTestCase')
			);
		$this->Task->execute();
	}

/**
 * test execute with type and class name defined
 *
 * @return void
 */
	public function testExecuteWithTwoArgs() {
		$this->Task->args = ['Model', 'TestTaskTag'];
		$this->Task->expects($this->at(0))->method('in')->will($this->returnValue('TestTaskTag'));
		$this->Task->expects($this->once())->method('createFile')
			->with(
				$this->anything(),
				$this->stringContains('class TestTaskTagTest extends CakeTestCase')
			);
		$this->Task->expects($this->any())->method('isLoadableClass')->will($this->returnValue(true));
		$this->Task->execute();
	}

/**
 * test execute with type and class name defined and lower case.
 *
 * @return void
 */
	public function testExecuteWithTwoArgsLowerCase() {
		$this->Task->args = ['model', 'TestTaskTag'];
		$this->Task->expects($this->at(0))->method('in')->will($this->returnValue('TestTaskTag'));
		$this->Task->expects($this->once())->method('createFile')
			->with(
				$this->anything(),
				$this->stringContains('class TestTaskTagTest extends CakeTestCase')
			);
		$this->Task->expects($this->any())->method('isLoadableClass')->will($this->returnValue(true));
		$this->Task->execute();
	}

/**
 * Data provider for mapType() tests.
 *
 * @return array
 */
	public static function mapTypeProvider() {
		return [
			['controller', null, 'Controller'],
			['Controller', null, 'Controller'],
			['component', null, 'Controller/Component'],
			['Component', null, 'Controller/Component'],
			['model', null, 'Model'],
			['Model', null, 'Model'],
			['behavior', null, 'Model/Behavior'],
			['Behavior', null, 'Model/Behavior'],
			['helper', null, 'View/Helper'],
			['Helper', null, 'View/Helper'],
			['Helper', 'DebugKit', 'DebugKit.View/Helper'],
		];
	}

/**
 * Test that mapType returns the correct package names.
 *
 * @dataProvider mapTypeProvider
 * @return void
 */
	public function testMapType($original, $plugin, $expected) {
		$this->assertEquals($expected, $this->Task->mapType($original, $plugin));
	}
}
