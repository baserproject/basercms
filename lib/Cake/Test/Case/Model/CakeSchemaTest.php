<?php
/**
 * Test for Schema database management
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.Test.Case.Model
 * @since         CakePHP(tm) v 1.2.0.5550
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('CakeSchema', 'Model');
App::uses('CakeTestFixture', 'TestSuite/Fixture');

/**
 * Test for Schema database management
 *
 * @package       Cake.Test.Case.Model
 */
class MyAppSchema extends CakeSchema {

/**
 * connection property
 *
 * @var string
 */
	public $connection = 'test';

/**
 * comments property
 *
 * @var array
 */
	public $comments = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
		'post_id' => ['type' => 'integer', 'null' => false, 'default' => 0],
		'user_id' => ['type' => 'integer', 'null' => false],
		'title' => ['type' => 'string', 'null' => false, 'length' => 100],
		'comment' => ['type' => 'text', 'null' => false, 'default' => null],
		'published' => ['type' => 'string', 'null' => true, 'default' => 'N', 'length' => 1],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => true]],
	];

/**
 * posts property
 *
 * @var array
 */
	public $posts = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
		'author_id' => ['type' => 'integer', 'null' => true, 'default' => ''],
		'title' => ['type' => 'string', 'null' => false, 'default' => 'Title'],
		'body' => ['type' => 'text', 'null' => true, 'default' => null],
		'summary' => ['type' => 'text', 'null' => true],
		'published' => ['type' => 'string', 'null' => true, 'default' => 'Y', 'length' => 1],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => true]],
	];

/**
 * _foo property
 *
 * @var array
 */
	protected $_foo = ['bar'];

/**
 * getVar method
 *
 * @param string $var Name of var
 * @return mixed
 */
	public function getVar($var) {
		if (!isset($this->$var)) {
			return null;
		}
		return $this->$var;
	}

}

/**
 * TestAppSchema class
 *
 * @package       Cake.Test.Case.Model
 */
class TestAppSchema extends CakeSchema {

/**
 * name property
 *
 * @var string
 */
	public $name = 'MyApp';

/**
 * comments property
 *
 * @var array
 */
	public $comments = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
		'article_id' => ['type' => 'integer', 'null' => false],
		'user_id' => ['type' => 'integer', 'null' => false],
		'comment' => ['type' => 'text', 'null' => true, 'default' => null],
		'published' => ['type' => 'string', 'null' => true, 'default' => 'N', 'length' => 1],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => true]],
		'tableParameters' => [],
	];

/**
 * posts property
 *
 * @var array
 */
	public $posts = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
		'author_id' => ['type' => 'integer', 'null' => false],
		'title' => ['type' => 'string', 'null' => false],
		'body' => ['type' => 'text', 'null' => true, 'default' => null],
		'published' => ['type' => 'string', 'null' => true, 'default' => 'N', 'length' => 1],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => true]],
		'tableParameters' => [],
	];

/**
 * posts_tags property
 *
 * @var array
 */
	public $posts_tags = [
		'post_id' => ['type' => 'integer', 'null' => false, 'key' => 'primary'],
		'tag_id' => ['type' => 'string', 'null' => false, 'key' => 'primary'],
		'indexes' => ['posts_tag' => ['column' => ['tag_id', 'post_id'], 'unique' => 1]],
		'tableParameters' => []
	];

/**
 * tags property
 *
 * @var array
 */
	public $tags = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
		'tag' => ['type' => 'string', 'null' => false],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'updated' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => true]],
		'tableParameters' => []
	];

/**
 * datatypes property
 *
 * @var array
 */
	public $datatypes = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
		'float_field' => ['type' => 'float', 'null' => false, 'length' => '5,2', 'default' => ''],
		'decimal_field' => ['type' => 'decimal', 'length' => '6,3', 'default' => '0.000'],
		'huge_int' => ['type' => 'biginteger'],
		'bool' => ['type' => 'boolean', 'null' => false, 'default' => false],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => true]],
		'tableParameters' => []
	];

/**
 * setup method
 *
 * @param mixed $version
 * @return void
 */
	public function setup($version) {
	}

/**
 * teardown method
 *
 * @param mixed $version
 * @return void
 */
	public function teardown($version) {
	}

}

/**
 * SchemaPost class
 *
 * @package       Cake.Test.Case.Model
 */
