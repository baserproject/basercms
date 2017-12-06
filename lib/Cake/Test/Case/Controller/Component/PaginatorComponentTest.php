<?php
/**
 * PaginatorComponentTest file
 *
 * Series of tests for paginator component.
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
 * @package       Cake.Test.Case.Controller.Component
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');
App::uses('PaginatorComponent', 'Controller/Component');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');

/**
 * PaginatorTestController class
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class PaginatorTestController extends Controller {

/**
 * components property
 *
 * @var array
 */
	public $components = ['Paginator'];
}

/**
 * PaginatorControllerPost class
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class PaginatorControllerPost extends CakeTestModel {

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'posts';

/**
 * invalidFields property
 *
 * @var array
 */
	public $invalidFields = ['name' => 'error_msg'];

/**
 * lastQueries property
 *
 * @var array
 */
	public $lastQueries = [];

/**
 * belongsTo property
 *
 * @var array
 */
	public $belongsTo = ['PaginatorAuthor' => ['foreignKey' => 'author_id']];

/**
 * beforeFind method
 *
 * @param mixed $query
 * @return void
 */
	public function beforeFind($query) {
		array_unshift($this->lastQueries, $query);
	}

/**
 * find method
 *
 * @param mixed $type
 * @param array $options
 * @return void
 */
	public function find($conditions = null, $fields = [], $order = null, $recursive = null) {
		if ($conditions === 'popular') {
			$conditions = [$this->name . '.' . $this->primaryKey . ' > ' => '1'];
			$options = Hash::merge($fields, compact('conditions'));
			return parent::find('all', $options);
		}
		return parent::find($conditions, $fields);
	}

}

/**
 * ControllerPaginateModel class
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class ControllerPaginateModel extends CakeTestModel {

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'comments';

/**
 * paginate method
 *
 * @return bool
 */
	public function paginate($conditions, $fields, $order, $limit, $page, $recursive, $extra) {
		$this->extra = $extra;
		return true;
	}

/**
 * paginateCount
 *
 * @return void
 */
	public function paginateCount($conditions, $recursive, $extra) {
		$this->extraCount = $extra;
	}

}

/**
 * PaginatorControllerComment class
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class PaginatorControllerComment extends CakeTestModel {

/**
 * name property
 *
 * @var string
 */
	public $name = 'Comment';

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'comments';

/**
 * alias property
 *
 * @var string
 */
	public $alias = 'PaginatorControllerComment';
}

/**
 * PaginatorAuthor class
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class PaginatorAuthor extends CakeTestModel {

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'authors';

/**
 * alias property
 *
 * @var string
 */
	public $virtualFields = [
		'joined_offset' => 'PaginatorAuthor.id + 1'
	];

}

/**
 * PaginatorCustomPost class
 *
 * @package       Cake.Test.Case.Controller.Component
 */
class PaginatorCustomPost extends CakeTestModel {

/**
 * useTable property
 *
 * @var string
 */
	public $useTable = 'posts';

/**
 * belongsTo property
 *
 * @var string
 */
	public $belongsTo = ['Author'];

/**
 * findMethods property
 *
 * @var array
 */
	public $findMethods = [
		'published' => true,
		'totals' => true,
		'totalsOperation' => true
	];

/**
 * _findPublished custom find
 *
 * @return array
 */
	protected function _findPublished($state, $query, $results = []) {
		if ($state === 'before') {
			$query['conditions']['published'] = 'Y';
			return $query;
		}
		return $results;
	}

/**
 * _findTotals custom find
 *
 * @return array
 */
	protected function _findTotals($state, $query, $results = []) {
		if ($state === 'before') {
			$query['fields'] = ['author_id'];
			$this->virtualFields['total_posts'] = "COUNT({$this->alias}.id)";
			$query['fields'][] = 'total_posts';
			$query['group'] = ['author_id'];
			$query['order'] = ['author_id' => 'ASC'];
			return $query;
		}
		$this->virtualFields = [];
		return $results;
	}

/**
 * _findTotalsOperation custom find
 *
 * @return array
 */
	protected function _findTotalsOperation($state, $query, $results = []) {
		if ($state === 'before') {
			if (!empty($query['operation']) && $query['operation'] === 'count') {
				unset($query['limit']);
				$query['recursive'] = -1;
				$query['fields'] = ['COUNT(DISTINCT author_id) AS count'];
				return $query;
			}
			$query['recursive'] = 0;
			$query['callbacks'] = 'before';
			$query['fields'] = ['author_id', 'Author.user'];
			$this->virtualFields['total_posts'] = "COUNT({$this->alias}.id)";
			$query['fields'][] = 'total_posts';
			$query['group'] = ['author_id', 'Author.user'];
			$query['order'] = ['author_id' => 'ASC'];
			return $query;
		}
		$this->virtualFields = [];
		return $results;
	}

}

