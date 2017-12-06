<?php
/**
 * ModelWriteTest file
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
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('MockTransactionDboSource', 'Model/Datasource');
App::uses('MockTransactionAssociatedDboSource', 'Model/Datasource');
App::uses('MockManyTransactionDboSource', 'Model/Datasource');
App::uses('MockAssociatedTransactionDboSource', 'Model/Datasource');

require_once dirname(__FILE__) . DS . 'ModelTestBase.php';

/**
 * Helper class for testing with mocked datasources
 */
class TestAuthor extends Author {

	public $hasMany = [
		'Post' => [
			'className' => 'TestPost'
		]
	];

	protected $_dataSourceObject;

	public $dataForAfterSave;

/**
 * Helper method to set a datasource object
 *
 * @param Object $object The datasource object
 * @return void
 */
	public function setDataSourceObject($object) {
		$this->_dataSourceObject = $object;
	}

/**
 * Overwritten in order to return the directly set datasource object if
 * available
 *
 * @return DataSource
 */
	public function getDataSource() {
		if ($this->_dataSourceObject !== null) {
			return $this->_dataSourceObject;
		}
		return parent::getDataSource();
	}

}

/**
 * Helper class for testing with mocked datasources
 */
class TestPost extends Post {

	public $belongsTo = [
		'Author' => [
			'className' => 'TestAuthor'
		]
	];

	protected $_dataSourceObject;

	public $dataForAfterSave;

/**
 * Helper method to set a datasource object
 *
 * @param Object $object The datasource object
 * @return void
 */
	public function setDataSourceObject($object) {
		$this->_dataSourceObject = $object;
	}

/**
 * Overwritten in order to return the directly set datasource object if
 * available
 *
 * @return DataSource
 */
	public function getDataSource() {
		if ($this->_dataSourceObject !== null) {
			return $this->_dataSourceObject;
		}
		return parent::getDataSource();
	}

}

/**
 * ModelWriteTest
 *
 * @package       Cake.Test.Case.Model
 */
class ModelWriteTest extends BaseModelTest {

/**
 * override locale to the default (eng).
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Config.language', 'eng');
	}

/**
 * Test save() failing when there is no data.
 *
 * @return void
 */
	public function testInsertNoData() {
		$this->loadFixtures('Bid');
		$Bid = ClassRegistry::init('Bid');

		$this->assertFalse($Bid->save());

		$result = $Bid->save(['Bid' => []]);
		$this->assertFalse($result);

		$result = $Bid->save(['Bid' => ['not in schema' => 1]]);
		$this->assertFalse($result);
	}

/**
 * testInsertAnotherHabtmRecordWithSameForeignKey method
 *
 * @return void
 */
	public function testInsertAnotherHabtmRecordWithSameForeignKey() {
		$this->loadFixtures('JoinA', 'JoinB', 'JoinAB', 'JoinC', 'JoinAC');
		$TestModel = new JoinA();

		$result = $TestModel->JoinAsJoinB->findById(1);
		$expected = [
			'JoinAsJoinB' => [
				'id' => 1,
				'join_a_id' => 1,
				'join_b_id' => 2,
				'other' => 'Data for Join A 1 Join B 2',
				'created' => '2008-01-03 10:56:33',
				'updated' => '2008-01-03 10:56:33'
		]];
		$this->assertEquals($expected, $result);

		$TestModel->JoinAsJoinB->create();
		$data = [
			'join_a_id' => 1,
			'join_b_id' => 1,
			'other' => 'Data for Join A 1 Join B 1',
			'created' => '2008-01-03 10:56:44',
			'updated' => '2008-01-03 10:56:44'
		];
		$result = $TestModel->JoinAsJoinB->save($data);
		$lastInsertId = $TestModel->JoinAsJoinB->getLastInsertID();
		$data['id'] = $lastInsertId;
		$this->assertEquals(['JoinAsJoinB' => $data], $result);
		$this->assertTrue($lastInsertId > 0);

		$result = $TestModel->JoinAsJoinB->findById(1);
		$expected = [
			'JoinAsJoinB' => [
				'id' => 1,
				'join_a_id' => 1,
				'join_b_id' => 2,
				'other' => 'Data for Join A 1 Join B 2',
				'created' => '2008-01-03 10:56:33',
				'updated' => '2008-01-03 10:56:33'
		]];
		$this->assertEquals($expected, $result);

		$updatedValue = 'UPDATED Data for Join A 1 Join B 2';
		$TestModel->JoinAsJoinB->id = 1;
		$result = $TestModel->JoinAsJoinB->saveField('other', $updatedValue, false);
		$this->assertFalse(empty($result));

		$result = $TestModel->JoinAsJoinB->findById(1);
		$this->assertEquals($updatedValue, $result['JoinAsJoinB']['other']);
	}

/**
 * testSaveDateAsFirstEntry method
 *
 * @return void
 */
	public function testSaveDateAsFirstEntry() {
		$this->loadFixtures('Article', 'User', 'Comment', 'Attachment', 'Tag', 'ArticlesTag');

		$Article = new Article();

		$data = [
			'Article' => [
				'created' => [
					'day' => '1',
					'month' => '1',
					'year' => '2008'
				],
				'title' => 'Test Title',
				'user_id' => 1
		]];
		$Article->create();
		$result = $Article->save($data);
		$this->assertFalse(empty($result));

		$testResult = $Article->find('first', ['conditions' => ['Article.title' => 'Test Title']]);

		$this->assertEquals($data['Article']['title'], $testResult['Article']['title']);
		$this->assertEquals('2008-01-01 00:00:00', $testResult['Article']['created']);
	}

/**
 * testUnderscoreFieldSave method
 *
 * @return void
 */
	public function testUnderscoreFieldSave() {
		$this->loadFixtures('UnderscoreField');
		$UnderscoreField = new UnderscoreField();

		$currentCount = $UnderscoreField->find('count');
		$this->assertEquals(3, $currentCount);
		$data = ['UnderscoreField' => [
			'user_id' => '1',
			'my_model_has_a_field' => 'Content here',
			'body' => 'Body',
			'published' => 'Y',
			'another_field' => 4
		]];
		$ret = $UnderscoreField->save($data);
		$this->assertFalse(empty($ret));

		$currentCount = $UnderscoreField->find('count');
		$this->assertEquals(4, $currentCount);
	}

/**
 * testAutoSaveUuid method
 *
 * @return void
 */
	public function testAutoSaveUuid() {
		// SQLite does not support non-integer primary keys
		$this->skipIf($this->db instanceof Sqlite, 'This test is not compatible with SQLite.');

		$this->loadFixtures('Uuid');
		$TestModel = new Uuid();

		$TestModel->save(['title' => 'Test record']);
		$result = $TestModel->findByTitle('Test record');
		$this->assertEquals(
			['id', 'title', 'count', 'created', 'updated'],
			array_keys($result['Uuid'])
		);
		$this->assertEquals(36, strlen($result['Uuid']['id']));
	}

/**
 * testAutoSaveUuidNative method
 *
 * @return void
 */
	public function testAutoSaveUuidNative() {
		$this->skipIf(!($this->db instanceof Postgres), 'This test is compatible with Postgres only.');

		$this->loadFixtures('UuidNative');
		$TestModel = new UuidNative();

		$TestModel->save(['title' => 'Test record']);
		$result = $TestModel->findByTitle('Test record');
		$this->assertEquals(
			['id', 'title', 'count', 'created', 'updated'],
			array_keys($result['UuidNative'])
		);
		$this->assertEquals(36, strlen($result['UuidNative']['id']));
	}

/**
 * Ensure that if the id key is null but present the save doesn't fail (with an
 * x sql error: "Column id specified twice")
 *
 * @return void
 */
	public function testSaveUuidNull() {
		// SQLite does not support non-integer primary keys
		$this->skipIf($this->db instanceof Sqlite, 'This test is not compatible with SQLite.');

		$this->loadFixtures('Uuid');
		$TestModel = new Uuid();

		$TestModel->save(['title' => 'Test record', 'id' => null]);
		$result = $TestModel->findByTitle('Test record');
		$this->assertEquals(
			['id', 'title', 'count', 'created', 'updated'],
			array_keys($result['Uuid'])
		);
		$this->assertEquals(36, strlen($result['Uuid']['id']));
	}

/**
 * Ensure that if the id key is null but present the save doesn't fail (with an
 * x sql error: "Column id specified twice")
 *
 * @return void
 */
	public function testSaveUuidNullNative() {
		$this->skipIf(!($this->db instanceof Postgres), 'This test is compatible with Postgres only.');

		$this->loadFixtures('UuidNative');
		$TestModel = new UuidNative();

		$TestModel->save(['title' => 'Test record', 'id' => null]);
		$result = $TestModel->findByTitle('Test record');
		$this->assertEquals(
			['id', 'title', 'count', 'created', 'updated'],
			array_keys($result['UuidNative'])
		);
		$this->assertEquals(36, strlen($result['UuidNative']['id']));
	}

/**
 * testZeroDefaultFieldValue method
 *
 * @return void
 */
	public function testZeroDefaultFieldValue() {
		$this->skipIf($this->db instanceof Sqlite, 'SQLite uses loose typing, this operation is unsupported.');

		$this->loadFixtures('DataTest');
		$TestModel = new DataTest();

		$TestModel->create([]);
		$TestModel->save();
		$result = $TestModel->findById($TestModel->id);
		$this->assertEquals(0, $result['DataTest']['count']);
		$this->assertEquals(0, $result['DataTest']['float']);
	}

/**
 * Tests validation parameter order in custom validation methods
 *
 * @return void
 */
	public function testAllowSimulatedFields() {
		$TestModel = new ValidationTest1();

		$TestModel->create([
			'title' => 'foo',
			'bar' => 'baz'
		]);
		$expected = [
			'ValidationTest1' => [
				'title' => 'foo',
				'bar' => 'baz'
		]];
		$this->assertEquals($expected, $TestModel->data);
	}

/**
 * test that Caches are getting cleared on save().
 * ensure that both inflections of controller names are getting cleared
 * as URL for controller could be either overallFavorites/index or overall_favorites/index
 *
 * @return void
 */
	public function testCacheClearOnSave() {
		$_back = [
			'check' => Configure::read('Cache.check'),
			'disable' => Configure::read('Cache.disable'),
		];
		Configure::write('Cache.check', true);
		Configure::write('Cache.disable', false);

		$this->loadFixtures('OverallFavorite');
		$OverallFavorite = new OverallFavorite();

		touch(CACHE . 'views' . DS . 'some_dir_overallfavorites_index.php');
		touch(CACHE . 'views' . DS . 'some_dir_overall_favorites_index.php');

		$data = [
			'OverallFavorite' => [
				'id' => 22,
				'model_type' => '8-track',
				'model_id' => '3',
				'priority' => '1'
			]
		];
		$OverallFavorite->create($data);
		$OverallFavorite->save();

		$this->assertFalse(file_exists(CACHE . 'views' . DS . 'some_dir_overallfavorites_index.php'));
		$this->assertFalse(file_exists(CACHE . 'views' . DS . 'some_dir_overall_favorites_index.php'));

		Configure::write('Cache.check', $_back['check']);
		Configure::write('Cache.disable', $_back['disable']);
	}

/**
 * test that save() resets whitelist on failed save
 *
 * @return void
 */
	public function testSaveFieldListResetsWhitelistOnFailedSave() {
		$this->loadFixtures('Bidding');
		$model = new Bidding();
		$whitelist = ['title'];
		$model->whitelist = $whitelist;
		$result = $model->save(
			[],
			['fieldList' => ['body']]
		);
		$this->assertFalse($result);
		$this->assertEquals($whitelist, $model->whitelist);
	}

/**
 * Test that save() with a fieldList continues to write
 * updated in all cases.
 *
 * @return void
 */
	public function testSaveUpdatedWithFieldList() {
		$this->loadFixtures('Post', 'Author');
		$model = ClassRegistry::init('Post');
		$original = $model->find('first', [
			'conditions' => ['Post.id' => 1]
		]);
		$data = [
			'Post' => [
				'id' => 1,
				'title' => 'New title',
				'updated' => '1999-01-01 00:00:00',
			]
		];
		$model->save($data, [
			'fieldList' => ['title']
		]);
		$new = $model->find('first', [
			'conditions' => ['Post.id' => 1]
		]);
		$this->assertGreaterThan($original['Post']['updated'], $new['Post']['updated']);
	}

/**
 * Test save() resets the whitelist after afterSave
 *
 * @return void
 */
	public function testSaveResetWhitelistOnSuccess() {
		$this->loadFixtures('Post');

		$callback = [$this, 'callbackForWhitelistReset'];
		$model = ClassRegistry::init('Post');
		$model->whitelist = ['author_id', 'title', 'body'];
		$model->getEventManager()->attach($callback, 'Model.afterSave');
		$data = [
			'title' => 'New post',
			'body' => 'Post body',
			'author_id' => 1
		];
		$result = $model->save($data);
		$this->assertNotEmpty($result);
	}

/**
 * Callback for testing whitelist in afterSave
 *
 * @param Model $model The model having save called.
 * @return void
 */
	public function callbackForWhitelistReset($event) {
		$expected = ['author_id', 'title', 'body', 'updated', 'created'];
		$this->assertEquals($expected, $event->subject()->whitelist);
	}

/**
 * testSaveWithCounterCache method
 *
 * @return void
 */
	public function testSaveWithCounterCache() {
		$this->loadFixtures('Syfile', 'Item', 'Image', 'Portfolio', 'ItemsPortfolio');
		$TestModel = new Syfile();
		$TestModel2 = new Item();

		$result = $TestModel->findById(1);
		$this->assertNull($result['Syfile']['item_count']);

		$TestModel2->save([
			'name' => 'Item 7',
			'syfile_id' => 1,
			'published' => false
		]);

		$result = $TestModel->findById(1);
		$this->assertEquals(2, $result['Syfile']['item_count']);

		$TestModel2->delete(1);
		$result = $TestModel->findById(1);
		$this->assertEquals(1, $result['Syfile']['item_count']);

		$TestModel2->id = 2;
		$TestModel2->saveField('syfile_id', 1);

		$result = $TestModel->findById(1);
		$this->assertEquals(2, $result['Syfile']['item_count']);

		$result = $TestModel->findById(2);
		$this->assertEquals(0, $result['Syfile']['item_count']);
	}

/**
 * Tests that counter caches are updated when records are added
 *
 * @return void
 */
	public function testCounterCacheIncrease() {
		$this->loadFixtures('CounterCacheUser', 'CounterCachePost');
		$User = new CounterCacheUser();
		$Post = new CounterCachePost();
		$data = ['Post' => [
			'id' => 22,
			'title' => 'New Post',
			'user_id' => 66
		]];

		$Post->save($data);
		$user = $User->find('first', [
			'conditions' => ['id' => 66],
			'recursive' => -1
		]);

		$result = $user[$User->alias]['post_count'];
		$expected = 3;
		$this->assertEquals($expected, $result);
	}

/**
 * Tests that counter caches are updated when records are deleted
 *
 * @return void
 */
	public function testCounterCacheDecrease() {
		$this->loadFixtures('CounterCacheUser', 'CounterCachePost');
		$User = new CounterCacheUser();
		$Post = new CounterCachePost();

		$Post->delete(2);
		$user = $User->find('first', [
			'conditions' => ['id' => 66],
			'recursive' => -1
		]);

		$result = $user[$User->alias]['post_count'];
		$expected = 1;
		$this->assertEquals($expected, $result);
	}

/**
 * Tests that counter caches are updated when foreign keys of counted records change
 *
 * @return void
 */
	public function testCounterCacheUpdated() {
		$this->loadFixtures('CounterCacheUser', 'CounterCachePost');
		$User = new CounterCacheUser();
		$Post = new CounterCachePost();

		$data = $Post->find('first', [
			'conditions' => ['id' => 1],
			'recursive' => -1
		]);
		$data[$Post->alias]['user_id'] = 301;
		$Post->save($data);

		$users = $User->find('all', ['order' => 'User.id']);
		$this->assertEquals(1, $users[0]['User']['post_count']);
		$this->assertEquals(2, $users[1]['User']['post_count']);
	}

/**
 * Test counter cache with models that use a non-standard (i.e. not using 'id')
 * as their primary key.
 *
 * @return void
 */
	public function testCounterCacheWithNonstandardPrimaryKey() {
		$this->loadFixtures(
			'CounterCacheUserNonstandardPrimaryKey',
			'CounterCachePostNonstandardPrimaryKey'
		);

		$User = new CounterCacheUserNonstandardPrimaryKey();
		$Post = new CounterCachePostNonstandardPrimaryKey();

		$data = $Post->find('first', [
			'conditions' => ['pid' => 1],
			'recursive' => -1
		]);
		$data[$Post->alias]['uid'] = 301;
		$Post->save($data);

		$users = $User->find('all', ['order' => 'User.uid']);
		$this->assertEquals(1, $users[0]['User']['post_count']);
		$this->assertEquals(2, $users[1]['User']['post_count']);
	}

/**
 * test Counter Cache With Self Joining table
 *
 * @return void
 */
	public function testCounterCacheWithSelfJoin() {
		$this->skipIf($this->db instanceof Sqlite, 'SQLite 2.x does not support ALTER TABLE ADD COLUMN');

		$this->loadFixtures('CategoryThread');
		$column = 'COLUMN ';
		if ($this->db instanceof Sqlserver) {
			$column = '';
		}
		$column .= $this->db->buildColumn(['name' => 'child_count', 'type' => 'integer']);
		$this->db->query('ALTER TABLE ' . $this->db->fullTableName('category_threads') . ' ADD ' . $column);
		$this->db->flushMethodCache();
		$Category = new CategoryThread();
		$result = $Category->updateAll(['CategoryThread.name' => "'updated'"], ['CategoryThread.parent_id' => 5]);
		$this->assertFalse(empty($result));

		$Category = new CategoryThread();
		$Category->belongsTo['ParentCategory']['counterCache'] = 'child_count';
		$Category->updateCounterCache(['parent_id' => 5]);
		$result = Hash::extract($Category->find('all', ['conditions' => ['CategoryThread.id' => 5]]), '{n}.CategoryThread.child_count');
		$expected = [1];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveWithCounterCacheScope method
 *
 * @return void
 */
	public function testSaveWithCounterCacheScope() {
		$this->loadFixtures('Syfile', 'Item', 'Image', 'ItemsPortfolio', 'Portfolio');
		$TestModel = new Syfile();
		$TestModel2 = new Item();
		$TestModel2->belongsTo['Syfile']['counterCache'] = true;
		$TestModel2->belongsTo['Syfile']['counterScope'] = ['published' => true];

		$result = $TestModel->findById(1);
		$this->assertNull($result['Syfile']['item_count']);

		$TestModel2->save([
			'name' => 'Item 7',
			'syfile_id' => 1,
			'published' => true
		]);

		$result = $TestModel->findById(1);

		$this->assertEquals(1, $result['Syfile']['item_count']);

		$TestModel2->id = 1;
		$TestModel2->saveField('published', true);
		$result = $TestModel->findById(1);
		$this->assertEquals(2, $result['Syfile']['item_count']);

		$TestModel2->save([
			'id' => 1,
			'syfile_id' => 1,
			'published' => false
		]);

		$result = $TestModel->findById(1);
		$this->assertEquals(1, $result['Syfile']['item_count']);
	}

/**
 * Tests having multiple counter caches for an associated model
 *
 * @return void
 */
	public function testCounterCacheMultipleCaches() {
		$this->loadFixtures('CounterCacheUser', 'CounterCachePost');
		$User = new CounterCacheUser();
		$Post = new CounterCachePost();
		$Post->unbindModel(['belongsTo' => ['User']], false);
		$Post->bindModel([
			'belongsTo' => [
				'User' => [
					'className' => 'CounterCacheUser',
					'foreignKey' => 'user_id',
					'counterCache' => [
						true,
						'posts_published' => ['Post.published' => true]
					]
				]
			]
		], false);

		// Count Increase
		$data = ['Post' => [
			'id' => 22,
			'title' => 'New Post',
			'user_id' => 66,
			'published' => true
		]];
		$Post->save($data);
		$result = $User->find('first', [
			'conditions' => ['id' => 66],
			'recursive' => -1
		]);
		$this->assertEquals(3, $result[$User->alias]['post_count']);
		$this->assertEquals(2, $result[$User->alias]['posts_published']);

		// Count decrease
		$Post->delete(1);
		$result = $User->find('first', [
			'conditions' => ['id' => 66],
			'recursive' => -1
		]);
		$this->assertEquals(2, $result[$User->alias]['post_count']);
		$this->assertEquals(2, $result[$User->alias]['posts_published']);

		// Count update
		$data = $Post->find('first', [
			'conditions' => ['id' => 1],
			'recursive' => -1
		]);
		$data[$Post->alias]['user_id'] = 301;
		$Post->save($data);
		$result = $User->find('all', ['order' => 'User.id']);
		$this->assertEquals(2, $result[0]['User']['post_count']);
		$this->assertEquals(1, $result[1]['User']['posts_published']);
	}

/**
 * Tests that counter caches are unchanged when using 'counterCache' => false
 *
 * @return void
 */
	public function testCounterCacheSkip() {
		$this->loadFixtures('CounterCacheUser', 'CounterCachePost');
		$User = new CounterCacheUser();
		$Post = new CounterCachePost();

		$data = $Post->find('first', [
			'conditions' => ['id' => 1],
			'recursive' => -1
		]);
		$data[$Post->alias]['user_id'] = 301;
		$Post->save($data, ['counterCache' => false]);

		$users = $User->find('all', ['order' => 'User.id']);
		$this->assertEquals(2, $users[0]['User']['post_count']);
		$this->assertEquals(1, $users[1]['User']['post_count']);
	}

/**
 * test that beforeValidate returning false can abort saves.
 *
 * @return void
 */
	public function testBeforeValidateSaveAbortion() {
		$this->loadFixtures('Post');
		$Model = new CallbackPostTestModel();
		$Model->beforeValidateReturn = false;

		$data = [
			'title' => 'new article',
			'body' => 'this is some text.'
		];
		$Model->create();
		$result = $Model->save($data);
		$this->assertFalse($result);
	}

/**
 * test that beforeSave returning false can abort saves.
 *
 * @return void
 */
	public function testBeforeSaveSaveAbortion() {
		$this->loadFixtures('Post');
		$Model = new CallbackPostTestModel();
		$Model->beforeSaveReturn = false;

		$data = [
			'title' => 'new article',
			'body' => 'this is some text.'
		];
		$Model->create();
		$result = $Model->save($data);
		$this->assertFalse($result);
	}

/**
 * testSaveAtomic method
 *
 * @return void
 */
	public function testSaveAtomic() {
		$this->loadFixtures('Article');
		$TestModel = new Article();

		// Create record with 'atomic' = false

		$data = [
			'Article' => [
				'user_id' => '1',
				'title' => 'Fourth Article',
				'body' => 'Fourth Article Body',
				'published' => 'Y'
			]
		];
		$TestModel->create();
		$result = $TestModel->save($data, ['atomic' => false]);
		$this->assertTrue((bool)$result);

		// Check record we created

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 4);
		$expected = [
			'Article' => [
				'id' => '4',
				'user_id' => '1',
				'title' => 'Fourth Article',
				'body' => 'Fourth Article Body',
				'published' => 'Y'
			]
		];
		$this->assertEquals($expected, $result);

		// Create record with 'atomic' = true

		$data = [
			'Article' => [
				'user_id' => '4',
				'title' => 'Fifth Article',
				'body' => 'Fifth Article Body',
				'published' => 'Y'
			]
		];
		$TestModel->create();
		$result = $TestModel->save($data, ['atomic' => true]);
		$this->assertTrue((bool)$result);

		// Check record we created

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 5);
		$expected = [
			'Article' => [
				'id' => '5',
				'user_id' => '4',
				'title' => 'Fifth Article',
				'body' => 'Fifth Article Body',
				'published' => 'Y'
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test save with transaction and ensure there is no missing rollback.
 *
 * @return void
 */
	public function testSaveTransactionNoRollback() {
		$this->loadFixtures('Post', 'Article');

		$db = $this->getMock('DboSource', ['begin', 'connect', 'rollback', 'describe']);

		$db->expects($this->once())
			->method('describe')
			->will($this->returnValue([]));
		$db->expects($this->once())
			->method('begin')
			->will($this->returnValue(true));
		$db->expects($this->once())
			->method('rollback');

		$Post = new TestPost();
		$Post->setDataSourceObject($db);

		$callback = [$this, 'callbackForTestSaveTransaction'];
		$Post->getEventManager()->attach($callback, 'Model.beforeSave');

		$data = [
			'Post' => [
				'author_id' => 1,
				'title' => 'New Fourth Post'
			]
		];
		$Post->save($data, ['atomic' => true]);
	}

/**
 * test callback used in testSaveTransaction method
 *
 * @return bool false to stop event propagation
 */
	public function callbackForTestSaveTransaction($event) {
		$TestModel = new Article();

		// Create record. Do not use same model as in testSaveTransaction
		// to avoid infinite loop.

		$data = [
			'Article' => [
				'user_id' => '1',
				'title' => 'Fourth Article',
				'body' => 'Fourth Article Body',
				'published' => 'Y'
			]
		];
		$TestModel->create();
		$result = $TestModel->save($data);
		$this->assertTrue((bool)$result);

		// force transaction to be rolled back in Post model
		$event->stopPropagation();
		return false;
	}

/**
 * testSaveTransaction method
 *
 * @return void
 */
	public function testSaveTransaction() {
		$this->loadFixtures('Post', 'Article');
		$PostModel = new Post();

		// Check if Database supports transactions

		$PostModel->validate = ['title' => 'notBlank'];
		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => 'New Fifth Post'],
			['author_id' => 1, 'title' => '']
		];
		$this->assertFalse($PostModel->saveAll($data));

		$result = $PostModel->find('all', ['recursive' => -1]);
		$expectedPosts = [
			[
				'Post' => [
					'id' => '1',
					'author_id' => 1,
					'title' => 'First Post',
					'body' => 'First Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				]
			],
			[
				'Post' => [
					'id' => '2',
					'author_id' => 3,
					'title' => 'Second Post',
					'body' => 'Second Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31'
				]
			],
			[
				'Post' => [
					'id' => '3',
					'author_id' => 1,
					'title' => 'Third Post',
					'body' => 'Third Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				]
			]
		];

		$this->skipIf(count($result) !== 3, 'Database does not support transactions.');

		$this->assertEquals($expectedPosts, $result);

		// Database supports transactions --> continue tests

		$data = [
			'Post' => [
				'author_id' => 1,
				'title' => 'New Fourth Post'
			]
		];

		$callback = [$this, 'callbackForTestSaveTransaction'];
		$PostModel->getEventManager()->attach($callback, 'Model.beforeSave');

		$PostModel->create();
		$result = $PostModel->save($data, ['atomic' => true]);
		$this->assertFalse($result);

		$result = $PostModel->find('all', ['recursive' => -1]);
		$this->assertEquals($expectedPosts, $result);

		// Check record we created in callbackForTestSaveTransaction method.
		// record should not exist due to rollback

		$ArticleModel = new Article();
		$result = $ArticleModel->find('all', ['recursive' => -1]);
		$expectedArticles = [
			[
				'Article' => [
					'user_id' => '1',
					'title' => 'First Article',
					'body' => 'First Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
					'id' => '1'
				]
			],
			[
				'Article' => [
					'user_id' => '3',
					'title' => 'Second Article',
					'body' => 'Second Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31',
					'id' => '2'
				]
			],
			[
				'Article' => [
					'user_id' => '1',
					'title' => 'Third Article',
					'body' => 'Third Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31',
					'id' => '3'
				]
			]
		];
		$this->assertEquals($expectedArticles, $result);
	}

/**
 * testSaveField method
 *
 * @return void
 */
	public function testSaveField() {
		$this->loadFixtures('Article');
		$TestModel = new Article();

		$TestModel->id = 1;
		$result = $TestModel->saveField('title', 'New First Article');
		$this->assertFalse(empty($result));

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body'], 1);
		$expected = ['Article' => [
			'id' => '1',
			'user_id' => '1',
			'title' => 'New First Article',
			'body' => 'First Article Body'
		]];
		$this->assertEquals($expected, $result);

		$TestModel->id = 1;
		$result = $TestModel->saveField('title', '');
		$this->assertFalse(empty($result));

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body'], 1);
		$expected = ['Article' => [
			'id' => '1',
			'user_id' => '1',
			'title' => '',
			'body' => 'First Article Body'
		]];
		$result['Article']['title'] = trim($result['Article']['title']);
		$this->assertEquals($expected, $result);

		$TestModel->id = 1;
		$TestModel->set('body', 'Messed up data');
		$result = $TestModel->saveField('title', 'First Article');
		$this->assertFalse(empty($result));
		$result = $TestModel->read(['id', 'user_id', 'title', 'body'], 1);
		$expected = ['Article' => [
			'id' => '1',
			'user_id' => '1',
			'title' => 'First Article',
			'body' => 'First Article Body'
		]];
		$this->assertEquals($expected, $result);

		$TestModel->recursive = -1;
		$TestModel->read(['id', 'user_id', 'title', 'body'], 1);

		$TestModel->id = 1;
		$result = $TestModel->saveField('title', '', true);
		$this->assertFalse($result);

		$TestModel->recursive = -1;
		$TestModel->id = 1;
		$result = $TestModel->saveField('user_id', 9999);
		$this->assertTrue((bool)$result);

		$result = $TestModel->read(['id', 'user_id'], 1);
		$expected = ['Article' => [
			'id' => '1',
			'user_id' => '9999',
		]];
		$this->assertEquals($expected, $result);

		$this->loadFixtures('Node', 'Dependency');
		$Node = new Node();
		$Node->set('id', 1);
		$result = $Node->read();
		$this->assertEquals(['Second'], Hash::extract($result, 'ParentNode.{n}.name'));

		$Node->saveField('state', 10);
		$result = $Node->read();
		$this->assertEquals(['Second'], Hash::extract($result, 'ParentNode.{n}.name'));
	}

/**
 * testSaveWithCreate method
 *
 * @return void
 */
	public function testSaveWithCreate() {
		$this->loadFixtures(
			'User',
			'Article',
			'User',
			'Comment',
			'Tag',
			'ArticlesTag',
			'Attachment'
		);
		$TestModel = new User();

		$data = ['User' => [
			'user' => 'user',
			'password' => ''
		]];
		$result = $TestModel->save($data);
		$this->assertFalse($result);
		$this->assertTrue(!empty($TestModel->validationErrors));

		$TestModel = new Article();

		$data = ['Article' => [
			'user_id' => '',
			'title' => '',
			'body' => ''
		]];
		$result = $TestModel->create($data) && $TestModel->save();
		$this->assertFalse($result);
		$this->assertTrue(!empty($TestModel->validationErrors));

		$data = ['Article' => [
			'id' => 1,
			'user_id' => '1',
			'title' => 'New First Article',
			'body' => ''
		]];
		$result = $TestModel->create($data) && $TestModel->save();
		$this->assertFalse($result);

		$data = ['Article' => [
			'id' => 1,
			'title' => 'New First Article'
		]];
		$result = $TestModel->create() && $TestModel->save($data, false);
		$this->assertFalse(empty($result));

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 1);
		$expected = ['Article' => [
			'id' => '1',
			'user_id' => '1',
			'title' => 'New First Article',
			'body' => 'First Article Body',
			'published' => 'N'
		]];
		$this->assertEquals($expected, $result);

		$data = ['Article' => [
			'id' => 1,
			'user_id' => '2',
			'title' => 'First Article',
			'body' => 'New First Article Body',
			'published' => 'Y'
		]];
		$result = $TestModel->create() && $TestModel->save($data, true, ['id', 'title', 'published']);
		$this->assertFalse(empty($result));

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 1);
		$expected = ['Article' => [
			'id' => '1',
			'user_id' => '1',
			'title' => 'First Article',
			'body' => 'First Article Body',
			'published' => 'Y'
		]];
		$this->assertEquals($expected, $result);

		$data = [
			'Article' => [
				'user_id' => '2',
				'title' => 'New Article',
				'body' => 'New Article Body',
				'created' => '2007-03-18 14:55:23',
				'updated' => '2007-03-18 14:57:31'
			],
			'Tag' => ['Tag' => [1, 3]]
		];
		$TestModel->create();
		$result = $TestModel->create() && $TestModel->save($data);
		$this->assertFalse(empty($result));

		$TestModel->recursive = 2;
		$result = $TestModel->read(null, 4);
		$expected = [
			'Article' => [
				'id' => '4',
				'user_id' => '2',
				'title' => 'New Article',
				'body' => 'New Article Body',
				'published' => 'N',
				'created' => '2007-03-18 14:55:23',
				'updated' => '2007-03-18 14:57:31'
			],
			'User' => [
				'id' => '2',
				'user' => 'nate',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
				'created' => '2007-03-17 01:18:23',
				'updated' => '2007-03-17 01:20:31'
			],
			'Comment' => [],
			'Tag' => [
				[
					'id' => '1',
					'tag' => 'tag1',
					'created' => '2007-03-18 12:22:23',
					'updated' => '2007-03-18 12:24:31'
				],
				[
					'id' => '3',
					'tag' => 'tag3',
					'created' => '2007-03-18 12:26:23',
					'updated' => '2007-03-18 12:28:31'
		]]];
		$this->assertEquals($expected, $result);

		$data = ['Comment' => [
			'article_id' => '4',
			'user_id' => '1',
			'comment' => 'Comment New Article',
			'published' => 'Y',
			'created' => '2007-03-18 14:57:23',
			'updated' => '2007-03-18 14:59:31'
		]];
		$result = $TestModel->Comment->create() && $TestModel->Comment->save($data);
		$this->assertFalse(empty($result));

		$data = ['Attachment' => [
			'comment_id' => '7',
			'attachment' => 'newattachment.zip',
			'created' => '2007-03-18 15:02:23',
			'updated' => '2007-03-18 15:04:31'
		]];
		$result = $TestModel->Comment->Attachment->save($data);
		$this->assertFalse(empty($result));

		$TestModel->recursive = 2;
		$result = $TestModel->read(null, 4);
		$expected = [
			'Article' => [
				'id' => '4',
				'user_id' => '2',
				'title' => 'New Article',
				'body' => 'New Article Body',
				'published' => 'N',
				'created' => '2007-03-18 14:55:23',
				'updated' => '2007-03-18 14:57:31'
			],
			'User' => [
				'id' => '2',
				'user' => 'nate',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
				'created' => '2007-03-17 01:18:23',
				'updated' => '2007-03-17 01:20:31'
			],
			'Comment' => [
				[
					'id' => '7',
					'article_id' => '4',
					'user_id' => '1',
					'comment' => 'Comment New Article',
					'published' => 'Y',
					'created' => '2007-03-18 14:57:23',
					'updated' => '2007-03-18 14:59:31',
					'Article' => [
						'id' => '4',
						'user_id' => '2',
						'title' => 'New Article',
						'body' => 'New Article Body',
						'published' => 'N',
						'created' => '2007-03-18 14:55:23',
						'updated' => '2007-03-18 14:57:31'
					],
					'User' => [
						'id' => '1',
						'user' => 'mariano',
						'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
						'created' => '2007-03-17 01:16:23',
						'updated' => '2007-03-17 01:18:31'
					],
					'Attachment' => [
						'id' => '2',
						'comment_id' => '7',
						'attachment' => 'newattachment.zip',
						'created' => '2007-03-18 15:02:23',
						'updated' => '2007-03-18 15:04:31'
			]]],
			'Tag' => [
				[
					'id' => '1',
					'tag' => 'tag1',
					'created' => '2007-03-18 12:22:23',
					'updated' => '2007-03-18 12:24:31'
				],
				[
					'id' => '3',
					'tag' => 'tag3',
					'created' => '2007-03-18 12:26:23',
					'updated' => '2007-03-18 12:28:31'
		]]];

		$this->assertEquals($expected, $result);
	}

/**
 * test that a null Id doesn't cause errors
 *
 * @return void
 */
	public function testSaveWithNullId() {
		$this->loadFixtures('User');
		$User = new User();
		$User->read(null, 1);
		$User->data['User']['id'] = null;
		$result = $User->save(['password' => 'test']);
		$this->assertFalse(empty($result));
		$this->assertTrue($User->id > 0);

		$User->read(null, 2);
		$User->data['User']['id'] = null;
		$result = $User->save(['password' => 'test']);
		$this->assertFalse(empty($result));
		$this->assertTrue($User->id > 0);

		$User->data['User'] = ['password' => 'something'];
		$result = $User->save();
		$this->assertFalse(empty($result));
		$result = $User->read();
		$this->assertEquals('something', $User->data['User']['password']);
	}

/**
 * testSaveWithSet method
 *
 * @return void
 */
	public function testSaveWithSet() {
		$this->loadFixtures('Article');
		$TestModel = new Article();

		// Create record we will be updating later

		$data = ['Article' => [
			'user_id' => '1',
			'title' => 'Fourth Article',
			'body' => 'Fourth Article Body',
			'published' => 'Y'
		]];
		$result = $TestModel->create() && $TestModel->save($data);
		$this->assertFalse(empty($result));

		// Check record we created

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 4);
		$expected = ['Article' => [
			'id' => '4',
			'user_id' => '1',
			'title' => 'Fourth Article',
			'body' => 'Fourth Article Body',
			'published' => 'Y'
		]];
		$this->assertEquals($expected, $result);

