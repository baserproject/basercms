<?php
/**
 * DataSourceTest file
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.Test.Case.Model.Datasource
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');
App::uses('DataSource', 'Model/Datasource');

/**
 * TestSource
 *
 * @package       Cake.Test.Case.Model.Datasource
 */
class TestSource extends DataSource {

/**
 * _schema
 * @var type
 */
	protected $_schema = [
		'id' => [
			'type' => 'integer',
			'null' => false,
			'key' => 'primary',
			'length' => 11,
		],
		'text' => [
			'type' => 'string',
			'null' => true,
			'length' => 140,
		],
		'status' => [
			'type' => 'string',
			'null' => true,
			'length' => 140,
		],
		'customField' => [
			'type' => 'string',
			'null' => true,
			'length' => 255,
		],
	];

/**
 * listSources
 *
 * @return bool
 */
	public function listSources() {
		return null;
	}

/**
 * Returns the schema for the datasource to enable create/update
 *
 * @param object $Model
 * @return array
 */
	public function describe(Model $Model) {
		return $this->_schema;
	}

/**
 * Just return $func to pass to read() to figure out the COUNT
 * Required for delete/update to work
 *
 * @param Model $Model
 * @param type $func
 * @param type $params
 * @return array
 */
	public function calculate(Model $Model, $func, $params = []) {
		return $func;
	}

}

/**
 * DataSourceTest class
 *
 * @package       Cake.Test.Case.Model.Datasource
 */
class DataSourceTest extends CakeTestCase {

/**
 * Name of test source
 *
 * @var string
 */
	public $sourceName = 'myapitest';

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Source = $this->getMock(
			'TestSource',
			['create', 'read', 'update', 'delete']
		);
		ConnectionManager::create($this->sourceName, [
			'datasource' => get_class($this->Source),
			'apiKey' => '1234abcd',
		]);
		$this->Model = $this->getMock(
			'Model',
			['getDataSource'],
			[['ds' => $this->sourceName]]
		);
		$this->Model->expects($this->any())
			->method('getDataSource')
			->will($this->returnValue($this->Source));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Model, $this->Source);
		ConnectionManager::drop($this->sourceName);
	}

/**
 * testCreate
 *
 * @return void
 */
	public function testCreate() {
		$data = [
			$this->Model->alias => [
				'text' => 'This is a test',
				'status' => 'Test status',
				'customField' => [
					'array', 'field', 'type',
					'for', 'custom', 'datasources',
				],
			],
		];
		$this->Source->expects($this->once())
			->method('create')
			->with(
				$this->equalTo($this->Model),
				$this->equalTo(array_keys($data[$this->Model->alias])),
				$this->equalTo(array_values($data[$this->Model->alias]))
			);
		$this->Model->save($data);
	}

/**
 * testRead
 *
 * @return void
 */
	public function testRead() {
		$expected = [
			'conditions'	=> ['status' => 'test'],
			'fields'		=> null,
			'joins'			=> [],
			'limit'			=> 10,
			'offset'		=> null,
			'order'			=> [['status']],
			'page'			=> 1,
			'group'			=> null,
			'callbacks'		=> true,
		];
		$this->Source->expects($this->once())
			->method('read')
			->with(
				$this->anything(),
				$this->equalTo($expected)
			);
		$this->Model->find('all', [
			'conditions' => ['status' => 'test'],
			'limit' => 10,
			'order' => ['status'],
		]);
	}

/**
 * testUpdate
 *
 * @return void
 */
	public function testUpdate() {
		$data = [
			$this->Model->alias => [
				'id' => 1,
				'text' => 'This is a test',
				'status' => 'Test status',
				'customField' => [
					'array', 'field', 'type',
					'for', 'custom', 'datasources',
				],
			],
		];
		$this->Source->expects($this->any())
			->method('read')
			->will($this->returnValue([
				[$this->Model->alias => ['count' => 1]]
			]));
		$this->Source->expects($this->once())
			->method('update')
			->with(
				$this->equalTo($this->Model),
				$this->equalTo(array_keys($data[$this->Model->alias])),
				$this->equalTo(array_values($data[$this->Model->alias]))
			);
		$this->Model->save($data);
	}

/**
 * testDelete
 *
 * @return void
 */
	public function testDelete() {
		$this->Source->expects($this->any())
			->method('read')
			->will($this->returnValue([
				[$this->Model->alias => ['count' => 1]]
			]));
		$this->Source->expects($this->once())
			->method('delete')
			->with(
				$this->equalTo($this->Model),
				$this->equalTo([$this->Model->alias . '.id' => 1])
			);
		$this->Model->delete(1);
	}

}