class SchemaPost extends CakeTestModel {

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'posts';

/**
 * hasMany property
 *
 * @var array
 */
	public $hasMany = ['SchemaComment'];

/**
 * hasAndBelongsToMany property
 *
 * @var array
 */
	public $hasAndBelongsToMany = ['SchemaTag'];
}

/**
 * SchemaComment class
 *
 * @package       Cake.Test.Case.Model
 */
class SchemaComment extends CakeTestModel {

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'comments';

/**
 * belongsTo property
 *
 * @var array
 */
	public $belongsTo = ['SchemaPost'];
}

/**
 * SchemaTag class
 *
 * @package       Cake.Test.Case.Model
 */
class SchemaTag extends CakeTestModel {

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'tags';

/**
 * hasAndBelongsToMany property
 *
 * @var array
 */
	public $hasAndBelongsToMany = ['SchemaPost'];
}

/**
 * SchemaDatatype class
 *
 * @package       Cake.Test.Case.Model
 */
class SchemaDatatype extends CakeTestModel {

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'datatypes';
}

/**
 * Testdescribe class
 *
 * This class is defined purely to inherit the cacheSources variable otherwise
 * testSchemaCreateTable will fail if listSources has already been called and
 * its source cache populated - I.e. if the test is run within a group
 *
 * @uses          CakeTestModel
 * @package       Cake.Test.Case.Model
 */
class Testdescribe extends CakeTestModel {
}

/**
 * SchemaCrossDatabase class
 *
 * @package       Cake.Test.Case.Model
 */
class SchemaCrossDatabase extends CakeTestModel {

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'cross_database';

/**
 * useDbConfig property
 *
 * @var string
 */
	public $useDbConfig = 'test2';
}

/**
 * SchemaCrossDatabaseFixture class
 *
 * @package       Cake.Test.Case.Model
 */
class SchemaCrossDatabaseFixture extends CakeTestFixture {

/**
 * name property
 *
 * @var string
 */
	public $name = 'CrossDatabase';

/**
 * table property
 *
 * @var string
 */
	public $table = 'cross_database';

/**
 * fields property
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'key' => 'primary'],
		'name' => 'string'
	];

/**
 * records property
 *
 * @var array
 */
	public $records = [
		['id' => 1, 'name' => 'First'],
		['id' => 2, 'name' => 'Second'],
	];
}

/**
 * SchemaPrefixAuthUser class
 *
 * @package       Cake.Test.Case.Model
 */
class SchemaPrefixAuthUser extends CakeTestModel {

/**
 * table prefix
 *
 * @var string
 */
	public $tablePrefix = 'auth_';

/**
 * useTable
 *
 * @var string
 */
	public $useTable = 'users';
}

/**
 * CakeSchemaTest
 *
 * @package       Cake.Test.Case.Model
 */
class CakeSchemaTest extends CakeTestCase {

/**
 * fixtures property
 *
 * @var array
 */
	public $fixtures = [
		'core.post', 'core.tag', 'core.posts_tag', 'core.test_plugin_comment',
		'core.datatype', 'core.auth_user', 'core.author',
		'core.test_plugin_article', 'core.user', 'core.comment',
		'core.prefix_test'
	];

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		ConnectionManager::getDataSource('test')->cacheSources = false;
		$this->Schema = new TestAppSchema();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		if (file_exists(TMP . 'tests' . DS . 'schema.php')) {
			unlink(TMP . 'tests' . DS . 'schema.php');
		}
		unset($this->Schema);
		CakePlugin::unload();
	}

/**
 * testSchemaName method
 *
 * @return void
 */
	public function testSchemaName() {
		$Schema = new CakeSchema();
		$this->assertEquals('App', $Schema->name);
	}