		// Create new record just to overlap Model->id on previously created record

		$data = ['Article' => [
			'user_id' => '4',
			'title' => 'Fifth Article',
			'body' => 'Fifth Article Body',
			'published' => 'Y'
		]];
		$result = $TestModel->create() && $TestModel->save($data);
		$this->assertFalse(empty($result));

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 5);
		$expected = ['Article' => [
			'id' => '5',
			'user_id' => '4',
			'title' => 'Fifth Article',
			'body' => 'Fifth Article Body',
			'published' => 'Y'
		]];
		$this->assertEquals($expected, $result);

		// Go back and edit the first article we created, starting by checking it's still there

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 4);
		$expected = ['Article' => [
			'id' => '4',
			'user_id' => '1',
			'title' => 'Fourth Article',
			'body' => 'Fourth Article Body',
			'published' => 'Y'
		]];
		$this->assertEquals($expected, $result);

		// And now do the update with set()

		$data = ['Article' => [
			'id' => '4',
			'title' => 'Fourth Article - New Title',
			'published' => 'N'
		]];
		$result = $TestModel->set($data) && $TestModel->save();
		$this->assertFalse(empty($result));

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 4);
		$expected = ['Article' => [
			'id' => '4',
			'user_id' => '1',
			'title' => 'Fourth Article - New Title',
			'body' => 'Fourth Article Body',
			'published' => 'N'
		]];
		$this->assertEquals($expected, $result);

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 5);
		$expected = ['Article' => [
			'id' => '5',
			'user_id' => '4',
			'title' => 'Fifth Article',
			'body' => 'Fifth Article Body',
			'published' => 'Y'
		]];
		$this->assertEquals($expected, $result);

		$data = ['Article' => ['id' => '5', 'title' => 'Fifth Article - New Title 5']];
		$result = ($TestModel->set($data) && $TestModel->save());
		$this->assertFalse(empty($result));

		$TestModel->recursive = -1;
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 5);
		$expected = ['Article' => [
			'id' => '5',
			'user_id' => '4',
			'title' => 'Fifth Article - New Title 5',
			'body' => 'Fifth Article Body',
			'published' => 'Y'
		]];
		$this->assertEquals($expected, $result);

		$TestModel->recursive = -1;
		$result = $TestModel->find('all', [
			'fields' => ['id', 'title'],
			'order' => ['Article.id' => 'ASC']
		]);
		$expected = [
			['Article' => ['id' => 1, 'title' => 'First Article']],
			['Article' => ['id' => 2, 'title' => 'Second Article']],
			['Article' => ['id' => 3, 'title' => 'Third Article']],
			['Article' => ['id' => 4, 'title' => 'Fourth Article - New Title']],
			['Article' => ['id' => 5, 'title' => 'Fifth Article - New Title 5']]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveWithNonExistentFields method
 *
 * @return void
 */
	public function testSaveWithNonExistentFields() {
		$this->loadFixtures('Article');
		$TestModel = new Article();
		$TestModel->recursive = -1;

		$data = [
			'non_existent' => 'This field does not exist',
			'user_id' => '1',
			'title' => 'Fourth Article - New Title',
			'body' => 'Fourth Article Body',
			'published' => 'N'
		];
		$result = $TestModel->create() && $TestModel->save($data);
		$this->assertFalse(empty($result));

		$expected = ['Article' => [
			'id' => '4',
			'user_id' => '1',
			'title' => 'Fourth Article - New Title',
			'body' => 'Fourth Article Body',
			'published' => 'N'
		]];
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 4);
		$this->assertEquals($expected, $result);

		$data = [
			'user_id' => '1',
			'non_existent' => 'This field does not exist',
			'title' => 'Fifth Article - New Title',
			'body' => 'Fifth Article Body',
			'published' => 'N'
		];
		$result = $TestModel->create() && $TestModel->save($data);
		$this->assertFalse(empty($result));

		$expected = ['Article' => [
			'id' => '5',
			'user_id' => '1',
			'title' => 'Fifth Article - New Title',
			'body' => 'Fifth Article Body',
			'published' => 'N'
		]];
		$result = $TestModel->read(['id', 'user_id', 'title', 'body', 'published'], 5);
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveFromXml method
 *
 * @return void
 */
	public function testSaveFromXml() {
		$this->markTestSkipped('This feature needs to be fixed or dropped');
		$this->loadFixtures('Article');
		App::uses('Xml', 'Utility');

		$Article = new Article();
		$result = $Article->save(Xml::build('<article title="test xml" user_id="5" />'));
		$this->assertFalse(empty($result));
		$results = $Article->find('first', ['conditions' => ['Article.title' => 'test xml']]);
		$this->assertFalse(empty($results));

		$result = $Article->save(Xml::build('<article><title>testing</title><user_id>6</user_id></article>'));
		$this->assertFalse(empty($result));
		$results = $Article->find('first', ['conditions' => ['Article.title' => 'testing']]);
		$this->assertFalse(empty($results));

		$result = $Article->save(Xml::build('<article><title>testing with DOMDocument</title><user_id>7</user_id></article>', ['return' => 'domdocument']));
		$this->assertFalse(empty($result));
		$results = $Article->find('first', ['conditions' => ['Article.title' => 'testing with DOMDocument']]);
		$this->assertFalse(empty($results));
	}

/**
 * testSaveHabtm method
 *
 * @return void
 */
	public function testSaveHabtm() {
		$this->loadFixtures('Article', 'User', 'Comment', 'Tag', 'ArticlesTag');
		$TestModel = new Article();

		$result = $TestModel->findById(2);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'Second Article',
				'body' => 'Second Article Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:41:23',
				'updated' => '2007-03-18 10:43:31'
			],
			'User' => [
				'id' => '3',
				'user' => 'larry',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
				'created' => '2007-03-17 01:20:23',
				'updated' => '2007-03-17 01:22:31'
			],
			'Comment' => [
				[
					'id' => '5',
					'article_id' => '2',
					'user_id' => '1',
					'comment' => 'First Comment for Second Article',
					'published' => 'Y',
					'created' => '2007-03-18 10:53:23',
					'updated' => '2007-03-18 10:55:31'
				],
				[
					'id' => '6',
					'article_id' => '2',
					'user_id' => '2',
					'comment' => 'Second Comment for Second Article',
					'published' => 'Y',
					'created' => '2007-03-18 10:55:23',
					'updated' => '2007-03-18 10:57:31'
			]],
			'Tag' => [
				[
					'id' => '1',
					'tag' => 'tag1',
					'created' => '2007-03-18 12:22:23',
					'updated' => '2007-03-18 12:24:31'
				],
				[
					'id' => '3',
					'tag' => 'tag3',
					'created' => '2007-03-18 12:26:23',
					'updated' => '2007-03-18 12:28:31'
				]
			]
		];
		$this->assertEquals($expected, $result);

		$data = [
			'Article' => [
				'id' => '2',
				'title' => 'New Second Article'
			],
			'Tag' => ['Tag' => [1, 2]]
		];

		$result = $TestModel->set($data);
		$this->assertFalse(empty($result));
		$result = $TestModel->save();
		$this->assertFalse(empty($result));
		$this->assertEquals($data['Tag'], $result['Tag']);

		$TestModel->unbindModel(['belongsTo' => ['User'], 'hasMany' => ['Comment']]);
		$result = $TestModel->find('first', ['fields' => ['id', 'user_id', 'title', 'body'], 'conditions' => ['Article.id' => 2]]);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'New Second Article',
				'body' => 'Second Article Body'
			],
			'Tag' => [
				[
					'id' => '1',
					'tag' => 'tag1',
					'created' => '2007-03-18 12:22:23',
					'updated' => '2007-03-18 12:24:31'
				],
				[
					'id' => '2',
					'tag' => 'tag2',
					'created' => '2007-03-18 12:24:23',
					'updated' => '2007-03-18 12:26:31'
		]]];
		$this->assertEquals($expected, $result);

		$data = ['Article' => ['id' => '2'], 'Tag' => ['Tag' => [2, 3]]];
		$result = $TestModel->set($data);
		$this->assertFalse(empty($result));

		$result = $TestModel->save();
		$this->assertFalse(empty($result));

		$TestModel->unbindModel([
			'belongsTo' => ['User'],
			'hasMany' => ['Comment']
		]);
		$result = $TestModel->find('first', ['fields' => ['id', 'user_id', 'title', 'body'], 'conditions' => ['Article.id' => 2]]);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'New Second Article',
				'body' => 'Second Article Body'
			],
			'Tag' => [
				[
					'id' => '2',
					'tag' => 'tag2',
					'created' => '2007-03-18 12:24:23',
					'updated' => '2007-03-18 12:26:31'
				],
				[
					'id' => '3',
					'tag' => 'tag3',
					'created' => '2007-03-18 12:26:23',
					'updated' => '2007-03-18 12:28:31'
		]]];
		$this->assertEquals($expected, $result);

		$data = ['Tag' => ['Tag' => [1, 2, 3]]];

		$result = $TestModel->set($data);
		$this->assertFalse(empty($result));

		$result = $TestModel->save();
		$this->assertFalse(empty($result));

		$TestModel->unbindModel([
			'belongsTo' => ['User'],
			'hasMany' => ['Comment']
		]);
		$result = $TestModel->find('first', ['fields' => ['id', 'user_id', 'title', 'body'], 'conditions' => ['Article.id' => 2]]);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'New Second Article',
				'body' => 'Second Article Body'
			],
			'Tag' => [
				[
					'id' => '1',
					'tag' => 'tag1',
					'created' => '2007-03-18 12:22:23',
					'updated' => '2007-03-18 12:24:31'
				],
				[
					'id' => '2',
					'tag' => 'tag2',
					'created' => '2007-03-18 12:24:23',
					'updated' => '2007-03-18 12:26:31'
				],
				[
					'id' => '3',
					'tag' => 'tag3',
					'created' => '2007-03-18 12:26:23',
					'updated' => '2007-03-18 12:28:31'
		]]];
		$this->assertEquals($expected, $result);

		$data = ['Tag' => ['Tag' => []]];
		$result = $TestModel->set($data);
		$this->assertFalse(empty($result));

		$result = $TestModel->save();
		$this->assertFalse(empty($result));

		$data = ['Tag' => ['Tag' => '']];
		$result = $TestModel->set($data);
		$this->assertFalse(empty($result));

		$result = $TestModel->save();
		$this->assertFalse(empty($result));

		$TestModel->unbindModel([
			'belongsTo' => ['User'],
			'hasMany' => ['Comment']
		]);
		$result = $TestModel->find('first', ['fields' => ['id', 'user_id', 'title', 'body'], 'conditions' => ['Article.id' => 2]]);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'New Second Article',
				'body' => 'Second Article Body'
			],
			'Tag' => []
		];
		$this->assertEquals($expected, $result);

		$data = ['Tag' => ['Tag' => [2, 3]]];
		$result = $TestModel->set($data);
		$this->assertFalse(empty($result));

		$result = $TestModel->save();
		$this->assertFalse(empty($result));

		$TestModel->unbindModel([
			'belongsTo' => ['User'],
			'hasMany' => ['Comment']
		]);
		$result = $TestModel->find('first', ['fields' => ['id', 'user_id', 'title', 'body'], 'conditions' => ['Article.id' => 2]]);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'New Second Article',
				'body' => 'Second Article Body'
			],
			'Tag' => [
				[
					'id' => '2',
					'tag' => 'tag2',
					'created' => '2007-03-18 12:24:23',
					'updated' => '2007-03-18 12:26:31'
				],
				[
					'id' => '3',
					'tag' => 'tag3',
					'created' => '2007-03-18 12:26:23',
					'updated' => '2007-03-18 12:28:31'
		]]];
		$this->assertEquals($expected, $result);

		$data = [
			'Tag' => [
				'Tag' => [1, 2]
			],
			'Article' => [
				'id' => '2',
				'title' => 'New Second Article'
		]];
		$result = $TestModel->set($data);
		$this->assertFalse(empty($result));
		$result = $TestModel->save();
		$this->assertFalse(empty($result));

		$TestModel->unbindModel([
			'belongsTo' => ['User'],
			'hasMany' => ['Comment']
		]);
		$result = $TestModel->find('first', ['fields' => ['id', 'user_id', 'title', 'body'], 'conditions' => ['Article.id' => 2]]);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'New Second Article',
				'body' => 'Second Article Body'
			],
			'Tag' => [
				[
					'id' => '1',
					'tag' => 'tag1',
					'created' => '2007-03-18 12:22:23',
					'updated' => '2007-03-18 12:24:31'
				],
				[
					'id' => '2',
					'tag' => 'tag2',
					'created' => '2007-03-18 12:24:23',
					'updated' => '2007-03-18 12:26:31'
		]]];
		$this->assertEquals($expected, $result);

		$data = [
			'Tag' => [
				'Tag' => [1, 2]
			],
			'Article' => [
				'id' => '2',
				'title' => 'New Second Article Title'
		]];
		$result = $TestModel->set($data);
		$this->assertFalse(empty($result));
		$result = $TestModel->save();
		$this->assertFalse(empty($result));

		$TestModel->unbindModel([
			'belongsTo' => ['User'],
			'hasMany' => ['Comment']
		]);
		$result = $TestModel->find('first', ['fields' => ['id', 'user_id', 'title', 'body'], 'conditions' => ['Article.id' => 2]]);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'New Second Article Title',
				'body' => 'Second Article Body'
			],
			'Tag' => [
				[
					'id' => '1',
					'tag' => 'tag1',
					'created' => '2007-03-18 12:22:23',
					'updated' => '2007-03-18 12:24:31'
				],
				[
					'id' => '2',
					'tag' => 'tag2',
					'created' => '2007-03-18 12:24:23',
					'updated' => '2007-03-18 12:26:31'
				]
			]
		];
		$this->assertEquals($expected, $result);

		$data = [
			'Tag' => [
				'Tag' => [2, 3]
			],
			'Article' => [
				'id' => '2',
				'title' => 'Changed Second Article'
		]];
		$result = $TestModel->set($data);
		$this->assertFalse(empty($result));
		$result = $TestModel->save();
		$this->assertFalse(empty($result));

		$TestModel->unbindModel([
			'belongsTo' => ['User'],
			'hasMany' => ['Comment']
		]);
		$result = $TestModel->find('first', ['fields' => ['id', 'user_id', 'title', 'body'], 'conditions' => ['Article.id' => 2]]);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'Changed Second Article',
				'body' => 'Second Article Body'
			],
			'Tag' => [
				[
					'id' => '2',
					'tag' => 'tag2',
					'created' => '2007-03-18 12:24:23',
					'updated' => '2007-03-18 12:26:31'
				],
				[
					'id' => '3',
					'tag' => 'tag3',
					'created' => '2007-03-18 12:26:23',
					'updated' => '2007-03-18 12:28:31'
				]
			]
		];
		$this->assertEquals($expected, $result);

		$data = [
			'Tag' => [
				'Tag' => [1, 3]
			],
			'Article' => ['id' => '2'],
		];

		$result = $TestModel->set($data);
		$this->assertFalse(empty($result));

		$result = $TestModel->save();
		$this->assertFalse(empty($result));

		$TestModel->unbindModel([
			'belongsTo' => ['User'],
			'hasMany' => ['Comment']
		]);
		$result = $TestModel->find('first', ['fields' => ['id', 'user_id', 'title', 'body'], 'conditions' => ['Article.id' => 2]]);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'Changed Second Article',
				'body' => 'Second Article Body'
			],
			'Tag' => [
				[
					'id' => '1',
					'tag' => 'tag1',
					'created' => '2007-03-18 12:22:23',
					'updated' => '2007-03-18 12:24:31'
				],
				[
					'id' => '3',
					'tag' => 'tag3',
					'created' => '2007-03-18 12:26:23',
					'updated' => '2007-03-18 12:28:31'
		]]];
		$this->assertEquals($expected, $result);

		$data = [
			'Article' => [
				'id' => 10,
				'user_id' => '2',
				'title' => 'New Article With Tags and fieldList',
				'body' => 'New Article Body with Tags and fieldList',
				'created' => '2007-03-18 14:55:23',
				'updated' => '2007-03-18 14:57:31'
			],
			'Tag' => [
				'Tag' => [1, 2, 3]
			]
		];
		$result = $TestModel->create()
				&& $TestModel->save($data, true, ['user_id', 'title', 'published']);
		$this->assertFalse(empty($result));

		$TestModel->unbindModel([
			'belongsTo' => ['User'],
			'hasMany' => ['Comment']
		]);
		$result = $TestModel->read();
		$expected = [
			'Article' => [
				'id' => 4,
				'user_id' => 2,
				'title' => 'New Article With Tags and fieldList',
				'body' => '',
				'published' => 'N',
				'created' => static::date(),
				'updated' => static::date(),
			],
			'Tag' => [
				0 => [
					'id' => 1,
					'tag' => 'tag1',
					'created' => '2007-03-18 12:22:23',
					'updated' => '2007-03-18 12:24:31'
				],
				1 => [
					'id' => 2,
					'tag' => 'tag2',
					'created' => '2007-03-18 12:24:23',
					'updated' => '2007-03-18 12:26:31'
				],
				2 => [
					'id' => 3,
					'tag' => 'tag3',
					'created' => '2007-03-18 12:26:23',
					'updated' => '2007-03-18 12:28:31'
		]]];
		$this->assertEquals($expected, $result);

		$this->loadFixtures('JoinA', 'JoinC', 'JoinAC', 'JoinB', 'JoinAB');
		$TestModel = new JoinA();
		$TestModel->hasBelongsToMany = ['JoinC' => ['unique' => true]];
		$data = [
			'JoinA' => [
				'id' => 1,
				'name' => 'Join A 1',
				'body' => 'Join A 1 Body',
			],
			'JoinC' => [
				'JoinC' => [
					['join_c_id' => 2, 'other' => 'new record'],
					['join_c_id' => 3, 'other' => 'new record']
				]
			]
		];
		$TestModel->save($data);
		$result = $TestModel->read(null, 1);
		$expected = [4, 5];
		$this->assertEquals($expected, Hash::extract($result, 'JoinC.{n}.JoinAsJoinC.id'));
		$expected = ['new record', 'new record'];
		$this->assertEquals($expected, Hash::extract($result, 'JoinC.{n}.JoinAsJoinC.other'));
	}