class PaginatorComponentTest extends CakeTestCase {

/**
 * fixtures property
 *
 * @var array
 */
	public $fixtures = ['core.post', 'core.comment', 'core.author'];

/**
 * setup
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->request = new CakeRequest('controller_posts/index');
		$this->request->params['pass'] = $this->request->params['named'] = [];
		$this->Controller = new Controller($this->request);
		$this->Paginator = new PaginatorComponent($this->getMock('ComponentCollection'), []);
		$this->Paginator->Controller = $this->Controller;
		$this->Controller->Post = $this->getMock('Model');
		$this->Controller->Post->alias = 'Post';
	}

/**
 * testPaginate method
 *
 * @return void
 */
	public function testPaginate() {
		$Controller = new PaginatorTestController($this->request);
		$Controller->uses = ['PaginatorControllerPost', 'PaginatorControllerComment'];
		$Controller->request->params['pass'] = ['1'];
		$Controller->request->query = [];
		$Controller->constructClasses();

		$Controller->PaginatorControllerPost->order = null;

		$Controller->Paginator->settings = [
			'order' => ['PaginatorControllerComment.id' => 'ASC']
		];
		$results = Hash::extract($Controller->Paginator->paginate('PaginatorControllerComment'), '{n}.PaginatorControllerComment.id');
		$this->assertEquals([1, 2, 3, 4, 5, 6], $results);

		$Controller->Paginator->settings = [
			'order' => ['PaginatorControllerPost.id' => 'ASC']
		];
		$results = Hash::extract($Controller->Paginator->paginate('PaginatorControllerPost'), '{n}.PaginatorControllerPost.id');
		$this->assertEquals([1, 2, 3], $results);

		$Controller->modelClass = null;

		$Controller->uses[0] = 'Plugin.PaginatorControllerPost';
		$results = Hash::extract($Controller->Paginator->paginate(), '{n}.PaginatorControllerPost.id');
		$this->assertEquals([1, 2, 3], $results);

		$Controller->request->params['named'] = ['page' => '-1'];
		$results = Hash::extract($Controller->Paginator->paginate('PaginatorControllerPost'), '{n}.PaginatorControllerPost.id');
		$this->assertEquals(1, $Controller->params['paging']['PaginatorControllerPost']['page']);
		$this->assertEquals([1, 2, 3], $results);

		$Controller->request->params['named'] = ['sort' => 'PaginatorControllerPost.id', 'direction' => 'asc'];
		$results = Hash::extract($Controller->Paginator->paginate('PaginatorControllerPost'), '{n}.PaginatorControllerPost.id');
		$this->assertEquals(1, $Controller->params['paging']['PaginatorControllerPost']['page']);
		$this->assertEquals([1, 2, 3], $results);

		$Controller->request->params['named'] = ['sort' => 'PaginatorControllerPost.id', 'direction' => 'desc'];
		$results = Hash::extract($Controller->Paginator->paginate('PaginatorControllerPost'), '{n}.PaginatorControllerPost.id');
		$this->assertEquals(1, $Controller->params['paging']['PaginatorControllerPost']['page']);
		$this->assertEquals([3, 2, 1], $results);

		$Controller->request->params['named'] = ['sort' => 'id', 'direction' => 'desc'];
		$results = Hash::extract($Controller->Paginator->paginate('PaginatorControllerPost'), '{n}.PaginatorControllerPost.id');
		$this->assertEquals(1, $Controller->params['paging']['PaginatorControllerPost']['page']);
		$this->assertEquals([3, 2, 1], $results);

		$Controller->request->params['named'] = ['sort' => 'NotExisting.field', 'direction' => 'desc', 'limit' => 2];
		$Controller->Paginator->paginate('PaginatorControllerPost');
		$this->assertEquals(1, $Controller->params['paging']['PaginatorControllerPost']['page']);
		$this->assertEquals([], $Controller->PaginatorControllerPost->lastQueries[1]['order'], 'no order should be set.');

		$Controller->request->params['named'] = [
			'sort' => 'PaginatorControllerPost.author_id', 'direction' => 'allYourBase'
		];
		$results = Hash::extract($Controller->Paginator->paginate('PaginatorControllerPost'), '{n}.PaginatorControllerPost.id');
		$this->assertEquals(['PaginatorControllerPost.author_id' => 'asc'], $Controller->PaginatorControllerPost->lastQueries[0]['order']);
		$this->assertEquals([1, 3, 2], $results);

		$Controller->request->params['named'] = [];
		$Controller->Paginator->settings = ['limit' => 0, 'maxLimit' => 10, 'paramType' => 'named'];
		$Controller->Paginator->paginate('PaginatorControllerPost');
		$this->assertSame(1, $Controller->params['paging']['PaginatorControllerPost']['page']);
		$this->assertSame($Controller->params['paging']['PaginatorControllerPost']['pageCount'], 3);
		$this->assertFalse($Controller->params['paging']['PaginatorControllerPost']['prevPage']);
		$this->assertTrue($Controller->params['paging']['PaginatorControllerPost']['nextPage']);

		$Controller->request->params['named'] = [];
		$Controller->Paginator->settings = ['limit' => 'garbage!', 'maxLimit' => 10, 'paramType' => 'named'];
		$Controller->Paginator->paginate('PaginatorControllerPost');
		$this->assertSame(1, $Controller->params['paging']['PaginatorControllerPost']['page']);
		$this->assertSame($Controller->params['paging']['PaginatorControllerPost']['pageCount'], 3);
		$this->assertFalse($Controller->params['paging']['PaginatorControllerPost']['prevPage']);
		$this->assertTrue($Controller->params['paging']['PaginatorControllerPost']['nextPage']);

		$Controller->request->params['named'] = [];
		$Controller->Paginator->settings = ['limit' => '-1', 'maxLimit' => 10, 'paramType' => 'named'];
		$Controller->Paginator->paginate('PaginatorControllerPost');

		$this->assertSame($Controller->params['paging']['PaginatorControllerPost']['limit'], 1);
		$this->assertSame(1, $Controller->params['paging']['PaginatorControllerPost']['page']);
		$this->assertSame($Controller->params['paging']['PaginatorControllerPost']['pageCount'], 3);
		$this->assertFalse($Controller->params['paging']['PaginatorControllerPost']['prevPage']);
		$this->assertTrue($Controller->params['paging']['PaginatorControllerPost']['nextPage']);

		$Controller->Paginator->settings = ['conditions' => ['PaginatorAuthor.user' => 'mariano']];
		$Controller->Paginator->paginate('PaginatorControllerPost');

		$this->assertSame(2, $Controller->params['paging']['PaginatorControllerPost']['count']);
	}

/**
 * Test that non-numeric values are rejected for page, and limit
 *
 * @return void
 */
	public function testPageParamCasting() {
		$this->Controller->Post->expects($this->at(0))
			->method('hasMethod')
			->with('paginate')
			->will($this->returnValue(false));

		$this->Controller->Post->expects($this->at(1))
			->method('find')
			->will($this->returnValue(['stuff']));

		$this->Controller->Post->expects($this->at(2))
			->method('hasMethod')
			->with('paginateCount')
			->will($this->returnValue(false));

		$this->Controller->Post->expects($this->at(3))
			->method('find')
			->will($this->returnValue(2));

		$this->request->params['named'] = ['page' => '1 " onclick="alert(\'xss\');">'];
		$this->Paginator->settings = ['limit' => 1, 'maxLimit' => 10, 'paramType' => 'named'];
		$this->Paginator->paginate('Post');
		$this->assertSame(1, $this->request->params['paging']['Post']['page'], 'XSS exploit opened');
	}

/**
 * testPaginateExtraParams method
 *
 * @return void
 */
	public function testPaginateExtraParams() {
		$Controller = new PaginatorTestController($this->request);

		$Controller->uses = ['PaginatorControllerPost', 'PaginatorControllerComment'];
		$Controller->request->params['pass'] = ['1'];
		$Controller->params['url'] = [];
		$Controller->constructClasses();

		$Controller->request->params['named'] = ['page' => '-1', 'contain' => ['PaginatorControllerComment']];
		$Controller->Paginator->settings = [
			'order' => ['PaginatorControllerPost.id' => 'ASC']
		];
		$result = $Controller->Paginator->paginate('PaginatorControllerPost');
		$this->assertEquals(1, $Controller->params['paging']['PaginatorControllerPost']['page']);
		$this->assertEquals([1, 2, 3], Hash::extract($result, '{n}.PaginatorControllerPost.id'));
		$this->assertTrue(!isset($Controller->PaginatorControllerPost->lastQueries[1]['contain']));

		$Controller->Paginator->settings = [
			'order' => ['PaginatorControllerPost.author_id']
		];
		$result = $Controller->Paginator->paginate('PaginatorControllerPost');
		$this->assertEquals(1, $Controller->params['paging']['PaginatorControllerPost']['page']);
		$this->assertEquals([1, 3, 2], Hash::extract($result, '{n}.PaginatorControllerPost.id'));

		$Controller->request->params['named'] = ['page' => '-1'];
		$Controller->Paginator->settings = [
			'PaginatorControllerPost' => [
				'contain' => ['PaginatorControllerComment'],
				'maxLimit' => 10,
				'paramType' => 'named',
				'order' => ['PaginatorControllerPost.id' => 'ASC']
			],
		];
		$result = $Controller->Paginator->paginate('PaginatorControllerPost');
		$this->assertEquals(1, $Controller->params['paging']['PaginatorControllerPost']['page']);
		$this->assertEquals([1, 2, 3], Hash::extract($result, '{n}.PaginatorControllerPost.id'));
		$this->assertTrue(isset($Controller->PaginatorControllerPost->lastQueries[0]['contain']));

		$Controller->Paginator->settings = [
			'PaginatorControllerPost' => [
				'popular', 'fields' => ['id', 'title'], 'maxLimit' => 10, 'paramType' => 'named'
			],
		];
		$result = $Controller->Paginator->paginate('PaginatorControllerPost');
		$this->assertEquals([2, 3], Hash::extract($result, '{n}.PaginatorControllerPost.id'));
		$this->assertEquals(['PaginatorControllerPost.id > ' => '1'], $Controller->PaginatorControllerPost->lastQueries[0]['conditions']);

		$Controller->request->params['named'] = ['limit' => 12];
		$Controller->Paginator->settings = ['limit' => 30, 'maxLimit' => 100, 'paramType' => 'named'];
		$Controller->Paginator->paginate('PaginatorControllerPost');
		$paging = $Controller->params['paging']['PaginatorControllerPost'];

		$this->assertEquals(12, $Controller->PaginatorControllerPost->lastQueries[0]['limit']);
		$this->assertEquals(12, $paging['options']['limit']);

		$Controller = new PaginatorTestController($this->request);
		$Controller->uses = ['ControllerPaginateModel'];
		$Controller->request->query = [];
		$Controller->constructClasses();
		$Controller->Paginator->settings = [
			'ControllerPaginateModel' => [
				'contain' => ['ControllerPaginateModel'],
				'group' => 'Comment.author_id',
				'maxLimit' => 10,
				'paramType' => 'named'
			]
		];
		$Controller->Paginator->paginate('ControllerPaginateModel');
		$expected = [
			'contain' => ['ControllerPaginateModel'],
			'group' => 'Comment.author_id',
			'maxLimit' => 10,
			'paramType' => 'named'
		];
		$this->assertEquals($expected, $Controller->ControllerPaginateModel->extra);
		$this->assertEquals($expected, $Controller->ControllerPaginateModel->extraCount);

		$Controller->Paginator->settings = [
			'ControllerPaginateModel' => [
				'foo', 'contain' => ['ControllerPaginateModel'],
				'group' => 'Comment.author_id',
				'maxLimit' => 10,
				'paramType' => 'named'
			]
		];
		$Controller->Paginator->paginate('ControllerPaginateModel');
		$expected = [
			'contain' => ['ControllerPaginateModel'],
			'group' => 'Comment.author_id',
			'type' => 'foo',
			'maxLimit' => 10,
			'paramType' => 'named'
		];
		$this->assertEquals($expected, $Controller->ControllerPaginateModel->extra);
		$this->assertEquals($expected, $Controller->ControllerPaginateModel->extraCount);
	}

/**
 * Test that special paginate types are called and that the type param doesn't leak out into defaults or options.
 *
 * @return void
 */
	public function testPaginateSpecialType() {
		$Controller = new PaginatorTestController($this->request);
		$Controller->uses = ['PaginatorControllerPost', 'PaginatorControllerComment'];
		$Controller->request->params['pass'][] = '1';
		$Controller->params['url'] = [];
		$Controller->constructClasses();

		$Controller->Paginator->settings = [
			'PaginatorControllerPost' => [
				'popular',
				'fields' => ['id', 'title'],
				'maxLimit' => 10,
				'paramType' => 'named'
			]
		];
		$result = $Controller->Paginator->paginate('PaginatorControllerPost');

		$this->assertEquals([2, 3], Hash::extract($result, '{n}.PaginatorControllerPost.id'));
		$this->assertEquals(
			$Controller->PaginatorControllerPost->lastQueries[0]['conditions'],
			['PaginatorControllerPost.id > ' => '1']
		);
		$this->assertFalse(isset($Controller->params['paging']['PaginatorControllerPost']['options'][0]));
	}

/**
 * testDefaultPaginateParams method
 *
 * @return void
 */
	public function testDefaultPaginateParams() {
		$Controller = new PaginatorTestController($this->request);
		$Controller->modelClass = 'PaginatorControllerPost';
		$Controller->params['url'] = [];
		$Controller->constructClasses();
		$Controller->Paginator->settings = [
			'order' => 'PaginatorControllerPost.id DESC',
			'maxLimit' => 10,
			'paramType' => 'named'
		];
		$results = Hash::extract($Controller->Paginator->paginate('PaginatorControllerPost'), '{n}.PaginatorControllerPost.id');
		$this->assertEquals('PaginatorControllerPost.id DESC', $Controller->params['paging']['PaginatorControllerPost']['order']);
		$this->assertEquals([3, 2, 1], $results);
	}

/**
 * test paginate() and model default order
 *
 * @return void
 */
	public function testPaginateOrderModelDefault() {
		$Controller = new PaginatorTestController($this->request);
		$Controller->uses = ['PaginatorControllerPost'];
		$Controller->params['url'] = [];
		$Controller->constructClasses();
		$Controller->PaginatorControllerPost->order = [
			$Controller->PaginatorControllerPost->alias . '.created' => 'desc'
		];

		$Controller->Paginator->settings = [
			'fields' => ['id', 'title', 'created'],
			'maxLimit' => 10,
			'paramType' => 'named'
		];
		$result = $Controller->Paginator->paginate('PaginatorControllerPost');
		$expected = ['2007-03-18 10:43:23', '2007-03-18 10:41:23', '2007-03-18 10:39:23'];
		$this->assertEquals($expected, Hash::extract($result, '{n}.PaginatorControllerPost.created'));
		$this->assertEquals(
			$Controller->PaginatorControllerPost->order,
			$Controller->request->paging['PaginatorControllerPost']['options']['order']
		);

		$Controller->PaginatorControllerPost->order = ['PaginatorControllerPost.id'];
		$result = $Controller->Paginator->validateSort($Controller->PaginatorControllerPost, []);
		$this->assertEquals(['PaginatorControllerPost.id' => 'asc'], $result['order']);

		$Controller->PaginatorControllerPost->order = 'PaginatorControllerPost.id';
		$result = $Controller->Paginator->validateSort($Controller->PaginatorControllerPost, []);
		$this->assertArrayNotHasKey('order', $result);

		$Controller->PaginatorControllerPost->order = [
			'PaginatorControllerPost.id',
			'PaginatorControllerPost.created' => 'asc'
		];
		$result = $Controller->Paginator->validateSort($Controller->PaginatorControllerPost, []);
		$expected = [
			'PaginatorControllerPost.id' => 'asc',
			'PaginatorControllerPost.created' => 'asc'
		];
		$this->assertEquals($expected, $result['order']);
	}

/**
 * test paginate() and virtualField interactions
 *
 * @return void
 */
	public function testPaginateOrderVirtualField() {
		$Controller = new PaginatorTestController($this->request);
		$Controller->uses = ['PaginatorControllerPost', 'PaginatorControllerComment'];
		$Controller->params['url'] = [];
		$Controller->constructClasses();
		$Controller->PaginatorControllerPost->virtualFields = [
			'offset_test' => 'PaginatorControllerPost.id + 1'
		];

		$Controller->Paginator->settings = [
			'fields' => ['id', 'title', 'offset_test'],
			'order' => ['offset_test' => 'DESC'],
			'maxLimit' => 10,
			'paramType' => 'named'
		];
		$result = $Controller->Paginator->paginate('PaginatorControllerPost');
		$this->assertEquals([4, 3, 2], Hash::extract($result, '{n}.PaginatorControllerPost.offset_test'));

		$Controller->request->params['named'] = ['sort' => 'offset_test', 'direction' => 'asc'];
		$result = $Controller->Paginator->paginate('PaginatorControllerPost');
		$this->assertEquals([2, 3, 4], Hash::extract($result, '{n}.PaginatorControllerPost.offset_test'));
	}

/**
 * test paginate() and virtualField on joined model
 *
 * @return void
 */
	public function testPaginateOrderVirtualFieldJoinedModel() {
		$Controller = new PaginatorTestController($this->request);
		$Controller->uses = ['PaginatorControllerPost'];
		$Controller->params['url'] = [];
		$Controller->constructClasses();
		$Controller->PaginatorControllerPost->recursive = 0;
		$Controller->Paginator->settings = [
			'order' => ['PaginatorAuthor.joined_offset' => 'DESC'],
			'maxLimit' => 10,
			'paramType' => 'named'
		];
		$result = $Controller->Paginator->paginate('PaginatorControllerPost');
		$this->assertEquals([4, 2, 2], Hash::extract($result, '{n}.PaginatorAuthor.joined_offset'));

		$Controller->request->params['named'] = ['sort' => 'PaginatorAuthor.joined_offset', 'direction' => 'asc'];
		$result = $Controller->Paginator->paginate('PaginatorControllerPost');
		$this->assertEquals([2, 2, 4], Hash::extract($result, '{n}.PaginatorAuthor.joined_offset'));
	}

/**
 * Tests for missing models
 *
 * @expectedException MissingModelException
 * @return void
 */
	public function testPaginateMissingModel() {
		$Controller = new PaginatorTestController($this->request);
		$Controller->constructClasses();
		$Controller->Paginator->paginate('MissingModel');
	}

/**
 * test that option merging prefers specific models
 *
 * @return void
 */
	public function testMergeOptionsModelSpecific() {
		$this->Paginator->settings = [
			'page' => 1,
			'limit' => 20,
			'maxLimit' => 100,
			'paramType' => 'named',
			'Post' => [
				'page' => 1,
				'limit' => 10,
				'maxLimit' => 50,
				'paramType' => 'named',
			]
		];
		$result = $this->Paginator->mergeOptions('Silly');
		$this->assertEquals($this->Paginator->settings, $result);

		$result = $this->Paginator->mergeOptions('Post');
		$expected = ['page' => 1, 'limit' => 10, 'paramType' => 'named', 'maxLimit' => 50];
		$this->assertEquals($expected, $result);
	}

/**
 * test mergeOptions with named params.
 *
 * @return void
 */
	public function testMergeOptionsNamedParams() {
		$this->request->params['named'] = [
			'page' => 10,
			'limit' => 10
		];
		$this->Paginator->settings = [
			'page' => 1,
			'limit' => 20,
			'maxLimit' => 100,
			'paramType' => 'named',
		];
		$result = $this->Paginator->mergeOptions('Post');
		$expected = ['page' => 10, 'limit' => 10, 'maxLimit' => 100, 'paramType' => 'named'];
		$this->assertEquals($expected, $result);
	}

/**
 * test mergeOptions with customFind key
 *
 * @return void
 */
	public function testMergeOptionsCustomFindKey() {
		$this->request->params['named'] = [
			'page' => 10,
			'limit' => 10
		];
		$this->Paginator->settings = [
			'page' => 1,
			'limit' => 20,
			'maxLimit' => 100,
			'paramType' => 'named',
			'findType' => 'myCustomFind'
		];
		$result = $this->Paginator->mergeOptions('Post');
		$expected = ['page' => 10, 'limit' => 10, 'maxLimit' => 100, 'paramType' => 'named', 'findType' => 'myCustomFind'];
		$this->assertEquals($expected, $result);
	}

/**
 * test merging options from the querystring.
 *
 * @return void
 */
	public function testMergeOptionsQueryString() {
		$this->request->params['named'] = [
			'page' => 10,
			'limit' => 10
		];
		$this->request->query = [
			'page' => 99,
			'limit' => 75
		];
		$this->Paginator->settings = [
			'page' => 1,
			'limit' => 20,
			'maxLimit' => 100,
			'paramType' => 'querystring',
		];
		$result = $this->Paginator->mergeOptions('Post');
		$expected = ['page' => 99, 'limit' => 75, 'maxLimit' => 100, 'paramType' => 'querystring'];
		$this->assertEquals($expected, $result);
	}

/**
 * test that the default whitelist doesn't let people screw with things they should not be allowed to.
 *
 * @return void
 */
	public function testMergeOptionsDefaultWhiteList() {
		$this->request->params['named'] = [
			'page' => 10,
			'limit' => 10,
			'fields' => ['bad.stuff'],
			'recursive' => 1000,
			'conditions' => ['bad.stuff'],
			'contain' => ['bad']
		];
		$this->Paginator->settings = [
			'page' => 1,
			'limit' => 20,
			'maxLimit' => 100,
			'paramType' => 'named',
		];
		$result = $this->Paginator->mergeOptions('Post');
		$expected = ['page' => 10, 'limit' => 10, 'maxLimit' => 100, 'paramType' => 'named'];
		$this->assertEquals($expected, $result);
	}

/**
 * test that modifying the whitelist works.
 *
 * @return void
 */
	public function testMergeOptionsExtraWhitelist() {
		$this->request->params['named'] = [
			'page' => 10,
			'limit' => 10,
			'fields' => ['bad.stuff'],
			'recursive' => 1000,
			'conditions' => ['bad.stuff'],
			'contain' => ['bad']
		];
		$this->Paginator->settings = [
			'page' => 1,
			'limit' => 20,
			'maxLimit' => 100,
			'paramType' => 'named',
		];
		$this->Paginator->whitelist[] = 'fields';
		$result = $this->Paginator->mergeOptions('Post');
		$expected = [
			'page' => 10, 'limit' => 10, 'maxLimit' => 100, 'paramType' => 'named', 'fields' => ['bad.stuff']
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test mergeOptions with limit > maxLimit in code.
 *
 * @return void
 */
	public function testMergeOptionsMaxLimit() {
		$this->Paginator->settings = [
			'limit' => 200,
			'paramType' => 'named',
		];
		$result = $this->Paginator->mergeOptions('Post');
		$expected = ['page' => 1, 'limit' => 200, 'maxLimit' => 100, 'paramType' => 'named'];
		$this->assertEquals($expected, $result);

		$this->Paginator->settings = [
			'maxLimit' => 10,
			'paramType' => 'named',
		];
		$result = $this->Paginator->mergeOptions('Post');
		$expected = ['page' => 1, 'limit' => 20, 'maxLimit' => 10, 'paramType' => 'named'];
		$this->assertEquals($expected, $result);

		$this->request->params['named'] = [
			'limit' => 500
		];
		$this->Paginator->settings = [
			'limit' => 150,
			'paramType' => 'named',
		];
		$result = $this->Paginator->mergeOptions('Post');
		$expected = ['page' => 1, 'limit' => 500, 'maxLimit' => 100, 'paramType' => 'named'];
		$this->assertEquals($expected, $result);
	}

/**
 * test that invalid directions are ignored.
 *
 * @return void
 */
	public function testValidateSortInvalidDirection() {
		$model = $this->getMock('Model');
		$model->alias = 'model';
		$model->expects($this->any())->method('hasField')->will($this->returnValue(true));

		$options = ['sort' => 'something', 'direction' => 'boogers'];
		$result = $this->Paginator->validateSort($model, $options);

		$this->assertEquals('asc', $result['order']['model.something']);
	}

/**
 * Test that a really large page number gets clamped to the max page size.
 *
 * @expectedException NotFoundException
 * @return void
 */
	public function testOutOfRangePageNumberGetsClamped() {
		$Controller = new PaginatorTestController($this->request);
		$Controller->uses = ['PaginatorControllerPost'];
		$Controller->params['named'] = [
			'page' => 3000,
		];
		$Controller->constructClasses();
		$Controller->PaginatorControllerPost->recursive = 0;
		$Controller->Paginator->paginate('PaginatorControllerPost');
	}

/**
 * Test that a really REALLY large page number gets clamped to the max page size.
 *
 * @expectedException NotFoundException
 * @return void
 */
	public function testOutOfVeryBigPageNumberGetsClamped() {
		$Controller = new PaginatorTestController($this->request);
		$Controller->uses = ['PaginatorControllerPost'];
		$Controller->params['named'] = [
			'page' => '3000000000000000000000000',
		];
		$Controller->constructClasses();
		$Controller->PaginatorControllerPost->recursive = 0;
		$Controller->Paginator->paginate('PaginatorControllerPost');
	}

/**
 * testOutOfRangePageNumberAndPageCountZero
 *
 * @return void
 */
	public function testOutOfRangePageNumberAndPageCountZero() {
		$Controller = new PaginatorTestController($this->request);
		$Controller->uses = ['PaginatorControllerPost'];
		$Controller->params['named'] = [
			'page' => '3000',
		];
		$Controller->constructClasses();
		$Controller->PaginatorControllerPost->recursive = 0;
		$Controller->paginate = [
			'conditions' => ['PaginatorControllerPost.id >' => 100]
		];

		try {
			$Controller->Paginator->paginate('PaginatorControllerPost');
			$this->fail();
		} catch (NotFoundException $e) {
			$this->assertEquals(
				1,
				$Controller->request->params['paging']['PaginatorControllerPost']['page'],
				'Page number should not be 0'
			);
		}
	}

/**
 * test that fields not in whitelist won't be part of order conditions.
 *
 * @return void
 */
	public function testValidateSortWhitelistFailure() {
		$model = $this->getMock('Model');
		$model->alias = 'model';
		$model->expects($this->any())->method('hasField')->will($this->returnValue(true));

		$options = ['sort' => 'body', 'direction' => 'asc'];
		$result = $this->Paginator->validateSort($model, $options, ['title', 'id']);

		$this->assertNull($result['order']);
	}

/**
 * test that fields in the whitelist are not validated
 *
 * @return void
 */
	public function testValidateSortWhitelistTrusted() {
		$model = $this->getMock('Model');
		$model->alias = 'model';
		$model->expects($this->never())->method('hasField');

		$options = ['sort' => 'body', 'direction' => 'asc'];
		$result = $this->Paginator->validateSort($model, $options, ['body']);

		$expected = ['body' => 'asc'];
		$this->assertEquals($expected, $result['order']);
	}

/**
 * test that virtual fields work.
 *
 * @return void
 */
	public function testValidateSortVirtualField() {
		$model = $this->getMock('Model');
		$model->alias = 'model';

		$model->expects($this->at(0))
			->method('hasField')
			->with('something')
			->will($this->returnValue(false));

		$model->expects($this->at(1))
			->method('hasField')
			->with('something', true)
			->will($this->returnValue(true));

		$options = ['sort' => 'something', 'direction' => 'desc'];
		$result = $this->Paginator->validateSort($model, $options);

		$this->assertEquals('desc', $result['order']['something']);
	}

/**
 * test that sorting fields is alias specific
 *
 * @return void
 */
	public function testValidateSortSharedFields() {
		$model = $this->getMock('Model');
		$model->alias = 'Parent';
		$model->Child = $this->getMock('Model');
		$model->Child->alias = 'Child';

		$model->expects($this->never())
			->method('hasField');

		$model->Child->expects($this->at(0))
			->method('hasField')
			->with('something')
			->will($this->returnValue(true));

		$options = ['sort' => 'Child.something', 'direction' => 'desc'];
		$result = $this->Paginator->validateSort($model, $options);

		$this->assertEquals('desc', $result['order']['Child.something']);
	}
/**
 * test that multiple sort works.
 *
 * @return void
 */
	public function testValidateSortMultiple() {
		$model = $this->getMock('Model');
		$model->alias = 'model';
		$model->expects($this->any())->method('hasField')->will($this->returnValue(true));

		$options = [
			'order' => [
				'author_id' => 'asc',
				'title' => 'asc'
			]
		];
		$result = $this->Paginator->validateSort($model, $options);
		$expected = [
			'model.author_id' => 'asc',
			'model.title' => 'asc'
		];

		$this->assertEquals($expected, $result['order']);
	}

/**
 * Test that no sort doesn't trigger an error.
 *
 * @return void
 */
	public function testValidateSortNoSort() {
		$model = $this->getMock('Model');
		$model->alias = 'model';
		$model->expects($this->any())->method('hasField')->will($this->returnValue(true));

		$options = ['direction' => 'asc'];
		$result = $this->Paginator->validateSort($model, $options, ['title', 'id']);
		$this->assertFalse(isset($result['order']));

		$options = ['order' => 'invalid desc'];
		$result = $this->Paginator->validateSort($model, $options, ['title', 'id']);

		$this->assertEquals($options['order'], $result['order']);
	}

/**
 * Test sorting with incorrect aliases on valid fields.
 *
 * @return void
 */
	public function testValidateSortInvalidAlias() {
		$model = $this->getMock('Model');
		$model->alias = 'Model';
		$model->expects($this->any())->method('hasField')->will($this->returnValue(true));

		$options = ['sort' => 'Derp.id'];
		$result = $this->Paginator->validateSort($model, $options);
		$this->assertEquals([], $result['order']);
	}

/**
 * test that maxLimit is respected
 *
 * @return void
 */
	public function testCheckLimit() {
		$result = $this->Paginator->checkLimit(['limit' => 1000000, 'maxLimit' => 100]);
		$this->assertEquals(100, $result['limit']);

		$result = $this->Paginator->checkLimit(['limit' => 'sheep!', 'maxLimit' => 100]);
		$this->assertEquals(1, $result['limit']);

		$result = $this->Paginator->checkLimit(['limit' => '-1', 'maxLimit' => 100]);
		$this->assertEquals(1, $result['limit']);

		$result = $this->Paginator->checkLimit(['limit' => null, 'maxLimit' => 100]);
		$this->assertEquals(1, $result['limit']);

		$result = $this->Paginator->checkLimit(['limit' => 0, 'maxLimit' => 100]);
		$this->assertEquals(1, $result['limit']);
	}

/**
 * testPaginateMaxLimit
 *
 * @return void
 */
	public function testPaginateMaxLimit() {
		$Controller = new Controller($this->request);

		$Controller->uses = ['PaginatorControllerPost', 'ControllerComment'];
		$Controller->request->params['pass'][] = '1';
		$Controller->constructClasses();

		$Controller->request->params['named'] = [
			'contain' => ['ControllerComment'], 'limit' => '1000'
		];
		$Controller->paginate('PaginatorControllerPost');
		$this->assertEquals(100, $Controller->params['paging']['PaginatorControllerPost']['options']['limit']);

		$Controller->request->params['named'] = [
			'contain' => ['ControllerComment'], 'limit' => '1000', 'maxLimit' => 1000
		];
		$Controller->paginate('PaginatorControllerPost');
		$this->assertEquals(100, $Controller->params['paging']['PaginatorControllerPost']['options']['limit']);

		$Controller->request->params['named'] = ['contain' => ['ControllerComment'], 'limit' => '10'];
		$Controller->paginate('PaginatorControllerPost');
		$this->assertEquals(10, $Controller->params['paging']['PaginatorControllerPost']['options']['limit']);

		$Controller->request->params['named'] = ['contain' => ['ControllerComment'], 'limit' => '1000'];
		$Controller->paginate = ['maxLimit' => 2000, 'paramType' => 'named'];
		$Controller->paginate('PaginatorControllerPost');
		$this->assertEquals(1000, $Controller->params['paging']['PaginatorControllerPost']['options']['limit']);

		$Controller->request->params['named'] = ['contain' => ['ControllerComment'], 'limit' => '5000'];
		$Controller->paginate('PaginatorControllerPost');
		$this->assertEquals(2000, $Controller->params['paging']['PaginatorControllerPost']['options']['limit']);
	}

/**
 * test paginate() and virtualField overlapping with real fields.
 *
 * @return void
 */
	public function testPaginateOrderVirtualFieldSharedWithRealField() {
		$Controller = new Controller($this->request);
		$Controller->uses = ['PaginatorControllerPost', 'PaginatorControllerComment'];
		$Controller->constructClasses();
		$Controller->PaginatorControllerComment->virtualFields = [
			'title' => 'PaginatorControllerComment.comment'
		];
		$Controller->PaginatorControllerComment->bindModel([
			'belongsTo' => [
				'PaginatorControllerPost' => [
					'className' => 'PaginatorControllerPost',
					'foreignKey' => 'article_id'
				]
			]
		], false);

		$Controller->paginate = [
			'fields' => [
				'PaginatorControllerComment.id',
				'title',
				'PaginatorControllerPost.title'
			],
		];
		$Controller->request->params['named'] = [
			'sort' => 'PaginatorControllerPost.title',
			'direction' => 'desc'
		];
		$result = Hash::extract(
			$Controller->paginate('PaginatorControllerComment'),
			'{n}.PaginatorControllerComment.id'
		);
		$result1 = array_splice($result, 0, 2);
		sort($result1);
		$this->assertEquals([5, 6], $result1);

		sort($result);
		$this->assertEquals([1, 2, 3, 4], $result);
	}

/**
 * test paginate() and custom find, to make sure the correct count is returned.
 *
 * @return void
 */
	public function testPaginateCustomFind() {
		$Controller = new Controller($this->request);
		$Controller->uses = ['PaginatorCustomPost'];
		$Controller->constructClasses();
		$data = ['author_id' => 3, 'title' => 'Fourth Article', 'body' => 'Article Body, unpublished', 'published' => 'N'];
		$Controller->PaginatorCustomPost->create($data);
		$result = $Controller->PaginatorCustomPost->save();
		$this->assertTrue(!empty($result));

		$result = $Controller->paginate();
		$this->assertEquals([1, 2, 3, 4], Hash::extract($result, '{n}.PaginatorCustomPost.id'));

		$result = $Controller->params['paging']['PaginatorCustomPost'];
		$this->assertEquals(4, $result['current']);
		$this->assertEquals(4, $result['count']);

		$Controller->paginate = ['published'];
		$result = $Controller->paginate();
		$this->assertEquals([1, 2, 3], Hash::extract($result, '{n}.PaginatorCustomPost.id'));

		$result = $Controller->params['paging']['PaginatorCustomPost'];
		$this->assertEquals(3, $result['current']);
		$this->assertEquals(3, $result['count']);

		$Controller->paginate = ['published', 'limit' => 2];
		$result = $Controller->paginate();
		$this->assertEquals([1, 2], Hash::extract($result, '{n}.PaginatorCustomPost.id'));

		$result = $Controller->params['paging']['PaginatorCustomPost'];
		$this->assertEquals(2, $result['current']);
		$this->assertEquals(3, $result['count']);
		$this->assertEquals(2, $result['pageCount']);
		$this->assertTrue($result['nextPage']);
		$this->assertFalse($result['prevPage']);
	}
/**
 * test paginate() and custom find with fields array, to make sure the correct count is returned.
 *
 * @return void
 */
	public function testPaginateCustomFindFieldsArray() {
		$Controller = new Controller($this->request);
		$Controller->uses = ['PaginatorCustomPost'];
		$Controller->constructClasses();
		$data = ['author_id' => 3, 'title' => 'Fourth Article', 'body' => 'Article Body, unpublished', 'published' => 'N'];
		$Controller->PaginatorCustomPost->create($data);
		$result = $Controller->PaginatorCustomPost->save();
		$this->assertTrue(!empty($result));

		$Controller->paginate = [
			'list',
			'conditions' => ['PaginatorCustomPost.published' => 'Y'],
			'limit' => 2
		];
		$result = $Controller->paginate();
		$expected = [
			1 => 'First Post',
			2 => 'Second Post',
		];
		$this->assertEquals($expected, $result);
		$result = $Controller->params['paging']['PaginatorCustomPost'];
		$this->assertEquals(2, $result['current']);
		$this->assertEquals(3, $result['count']);
		$this->assertEquals(2, $result['pageCount']);
		$this->assertTrue($result['nextPage']);
		$this->assertFalse($result['prevPage']);
	}
/**
 * test paginate() and custom find with customFind key, to make sure the correct count is returned.
 *
 * @return void
 */
	public function testPaginateCustomFindWithCustomFindKey() {
		$Controller = new Controller($this->request);
		$Controller->uses = ['PaginatorCustomPost'];
		$Controller->constructClasses();
		$data = ['author_id' => 3, 'title' => 'Fourth Article', 'body' => 'Article Body, unpublished', 'published' => 'N'];
		$Controller->PaginatorCustomPost->create($data);
		$result = $Controller->PaginatorCustomPost->save();
		$this->assertTrue(!empty($result));

		$Controller->paginate = [
			'conditions' => ['PaginatorCustomPost.published' => 'Y'],
			'findType' => 'list',
			'limit' => 2
		];
		$result = $Controller->paginate();
		$expected = [
			1 => 'First Post',
			2 => 'Second Post',
		];
		$this->assertEquals($expected, $result);
		$result = $Controller->params['paging']['PaginatorCustomPost'];
		$this->assertEquals(2, $result['current']);
		$this->assertEquals(3, $result['count']);
		$this->assertEquals(2, $result['pageCount']);
		$this->assertTrue($result['nextPage']);
		$this->assertFalse($result['prevPage']);
	}

/**
 * test paginate() and custom find with fields array, to make sure the correct count is returned.
 *
 * @return void
 */
	public function testPaginateCustomFindGroupBy() {
		$Controller = new Controller($this->request);
		$Controller->uses = ['PaginatorCustomPost'];
		$Controller->constructClasses();
		$data = ['author_id' => 2, 'title' => 'Fourth Article', 'body' => 'Article Body, unpublished', 'published' => 'N'];
		$Controller->PaginatorCustomPost->create($data);
		$result = $Controller->PaginatorCustomPost->save();
		$this->assertTrue(!empty($result));

		$Controller->paginate = [
			'totals',
			'limit' => 2
		];
		$result = $Controller->paginate();
		$expected = [
			[
				'PaginatorCustomPost' => [
					'author_id' => '1',
					'total_posts' => '2'
				]
			],
			[
				'PaginatorCustomPost' => [
					'author_id' => '2',
					'total_posts' => '1'
				]
			]
		];
		$this->assertEquals($expected, $result);
		$result = $Controller->params['paging']['PaginatorCustomPost'];
		$this->assertEquals(2, $result['current']);
		$this->assertEquals(3, $result['count']);
		$this->assertEquals(2, $result['pageCount']);
		$this->assertTrue($result['nextPage']);
		$this->assertFalse($result['prevPage']);

		$Controller->paginate = [
			'totals',
			'limit' => 2,
			'page' => 2
		];
		$result = $Controller->paginate();
		$expected = [
			[
				'PaginatorCustomPost' => [
					'author_id' => '3',
					'total_posts' => '1'
				]
			],
		];
		$this->assertEquals($expected, $result);
		$result = $Controller->params['paging']['PaginatorCustomPost'];
		$this->assertEquals(1, $result['current']);
		$this->assertEquals(3, $result['count']);
		$this->assertEquals(2, $result['pageCount']);
		$this->assertFalse($result['nextPage']);
		$this->assertTrue($result['prevPage']);
	}

/**
 * test paginate() and custom find with returning other query on count operation,
 * to make sure the correct count is returned.
 *
 * @return void
 */
	public function testPaginateCustomFindCount() {
		$Controller = new Controller($this->request);
		$Controller->uses = ['PaginatorCustomPost'];
		$Controller->constructClasses();
		$data = ['author_id' => 2, 'title' => 'Fourth Article', 'body' => 'Article Body, unpublished', 'published' => 'N'];
		$Controller->PaginatorCustomPost->create($data);
		$result = $Controller->PaginatorCustomPost->save();
		$this->assertTrue(!empty($result));

		$Controller->paginate = [
			'totalsOperation',
			'limit' => 2
		];
		$result = $Controller->paginate();
		$expected = [
			[
				'PaginatorCustomPost' => [
					'author_id' => '1',
					'total_posts' => '2'
				],
				'Author' => [
					'user' => 'mariano',
				]
			],
			[
				'PaginatorCustomPost' => [
					'author_id' => '2',
					'total_posts' => '1'
				],
				'Author' => [
					'user' => 'nate'
				]
			]
		];
		$this->assertEquals($expected, $result);
		$result = $Controller->params['paging']['PaginatorCustomPost'];
		$this->assertEquals(2, $result['current']);
		$this->assertEquals(3, $result['count']);
		$this->assertEquals(2, $result['pageCount']);
		$this->assertTrue($result['nextPage']);
		$this->assertFalse($result['prevPage']);
	}
}