/**
 * testSchemaRead method
 *
 * @return void
 */
	public function testSchemaRead() {
		$read = $this->Schema->read([
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => ['SchemaPost', 'SchemaComment', 'SchemaTag', 'SchemaDatatype']
		]);
		unset($read['tables']['missing']);

		$expected = ['comments', 'datatypes', 'posts', 'posts_tags', 'tags'];
		foreach ($expected as $table) {
			$this->assertTrue(isset($read['tables'][$table]), 'Missing table ' . $table);
		}
		foreach ($this->Schema->tables as $table => $fields) {
			$this->assertEquals(array_keys($fields), array_keys($read['tables'][$table]));
		}

		if (isset($read['tables']['datatypes']['float_field']['length'])) {
			$this->assertEquals(
				$read['tables']['datatypes']['float_field']['length'],
				$this->Schema->tables['datatypes']['float_field']['length']
			);
		}

		$this->assertEquals(
			$read['tables']['datatypes']['float_field']['type'],
			$this->Schema->tables['datatypes']['float_field']['type']
		);

		$this->assertEquals(
			$read['tables']['datatypes']['float_field']['null'],
			$this->Schema->tables['datatypes']['float_field']['null']
		);

		$db = ConnectionManager::getDataSource('test');
		$config = $db->config;
		$config['prefix'] = 'schema_test_prefix_';
		ConnectionManager::create('schema_prefix', $config);
		$read = $this->Schema->read(['connection' => 'schema_prefix', 'models' => false]);
		$this->assertTrue(empty($read['tables']));

		$read = $this->Schema->read([
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => ['SchemaComment', 'SchemaTag', 'SchemaPost']
		]);
		$this->assertFalse(isset($read['tables']['missing']['posts_tags']), 'Join table marked as missing');
	}

/**
 * testSchemaReadWithAppModel method
 *
 * @return void
 */
	public function testSchemaReadWithAppModel() {
		$connections = ConnectionManager::enumConnectionObjects();
		ConnectionManager::drop('default');
		ConnectionManager::create('default', $connections['test']);
		try {
			$this->Schema->read([
				'connection' => 'default',
				'name' => 'TestApp',
				'models' => ['AppModel']
			]);
		} catch(MissingTableException $mte) {
			ConnectionManager::drop('default');
			$this->fail($mte->getMessage());
		}
		ConnectionManager::drop('default');
	}

/**
 * testSchemaReadWithOddTablePrefix method
 *
 * @return void
 */
	public function testSchemaReadWithOddTablePrefix() {
		$config = ConnectionManager::getDataSource('test')->config;
		$this->skipIf(!empty($config['prefix']), 'This test can not be executed with datasource prefix set.');

		$SchemaPost = ClassRegistry::init('SchemaPost');
		$SchemaPost->tablePrefix = 'po';
		$SchemaPost->useTable = 'sts';
		$read = $this->Schema->read([
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => ['SchemaPost']
		]);

		$this->assertFalse(isset($read['tables']['missing']['posts']), 'Posts table was not read from tablePrefix');
	}

/**
 * test read() with tablePrefix properties.
 *
 * @return void
 */
	public function testSchemaReadWithTablePrefix() {
		$config = ConnectionManager::getDataSource('test')->config;
		$this->skipIf(!empty($config['prefix']), 'This test can not be executed with datasource prefix set.');

		$Schema = new CakeSchema();
		$read = $Schema->read([
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => ['SchemaPrefixAuthUser']
		]);
		unset($read['tables']['missing']);
		$this->assertTrue(isset($read['tables']['auth_users']), 'auth_users key missing %s');
	}

/**
 * test reading schema with config prefix.
 *
 * @return void
 */
	public function testSchemaReadWithConfigPrefix() {
		$this->skipIf($this->db instanceof Sqlite, 'Cannot open 2 connections to Sqlite');

		$db = ConnectionManager::getDataSource('test');
		$config = $db->config;
		$this->skipIf(!empty($config['prefix']), 'This test can not be executed with datasource prefix set.');

		$config['prefix'] = 'schema_test_prefix_';
		ConnectionManager::create('schema_prefix', $config);
		$read = $this->Schema->read(['connection' => 'schema_prefix', 'models' => false]);
		$this->assertTrue(empty($read['tables']));

		$config['prefix'] = 'prefix_';
		ConnectionManager::create('schema_prefix2', $config);
		$read = $this->Schema->read([
			'connection' => 'schema_prefix2',
			'name' => 'TestApp',
			'models' => false]);
		$this->assertTrue(isset($read['tables']['prefix_tests']));
	}

/**
 * test reading schema from plugins.
 *
 * @return void
 */
	public function testSchemaReadWithPlugins() {
		App::objects('model', null, false);
		App::build([
			'Plugin' => [CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS]
		]);
		CakePlugin::load('TestPlugin');

		$Schema = new CakeSchema();
		$Schema->plugin = 'TestPlugin';
		$read = $Schema->read([
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => true
		]);
		unset($read['tables']['missing']);
		$this->assertTrue(isset($read['tables']['auth_users']));
		$this->assertTrue(isset($read['tables']['authors']));
		$this->assertTrue(isset($read['tables']['test_plugin_comments']));
		$this->assertTrue(isset($read['tables']['posts']));
		$this->assertTrue(count($read['tables']) >= 4);

		App::build();
	}