/**
 * test that saving HABTM with an empty array will clear existing HABTM if
 * unique is true
 *
 * @return void
 */
	public function testSaveHabtmEmptyData() {
		$this->loadFixtures('Node', 'Dependency');
		$Node = new Node();

		$data = [
			'Node' => ['name' => 'New First']
		];
		$Node->id = 1;
		$Node->save($data);

		$node = $Node->find('first', [
			'conditions' => ['Node.id' => 1],
			'contain' => ['ParentNode']
		]);

		$result = Hash::extract($node, 'ParentNode.{n}.id');
		$expected = [2];
		$this->assertEquals($expected, $result);

		$data = [
			'ParentNode' => []
		];
		$Node->id = 1;
		$Node->save($data);

		$node = $Node->find('first', [
			'conditions' => ['Node.id' => 1],
			'contain' => ['ParentNode']
		]);

		$result = Hash::extract($node, 'ParentNode.{n}.id');
		$expected = [];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveHabtmNoPrimaryData method
 *
 * @return void
 */
	public function testSaveHabtmNoPrimaryData() {
		$this->loadFixtures('Article', 'User', 'Comment', 'Tag', 'ArticlesTag');
		$TestModel = new Article();

		$TestModel->unbindModel(['belongsTo' => ['User'], 'hasMany' => ['Comment']], false);
		$result = $TestModel->findById(2);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'Second Article',
				'body' => 'Second Article Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:41:23',
				'updated' => '2007-03-18 10:43:31'
			],
			'Tag' => [
				[
					'id' => '1',
					'tag' => 'tag1',
					'created' => '2007-03-18 12:22:23',
					'updated' => '2007-03-18 12:24:31'
				],
				[
					'id' => '3',
					'tag' => 'tag3',
					'created' => '2007-03-18 12:26:23',
					'updated' => '2007-03-18 12:28:31'
				]
			]
		];
		$this->assertEquals($expected, $result);

		$TestModel->id = 2;
		$data = ['Tag' => ['Tag' => [2]]];
		$result = $TestModel->save($data);

		$this->assertEquals($data['Tag'], $result['Tag']);

		$result = $TestModel->findById(2);
		$expected = [
			'Article' => [
				'id' => '2',
				'user_id' => '3',
				'title' => 'Second Article',
				'body' => 'Second Article Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:41:23',
				'updated' => static::date()
			],
			'Tag' => [
				[
					'id' => '2',
					'tag' => 'tag2',
					'created' => '2007-03-18 12:24:23',
					'updated' => '2007-03-18 12:26:31'
				]
			]
		];
		$this->assertEquals($expected, $result);

		$this->loadFixtures('Portfolio', 'Item', 'ItemsPortfolio');
		$TestModel = new Portfolio();
		$result = $TestModel->findById(2);
		$expected = [
			'Portfolio' => [
				'id' => 2,
				'seller_id' => 1,
				'name' => 'Portfolio 2'
			],
			'Item' => [
				[
					'id' => 2,
					'syfile_id' => 2,
					'published' => '',
					'name' => 'Item 2',
					'ItemsPortfolio' => [
						'id' => 2,
						'item_id' => 2,
						'portfolio_id' => 2
					]
				],
				[
					'id' => 6,
					'syfile_id' => 6,
					'published' => '',
					'name' => 'Item 6',
					'ItemsPortfolio' => [
						'id' => 6,
						'item_id' => 6,
						'portfolio_id' => 2
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$data = ['Item' => ['Item' => [1, 2]]];
		$TestModel->id = 2;
		$result = $TestModel->save($data);
		$this->assertTrue((bool)$result);

		$result = $TestModel->findById(2);
		$result['Item'] = Hash::sort($result['Item'], '{n}.id', 'asc');
		$expected = [
			'Portfolio' => [
				'id' => 2,
				'seller_id' => 1,
				'name' => 'Portfolio 2'
			],
			'Item' => [
				[
					'id' => 1,
					'syfile_id' => 1,
					'published' => '',
					'name' => 'Item 1',
					'ItemsPortfolio' => [
						'id' => 7,
						'item_id' => 1,
						'portfolio_id' => 2
					]
				],
				[
					'id' => 2,
					'syfile_id' => 2,
					'published' => '',
					'name' => 'Item 2',
					'ItemsPortfolio' => [
						'id' => 8,
						'item_id' => 2,
						'portfolio_id' => 2
					]
				]
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveHabtmCustomKeys method
 *
 * @return void
 */
	public function testSaveHabtmCustomKeys() {
		$this->loadFixtures('Story', 'StoriesTag', 'Tag');
		$Story = new Story();

		$data = [
			'Story' => ['story' => '1'],
			'Tag' => [
				'Tag' => [2, 3]
		]];
		$result = $Story->set($data);
		$this->assertFalse(empty($result));

		$result = $Story->save();
		$this->assertFalse(empty($result));

		$result = $Story->find('all', ['order' => ['Story.story']]);
		$expected = [
			[
				'Story' => [
					'story' => 1,
					'title' => 'First Story'
				],
				'Tag' => [
					[
						'id' => 2,
						'tag' => 'tag2',
						'created' => '2007-03-18 12:24:23',
						'updated' => '2007-03-18 12:26:31'
					],
					[
						'id' => 3,
						'tag' => 'tag3',
						'created' => '2007-03-18 12:26:23',
						'updated' => '2007-03-18 12:28:31'
			]]],
			[
				'Story' => [
					'story' => 2,
					'title' => 'Second Story'
				],
				'Tag' => []
		]];
		$this->assertEquals($expected, $result);
	}

/**
 * test that saving habtm records respects conditions set in the 'conditions' key
 * for the association.
 *
 * @return void
 */
	public function testHabtmSaveWithConditionsInAssociation() {
		$this->loadFixtures('JoinThing', 'Something', 'SomethingElse');
		$Something = new Something();
		$Something->unbindModel(['hasAndBelongsToMany' => ['SomethingElse']], false);

		$Something->bindModel([
			'hasAndBelongsToMany' => [
				'DoomedSomethingElse' => [
					'className' => 'SomethingElse',
					'joinTable' => 'join_things',
					'conditions' => ['JoinThing.doomed' => true],
					'unique' => true
				],
				'NotDoomedSomethingElse' => [
					'className' => 'SomethingElse',
					'joinTable' => 'join_things',
					'conditions' => ['JoinThing.doomed' => 0],
					'unique' => true
				]
			]
		], false);
		$result = $Something->read(null, 1);
		$this->assertTrue(empty($result['NotDoomedSomethingElse']));
		$this->assertEquals(1, count($result['DoomedSomethingElse']));

		$data = [
			'Something' => ['id' => 1],
			'NotDoomedSomethingElse' => [
				'NotDoomedSomethingElse' => [
					['something_else_id' => 2, 'doomed' => 0],
					['something_else_id' => 3, 'doomed' => 0]
				]
			]
		];
		$Something->create($data);
		$result = $Something->save();
		$this->assertFalse(empty($result));

		$result = $Something->read(null, 1);
		$this->assertEquals(2, count($result['NotDoomedSomethingElse']));
		$this->assertEquals(1, count($result['DoomedSomethingElse']));
	}

/**
 * testHabtmSaveKeyResolution method
 *
 * @return void
 */
	public function testHabtmSaveKeyResolution() {
		$this->loadFixtures('Apple', 'Device', 'ThePaperMonkies');
		$ThePaper = new ThePaper();

		$ThePaper->id = 1;
		$ThePaper->save(['Monkey' => [2, 3]]);

		$result = $ThePaper->findById(1);
		$expected = [
			[
				'id' => '2',
				'device_type_id' => '1',
				'name' => 'Device 2',
				'typ' => '1'
			],
			[
				'id' => '3',
				'device_type_id' => '1',
				'name' => 'Device 3',
				'typ' => '2'
		]];
		$this->assertEquals($expected, $result['Monkey']);

		$ThePaper->id = 2;
		$ThePaper->save(['Monkey' => [1, 2, 3]]);

		$result = $ThePaper->findById(2);
		$expected = [
			[
				'id' => '1',
				'device_type_id' => '1',
				'name' => 'Device 1',
				'typ' => '1'
			],
			[
				'id' => '2',
				'device_type_id' => '1',
				'name' => 'Device 2',
				'typ' => '1'
			],
			[
				'id' => '3',
				'device_type_id' => '1',
				'name' => 'Device 3',
				'typ' => '2'
		]];
		$this->assertEquals($expected, $result['Monkey']);

		$ThePaper->id = 2;
		$ThePaper->save(['Monkey' => [1, 3]]);

		$result = $ThePaper->findById(2);
		$expected = [
			[
				'id' => '1',
				'device_type_id' => '1',
				'name' => 'Device 1',
				'typ' => '1'
			],
			[
				'id' => '3',
				'device_type_id' => '1',
				'name' => 'Device 3',
				'typ' => '2'
			]];
		$this->assertEquals($expected, $result['Monkey']);

		$result = $ThePaper->findById(1);
		$expected = [
			[
				'id' => '2',
				'device_type_id' => '1',
				'name' => 'Device 2',
				'typ' => '1'
			],
			[
				'id' => '3',
				'device_type_id' => '1',
				'name' => 'Device 3',
				'typ' => '2'
		]];
		$this->assertEquals($expected, $result['Monkey']);
	}

/**
 * testCreationOfEmptyRecord method
 *
 * @return void
 */
	public function testCreationOfEmptyRecord() {
		$this->loadFixtures('Author');
		$TestModel = new Author();
		$this->assertEquals(4, $TestModel->find('count'));

		$TestModel->deleteAll(true, false, false);
		$this->assertEquals(0, $TestModel->find('count'));

		$result = $TestModel->save();
		$this->assertTrue(isset($result['Author']['created']));
		$this->assertTrue(isset($result['Author']['updated']));
		$this->assertEquals(1, $TestModel->find('count'));
	}

/**
 * testCreateWithPKFiltering method
 *
 * @return void
 */
	public function testCreateWithPKFiltering() {
		$TestModel = new Article();
		$data = [
			'id' => 5,
			'user_id' => 2,
			'title' => 'My article',
			'body' => 'Some text'
		];

		$result = $TestModel->create($data);
		$expected = [
			'Article' => [
				'published' => 'N',
				'id' => 5,
				'user_id' => 2,
				'title' => 'My article',
				'body' => 'Some text'
		]];

		$this->assertEquals($expected, $result);
		$this->assertEquals(5, $TestModel->id);

		$result = $TestModel->create($data, true);
		$expected = [
			'Article' => [
				'published' => 'N',
				'id' => false,
				'user_id' => 2,
				'title' => 'My article',
				'body' => 'Some text'
		]];

		$this->assertEquals($expected, $result);
		$this->assertFalse($TestModel->id);

		$result = $TestModel->create(['Article' => $data], true);
		$expected = [
			'Article' => [
				'published' => 'N',
				'id' => false,
				'user_id' => 2,
				'title' => 'My article',
				'body' => 'Some text'
		]];

		$this->assertEquals($expected, $result);
		$this->assertFalse($TestModel->id);

		$data = [
			'id' => 6,
			'user_id' => 2,
			'title' => 'My article',
			'body' => 'Some text',
			'created' => '1970-01-01 00:00:00',
			'updated' => '1970-01-01 12:00:00',
			'modified' => '1970-01-01 12:00:00'
		];

		$result = $TestModel->create($data);
		$expected = [
			'Article' => [
				'published' => 'N',
				'id' => 6,
				'user_id' => 2,
				'title' => 'My article',
				'body' => 'Some text',
				'created' => '1970-01-01 00:00:00',
				'updated' => '1970-01-01 12:00:00',
				'modified' => '1970-01-01 12:00:00'
		]];
		$this->assertEquals($expected, $result);
		$this->assertEquals(6, $TestModel->id);

		$result = $TestModel->create([
			'Article' => array_diff_key($data, [
				'created' => true,
				'updated' => true,
				'modified' => true
		])], true);
		$expected = [
			'Article' => [
				'published' => 'N',
				'id' => false,
				'user_id' => 2,
				'title' => 'My article',
				'body' => 'Some text'
		]];
		$this->assertEquals($expected, $result);
		$this->assertFalse($TestModel->id);
	}

/**
 * testCreationWithMultipleData method
 *
 * @return void
 */
	public function testCreationWithMultipleData() {
		$this->loadFixtures('Article', 'Comment');
		$Article = new Article();
		$Comment = new Comment();

		$articles = $Article->find('all', [
			'fields' => ['id', 'title'],
			'recursive' => -1,
			'order' => ['Article.id' => 'ASC']
		]);
		$expected = [
			['Article' => [
				'id' => 1,
				'title' => 'First Article'
			]],
			['Article' => [
				'id' => 2,
				'title' => 'Second Article'
			]],
			['Article' => [
				'id' => 3,
				'title' => 'Third Article'
		]]];
		$this->assertEquals($expected, $articles);

		$comments = $Comment->find('all', [
			'fields' => ['id', 'article_id', 'user_id', 'comment', 'published'],
			'recursive' => -1,
			'order' => ['Comment.id' => 'ASC']
		]);
		$expected = [
			['Comment' => [
				'id' => 1,
				'article_id' => 1,
				'user_id' => 2,
				'comment' => 'First Comment for First Article',
				'published' => 'Y'
			]],
			['Comment' => [
				'id' => 2,
				'article_id' => 1,
				'user_id' => 4,
				'comment' => 'Second Comment for First Article',
				'published' => 'Y'
			]],
			['Comment' => [
				'id' => 3,
				'article_id' => 1,
				'user_id' => 1,
				'comment' => 'Third Comment for First Article',
				'published' => 'Y'
			]],
			['Comment' => [
				'id' => 4,
				'article_id' => 1,
				'user_id' => 1,
				'comment' => 'Fourth Comment for First Article',
				'published' => 'N'
			]],
			['Comment' => [
				'id' => 5,
				'article_id' => 2,
				'user_id' => 1,
				'comment' => 'First Comment for Second Article',
				'published' => 'Y'
			]],
			['Comment' => [
				'id' => 6,
				'article_id' => 2,
				'user_id' => 2,
				'comment' => 'Second Comment for Second Article',
				'published' => 'Y'
		]]];
		$this->assertEquals($expected, $comments);

		$data = [
			'Comment' => [
				'article_id' => 2,
				'user_id' => 4,
				'comment' => 'Brand New Comment',
				'published' => 'N'
			],
			'Article' => [
				'id' => 2,
				'title' => 'Second Article Modified'
		]];
		$result = $Comment->create($data);
		$this->assertFalse(empty($result));

		$result = $Comment->save();
		$this->assertFalse(empty($result));

		$articles = $Article->find('all', [
			'fields' => ['id', 'title'],
			'recursive' => -1,
			'order' => ['Article.id' => 'ASC']
		]);
		$expected = [
			['Article' => [
				'id' => 1,
				'title' => 'First Article'
			]],
			['Article' => [
				'id' => 2,
				'title' => 'Second Article'
			]],
			['Article' => [
				'id' => 3,
				'title' => 'Third Article'
		]]];
		$this->assertEquals($expected, $articles);

		$comments = $Comment->find('all', [
			'fields' => ['id', 'article_id', 'user_id', 'comment', 'published'],
			'recursive' => -1,
			'order' => ['Comment.id' => 'ASC']
		]);
		$expected = [
			['Comment' => [
				'id' => 1,
				'article_id' => 1,
				'user_id' => 2,
				'comment' => 'First Comment for First Article',
				'published' => 'Y'
			]],
			['Comment' => [
				'id' => 2,
				'article_id' => 1,
				'user_id' => 4,
				'comment' => 'Second Comment for First Article',
				'published' => 'Y'
			]],
			['Comment' => [
				'id' => 3,
				'article_id' => 1,
				'user_id' => 1,
				'comment' => 'Third Comment for First Article',
				'published' => 'Y'
			]],
			['Comment' => [
				'id' => 4,
				'article_id' => 1,
				'user_id' => 1,
				'comment' => 'Fourth Comment for First Article',
				'published' => 'N'
			]],
			['Comment' => [
				'id' => 5,
				'article_id' => 2,
				'user_id' => 1,
				'comment' => 'First Comment for Second Article',
				'published' => 'Y'
			]],
			['Comment' => [
				'id' => 6,
				'article_id' => 2,
				'user_id' => 2, 'comment' =>
				'Second Comment for Second Article',
				'published' => 'Y'
			]],
			['Comment' => [
				'id' => 7,
				'article_id' => 2,
				'user_id' => 4,
				'comment' => 'Brand New Comment',
				'published' => 'N'
		]]];
		$this->assertEquals($expected, $comments);
	}

/**
 * testCreationWithMultipleDataSameModel method
 *
 * @return void
 */
	public function testCreationWithMultipleDataSameModel() {
		$this->loadFixtures('Article');
		$Article = new Article();

		$result = $Article->field('title', ['id' => 1]);
		$this->assertEquals('First Article', $result);

		$data = [
			'Article' => [
				'user_id' => 2,
				'title' => 'Brand New Article',
				'body' => 'Brand New Article Body',
				'published' => 'Y'
			],
			'SecondaryArticle' => [
				'id' => 1
		]];

		$Article->create();
		$result = $Article->save($data);
		$this->assertFalse(empty($result));

		$result = $Article->getInsertID();
		$this->assertTrue(!empty($result));

		$result = $Article->field('title', ['id' => 1]);
		$this->assertEquals('First Article', $result);

		$articles = $Article->find('all', [
			'fields' => ['id', 'title'],
			'recursive' => -1,
			'order' => ['Article.id' => 'ASC']
		]);
		$expected = [
			['Article' => [
				'id' => 1,
				'title' => 'First Article'
			]],
			['Article' => [
				'id' => 2,
				'title' => 'Second Article'
			]],
			['Article' => [
				'id' => 3,
				'title' => 'Third Article'
			]],
			['Article' => [
				'id' => 4,
				'title' => 'Brand New Article'
		]]];

		$this->assertEquals($expected, $articles);
	}

/**
 * testCreationWithMultipleDataSameModelManualInstances method
 *
 * @return void
 */
	public function testCreationWithMultipleDataSameModelManualInstances() {
		$this->loadFixtures('PrimaryModel');
		$Primary = new PrimaryModel();

		$result = $Primary->field('primary_name', ['id' => 1]);
		$this->assertEquals('Primary Name Existing', $result);

		$data = [
			'PrimaryModel' => [
				'primary_name' => 'Primary Name New'
			],
			'SecondaryModel' => [
				'id' => [1]
		]];

		$Primary->create();
		$result = $Primary->save($data);
		$this->assertFalse(empty($result));

		$result = $Primary->field('primary_name', ['id' => 1]);
		$this->assertEquals('Primary Name Existing', $result);

		$result = $Primary->getInsertID();
		$this->assertTrue(!empty($result));

		$result = $Primary->field('primary_name', ['id' => $result]);
		$this->assertEquals('Primary Name New', $result);

		$result = $Primary->find('count');
		$this->assertEquals(2, $result);
	}

/**
 * testRecordExists method
 *
 * @return void
 */
	public function testRecordExists() {
		$this->loadFixtures('User');
		$TestModel = new User();

		$this->assertFalse($TestModel->exists());
		$TestModel->read(null, 1);
		$this->assertTrue($TestModel->exists());
		$TestModel->create();
		$this->assertFalse($TestModel->exists());
		$TestModel->id = 4;
		$this->assertTrue($TestModel->exists());

		$TestModel = new TheVoid();
		$this->assertFalse($TestModel->exists());

		$TestModel->id = 5;
		$this->assertFalse($TestModel->exists());
	}

/**
 * testUpdateExisting method
 *
 * @return void
 */
	public function testUpdateExisting() {
		$this->loadFixtures('User', 'Article', 'Comment');
		$TestModel = new User();
		$TestModel->create();

		$TestModel->save([
			'User' => [
				'user' => 'some user',
				'password' => 'some password'
		]]);
		$this->assertTrue(is_int($TestModel->id) || ((int)$TestModel->id === 5));
		$id = $TestModel->id;

		$TestModel->save([
			'User' => [
				'user' => 'updated user'
		]]);
		$this->assertEquals($id, $TestModel->id);

		$result = $TestModel->findById($id);
		$this->assertEquals('updated user', $result['User']['user']);
		$this->assertEquals('some password', $result['User']['password']);

		$Article = new Article();
		$Comment = new Comment();
		$data = [
			'Comment' => [
				'id' => 1,
				'comment' => 'First Comment for First Article'
			],
			'Article' => [
				'id' => 2,
				'title' => 'Second Article'
		]];

		$result = $Article->save($data);
		$this->assertFalse(empty($result));

		$result = $Comment->save($data);
		$this->assertFalse(empty($result));
	}

/**
 * test updating records and saving blank values.
 *
 * @return void
 */
	public function testUpdateSavingBlankValues() {
		$this->loadFixtures('Article');
		$Article = new Article();
		$Article->validate = [];
		$Article->create();
		$result = $Article->save([
			'id' => 1,
			'title' => '',
			'body' => ''
		]);
		$this->assertTrue((bool)$result);
		$result = $Article->find('first', ['conditions' => ['Article.id' => 1]]);
		$this->assertEquals('', $result['Article']['title'], 'Title is not blank');
		$this->assertEquals('', $result['Article']['body'], 'Body is not blank');
	}

/**
 * testUpdateMultiple method
 *
 * @return void
 */
	public function testUpdateMultiple() {
		$this->loadFixtures('Comment', 'Article', 'User', 'CategoryThread');
		$TestModel = new Comment();
		$result = Hash::extract($TestModel->find('all'), '{n}.Comment.user_id');
		$expected = ['2', '4', '1', '1', '1', '2'];
		$this->assertEquals($expected, $result);

		$TestModel->updateAll(['Comment.user_id' => 5], ['Comment.user_id' => 2]);
		$result = Hash::combine($TestModel->find('all'), '{n}.Comment.id', '{n}.Comment.user_id');
		$expected = [1 => 5, 2 => 4, 3 => 1, 4 => 1, 5 => 1, 6 => 5];
		$this->assertEquals($expected, $result);

		$result = $TestModel->updateAll(
			['Comment.comment' => "'Updated today'"],
			['Comment.user_id' => 5]
		);
		$this->assertFalse(empty($result));
		$result = Hash::extract(
			$TestModel->find('all', [
				'conditions' => [
					'Comment.user_id' => 5
			]]),
			'{n}.Comment.comment'
		);
		$expected = array_fill(0, 2, 'Updated today');
		$this->assertEquals($expected, $result);
	}

/**
 * testHabtmUuidWithUuidId method
 *
 * @return void
 */
	public function testHabtmUuidWithUuidId() {
		$this->loadFixtures('Uuidportfolio', 'Uuiditem', 'UuiditemsUuidportfolio', 'UuiditemsUuidportfolioNumericid');
		$TestModel = new Uuidportfolio();

		$data = ['Uuidportfolio' => ['name' => 'Portfolio 3']];
		$data['Uuiditem']['Uuiditem'] = ['483798c8-c7cc-430e-8cf9-4fcc40cf8569'];
		$TestModel->create($data);
		$TestModel->save();
		$id = $TestModel->id;
		$result = $TestModel->read(null, $id);
		$this->assertEquals(1, count($result['Uuiditem']));
		$this->assertEquals(36, strlen($result['Uuiditem'][0]['UuiditemsUuidportfolio']['id']));
	}

/**
 * testHabtmUuidWithUuidId method
 *
 * @return void
 */
	public function testHabtmUuidWithUuidIdNative() {
		$this->skipIf(!($this->db instanceof Postgres), 'This test is compatible with Postgres only.');
		$this->loadFixtures('Uuidnativeportfolio', 'Uuidnativeitem', 'UuidnativeitemsUuidnativeportfolio', 'UuidnativeitemsUuidnativeportfolioNumericid');
		$TestModel = new Uuidnativeportfolio();

		$data = ['Uuidnativeportfolio' => ['name' => 'Portfolio 3']];
		$data['Uuidnativeitem']['Uuidnativeitem'] = ['483798c8-c7cc-430e-8cf9-4fcc40cf8569'];
		$TestModel->create($data);
		$TestModel->save();
		$id = $TestModel->id;
		$result = $TestModel->read(null, $id);
		$this->assertEquals(1, count($result['Uuidnativeitem']));
		$this->assertEquals(36, strlen($result['Uuidnativeitem'][0]['UuidnativeitemsUuidnativeportfolio']['id']));
	}

/**
 * test HABTM saving when join table has no primary key and only 2 columns.
 *
 * @return void
 */
	public function testHabtmSavingWithNoPrimaryKeyUuidJoinTable() {
		$this->loadFixtures('UuidTag', 'Fruit', 'FruitsUuidTag');
		$Fruit = new Fruit();
		$Fruit->FruitsUuidTag->order = null;
		$data = [
			'Fruit' => [
				'color' => 'Red',
				'shape' => 'Heart-shaped',
				'taste' => 'sweet',
				'name' => 'Strawberry',
			],
			'UuidTag' => [
				'UuidTag' => [
					'481fc6d0-b920-43e0-e50f-6d1740cf8569'
				]
			]
		];
		$result = $Fruit->save($data);
		$this->assertFalse(empty($result));
	}

/**
 * test HABTM saving when join table has no primary key and only 2 columns, no with model is used.
 *
 * @return void
 */
	public function testHabtmSavingWithNoPrimaryKeyUuidJoinTableNoWith() {
		$this->loadFixtures('UuidTag', 'Fruit', 'FruitsUuidTag');
		$Fruit = new FruitNoWith();
		$data = [
			'Fruit' => [
				'color' => 'Red',
				'shape' => 'Heart-shaped',
				'taste' => 'sweet',
				'name' => 'Strawberry',
			],
			'UuidTag' => [
				'UuidTag' => [
					'481fc6d0-b920-43e0-e50f-6d1740cf8569'
				]
			]
		];
		$result = $Fruit->save($data);
		$this->assertFalse(empty($result));
	}

/**
 * testHabtmUuidWithNumericId method
 *
 * @return void
 */
	public function testHabtmUuidWithNumericId() {
		$this->loadFixtures('Uuidportfolio', 'Uuiditem', 'UuiditemsUuidportfolioNumericid');
		$TestModel = new Uuiditem();

		$data = ['Uuiditem' => ['name' => 'Item 7', 'published' => 0]];
		$data['Uuidportfolio']['Uuidportfolio'] = ['480af662-eb8c-47d3-886b-230540cf8569'];
		$TestModel->create($data);
		$TestModel->save();
		$id = $TestModel->id;
		$result = $TestModel->read(null, $id);
		$this->assertEquals(1, count($result['Uuidportfolio']));
	}

/**
 * testHabtmUuidWithNumericId method
 *
 * @return void
 */
	public function testHabtmUuidWithNumericIdNative() {
		$this->skipIf(!($this->db instanceof Postgres), 'This test is compatible with Postgres only.');
		$this->loadFixtures('Uuidnativeportfolio', 'Uuidnativeitem', 'UuidnativeitemsUuidnativeportfolioNumericid');
		$TestModel = new Uuidnativeitem();

		$data = ['Uuidnativeitem' => ['name' => 'Item 7', 'published' => 0]];
		$data['Uuidnativeportfolio']['Uuidnativeportfolio'] = ['480af662-eb8c-47d3-886b-230540cf8569'];
		$TestModel->create($data);
		$TestModel->save();
		$id = $TestModel->id;
		$result = $TestModel->read(null, $id);
		$this->assertEquals(1, count($result['Uuidnativeportfolio']));
	}

/**
 * testSaveMultipleHabtm method
 *
 * @return void
 */
	public function testSaveMultipleHabtm() {
		$this->loadFixtures('JoinA', 'JoinB', 'JoinC', 'JoinAB', 'JoinAC');
		$TestModel = new JoinA();
		$result = $TestModel->findById(1);

		$expected = [
			'JoinA' => [
				'id' => 1,
				'name' => 'Join A 1',
				'body' => 'Join A 1 Body',
				'created' => '2008-01-03 10:54:23',
				'updated' => '2008-01-03 10:54:23'
			],
			'JoinB' => [
				0 => [
					'id' => 2,
					'name' => 'Join B 2',
					'created' => '2008-01-03 10:55:02',
					'updated' => '2008-01-03 10:55:02',
					'JoinAsJoinB' => [
						'id' => 1,
						'join_a_id' => 1,
						'join_b_id' => 2,
						'other' => 'Data for Join A 1 Join B 2',
						'created' => '2008-01-03 10:56:33',
						'updated' => '2008-01-03 10:56:33'
			]]],
			'JoinC' => [
				0 => [
					'id' => 2,
					'name' => 'Join C 2',
					'created' => '2008-01-03 10:56:12',
					'updated' => '2008-01-03 10:56:12',
					'JoinAsJoinC' => [
						'id' => 1,
						'join_a_id' => 1,
						'join_c_id' => 2,
						'other' => 'Data for Join A 1 Join C 2',
						'created' => '2008-01-03 10:57:22',
						'updated' => '2008-01-03 10:57:22'
		]]]];

		$this->assertEquals($expected, $result);

		$TestModel->id = 1;
		$data = [
			'JoinA' => [
				'id' => '1',
				'name' => 'New name for Join A 1',
				'updated' => static::date()
			],
			'JoinB' => [
				[
					'id' => 1,
					'join_b_id' => 2,
					'other' => 'New data for Join A 1 Join B 2',
					'created' => static::date(),
					'updated' => static::date()
			]],
			'JoinC' => [
				[
					'id' => 1,
					'join_c_id' => 2,
					'other' => 'New data for Join A 1 Join C 2',
					'created' => static::date(),
					'updated' => static::date()
		]]];

		$TestModel->set($data);
		$TestModel->save();

		$result = $TestModel->findById(1);
		$expected = [
			'JoinA' => [
				'id' => 1,
				'name' => 'New name for Join A 1',
				'body' => 'Join A 1 Body',
				'created' => '2008-01-03 10:54:23',
				'updated' => static::date()
			],
			'JoinB' => [
				0 => [
					'id' => 2,
					'name' => 'Join B 2',
					'created' => '2008-01-03 10:55:02',
					'updated' => '2008-01-03 10:55:02',
					'JoinAsJoinB' => [
						'id' => 1,
						'join_a_id' => 1,
						'join_b_id' => 2,
						'other' => 'New data for Join A 1 Join B 2',
						'created' => static::date(),
						'updated' => static::date()
			]]],
			'JoinC' => [
				0 => [
					'id' => 2,
					'name' => 'Join C 2',
					'created' => '2008-01-03 10:56:12',
					'updated' => '2008-01-03 10:56:12',
					'JoinAsJoinC' => [
						'id' => 1,
						'join_a_id' => 1,
						'join_c_id' => 2,
						'other' => 'New data for Join A 1 Join C 2',
						'created' => static::date(),
						'updated' => static::date()
		]]]];

		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAll method
 *
 * @return void
 */
	public function testSaveAll() {
		$this->loadFixtures('Post', 'Author', 'Comment', 'Attachment', 'Article', 'User');
		$TestModel = new Post();

		$result = $TestModel->find('all');
		$this->assertEquals(3, count($result));
		$this->assertFalse(isset($result[3]));

		$TestModel->saveAll([
			'Post' => [
				'title' => 'Post with Author',
				'body' => 'This post will be saved with an author'
			],
			'Author' => [
				'user' => 'bob',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf90'
		]]);

		$result = $TestModel->find('all');
		$expected = [
			'Post' => [
				'id' => '4',
				'author_id' => '5',
				'title' => 'Post with Author',
				'body' => 'This post will be saved with an author',
				'published' => 'N'
			],
			'Author' => [
				'id' => '5',
				'user' => 'bob',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf90',
				'test' => 'working'
		]];
		$this->assertEquals(static::date(), $result[3]['Post']['created']);
		$this->assertEquals(static::date(), $result[3]['Post']['updated']);
		$this->assertEquals(static::date(), $result[3]['Author']['created']);
		$this->assertEquals(static::date(), $result[3]['Author']['updated']);
		unset($result[3]['Post']['created'], $result[3]['Post']['updated']);
		unset($result[3]['Author']['created'], $result[3]['Author']['updated']);
		$this->assertEquals($expected, $result[3]);
		$this->assertEquals(4, count($result));

		$TestModel->deleteAll(true);
		$this->assertEquals([], $TestModel->find('all'));

		// SQLite seems to reset the PK counter when that happens, so we need this to make the tests pass
		$this->db->truncate($TestModel);

		$TestModel->saveAll([
			[
				'title' => 'Multi-record post 1',
				'body' => 'First multi-record post',
				'author_id' => 2
			],
			[
				'title' => 'Multi-record post 2',
				'body' => 'Second multi-record post',
				'author_id' => 2
		]]);

		$result = $TestModel->find('all', [
			'recursive' => -1,
			'order' => 'Post.id ASC'
		]);
		$expected = [
			[
				'Post' => [
					'id' => '1',
					'author_id' => '2',
					'title' => 'Multi-record post 1',
					'body' => 'First multi-record post',
					'published' => 'N'
			]],
			[
				'Post' => [
					'id' => '2',
					'author_id' => '2',
					'title' => 'Multi-record post 2',
					'body' => 'Second multi-record post',
					'published' => 'N'
		]]];
		$this->assertEquals(static::date(), $result[0]['Post']['created']);
		$this->assertEquals(static::date(), $result[0]['Post']['updated']);
		$this->assertEquals(static::date(), $result[1]['Post']['created']);
		$this->assertEquals(static::date(), $result[1]['Post']['updated']);
		unset($result[0]['Post']['created'], $result[0]['Post']['updated']);
		unset($result[1]['Post']['created'], $result[1]['Post']['updated']);
		$this->assertEquals($expected, $result);

		$TestModel = new Comment();
		$result = $TestModel->saveAll([
			'Comment' => [
				'article_id' => 2,
				'user_id' => 2,
				'comment' => 'New comment with attachment',
				'published' => 'Y'
			],
			'Attachment' => [
				'attachment' => 'some_file.tgz'
			]]);
		$this->assertFalse(empty($result));

		$result = $TestModel->find('all');
		$expected = [
			'id' => '7',
			'article_id' => '2',
			'user_id' => '2',
			'comment' => 'New comment with attachment',
			'published' => 'Y'
		];
		$this->assertEquals(static::date(), $result[6]['Comment']['created']);
		$this->assertEquals(static::date(), $result[6]['Comment']['updated']);
		unset($result[6]['Comment']['created'], $result[6]['Comment']['updated']);
		$this->assertEquals($expected, $result[6]['Comment']);

		$expected = [
			'id' => '2',
			'comment_id' => '7',
			'attachment' => 'some_file.tgz'
		];
		$this->assertEquals(static::date(), $result[6]['Attachment']['created']);
		$this->assertEquals(static::date(), $result[6]['Attachment']['updated']);
		unset($result[6]['Attachment']['created'], $result[6]['Attachment']['updated']);
		$this->assertEquals($expected, $result[6]['Attachment']);
	}

/**
 * Test SaveAll with Habtm relations
 *
 * @return void
 */
	public function testSaveAllHabtm() {
		$this->loadFixtures('Article', 'Tag', 'Comment', 'User', 'ArticlesTag');
		$data = [
			'Article' => [
				'user_id' => 1,
				'title' => 'Article Has and belongs to Many Tags'
			],
			'Tag' => [
				'Tag' => [1, 2]
			],
			'Comment' => [
				[
					'comment' => 'Article comment',
					'user_id' => 1
		]]];
		$Article = new Article();
		$result = $Article->saveAll($data);
		$this->assertFalse(empty($result));

		$result = $Article->read();
		$this->assertEquals(2, count($result['Tag']));
		$this->assertEquals('tag1', $result['Tag'][0]['tag']);
		$this->assertEquals(1, count($result['Comment']));
		$this->assertEquals(1, count($result['Comment'][0]['comment']));
	}

/**
 * Test SaveAll with Habtm relations and extra join table fields
 *
 * @return void
 */
	public function testSaveAllHabtmWithExtraJoinTableFields() {
		$this->loadFixtures('Something', 'SomethingElse', 'JoinThing');

		$data = [
			'Something' => [
				'id' => 4,
				'title' => 'Extra Fields',
				'body' => 'Extra Fields Body',
				'published' => '1'
			],
			'SomethingElse' => [
				['something_else_id' => 1, 'doomed' => '1'],
				['something_else_id' => 2, 'doomed' => '0'],
				['something_else_id' => 3, 'doomed' => '1']
			]
		];

		$Something = new Something();
		$result = $Something->saveAll($data);
		$this->assertFalse(empty($result));
		$result = $Something->read();

		$this->assertEquals(3, count($result['SomethingElse']));
		$this->assertTrue(Set::matches('/Something[id=4]', $result));

		$this->assertTrue(Set::matches('/SomethingElse[id=1]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=1]/JoinThing[something_else_id=1]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=1]/JoinThing[doomed=1]', $result));

		$this->assertTrue(Set::matches('/SomethingElse[id=2]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=2]/JoinThing[something_else_id=2]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=2]/JoinThing[doomed=0]', $result));

		$this->assertTrue(Set::matches('/SomethingElse[id=3]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=3]/JoinThing[something_else_id=3]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=3]/JoinThing[doomed=1]', $result));
	}

/**
 * testSaveAllHasOne method
 *
 * @return void
 */
	public function testSaveAllHasOne() {
		$model = new Comment();
		$model->deleteAll(true);
		$this->assertEquals([], $model->find('all'));

		$model->Attachment->deleteAll(true);
		$this->assertEquals([], $model->Attachment->find('all'));

		$this->assertTrue($model->saveAll([
			'Comment' => [
				'comment' => 'Comment with attachment',
				'article_id' => 1,
				'user_id' => 1
			],
			'Attachment' => [
				'attachment' => 'some_file.zip'
		]]));
		$result = $model->find('all', ['fields' => [
			'Comment.id', 'Comment.comment', 'Attachment.id',
			'Attachment.comment_id', 'Attachment.attachment'
		]]);
		$expected = [[
			'Comment' => [
				'id' => '1',
				'comment' => 'Comment with attachment'
			],
			'Attachment' => [
				'id' => '1',
				'comment_id' => '1',
				'attachment' => 'some_file.zip'
		]]];
		$this->assertEquals($expected, $result);

		$model->Attachment->bindModel(['belongsTo' => ['Comment']], false);
		$data = [
			'Comment' => [
				'comment' => 'Comment with attachment',
				'article_id' => 1,
				'user_id' => 1
			],
			'Attachment' => [
				'attachment' => 'some_file.zip'
		]];
		$this->assertTrue($model->saveAll($data, ['validate' => 'first']));
	}

/**
 * testSaveAllBelongsTo method
 *
 * @return void
 */
	public function testSaveAllBelongsTo() {
		$model = new Comment();
		$model->deleteAll(true);
		$this->assertEquals([], $model->find('all'));

		$model->Article->deleteAll(true);
		$this->assertEquals([], $model->Article->find('all'));

		$this->assertTrue($model->saveAll([
			'Comment' => [
				'comment' => 'Article comment',
				'article_id' => 1,
				'user_id' => 1
			],
			'Article' => [
				'title' => 'Model Associations 101',
				'user_id' => 1
		]]));
		$result = $model->find('all', ['fields' => [
			'Comment.id', 'Comment.comment', 'Comment.article_id', 'Article.id', 'Article.title'
		]]);
		$expected = [[
			'Comment' => [
				'id' => '1',
				'article_id' => '1',
				'comment' => 'Article comment'
			],
			'Article' => [
				'id' => '1',
				'title' => 'Model Associations 101'
		]]];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllHasOneValidation method
 *
 * @return void
 */
	public function testSaveAllHasOneValidation() {
		$model = new Comment();
		$model->deleteAll(true);
		$this->assertEquals([], $model->find('all'));

		$model->Attachment->deleteAll(true);
		$this->assertEquals([], $model->Attachment->find('all'));

		$model->validate = ['comment' => 'notBlank'];
		$model->Attachment->validate = ['attachment' => 'notBlank'];
		$model->Attachment->bindModel(['belongsTo' => ['Comment']]);

		$result = $model->saveAll(
			[
				'Comment' => [
					'comment' => '',
					'article_id' => 1,
					'user_id' => 1
				],
				'Attachment' => ['attachment' => '']
			],
			['validate' => 'first']
		);
		$this->assertEquals(false, $result);
		$expected = [
			'comment' => ['This field cannot be left blank'],
			'Attachment' => [
				'attachment' => ['This field cannot be left blank']
			]
		];
		$this->assertEquals($expected, $model->validationErrors);
		$this->assertEquals($expected['Attachment'], $model->Attachment->validationErrors);
	}

/**
 * testSaveAllAtomic method
 *
 * @return void
 */
	public function testSaveAllAtomic() {
		$this->loadFixtures('Article', 'User', 'Comment');
		$TestModel = new Article();

		$result = $TestModel->saveAll([
			'Article' => [
				'title' => 'Post with Author',
				'body' => 'This post will be saved with an author',
				'user_id' => 2
			],
			'Comment' => [
				['comment' => 'First new comment', 'user_id' => 2]]
		], ['atomic' => false]);

		$this->assertSame($result, ['Article' => true, 'Comment' => [true]]);

		$result = $TestModel->saveAll([
			[
				'id' => '1',
				'title' => 'Baleeted First Post',
				'body' => 'Baleeted!',
				'published' => 'N'
			],
			[
				'id' => '2',
				'title' => 'Just update the title'
			],
			[
				'title' => 'Creating a fourth post',
				'body' => 'Fourth post body',
				'user_id' => 2
			]
		], ['atomic' => false]);
		$this->assertSame($result, [true, true, true]);

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				[
					'comment' => 'First new comment',
					'published' => 'Y',
					'user_id' => 1
				],
				[
					'comment' => 'Second new comment',
					'published' => 'Y',
					'user_id' => 2
			]]
		], ['validate' => true, 'atomic' => false]);
		$this->assertSame($result, ['Article' => true, 'Comment' => [true, true]]);

		$TestModel->validate = [
			'title' => 'notBlank',
			'author_id' => 'numeric'
		];
		$result = $TestModel->saveAll([
			[
				'id' => '1',
				'title' => 'Un-Baleeted First Post',
				'body' => 'Not Baleeted!',
				'published' => 'Y'
			],
			[
				'id' => '2',
				'title' => '',
				'body' => 'Trying to get away with an empty title'
			]
		], ['validate' => true, 'atomic' => false]);
		$this->assertSame([true, false], $result);
	}

/**
 * testSaveAllDeepAssociated method
 *
 * @return void
 */
	public function testSaveAllDeepAssociated() {
		$this->loadFixtures('Article', 'Comment', 'User', 'Attachment');
		$TestModel = new Article();
		$TestModel->hasMany['Comment']['order'] = ['Comment.created' => 'ASC'];
		$TestModel->hasAndBelongsToMany = [];

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => 'newuser', 'password' => 'newuserpass']],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		], ['deep' => true]);
		$this->assertTrue($result);

		$result = $TestModel->findById(2);
		$expected = [
			'First Comment for Second Article',
			'Second Comment for Second Article',
			'First new comment',
			'Second new comment'
		];
		$result = Hash::extract(Hash::sort($result['Comment'], '{n}.id', 'ASC'), '{n}.comment');
		$this->assertEquals($expected, $result);

		$result = $TestModel->Comment->User->field('id', ['user' => 'newuser', 'password' => 'newuserpass']);
		$this->assertEquals(5, $result);
		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => 'deepsaved']]
			]
		], ['deep' => true]);
		$this->assertTrue($result);

		$result = $TestModel->findById(2);
		$expected = [
			'First Comment for Second Article',
			'Second Comment for Second Article',
			'First new comment',
			'Second new comment',
			'Third new comment',
			'Fourth new comment'
		];
		$result = Hash::extract(Hash::sort($result['Comment'], '{n}.id', 'ASC'), '{n}.comment');
		$this->assertEquals($expected, $result);

		$result = $TestModel->Comment->Attachment->field('id', ['attachment' => 'deepsaved']);
		$this->assertEquals(2, $result);
		$data = [
			'Attachment' => [
				'attachment' => 'deepsave insert',
			],
			'Comment' => [
				'comment' => 'First comment deepsave insert',
				'published' => 'Y',
				'user_id' => 5,
				'Article' => [
					'title' => 'First Article deepsave insert',
					'body' => 'First Article Body deepsave insert',
					'User' => [
						'user' => '',
						'password' => 'magic'
					],
				],
			]
		];

		$TestModel->Comment->Attachment->create();
		$result = $TestModel->Comment->Attachment->saveAll($data, ['deep' => true]);
		$this->assertFalse($result);

		$expected = ['User' => ['user' => ['This field cannot be left blank']]];
		$this->assertEquals($expected, $TestModel->validationErrors);

		$data['Comment']['Article']['User']['user'] = 'deepsave';
		$TestModel->Comment->Attachment->create();
		$result = $TestModel->Comment->Attachment->saveAll($data, ['deep' => true]);
		$this->assertTrue($result);

		$result = $TestModel->Comment->Attachment->findById($TestModel->Comment->Attachment->id);
		$expected = [
			'Attachment' => [
				'id' => '3',
				'comment_id' => '11',
				'attachment' => 'deepsave insert',
			],
			'Comment' => [
				'id' => '11',
				'article_id' => '4',
				'user_id' => '5',
				'comment' => 'First comment deepsave insert',
				'published' => 'Y',
			]
		];
		unset($result['Attachment']['created'], $result['Attachment']['updated']);
		$this->assertEquals($expected['Attachment'], $result['Attachment']);

		unset($result['Comment']['created'], $result['Comment']['updated']);
		$this->assertEquals($expected['Comment'], $result['Comment']);

		$result = $TestModel->findById($result['Comment']['article_id']);
		$expected = [
			'Article' => [
				'id' => '4',
				'user_id' => '6',
				'title' => 'First Article deepsave insert',
				'body' => 'First Article Body deepsave insert',
				'published' => 'N',
			],
			'User' => [
				'id' => '6',
				'user' => 'deepsave',
				'password' => 'magic',
			],
			'Comment' => [
				[
					'id' => '11',
					'article_id' => '4',
					'user_id' => '5',
					'comment' => 'First comment deepsave insert',
					'published' => 'Y',
				]
			]
		];
		unset(
			$result['Article']['created'], $result['Article']['updated'],
			$result['User']['created'], $result['User']['updated'],
			$result['Comment'][0]['created'], $result['Comment'][0]['updated']
		);
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllDeepMany
 * tests the validate methods with deeper recursive data
 *
 * @return void
 */
	public function testSaveAllDeepMany() {
		$this->loadFixtures('Article', 'Comment', 'User', 'Attachment');
		$TestModel = new Article();
		$TestModel->hasMany['Comment']['order'] = ['Comment.created' => 'ASC'];
		$TestModel->hasAndBelongsToMany = [];

		$data = [
			[
				'Article' => ['id' => 1],
				'Comment' => [
					['comment' => 'First comment deepsaved article 1', 'published' => 'Y', 'User' => ['user' => 'savemany', 'password' => 'manysaved']],
					['comment' => 'Second comment deepsaved article 1', 'published' => 'Y', 'user_id' => 2]
				]
			],
			[
				'Article' => ['id' => 2],
				'Comment' => [
					['comment' => 'First comment deepsaved article 2', 'published' => 'Y', 'User' => ['user' => 'savemore', 'password' => 'moresaved']],
					['comment' => 'Second comment deepsaved article 2', 'published' => 'Y', 'user_id' => 2]
				]
			]
		];
		$result = $TestModel->saveAll($data, ['deep' => true]);
		$this->assertTrue($result);

		$data = [
			[
				'id' => 1, 'body' => '',
				'Comment' => [
					['comment' => '', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'manysaved']],
					['comment' => 'Second comment deepsaved article 1', 'published' => 'Y', 'user_id' => 2]
				]
			],
			[
				'Article' => ['id' => 2],
				'Comment' => [
					['comment' => 'First comment deepsaved article 2', 'published' => 'Y', 'User' => ['user' => 'savemore', 'password' => '']],
					['comment' => '', 'published' => 'Y', 'user_id' => 2]
				]
			]
		];
		$TestModel->Comment->validate['comment'] = 'notBlank';
		$result = $TestModel->saveAll($data, ['deep' => true]);
		$this->assertFalse($result);

		$expected = [
			0 => [
				'body' => ['This field cannot be left blank'],
				'Comment' => [
					0 => [
						'comment' => ['This field cannot be left blank'],
						'User' => [
							'user' => ['This field cannot be left blank']
						]
					]
				]
			],
			1 => [
				'Comment' => [
					0 => [
						'User' => [
							'password' => ['This field cannot be left blank']
						]
					],
					1 => [
						'comment' => ['This field cannot be left blank']
					]
				]
			]
		];
		$result = $TestModel->validationErrors;
		$this->assertSame($expected, $result);
	}