/**
 * test reading schema with tables from another database.
 *
 * @return void
 */
	public function testSchemaReadWithCrossDatabase() {
		$config = ConnectionManager::enumConnectionObjects();
		$this->skipIf(
			!isset($config['test']) || !isset($config['test2']),
			'Primary and secondary test databases not configured, ' .
			'skipping cross-database join tests. ' .
			'To run these tests, you must define $test and $test2 in your database configuration.'
		);

		$db = ConnectionManager::getDataSource('test2');
		$fixture = new SchemaCrossDatabaseFixture();
		$fixture->create($db);
		$fixture->insert($db);

		$read = $this->Schema->read([
			'connection' => 'test',
			'name' => 'TestApp',
			'models' => ['SchemaCrossDatabase', 'SchemaPost']
		]);
		$this->assertTrue(isset($read['tables']['posts']));
		$this->assertFalse(isset($read['tables']['cross_database']), 'Cross database should not appear');
		$this->assertFalse(isset($read['tables']['missing']['cross_database']), 'Cross database should not appear');

		$read = $this->Schema->read([
			'connection' => 'test2',
			'name' => 'TestApp',
			'models' => ['SchemaCrossDatabase', 'SchemaPost']
		]);
		$this->assertFalse(isset($read['tables']['posts']), 'Posts should not appear');
		$this->assertFalse(isset($read['tables']['posts']), 'Posts should not appear');
		$this->assertTrue(isset($read['tables']['cross_database']));

		$fixture->drop($db);
	}

/**
 * test that tables are generated correctly
 *
 * @return void
 */
	public function testGenerateTable() {
		$posts = [
			'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
			'author_id' => ['type' => 'integer', 'null' => false],
			'title' => ['type' => 'string', 'null' => false],
			'body' => ['type' => 'text', 'null' => true, 'default' => null],
			'published' => ['type' => 'string', 'null' => true, 'default' => 'N', 'length' => 1],
			'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
			'updated' => ['type' => 'datetime', 'null' => true, 'default' => null],
			'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => true]],
		];
		$result = $this->Schema->generateTable('posts', $posts);
		$this->assertRegExp('/public \$posts/', $result);

		$posts = [
			'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
			'author_id' => ['type' => 'integer', 'null' => false],
			'title' => ['type' => 'string', 'null' => false],
			'body' => ['type' => 'text', 'null' => true, 'default' => null],
			'published' => ['type' => 'string', 'null' => true, 'default' => 'N', 'length' => 1],
			'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
			'updated' => ['type' => 'datetime', 'null' => true, 'default' => null],
			'indexes' => [
				'PRIMARY' => ['column' => 'id', 'unique' => true],
				'MyFtIndex' => ['column' => ['title', 'body'], 'type' => 'fulltext']
			]
		];
		$result = $this->Schema->generateTable('fields', $posts);
		$this->assertRegExp('/public \$fields/', $result);
		$this->assertRegExp('/\'type\' \=\> \'fulltext\'/', $result);
	}

/**
 * testSchemaWrite method
 *
 * @return void
 */
	public function testSchemaWrite() {
		$write = $this->Schema->write([
			'name' => 'MyOtherApp',
			'tables' => $this->Schema->tables,
			'path' => TMP . 'tests'
		]);
		$file = file_get_contents(TMP . 'tests' . DS . 'schema.php');
		$this->assertEquals($write, $file);

		require_once TMP . 'tests' . DS . 'schema.php';
		$OtherSchema = new MyOtherAppSchema();
		$this->assertEquals($this->Schema->tables, $OtherSchema->tables);
	}