/**
 * testSaveAllDeepValidateOnly
 * tests the validate methods with deeper recursive data
 *
 * @return void
 */
	public function testSaveAllDeepValidateOnly() {
		$this->loadFixtures('Article', 'Comment', 'User', 'Attachment');
		$TestModel = new Article();
		$TestModel->hasMany['Comment']['order'] = ['Comment.created' => 'ASC'];
		$TestModel->hasAndBelongsToMany = [];
		$TestModel->Comment->Attachment->validate['attachment'] = 'notBlank';
		$TestModel->Comment->validate['comment'] = 'notBlank';

		$result = $TestModel->saveAll(
			[
				'Article' => ['id' => 2],
				'Comment' => [
					['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => 'newuser', 'password' => 'newuserpass']],
					['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
				]
			],
			['validate' => 'only', 'deep' => true]
		);
		$this->assertTrue($result);

		$result = $TestModel->saveAll(
			[
				'Article' => ['id' => 2],
				'Comment' => [
					['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'newuserpass']],
					['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
				]
			],
			['validate' => 'only', 'deep' => true]
		);
		$this->assertFalse($result);

		$result = $TestModel->saveAll(
			[
				'Article' => ['id' => 2],
				'Comment' => [
					['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => 'newuser', 'password' => 'newuserpass']],
					['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
				]
			],
			['validate' => 'only', 'atomic' => false, 'deep' => true]
		);
		$expected = [
			'Article' => true,
			'Comment' => [
				true,
				true
			]
		];
		$this->assertSame($expected, $result);

		$result = $TestModel->saveAll(
			[
				'Article' => ['id' => 2],
				'Comment' => [
					['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'newuserpass']],
					['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
				]
			],
			['validate' => 'only', 'atomic' => false, 'deep' => true]
		);
		$expected = [
			'Article' => true,
			'Comment' => [
				false,
				true
			]
		];
		$this->assertSame($expected, $result);

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => 'deepsaved']]
			]
		],
		['validate' => 'only', 'deep' => true]
		);
		$this->assertTrue($result);

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => '']]
			]
		],
		['validate' => 'only', 'deep' => true]
		);
		$this->assertFalse($result);

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => 'deepsave']]
			]
		],
		['validate' => 'only', 'atomic' => false, 'deep' => true]
		);
		$expected = [
			'Article' => true,
			'Comment' => [
				true,
				true
			]
		];
		$this->assertSame($expected, $result);

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => '']]
			]
		],
		['validate' => 'only', 'atomic' => false, 'deep' => true]
		);
		$expected = [
			'Article' => true,
			'Comment' => [
				true,
				false
			]
		];
		$this->assertSame($expected, $result);

		$expected = [
			'Comment' => [
				1 => [
					'Attachment' => [
						'attachment' => ['This field cannot be left blank']
					]
				]
			]
		];
		$result = $TestModel->validationErrors;
		$this->assertSame($expected, $result);

		$data = [
			'Attachment' => [
				'attachment' => 'deepsave insert',
			],
			'Comment' => [
				'comment' => 'First comment deepsave insert',
				'published' => 'Y',
				'user_id' => 5,
				'Article' => [
					'title' => 'First Article deepsave insert',
					'body' => 'First Article Body deepsave insert',
					'User' => [
						'user' => 'deepsave',
						'password' => 'magic'
					],
				],
			]
		];

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertTrue($result);

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$expected = [
			'Attachment' => true,
			'Comment' => true
		];
		$this->assertSame($expected, $result);

		$data = [
			'Attachment' => [
				'attachment' => 'deepsave insert',
			],
			'Comment' => [
				'comment' => 'First comment deepsave insert',
				'published' => 'Y',
				'user_id' => 5,
				'Article' => [
					'title' => 'First Article deepsave insert',
					'body' => 'First Article Body deepsave insert',
					'User' => [
						'user' => '',
						'password' => 'magic'
					],
				],
			]
		];

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertFalse($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [
			'Comment' => [
				'Article' => [
					'User' => [
						'user' => ['This field cannot be left blank']
					]
				]
			]
		];
		$this->assertSame($expected, $result);

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$expected = [
			'Attachment' => true,
			'Comment' => false
		];
		$this->assertEquals($expected, $result);

		$data['Comment']['Article']['body'] = '';
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertFalse($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [
			'Comment' => [
				'Article' => [
					'body' => ['This field cannot be left blank'],
					'User' => [
						'user' => ['This field cannot be left blank']
					]
				]
			]
		];
		$this->assertSame($expected, $result);

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$expected = [
			'Attachment' => true,
			'Comment' => false
		];
		$this->assertEquals($expected, $result);

		$data['Comment']['comment'] = '';
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertFalse($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [
			'Comment' => [
				'comment' => ['This field cannot be left blank'],
				'Article' => [
					'body' => ['This field cannot be left blank'],
					'User' => [
						'user' => ['This field cannot be left blank']
					]
				]
			]
		];
		$this->assertSame($expected, $result);

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$expected = [
			'Attachment' => true,
			'Comment' => false
		];
		$this->assertEquals($expected, $result);

		$data['Attachment']['attachment'] = '';
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => true]);
		$this->assertFalse($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [
			'attachment' => ['This field cannot be left blank'],
			'Comment' => [
				'comment' => ['This field cannot be left blank'],
				'Article' => [
					'body' => ['This field cannot be left blank'],
					'User' => [
						'user' => ['This field cannot be left blank']
					]
				]
			]
		];
		$this->assertSame($expected, $result);

		$result = $TestModel->Comment->validationErrors;
		$expected = [
			'comment' => ['This field cannot be left blank'],
			'Article' => [
				'body' => ['This field cannot be left blank'],
				'User' => [
					'user' => ['This field cannot be left blank']
				]
			]
		];
		$this->assertSame($expected, $result);

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => true]);
		$expected = [
			'Attachment' => false,
			'Comment' => false
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllNotDeepAssociated method
 * test that only directly associated data gets saved
 *
 * @return void
 */
	public function testSaveAllNotDeepAssociated() {
		$this->loadFixtures('Article', 'Comment', 'User', 'Attachment');
		$TestModel = new Article();
		$TestModel->hasMany['Comment']['order'] = ['Comment.created' => 'ASC'];
		$TestModel->hasAndBelongsToMany = [];

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				[
					'comment' => 'First new comment', 'published' => 'Y', 'user_id' => 2,
					'User' => ['user' => 'newuser', 'password' => 'newuserpass']
				],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		], ['deep' => false]);
		$this->assertTrue($result);

		$result = $TestModel->Comment->User->field('id', ['user' => 'newuser', 'password' => 'newuserpass']);
		$this->assertFalse($result);

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 4],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => 'deepsaved']]
			]
		], ['deep' => false]);
		$this->assertTrue($result);

		$result = $TestModel->Comment->Attachment->field('id', ['attachment' => 'deepsaved']);
		$this->assertFalse($result);

		$data = [
			'Attachment' => [
				'attachment' => 'deepsave insert',
			],
			'Comment' => [
				'comment' => 'First comment deepsave insert',
				'published' => 'Y',
				'user_id' => 4,
				'article_id' => 1,
				'Article' => [
					'title' => 'First Article deepsave insert',
					'body' => 'First Article Body deepsave insert',
					'User' => [
						'user' => 'deepsave',
						'password' => 'magic'
					],
				],
			]
		];
		$expected = $TestModel->User->find('count');

		$TestModel->Comment->Attachment->create();
		$result = $TestModel->Comment->Attachment->saveAll($data, ['deep' => false]);
		$this->assertTrue($result);

		$result = $TestModel->User->find('count');
		$this->assertEquals($expected, $result);

		$result = $TestModel->Comment->Attachment->findById($TestModel->Comment->Attachment->id);
		$expected = [
			'Attachment' => [
				'id' => '2',
				'comment_id' => '11',
				'attachment' => 'deepsave insert',
			],
			'Comment' => [
				'id' => '11',
				'article_id' => 1,
				'user_id' => '4',
				'comment' => 'First comment deepsave insert',
				'published' => 'Y',
			]
		];
		unset($result['Attachment']['created'], $result['Attachment']['updated']);
		$this->assertEquals($expected['Attachment'], $result['Attachment']);

		unset($result['Comment']['created'], $result['Comment']['updated']);
		$this->assertEquals($expected['Comment'], $result['Comment']);
	}

/**
 * testSaveAllNotDeepMany
 * tests the save methods to not save deeper recursive data
 *
 * @return void
 */
	public function testSaveAllNotDeepMany() {
		$this->loadFixtures('Article', 'Comment', 'User', 'Attachment');
		$TestModel = new Article();
		$TestModel->hasMany['Comment']['order'] = ['Comment.created' => 'ASC'];
		$TestModel->hasAndBelongsToMany = [];

		$data = [
			[
				'id' => 1,
				'body' => '',
				'Comment' => [
					['comment' => '', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'manysaved']],
					['comment' => 'Second comment deepsaved article 1', 'published' => 'Y', 'user_id' => 2]
				]
			],
			[
				'Article' => ['id' => 2],
				'Comment' => [
					['comment' => 'First comment deepsaved article 2', 'published' => 'Y', 'User' => ['user' => 'savemore', 'password' => '']],
					['comment' => '', 'published' => 'Y', 'user_id' => 2]
				]
			]
		];
		$TestModel->Comment->validate['comment'] = 'notBlank';
		$result = $TestModel->saveAll($data, ['deep' => false]);
		$this->assertFalse($result);

		$expected = [
			0 => [
				'body' => ['This field cannot be left blank']
			]
		];
		$result = $TestModel->validationErrors;
		$this->assertSame($expected, $result);

		$data = [
			[
				'Article' => ['id' => 1, 'body' => 'Ignore invalid comment'],
				'Comment' => [
					['comment' => '', 'published' => 'Y', 'user_id' => 2]
				]
			],
			[
				'Article' => ['id' => 2],
				'Comment' => [
					['comment' => '', 'published' => 'Y', 'user_id' => 2]
				]
			]
		];
		$result = $TestModel->saveAll($data, ['deep' => false]);
		$this->assertTrue($result);
	}
/**
 * testSaveAllNotDeepValidateOnly
 * tests the validate methods to not validate deeper recursive data
 *
 * @return void
 */
	public function testSaveAllNotDeepValidateOnly() {
		$this->loadFixtures('Article', 'Comment', 'User', 'Attachment');
		$TestModel = new Article();
		$TestModel->hasMany['Comment']['order'] = ['Comment.created' => 'ASC'];
		$TestModel->hasAndBelongsToMany = [];
		$TestModel->Comment->Attachment->validate['attachment'] = 'notBlank';
		$TestModel->Comment->validate['comment'] = 'notBlank';

		$result = $TestModel->saveAll(
			[
				'Article' => ['id' => 2, 'body' => ''],
				'Comment' => [
					['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'newuserpass']],
					['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
				]
			],
			['validate' => 'only', 'deep' => false]
		);
		$this->assertFalse($result);

		$expected = ['body' => ['This field cannot be left blank']];
		$result = $TestModel->validationErrors;
		$this->assertSame($expected, $result);

		$result = $TestModel->saveAll(
			[
				'Article' => ['id' => 2, 'body' => 'Ignore invalid user data'],
				'Comment' => [
					['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'newuserpass']],
					['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
				]
			],
			['validate' => 'only', 'deep' => false]
		);
		$this->assertTrue($result);

		$result = $TestModel->saveAll(
			[
				'Article' => ['id' => 2, 'body' => 'Ignore invalid user data'],
				'Comment' => [
					['comment' => 'First new comment', 'published' => 'Y', 'User' => ['user' => '', 'password' => 'newuserpass']],
					['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
				]
			],
			['validate' => 'only', 'atomic' => false, 'deep' => false]
		);
		$expected = [
			'Article' => true,
			'Comment' => [
				true,
				true
			]
		];
		$this->assertSame($expected, $result);

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2, 'body' => 'Ignore invalid attachment data'],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => '']]
			]
		],
		['validate' => 'only', 'deep' => false]
		);
		$this->assertTrue($result);

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2, 'body' => 'Ignore invalid attachment data'],
			'Comment' => [
				['comment' => 'Third new comment', 'published' => 'Y', 'user_id' => 5],
				['comment' => 'Fourth new comment', 'published' => 'Y', 'user_id' => 2, 'Attachment' => ['attachment' => '']]
			]
		],
		['validate' => 'only', 'atomic' => false, 'deep' => false]
		);
		$expected = [
			'Article' => true,
			'Comment' => [
				true,
				true
			]
		];
		$this->assertSame($expected, $result);

		$expected = [];
		$result = $TestModel->validationErrors;
		$this->assertSame($expected, $result);

		$data = [
			'Attachment' => [
				'attachment' => 'deepsave insert',
			],
			'Comment' => [
				'comment' => 'First comment deepsave insert',
				'published' => 'Y',
				'user_id' => 5,
				'Article' => [
					'title' => 'First Article deepsave insert ignored',
					'body' => 'First Article Body deepsave insert',
					'User' => [
						'user' => '',
						'password' => 'magic'
					],
				],
			]
		];

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => false]);
		$this->assertTrue($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [];
		$this->assertSame($expected, $result);

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => false]);
		$expected = [
			'Attachment' => true,
			'Comment' => true
		];
		$this->assertEquals($expected, $result);

		$data['Comment']['Article']['body'] = '';
		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'deep' => false]);
		$this->assertTrue($result);

		$result = $TestModel->Comment->Attachment->validationErrors;
		$expected = [];
		$this->assertSame($expected, $result);

		$result = $TestModel->Comment->Attachment->saveAll($data, ['validate' => 'only', 'atomic' => false, 'deep' => false]);
		$expected = [
			'Attachment' => true,
			'Comment' => true
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllHasMany method
 *
 * @return void
 */
	public function testSaveAllHasMany() {
		$this->loadFixtures('Article', 'Comment');
		$TestModel = new Article();
		$TestModel->hasMany['Comment']['order'] = ['Comment.created' => 'ASC'];
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = [];

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'user_id' => 1],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		]);
		$this->assertFalse(empty($result));

		$result = $TestModel->findById(2);
		$expected = [
			'First Comment for Second Article',
			'Second Comment for Second Article',
			'First new comment',
			'Second new comment'
		];
		$result = Hash::extract(Hash::sort($result['Comment'], '{n}.id', 'ASC'), '{n}.comment');
		$this->assertEquals($expected, $result);

		$result = $TestModel->saveAll(
			[
				'Article' => ['id' => 2],
				'Comment' => [
					[
						'comment' => 'Third new comment',
						'published' => 'Y',
						'user_id' => 1
			]]],
			['atomic' => false]
		);
		$this->assertFalse(empty($result));

		$result = $TestModel->findById(2);
		$expected = [
			'First Comment for Second Article',
			'Second Comment for Second Article',
			'First new comment',
			'Second new comment',
			'Third new comment'
		];
		$result = Hash::extract(Hash::sort($result['Comment'], '{n}.id', 'ASC'), '{n}.comment');
		$this->assertEquals($expected, $result);

		$TestModel->beforeSaveReturn = false;
		$result = $TestModel->saveAll(
			[
				'Article' => ['id' => 2],
				'Comment' => [
					[
						'comment' => 'Fourth new comment',
						'published' => 'Y',
						'user_id' => 1
			]]],
			['atomic' => false]
		);
		$this->assertEquals(['Article' => false], $result);

		$result = $TestModel->findById(2);
		$expected = [
			'First Comment for Second Article',
			'Second Comment for Second Article',
			'First new comment',
			'Second new comment',
			'Third new comment'
		];
		$result = Hash::extract(Hash::sort($result['Comment'], '{n}.id', 'ASC'), '{n}.comment');
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllHasManyValidation method
 *
 * @return void
 */
	public function testSaveAllHasManyValidation() {
		$this->loadFixtures('Article', 'Comment');
		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = [];
		$TestModel->Comment->validate = ['comment' => 'notBlank'];

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => '', 'published' => 'Y', 'user_id' => 1],
			]
		], ['validate' => true]);
		$this->assertFalse($result);

		$expected = ['Comment' => [
			['comment' => ['This field cannot be left blank']]
		]];
		$this->assertEquals($expected, $TestModel->validationErrors);
		$expected = [
			['comment' => ['This field cannot be left blank']]
		];
		$this->assertEquals($expected, $TestModel->Comment->validationErrors);

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2],
			'Comment' => [
				[
					'comment' => '',
					'published' => 'Y',
					'user_id' => 1
			]]
		], ['validate' => 'first']);
		$this->assertFalse($result);
	}

/**
 * test saveAll with transactions and ensure there is no missing rollback.
 *
 * @return void
 */
	public function testSaveAllManyRowsTransactionNoRollback() {
		$this->loadFixtures('Post');

		$Post = new TestPost();
		$Post->validate = [
			'title' => ['rule' => ['notBlank']]
		];

		// If validation error occurs, rollback() should be called.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->never())->method('commit');
		$db->expects($this->once())->method('rollback');

		$Post->setDataSourceObject($db);

		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => '']
		];
		$Post->saveAll($data, ['atomic' => true, 'validate' => true]);

		// If exception thrown, rollback() should be called too.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->never())->method('commit');
		$db->expects($this->once())->method('rollback');

		$Post->setDataSourceObject($db);

		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => 'New Fifth Post', 'body' => $db->expression('PDO_EXCEPTION()')]
		];

		try {
			$Post->saveAll($data, ['atomic' => true, 'validate' => true]);
			$this->fail('No exception thrown');
		} catch (PDOException $e) {
		}

		// Otherwise, commit() should be called.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->once())->method('commit');
		$db->expects($this->never())->method('rollback');

		$Post->setDataSourceObject($db);

		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => 'New Fifth Post']
		];
		$Post->saveAll($data, ['atomic' => true, 'validate' => true]);
	}

/**
 * test saveAll with transactions and ensure there is no missing rollback.
 *
 * @return void
 */
	public function testSaveAllAssociatedTransactionNoRollback() {
		$this->loadFixtures('Post', 'Author');

		$Post = new TestPost();
		$Post->Author->validate = [
			'user' => ['rule' => ['notBlank']]
		];

		// If validation error occurs, rollback() should be called.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->never())->method('commit');
		$db->expects($this->once())->method('rollback');

		$Post->setDataSourceObject($db);
		$Post->Author->setDataSourceObject($db);

		$data = [
			'Post' => [
				'title' => 'New post',
				'body' => 'Content',
				'published' => 'Y'
			],
			'Author' => [
				'user' => '',
				'password' => "sekret"
			]
		];
		$Post->saveAll($data, ['validate' => true]);

		// If exception thrown, rollback() should be called too.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->never())->method('commit');
		$db->expects($this->once())->method('rollback');

		$Post->setDataSourceObject($db);
		$Post->Author->setDataSourceObject($db);

		$data = [
			'Post' => [
				'title' => 'New post',
				'body' => $db->expression('PDO_EXCEPTION()'),
				'published' => 'Y'
			],
			'Author' => [
				'user' => 'New user',
				'password' => "sekret"
			]
		];

		try {
			$Post->saveAll($data, ['validate' => true]);
			$this->fail('No exception thrown');
		} catch (PDOException $e) {
		}

		// Otherwise, commit() should be called.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->once())->method('commit');
		$db->expects($this->never())->method('rollback');

		$Post->setDataSourceObject($db);
		$Post->Author->setDataSourceObject($db);

		$data = [
			'Post' => [
				'title' => 'New post',
				'body' => 'Content',
				'published' => 'Y'
			],
			'Author' => [
				'user' => 'New user',
				'password' => "sekret"
			]
		];
		$Post->saveAll($data, ['validate' => true]);
	}

/**
 * test saveAll with nested saveAll call.
 *
 * @return void
 */
	public function testSaveAllNestedSaveAll() {
		$this->loadFixtures('Sample');
		$TransactionTestModel = new TransactionTestModel();

		$data = [
			['apple_id' => 1, 'name' => 'sample5'],
		];

		$this->assertTrue($TransactionTestModel->saveAll($data, ['atomic' => true]));
	}