/**
 * testSchemaComparison method
 *
 * @return void
 */
	public function testSchemaComparison() {
		$New = new MyAppSchema();
		$compare = $New->compare($this->Schema);
		$expected = [
			'comments' => [
				'add' => [
					'post_id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'after' => 'id'],
					'title' => ['type' => 'string', 'null' => false, 'length' => 100, 'after' => 'user_id'],
				],
				'drop' => [
					'article_id' => ['type' => 'integer', 'null' => false],
					'tableParameters' => [],
				],
				'change' => [
					'comment' => ['type' => 'text', 'null' => false, 'default' => null],
				]
			],
			'posts' => [
				'add' => [
					'summary' => ['type' => 'text', 'null' => true, 'after' => 'body'],
				],
				'drop' => [
					'tableParameters' => [],
				],
				'change' => [
					'author_id' => ['type' => 'integer', 'null' => true, 'default' => ''],
					'title' => ['type' => 'string', 'null' => false, 'default' => 'Title'],
					'published' => ['type' => 'string', 'null' => true, 'default' => 'Y', 'length' => 1]
				]
			],
		];
		$this->assertEquals($expected, $compare);
		$this->assertNull($New->getVar('comments'));
		$this->assertEquals(['bar'], $New->getVar('_foo'));

		$tables = [
			'missing' => [
				'categories' => [
					'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
					'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
					'modified' => ['type' => 'datetime', 'null' => false, 'default' => null],
					'name' => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100],
					'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
					'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM']
				]
			],
			'ratings' => [
				'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
				'foreign_key' => ['type' => 'integer', 'null' => false, 'default' => null],
				'model' => ['type' => 'varchar', 'null' => false, 'default' => null],
				'value' => ['type' => 'float', 'null' => false, 'length' => '5,2', 'default' => null],
				'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
				'modified' => ['type' => 'datetime', 'null' => false, 'default' => null],
				'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
				'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM']
			]
		];
		$compare = $New->compare($this->Schema, $tables);
		$expected = [
			'ratings' => [
				'create' => [
					'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
					'foreign_key' => ['type' => 'integer', 'null' => false, 'default' => null],
					'model' => ['type' => 'varchar', 'null' => false, 'default' => null],
					'value' => ['type' => 'float', 'null' => false, 'length' => '5,2', 'default' => null],
					'created' => ['type' => 'datetime', 'null' => false, 'default' => null],
					'modified' => ['type' => 'datetime', 'null' => false, 'default' => null],
					'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
					'tableParameters' => ['charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM']
				]
			]
		];
		$this->assertEquals($expected, $compare);
	}

/**
 * test comparing '' and null and making sure they are different.
 *
 * @return void
 */
	public function testCompareEmptyStringAndNull() {
		$One = new CakeSchema([
			'posts' => [
				'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
				'name' => ['type' => 'string', 'null' => false, 'default' => '']
			]
		]);
		$Two = new CakeSchema([
			'posts' => [
				'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
				'name' => ['type' => 'string', 'null' => false, 'default' => null]
			]
		]);
		$compare = $One->compare($Two);
		$expected = [
			'posts' => [
				'change' => [
					'name' => ['type' => 'string', 'null' => false, 'default' => null]
				]
			]
		];
		$this->assertEquals($expected, $compare);
	}

/**
 * Test comparing tableParameters and indexes.
 *
 * @return void
 */
	public function testTableParametersAndIndexComparison() {
		$old = [
			'posts' => [
				'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
				'author_id' => ['type' => 'integer', 'null' => false],
				'title' => ['type' => 'string', 'null' => false],
				'indexes' => [
					'PRIMARY' => ['column' => 'id', 'unique' => true]
				],
				'tableParameters' => [
					'charset' => 'latin1',
					'collate' => 'latin1_general_ci'
				]
			],
			'comments' => [
				'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
				'post_id' => ['type' => 'integer', 'null' => false, 'default' => 0],
				'comment' => ['type' => 'text'],
				'indexes' => [
					'PRIMARY' => ['column' => 'id', 'unique' => true],
					'post_id' => ['column' => 'post_id'],
				],
				'tableParameters' => [
					'engine' => 'InnoDB',
					'charset' => 'latin1',
					'collate' => 'latin1_general_ci'
				]
			]
		];
		$new = [
			'posts' => [
				'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
				'author_id' => ['type' => 'integer', 'null' => false],
				'title' => ['type' => 'string', 'null' => false],
				'indexes' => [
					'PRIMARY' => ['column' => 'id', 'unique' => true],
					'author_id' => ['column' => 'author_id'],
				],
				'tableParameters' => [
					'charset' => 'utf8',
					'collate' => 'utf8_general_ci',
					'engine' => 'MyISAM'
				]
			],
			'comments' => [
				'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
				'post_id' => ['type' => 'integer', 'null' => false, 'default' => 0],
				'comment' => ['type' => 'text'],
				'indexes' => [
					'PRIMARY' => ['column' => 'id', 'unique' => true],
				],
				'tableParameters' => [
					'charset' => 'utf8',
					'collate' => 'utf8_general_ci'
				]
			]
		];
		$compare = $this->Schema->compare($old, $new);
		$expected = [
			'posts' => [
				'add' => [
					'indexes' => ['author_id' => ['column' => 'author_id']],
				],
				'change' => [
					'tableParameters' => [
						'charset' => 'utf8',
						'collate' => 'utf8_general_ci',
						'engine' => 'MyISAM'
					]
				]
			],
			'comments' => [
				'drop' => [
					'indexes' => ['post_id' => ['column' => 'post_id']],
				],
				'change' => [
					'tableParameters' => [
						'charset' => 'utf8',
						'collate' => 'utf8_general_ci',
					]
				]
			]
		];
		$this->assertEquals($expected, $compare);
	}