/**
 * testSaveAllTransaction method
 *
 * @return void
 */
	public function testSaveAllTransaction() {
		$this->loadFixtures('Post', 'Author', 'Comment', 'Attachment');
		$TestModel = new Post();

		$TestModel->validate = ['title' => 'notBlank'];
		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => 'New Fifth Post'],
			['author_id' => 1, 'title' => '']
		];
		$this->assertFalse($TestModel->saveAll($data));

		$result = $TestModel->find('all', ['recursive' => -1]);
		$expected = [
			['Post' => [
				'id' => '1',
				'author_id' => 1,
				'title' => 'First Post',
				'body' => 'First Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:39:23',
				'updated' => '2007-03-18 10:41:31'
			]],
			['Post' => [
				'id' => '2',
				'author_id' => 3,
				'title' => 'Second Post',
				'body' => 'Second Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:41:23',
				'updated' => '2007-03-18 10:43:31'
			]],
			['Post' => [
				'id' => '3',
				'author_id' => 1,
				'title' => 'Third Post',
				'body' => 'Third Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:43:23',
				'updated' => '2007-03-18 10:45:31'
		]]];

		if (count($result) != 3) {
			// Database doesn't support transactions
			$expected[] = [
				'Post' => [
					'id' => '4',
					'author_id' => 1,
					'title' => 'New Fourth Post',
					'body' => null,
					'published' => 'N',
					'created' => static::date(),
					'updated' => static::date()
			]];

			$expected[] = [
				'Post' => [
					'id' => '5',
					'author_id' => 1,
					'title' => 'New Fifth Post',
					'body' => null,
					'published' => 'N',
					'created' => static::date(),
					'updated' => static::date()
			]];

			$this->assertEquals($expected, $result);
			// Skip the rest of the transactional tests
			return;
		}

		$this->assertEquals($expected, $result);

		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => ''],
			['author_id' => 1, 'title' => 'New Sixth Post']
		];
		$this->assertFalse($TestModel->saveAll($data));

		$result = $TestModel->find('all', ['recursive' => -1]);
		$expected = [
			['Post' => [
				'id' => '1',
				'author_id' => 1,
				'title' => 'First Post',
				'body' => 'First Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:39:23',
				'updated' => '2007-03-18 10:41:31'
			]],
			['Post' => [
				'id' => '2',
				'author_id' => 3,
				'title' => 'Second Post',
				'body' => 'Second Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:41:23',
				'updated' => '2007-03-18 10:43:31'
			]],
			['Post' => [
				'id' => '3',
				'author_id' => 1,
				'title' => 'Third Post',
				'body' => 'Third Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:43:23',
				'updated' => '2007-03-18 10:45:31'
		]]];

		if (count($result) != 3) {
			// Database doesn't support transactions
			$expected[] = [
				'Post' => [
					'id' => '4',
					'author_id' => 1,
					'title' => 'New Fourth Post',
					'body' => 'Third Post Body',
					'published' => 'N',
					'created' => static::date(),
					'updated' => static::date()
			]];

			$expected[] = [
				'Post' => [
					'id' => '5',
					'author_id' => 1,
					'title' => 'Third Post',
					'body' => 'Third Post Body',
					'published' => 'N',
					'created' => static::date(),
					'updated' => static::date()
			]];
		}
		$this->assertEquals($expected, $result);

		$TestModel->validate = ['title' => 'notBlank'];
		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => 'New Fifth Post'],
			['author_id' => 1, 'title' => 'New Sixth Post']
		];
		$this->assertTrue($TestModel->saveAll($data));

		$result = $TestModel->find('all', [
			'recursive' => -1,
			'fields' => ['author_id', 'title', 'body', 'published'],
			'order' => ['Post.created' => 'ASC']
		]);

		$expected = [
			['Post' => [
				'author_id' => 1,
				'title' => 'First Post',
				'body' => 'First Post Body',
				'published' => 'Y'
			]],
			['Post' => [
				'author_id' => 3,
				'title' => 'Second Post',
				'body' => 'Second Post Body',
				'published' => 'Y'
			]],
			['Post' => [
				'author_id' => 1,
				'title' => 'Third Post',
				'body' => 'Third Post Body',
				'published' => 'Y'
			]],
			['Post' => [
				'author_id' => 1,
				'title' => 'New Fourth Post',
				'body' => '',
				'published' => 'N'
			]],
			['Post' => [
				'author_id' => 1,
				'title' => 'New Fifth Post',
				'body' => '',
				'published' => 'N'
			]],
			['Post' => [
				'author_id' => 1,
				'title' => 'New Sixth Post',
				'body' => '',
				'published' => 'N'
		]]];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllValidation method
 *
 * @return void
 */
	public function testSaveAllValidation() {
		$this->loadFixtures('Post', 'Author', 'Comment', 'Attachment');
		$TestModel = new Post();

		$data = [
			[
				'id' => '1',
				'title' => 'Baleeted First Post',
				'body' => 'Baleeted!',
				'published' => 'N'
			],
			[
				'id' => '2',
				'title' => 'Just update the title'
			],
			[
				'title' => 'Creating a fourth post',
				'body' => 'Fourth post body',
				'author_id' => 2
		]];

		$this->assertTrue($TestModel->saveAll($data));

		$result = $TestModel->find('all', ['recursive' => -1, 'order' => 'Post.id ASC']);
		$expected = [
			[
				'Post' => [
					'id' => '1',
					'author_id' => '1',
					'title' => 'Baleeted First Post',
					'body' => 'Baleeted!',
					'published' => 'N',
					'created' => '2007-03-18 10:39:23'
			]],
			[
				'Post' => [
					'id' => '2',
					'author_id' => '3',
					'title' => 'Just update the title',
					'body' => 'Second Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23'
			]],
			[
				'Post' => [
					'id' => '3',
					'author_id' => '1',
					'title' => 'Third Post',
					'body' => 'Third Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
			]],
			[
				'Post' => [
					'id' => '4',
					'author_id' => '2',
					'title' => 'Creating a fourth post',
					'body' => 'Fourth post body',
					'published' => 'N'
		]]];
		$this->assertEquals(static::date(), $result[0]['Post']['updated']);
		$this->assertEquals(static::date(), $result[1]['Post']['updated']);
		$this->assertEquals(static::date(), $result[3]['Post']['created']);
		$this->assertEquals(static::date(), $result[3]['Post']['updated']);
		unset($result[0]['Post']['updated'], $result[1]['Post']['updated']);
		unset($result[3]['Post']['created'], $result[3]['Post']['updated']);
		$this->assertEquals($expected, $result);

		$TestModel->validate = ['title' => 'notBlank', 'author_id' => 'numeric'];
		$data = [
			[
				'id' => '1',
				'title' => 'Un-Baleeted First Post',
				'body' => 'Not Baleeted!',
				'published' => 'Y'
			],
			[
				'id' => '2',
				'title' => '',
				'body' => 'Trying to get away with an empty title'
		]];
		$result = $TestModel->saveAll($data);
		$this->assertFalse($result);

		$result = $TestModel->find('all', ['recursive' => -1, 'order' => 'Post.id ASC']);
		$errors = [1 => ['title' => ['This field cannot be left blank']]];
		$transactionWorked = Set::matches('/Post[1][title=Baleeted First Post]', $result);
		if (!$transactionWorked) {
			$this->assertTrue(Set::matches('/Post[1][title=Un-Baleeted First Post]', $result));
			$this->assertTrue(Set::matches('/Post[2][title=Just update the title]', $result));
		}

		$this->assertEquals($errors, $TestModel->validationErrors);

		$TestModel->validate = ['title' => 'notBlank', 'author_id' => 'numeric'];
		$data = [
			[
				'id' => '1',
				'title' => 'Un-Baleeted First Post',
				'body' => 'Not Baleeted!',
				'published' => 'Y'
			],
			[
				'id' => '2',
				'title' => '',
				'body' => 'Trying to get away with an empty title'
		]];
		$result = $TestModel->saveAll($data, ['validate' => true, 'atomic' => false]);
		$this->assertEquals([true, false], $result);
		$result = $TestModel->find('all', ['recursive' => -1, 'order' => 'Post.id ASC']);
		$errors = [1 => ['title' => ['This field cannot be left blank']]];
		$expected = [
			[
				'Post' => [
					'id' => '1',
					'author_id' => '1',
					'title' => 'Un-Baleeted First Post',
					'body' => 'Not Baleeted!',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23'
				]
			],
			[
				'Post' => [
					'id' => '2',
					'author_id' => '3',
					'title' => 'Just update the title',
					'body' => 'Second Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23'
				]
			],
			[
				'Post' => [
					'id' => '3',
					'author_id' => '1',
					'title' => 'Third Post',
					'body' => 'Third Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				]
			],
			[
				'Post' => [
					'id' => '4',
					'author_id' => '2',
					'title' => 'Creating a fourth post',
					'body' => 'Fourth post body',
					'published' => 'N'
				]
			]
		];

		$this->assertEquals(static::date(), $result[0]['Post']['updated']);
		$this->assertEquals(static::date(), $result[1]['Post']['updated']);
		$this->assertEquals(static::date(), $result[3]['Post']['updated']);
		$this->assertEquals(static::date(), $result[3]['Post']['created']);
		unset(
			$result[0]['Post']['updated'], $result[1]['Post']['updated'],
			$result[3]['Post']['updated'], $result[3]['Post']['created']
		);
		$this->assertEquals($expected, $result);
		$this->assertEquals($errors, $TestModel->validationErrors);

		$data = [
			[
				'id' => '1',
				'title' => 'Re-Baleeted First Post',
				'body' => 'Baleeted!',
				'published' => 'N'
			],
			[
				'id' => '2',
				'title' => '',
				'body' => 'Trying to get away with an empty title'
		]];
		$this->assertFalse($TestModel->saveAll($data, ['validate' => 'first']));

		$result = $TestModel->find('all', ['recursive' => -1, 'order' => 'Post.id ASC']);
		unset(
			$result[0]['Post']['updated'], $result[1]['Post']['updated'],
			$result[3]['Post']['updated'], $result[3]['Post']['created']
		);
		$this->assertEquals($expected, $result);
		$this->assertEquals($errors, $TestModel->validationErrors);
	}

/**
 * testSaveAllValidationOnly method
 *
 * @return void
 */
	public function testSaveAllValidationOnly() {
		$this->loadFixtures('Comment', 'Attachment');
		$TestModel = new Comment();
		$TestModel->Attachment->validate = ['attachment' => 'notBlank'];

		$data = [
			'Comment' => [
				'comment' => 'This is the comment'
			],
			'Attachment' => [
				'attachment' => ''
			]
		];

		$result = $TestModel->saveAll($data, ['validate' => 'only']);
		$this->assertFalse($result);

		$TestModel = new Article();
		$TestModel->validate = ['title' => 'notBlank'];
		$result = $TestModel->saveAll(
			[
				0 => ['title' => ''],
				1 => ['title' => 'title 1'],
				2 => ['title' => 'title 2'],
			],
			['validate' => 'only']
		);
		$this->assertFalse($result);
		$expected = [
			0 => ['title' => ['This field cannot be left blank']],
		];
		$this->assertEquals($expected, $TestModel->validationErrors);

		$result = $TestModel->saveAll(
			[
				0 => ['title' => 'title 0'],
				1 => ['title' => ''],
				2 => ['title' => 'title 2'],
			],
			['validate' => 'only']
		);
		$this->assertFalse($result);
		$expected = [
			1 => ['title' => ['This field cannot be left blank']],
		];
		$this->assertEquals($expected, $TestModel->validationErrors);
	}

/**
 * testSaveAllValidateFirst method
 *
 * @return void
 */
	public function testSaveAllValidateFirst() {
		$this->loadFixtures('Article', 'Comment', 'Attachment', 'User', 'ArticlesTag', 'Tag');
		$model = new Article();
		$model->deleteAll(true);

		$model->Comment->validate = ['comment' => 'notBlank'];
		$result = $model->saveAll([
			'Article' => [
				'title' => 'Post with Author',
				'body' => 'This post will be saved author'
			],
			'Comment' => [
				['comment' => 'First new comment'],
				['comment' => '']
			]
		], ['validate' => 'first']);

		$this->assertFalse($result);

		$result = $model->find('all');
		$this->assertSame([], $result);
		$expected = ['Comment' => [
			1 => ['comment' => ['This field cannot be left blank']]
		]];

		$this->assertEquals($expected['Comment'], $model->Comment->validationErrors);

		$this->assertSame($model->Comment->find('count'), 0);

		$result = $model->saveAll(
			[
				'Article' => [
					'title' => 'Post with Author',
					'body' => 'This post will be saved with an author',
					'user_id' => 2
				],
				'Comment' => [
					[
						'comment' => 'Only new comment',
						'user_id' => 2
			]]],
			['validate' => 'first']
		);

		$this->assertTrue($result);

		$result = $model->Comment->find('all');
		$this->assertSame(count($result), 1);
		$result = Hash::extract($result, '{n}.Comment.article_id');
		$this->assertEquals(4, $result[0]);

		$model->deleteAll(true);
		$data = [
			'Article' => [
				'title' => 'Post with Author saveAlled from comment',
				'body' => 'This post will be saved with an author',
				'user_id' => 2
			],
			'Comment' => [
				'comment' => 'Only new comment', 'user_id' => 2
		]];

		$result = $model->Comment->saveAll($data, ['validate' => 'first']);
		$this->assertFalse(empty($result));

		$result = $model->find('all');
		$this->assertEquals(
			$result[0]['Article']['title'],
			'Post with Author saveAlled from comment'
		);
		$this->assertEquals('Only new comment', $result[0]['Comment'][0]['comment']);
	}

/**
 * test saveAll()'s return is correct when using atomic = false and validate = first.
 *
 * @return void
 */
	public function testSaveAllValidateFirstAtomicFalse() {
		$this->loadFixtures('Something');
		$Something = new Something();
		$invalidData = [
			[
				'title' => 'foo',
				'body' => 'bar',
				'published' => 'baz',
			],
			[
				'body' => 3,
				'published' => 'sd',
			],
		];
		$Something->create();
		$Something->validate = [
			'title' => [
				'rule' => 'alphaNumeric',
				'required' => true,
			],
			'body' => [
				'rule' => 'alphaNumeric',
				'required' => true,
				'allowEmpty' => true,
			],
		];
		$result = $Something->saveAll($invalidData, [
			'atomic' => false,
			'validate' => 'first',
		]);
		$expected = [true, false];
		$this->assertEquals($expected, $result);

		$Something = new Something();
		$validData = [
			[
				'title' => 'title value',
				'body' => 'body value',
				'published' => 'baz',
			],
			[
				'title' => 'valid',
				'body' => 'this body',
				'published' => 'sd',
			],
		];
		$Something->create();
		$result = $Something->saveAll($validData, [
			'atomic' => false,
			'validate' => 'first',
		]);
		$expected = [true, true];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllHasManyValidationOnly method
 *
 * @return void
 */
	public function testSaveAllHasManyValidationOnly() {
		$this->loadFixtures('Article', 'Comment', 'Attachment');
		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = [];
		$TestModel->Comment->validate = ['comment' => 'notBlank'];

		$result = $TestModel->saveAll(
			[
				'Article' => ['id' => 2],
				'Comment' => [
					[
						'id' => 1,
						'comment' => '',
						'published' => 'Y',
						'user_id' => 1],
					[
						'id' => 2,
						'comment' =>
						'comment',
						'published' => 'Y',
						'user_id' => 1
			]]],
			['validate' => 'only']
		);
		$this->assertFalse($result);

		$result = $TestModel->saveAll(
			[
				'Article' => ['id' => 2],
				'Comment' => [
					[
						'id' => 1,
						'comment' => '',
						'published' => 'Y',
						'user_id' => 1
					],
					[
						'id' => 2,
						'comment' => 'comment',
						'published' => 'Y',
						'user_id' => 1
					],
					[
						'id' => 3,
						'comment' => '',
						'published' => 'Y',
						'user_id' => 1
			]]],
			[
				'validate' => 'only',
				'atomic' => false
		]);
		$expected = [
			'Article' => true,
			'Comment' => [false, true, false]
		];
		$this->assertSame($expected, $result);

		$expected = ['Comment' => [
			0 => ['comment' => ['This field cannot be left blank']],
			2 => ['comment' => ['This field cannot be left blank']]
		]];
		$this->assertEquals($expected, $TestModel->validationErrors);

		$expected = [
			0 => ['comment' => ['This field cannot be left blank']],
			2 => ['comment' => ['This field cannot be left blank']]
		];
		$this->assertEquals($expected, $TestModel->Comment->validationErrors);
	}

/**
 * test that saveAll still behaves like previous versions (does not necessarily need a first argument)
 *
 * @return void
 */
	public function testSaveAllWithSet() {
		$this->loadFixtures('Article', 'Tag', 'Comment', 'User', 'ArticlesTag');
		$data = [
			'Article' => [
				'user_id' => 1,
				'title' => 'Article Has and belongs to Many Tags'
			],
			'Tag' => [
				'Tag' => [1, 2]
			],
			'Comment' => [
				[
					'comment' => 'Article comment',
					'user_id' => 1
		]]];
		$Article = new Article();
		$Article->set($data);
		$result = $Article->saveAll();
		$this->assertFalse(empty($result));
	}

/**
 * test that saveAll behaves like plain save() when supplied empty data
 *
 * @return void
 */
	public function testSaveAllEmptyData() {
		$this->skipIf($this->db instanceof Sqlserver, 'This test is not compatible with SQL Server.');

		$this->loadFixtures('Article', 'ProductUpdateAll', 'Comment', 'Attachment');
		$model = new Article();
		$result = $model->saveAll([], ['validate' => 'first']);
		$this->assertFalse(empty($result));

		$model = new ProductUpdateAll();
		$result = $model->saveAll();
		$this->assertFalse($result);
	}

/**
 * testSaveAssociated method
 *
 * @return void
 */
	public function testSaveAssociated() {
		$this->loadFixtures('Post', 'Author', 'Comment', 'Attachment', 'Article', 'User');
		$TestModel = new Post();

		$result = $TestModel->find('all');
		$this->assertEquals(3, count($result));
		$this->assertFalse(isset($result[3]));

		$TestModel->saveAssociated([
			'Post' => [
				'title' => 'Post with Author',
				'body' => 'This post will be saved with an author'
			],
			'Author' => [
				'user' => 'bob',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf90'
		]]);

		$result = $TestModel->find('all', ['order' => ['Post.id ' => 'ASC']]);
		$expected = [
			'Post' => [
				'id' => '4',
				'author_id' => '5',
				'title' => 'Post with Author',
				'body' => 'This post will be saved with an author',
				'published' => 'N'
			],
			'Author' => [
				'id' => '5',
				'user' => 'bob',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf90',
				'test' => 'working'
		]];
		$this->assertEquals(static::date(), $result[3]['Post']['updated']);
		$this->assertEquals(static::date(), $result[3]['Post']['created']);
		$this->assertEquals(static::date(), $result[3]['Author']['created']);
		$this->assertEquals(static::date(), $result[3]['Author']['updated']);
		unset(
			$result[3]['Post']['updated'], $result[3]['Post']['created'],
			$result[3]['Author']['updated'], $result[3]['Author']['created']
		);
		$this->assertEquals($expected, $result[3]);
		$this->assertEquals(4, count($result));

		$TestModel = new Comment();
		$result = $TestModel->saveAssociated([
			'Comment' => [
				'article_id' => 2,
				'user_id' => 2,
				'comment' => 'New comment with attachment',
				'published' => 'Y'
			],
			'Attachment' => [
				'attachment' => 'some_file.tgz'
			]]);
		$this->assertFalse(empty($result));

		$result = $TestModel->find('all');
		$expected = [
			'id' => '7',
			'article_id' => '2',
			'user_id' => '2',
			'comment' => 'New comment with attachment',
			'published' => 'Y'
		];
		$this->assertEquals(static::date(), $result[6]['Comment']['updated']);
		$this->assertEquals(static::date(), $result[6]['Comment']['created']);
		unset($result[6]['Comment']['updated'], $result[6]['Comment']['created']);
		$this->assertEquals($expected, $result[6]['Comment']);

		$expected = [
			'id' => '2',
			'comment_id' => '7',
			'attachment' => 'some_file.tgz'
		];
		$this->assertEquals(static::date(), $result[6]['Attachment']['updated']);
		$this->assertEquals(static::date(), $result[6]['Attachment']['created']);
		unset($result[6]['Attachment']['updated'], $result[6]['Attachment']['created']);
		$this->assertEquals($expected, $result[6]['Attachment']);
	}

/**
 * Test that validate = first, atomic = false works when associated records
 * fail validation.
 *
 * @return void
 */
	public function testSaveAssociatedAtomicFalseValidateFirstWithErrors() {
		$this->loadFixtures('Comment', 'Article', 'User');
		$Article = ClassRegistry::init('Article');
		$Article->Comment->validator()->add('comment', [
			['rule' => 'notBlank']
		]);

		$data = [
			'Article' => [
				'user_id' => 1,
				'title' => 'Foo',
				'body' => 'text',
				'published' => 'N'
			],
			'Comment' => [
				[
					'user_id' => 1,
					'comment' => '',
					'published' => 'N',
				]
			],
		];

		$Article->saveAssociated(
			$data,
			['validate' => 'first', 'atomic' => false]
		);

		$result = $Article->validationErrors;
		$expected = [
			'Comment' => [
				[
					'comment' => ['This field cannot be left blank']
				]
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveMany method
 *
 * @return void
 */
	public function testSaveMany() {
		$this->loadFixtures('Post');
		$TestModel = new Post();
		$TestModel->deleteAll(true);
		$this->assertEquals([], $TestModel->find('all'));

		// SQLite seems to reset the PK counter when that happens, so we need this to make the tests pass
		$this->db->truncate($TestModel);

		$TestModel->saveMany([
			[
				'title' => 'Multi-record post 1',
				'body' => 'First multi-record post',
				'author_id' => 2
			],
			[
				'title' => 'Multi-record post 2',
				'body' => 'Second multi-record post',
				'author_id' => 2
		]]);

		$result = $TestModel->find('all', [
			'recursive' => -1,
			'order' => 'Post.id ASC'
		]);
		$expected = [
			[
				'Post' => [
					'id' => '1',
					'author_id' => '2',
					'title' => 'Multi-record post 1',
					'body' => 'First multi-record post',
					'published' => 'N'
				]
			],
			[
				'Post' => [
					'id' => '2',
					'author_id' => '2',
					'title' => 'Multi-record post 2',
					'body' => 'Second multi-record post',
					'published' => 'N'
				]
			]
		];
		$this->assertEquals(static::date(), $result[0]['Post']['updated']);
		$this->assertEquals(static::date(), $result[0]['Post']['created']);
		$this->assertEquals(static::date(), $result[1]['Post']['updated']);
		$this->assertEquals(static::date(), $result[1]['Post']['created']);
		unset($result[0]['Post']['updated'], $result[0]['Post']['created']);
		unset($result[1]['Post']['updated'], $result[1]['Post']['created']);
		$this->assertEquals($expected, $result);
	}

/**
 * Test SaveMany with validate=false.
 *
 * @return void
 */
	public function testSaveManyValidateFalse() {
		$this->loadFixtures('Post');
		$TestModel = new Post();
		$TestModel->deleteAll(true);
		$data = [
			['id' => 1, 'author_id' => 1, 'title' => 'hi'],
			['id' => 2, 'author_id' => 1, 'title' => 'bye']
		];
		$result = $TestModel->saveAll($data, ['validate' => false]);
		$this->assertTrue($result);
	}

/**
 * Test SaveAssociated with Habtm relations
 *
 * @return void
 */
	public function testSaveAssociatedHabtm() {
		$this->loadFixtures('Article', 'Tag', 'Comment', 'User', 'ArticlesTag');
		$data = [
			'Article' => [
				'user_id' => 1,
				'title' => 'Article Has and belongs to Many Tags'
			],
			'Tag' => [
				'Tag' => [1, 2]
			],
			'Comment' => [
				[
					'comment' => 'Article comment',
					'user_id' => 1
		]]];
		$Article = new Article();
		$result = $Article->saveAssociated($data);
		$this->assertFalse(empty($result));

		$result = $Article->read();
		$this->assertEquals(2, count($result['Tag']));
		$this->assertEquals('tag1', $result['Tag'][0]['tag']);
		$this->assertEquals(1, count($result['Comment']));
		$this->assertEquals(1, count($result['Comment'][0]['comment']));
	}

/**
 * Test SaveAssociated with Habtm relations and extra join table fields
 *
 * @return void
 */
	public function testSaveAssociatedHabtmWithExtraJoinTableFields() {
		$this->loadFixtures('Something', 'SomethingElse', 'JoinThing');

		$data = [
			'Something' => [
				'id' => 4,
				'title' => 'Extra Fields',
				'body' => 'Extra Fields Body',
				'published' => '1'
			],
			'SomethingElse' => [
				['something_else_id' => 1, 'doomed' => '1'],
				['something_else_id' => 2, 'doomed' => '0'],
				['something_else_id' => 3, 'doomed' => '1']
			]
		];

		$Something = new Something();
		$result = $Something->saveAssociated($data);
		$this->assertFalse(empty($result));
		$result = $Something->read();

		$this->assertEquals(3, count($result['SomethingElse']));
		$this->assertTrue(Set::matches('/Something[id=4]', $result));

		$this->assertTrue(Set::matches('/SomethingElse[id=1]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=1]/JoinThing[something_else_id=1]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=1]/JoinThing[doomed=1]', $result));

		$this->assertTrue(Set::matches('/SomethingElse[id=2]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=2]/JoinThing[something_else_id=2]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=2]/JoinThing[doomed=0]', $result));

		$this->assertTrue(Set::matches('/SomethingElse[id=3]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=3]/JoinThing[something_else_id=3]', $result));
		$this->assertTrue(Set::matches('/SomethingElse[id=3]/JoinThing[doomed=1]', $result));
	}

/**
 * testSaveAssociatedHasOne method
 *
 * @return void
 */
	public function testSaveAssociatedHasOne() {
		$model = new Comment();
		$model->deleteAll(true);
		$this->assertEquals([], $model->find('all'));

		$model->Attachment->deleteAll(true);
		$this->assertEquals([], $model->Attachment->find('all'));

		$this->assertTrue($model->saveAssociated([
			'Comment' => [
				'comment' => 'Comment with attachment',
				'article_id' => 1,
				'user_id' => 1
			],
			'Attachment' => [
				'attachment' => 'some_file.zip'
		]]));
		$result = $model->find('all', ['fields' => [
			'Comment.id', 'Comment.comment', 'Attachment.id',
			'Attachment.comment_id', 'Attachment.attachment'
		]]);
		$expected = [[
			'Comment' => [
				'id' => '1',
				'comment' => 'Comment with attachment'
			],
			'Attachment' => [
				'id' => '1',
				'comment_id' => '1',
				'attachment' => 'some_file.zip'
		]]];
		$this->assertEquals($expected, $result);

		$model->Attachment->bindModel(['belongsTo' => ['Comment']], false);
		$data = [
			'Comment' => [
				'comment' => 'Comment with attachment',
				'article_id' => 1,
				'user_id' => 1
			],
			'Attachment' => [
				'attachment' => 'some_file.zip'
		]];
		$this->assertTrue($model->saveAssociated($data, ['validate' => 'first']));
	}

/**
 * testSaveAssociatedBelongsTo method
 *
 * @return void
 */
	public function testSaveAssociatedBelongsTo() {
		$model = new Comment();
		$model->deleteAll(true);
		$this->assertEquals([], $model->find('all'));

		$model->Article->deleteAll(true);
		$this->assertEquals([], $model->Article->find('all'));

		$this->assertTrue($model->saveAssociated([
			'Comment' => [
				'comment' => 'Article comment',
				'article_id' => 1,
				'user_id' => 1
			],
			'Article' => [
				'title' => 'Model Associations 101',
				'user_id' => 1
		]]));
		$result = $model->find('all', ['fields' => [
			'Comment.id', 'Comment.comment', 'Comment.article_id', 'Article.id', 'Article.title'
		]]);
		$expected = [[
			'Comment' => [
				'id' => '1',
				'article_id' => '1',
				'comment' => 'Article comment'
			],
			'Article' => [
				'id' => '1',
				'title' => 'Model Associations 101'
		]]];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAssociatedHasOneValidation method
 *
 * @return void
 */
	public function testSaveAssociatedHasOneValidation() {
		$model = new Comment();
		$model->deleteAll(true);
		$this->assertEquals([], $model->find('all'));

		$model->Attachment->deleteAll(true);
		$this->assertEquals([], $model->Attachment->find('all'));

		$model->validate = ['comment' => 'notBlank'];
		$model->Attachment->validate = ['attachment' => 'notBlank'];
		$model->Attachment->bindModel(['belongsTo' => ['Comment']]);

		$result = $model->saveAssociated(
			[
				'Comment' => [
					'comment' => '',
					'article_id' => 1,
					'user_id' => 1
				],
				'Attachment' => ['attachment' => '']
			]
		);
		$this->assertFalse($result);
		$expected = [
			'comment' => [
				'This field cannot be left blank'
			],
			'Attachment' => [
				'attachment' => [
					'This field cannot be left blank'
				]
			]
		];
		$this->assertEquals($expected, $model->validationErrors);
		$this->assertEquals($expected['Attachment'], $model->Attachment->validationErrors);
	}

/**
 * testSaveAssociatedAtomic method
 *
 * @return void
 */
	public function testSaveAssociatedAtomic() {
		$this->loadFixtures('Article', 'User');
		$TestModel = new Article();

		$result = $TestModel->saveAssociated([
			'Article' => [
				'title' => 'Post with Author',
				'body' => 'This post will be saved with an author',
				'user_id' => 2
			],
			'Comment' => [
				['comment' => 'First new comment', 'user_id' => 2]]
		], ['atomic' => false]);

		$this->assertSame($result, ['Article' => true, 'Comment' => [true]]);

		$result = $TestModel->saveAssociated([
			'Article' => ['id' => 2],
			'Comment' => [
				[
					'comment' => 'First new comment',
					'published' => 'Y',
					'user_id' => 1
				],
				[
					'comment' => 'Second new comment',
					'published' => 'Y',
					'user_id' => 2
			]]
		], ['validate' => true, 'atomic' => false]);
		$this->assertSame($result, ['Article' => true, 'Comment' => [true, true]]);
	}

/**
 * testSaveManyAtomic method
 *
 * @return void
 */
	public function testSaveManyAtomic() {
		$this->loadFixtures('Article', 'User');
		$TestModel = new Article();

		$result = $TestModel->saveMany([
			[
				'id' => '1',
				'title' => 'Baleeted First Post',
				'body' => 'Baleeted!',
				'published' => 'N'
			],
			[
				'id' => '2',
				'title' => 'Just update the title'
			],
			[
				'title' => 'Creating a fourth post',
				'body' => 'Fourth post body',
				'user_id' => 2
			]
		], ['atomic' => false]);
		$this->assertSame($result, [true, true, true]);

		$TestModel->validate = ['title' => 'notBlank', 'author_id' => 'numeric'];
		$result = $TestModel->saveMany([
			[
				'id' => '1',
				'title' => 'Un-Baleeted First Post',
				'body' => 'Not Baleeted!',
				'published' => 'Y'
			],
			[
				'id' => '2',
				'title' => '',
				'body' => 'Trying to get away with an empty title'
			]
		], ['validate' => true, 'atomic' => false]);

		$this->assertSame([true, false], $result);
	}

/**
 * testSaveAssociatedHasMany method
 *
 * @return void
 */
	public function testSaveAssociatedHasMany() {
		$this->loadFixtures('Article', 'Comment');
		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = [];

		$result = $TestModel->saveAssociated([
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'user_id' => 1],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		]);
		$this->assertFalse(empty($result));

		$result = $TestModel->findById(2);
		$expected = [
			'First Comment for Second Article',
			'Second Comment for Second Article',
			'First new comment',
			'Second new comment'
		];
		$this->assertEquals($expected, Hash::extract($result['Comment'], '{n}.comment'));

		$result = $TestModel->saveAssociated(
			[
				'Article' => ['id' => 2],
				'Comment' => [
					[
						'comment' => 'Third new comment',
						'published' => 'Y',
						'user_id' => 1
			]]],
			['atomic' => false]
		);
		$this->assertFalse(empty($result));

		$result = $TestModel->findById(2);
		$expected = [
			'First Comment for Second Article',
			'Second Comment for Second Article',
			'First new comment',
			'Second new comment',
			'Third new comment'
		];
		$this->assertEquals($expected, Hash::extract($result['Comment'], '{n}.comment'));

		$TestModel->beforeSaveReturn = false;
		$result = $TestModel->saveAssociated(
			[
				'Article' => ['id' => 2],
				'Comment' => [
					[
						'comment' => 'Fourth new comment',
						'published' => 'Y',
						'user_id' => 1
			]]],
			['atomic' => false]
		);
		$this->assertEquals(['Article' => false], $result);

		$result = $TestModel->findById(2);
		$expected = [
			'First Comment for Second Article',
			'Second Comment for Second Article',
			'First new comment',
			'Second new comment',
			'Third new comment'
		];
		$this->assertEquals($expected, Hash::extract($result['Comment'], '{n}.comment'));
	}

/**
 * testSaveAssociatedHasManyEmpty method
 *
 * @return void
 */
	public function testSaveAssociatedHasManyEmpty() {
		$this->loadFixtures('Article', 'Comment');
		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = [];
		$TestModel->validate = $TestModel->Comment->validate = ['user_id' => ['notBlank' => ['rule' => 'notBlank', 'required' => true]]];

		//empty hasMany data is ignored in save
		$result = $TestModel->saveAssociated([
			'Article' => ['title' => 'title', 'user_id' => 1],
			'Comment' => []
		], ['validate' => true]);
		$this->assertTrue($result);

		$result = $TestModel->saveAssociated([
			'Article' => ['title' => 'title', 'user_id' => 1],
			'Comment' => []
		], ['validate' => true, 'atomic' => false]);
		$this->assertEquals(['Article' => true], $result);

		//empty primary data is not ignored
		$result = $TestModel->saveAssociated(['Article' => []], ['validate' => true]);
		$this->assertFalse($result);

		$result = $TestModel->saveAssociated(['Article' => []], ['validate' => true, 'atomic' => false]);
		$this->assertEquals(['Article' => false], $result);
	}

/**
 * testSaveAssociatedHasManyValidation method
 *
 * @return void
 */
	public function testSaveAssociatedHasManyValidation() {
		$this->loadFixtures('Article', 'Comment');
		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = [];
		$TestModel->Comment->validate = ['comment' => 'notBlank'];

		$result = $TestModel->saveAssociated([
			'Article' => ['id' => 2],
			'Comment' => [
				['comment' => '', 'published' => 'Y', 'user_id' => 1],
			]
		], ['validate' => true]);
		$this->assertFalse($result);

		$expected = ['Comment' => [
			['comment' => ['This field cannot be left blank']]
		]];
		$this->assertEquals($expected, $TestModel->validationErrors);
		$expected = [
			['comment' => ['This field cannot be left blank']]
		];
		$this->assertEquals($expected, $TestModel->Comment->validationErrors);

		$result = $TestModel->saveAssociated([
			'Article' => ['id' => 2],
			'Comment' => [
				[
					'comment' => '',
					'published' => 'Y',
					'user_id' => 1
			]]
		], ['validate' => 'first']);
		$this->assertFalse($result);
	}

/**
 * test saveMany with transactions and ensure there is no missing rollback.
 *
 * @return void
 */
	public function testSaveManyTransactionNoRollback() {
		$this->loadFixtures('Post');

		$Post = new TestPost();
		$Post->validate = [
			'title' => ['rule' => ['notBlank']]
		];

		// If validation error occurs, rollback() should be called.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->never())->method('commit');
		$db->expects($this->once())->method('rollback');

		$Post->setDataSourceObject($db);

		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => '']
		];
		$Post->saveMany($data, ['validate' => true]);

		// If exception thrown, rollback() should be called too.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->never())->method('commit');
		$db->expects($this->once())->method('rollback');

		$Post->setDataSourceObject($db);

		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => 'New Fifth Post', 'body' => $db->expression('PDO_EXCEPTION()')]
		];

		try {
			$Post->saveMany($data, ['validate' => true]);
			$this->fail('No exception thrown');
		} catch (PDOException $e) {
		}

		// Otherwise, commit() should be called.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->once())->method('commit');
		$db->expects($this->never())->method('rollback');

		$Post->setDataSourceObject($db);

		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => 'New Fifth Post']
		];
		$Post->saveMany($data, ['validate' => true]);
	}

/**
 * test saveAssociated with transactions and ensure there is no missing rollback.
 *
 * @return void
 */
	public function testSaveAssociatedTransactionNoRollback() {
		$this->loadFixtures('Post', 'Author');

		$Post = new TestPost();
		$Post->Author->validate = [
			'user' => ['rule' => ['notBlank']]
		];

		// If validation error occurs, rollback() should be called.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->never())->method('commit');
		$db->expects($this->once())->method('rollback');

		$Post->setDataSourceObject($db);
		$Post->Author->setDataSourceObject($db);

		$data = [
			'Post' => [
				'title' => 'New post',
				'body' => 'Content',
				'published' => 'Y'
			],
			'Author' => [
				'user' => '',
				'password' => "sekret"
			]
		];
		$Post->saveAssociated($data, ['validate' => true, 'atomic' => true]);

		// If exception thrown, commit() should be called.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->never())->method('commit');
		$db->expects($this->once())->method('rollback');

		$Post->setDataSourceObject($db);
		$Post->Author->setDataSourceObject($db);

		$data = [
			'Post' => [
				'title' => 'New post',
				'body' => $db->expression('PDO_EXCEPTION()'),
				'published' => 'Y'
			],
			'Author' => [
				'user' => 'New user',
				'password' => "sekret"
			]
		];

		try {
			$Post->saveAssociated($data, ['validate' => true, 'atomic' => true]);
			$this->fail('No exception thrown');
		} catch (PDOException $e) {
		}

		// Otherwise, commit() should be called.
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->once())->method('begin')->will($this->returnValue(true));
		$db->expects($this->once())->method('commit');
		$db->expects($this->never())->method('rollback');

		$Post->setDataSourceObject($db);
		$Post->Author->setDataSourceObject($db);

		$data = [
			'Post' => [
				'title' => 'New post',
				'body' => 'Content',
				'published' => 'Y'
			],
			'Author' => [
				'user' => 'New user',
				'password' => "sekret"
			]
		];
		$Post->saveAssociated($data, ['validate' => true, 'atomic' => true]);
	}

/**
 * test saveMany with nested saveMany call.
 *
 * @return void
 */
	public function testSaveManyNestedSaveMany() {
		$this->loadFixtures('Sample');
		$TransactionManyTestModel = new TransactionManyTestModel();

		$data = [
			['apple_id' => 1, 'name' => 'sample5'],
		];

		$this->assertTrue($TransactionManyTestModel->saveMany($data, ['atomic' => true]));
	}

/**
 * testSaveManyTransaction method
 *
 * @return void
 */
	public function testSaveManyTransaction() {
		$this->loadFixtures('Post', 'Author', 'Comment', 'Attachment');
		$TestModel = new Post();

		$TestModel->validate = ['title' => 'notBlank'];
		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => 'New Fifth Post'],
			['author_id' => 1, 'title' => '']
		];
		$this->assertFalse($TestModel->saveMany($data));

		$result = $TestModel->find('all', ['recursive' => -1]);
		$expected = [
			['Post' => [
				'id' => '1',
				'author_id' => 1,
				'title' => 'First Post',
				'body' => 'First Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:39:23',
				'updated' => '2007-03-18 10:41:31'
			]],
			['Post' => [
				'id' => '2',
				'author_id' => 3,
				'title' => 'Second Post',
				'body' => 'Second Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:41:23',
				'updated' => '2007-03-18 10:43:31'
			]],
			['Post' => [
				'id' => '3',
				'author_id' => 1,
				'title' => 'Third Post',
				'body' => 'Third Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:43:23',
				'updated' => '2007-03-18 10:45:31'
		]]];

		if (count($result) != 3) {
			// Database doesn't support transactions
			$expected[] = [
				'Post' => [
					'id' => '4',
					'author_id' => 1,
					'title' => 'New Fourth Post',
					'body' => null,
					'published' => 'N'
			]];

			$expected[] = [
				'Post' => [
					'id' => '5',
					'author_id' => 1,
					'title' => 'New Fifth Post',
					'body' => null,
					'published' => 'N',
			]];

			$this->assertEquals(static::date(), $result[3]['Post']['created']);
			$this->assertEquals(static::date(), $result[3]['Post']['updated']);
			$this->assertEquals(static::date(), $result[4]['Post']['created']);
			$this->assertEquals(static::date(), $result[4]['Post']['updated']);
			unset($result[3]['Post']['created'], $result[3]['Post']['updated']);
			unset($result[4]['Post']['created'], $result[4]['Post']['updated']);
			$this->assertEquals($expected, $result);
			// Skip the rest of the transactional tests
			return;
		}

		$this->assertEquals($expected, $result);

		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => ''],
			['author_id' => 1, 'title' => 'New Sixth Post']
		];
		$this->assertFalse($TestModel->saveMany($data));

		$result = $TestModel->find('all', ['recursive' => -1]);
		$expected = [
			['Post' => [
				'id' => '1',
				'author_id' => 1,
				'title' => 'First Post',
				'body' => 'First Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:39:23',
				'updated' => '2007-03-18 10:41:31'
			]],
			['Post' => [
				'id' => '2',
				'author_id' => 3,
				'title' => 'Second Post',
				'body' => 'Second Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:41:23',
				'updated' => '2007-03-18 10:43:31'
			]],
			['Post' => [
				'id' => '3',
				'author_id' => 1,
				'title' => 'Third Post',
				'body' => 'Third Post Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:43:23',
				'updated' => '2007-03-18 10:45:31'
		]]];

		if (count($result) != 3) {
			// Database doesn't support transactions
			$expected[] = [
				'Post' => [
					'id' => '4',
					'author_id' => 1,
					'title' => 'New Fourth Post',
					'body' => 'Third Post Body',
					'published' => 'N'
			]];

			$expected[] = [
				'Post' => [
					'id' => '5',
					'author_id' => 1,
					'title' => 'Third Post',
					'body' => 'Third Post Body',
					'published' => 'N'
			]];
			$this->assertEquals(static::date(), $result[3]['Post']['created']);
			$this->assertEquals(static::date(), $result[3]['Post']['updated']);
			$this->assertEquals(static::date(), $result[4]['Post']['created']);
			$this->assertEquals(static::date(), $result[4]['Post']['updated']);
			unset($result[3]['Post']['created'], $result[3]['Post']['updated']);
			unset($result[4]['Post']['created'], $result[4]['Post']['updated']);
		}
		$this->assertEquals($expected, $result);

		$TestModel->validate = ['title' => 'notBlank'];
		$data = [
			['author_id' => 1, 'title' => 'New Fourth Post'],
			['author_id' => 1, 'title' => 'New Fifth Post'],
			['author_id' => 1, 'title' => 'New Sixth Post']
		];
		$this->assertTrue($TestModel->saveMany($data));

		$result = $TestModel->find('all', [
			'recursive' => -1,
			'fields' => ['author_id', 'title', 'body', 'published'],
			'order' => ['Post.created' => 'ASC']
		]);

		$expected = [
			['Post' => [
				'author_id' => 1,
				'title' => 'First Post',
				'body' => 'First Post Body',
				'published' => 'Y'
			]],
			['Post' => [
				'author_id' => 3,
				'title' => 'Second Post',
				'body' => 'Second Post Body',
				'published' => 'Y'
			]],
			['Post' => [
				'author_id' => 1,
				'title' => 'Third Post',
				'body' => 'Third Post Body',
				'published' => 'Y'
			]],
			['Post' => [
				'author_id' => 1,
				'title' => 'New Fourth Post',
				'body' => '',
				'published' => 'N'
			]],
			['Post' => [
				'author_id' => 1,
				'title' => 'New Fifth Post',
				'body' => '',
				'published' => 'N'
			]],
			['Post' => [
				'author_id' => 1,
				'title' => 'New Sixth Post',
				'body' => '',
				'published' => 'N'
		]]];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveManyValidation method
 *
 * @return void
 */
	public function testSaveManyValidation() {
		$this->loadFixtures('Post', 'Author', 'Comment', 'Attachment');
		$TestModel = new Post();

		$data = [
			[
				'id' => '1',
				'title' => 'Baleeted First Post',
				'body' => 'Baleeted!',
				'published' => 'N'
			],
			[
				'id' => '2',
				'title' => 'Just update the title'
			],
			[
				'title' => 'Creating a fourth post',
				'body' => 'Fourth post body',
				'author_id' => 2
		]];

		$this->assertTrue($TestModel->saveMany($data));

		$result = $TestModel->find('all', ['recursive' => -1, 'order' => 'Post.id ASC']);
		$expected = [
			[
				'Post' => [
					'id' => '1',
					'author_id' => '1',
					'title' => 'Baleeted First Post',
					'body' => 'Baleeted!',
					'published' => 'N',
					'created' => '2007-03-18 10:39:23'
				]
			],
			[
				'Post' => [
					'id' => '2',
					'author_id' => '3',
					'title' => 'Just update the title',
					'body' => 'Second Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23'
				]
			],
			[
				'Post' => [
					'id' => '3',
					'author_id' => '1',
					'title' => 'Third Post',
					'body' => 'Third Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
			]],
			[
				'Post' => [
					'id' => '4',
					'author_id' => '2',
					'title' => 'Creating a fourth post',
					'body' => 'Fourth post body',
					'published' => 'N'
				]
			]
		];

		$this->assertEquals(static::date(), $result[0]['Post']['updated']);
		$this->assertEquals(static::date(), $result[1]['Post']['updated']);
		$this->assertEquals(static::date(), $result[3]['Post']['created']);
		$this->assertEquals(static::date(), $result[3]['Post']['updated']);
		unset($result[0]['Post']['updated'], $result[1]['Post']['updated']);
		unset($result[3]['Post']['created'], $result[3]['Post']['updated']);
		$this->assertEquals($expected, $result);

		$TestModel->validate = ['title' => 'notBlank', 'author_id' => 'numeric'];
		$data = [
			[
				'id' => '1',
				'title' => 'Un-Baleeted First Post',
				'body' => 'Not Baleeted!',
				'published' => 'Y'
			],
			[
				'id' => '2',
				'title' => '',
				'body' => 'Trying to get away with an empty title'
		]];
		$result = $TestModel->saveMany($data);
		$this->assertFalse($result);

		$result = $TestModel->find('all', ['recursive' => -1, 'order' => 'Post.id ASC']);
		$errors = [1 => ['title' => ['This field cannot be left blank']]];
		$transactionWorked = Set::matches('/Post[1][title=Baleeted First Post]', $result);
		if (!$transactionWorked) {
			$this->assertTrue(Set::matches('/Post[1][title=Un-Baleeted First Post]', $result));
			$this->assertTrue(Set::matches('/Post[2][title=Just update the title]', $result));
		}

		$this->assertEquals($errors, $TestModel->validationErrors);

		$TestModel->validate = ['title' => 'notBlank', 'author_id' => 'numeric'];
		$data = [
			[
				'id' => '1',
				'title' => 'Un-Baleeted First Post',
				'body' => 'Not Baleeted!',
				'published' => 'Y'
			],
			[
				'id' => '2',
				'title' => '',
				'body' => 'Trying to get away with an empty title'
		]];
		$result = $TestModel->saveMany($data, ['validate' => true, 'atomic' => false]);
		$this->assertEquals([true, false], $result);

		$result = $TestModel->find('all', [
			'fields' => ['id', 'author_id', 'title', 'body', 'published'],
			'recursive' => -1,
			'order' => 'Post.id ASC'
		]);
		$errors = [1 => ['title' => ['This field cannot be left blank']]];
		$expected = [
			[
				'Post' => [
					'id' => '1',
					'author_id' => '1',
					'title' => 'Un-Baleeted First Post',
					'body' => 'Not Baleeted!',
					'published' => 'Y',
			]],
			[
				'Post' => [
					'id' => '2',
					'author_id' => '3',
					'title' => 'Just update the title',
					'body' => 'Second Post Body',
					'published' => 'Y',
			]],
			[
				'Post' => [
					'id' => '3',
					'author_id' => '1',
					'title' => 'Third Post',
					'body' => 'Third Post Body',
					'published' => 'Y',
			]],
			[
				'Post' => [
					'id' => '4',
					'author_id' => '2',
					'title' => 'Creating a fourth post',
					'body' => 'Fourth post body',
					'published' => 'N',
		]]];
		$this->assertEquals($expected, $result);
		$this->assertEquals($errors, $TestModel->validationErrors);

		$data = [
			[
				'id' => '1',
				'title' => 'Re-Baleeted First Post',
				'body' => 'Baleeted!',
				'published' => 'N'
			],
			[
				'id' => '2',
				'title' => '',
				'body' => 'Trying to get away with an empty title'
		]];
		$this->assertFalse($TestModel->saveMany($data, ['validate' => 'first']));

		$result = $TestModel->find('all', [
			'fields' => ['id', 'author_id', 'title', 'body', 'published'],
			'recursive' => -1,
			'order' => 'Post.id ASC'
		]);
		$this->assertEquals($expected, $result);
		$this->assertEquals($errors, $TestModel->validationErrors);
	}

/**
 * testValidateMany method
 *
 * @return void
 */
	public function testValidateMany() {
		$TestModel = new Article();
		$TestModel->validate = ['title' => 'notBlank'];
		$data = [
				0 => ['title' => ''],
				1 => ['title' => 'title 1'],
				2 => ['title' => 'title 2'],
		];
		$result = $TestModel->validateMany($data);
		$this->assertFalse($result);
		$expected = [
			0 => ['title' => ['This field cannot be left blank']],
		];
		$this->assertEquals($expected, $TestModel->validationErrors);

		$data = [
				0 => ['title' => 'title 0'],
				1 => ['title' => ''],
				2 => ['title' => 'title 2'],
		];
		$result = $TestModel->validateMany($data);
		$this->assertFalse($result);
		$expected = [
			1 => ['title' => ['This field cannot be left blank']],
		];
		$this->assertEquals($expected, $TestModel->validationErrors);
	}

/**
 * testSaveAssociatedValidateFirst method
 *
 * @return void
 */
	public function testSaveAssociatedValidateFirst() {
		$this->loadFixtures('Article', 'Comment', 'Attachment');
		$model = new Article();
		$model->deleteAll(true);

		$model->Comment->validate = ['comment' => 'notBlank'];
		$result = $model->saveAssociated([
			'Article' => [
				'title' => 'Post with Author',
				'body' => 'This post will be saved author'
			],
			'Comment' => [
				['comment' => 'First new comment'],
				['comment' => '']
			]
		], ['validate' => 'first']);

		$this->assertFalse($result);

		$result = $model->find('all');
		$this->assertSame([], $result);
		$expected = ['Comment' => [
			1 => ['comment' => ['This field cannot be left blank']]
		]];

		$this->assertEquals($expected['Comment'], $model->Comment->validationErrors);

		$this->assertSame($model->Comment->find('count'), 0);

		$result = $model->saveAssociated(
			[
				'Article' => [
					'title' => 'Post with Author',
					'body' => 'This post will be saved with an author',
					'user_id' => 2
				],
				'Comment' => [
					[
						'comment' => 'Only new comment',
						'user_id' => 2
			]]],
			['validate' => 'first']
		);

		$this->assertTrue($result);

		$result = $model->Comment->find('all');
		$this->assertSame(count($result), 1);
		$result = Hash::extract($result, '{n}.Comment.article_id');
		$this->assertEquals(4, $result[0]);

		$model->deleteAll(true);
		$data = [
			'Article' => [
				'title' => 'Post with Author saveAlled from comment',
				'body' => 'This post will be saved with an author',
				'user_id' => 2
			],
			'Comment' => [
				'comment' => 'Only new comment', 'user_id' => 2
		]];

		$result = $model->Comment->saveAssociated($data, ['validate' => 'first']);
		$this->assertFalse(empty($result));

		$result = $model->find('all');
		$this->assertEquals(
			'Post with Author saveAlled from comment',
			$result[0]['Article']['title']
		);
		$this->assertEquals('Only new comment', $result[0]['Comment'][0]['comment']);
	}

/**
 * test saveMany()'s return is correct when using atomic = false and validate = first.
 *
 * @return void
 */
	public function testSaveManyValidateFirstAtomicFalse() {
		$Something = new Something();
		$invalidData = [
			[
				'title' => 'foo',
				'body' => 'bar',
				'published' => 'baz',
			],
			[
				'body' => 3,
				'published' => 'sd',
			],
		];
		$Something->create();
		$Something->validate = [
			'title' => [
				'rule' => 'alphaNumeric',
				'required' => true,
			],
			'body' => [
				'rule' => 'alphaNumeric',
				'required' => true,
				'allowEmpty' => true,
			],
		];
		$result = $Something->saveMany($invalidData, [
			'atomic' => false,
			'validate' => 'first',
		]);
		$expected = [true, false];
		$this->assertEquals($expected, $result);

		$Something = new Something();
		$validData = [
			[
				'title' => 'title value',
				'body' => 'body value',
				'published' => 'baz',
			],
			[
				'title' => 'valid',
				'body' => 'this body',
				'published' => 'sd',
			],
		];
		$Something->create();
		$result = $Something->saveMany($validData, [
			'atomic' => false,
			'validate' => 'first',
		]);
		$expected = [true, true];
		$this->assertEquals($expected, $result);
	}

/**
 * testValidateAssociated method
 *
 * @return void
 */
	public function testValidateAssociated() {
		$this->loadFixtures('Attachment', 'Article', 'Comment');
		$TestModel = new Comment();
		$TestModel->Attachment->validate = ['attachment' => 'notBlank'];

		$data = [
			'Comment' => [
				'comment' => 'This is the comment'
			],
			'Attachment' => [
				'attachment' => ''
			]
		];

		$result = $TestModel->validateAssociated($data);
		$this->assertFalse($result);

		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = [];
		$TestModel->Comment->validate = ['comment' => 'notBlank'];

		$data = [
			'Article' => ['id' => 2],
			'Comment' => [
				[
					'id' => 1,
					'comment' => '',
					'published' => 'Y',
					'user_id' => 1],
				[
					'id' => 2,
					'comment' =>
					'comment',
					'published' => 'Y',
					'user_id' => 1
		]]];
		$result = $TestModel->validateAssociated($data);
		$this->assertFalse($result);

		$data = [
			'Article' => ['id' => 2],
			'Comment' => [
				[
					'id' => 1,
					'comment' => '',
					'published' => 'Y',
					'user_id' => 1
				],
				[
					'id' => 2,
					'comment' => 'comment',
					'published' => 'Y',
					'user_id' => 1
				],
				[
					'id' => 3,
					'comment' => '',
					'published' => 'Y',
					'user_id' => 1
		]]];
		$result = $TestModel->validateAssociated($data, [
				'atomic' => false
		]);
		$expected = [
			'Article' => true,
			'Comment' => [false, true, false]
		];
		$this->assertSame($expected, $result);

		$expected = ['Comment' => [
			0 => ['comment' => ['This field cannot be left blank']],
			2 => ['comment' => ['This field cannot be left blank']]
		]];
		$this->assertEquals($expected, $TestModel->validationErrors);

		$expected = [
			0 => ['comment' => ['This field cannot be left blank']],
			2 => ['comment' => ['This field cannot be left blank']]
		];
		$this->assertEquals($expected, $TestModel->Comment->validationErrors);
	}

/**
 * test that saveMany behaves like plain save() when suplied empty data
 *
 * @return void
 */
	public function testSaveManyEmptyData() {
		$this->skipIf($this->db instanceof Sqlserver, 'This test is not compatible with SQL Server.');

		$this->loadFixtures('Article', 'ProductUpdateAll', 'Comment', 'Attachment');
		$model = new Article();
		$result = $model->saveMany([], ['validate' => true]);
		$this->assertFalse(empty($result));

		$model = new ProductUpdateAll();
		$result = $model->saveMany([]);
		$this->assertFalse($result);
	}

/**
 * test that saveAssociated behaves like plain save() when supplied empty data
 *
 * @return void
 */
	public function testSaveAssociatedEmptyData() {
		$this->skipIf($this->db instanceof Sqlserver, 'This test is not compatible with SQL Server.');

		$this->loadFixtures('Article', 'ProductUpdateAll', 'Comment', 'Attachment');
		$model = new Article();
		$result = $model->saveAssociated([], ['validate' => true]);
		$this->assertFalse(empty($result));

		$model = new ProductUpdateAll();
		$result = $model->saveAssociated([]);
		$this->assertFalse($result);
	}

/**
 * Test that saveAssociated will accept expression object values when saving.
 *
 * @return void
 */
	public function testSaveAssociatedExpressionObjects() {
		$this->loadFixtures('Post', 'Author', 'Comment', 'Attachment', 'Article', 'User');
		$TestModel = new Post();
		$db = $TestModel->getDataSource();

		$TestModel->saveAssociated([
			'Post' => [
				'title' => $db->expression("(SELECT 'Post with Author')"),
				'body' => 'This post will be saved with an author'
			],
			'Author' => [
				'user' => 'bob',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf90'
		]], ['atomic' => false]);

		$result = $TestModel->find('first', [
			'order' => ['Post.id ' => 'DESC']
		]);
		$this->assertEquals('Post with Author', $result['Post']['title']);
	}

/**
 * testUpdateWithCalculation method
 *
 * @return void
 */
	public function testUpdateWithCalculation() {
		$this->loadFixtures('DataTest');
		$model = new DataTest();
		$model->deleteAll(true);
		$result = $model->saveMany([
			['count' => 5, 'float' => 1.1],
			['count' => 3, 'float' => 1.2],
			['count' => 4, 'float' => 1.3],
			['count' => 1, 'float' => 2.0],
		]);
		$this->assertFalse(empty($result));

		$result = Hash::extract($model->find('all', ['fields' => 'count']), '{n}.DataTest.count');
		$this->assertEquals([5, 3, 4, 1], $result);

		$this->assertTrue($model->updateAll(['count' => 'count + 2']));
		$result = Hash::extract($model->find('all', ['fields' => 'count']), '{n}.DataTest.count');
		$this->assertEquals([7, 5, 6, 3], $result);

		$this->assertTrue($model->updateAll(['DataTest.count' => 'DataTest.count - 1']));
		$result = Hash::extract($model->find('all', ['fields' => 'count']), '{n}.DataTest.count');
		$this->assertEquals([6, 4, 5, 2], $result);
	}

/**
 * testToggleBoolFields method
 *
 * @return void
 */
	public function testToggleBoolFields() {
		$this->loadFixtures('CounterCacheUser', 'CounterCachePost');
		$Post = new CounterCachePost();
		$Post->unbindModel(['belongsTo' => ['User']], true);

		$true = ['Post' => ['published' => true, 'id' => 2]];
		$false = ['Post' => ['published' => false, 'id' => 2]];
		$fields = ['Post.published', 'Post.id'];
		$updateConditions = ['Post.id' => 2];

		// check its true
		$result = $Post->find('first', ['conditions' => $updateConditions, 'fields' => $fields]);
		$this->assertEquals($true, $result);

		// Testing without the alias
		$this->assertTrue($Post->updateAll(['published' => 'NOT published'], $updateConditions));
		$result = $Post->find('first', ['conditions' => $updateConditions, 'fields' => $fields]);
		$this->assertEquals($false, $result);

		$this->assertTrue($Post->updateAll(['published' => 'NOT published'], $updateConditions));
		$result = $Post->find('first', ['conditions' => $updateConditions, 'fields' => $fields]);
		$this->assertEquals($true, $result);

		$db = ConnectionManager::getDataSource('test');
		$alias = $db->name('Post.published');

		// Testing with the alias
		$this->assertTrue($Post->updateAll(['Post.published' => "NOT $alias"], $updateConditions));
		$result = $Post->find('first', ['conditions' => $updateConditions, 'fields' => $fields]);
		$this->assertEquals($false, $result);

		$this->assertTrue($Post->updateAll(['Post.published' => "NOT $alias"], $updateConditions));
		$result = $Post->find('first', ['conditions' => $updateConditions, 'fields' => $fields]);
		$this->assertEquals($true, $result);
	}

/**
 * TestFindAllWithoutForeignKey
 *
 * @return void
 */
	public function testFindAllForeignKey() {
		$this->loadFixtures('ProductUpdateAll', 'GroupUpdateAll');
		$ProductUpdateAll = new ProductUpdateAll();

		$conditions = ['Group.name' => 'group one'];

		$ProductUpdateAll->bindModel([
			'belongsTo' => [
				'Group' => ['className' => 'GroupUpdateAll']
			]
		]);

		$ProductUpdateAll->belongsTo = [
			'Group' => ['className' => 'GroupUpdateAll', 'foreignKey' => 'group_id']
		];

		$results = $ProductUpdateAll->find('all', compact('conditions'));
		$this->assertTrue(!empty($results));

		$ProductUpdateAll->bindModel(['belongsTo' => ['Group']]);
		$ProductUpdateAll->belongsTo = [
			'Group' => [
				'className' => 'GroupUpdateAll',
				'foreignKey' => false,
				'conditions' => 'ProductUpdateAll.groupcode = Group.code'
			]];

		$resultsFkFalse = $ProductUpdateAll->find('all', compact('conditions'));
		$this->assertTrue(!empty($resultsFkFalse));
		$expected = [
			'0' => [
				'ProductUpdateAll' => [
					'id' => 1,
					'name'	=> 'product one',
					'groupcode' => 120,
					'group_id' => 1],
				'Group' => [
					'id' => 1,
					'name' => 'group one',
					'code' => 120]
				],
			'1' => [
				'ProductUpdateAll' => [
					'id' => 2,
					'name'	=> 'product two',
					'groupcode'	=> 120,
					'group_id'	=> 1],
				'Group' => [
					'id' => 1,
					'name' => 'group one',
					'code' => 120]
				]

			];
		$this->assertEquals($expected, $results);
		$this->assertEquals($expected, $resultsFkFalse);
	}

/**
 * test updateAll with empty values.
 *
 * @return void
 */
	public function testUpdateAllEmptyValues() {
		$this->skipIf($this->db instanceof Sqlserver || $this->db instanceof Postgres, 'This test is not compatible with Postgres or SQL Server.');

		$this->loadFixtures('Author', 'Post');
		$model = new Author();
		$result = $model->updateAll(['user' => '""']);
		$this->assertTrue($result);
	}

/**
 * testUpdateAllWithJoins
 *
 * @return void
 */
	public function testUpdateAllWithJoins() {
		$this->skipIf(!$this->db instanceof Mysql, 'Currently, there is no way of doing joins in an update statement in postgresql or sqlite');

		$this->loadFixtures('ProductUpdateAll', 'GroupUpdateAll');
		$ProductUpdateAll = new ProductUpdateAll();

		$conditions = ['Group.name' => 'group one'];

		$ProductUpdateAll->bindModel(['belongsTo' => [
			'Group' => ['className' => 'GroupUpdateAll']]]
		);

		$ProductUpdateAll->updateAll(['name' => "'new product'"], $conditions);
		$results = $ProductUpdateAll->find('all', [
			'conditions' => ['ProductUpdateAll.name' => 'new product']
		]);
		$expected = [
			'0' => [
				'ProductUpdateAll' => [
					'id' => 1,
					'name' => 'new product',
					'groupcode'	=> 120,
					'group_id' => 1],
				'Group' => [
					'id' => 1,
					'name' => 'group one',
					'code' => 120]
				],
			'1' => [
				'ProductUpdateAll' => [
					'id' => 2,
					'name' => 'new product',
					'groupcode' => 120,
					'group_id' => 1],
				'Group' => [
					'id' => 1,
					'name' => 'group one',
					'code' => 120]]];

		$this->assertEquals($expected, $results);
	}

/**
 * testUpdateAllWithoutForeignKey
 *
 * @return void
 */
	public function testUpdateAllWithoutForeignKey() {
		$this->skipIf(!$this->db instanceof Mysql, 'Currently, there is no way of doing joins in an update statement in postgresql');

		$this->loadFixtures('ProductUpdateAll', 'GroupUpdateAll');
		$ProductUpdateAll = new ProductUpdateAll();

		$conditions = ['Group.name' => 'group one'];

		$ProductUpdateAll->bindModel(['belongsTo' => [
			'Group' => ['className' => 'GroupUpdateAll']
		]]);

		$ProductUpdateAll->belongsTo = [
			'Group' => [
				'className' => 'GroupUpdateAll',
				'foreignKey' => false,
				'conditions' => 'ProductUpdateAll.groupcode = Group.code'
			]
		];

		$ProductUpdateAll->updateAll(['name' => "'new product'"], $conditions);
		$resultsFkFalse = $ProductUpdateAll->find('all', ['conditions' => ['ProductUpdateAll.name' => 'new product']]);
		$expected = [
			'0' => [
				'ProductUpdateAll' => [
					'id' => 1,
					'name' => 'new product',
					'groupcode'	=> 120,
					'group_id' => 1],
				'Group' => [
					'id' => 1,
					'name' => 'group one',
					'code' => 120]
				],
			'1' => [
				'ProductUpdateAll' => [
					'id' => 2,
					'name' => 'new product',
					'groupcode' => 120,
					'group_id' => 1],
				'Group' => [
					'id' => 1,
					'name' => 'group one',
					'code' => 120]]];
		$this->assertEquals($expected, $resultsFkFalse);
	}

/**
 * test writing floats in german locale.
 *
 * @return void
 */
	public function testWriteFloatAsGerman() {
		$restore = setlocale(LC_NUMERIC, 0);

		$this->skipIf(setlocale(LC_NUMERIC, 'de_DE') === false, "The German locale isn't available.");

		$model = new DataTest();
		$result = $model->save([
			'count' => 1,
			'float' => 3.14593
		]);
		$this->assertTrue((bool)$result);
		setlocale(LC_NUMERIC, $restore);
	}

/**
 * Test returned array contains primary key when save creates a new record
 *
 * @return void
 */
	public function testPkInReturnArrayForCreate() {
		$this->loadFixtures('Article');
		$TestModel = new Article();

		$data = ['Article' => [
			'user_id' => '1',
			'title' => 'Fourth Article',
			'body' => 'Fourth Article Body',
			'published' => 'Y'
		]];
		$result = $TestModel->save($data);
		$this->assertSame($result['Article']['id'], $TestModel->id);
	}