/**
 * Test comparing with field changed from VARCHAR to DATETIME
 *
 * @return void
 */
	public function testCompareVarcharToDatetime() {
		$old = [
			'posts' => [
				'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
				'author_id' => ['type' => 'integer', 'null' => false],
				'title' => ['type' => 'string', 'null' => true, 'length' => 45],
				'indexes' => [
					'PRIMARY' => ['column' => 'id', 'unique' => true]
				],
				'tableParameters' => [
					'charset' => 'latin1',
					'collate' => 'latin1_general_ci'
				]
			],
		];
		$new = [
			'posts' => [
				'id' => ['type' => 'integer', 'null' => false, 'default' => 0, 'key' => 'primary'],
				'author_id' => ['type' => 'integer', 'null' => false],
				'title' => ['type' => 'datetime', 'null' => false],
				'indexes' => [
					'PRIMARY' => ['column' => 'id', 'unique' => true]
				],
				'tableParameters' => [
					'charset' => 'latin1',
					'collate' => 'latin1_general_ci'
				]
			],
		];
		$compare = $this->Schema->compare($old, $new);
		$expected = [
			'posts' => [
				'change' => [
					'title' => [
						'type' => 'datetime',
						'null' => false,
					]
				]
			],
		];
		$this->assertEquals($expected, $compare, 'Invalid SQL, datetime does not have length');
	}

/**
 * testSchemaLoading method
 *
 * @return void
 */
	public function testSchemaLoading() {
		$Other = $this->Schema->load(['name' => 'MyOtherApp', 'path' => TMP . 'tests']);
		$this->assertEquals('MyOtherApp', $Other->name);
		$this->assertEquals($Other->tables, $this->Schema->tables);
	}

/**
 * test loading schema files inside of plugins.
 *
 * @return void
 */
	public function testSchemaLoadingFromPlugin() {
		App::build([
			'Plugin' => [CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS]
		]);
		CakePlugin::load('TestPlugin');
		$Other = $this->Schema->load(['name' => 'TestPluginApp', 'plugin' => 'TestPlugin']);
		$this->assertEquals('TestPluginApp', $Other->name);
		$this->assertEquals(['test_plugin_acos'], array_keys($Other->tables));

		App::build();
	}

/**
 * testSchemaCreateTable method
 *
 * @return void
 */
	public function testSchemaCreateTable() {
		$db = ConnectionManager::getDataSource('test');
		$db->cacheSources = false;

		$Schema = new CakeSchema([
			'connection' => 'test',
			'testdescribes' => [
				'id' => ['type' => 'integer', 'key' => 'primary'],
				'int_null' => ['type' => 'integer', 'null' => true],
				'int_not_null' => ['type' => 'integer', 'null' => false],
			],
		]);
		$sql = $db->createSchema($Schema);

		$col = $Schema->tables['testdescribes']['int_null'];
		$col['name'] = 'int_null';
		$column = $this->db->buildColumn($col);
		$this->assertRegExp('/' . preg_quote($column, '/') . '/', $sql);

		$col = $Schema->tables['testdescribes']['int_not_null'];
		$col['name'] = 'int_not_null';
		$column = $this->db->buildColumn($col);
		$this->assertRegExp('/' . preg_quote($column, '/') . '/', $sql);
	}
}