/**
 * testSaveAllFieldListValidateBelongsTo
 *
 * @return void
 */
	public function testSaveAllFieldListValidateBelongsTo() {
		$this->loadFixtures('Post', 'Author', 'Comment', 'Attachment');
		$TestModel = new Post();

		$result = $TestModel->find('all');
		$this->assertEquals(3, count($result));
		$this->assertFalse(isset($result[3]));

		// test belongsTo
		$fieldList = [
			'Post' => ['title'],
			'Author' => ['user']
		];
		$data = [
			'Post' => [
				'title' => 'Post without body',
				'body' => 'This will not be saved',
			],
			'Author' => [
				'user' => 'bob',
				'test' => 'This will not be saved',

		]];
		$TestModel->saveAll($data, ['fieldList' => $fieldList]);

		$result = $TestModel->find('all', [
			'order' => 'Post.id ASC',
		]);
		$expected = [
			'Post' => [
				'id' => '4',
				'author_id' => '5',
				'title' => 'Post without body',
				'body' => null,
				'published' => 'N',
				'created' => static::date(),
				'updated' => static::date(),
			],
			'Author' => [
				'id' => '5',
				'user' => 'bob',
				'password' => null,
				'created' => static::date(),
				'updated' => static::date(),
				'test' => 'working',
			],
		];
		$this->assertEquals($expected, $result[3]);
		$this->assertEquals(4, count($result));
		$this->assertEquals('', $result[3]['Post']['body']);
		$this->assertEquals('working', $result[3]['Author']['test']);

		$fieldList = [
			'Post' => ['title']
		];
		$data = [
			'Post' => [
				'title' => 'Post without body 2',
				'body' => 'This will not be saved'
			],
			'Author' => [
				'user' => 'jack'
			]
		];
		$TestModel->saveAll($data, ['fieldList' => $fieldList]);
		$result = $TestModel->find('all', [
			'order' => 'Post.id ASC',
		]);
		$this->assertNull($result[4]['Post']['body']);

		$fieldList = [
			'Author' => ['password']
		];
		$data = [
			'Post' => [
				'id' => '5',
				'title' => 'Post title',
				'body' => 'Post body'
			],
			'Author' => [
				'id' => '6',
				'user' => 'will not change',
				'password' => 'foobar'
			]
		];
		$result = $TestModel->saveAll($data, ['fieldList' => $fieldList]);
		$this->assertTrue($result);

		$result = $TestModel->find('all', [
			'order' => 'Post.id ASC',
		]);
		$expected = [
			'Post' => [
				'id' => '5',
				'author_id' => '6',
				'title' => 'Post title',
				'body' => 'Post body',
				'published' => 'N',
				'created' => static::date(),
				'updated' => static::date()
			],
			'Author' => [
				'id' => '6',
				'user' => 'jack',
				'password' => 'foobar',
				'created' => static::date(),
				'updated' => static::date(),
				'test' => 'working'
			],
		];
		$this->assertEquals($expected, $result[4]);

		// test multirecord
		$this->db->truncate($TestModel);

		$fieldList = ['title', 'author_id'];
		$TestModel->saveAll([
			[
				'title' => 'Multi-record post 1',
				'body' => 'First multi-record post',
				'author_id' => 2
			],
			[
				'title' => 'Multi-record post 2',
				'body' => 'Second multi-record post',
				'author_id' => 2
		]], ['fieldList' => $fieldList]);

		$result = $TestModel->find('all', [
			'recursive' => -1,
			'order' => 'Post.id ASC'
		]);
		$expected = [
			[
				'Post' => [
					'id' => '1',
					'author_id' => '2',
					'title' => 'Multi-record post 1',
					'body' => '',
					'published' => 'N',
					'created' => static::date(),
					'updated' => static::date()
				]
			],
			[
				'Post' => [
					'id' => '2',
					'author_id' => '2',
					'title' => 'Multi-record post 2',
					'body' => '',
					'published' => 'N',
					'created' => static::date(),
					'updated' => static::date()
				]
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllFieldListHasMany method
 *
 * @return void
 */
	public function testSaveAllFieldListHasMany() {
		$this->loadFixtures('Article', 'Comment');
		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = [];

		$this->db->truncate($TestModel);
		$this->db->truncate(new Comment());

		$data = [
			'Article' => ['title' => 'I will not save'],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'user_id' => 1],
				['comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2]
			]
		];

		$fieldList = [
			'Article' => ['id'],
			'Comment' => ['article_id', 'user_id']
		];
		$TestModel->saveAll($data, ['fieldList' => $fieldList]);

		$result = $TestModel->find('all');
		$this->assertEquals('', $result[0]['Article']['title']);
		$this->assertEquals('', $result[0]['Comment'][0]['comment']);
		$this->assertEquals('', $result[0]['Comment'][1]['comment']);

		$fieldList = [
			'Article' => ['id'],
			'Comment' => ['user_id']
		];
		$TestModel->saveAll($data, ['fieldList' => $fieldList]);
		$result = $TestModel->find('all');

		$this->assertEquals('', $result[1]['Article']['title']);
		$this->assertEquals(2, count($result[1]['Comment']));

		$TestModel->whitelist = ['id'];
		$TestModel->Comment->whitelist = ['user_id'];
		$TestModel->saveAll($data);
		$result = $TestModel->find('all');

		$this->assertEquals('', $result[2]['Article']['title']);
		$this->assertEquals(2, count($result[2]['Comment']));
	}

/**
 * testSaveAllFieldListHasOne method
 *
 * @return void
 */
	public function testSaveAllFieldListHasOne() {
		$this->loadFixtures('Attachment', 'Comment', 'Article', 'User');
		$TestModel = new Comment();

		$TestModel->validate = ['comment' => 'notBlank'];
		$TestModel->Attachment->validate = ['attachment' => 'notBlank'];

		$record = [
			'Comment' => [
				'user_id' => 1,
				'article_id' => 1,
				'comment' => '',
			],
			'Attachment' => [
				'attachment' => ''
			]
		];
		$result = $TestModel->saveAll($record, ['validate' => 'only']);
		$this->assertFalse($result);

		$fieldList = [
			'Comment' => ['id', 'article_id', 'user_id'],
			'Attachment' => ['comment_id']
		];
		$result = $TestModel->saveAll($record, [
			'fieldList' => $fieldList, 'validate' => 'only'
		]);
		$this->assertTrue($result);
		$this->assertEmpty($TestModel->validationErrors);
	}

/**
 * testSaveAllFieldListHasOneAddFkToWhitelist method
 *
 * @return void
 */
	public function testSaveAllFieldListHasOneAddFkToWhitelist() {
		$this->loadFixtures('ArticleFeatured', 'Featured');
		$Article = new ArticleFeatured();
		$Article->belongsTo = $Article->hasMany = [];
		$Article->Featured->validate = ['end_date' => 'notBlank'];

		$record = [
			'ArticleFeatured' => [
				'user_id' => 1,
				'title' => 'First Article',
				'body' => '',
				'published' => 'Y'
			],
			'Featured' => [
				'category_id' => 1,
				'end_date' => ''
			]
		];
		$result = $Article->saveAll($record, ['validate' => 'only']);
		$this->assertFalse($result);
		$expected = [
			'body' => [
				'This field cannot be left blank'
			],
			'Featured' => [
				'end_date' => [
					'This field cannot be left blank'
				]
			]
		];
		$this->assertEquals($expected, $Article->validationErrors);

		$fieldList = [
			'ArticleFeatured' => ['user_id', 'title'],
			'Featured' => ['category_id']
		];

		$result = $Article->saveAll($record, [
			'fieldList' => $fieldList, 'validate' => 'first'
		]);
		$this->assertTrue($result);
		$this->assertEmpty($Article->validationErrors);

		$Article->recursive = 0;
		$result = $Article->find('first', ['order' => ['ArticleFeatured.created' => 'DESC']]);
		$this->assertSame($result['ArticleFeatured']['id'], $result['Featured']['article_featured_id']);
	}

/**
 * testSaveAllDeepFieldListValidateBelongsTo
 *
 * @return void
 */
	public function testSaveAllDeepFieldListValidateBelongsTo() {
		$this->loadFixtures('Post', 'Author', 'Comment', 'Attachment', 'Article', 'User');
		$TestModel = new Post();
		$TestModel->Author->bindModel(['hasMany' => ['Comment' => ['foreignKey' => 'user_id']]], false);
		$TestModel->recursive = 2;

		$result = $TestModel->find('all');
		$this->assertEquals(3, count($result));
		$this->assertFalse(isset($result[3]));

		// test belongsTo
		$fieldList = [
			'Post' => ['title', 'author_id'],
			'Author' => ['user'],
			'Comment' => ['comment']
		];
		$TestModel->saveAll([
			'Post' => [
				'title' => 'Post without body',
				'body' => 'This will not be saved',
			],
			'Author' => [
				'user' => 'bob',
				'test' => 'This will not be saved',
				'Comment' => [
					['id' => 5, 'comment' => 'I am still published', 'published' => 'N']]

		]], ['fieldList' => $fieldList, 'deep' => true]);

		$result = $TestModel->Author->Comment->find('first', [
			'conditions' => ['Comment.id' => 5],
			'fields' => ['comment', 'published']
		]);
		$expected = [
			'Comment' => [
				'comment' => 'I am still published',
				'published' => 'Y'
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllDeepFieldListHasMany method
 *
 * @return void
 */
	public function testSaveAllDeepFieldListHasMany() {
		$this->loadFixtures('Article', 'Comment', 'User');
		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = [];

		$this->db->truncate($TestModel);
		$this->db->truncate(new Comment());

		$fieldList = [
			'Article' => ['id'],
			'Comment' => ['article_id', 'user_id'],
			'User' => ['user']
		];

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2, 'title' => 'I will not save'],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'user_id' => 1],
				[
					'comment' => 'Second new comment', 'published' => 'Y', 'user_id' => 2,
					'User' => ['user' => 'nopassword', 'password' => 'not saved']
				]
			]
		], ['fieldList' => $fieldList, 'deep' => true]);

		$result = $TestModel->Comment->User->find('first', [
			'conditions' => ['User.user' => 'nopassword'],
			'fields' => ['user', 'password']
		]);
		$expected = [
			'User' => [
				'user' => 'nopassword',
				'password' => ''
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllDeepHasManyBelongsTo method
 *
 * @return void
 */
	public function testSaveAllDeepHasManyBelongsTo() {
		$this->loadFixtures('Article', 'Comment', 'User');
		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = [];

		$this->db->truncate($TestModel);
		$this->db->truncate(new Comment());

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2, 'title' => 'The title'],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'user_id' => 1],
				[
					'comment' => 'belongsto', 'published' => 'Y',
					'User' => ['user' => 'findme', 'password' => 'somepass']
				]
			]
		], ['deep' => true]);

		$result = $TestModel->Comment->User->find('first', [
			'conditions' => ['User.user' => 'findme'],
			'fields' => ['id', 'user', 'password']
		]);
		$expected = [
			'User' => [
				'id' => 5,
				'user' => 'findme',
				'password' => 'somepass',
			]
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->Comment->find('first', [
			'conditions' => ['Comment.user_id' => 5],
			'fields' => ['id', 'comment', 'published', 'user_id']
		]);
		$expected = [
			'Comment' => [
				'id' => 2,
				'comment' => 'belongsto',
				'published' => 'Y',
				'user_id' => 5
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllDeepHasManyhasMany method
 *
 * @return void
 */
	public function testSaveAllDeepHasManyHasMany() {
		$this->loadFixtures('Article', 'Comment', 'User', 'Attachment');
		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = $TestModel->Comment->belongsTo = [];
		$TestModel->Comment->unbindModel(['hasOne' => ['Attachment']], false);
		$TestModel->Comment->bindModel(['hasMany' => ['Attachment']], false);

		$this->db->truncate($TestModel);
		$this->db->truncate(new Comment());
		$this->db->truncate(new Attachment());

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2, 'title' => 'The title'],
			'Comment' => [
				['comment' => 'First new comment', 'published' => 'Y', 'user_id' => 1],
				[
					'comment' => 'hasmany', 'published' => 'Y', 'user_id' => 5,
					'Attachment' => [
						['attachment' => 'first deep attachment'],
						['attachment' => 'second deep attachment'],
					]
				]
			]
		], ['deep' => true]);

		$result = $TestModel->Comment->find('first', [
			'conditions' => ['Comment.comment' => 'hasmany'],
			'fields' => ['id', 'comment', 'published', 'user_id'],
			'recursive' => -1
		]);
		$expected = [
			'Comment' => [
				'id' => 2,
				'comment' => 'hasmany',
				'published' => 'Y',
				'user_id' => 5
			]
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->Comment->Attachment->find('all', [
			'fields' => ['attachment', 'comment_id'],
			'order' => ['Attachment.id' => 'ASC']
		]);
		$expected = [
			['Attachment' => ['attachment' => 'first deep attachment', 'comment_id' => 2]],
			['Attachment' => ['attachment' => 'second deep attachment', 'comment_id' => 2]],
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveAllDeepOrderHasManyHasMany method
 *
 * @return void
 */
	public function testSaveAllDeepOrderHasManyHasMany() {
		$this->loadFixtures('Article', 'Comment', 'User', 'Attachment');
		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = $TestModel->Comment->belongsTo = [];
		$TestModel->Comment->unbindModel(['hasOne' => ['Attachment']], false);
		$TestModel->Comment->bindModel(['hasMany' => ['Attachment']], false);

		$this->db->truncate($TestModel);
		$this->db->truncate(new Comment());
		$this->db->truncate(new Attachment());

		$result = $TestModel->saveAll([
			'Article' => ['id' => 2, 'title' => 'Comment has its data after Attachment'],
			'Comment' => [
				[
					'Attachment' => [
						['attachment' => 'attachment should be created with comment_id'],
						['attachment' => 'comment should be created with article_id'],
					],
					'comment' => 'after associated data',
					'user_id' => 1
				]
			]
		], ['deep' => true]);
		$result = $TestModel->Comment->find('first', [
			'conditions' => ['Comment.article_id' => 2],
		]);

		$this->assertEquals(2, $result['Comment']['article_id']);
		$this->assertEquals(2, count($result['Attachment']));
	}

/**
 * testSaveAllDeepEmptyHasManyHasMany method
 *
 * @return void
 */
	public function testSaveAllDeepEmptyHasManyHasMany() {
		$this->skipIf(!$this->db instanceof Mysql, 'This test is only compatible with Mysql.');

		$this->loadFixtures('Article', 'Comment', 'User', 'Attachment');
		$TestModel = new Article();
		$TestModel->belongsTo = $TestModel->hasAndBelongsToMany = $TestModel->Comment->belongsTo = [];
		$TestModel->Comment->unbindModel(['hasOne' => ['Attachment']], false);
		$TestModel->Comment->bindModel(['hasMany' => ['Attachment']], false);

		$this->db->truncate($TestModel);
		$this->db->truncate(new Comment());
		$this->db->truncate(new Attachment());

		$result = $TestModel->saveAll([
			'Article' => ['id' => 3, 'user_id' => 1, 'title' => 'Comment has no data'],
			'Comment' => [
				[
					'user_id' => 1,
					'Attachment' => [
						['attachment' => 'attachment should be created with comment_id'],
						['attachment' => 'comment should be created with article_id'],
					],
				]
			]
		], ['deep' => true]);
		$result = $TestModel->Comment->find('first', [
			'conditions' => ['Comment.article_id' => 3],
		]);

		$this->assertEquals(3, $result['Comment']['article_id']);
		$this->assertEquals(2, count($result['Attachment']));
	}

/**
 * Test that boolean fields don't cause saveMany to fail
 *
 * @return void
 */
	public function testSaveManyBooleanFields() {
		$this->loadFixtures('Item', 'Syfile', 'Image');
		$data = [
			[
				'Item' => [
					'name' => 'testing',
					'syfile_id' => 1,
					'published' => false
				]
			],
			[
				'Item' => [
					'name' => 'testing 2',
					'syfile_id' => 1,
					'published' => true
				]
			],
		];
		$item = ClassRegistry::init('Item');
		$result = $item->saveMany($data, ['atomic' => false]);

		$this->assertCount(2, $result, '2 records should have been saved.');
		$this->assertTrue($result[0], 'Both should have succeded');
		$this->assertTrue($result[1], 'Both should have succeded');
	}

/**
 * testSaveManyDeepHasManyValidationFailure method
 *
 * @return void
 */
	public function testSaveManyDeepHasManyValidationFailure() {
		$this->loadFixtures('Article', 'Comment');
		$TestModel = new Article();
		$TestModel->Comment->validate = [
			'comment' => [
				'notBlank' => [
					'rule' => ['notBlank'],
				]
			]
		];

		$result = $TestModel->saveMany([
			[
				'user_id' => 1,
				'title' => 'New Article',
				'body' => 'This article contains a invalid comment',
				'Comment' => [
					[
						'user_id' => 1,
						'comment' => ''
					]
				]
			]
		], ['deep' => true]);
		$this->assertFalse($result);
		$this->assertEquals([
			[
				'Comment' => [
					['comment' => ['notBlank']]
				]
			]
		], $TestModel->validationErrors);
	}

/**
 * testSaveAssociatedDeepHasOneHasManyValidateTrueValidationFailure method
 *
 * @return void
 */
	public function testSaveAssociatedDeepHasOneHasManyValidateTrueValidationFailure() {
		$this->loadFixtures('User', 'Article', 'Comment');
		$TestModel = new UserHasOneArticle();
		$TestModel->Article->Comment->validate = [
			'comment' => [
				'notBlank' => [
					'rule' => ['notBlank'],
				]
			]
		];

		$result = $TestModel->saveAssociated([
			'User' => [
				'user' => 'hiromi',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
			],
			'Article' => [
				'title' => 'Article with User',
				'body' => 'This article will be saved with an user and contains a invalid comment',
				'Comment' => [
					[
						'user_id' => 1,
						'comment' => ''
					]
				]
			]
		], ['deep' => true, 'validate' => true]);
		$this->assertFalse($result);
		$this->assertEquals([
			'Article' => [
				'Comment' => [
					['comment' => ['notBlank']]
				]
			]
		], $TestModel->validationErrors);
	}

/**
 * testSaveAssociatedDeepBelongsToHasManyValidateTrueValidationFailure method
 *
 * @return void
 */
	public function testSaveAssociatedDeepBelongsToHasManyValidateTrueValidationFailure() {
		$this->loadFixtures('ArticlesTag', 'Article', 'Comment');
		$TestModel = new ArticlesTagBelongsToArticle();
		$TestModel->Article->Comment->validate = [
			'comment' => [
				'notBlank' => [
					'rule' => ['notBlank'],
				]
			]
		];

		$result = $TestModel->saveAssociated([
			'ArticlesTagBelongsToArticle' => [
				'tag_id' => 1,
			],
			'Article' => [
				'title' => 'Article with User',
				'body' => 'This article will be saved with an user and contains a invalid comment',
				'Comment' => [
					[
						'user_id' => 1,
						'comment' => ''
					]
				]
			]
		], ['deep' => true, 'validate' => true]);
		$this->assertFalse($result);
		$this->assertEquals([
			'Article' => [
				'Comment' => [
					['comment' => ['notBlank']]
				]
			]
		], $TestModel->validationErrors);
	}

/**
 * Test that boolean fields don't cause saveAssociated to fail
 *
 * @return void
 */
	public function testSaveAssociatedHasOneBooleanFields() {
		$this->loadFixtures('Item', 'Syfile', 'Image');
		$data = [
			'Syfile' => [
				'image_id' => 1,
				'name' => 'Some file',
			],
			'Item' => [
				'name' => 'testing',
				'published' => false
			],
		];
		$syfile = ClassRegistry::init('Syfile');
		$syfile->bindModel(['hasOne' => ['Item']], false);
		$result = $syfile->saveAssociated($data, ['atomic' => false]);

		$this->assertCount(2, $result, '2 records should have been saved.');
		$this->assertTrue($result['Syfile'], 'Both should have succeded');
		$this->assertTrue($result['Item'], 'Both should have succeded');
	}

/**
 * Test that boolean fields don't cause saveAssociated to fail
 *
 * @return void
 */
	public function testSaveAssociatedBelongsToBooleanFields() {
		$this->loadFixtures('Item', 'Syfile', 'Image');
		$data = [
			'Syfile' => [
				'image_id' => 1,
				'name' => 'Some file',
			],
			'Item' => [
				'name' => 'testing',
				'syfile_id' => 2,
				'published' => false
			],
		];
		$item = ClassRegistry::init('Item');
		$item->bindModel([
			'belongsTo' => [
				'Item' => [
					'foreignKey' => 'image_id'
				]
			]
		], false);
		$result = $item->saveAssociated($data, ['atomic' => false]);

		$this->assertCount(2, $result, '2 records should have been saved.');
		$this->assertTrue($result['Syfile'], 'Both should have succeded');
		$this->assertTrue($result['Item'], 'Both should have succeded');
	}

/**
 * testUpdateAllBoolean
 *
 * @return void
 */
	public function testUpdateAllBoolean() {
		$this->loadFixtures('Item', 'Syfile', 'Portfolio', 'Image', 'ItemsPortfolio');
		$TestModel = new Item();
		$result = $TestModel->updateAll(['published' => true]);
		$this->assertTrue($result);

		$result = $TestModel->find('first', ['fields' => ['id', 'published']]);
		$this->assertEquals(true, $result['Item']['published']);
	}

/**
 * testUpdateAllBooleanConditions
 *
 * @return void
 */
	public function testUpdateAllBooleanConditions() {
		$this->loadFixtures('Item', 'Syfile', 'Portfolio', 'Image', 'ItemsPortfolio');
		$TestModel = new Item();

		$result = $TestModel->updateAll(['published' => true], ['Item.id' => 1]);
		$this->assertTrue($result);
		$result = $TestModel->find('first', [
			'fields' => ['id', 'published'],
			'conditions' => ['Item.id' => 1]]);
		$this->assertEquals(true, $result['Item']['published']);
	}

/**
 * testUpdateBoolean
 *
 * @return void
 */
	public function testUpdateBoolean() {
		$this->loadFixtures('Item', 'Syfile', 'Portfolio', 'Image', 'ItemsPortfolio');
		$TestModel = new Item();

		$result = $TestModel->save(['published' => true, 'id' => 1]);
		$this->assertTrue((bool)$result);
		$result = $TestModel->find('first', [
			'fields' => ['id', 'published'],
			'conditions' => ['Item.id' => 1]]);
		$this->assertEquals(true, $result['Item']['published']);
	}

/**
 * Test the clear() method.
 *
 * @return void
 */
	public function testClear() {
		$this->loadFixtures('Bid');
		$model = ClassRegistry::init('Bid');
		$model->set(['name' => 'Testing', 'message_id' => 3]);
		$this->assertTrue(isset($model->data['Bid']['name']));
		$this->assertTrue($model->clear());
		$this->assertFalse(isset($model->data['Bid']['name']));
		$this->assertFalse(isset($model->data['Bid']['message_id']));
	}

/**
 * Test that Model::save() doesn't generate a query with WHERE 1 = 1 on race condition.
 *
 * @link https://github.com/cakephp/cakephp/issues/3857
 * @return void
 */
	public function testSafeUpdateMode() {
		$this->loadFixtures('User');

		$User = ClassRegistry::init('User');
		$this->assertFalse($User->__safeUpdateMode);

		$User->getEventManager()->attach([$this, 'deleteMe'], 'Model.beforeSave');

		$User->id = 1;
		$User->set(['user' => 'nobody']);
		$User->save();

		$users = $User->find('list', ['fields' => 'User.user']);

		$expected = [
			2 => 'nate',
			3 => 'larry',
			4 => 'garrett',
		];
		$this->assertEquals($expected, $users);
		$this->assertFalse($User->__safeUpdateMode);

		$User->id = 2;
		$User->set(['user' => $User->getDataSource()->expression('PDO_EXCEPTION()')]);
		try {
			$User->save(null, false);
			$this->fail('No exception thrown');
		} catch (PDOException $e) {
			$this->assertFalse($User->__safeUpdateMode);
		}
	}

/**
 * Emulates race condition
 *
 * @param CakeEvent $event containing the Model
 * @return void
 */
	public function deleteMe($event) {
		$Model = $event->subject;
		$Model->getDataSource()->delete($Model, [$Model->alias . '.' . $Model->primaryKey => $Model->id]);
	}

/**
 * Creates a convenient mock DboSource
 *
 * We cannot call several methods via mock DboSource, such as DboSource::value()
 * because mock DboSource has no $_connection.
 * This method helps us to avoid this problem.
 *
 * @param array $methods Configurable method names.
 * @return DboSource
 */
	protected function _getMockDboSource($methods = []) {
		$testDb = ConnectionManager::getDataSource('test');

		$passthrough = array_diff(['value', 'begin', 'rollback', 'commit', 'describe', 'lastInsertId', 'execute'], $methods);

		$methods = array_merge($methods, $passthrough);
		if (!in_array('connect', $methods)) {
			$methods[] = 'connect'; // This will be called by DboSource::__construct().
		}

		$db = $this->getMock('DboSource', $methods);
		$db->columns = $testDb->columns;
		$db->startQuote = $testDb->startQuote;
		$db->endQuote = $testDb->endQuote;

		foreach ($passthrough as $method) {
			$db->expects($this->any())
				->method($method)
				->will($this->returnCallback([$testDb, $method]));
		}

		return $db;
	}

/**
 * Test that transactions behave correctly on nested saveMany calls.
 *
 * @return void
 */
	public function testTransactionOnNestedSaveMany() {
		$this->loadFixtures('Post');
		$Post = new TestPost();
		$Post->getEventManager()->attach([$this, 'nestedSaveMany'], 'Model.afterSave');

		// begin -> [ begin -> commit ] -> commit
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->exactly(2))->method('begin')->will($this->returnValue(true));
		$db->expects($this->exactly(2))->method('commit');
		$db->expects($this->never())->method('rollback');
		$Post->setDataSourceObject($db);

		$data = [
			['author_id' => 1, 'title' => 'Outer Post'],
		];
		$Post->dataForAfterSave = [
			['author_id' => 1, 'title' => 'Inner Post'],
		];
		$this->assertTrue($Post->saveMany($data));

		// begin -> [  begin(false) ] -> commit
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->at(0))->method('begin')->will($this->returnValue(true));
		$db->expects($this->at(1))->method('begin')->will($this->returnValue(false));
		$db->expects($this->once())->method('commit');
		$db->expects($this->never())->method('rollback');
		$Post->setDataSourceObject($db);

		$data = [
			['author_id' => 1, 'title' => 'Outer Post'],
		];
		$Post->dataForAfterSave = [
			['author_id' => 1, 'title' => 'Inner Post'],
		];
		$this->assertTrue($Post->saveMany($data));

		// begin -> [ begin -> rollback ] -> rollback
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->exactly(2))->method('begin')->will($this->returnValue(true));
		$db->expects($this->never())->method('commit');
		$db->expects($this->exactly(2))->method('rollback');
		$Post->setDataSourceObject($db);
		$data = [
			['author_id' => 1, 'title' => 'Outer Post'],
		];
		$Post->dataForAfterSave = [
			['author_id' => 1, 'title' => 'Inner Post', 'body' => $db->expression('PDO_EXCEPTION()')],
		];

		try {
			$Post->saveMany($data);
			$this->fail('No exception thrown');
		} catch(Exception $e) {
		}
	}

/**
 * Test that transaction behaves correctly on nested saveAssociated calls.
 *
 * @return void
 */
	public function testTransactionOnNestedSaveAssociated() {
		$this->loadFixtures('Author', 'Post');

		$Author = new TestAuthor();
		$Author->getEventManager()->attach([$this, 'nestedSaveAssociated'], 'Model.afterSave');

		// begin -> [ begin -> commit ] -> commit
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->exactly(2))->method('begin')->will($this->returnValue(true));
		$db->expects($this->exactly(2))->method('commit');
		$db->expects($this->never())->method('rollback');
		$Author->setDataSourceObject($db);
		$Author->Post->setDataSourceObject($db);

		$data = [
			'Author' => ['user' => 'outer'],
			'Post' => [
				['title' => 'Outer Post'],
			]
		];
		$Author->dataForAfterSave = [
			'Author' => ['user' => 'inner'],
			'Post' => [
				['title' => 'Inner Post'],
			]
		];
		$this->assertTrue($Author->saveAssociated($data));

		// begin -> [  begin(false) ] -> commit
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->at(0))->method('begin')->will($this->returnValue(true));
		$db->expects($this->at(1))->method('begin')->will($this->returnValue(false));
		$db->expects($this->once())->method('commit');
		$db->expects($this->never())->method('rollback');
		$Author->setDataSourceObject($db);
		$Author->Post->setDataSourceObject($db);
		$data = [
			'Author' => ['user' => 'outer'],
			'Post' => [
				['title' => 'Outer Post'],
			]
		];
		$Author->dataForAfterSave = [
			'Author' => ['user' => 'inner'],
			'Post' => [
				['title' => 'Inner Post'],
			]
		];
		$this->assertTrue($Author->saveAssociated($data));

		// begin -> [ begin -> rollback ] -> rollback
		$db = $this->_getMockDboSource(['begin', 'commit', 'rollback']);
		$db->expects($this->exactly(2))->method('begin')->will($this->returnValue(true));
		$db->expects($this->never())->method('commit');
		$db->expects($this->exactly(2))->method('rollback');
		$Author->setDataSourceObject($db);
		$Author->Post->setDataSourceObject($db);
		$data = [
			'Author' => ['user' => 'outer'],
			'Post' => [
				['title' => 'Outer Post'],
			]
		];
		$Author->dataForAfterSave = [
			'Author' => ['user' => 'inner', 'password' => $db->expression('PDO_EXCEPTION()')],
			'Post' => [
				['title' => 'Inner Post'],
			]
		];

		try {
			$Author->saveAssociated($data);
			$this->fail('No exception thrown');
		} catch(Exception $e) {
		}
	}

/**
 * A callback for testing nested saveMany.
 *
 * @param CakeEvent $event containing the Model
 * @return void
 */
	public function nestedSaveMany($event) {
		$Model = $event->subject;
		$Model->saveMany($Model->dataForAfterSave, ['callbacks' => false]);
	}

/**
 * A callback for testing nested saveAssociated.
 *
 * @param CakeEvent $event containing the Model
 * @return void
 */
	public function nestedSaveAssociated($event) {
		$Model = $event->subject;
		$Model->saveAssociated($Model->dataForAfterSave, ['callbacks' => false]);
	}
}
