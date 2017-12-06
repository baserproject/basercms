<?php
/**
 * ModelReadTest file
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

require_once dirname(__FILE__) . DS . 'ModelTestBase.php';

/**
 * ModelReadTest
 *
 * @package       Cake.Test.Case.Model
 */
class ModelReadTest extends BaseModelTest {

/**
 * testExists function
 * @return void
 */
	public function testExists() {
		$this->loadFixtures('User');
		$TestModel = new User();

		$this->assertTrue($TestModel->exists(1));

		$TestModel->id = 2;
		$this->assertTrue($TestModel->exists());

		$TestModel->delete();
		$this->assertFalse($TestModel->exists());

		$this->assertFalse($TestModel->exists(2));
	}

/**
 * testFetchingNonUniqueFKJoinTableRecords()
 *
 * Tests if the results are properly returned in the case there are non-unique FK's
 * in the join table but another fields value is different. For example:
 * something_id | something_else_id | doomed = 1
 * something_id | something_else_id | doomed = 0
 * Should return both records and not just one.
 *
 * @return void
 */
	public function testFetchingNonUniqueFKJoinTableRecords() {
		$this->loadFixtures('Something', 'SomethingElse', 'JoinThing');
		$Something = new Something();

		$joinThingData = [
			'JoinThing' => [
				'something_id' => 1,
				'something_else_id' => 2,
				'doomed' => '0',
				'created' => '2007-03-18 10:39:23',
				'updated' => '2007-03-18 10:41:31'
			]
		];

		$Something->JoinThing->create($joinThingData);
		$Something->JoinThing->save();

		$result = $Something->JoinThing->find('all', ['conditions' => ['something_else_id' => 2]]);

		$this->assertEquals(true, $result[0]['JoinThing']['doomed']);
		$this->assertEquals(false, $result[1]['JoinThing']['doomed']);

		$result = $Something->find('first');

		$this->assertEquals(2, count($result['SomethingElse']));

		$doomed = Hash::extract($result['SomethingElse'], '{n}.JoinThing.doomed');
		$this->assertTrue(in_array(true, $doomed));
		$this->assertTrue(in_array(false, $doomed));
	}

/**
 * Test IN operator
 *
 * @return void
 */
	public function testInOperator() {
		$this->loadFixtures('Product');
		$Product = new Product();
		$expected = [
			[
				'Product' => [
					'id' => 1,
					'name' => "Park's Great Hits",
					'type' => 'Music',
					'price' => 19
				]
			]
		];

		$result = $Product->find('all', ['conditions' => ['Product.id IN' => [1]]]);
		$this->assertEquals($expected, $result);

		$expected = [
			[
				'Product' => [
					'id' => 2,
					'name' => "Silly Puddy",
					'type' => 'Toy',
					'price' => 3
				]
			],
			[
				'Product' => [
					'id' => 3,
					'name' => "Playstation",
					'type' => 'Toy',
					'price' => 89
				]
			],
			[
				'Product' => [
					'id' => 4,
					'name' => "Men's T-Shirt",
					'type' => 'Clothing',
					'price' => 32
				]
			],
			[
				'Product' => [
					'id' => 5,
					'name' => "Blouse",
					'type' => 'Clothing',
					'price' => 34
				]
			],
			[
				'Product' => [
					'id' => 6,
					'name' => "Electronica 2002",
					'type' => 'Music',
					'price' => 4
				]
			],
			[
				'Product' => [
					'id' => 7,
					'name' => "Country Tunes",
					'type' => 'Music',
					'price' => 21
				]
			],
			[
				'Product' => [
					'id' => 8,
					'name' => "Watermelon",
					'type' => 'Food',
					'price' => 9
				]
			]
		];
		$result = $Product->find('all', ['conditions' => ['Product.id NOT IN' => [1]]]);
		$this->assertEquals($expected, $result);

		$expected = [
			[
				'Product' => [
					'id' => 1,
					'name' => "Park's Great Hits",
					'type' => 'Music',
					'price' => 19
				]
			],
			[
				'Product' => [
					'id' => 2,
					'name' => "Silly Puddy",
					'type' => 'Toy',
					'price' => 3
				]
			],
		];

		$result = $Product->find('all', ['conditions' => ['Product.id IN' => [1, 2]]]);
		$this->assertEquals($expected, $result);
	}

/**
 * testGroupBy method
 *
 * These tests will never pass with Postgres or Oracle as all fields in a select must be
 * part of an aggregate function or in the GROUP BY statement.
 *
 * @return void
 */
	public function testGroupBy() {
		$isStrictGroupBy = $this->db instanceof Postgres || $this->db instanceof Sqlite || $this->db instanceof Oracle || $this->db instanceof Sqlserver;
		$message = 'Postgres, Oracle, SQLite and SQL Server have strict GROUP BY and are incompatible with this test.';

		$this->skipIf($isStrictGroupBy, $message);

		$this->loadFixtures('Project', 'Product', 'Thread', 'Message', 'Bid');
		$Thread = new Thread();
		$Product = new Product();

		$result = $Thread->find('all', [
			'group' => 'Thread.project_id',
			'order' => 'Thread.id ASC'
		]);

		$expected = [
			[
				'Thread' => [
					'id' => 1,
					'project_id' => 1,
					'name' => 'Project 1, Thread 1'
				],
				'Project' => [
					'id' => 1,
					'name' => 'Project 1'
				],
				'Message' => [
					[
						'id' => 1,
						'thread_id' => 1,
						'name' => 'Thread 1, Message 1'
			]]],
			[
				'Thread' => [
					'id' => 3,
					'project_id' => 2,
					'name' => 'Project 2, Thread 1'
				],
				'Project' => [
					'id' => 2,
					'name' => 'Project 2'
				],
				'Message' => [
					[
						'id' => 3,
						'thread_id' => 3,
						'name' => 'Thread 3, Message 1'
		]]]];
		$this->assertEquals($expected, $result);

		$rows = $Thread->find('all', [
			'group' => 'Thread.project_id',
			'fields' => ['Thread.project_id', 'COUNT(*) AS total']
		]);
		$result = [];
		foreach ($rows as $row) {
			$result[$row['Thread']['project_id']] = $row[0]['total'];
		}
		$expected = [
			1 => 2,
			2 => 1
		];
		$this->assertEquals($expected, $result);

		$rows = $Thread->find('all', [
			'group' => 'Thread.project_id',
			'fields' => ['Thread.project_id', 'COUNT(*) AS total'],
			'order' => 'Thread.project_id'
		]);
		$result = [];
		foreach ($rows as $row) {
			$result[$row['Thread']['project_id']] = $row[0]['total'];
		}
		$expected = [
			1 => 2,
			2 => 1
		];
		$this->assertEquals($expected, $result);

		$result = $Thread->find('all', [
			'conditions' => ['Thread.project_id' => 1],
			'group' => 'Thread.project_id'
		]);
		$expected = [
			[
				'Thread' => [
					'id' => 1,
					'project_id' => 1,
					'name' => 'Project 1, Thread 1'
				],
				'Project' => [
					'id' => 1,
					'name' => 'Project 1'
				],
				'Message' => [
					[
						'id' => 1,
						'thread_id' => 1,
						'name' => 'Thread 1, Message 1'
		]]]];
		$this->assertEquals($expected, $result);

		$result = $Thread->find('all', [
			'conditions' => ['Thread.project_id' => 1],
			'group' => 'Thread.project_id, Project.id'
		]);
		$this->assertEquals($expected, $result);

		$result = $Thread->find('all', [
			'conditions' => ['Thread.project_id' => 1],
			'group' => 'project_id'
		]);
		$this->assertEquals($expected, $result);

		$result = $Thread->find('all', [
			'conditions' => ['Thread.project_id' => 1],
			'group' => ['project_id']
		]);
		$this->assertEquals($expected, $result);

		$result = $Thread->find('all', [
			'conditions' => ['Thread.project_id' => 1],
			'group' => ['project_id', 'Project.id']
		]);
		$this->assertEquals($expected, $result);

		$result = $Thread->find('all', [
			'conditions' => ['Thread.project_id' => 1],
			'group' => ['Thread.project_id', 'Project.id']
		]);
		$this->assertEquals($expected, $result);

		$expected = [
			['Product' => ['type' => 'Clothing'], ['price' => 32]],
			['Product' => ['type' => 'Food'], ['price' => 9]],
			['Product' => ['type' => 'Music'], ['price' => 4]],
			['Product' => ['type' => 'Toy'], ['price' => 3]]
		];
		$result = $Product->find('all', [
			'fields' => ['Product.type', 'MIN(Product.price) as price'],
			'group' => 'Product.type',
			'order' => 'Product.type ASC'
			]);
		$this->assertEquals($expected, $result);

		$result = $Product->find('all', [
			'fields' => ['Product.type', 'MIN(Product.price) as price'],
			'group' => ['Product.type'],
			'order' => 'Product.type ASC']);
		$this->assertEquals($expected, $result);
	}

/**
 * testOldQuery method
 *
 * @return void
 */
	public function testOldQuery() {
		$this->loadFixtures('Article', 'User', 'Tag', 'ArticlesTag', 'Comment', 'Attachment');
		$Article = new Article();

		$query = 'SELECT title FROM ';
		$query .= $this->db->fullTableName('articles');
		$query .= ' WHERE ' . $this->db->fullTableName('articles') . '.id IN (1,2)';

		$results = $Article->query($query);
		$this->assertTrue(is_array($results));
		$this->assertEquals(2, count($results));

		$query = 'SELECT title, body FROM ';
		$query .= $this->db->fullTableName('articles');
		$query .= ' WHERE ' . $this->db->fullTableName('articles') . '.id = 1';

		$results = $Article->query($query, false);
		$this->assertFalse($this->db->getQueryCache($query));
		$this->assertTrue(is_array($results));

		$query = 'SELECT title, id FROM ';
		$query .= $this->db->fullTableName('articles');
		$query .= ' WHERE ' . $this->db->fullTableName('articles');
		$query .= '.published = ' . $this->db->value('Y');

		$results = $Article->query($query, true);
		$result = $this->db->getQueryCache($query);
		$this->assertFalse(empty($result));
		$this->assertTrue(is_array($results));
	}

/**
 * testPreparedQuery method
 *
 * @return void
 */
	public function testPreparedQuery() {
		$this->loadFixtures('Article', 'User', 'Tag', 'ArticlesTag');
		$Article = new Article();

		$query = 'SELECT title, published FROM ';
		$query .= $this->db->fullTableName('articles');
		$query .= ' WHERE ' . $this->db->fullTableName('articles');
		$query .= '.id = ? AND ' . $this->db->fullTableName('articles') . '.published = ?';

		$params = [1, 'Y'];
		$result = $Article->query($query, $params);
		$expected = [
			'0' => [
				$this->db->fullTableName('articles', false, false) => [
					'title' => 'First Article', 'published' => 'Y']
		]];

		if (isset($result[0][0])) {
			$expected[0][0] = $expected[0][$this->db->fullTableName('articles', false, false)];
			unset($expected[0][$this->db->fullTableName('articles', false, false)]);
		}

		$this->assertEquals($expected, $result);
		$result = $this->db->getQueryCache($query, $params);
		$this->assertFalse(empty($result));

		$query = 'SELECT id, created FROM ';
		$query .= $this->db->fullTableName('articles');
		$query .= '  WHERE ' . $this->db->fullTableName('articles') . '.title = ?';

		$params = ['First Article'];
		$result = $Article->query($query, $params, false);
		$this->assertTrue(is_array($result));
		$this->assertTrue(
			isset($result[0][$this->db->fullTableName('articles', false, false)]) ||
			isset($result[0][0])
		);
		$result = $this->db->getQueryCache($query, $params);
		$this->assertTrue(empty($result));

		$query = 'SELECT title FROM ';
		$query .= $this->db->fullTableName('articles');
		$query .= ' WHERE ' . $this->db->fullTableName('articles') . '.title LIKE ?';

		$params = ['%First%'];
		$result = $Article->query($query, $params);
		$this->assertTrue(is_array($result));
		$this->assertTrue(
			isset($result[0][$this->db->fullTableName('articles', false, false)]['title']) ||
			isset($result[0][0]['title'])
		);

		//related to ticket #5035
		$query = 'SELECT title FROM ';
		$query .= $this->db->fullTableName('articles') . ' WHERE title = ? AND published = ?';
		$params = ['First? Article', 'Y'];
		$Article->query($query, $params);

		$result = $this->db->getQueryCache($query, $params);
		$this->assertFalse($result === false);
	}

/**
 * testParameterMismatch method
 *
 * @expectedException PDOException
 * @return void
 */
	public function testParameterMismatch() {
		$this->skipIf($this->db instanceof Sqlite, 'Sqlite does not accept real prepared statements, no way to check this');
		$this->loadFixtures('Article', 'User', 'Tag', 'ArticlesTag');
		$Article = new Article();

		$query = 'SELECT * FROM ' . $this->db->fullTableName('articles');
		$query .= ' WHERE ' . $this->db->fullTableName('articles');
		$query .= '.published = ? AND ' . $this->db->fullTableName('articles') . '.user_id = ?';
		$params = ['Y'];

		$Article->query($query, $params);
	}

/**
 * testVeryStrangeUseCase method
 *
 * @expectedException PDOException
 * @return void
 */
	public function testVeryStrangeUseCase() {
		$this->loadFixtures('Article', 'User', 'Tag', 'ArticlesTag');
		$Article = new Article();

		$query = 'SELECT * FROM ? WHERE ? = ? AND ? = ?';
		$param = [
			$this->db->fullTableName('articles'),
			$this->db->fullTableName('articles') . '.user_id', '3',
			$this->db->fullTableName('articles') . '.published', 'Y'
		];

		$Article->query($query, $param);
	}

/**
 * testRecursiveUnbind method
 *
 * @return void
 */
	public function testRecursiveUnbind() {
		$this->skipIf($this->db instanceof Sqlserver, 'The test of testRecursiveUnbind test is not compatible with SQL Server, because it check for time columns.');

		$this->loadFixtures('Apple', 'Sample');
		$TestModel = new Apple();
		$TestModel->recursive = 2;

		$result = $TestModel->find('all');
		$expected = [
			[
				'Apple' => [
					'id' => 1,
					'apple_id' => 2,
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17'
					],
					'Sample' => [
						'id' => 2,
						'apple_id' => 2,
						'name' => 'sample2'
					],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17'
						],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
					]]],
					'Sample' => [
						'id' => '',
						'apple_id' => '',
						'name' => ''
					],
					'Child' => [
						[
							'id' => 2,
							'apple_id' => 1,
							'color' => 'Bright Red 1',
							'name' => 'Bright Red Apple',
							'created' => '2006-11-22 10:43:13',
							'date' => '2014-01-01',
							'modified' => '2006-11-30 18:38:10',
							'mytime' => '22:57:17',
							'Parent' => [
								'id' => 1,
								'apple_id' => 2,
								'color' => 'Red 1',
								'name' => 'Red Apple 1',
								'created' => '2006-11-22 10:38:58',
								'date' => '1951-01-04',
								'modified' => '2006-12-01 13:31:26',
								'mytime' => '22:57:17'
							],
							'Sample' => [
								'id' => 2,
								'apple_id' => 2,
								'name' => 'sample2'
							],
							'Child' => [
								[
									'id' => 1,
									'apple_id' => 2,
									'color' => 'Red 1',
									'name' => 'Red Apple 1',
									'created' => '2006-11-22 10:38:58',
									'date' => '1951-01-04',
									'modified' => '2006-12-01 13:31:26',
									'mytime' => '22:57:17'
								],
								[
									'id' => 3,
									'apple_id' => 2,
									'color' => 'blue green',
									'name' => 'green blue',
									'created' => '2006-12-25 05:13:36',
									'date' => '2006-12-25',
									'modified' => '2006-12-25 05:23:24',
									'mytime' => '22:57:17'
								],
								[
									'id' => 4,
									'apple_id' => 2,
									'color' => 'Blue Green',
									'name' => 'Test Name',
									'created' => '2006-12-25 05:23:36',
									'date' => '2006-12-25',
									'modified' => '2006-12-25 05:23:36',
									'mytime' => '22:57:17'
			]]]]],
			[
				'Apple' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17'
				],
				'Parent' => [
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17',
						'Parent' => [
							'id' => 2,
							'apple_id' => 1,
							'color' => 'Bright Red 1',
							'name' => 'Bright Red Apple',
							'created' => '2006-11-22 10:43:13',
							'date' => '2014-01-01',
							'modified' => '2006-11-30 18:38:10',
							'mytime' => '22:57:17'
						],
						'Sample' => [],
						'Child' => [
							[
								'id' => 2,
								'apple_id' => 1,
								'color' => 'Bright Red 1',
								'name' => 'Bright Red Apple',
								'created' => '2006-11-22 10:43:13',
								'date' => '2014-01-01',
								'modified' => '2006-11-30 18:38:10',
								'mytime' => '22:57:17'
					]]],
					'Sample' => [
						'id' => 2,
						'apple_id' => 2,
						'name' => 'sample2',
						'Apple' => [
							'id' => 2,
							'apple_id' => 1,
							'color' => 'Bright Red 1',
							'name' => 'Bright Red Apple',
							'created' => '2006-11-22 10:43:13',
							'date' => '2014-01-01',
							'modified' => '2006-11-30 18:38:10',
							'mytime' => '22:57:17'
					]],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17',
							'Parent' => [
								'id' => 2,
								'apple_id' => 1,
								'color' => 'Bright Red 1',
								'name' => 'Bright Red Apple',
								'created' => '2006-11-22 10:43:13',
								'date' => '2014-01-01',
								'modified' => '2006-11-30 18:38:10',
								'mytime' => '22:57:17'
							],
							'Sample' => [],
							'Child' => [
								[
									'id' => 2,
									'apple_id' => 1,
									'color' => 'Bright Red 1',
									'name' => 'Bright Red Apple',
									'created' => '2006-11-22 10:43:13',
									'date' => '2014-01-01',
									'modified' => '2006-11-30 18:38:10',
									'mytime' => '22:57:17'
						]]],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17',
							'Parent' => [
								'id' => 2,
								'apple_id' => 1,
								'color' => 'Bright Red 1',
								'name' => 'Bright Red Apple',
								'created' => '2006-11-22 10:43:13',
								'date' => '2014-01-01',
								'modified' => '2006-11-30 18:38:10',
								'mytime' => '22:57:17'
							],
							'Sample' => [
								'id' => 1,
								'apple_id' => 3,
								'name' => 'sample1'
						]],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17',
							'Parent' => [
								'id' => 2,
								'apple_id' => 1,
								'color' => 'Bright Red 1',
								'name' => 'Bright Red Apple',
								'created' => '2006-11-22 10:43:13',
								'date' => '2014-01-01',
								'modified' => '2006-11-30 18:38:10',
								'mytime' => '22:57:17'
							],
							'Sample' => [
								'id' => 3,
								'apple_id' => 4,
								'name' => 'sample3'
							],
							'Child' => [
								[
									'id' => 6,
									'apple_id' => 4,
									'color' => 'My new appleOrange',
									'name' => 'My new apple',
									'created' => '2006-12-25 05:29:39',
									'date' => '2006-12-25',
									'modified' => '2006-12-25 05:29:39',
									'mytime' => '22:57:17'
			]]]]],
			[
				'Apple' => [
					'id' => 3,
					'apple_id' => 2,
					'color' => 'blue green',
					'name' => 'green blue',
					'created' => '2006-12-25 05:13:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:24',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17'
					],
					'Sample' => [
						'id' => 2,
						'apple_id' => 2,
						'name' => 'sample2'
					],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17'
						],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 1,
					'apple_id' => 3,
					'name' => 'sample1',
					'Apple' => [
						'id' => 3,
						'apple_id' => 2,
						'color' => 'blue green',
						'name' => 'green blue',
						'created' => '2006-12-25 05:13:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:24',
						'mytime' => '22:57:17'
				]],
				'Child' => []
			],
			[
				'Apple' => [
					'id' => 4,
					'apple_id' => 2,
					'color' => 'Blue Green',
					'name' => 'Test Name',
					'created' => '2006-12-25 05:23:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:36',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26', 'mytime' => '22:57:17'],
						'Sample' => ['id' => 2, 'apple_id' => 2, 'name' => 'sample2'],
						'Child' => [
							[
								'id' => 1,
								'apple_id' => 2,
								'color' => 'Red 1',
								'name' => 'Red Apple 1',
								'created' => '2006-11-22 10:38:58',
								'date' => '1951-01-04',
								'modified' => '2006-12-01 13:31:26',
								'mytime' => '22:57:17'
							],
							[
								'id' => 3,
								'apple_id' => 2,
								'color' => 'blue green',
								'name' => 'green blue',
								'created' => '2006-12-25 05:13:36',
								'date' => '2006-12-25',
								'modified' => '2006-12-25 05:23:24',
								'mytime' => '22:57:17'
							],
							[
								'id' => 4,
								'apple_id' => 2,
								'color' => 'Blue Green',
								'name' => 'Test Name',
								'created' => '2006-12-25 05:23:36',
								'date' => '2006-12-25',
								'modified' => '2006-12-25 05:23:36',
								'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 3,
					'apple_id' => 4,
					'name' => 'sample3',
					'Apple' => [
						'id' => 4,
						'apple_id' => 2,
						'color' => 'Blue Green',
						'name' => 'Test Name',
						'created' => '2006-12-25 05:23:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:36',
						'mytime' => '22:57:17'
				]],
				'Child' => [
					[
						'id' => 6,
						'apple_id' => 4,
						'color' => 'My new appleOrange',
						'name' => 'My new apple',
						'created' => '2006-12-25 05:29:39',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:39',
						'mytime' => '22:57:17',
						'Parent' => [
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
						],
						'Sample' => [],
						'Child' => [
							[
								'id' => 7,
								'apple_id' => 6,
								'color' => 'Some wierd color',
								'name' => 'Some odd color',
								'created' => '2006-12-25 05:34:21',
								'date' => '2006-12-25',
								'modified' => '2006-12-25 05:34:21',
								'mytime' => '22:57:17'
			]]]]],
			[
				'Apple' => [
					'id' => 5,
					'apple_id' => 5,
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:16',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 5,
					'apple_id' => 5,
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:16',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 5,
						'apple_id' => 5,
						'color' => 'Green',
						'name' => 'Blue Green',
						'created' => '2006-12-25 05:24:06',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:16',
						'mytime' => '22:57:17'
					],
					'Sample' => [
						'id' => 4,
						'apple_id' => 5,
						'name' => 'sample4'
					],
					'Child' => [
						[
							'id' => 5,
							'apple_id' => 5,
							'color' => 'Green',
							'name' => 'Blue Green',
							'created' => '2006-12-25 05:24:06',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:29:16',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 4,
					'apple_id' => 5,
					'name' => 'sample4',
					'Apple' => [
						'id' => 5,
						'apple_id' => 5,
						'color' => 'Green',
						'name' => 'Blue Green',
						'created' => '2006-12-25 05:24:06',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:16',
						'mytime' => '22:57:17'
					]],
					'Child' => [
						[
							'id' => 5,
							'apple_id' => 5,
							'color' => 'Green',
							'name' => 'Blue Green',
							'created' => '2006-12-25 05:24:06',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:29:16',
							'mytime' => '22:57:17',
							'Parent' => [
								'id' => 5,
								'apple_id' => 5,
								'color' => 'Green',
								'name' => 'Blue Green',
								'created' => '2006-12-25 05:24:06',
								'date' => '2006-12-25',
								'modified' => '2006-12-25 05:29:16',
								'mytime' => '22:57:17'
							],
							'Sample' => [
								'id' => 4,
								'apple_id' => 5,
								'name' => 'sample4'
							],
							'Child' => [
								[
									'id' => 5,
									'apple_id' => 5,
									'color' => 'Green',
									'name' => 'Blue Green',
									'created' => '2006-12-25 05:24:06',
									'date' => '2006-12-25',
									'modified' => '2006-12-25 05:29:16',
									'mytime' => '22:57:17'
			]]]]],
			[
				'Apple' => [
					'id' => 6,
					'apple_id' => 4,
					'color' => 'My new appleOrange',
					'name' => 'My new apple',
					'created' => '2006-12-25 05:29:39',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:39',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 4,
					'apple_id' => 2,
					'color' => 'Blue Green',
					'name' => 'Test Name',
					'created' => '2006-12-25 05:23:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:36',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 2,
						'apple_id' => 1,
						'color' => 'Bright Red 1',
						'name' => 'Bright Red Apple',
						'created' => '2006-11-22 10:43:13',
						'date' => '2014-01-01',
						'modified' => '2006-11-30 18:38:10',
						'mytime' => '22:57:17'
					],
					'Sample' => [
						'id' => 3,
						'apple_id' => 4,
						'name' => 'sample3'
					],
					'Child' => [
						[
							'id' => 6,
							'apple_id' => 4,
							'color' => 'My new appleOrange',
							'name' => 'My new apple',
							'created' => '2006-12-25 05:29:39',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:29:39',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => '',
					'apple_id' => '',
					'name' => ''
				],
				'Child' => [
					[
						'id' => 7,
						'apple_id' => 6,
						'color' => 'Some wierd color',
						'name' => 'Some odd color',
						'created' => '2006-12-25 05:34:21',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:34:21',
						'mytime' => '22:57:17',
						'Parent' => [
							'id' => 6,
							'apple_id' => 4,
							'color' => 'My new appleOrange',
							'name' => 'My new apple',
							'created' => '2006-12-25 05:29:39',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:29:39',
							'mytime' => '22:57:17'
						],
						'Sample' => []
			]]],
			[
				'Apple' => [
					'id' => 7,
					'apple_id' => 6,
					'color' =>
					'Some wierd color',
					'name' => 'Some odd color',
					'created' => '2006-12-25 05:34:21',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:34:21',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 6,
					'apple_id' => 4,
					'color' => 'My new appleOrange',
					'name' => 'My new apple',
					'created' => '2006-12-25 05:29:39',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:39',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 4,
						'apple_id' => 2,
						'color' => 'Blue Green',
						'name' => 'Test Name',
						'created' => '2006-12-25 05:23:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:36',
						'mytime' => '22:57:17'
					],
					'Sample' => [],
					'Child' => [
						[
							'id' => 7,
							'apple_id' => 6,
							'color' => 'Some wierd color',
							'name' => 'Some odd color',
							'created' => '2006-12-25 05:34:21',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:34:21',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => '',
					'apple_id' => '',
					'name' => ''
				],
				'Child' => []]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->Parent->unbindModel(['hasOne' => ['Sample']]);
		$this->assertTrue($result);

		$result = $TestModel->find('all');
		$expected = [
			[
				'Apple' => [
					'id' => 1,
					'apple_id' => 2,
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17'],
					'Parent' => [
						'id' => 2,
						'apple_id' => 1,
						'color' => 'Bright Red 1',
						'name' => 'Bright Red Apple',
						'created' => '2006-11-22 10:43:13',
						'date' => '2014-01-01',
						'modified' => '2006-11-30 18:38:10',
						'mytime' => '22:57:17',
						'Parent' => [
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						'Child' => [
							[
								'id' => 1,
								'apple_id' => 2,
								'color' => 'Red 1',
								'name' => 'Red Apple 1',
								'created' => '2006-11-22 10:38:58',
								'date' => '1951-01-04',
								'modified' => '2006-12-01 13:31:26',
								'mytime' => '22:57:17'
							],
							[
								'id' => 3,
								'apple_id' => 2,
								'color' => 'blue green',
								'name' => 'green blue',
								'created' => '2006-12-25 05:13:36',
								'date' => '2006-12-25',
								'modified' => '2006-12-25 05:23:24',
								'mytime' => '22:57:17'
							],
							[
								'id' => 4,
								'apple_id' => 2,
								'color' => 'Blue Green',
								'name' => 'Test Name',
								'created' => '2006-12-25 05:23:36',
								'date' => '2006-12-25',
								'modified' => '2006-12-25 05:23:36',
								'mytime' => '22:57:17'
					]]],
					'Sample' => [
						'id' => '',
						'apple_id' => '',
						'name' => ''
					],
					'Child' => [
						[
							'id' => 2,
							'apple_id' => 1,
							'color' => 'Bright Red 1',
							'name' => 'Bright Red Apple',
							'created' => '2006-11-22 10:43:13',
							'date' => '2014-01-01',
							'modified' => '2006-11-30 18:38:10',
							'mytime' => '22:57:17',
							'Parent' => [
								'id' => 1,
								'apple_id' => 2,
								'color' => 'Red 1',
								'name' => 'Red Apple 1',
								'created' => '2006-11-22 10:38:58',
								'date' => '1951-01-04',
								'modified' => '2006-12-01 13:31:26',
								'mytime' => '22:57:17'
							],
							'Sample' => [
								'id' => 2,
								'apple_id' => 2,
								'name' => 'sample2'
							],
							'Child' => [
								[
									'id' => 1,
									'apple_id' => 2,
									'color' => 'Red 1',
									'name' => 'Red Apple 1',
									'created' => '2006-11-22 10:38:58',
									'date' => '1951-01-04',
									'modified' => '2006-12-01 13:31:26',
									'mytime' => '22:57:17'
								],
								[
									'id' => 3,
									'apple_id' => 2,
									'color' => 'blue green',
									'name' => 'green blue',
									'created' => '2006-12-25 05:13:36',
									'date' => '2006-12-25',
									'modified' => '2006-12-25 05:23:24',
									'mytime' => '22:57:17'
								],
								[
									'id' => 4,
									'apple_id' => 2,
									'color' => 'Blue Green',
									'name' => 'Test Name',
									'created' => '2006-12-25 05:23:36',
									'date' => '2006-12-25',
									'modified' => '2006-12-25 05:23:36',
									'mytime' => '22:57:17'
			]]]]],
			[
				'Apple' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 1,
					'apple_id' => 2,
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 2,
						'apple_id' => 1,
						'color' => 'Bright Red 1',
						'name' => 'Bright Red Apple',
						'created' => '2006-11-22 10:43:13',
						'date' => '2014-01-01',
						'modified' => '2006-11-30 18:38:10',
						'mytime' => '22:57:17'
					],
					'Child' => [
						[
							'id' => 2,
							'apple_id' => 1,
							'color' => 'Bright Red 1',
							'name' => 'Bright Red Apple',
							'created' => '2006-11-22 10:43:13',
							'date' => '2014-01-01',
							'modified' => '2006-11-30 18:38:10',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 2,
					'apple_id' => 2,
					'name' => 'sample2',
					'Apple' => [
						'id' => 2,
						'apple_id' => 1,
						'color' => 'Bright Red 1',
						'name' => 'Bright Red Apple',
						'created' => '2006-11-22 10:43:13',
						'date' => '2014-01-01',
						'modified' => '2006-11-30 18:38:10',
						'mytime' => '22:57:17'
				]],
				'Child' => [
					[
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17',
						'Parent' => [
							'id' => 2,
							'apple_id' => 1,
							'color' => 'Bright Red 1',
							'name' => 'Bright Red Apple',
							'created' => '2006-11-22 10:43:13',
							'date' => '2014-01-01',
							'modified' => '2006-11-30 18:38:10',
							'mytime' => '22:57:17'
						],
						'Sample' => [],
						'Child' => [
							[
								'id' => 2,
								'apple_id' => 1,
								'color' => 'Bright Red 1',
								'name' => 'Bright Red Apple',
								'created' => '2006-11-22 10:43:13',
								'date' => '2014-01-01', 'modified' =>
								'2006-11-30 18:38:10',
								'mytime' => '22:57:17'
					]]],
					[
						'id' => 3,
						'apple_id' => 2,
						'color' => 'blue green',
						'name' => 'green blue',
						'created' => '2006-12-25 05:13:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:24',
						'mytime' => '22:57:17',
						'Parent' => [
							'id' => 2,
							'apple_id' => 1,
							'color' => 'Bright Red 1',
							'name' => 'Bright Red Apple',
							'created' => '2006-11-22 10:43:13',
							'date' => '2014-01-01',
							'modified' => '2006-11-30 18:38:10',
							'mytime' => '22:57:17'
						],
						'Sample' => [
							'id' => 1,
							'apple_id' => 3,
							'name' => 'sample1'
					]],
					[
						'id' => 4,
						'apple_id' => 2,
						'color' => 'Blue Green',
						'name' => 'Test Name',
						'created' => '2006-12-25 05:23:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:36',
						'mytime' => '22:57:17',
						'Parent' => [
							'id' => 2,
							'apple_id' => 1,
							'color' => 'Bright Red 1',
							'name' => 'Bright Red Apple',
							'created' => '2006-11-22 10:43:13',
							'date' => '2014-01-01',
							'modified' => '2006-11-30 18:38:10',
							'mytime' => '22:57:17'
						],
						'Sample' => [
							'id' => 3,
							'apple_id' => 4,
							'name' => 'sample3'
						],
						'Child' => [
							[
								'id' => 6,
								'apple_id' => 4,
								'color' => 'My new appleOrange',
								'name' => 'My new apple',
								'created' => '2006-12-25 05:29:39',
								'date' => '2006-12-25',
								'modified' => '2006-12-25 05:29:39',
								'mytime' => '22:57:17'
			]]]]],
			[
				'Apple' => [
					'id' => 3,
					'apple_id' => 2,
					'color' => 'blue green',
					'name' => 'green blue',
					'created' => '2006-12-25 05:13:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:24',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17'
					],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17'
						],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 1,
					'apple_id' => 3,
					'name' => 'sample1',
					'Apple' => [
						'id' => 3,
						'apple_id' => 2,
						'color' => 'blue green',
						'name' => 'green blue',
						'created' => '2006-12-25 05:13:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:24',
						'mytime' => '22:57:17'
				]],
				'Child' => []
			],
			[
				'Apple' => [
					'id' => 4,
					'apple_id' => 2,
					'color' => 'Blue Green',
					'name' => 'Test Name',
					'created' => '2006-12-25 05:23:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:36',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17'
					],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17'
						],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 3,
					'apple_id' => 4,
					'name' => 'sample3',
					'Apple' => [
						'id' => 4,
						'apple_id' => 2,
						'color' => 'Blue Green',
						'name' => 'Test Name',
						'created' => '2006-12-25 05:23:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:36',
						'mytime' => '22:57:17'
				]],
				'Child' => [
					[
						'id' => 6,
						'apple_id' => 4,
						'color' => 'My new appleOrange',
						'name' => 'My new apple',
						'created' => '2006-12-25 05:29:39',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:39',
						'mytime' => '22:57:17',
						'Parent' => [
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
						],
						'Sample' => [],
							'Child' => [
								[
									'id' => 7,
									'apple_id' => 6,
									'color' => 'Some wierd color',
									'name' => 'Some odd color',
									'created' => '2006-12-25 05:34:21',
									'date' => '2006-12-25',
									'modified' => '2006-12-25 05:34:21',
									'mytime' => '22:57:17'
			]]]]],
			[
				'Apple' => [
					'id' => 5,
					'apple_id' => 5,
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:16',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 5,
					'apple_id' => 5,
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:16',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 5,
						'apple_id' => 5,
						'color' => 'Green',
						'name' => 'Blue Green',
						'created' => '2006-12-25 05:24:06',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:16',
						'mytime' => '22:57:17'
					],
					'Child' => [
						[
							'id' => 5,
							'apple_id' => 5,
							'color' => 'Green',
							'name' => 'Blue Green',
							'created' => '2006-12-25 05:24:06',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:29:16',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 4,
					'apple_id' => 5,
					'name' => 'sample4',
					'Apple' => [
						'id' => 5,
						'apple_id' => 5,
						'color' => 'Green',
						'name' => 'Blue Green',
						'created' => '2006-12-25 05:24:06',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:16',
						'mytime' => '22:57:17'
				]],
				'Child' => [
					[
						'id' => 5,
						'apple_id' => 5,
						'color' => 'Green',
						'name' => 'Blue Green',
						'created' => '2006-12-25 05:24:06',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:16',
						'mytime' => '22:57:17',
						'Parent' => [
							'id' => 5,
							'apple_id' => 5,
							'color' => 'Green',
							'name' => 'Blue Green',
							'created' => '2006-12-25 05:24:06',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:29:16',
							'mytime' => '22:57:17'
						],
						'Sample' => [
							'id' => 4,
							'apple_id' => 5,
							'name' => 'sample4'
						],
						'Child' => [
							[
								'id' => 5,
								'apple_id' => 5,
								'color' => 'Green',
								'name' => 'Blue Green',
								'created' => '2006-12-25 05:24:06',
								'date' => '2006-12-25',
								'modified' => '2006-12-25 05:29:16',
								'mytime' => '22:57:17'
			]]]]],
			[
				'Apple' => [
					'id' => 6,
					'apple_id' => 4,
					'color' => 'My new appleOrange',
					'name' => 'My new apple',
					'created' => '2006-12-25 05:29:39',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:39',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 4,
					'apple_id' => 2,
					'color' => 'Blue Green',
					'name' => 'Test Name',
					'created' => '2006-12-25 05:23:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:36',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 2,
						'apple_id' => 1,
						'color' => 'Bright Red 1',
						'name' => 'Bright Red Apple',
						'created' => '2006-11-22 10:43:13',
						'date' => '2014-01-01',
						'modified' => '2006-11-30 18:38:10',
						'mytime' => '22:57:17'
					],
					'Child' => [
						[
							'id' => 6,
							'apple_id' => 4,
							'color' => 'My new appleOrange',
							'name' => 'My new apple',
							'created' => '2006-12-25 05:29:39',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:29:39',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => '',
					'apple_id' => '',
					'name' => ''
				],
				'Child' => [
					[
						'id' => 7,
						'apple_id' => 6,
						'color' => 'Some wierd color',
						'name' => 'Some odd color',
						'created' => '2006-12-25 05:34:21',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:34:21',
						'mytime' => '22:57:17',
						'Parent' => [
							'id' => 6,
							'apple_id' => 4,
							'color' => 'My new appleOrange',
							'name' => 'My new apple',
							'created' => '2006-12-25 05:29:39',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:29:39',
							'mytime' => '22:57:17'
						],
						'Sample' => []
			]]],
			[
				'Apple' => [
					'id' => 7,
					'apple_id' => 6,
					'color' => 'Some wierd color',
					'name' => 'Some odd color',
					'created' => '2006-12-25 05:34:21',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:34:21',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 6,
					'apple_id' => 4,
					'color' => 'My new appleOrange',
					'name' => 'My new apple',
					'created' => '2006-12-25 05:29:39',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:39',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 4,
						'apple_id' => 2,
						'color' => 'Blue Green',
						'name' => 'Test Name',
						'created' => '2006-12-25 05:23:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:36',
						'mytime' => '22:57:17'
					],
					'Child' => [
						[
							'id' => 7,
							'apple_id' => 6,
							'color' => 'Some wierd color',
							'name' => 'Some odd color',
							'created' => '2006-12-25 05:34:21',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:34:21',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => '',
					'apple_id' => '',
					'name' => ''
				],
				'Child' => []
		]];

		$this->assertEquals($expected, $result);

		$result = $TestModel->Parent->unbindModel(['hasOne' => ['Sample']]);
		$this->assertTrue($result);

		$result = $TestModel->unbindModel(['hasMany' => ['Child']]);
		$this->assertTrue($result);

		$result = $TestModel->find('all');
		$expected = [
			[
				'Apple' => [
					'id' => 1,
					'apple_id' => 2,
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17'
					],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17'
						],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => '',
					'apple_id' => '',
					'name' => ''
			]],
			[
				'Apple' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 1,
					'apple_id' => 2,
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 2,
						'apple_id' => 1,
						'color' => 'Bright Red 1',
						'name' => 'Bright Red Apple',
						'created' => '2006-11-22 10:43:13',
						'date' => '2014-01-01',
						'modified' => '2006-11-30 18:38:10',
						'mytime' => '22:57:17'
					],
					'Child' => [
						[
							'id' => 2,
							'apple_id' => 1,
							'color' => 'Bright Red 1',
							'name' => 'Bright Red Apple',
							'created' => '2006-11-22 10:43:13',
							'date' => '2014-01-01',
							'modified' => '2006-11-30 18:38:10',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 2,
					'apple_id' => 2,
					'name' => 'sample2',
					'Apple' => [
						'id' => 2,
						'apple_id' => 1,
						'color' => 'Bright Red 1',
						'name' => 'Bright Red Apple',
						'created' => '2006-11-22 10:43:13',
						'date' => '2014-01-01',
						'modified' => '2006-11-30 18:38:10',
						'mytime' => '22:57:17'
			]]],
			[
				'Apple' => [
				'id' => 3,
				'apple_id' => 2,
				'color' => 'blue green',
				'name' => 'green blue',
				'created' => '2006-12-25 05:13:36',
				'date' => '2006-12-25',
				'modified' => '2006-12-25 05:23:24',
				'mytime' => '22:57:17'
			],
			'Parent' => [
				'id' => 2,
				'apple_id' => 1,
				'color' => 'Bright Red 1',
				'name' => 'Bright Red Apple',
				'created' => '2006-11-22 10:43:13',
				'date' => '2014-01-01',
				'modified' => '2006-11-30 18:38:10',
				'mytime' => '22:57:17',
				'Parent' => [
					'id' => 1,
					'apple_id' => 2,
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17'
				],
				'Child' => [
					[
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17'
					],
					[
						'id' => 3,
						'apple_id' => 2,
						'color' => 'blue green',
						'name' => 'green blue',
						'created' => '2006-12-25 05:13:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:24',
						'mytime' => '22:57:17'
					],
					[
						'id' => 4,
						'apple_id' => 2,
						'color' => 'Blue Green',
						'name' => 'Test Name',
						'created' => '2006-12-25 05:23:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:36',
						'mytime' => '22:57:17'
			]]],
			'Sample' => [
				'id' => 1,
				'apple_id' => 3,
				'name' => 'sample1',
				'Apple' => [
					'id' => 3,
					'apple_id' => 2,
					'color' => 'blue green',
					'name' => 'green blue',
					'created' => '2006-12-25 05:13:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:24',
					'mytime' => '22:57:17'
		]]],
		[
			'Apple' => [
				'id' => 4,
				'apple_id' => 2,
				'color' => 'Blue Green',
				'name' => 'Test Name',
				'created' => '2006-12-25 05:23:36',
				'date' => '2006-12-25',
				'modified' => '2006-12-25 05:23:36',
				'mytime' => '22:57:17'
			],
			'Parent' => [
				'id' => 2,
				'apple_id' => 1,
				'color' => 'Bright Red 1',
				'name' => 'Bright Red Apple',
				'created' => '2006-11-22 10:43:13',
				'date' => '2014-01-01',
				'modified' => '2006-11-30 18:38:10',
				'mytime' => '22:57:17',
				'Parent' => [
					'id' => 1,
					'apple_id' => 2,
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17'
				],
				'Child' => [
					[
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17'
					],
					[
						'id' => 3,
						'apple_id' => 2,
						'color' => 'blue green',
						'name' => 'green blue',
						'created' => '2006-12-25 05:13:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:24',
						'mytime' => '22:57:17'
					],
					[
						'id' => 4,
						'apple_id' => 2,
						'color' => 'Blue Green',
						'name' => 'Test Name',
						'created' => '2006-12-25 05:23:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:36',
						'mytime' => '22:57:17'
			]]],
			'Sample' => [
				'id' => 3,
				'apple_id' => 4,
				'name' => 'sample3',
				'Apple' => [
					'id' => 4,
					'apple_id' => 2,
					'color' => 'Blue Green',
					'name' => 'Test Name',
					'created' => '2006-12-25 05:23:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:36',
					'mytime' => '22:57:17'
		]]],
		[
			'Apple' => [
				'id' => 5,
				'apple_id' => 5,
				'color' => 'Green',
				'name' => 'Blue Green',
				'created' => '2006-12-25 05:24:06',
				'date' => '2006-12-25',
				'modified' => '2006-12-25 05:29:16',
				'mytime' => '22:57:17'
			],
			'Parent' => [
				'id' => 5,
				'apple_id' => 5,
				'color' => 'Green',
				'name' => 'Blue Green',
				'created' => '2006-12-25 05:24:06',
				'date' => '2006-12-25',
				'modified' => '2006-12-25 05:29:16',
				'mytime' => '22:57:17',
				'Parent' => [
					'id' => 5,
					'apple_id' => 5,
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:16',
					'mytime' => '22:57:17'
				],
				'Child' => [
					[
						'id' => 5,
						'apple_id' => 5,
						'color' => 'Green',
						'name' => 'Blue Green',
						'created' => '2006-12-25 05:24:06',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:16',
						'mytime' => '22:57:17'
			]]],
			'Sample' => [
				'id' => 4,
				'apple_id' => 5,
				'name' => 'sample4',
				'Apple' => [
					'id' => 5,
					'apple_id' => 5,
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:16',
					'mytime' => '22:57:17'
		]]],
		[
			'Apple' => [
				'id' => 6,
				'apple_id' => 4,
				'color' => 'My new appleOrange',
				'name' => 'My new apple',
				'created' => '2006-12-25 05:29:39',
				'date' => '2006-12-25',
				'modified' => '2006-12-25 05:29:39',
				'mytime' => '22:57:17'
			],
			'Parent' => [
				'id' => 4,
				'apple_id' => 2,
				'color' => 'Blue Green',
				'name' => 'Test Name',
				'created' => '2006-12-25 05:23:36',
				'date' => '2006-12-25',
				'modified' => '2006-12-25 05:23:36',
				'mytime' => '22:57:17',
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17'
				],
				'Child' => [
					[
						'id' => 6,
						'apple_id' => 4,
						'color' => 'My new appleOrange',
						'name' => 'My new apple',
						'created' => '2006-12-25 05:29:39',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:39',
						'mytime' => '22:57:17'
			]]],
			'Sample' => [
				'id' => '',
				'apple_id' => '',
				'name' => ''
		]],
		[
			'Apple' => [
				'id' => 7,
				'apple_id' => 6,
				'color' => 'Some wierd color',
				'name' => 'Some odd color',
				'created' => '2006-12-25 05:34:21',
				'date' => '2006-12-25',
				'modified' => '2006-12-25 05:34:21',
				'mytime' => '22:57:17'
			],
			'Parent' => [
				'id' => 6,
				'apple_id' => 4,
				'color' => 'My new appleOrange',
				'name' => 'My new apple',
				'created' => '2006-12-25 05:29:39',
				'date' => '2006-12-25',
				'modified' => '2006-12-25 05:29:39',
				'mytime' => '22:57:17',
				'Parent' => [
					'id' => 4,
					'apple_id' => 2,
					'color' => 'Blue Green',
					'name' => 'Test Name',
					'created' => '2006-12-25 05:23:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:36',
					'mytime' => '22:57:17'
				],
				'Child' => [
					[
						'id' => 7,
						'apple_id' => 6,
						'color' => 'Some wierd color',
						'name' => 'Some odd color',
						'created' => '2006-12-25 05:34:21',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:34:21',
						'mytime' => '22:57:17'
			]]],
			'Sample' => [
				'id' => '',
				'apple_id' => '',
				'name' => ''
		]]];

		$this->assertEquals($expected, $result);

		$result = $TestModel->unbindModel(['hasMany' => 'Child']);
		$this->assertTrue($result);

		$result = $TestModel->Sample->unbindModel(['belongsTo' => 'Apple']);
		$this->assertTrue($result);

		$result = $TestModel->find('all');
		$expected = [
			[
				'Apple' => [
					'id' => 1,
					'apple_id' => 2,
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17'
					],
					'Sample' => [
						'id' => 2,
						'apple_id' => 2,
						'name' => 'sample2'
					],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17'
						],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => '',
					'apple_id' => '',
					'name' => ''
			]],
			[
				'Apple' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 1,
					'apple_id' => 2,
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 2,
						'apple_id' => 1,
						'color' => 'Bright Red 1',
						'name' => 'Bright Red Apple',
						'created' => '2006-11-22 10:43:13',
						'date' => '2014-01-01',
						'modified' => '2006-11-30 18:38:10',
						'mytime' => '22:57:17'
					],
					'Sample' => [],
					'Child' => [
						[
							'id' => 2,
							'apple_id' => 1,
							'color' => 'Bright Red 1',
							'name' => 'Bright Red Apple',
							'created' => '2006-11-22 10:43:13',
							'date' => '2014-01-01',
							'modified' => '2006-11-30 18:38:10',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 2,
					'apple_id' => 2,
					'name' => 'sample2'
			]],
			[
				'Apple' => [
					'id' => 3,
					'apple_id' => 2,
					'color' => 'blue green',
					'name' => 'green blue',
					'created' => '2006-12-25 05:13:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:24',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17'
					],
					'Sample' => [
						'id' => 2,
						'apple_id' => 2,
						'name' => 'sample2'
					],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17'
						],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 1,
					'apple_id' => 3,
					'name' => 'sample1'
			]],
			[
				'Apple' => [
					'id' => 4,
					'apple_id' => 2,
					'color' => 'Blue Green',
					'name' => 'Test Name',
					'created' => '2006-12-25 05:23:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:36',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 1,
						'apple_id' => 2,
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17'
					],
					'Sample' => [
						'id' => 2,
						'apple_id' => 2,
						'name' => 'sample2'
					],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17'
						],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 3,
					'apple_id' => 4,
					'name' => 'sample3'
			]],
			[
				'Apple' => [
					'id' => 5,
					'apple_id' => 5,
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:16',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 5,
					'apple_id' => 5,
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:16',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 5,
						'apple_id' => 5,
						'color' => 'Green',
						'name' => 'Blue Green',
						'created' => '2006-12-25 05:24:06',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:16',
						'mytime' => '22:57:17'
					],
					'Sample' => [
						'id' => 4,
						'apple_id' => 5,
						'name' => 'sample4'
					],
					'Child' => [
						[
							'id' => 5,
							'apple_id' => 5,
							'color' => 'Green',
							'name' => 'Blue Green',
							'created' => '2006-12-25 05:24:06',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:29:16',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 4,
					'apple_id' => 5,
					'name' => 'sample4'
			]],
			[
				'Apple' => [
					'id' => 6,
					'apple_id' => 4,
					'color' => 'My new appleOrange',
					'name' => 'My new apple',
					'created' => '2006-12-25 05:29:39',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:39',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 4,
					'apple_id' => 2,
					'color' => 'Blue Green',
					'name' => 'Test Name',
					'created' => '2006-12-25 05:23:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:36',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 2,
						'apple_id' => 1,
						'color' => 'Bright Red 1',
						'name' => 'Bright Red Apple',
						'created' => '2006-11-22 10:43:13',
						'date' => '2014-01-01',
						'modified' => '2006-11-30 18:38:10',
						'mytime' => '22:57:17'
					],
					'Sample' => [
						'id' => 3,
						'apple_id' => 4,
						'name' => 'sample3'
					],
					'Child' => [
						[
							'id' => 6,
							'apple_id' => 4,
							'color' => 'My new appleOrange',
							'name' => 'My new apple',
							'created' => '2006-12-25 05:29:39',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:29:39',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => '',
					'apple_id' => '',
					'name' => ''
			]],
			[
				'Apple' => [
					'id' => 7,
					'apple_id' => 6,
					'color' => 'Some wierd color',
					'name' => 'Some odd color',
					'created' => '2006-12-25 05:34:21',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:34:21',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 6,
					'apple_id' => 4,
					'color' => 'My new appleOrange',
					'name' => 'My new apple',
					'created' => '2006-12-25 05:29:39',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:39',
					'mytime' => '22:57:17',
					'Parent' => [
						'id' => 4,
						'apple_id' => 2,
						'color' => 'Blue Green',
						'name' => 'Test Name',
						'created' => '2006-12-25 05:23:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:36',
						'mytime' => '22:57:17'
					],
					'Sample' => [],
					'Child' => [
						[
							'id' => 7,
							'apple_id' => 6,
							'color' => 'Some wierd color',
							'name' => 'Some odd color',
							'created' => '2006-12-25 05:34:21',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:34:21',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => '',
					'apple_id' => '',
					'name' => ''
		]]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->Parent->unbindModel(['belongsTo' => ['Parent']]);
		$this->assertTrue($result);

		$result = $TestModel->unbindModel(['hasMany' => ['Child']]);
		$this->assertTrue($result);

		$result = $TestModel->find('all');
		$expected = [
			[
				'Apple' => [
					'id' => 1,
					'apple_id' => 2,
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Sample' => [
						'id' => 2,
						'apple_id' => 2,
						'name' => 'sample2'
					],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17'
						],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => '',
					'apple_id' => '',
					'name' => ''
			]],
			[
				'Apple' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 1,
					'apple_id' => 2,
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17',
					'Sample' => [],
						'Child' => [
							[
								'id' => 2,
								'apple_id' => 1,
								'color' => 'Bright Red 1',
								'name' => 'Bright Red Apple',
								'created' => '2006-11-22 10:43:13',
								'date' => '2014-01-01',
								'modified' => '2006-11-30 18:38:10',
								'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 2,
					'apple_id' => 2,
					'name' => 'sample2',
					'Apple' => [
						'id' => 2,
						'apple_id' => 1,
						'color' => 'Bright Red 1',
						'name' => 'Bright Red Apple',
						'created' => '2006-11-22 10:43:13',
						'date' => '2014-01-01',
						'modified' => '2006-11-30 18:38:10',
						'mytime' => '22:57:17'
			]]],
			[
				'Apple' => [
					'id' => 3,
					'apple_id' => 2,
					'color' => 'blue green',
					'name' => 'green blue',
					'created' => '2006-12-25 05:13:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:24',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Sample' => [
						'id' => 2,
						'apple_id' => 2,
						'name' => 'sample2'
					],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17'
						],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 1,
					'apple_id' => 3,
					'name' => 'sample1',
					'Apple' => [
						'id' => 3,
						'apple_id' => 2,
						'color' => 'blue green',
						'name' => 'green blue',
						'created' => '2006-12-25 05:13:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:24',
						'mytime' => '22:57:17'
			]]],
			[
				'Apple' => [
					'id' => 4,
					'apple_id' => 2,
					'color' => 'Blue Green',
					'name' => 'Test Name',
					'created' => '2006-12-25 05:23:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:36',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 2,
					'apple_id' => 1,
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17',
					'Sample' => [
						'id' => 2,
						'apple_id' => 2,
						'name' => 'sample2'
					],
					'Child' => [
						[
							'id' => 1,
							'apple_id' => 2,
							'color' => 'Red 1',
							'name' => 'Red Apple 1',
							'created' => '2006-11-22 10:38:58',
							'date' => '1951-01-04',
							'modified' => '2006-12-01 13:31:26',
							'mytime' => '22:57:17'
						],
						[
							'id' => 3,
							'apple_id' => 2,
							'color' => 'blue green',
							'name' => 'green blue',
							'created' => '2006-12-25 05:13:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:24',
							'mytime' => '22:57:17'
						],
						[
							'id' => 4,
							'apple_id' => 2,
							'color' => 'Blue Green',
							'name' => 'Test Name',
							'created' => '2006-12-25 05:23:36',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:23:36',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 3,
					'apple_id' => 4,
					'name' => 'sample3',
					'Apple' => [
						'id' => 4,
						'apple_id' => 2,
						'color' => 'Blue Green',
						'name' => 'Test Name',
						'created' => '2006-12-25 05:23:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:36',
						'mytime' => '22:57:17'
			]]],
			[
				'Apple' => [
					'id' => 5,
					'apple_id' => 5,
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' =>
					'2006-12-25 05:29:16',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 5,
					'apple_id' => 5,
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:16',
					'mytime' => '22:57:17',
					'Sample' => [
						'id' => 4,
						'apple_id' => 5,
						'name' => 'sample4'
					],
					'Child' => [
						[
							'id' => 5,
							'apple_id' => 5,
							'color' => 'Green',
							'name' => 'Blue Green',
							'created' => '2006-12-25 05:24:06',
							'date' => '2006-12-25',
							'modified' => '2006-12-25 05:29:16',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => 4,
					'apple_id' => 5,
					'name' => 'sample4',
					'Apple' => [
						'id' => 5,
						'apple_id' => 5,
						'color' => 'Green',
						'name' => 'Blue Green',
						'created' => '2006-12-25 05:24:06',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:16',
						'mytime' => '22:57:17'
			]]],
			[
				'Apple' => [
					'id' => 6,
					'apple_id' => 4,
					'color' => 'My new appleOrange',
					'name' => 'My new apple',
					'created' => '2006-12-25 05:29:39',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:39',
					'mytime' => '22:57:17'],
					'Parent' => [
						'id' => 4,
						'apple_id' => 2,
						'color' => 'Blue Green',
						'name' => 'Test Name',
						'created' => '2006-12-25 05:23:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:36',
						'mytime' => '22:57:17',
						'Sample' => [
							'id' => 3,
							'apple_id' => 4,
							'name' => 'sample3'
						],
						'Child' => [
							[
								'id' => 6,
								'apple_id' => 4,
								'color' => 'My new appleOrange',
								'name' => 'My new apple',
								'created' => '2006-12-25 05:29:39',
								'date' => '2006-12-25',
								'modified' => '2006-12-25 05:29:39',
								'mytime' => '22:57:17'
					]]],
					'Sample' => [
						'id' => '',
						'apple_id' => '',
						'name' => ''
			]],
			[
				'Apple' => [
					'id' => 7,
					'apple_id' => 6,
					'color' => 'Some wierd color',
					'name' => 'Some odd color',
					'created' => '2006-12-25 05:34:21',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:34:21',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => 6,
					'apple_id' => 4,
					'color' => 'My new appleOrange',
					'name' => 'My new apple',
					'created' => '2006-12-25 05:29:39',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:39',
					'mytime' => '22:57:17',
					'Sample' => [],
					'Child' => [
						[
							'id' => 7,
							'apple_id' => 6,
							'color' => 'Some wierd color',
							'name' => 'Some odd color',
							'created' => '2006-12-25 05:34:21',
							'date' => '2006-12-25', 'modified' =>
							'2006-12-25 05:34:21',
							'mytime' => '22:57:17'
				]]],
				'Sample' => [
					'id' => '',
					'apple_id' => '',
					'name' => ''
		]]];
		$this->assertEquals($expected, $result);
	}

/**
 * testSelfAssociationAfterFind method
 *
 * @return void
 */
	public function testSelfAssociationAfterFind() {
		$this->loadFixtures('Apple', 'Sample');
		$afterFindModel = new NodeAfterFind();
		$afterFindModel->recursive = 3;
		$afterFindData = $afterFindModel->find('all');

		$duplicateModel = new NodeAfterFind();
		$duplicateModel->recursive = 3;

		$noAfterFindModel = new NodeNoAfterFind();
		$noAfterFindModel->recursive = 3;
		$noAfterFindData = $noAfterFindModel->find('all');

		$this->assertFalse($afterFindModel == $noAfterFindModel);
		$this->assertEquals($afterFindData, $noAfterFindData);
	}

/**
 * Test that afterFind can completely unset data.
 *
 * @return void
 */
	public function testAfterFindUnset() {
		$this->loadFixtures('Article', 'Comment', 'User');
		$model = new CustomArticle();
		$model->bindModel([
			'hasMany' => [
				'ModifiedComment' => [
					'className' => 'ModifiedComment',
					'foreignKey' => 'article_id',
				]
			]
		]);
		$model->ModifiedComment->remove = true;
		$result = $model->find('all');
		$this->assertTrue(
			empty($result[0]['ModifiedComment']),
			'Zeroith row should be removed by afterFind'
		);
	}

/**
 * testFindThreadedNoParent method
 *
 * @return void
 */
	public function testFindThreadedNoParent() {
		$this->loadFixtures('Apple', 'Sample');
		$Apple = new Apple();
		$result = $Apple->find('threaded');
		$result = Hash::extract($result, '{n}.children');
		$expected = [[], [], [], [], [], [], []];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindThreaded method
 *
 * @return void
 */
	public function testFindThreaded() {
		$this->loadFixtures('Person');
		$Model = new Person();
		$Model->recursive = -1;
		$result = $Model->find('threaded');
		$result = Hash::extract($result, '{n}.children');
		$expected = [[], [], [], [], [], [], []];
		$this->assertEquals($expected, $result);

		$result = $Model->find('threaded', ['parent' => 'mother_id']);
		$expected = [
			[
				'Person' => [
					'id' => '4',
					'name' => 'mother - grand mother',
					'mother_id' => '0',
					'father_id' => '0'
				],
				'children' => [
					[
						'Person' => [
							'id' => '2',
							'name' => 'mother',
							'mother_id' => '4',
							'father_id' => '5'
						],
						'children' => [
							[
								'Person' => [
									'id' => '1',
									'name' => 'person',
									'mother_id' => '2',
									'father_id' => '3'
								],
								'children' => []
							]
						]
					]
				]
			],
			[
				'Person' => [
					'id' => '5',
					'name' => 'mother - grand father',
					'mother_id' => '0',
					'father_id' => '0'
				],
				'children' => []
			],
			[
				'Person' => [
					'id' => '6',
					'name' => 'father - grand mother',
					'mother_id' => '0',
					'father_id' => '0'
				],
				'children' => [
					[
						'Person' => [
							'id' => '3',
							'name' => 'father',
							'mother_id' => '6',
							'father_id' => '7'
						],
						'children' => []
					]
				]
			],
			[
				'Person' => [
					'id' => '7',
					'name' => 'father - grand father',
					'mother_id' => '0',
					'father_id' => '0'
				],
				'children' => []
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindAllThreaded method
 *
 * @return void
 */
	public function testFindAllThreaded() {
		$this->loadFixtures('Category');
		$TestModel = new Category();

		$result = $TestModel->find('threaded');
		$expected = [
			[
				'Category' => [
					'id' => '1',
					'parent_id' => '0',
					'name' => 'Category 1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'children' => [
					[
						'Category' => [
							'id' => '2',
							'parent_id' => '1',
							'name' => 'Category 1.1',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31'
						],
						'children' => [
							['Category' => [
								'id' => '7',
								'parent_id' => '2',
								'name' => 'Category 1.1.1',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31'],
								'children' => []],
							['Category' => [
								'id' => '8',
								'parent_id' => '2',
								'name' => 'Category 1.1.2',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31'],
								'children' => []]]
					],
					[
						'Category' => [
							'id' => '3',
							'parent_id' => '1',
							'name' => 'Category 1.2',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31'
						],
						'children' => []
					]
				]
			],
			[
				'Category' => [
					'id' => '4',
					'parent_id' => '0',
					'name' => 'Category 2',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'children' => []
			],
			[
				'Category' => [
					'id' => '5',
					'parent_id' => '0',
					'name' => 'Category 3',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'children' => [
					[
						'Category' => [
							'id' => '6',
							'parent_id' => '5',
							'name' => 'Category 3.1',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31'
						],
						'children' => []
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('threaded', [
			'conditions' => ['Category.name LIKE' => 'Category 1%']
		]);

		$expected = [
			[
				'Category' => [
					'id' => '1',
					'parent_id' => '0',
					'name' => 'Category 1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'children' => [
					[
						'Category' => [
							'id' => '2',
							'parent_id' => '1',
							'name' => 'Category 1.1',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31'
						],
						'children' => [
							['Category' => [
								'id' => '7',
								'parent_id' => '2',
								'name' => 'Category 1.1.1',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31'],
								'children' => []],
							['Category' => [
								'id' => '8',
								'parent_id' => '2',
								'name' => 'Category 1.1.2',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31'],
								'children' => []]]
					],
					[
						'Category' => [
							'id' => '3',
							'parent_id' => '1',
							'name' => 'Category 1.2',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31'
						],
						'children' => []
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('threaded', [
			'fields' => 'id, parent_id, name'
		]);

		$expected = [
			[
				'Category' => [
					'id' => '1',
					'parent_id' => '0',
					'name' => 'Category 1'
				],
				'children' => [
					[
						'Category' => [
							'id' => '2',
							'parent_id' => '1',
							'name' => 'Category 1.1'
						],
						'children' => [
							['Category' => [
								'id' => '7',
								'parent_id' => '2',
								'name' => 'Category 1.1.1'],
								'children' => []],
							['Category' => [
								'id' => '8',
								'parent_id' => '2',
								'name' => 'Category 1.1.2'],
								'children' => []]]
					],
					[
						'Category' => [
							'id' => '3',
							'parent_id' => '1',
							'name' => 'Category 1.2'
						],
						'children' => []
					]
				]
			],
			[
				'Category' => [
					'id' => '4',
					'parent_id' => '0',
					'name' => 'Category 2'
				],
				'children' => []
			],
			[
				'Category' => [
					'id' => '5',
					'parent_id' => '0',
					'name' => 'Category 3'
				],
				'children' => [
					[
						'Category' => [
							'id' => '6',
							'parent_id' => '5',
							'name' => 'Category 3.1'
						],
						'children' => []
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('threaded', ['order' => 'id DESC']);

		$expected = [
			[
				'Category' => [
					'id' => 5,
					'parent_id' => 0,
					'name' => 'Category 3',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'children' => [
					[
						'Category' => [
							'id' => 6,
							'parent_id' => 5,
							'name' => 'Category 3.1',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31'
						],
						'children' => []
					]
				]
			],
			[
				'Category' => [
					'id' => 4,
					'parent_id' => 0,
					'name' => 'Category 2',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'children' => []
			],
			[
				'Category' => [
					'id' => 1,
					'parent_id' => 0,
					'name' => 'Category 1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'children' => [
					[
						'Category' => [
							'id' => 3,
							'parent_id' => 1,
							'name' => 'Category 1.2',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31'
						],
						'children' => []
					],
					[
						'Category' => [
							'id' => 2,
							'parent_id' => 1,
							'name' => 'Category 1.1',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31'
						],
						'children' => [
							['Category' => [
								'id' => '8',
								'parent_id' => '2',
								'name' => 'Category 1.1.2',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31'],
								'children' => []],
							['Category' => [
								'id' => '7',
								'parent_id' => '2',
								'name' => 'Category 1.1.1',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31'],
								'children' => []]]
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('threaded', [
			'conditions' => ['Category.name LIKE' => 'Category 3%']
		]);
		$expected = [
			[
				'Category' => [
					'id' => '5',
					'parent_id' => '0',
					'name' => 'Category 3',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'children' => [
					[
						'Category' => [
							'id' => '6',
							'parent_id' => '5',
							'name' => 'Category 3.1',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31'
						],
						'children' => []
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('threaded', [
			'conditions' => ['Category.name LIKE' => 'Category 1.1%']
		]);
		$expected = [
				['Category' =>
					[
						'id' => '2',
						'parent_id' => '1',
						'name' => 'Category 1.1',
						'created' => '2007-03-18 15:30:23',
						'updated' => '2007-03-18 15:32:31'],
						'children' => [
							['Category' => [
								'id' => '7',
								'parent_id' => '2',
								'name' => 'Category 1.1.1',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31'],
								'children' => []],
							['Category' => [
								'id' => '8',
								'parent_id' => '2',
								'name' => 'Category 1.1.2',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31'],
								'children' => []]]]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('threaded', [
			'fields' => 'id, parent_id, name',
			'conditions' => ['Category.id !=' => 2]
		]);
		$expected = [
			[
				'Category' => [
					'id' => '1',
					'parent_id' => '0',
					'name' => 'Category 1'
				],
				'children' => [
					[
						'Category' => [
							'id' => '3',
							'parent_id' => '1',
							'name' => 'Category 1.2'
						],
						'children' => []
					]
				]
			],
			[
				'Category' => [
					'id' => '4',
					'parent_id' => '0',
					'name' => 'Category 2'
				],
				'children' => []
			],
			[
				'Category' => [
					'id' => '5',
					'parent_id' => '0',
					'name' => 'Category 3'
				],
				'children' => [
					[
						'Category' => [
							'id' => '6',
							'parent_id' => '5',
							'name' => 'Category 3.1'
						],
						'children' => []
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', [
			'fields' => 'id, name, parent_id',
			'conditions' => ['Category.id !=' => 1]
		]);
		$expected = [
			['Category' => [
				'id' => '2',
				'name' => 'Category 1.1',
				'parent_id' => '1'
			]],
			['Category' => [
				'id' => '3',
				'name' => 'Category 1.2',
				'parent_id' => '1'
			]],
			['Category' => [
				'id' => '4',
				'name' => 'Category 2',
				'parent_id' => '0'
			]],
			['Category' => [
				'id' => '5',
				'name' => 'Category 3',
				'parent_id' => '0'
			]],
			['Category' => [
				'id' => '6',
				'name' => 'Category 3.1',
				'parent_id' => '5'
			]],
			['Category' => [
				'id' => '7',
				'name' => 'Category 1.1.1',
				'parent_id' => '2'
			]],
			['Category' => [
				'id' => '8',
				'name' => 'Category 1.1.2',
				'parent_id' => '2'
		]]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('threaded', [
			'fields' => 'id, parent_id, name',
			'conditions' => ['Category.id !=' => 1]
		]);
		$expected = [
			[
				'Category' => [
					'id' => '2',
					'parent_id' => '1',
					'name' => 'Category 1.1'
				],
				'children' => [
					['Category' => [
						'id' => '7',
						'parent_id' => '2',
						'name' => 'Category 1.1.1'],
						'children' => []],
					['Category' => [
						'id' => '8',
						'parent_id' => '2',
						'name' => 'Category 1.1.2'],
						'children' => []]]
			],
			[
				'Category' => [
					'id' => '3',
					'parent_id' => '1',
					'name' => 'Category 1.2'
				],
				'children' => []
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test find('neighbors')
 *
 * @return void
 */
	public function testFindNeighbors() {
		$this->loadFixtures('User', 'Article', 'Comment', 'Tag', 'ArticlesTag', 'Attachment');
		$TestModel = new Article();

		$TestModel->id = 1;
		$result = $TestModel->find('neighbors', ['fields' => ['id']]);

		$this->assertNull($result['prev']);
		$this->assertEquals(['id' => 2], $result['next']['Article']);
		$this->assertEquals(2, count($result['next']['Comment']));
		$this->assertEquals(2, count($result['next']['Tag']));

		$TestModel->id = 2;
		$TestModel->recursive = 0;
		$result = $TestModel->find('neighbors', [
			'fields' => ['id']
		]);

		$expected = [
			'prev' => [
				'Article' => [
					'id' => 1
			]],
			'next' => [
				'Article' => [
					'id' => 3
		]]];
		$this->assertEquals($expected, $result);

		$TestModel->id = 3;
		$TestModel->recursive = 1;
		$result = $TestModel->find('neighbors', ['fields' => ['id']]);

		$this->assertNull($result['next']);
		$this->assertEquals(['id' => 2], $result['prev']['Article']);
		$this->assertEquals(2, count($result['prev']['Comment']));
		$this->assertEquals(2, count($result['prev']['Tag']));

		$TestModel->id = 1;
		$result = $TestModel->find('neighbors', ['recursive' => -1]);
		$expected = [
			'prev' => null,
			'next' => [
				'Article' => [
					'id' => 2,
					'user_id' => 3,
					'title' => 'Second Article',
					'body' => 'Second Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31'
				]
			]
		];
		$this->assertEquals($expected, $result);

		$TestModel->id = 2;
		$result = $TestModel->find('neighbors', ['recursive' => -1]);
		$expected = [
			'prev' => [
				'Article' => [
					'id' => 1,
					'user_id' => 1,
					'title' => 'First Article',
					'body' => 'First Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				]
			],
			'next' => [
				'Article' => [
					'id' => 3,
					'user_id' => 1,
					'title' => 'Third Article',
					'body' => 'Third Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				]
			]
		];
		$this->assertEquals($expected, $result);

		$TestModel->id = 3;
		$result = $TestModel->find('neighbors', ['recursive' => -1]);
		$expected = [
			'prev' => [
				'Article' => [
					'id' => 2,
					'user_id' => 3,
					'title' => 'Second Article',
					'body' => 'Second Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31'
				]
			],
			'next' => null
		];
		$this->assertEquals($expected, $result);

		$TestModel->recursive = 0;
		$TestModel->id = 1;
		$one = $TestModel->read();
		$TestModel->id = 2;
		$two = $TestModel->read();
		$TestModel->id = 3;
		$three = $TestModel->read();

		$TestModel->id = 1;
		$result = $TestModel->find('neighbors');
		$expected = ['prev' => null, 'next' => $two];
		$this->assertEquals($expected, $result);

		$TestModel->id = 2;
		$result = $TestModel->find('neighbors');
		$expected = ['prev' => $one, 'next' => $three];
		$this->assertEquals($expected, $result);

		$TestModel->id = 3;
		$result = $TestModel->find('neighbors');
		$expected = ['prev' => $two, 'next' => null];
		$this->assertEquals($expected, $result);

		$TestModel->recursive = 2;
		$TestModel->id = 1;
		$one = $TestModel->read();
		$TestModel->id = 2;
		$two = $TestModel->read();
		$TestModel->id = 3;
		$three = $TestModel->read();

		$TestModel->id = 1;
		$result = $TestModel->find('neighbors', ['recursive' => 2]);
		$expected = ['prev' => null, 'next' => $two];
		$this->assertEquals($expected, $result);

		$TestModel->id = 2;
		$result = $TestModel->find('neighbors', ['recursive' => 2]);
		$expected = ['prev' => $one, 'next' => $three];
		$this->assertEquals($expected, $result);

		$TestModel->id = 3;
		$result = $TestModel->find('neighbors', ['recursive' => 2]);
		$expected = ['prev' => $two, 'next' => null];
		$this->assertEquals($expected, $result);
	}

/**
 * Test find(neighbors) with missing fields so no neighbors are found.
 *
 * @return void
 */
	public function testFindNeighborsNoPrev() {
		$this->loadFixtures('User', 'Article', 'Comment', 'Tag', 'ArticlesTag', 'Attachment');
		$Article = new Article();

		$result = $Article->find('neighbors', [
			'field' => 'Article.title',
			'value' => 'Second Article',
			'fields' => ['id'],
			'conditions' => [
				'Article.title LIKE' => '%Article%'
			],
			'recursive' => 0,
		]);
		$expected = [
			'prev' => null,
			'next' => null
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindCombinedRelations method
 *
 * @return void
 */
	public function testFindCombinedRelations() {
		$this->skipIf($this->db instanceof Sqlserver, 'The test of testRecursiveUnbind test is not compatible with SQL Server, because it check for time columns.');

		$this->loadFixtures('Apple', 'Sample');
		$TestModel = new Apple();

		$result = $TestModel->find('all');

		$expected = [
			[
				'Apple' => [
					'id' => '1',
					'apple_id' => '2',
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => '2',
					'apple_id' => '1',
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17'
				],
				'Sample' => [
					'id' => null,
					'apple_id' => null,
					'name' => null
				],
				'Child' => [
					[
						'id' => '2',
						'apple_id' => '1',
						'color' => 'Bright Red 1',
						'name' => 'Bright Red Apple',
						'created' => '2006-11-22 10:43:13',
						'date' => '2014-01-01',
						'modified' => '2006-11-30 18:38:10',
						'mytime' => '22:57:17'
			]]],
			[
				'Apple' => [
					'id' => '2',
					'apple_id' => '1',
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => '1',
					'apple_id' => '2',
					'color' => 'Red 1',
					'name' => 'Red Apple 1',
					'created' => '2006-11-22 10:38:58',
					'date' => '1951-01-04',
					'modified' => '2006-12-01 13:31:26',
					'mytime' => '22:57:17'
				],
				'Sample' => [
					'id' => '2',
					'apple_id' => '2',
					'name' => 'sample2'
				],
				'Child' => [
					[
						'id' => '1',
						'apple_id' => '2',
						'color' => 'Red 1',
						'name' => 'Red Apple 1',
						'created' => '2006-11-22 10:38:58',
						'date' => '1951-01-04',
						'modified' => '2006-12-01 13:31:26',
						'mytime' => '22:57:17'
					],
					[
						'id' => '3',
						'apple_id' => '2',
						'color' => 'blue green',
						'name' => 'green blue',
						'created' => '2006-12-25 05:13:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:24',
						'mytime' => '22:57:17'
					],
					[
						'id' => '4',
						'apple_id' => '2',
						'color' => 'Blue Green',
						'name' => 'Test Name',
						'created' => '2006-12-25 05:23:36',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:23:36',
						'mytime' => '22:57:17'
			]]],
			[
				'Apple' => [
					'id' => '3',
					'apple_id' => '2',
					'color' => 'blue green',
					'name' => 'green blue',
					'created' => '2006-12-25 05:13:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:24',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => '2',
					'apple_id' => '1',
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17'
				],
				'Sample' => [
					'id' => '1',
					'apple_id' => '3',
					'name' => 'sample1'
				],
				'Child' => []
			],
			[
				'Apple' => [
					'id' => '4',
					'apple_id' => '2',
					'color' => 'Blue Green',
					'name' => 'Test Name',
					'created' => '2006-12-25 05:23:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:36',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => '2',
					'apple_id' => '1',
					'color' => 'Bright Red 1',
					'name' => 'Bright Red Apple',
					'created' => '2006-11-22 10:43:13',
					'date' => '2014-01-01',
					'modified' => '2006-11-30 18:38:10',
					'mytime' => '22:57:17'
				],
				'Sample' => [
					'id' => '3',
					'apple_id' => '4',
					'name' => 'sample3'
				],
				'Child' => [
					[
						'id' => '6',
						'apple_id' => '4',
						'color' => 'My new appleOrange',
						'name' => 'My new apple',
						'created' => '2006-12-25 05:29:39',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:39',
						'mytime' => '22:57:17'
			]]],
			[
				'Apple' => [
					'id' => '5',
					'apple_id' => '5',
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:16',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => '5',
					'apple_id' => '5',
					'color' => 'Green',
					'name' => 'Blue Green',
					'created' => '2006-12-25 05:24:06',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:16',
					'mytime' => '22:57:17'
				],
				'Sample' => [
					'id' => '4',
					'apple_id' => '5',
					'name' => 'sample4'
				],
				'Child' => [
					[
						'id' => '5',
						'apple_id' => '5',
						'color' => 'Green',
						'name' => 'Blue Green',
						'created' => '2006-12-25 05:24:06',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:29:16',
						'mytime' => '22:57:17'
			]]],
			[
				'Apple' => [
					'id' => '6',
					'apple_id' => '4',
					'color' => 'My new appleOrange',
					'name' => 'My new apple',
					'created' => '2006-12-25 05:29:39',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:39',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => '4',
					'apple_id' => '2',
					'color' => 'Blue Green',
					'name' => 'Test Name',
					'created' => '2006-12-25 05:23:36',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:23:36',
					'mytime' => '22:57:17'
				],
				'Sample' => [
					'id' => null,
					'apple_id' => null,
					'name' => null
				],
				'Child' => [
					[
						'id' => '7',
						'apple_id' => '6',
						'color' => 'Some wierd color',
						'name' => 'Some odd color',
						'created' => '2006-12-25 05:34:21',
						'date' => '2006-12-25',
						'modified' => '2006-12-25 05:34:21',
						'mytime' => '22:57:17'
			]]],
			[
				'Apple' => [
					'id' => '7',
					'apple_id' => '6',
					'color' => 'Some wierd color',
					'name' => 'Some odd color',
					'created' => '2006-12-25 05:34:21',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:34:21',
					'mytime' => '22:57:17'
				],
				'Parent' => [
					'id' => '6',
					'apple_id' => '4',
					'color' => 'My new appleOrange',
					'name' => 'My new apple',
					'created' => '2006-12-25 05:29:39',
					'date' => '2006-12-25',
					'modified' => '2006-12-25 05:29:39',
					'mytime' => '22:57:17'
				],
				'Sample' => [
					'id' => null,
					'apple_id' => null,
					'name' => null
				],
				'Child' => []
		]];
		$this->assertEquals($expected, $result);
	}

/**
 * testSaveEmpty method
 *
 * @return void
 */
	public function testSaveEmpty() {
		$this->loadFixtures('Thread');
		$TestModel = new Thread();
		$data = [];
		$expected = $TestModel->save($data);
		$this->assertFalse($expected);
	}

/**
 * testFindAllWithConditionInChildQuery
 *
 * @return void
 */
	public function testFindAllWithConditionInChildQuery() {
		$this->loadFixtures('Basket', 'FilmFile');

		$TestModel = new Basket();
		$recursive = 3;
		$result = $TestModel->find('all', compact('recursive'));

		$expected = [
			[
				'Basket' => [
					'id' => 1,
					'type' => 'nonfile',
					'name' => 'basket1',
					'object_id' => 1,
					'user_id' => 1,
				],
				'FilmFile' => [
					'id' => '',
					'name' => '',
				]
			],
			[
				'Basket' => [
					'id' => 2,
					'type' => 'file',
					'name' => 'basket2',
					'object_id' => 2,
					'user_id' => 1,
				],
				'FilmFile' => [
					'id' => 2,
					'name' => 'two',
				]
			],
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindAllWithConditionsHavingMixedDataTypes method
 *
 * @return void
 */
	public function testFindAllWithConditionsHavingMixedDataTypes() {
		$this->loadFixtures('Article', 'User', 'Tag', 'ArticlesTag');
		$TestModel = new Article();
		$expected = [
			[
				'Article' => [
					'id' => 1,
					'user_id' => 1,
					'title' => 'First Article',
					'body' => 'First Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				]
			],
			[
				'Article' => [
					'id' => 2,
					'user_id' => 3,
					'title' => 'Second Article',
					'body' => 'Second Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31'
				]
			]
		];
		$conditions = ['id' => ['1', 2]];
		$recursive = -1;
		$order = 'Article.id ASC';
		$result = $TestModel->find('all', compact('conditions', 'recursive', 'order'));
		$this->assertEquals($expected, $result);

		$this->skipIf($this->db instanceof Postgres, 'The rest of testFindAllWithConditionsHavingMixedDataTypes test is not compatible with Postgres.');

		$conditions = ['id' => ['1', 2, '3.0']];
		$order = 'Article.id ASC';
		$result = $TestModel->find('all', compact('recursive', 'conditions', 'order'));
		$expected = [
			[
				'Article' => [
					'id' => 1,
					'user_id' => 1,
					'title' => 'First Article',
					'body' => 'First Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				]
			],
			[
				'Article' => [
					'id' => 2,
					'user_id' => 3,
					'title' => 'Second Article',
					'body' => 'Second Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31'
				]
			],
			[
				'Article' => [
					'id' => 3,
					'user_id' => 1,
					'title' => 'Third Article',
					'body' => 'Third Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				]
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testBindUnbind method
 *
 * @return void
 */
	public function testBindUnbind() {
		$this->loadFixtures(
			'User',
			'Comment',
			'FeatureSet',
			'DeviceType',
			'DeviceTypeCategory',
			'ExteriorTypeCategory',
			'Device',
			'Document',
			'DocumentDirectory'
		);
		$TestModel = new User();

		$result = $TestModel->hasMany;
		$expected = [];
		$this->assertEquals($expected, $result);

		$result = $TestModel->bindModel(['hasMany' => ['Comment']]);
		$this->assertTrue($result);

		$result = $TestModel->find('all', [
			'fields' => 'User.id, User.user',
			'order' => ['User.id' => 'ASC'],
		]);
		$expected = [
			[
				'User' => [
					'id' => '1',
					'user' => 'mariano'
				],
				'Comment' => [
					[
						'id' => '3',
						'article_id' => '1',
						'user_id' => '1',
						'comment' => 'Third Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:49:23',
						'updated' => '2007-03-18 10:51:31'
					],
					[
						'id' => '4',
						'article_id' => '1',
						'user_id' => '1',
						'comment' => 'Fourth Comment for First Article',
						'published' => 'N',
						'created' => '2007-03-18 10:51:23',
						'updated' => '2007-03-18 10:53:31'
					],
					[
						'id' => '5',
						'article_id' => '2',
						'user_id' => '1',
						'comment' => 'First Comment for Second Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:53:23',
						'updated' => '2007-03-18 10:55:31'
			]]],
			[
				'User' => [
					'id' => '2',
					'user' => 'nate'
				],
				'Comment' => [
					[
						'id' => '1',
						'article_id' => '1',
						'user_id' => '2',
						'comment' => 'First Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:45:23',
						'updated' => '2007-03-18 10:47:31'
					],
					[
						'id' => '6',
						'article_id' => '2',
						'user_id' => '2',
						'comment' => 'Second Comment for Second Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:55:23',
						'updated' => '2007-03-18 10:57:31'
			]]],
			[
				'User' => [
					'id' => '3',
					'user' => 'larry'
				],
				'Comment' => []
			],
			[
				'User' => [
					'id' => '4',
					'user' => 'garrett'
				],
				'Comment' => [
					[
						'id' => '2',
						'article_id' => '1',
						'user_id' => '4',
						'comment' => 'Second Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:47:23',
						'updated' => '2007-03-18 10:49:31'
		]]]];

		$this->assertEquals($expected, $result);

		$TestModel->resetAssociations();
		$result = $TestModel->hasMany;
		$this->assertSame([], $result);

		$result = $TestModel->bindModel(['hasMany' => ['Comment']], false);
		$this->assertTrue($result);

		$result = $TestModel->find('all', [
			'fields' => 'User.id, User.user',
			'order' => ['User.id' => 'ASC'],
		]);

		$expected = [
			[
				'User' => [
					'id' => '1',
					'user' => 'mariano'
				],
				'Comment' => [
					[
						'id' => '3',
						'article_id' => '1',
						'user_id' => '1',
						'comment' => 'Third Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:49:23',
						'updated' => '2007-03-18 10:51:31'
					],
					[
						'id' => '4',
						'article_id' => '1',
						'user_id' => '1',
						'comment' => 'Fourth Comment for First Article',
						'published' => 'N',
						'created' => '2007-03-18 10:51:23',
						'updated' => '2007-03-18 10:53:31'
					],
					[
						'id' => '5',
						'article_id' => '2',
						'user_id' => '1',
						'comment' => 'First Comment for Second Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:53:23',
						'updated' => '2007-03-18 10:55:31'
			]]],
			[
				'User' => [
					'id' => '2',
					'user' => 'nate'
				],
				'Comment' => [
					[
						'id' => '1',
						'article_id' => '1',
						'user_id' => '2',
						'comment' => 'First Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:45:23',
						'updated' => '2007-03-18 10:47:31'
					],
					[
						'id' => '6',
						'article_id' => '2',
						'user_id' => '2',
						'comment' => 'Second Comment for Second Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:55:23',
						'updated' => '2007-03-18 10:57:31'
			]]],
			[
				'User' => [
					'id' => '3',
					'user' => 'larry'
				],
				'Comment' => []
			],
			[
				'User' => [
					'id' => '4',
					'user' => 'garrett'
				],
				'Comment' => [
					[
						'id' => '2',
						'article_id' => '1',
						'user_id' => '4',
						'comment' => 'Second Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:47:23',
						'updated' => '2007-03-18 10:49:31'
		]]]];

		$this->assertEquals($expected, $result);

		$result = $TestModel->hasMany;
		$expected = [
			'Comment' => [
				'className' => 'Comment',
				'foreignKey' => 'user_id',
				'conditions' => null,
				'fields' => null,
				'order' => null,
				'limit' => null,
				'offset' => null,
				'dependent' => null,
				'exclusive' => null,
				'finderQuery' => null,
				'counterQuery' => null
		]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->unbindModel(['hasMany' => ['Comment']]);
		$this->assertTrue($result);

		$result = $TestModel->hasMany;
		$expected = [];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', [
			'fields' => 'User.id, User.user',
			'order' => ['User.id' => 'ASC'],
		]);
		$expected = [
			['User' => ['id' => '1', 'user' => 'mariano']],
			['User' => ['id' => '2', 'user' => 'nate']],
			['User' => ['id' => '3', 'user' => 'larry']],
			['User' => ['id' => '4', 'user' => 'garrett']]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', [
			'fields' => 'User.id, User.user',
			'order' => ['User.id' => 'ASC'],
		]);
		$expected = [
			[
				'User' => [
					'id' => '1',
					'user' => 'mariano'
				],
				'Comment' => [
					[
						'id' => '3',
						'article_id' => '1',
						'user_id' => '1',
						'comment' => 'Third Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:49:23',
						'updated' => '2007-03-18 10:51:31'
					],
					[
						'id' => '4',
						'article_id' => '1',
						'user_id' => '1',
						'comment' => 'Fourth Comment for First Article',
						'published' => 'N',
						'created' => '2007-03-18 10:51:23',
						'updated' => '2007-03-18 10:53:31'
					],
					[
						'id' => '5',
						'article_id' => '2',
						'user_id' => '1',
						'comment' => 'First Comment for Second Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:53:23',
						'updated' => '2007-03-18 10:55:31'
			]]],
			[
				'User' => [
					'id' => '2',
					'user' => 'nate'
				],
				'Comment' => [
					[
						'id' => '1',
						'article_id' => '1',
						'user_id' => '2',
						'comment' => 'First Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:45:23',
						'updated' => '2007-03-18 10:47:31'
					],
					[
						'id' => '6',
						'article_id' => '2',
						'user_id' => '2',
						'comment' => 'Second Comment for Second Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:55:23',
						'updated' => '2007-03-18 10:57:31'
			]]],
			[
				'User' => [
					'id' => '3',
					'user' => 'larry'
				],
				'Comment' => []
			],
			[
				'User' => [
					'id' => '4',
					'user' => 'garrett'
				],
				'Comment' => [
					[
						'id' => '2',
						'article_id' => '1',
						'user_id' => '4',
						'comment' =>
						'Second Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:47:23',
						'updated' => '2007-03-18 10:49:31'
		]]]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->unbindModel(['hasMany' => ['Comment']], false);
		$this->assertTrue($result);

		$result = $TestModel->find('all', [
			'fields' => 'User.id, User.user',
			'order' => ['User.id' => 'ASC'],
		]);
		$expected = [
			['User' => ['id' => '1', 'user' => 'mariano']],
			['User' => ['id' => '2', 'user' => 'nate']],
			['User' => ['id' => '3', 'user' => 'larry']],
			['User' => ['id' => '4', 'user' => 'garrett']]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->hasMany;
		$expected = [];
		$this->assertEquals($expected, $result);

		$result = $TestModel->bindModel(['hasMany' => [
			'Comment' => ['className' => 'Comment', 'conditions' => 'Comment.published = \'Y\'']
		]]);
		$this->assertTrue($result);

		$result = $TestModel->find('all', [
			'fields' => 'User.id, User.user',
			'order' => ['User.id' => 'ASC'],
		]);
		$expected = [
			[
				'User' => [
					'id' => '1',
					'user' => 'mariano'
				],
				'Comment' => [
					[
						'id' => '3',
						'article_id' => '1',
						'user_id' => '1',
						'comment' => 'Third Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:49:23',
						'updated' => '2007-03-18 10:51:31'
					],
					[
						'id' => '5',
						'article_id' => '2',
						'user_id' => '1',
						'comment' => 'First Comment for Second Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:53:23',
						'updated' => '2007-03-18 10:55:31'
			]]],
			[
				'User' => [
					'id' => '2',
					'user' => 'nate'
				],
				'Comment' => [
					[
						'id' => '1',
						'article_id' => '1',
						'user_id' => '2',
						'comment' => 'First Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:45:23',
						'updated' => '2007-03-18 10:47:31'
					],
					[
						'id' => '6',
						'article_id' => '2',
						'user_id' => '2',
						'comment' => 'Second Comment for Second Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:55:23',
						'updated' => '2007-03-18 10:57:31'
			]]],
			[
				'User' => [
					'id' => '3',
					'user' => 'larry'
				],
				'Comment' => []
			],
			[
				'User' => [
					'id' => '4',
					'user' => 'garrett'
				],
				'Comment' => [
					[
						'id' => '2',
						'article_id' => '1',
						'user_id' => '4',
						'comment' => 'Second Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:47:23',
						'updated' => '2007-03-18 10:49:31'
		]]]];

		$this->assertEquals($expected, $result);

		$TestModel2 = new DeviceType();

		$expected = [
			'className' => 'FeatureSet',
			'foreignKey' => 'feature_set_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'counterCache' => ''
		];
		$this->assertEquals($expected, $TestModel2->belongsTo['FeatureSet']);

		$TestModel2->bindModel([
			'belongsTo' => [
				'FeatureSet' => [
					'className' => 'FeatureSet',
					'conditions' => ['active' => true]
				]
			]
		]);
		$expected['conditions'] = ['active' => true];
		$this->assertEquals($expected, $TestModel2->belongsTo['FeatureSet']);

		$TestModel2->bindModel([
			'belongsTo' => [
				'FeatureSet' => [
					'className' => 'FeatureSet',
					'foreignKey' => false,
					'conditions' => ['Feature.name' => 'DeviceType.name']
				]
			]
		]);
		$expected['conditions'] = ['Feature.name' => 'DeviceType.name'];
		$expected['foreignKey'] = false;
		$this->assertEquals($expected, $TestModel2->belongsTo['FeatureSet']);

		$TestModel2->bindModel([
			'hasMany' => [
				'NewFeatureSet' => [
					'className' => 'FeatureSet',
					'conditions' => ['active' => true]
				]
			]
		]);

		$expected = [
			'className' => 'FeatureSet',
			'conditions' => ['active' => true],
			'foreignKey' => 'device_type_id',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'dependent' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		];
		$this->assertEquals($expected, $TestModel2->hasMany['NewFeatureSet']);
		$this->assertTrue(is_object($TestModel2->NewFeatureSet));
	}

/**
 * testBindMultipleTimes method
 *
 * @return void
 */
	public function testBindMultipleTimes() {
		$this->loadFixtures('User', 'Comment', 'Article', 'Tag', 'ArticlesTag');
		$TestModel = new User();

		$result = $TestModel->hasMany;
		$expected = [];
		$this->assertEquals($expected, $result);

		$result = $TestModel->bindModel([
			'hasMany' => [
				'Items' => ['className' => 'Comment']
		]]);
		$this->assertTrue($result);

		$result = $TestModel->find('all', [
			'fields' => 'User.id, User.user'
		]);

		$expected = [
			[
				'User' => [
					'id' => '1',
					'user' => 'mariano'
				],
				'Items' => [
					[
						'id' => '3',
						'article_id' => '1',
						'user_id' => '1',
						'comment' => 'Third Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:49:23',
						'updated' => '2007-03-18 10:51:31'
					],
					[
						'id' => '4',
						'article_id' => '1',
						'user_id' => '1',
						'comment' => 'Fourth Comment for First Article',
						'published' => 'N',
						'created' => '2007-03-18 10:51:23',
						'updated' => '2007-03-18 10:53:31'
					],
					[
						'id' => '5',
						'article_id' => '2',
						'user_id' => '1',
						'comment' => 'First Comment for Second Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:53:23',
						'updated' => '2007-03-18 10:55:31'
			]]],
			[
				'User' => [
					'id' => '2',
					'user' => 'nate'
				],
				'Items' => [
					[
						'id' => '1',
						'article_id' => '1',
						'user_id' => '2',
						'comment' => 'First Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:45:23',
						'updated' => '2007-03-18 10:47:31'
					],
					[
						'id' => '6',
						'article_id' => '2',
						'user_id' => '2',
						'comment' => 'Second Comment for Second Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:55:23',
						'updated' => '2007-03-18 10:57:31'
			]]],
			[
				'User' => [
					'id' => '3',
					'user' => 'larry'
				],
				'Items' => []
			],
			[
				'User' => [
					'id' => '4',
					'user' => 'garrett'
				],
					'Items' => [
						[
							'id' => '2',
							'article_id' => '1',
							'user_id' => '4',
							'comment' => 'Second Comment for First Article',
							'published' => 'Y',
							'created' => '2007-03-18 10:47:23',
							'updated' => '2007-03-18 10:49:31'
		]]]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->bindModel([
			'hasMany' => [
				'Items' => ['className' => 'Article']
		]]);
		$this->assertTrue($result);

		$result = $TestModel->find('all', [
			'fields' => 'User.id, User.user'
		]);
		$expected = [
			[
				'User' => [
					'id' => '1',
					'user' => 'mariano'
				],
				'Items' => [
					[
						'id' => 1,
						'user_id' => 1,
						'title' => 'First Article',
						'body' => 'First Article Body',
						'published' => 'Y',
						'created' => '2007-03-18 10:39:23',
						'updated' => '2007-03-18 10:41:31'
					],
					[
						'id' => 3,
						'user_id' => 1,
						'title' => 'Third Article',
						'body' => 'Third Article Body',
						'published' => 'Y',
						'created' => '2007-03-18 10:43:23',
						'updated' => '2007-03-18 10:45:31'
			]]],
			[
				'User' => [
					'id' => '2',
					'user' => 'nate'
				],
				'Items' => []
			],
			[
				'User' => [
					'id' => '3',
					'user' => 'larry'
				],
				'Items' => [
					[
						'id' => 2,
						'user_id' => 3,
						'title' => 'Second Article',
						'body' => 'Second Article Body',
						'published' => 'Y',
						'created' => '2007-03-18 10:41:23',
						'updated' => '2007-03-18 10:43:31'
			]]],
			[
				'User' => [
					'id' => '4',
					'user' => 'garrett'
				],
				'Items' => []
		]];

		$this->assertEquals($expected, $result);
	}

/**
 * test that multiple reset = true calls to bindModel() result in the original associations.
 *
 * @return void
 */
	public function testBindModelMultipleTimesResetCorrectly() {
		$this->loadFixtures('User', 'Comment', 'Article');
		$TestModel = new User();

		$TestModel->bindModel(['hasMany' => ['Comment']]);
		$TestModel->bindModel(['hasMany' => ['Comment']]);
		$TestModel->resetAssociations();

		$this->assertFalse(isset($TestModel->hasMany['Comment']), 'Association left behind');
	}

/**
 * testBindMultipleTimes method with different reset settings
 *
 * @return void
 */
	public function testBindMultipleTimesWithDifferentResetSettings() {
		$this->loadFixtures('User', 'Comment', 'Article');
		$TestModel = new User();

		$result = $TestModel->hasMany;
		$expected = [];
		$this->assertEquals($expected, $result);

		$result = $TestModel->bindModel([
			'hasMany' => ['Comment']
		]);
		$this->assertTrue($result);
		$result = $TestModel->bindModel(
			['hasMany' => ['Article']],
			false
		);
		$this->assertTrue($result);

		$result = array_keys($TestModel->hasMany);
		$expected = ['Comment', 'Article'];
		$this->assertEquals($expected, $result);

		$TestModel->resetAssociations();

		$result = array_keys($TestModel->hasMany);
		$expected = ['Article'];
		$this->assertEquals($expected, $result);
	}

/**
 * test that bindModel behaves with Custom primary Key associations
 *
 * @return void
 */
	public function testBindWithCustomPrimaryKey() {
		$this->loadFixtures('Story', 'StoriesTag', 'Tag');
		$Model = ClassRegistry::init('StoriesTag');
		$Model->bindModel([
			'belongsTo' => [
				'Tag' => [
					'className' => 'Tag',
					'foreignKey' => 'story'
		]]]);

		$result = $Model->find('all');
		$this->assertFalse(empty($result));
	}

/**
 * test that calling unbindModel() with reset == true multiple times
 * leaves associations in the correct state.
 *
 * @return void
 */
	public function testUnbindMultipleTimesResetCorrectly() {
		$this->loadFixtures('User', 'Comment', 'Article');
		$TestModel = new Article10();

		$TestModel->unbindModel(['hasMany' => ['Comment']]);
		$TestModel->unbindModel(['hasMany' => ['Comment']]);
		$TestModel->resetAssociations();

		$this->assertTrue(isset($TestModel->hasMany['Comment']), 'Association permanently removed');
	}

/**
 * testBindMultipleTimes method with different reset settings
 *
 * @return void
 */
	public function testUnBindMultipleTimesWithDifferentResetSettings() {
		$this->loadFixtures('User', 'Comment', 'Article');
		$TestModel = new Comment();

		$result = array_keys($TestModel->belongsTo);
		$expected = ['Article', 'User'];
		$this->assertEquals($expected, $result);

		$result = $TestModel->unbindModel([
			'belongsTo' => ['User']
		]);
		$this->assertTrue($result);
		$result = $TestModel->unbindModel(
			['belongsTo' => ['Article']],
			false
		);
		$this->assertTrue($result);

		$result = array_keys($TestModel->belongsTo);
		$expected = [];
		$this->assertEquals($expected, $result);

		$TestModel->resetAssociations();

		$result = array_keys($TestModel->belongsTo);
		$expected = ['User'];
		$this->assertEquals($expected, $result);
	}

/**
 * testAssociationAfterFind method
 *
 * @return void
 */
	public function testAssociationAfterFind() {
		$this->loadFixtures('Post', 'Author', 'Comment');
		$TestModel = new Post();
		$result = $TestModel->find('all', [
			'order' => ['Post.id' => 'ASC']
		]);
		$expected = [
			[
				'Post' => [
					'id' => '1',
					'author_id' => '1',
					'title' => 'First Post',
					'body' => 'First Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				'Author' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31',
					'test' => 'working'
			]],
			[
				'Post' => [
					'id' => '2',
					'author_id' => '3',
					'title' => 'Second Post',
					'body' => 'Second Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31'
				],
				'Author' => [
					'id' => '3',
					'user' => 'larry',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23',
					'updated' => '2007-03-17 01:22:31',
					'test' => 'working'
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
				],
				'Author' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31',
					'test' => 'working'
		]]];
		$this->assertEquals($expected, $result);
		unset($TestModel);

		$Author = new Author();
		$Author->Post->bindModel([
			'hasMany' => [
				'Comment' => [
					'className' => 'ModifiedComment',
					'foreignKey' => 'article_id',
				]
		]]);
		$result = $Author->find('all', [
			'conditions' => ['Author.id' => 1],
			'order' => ['Author.id' => 'ASC'],
			'recursive' => 2
		]);
		$expected = [
			'id' => 1,
			'article_id' => 1,
			'user_id' => 2,
			'comment' => 'First Comment for First Article',
			'published' => 'Y',
			'created' => '2007-03-18 10:45:23',
			'updated' => '2007-03-18 10:47:31',
			'callback' => 'Fire'
		];
		$this->assertEquals($expected, $result[0]['Post'][0]['Comment'][0]);
	}

/**
 * testDeeperAssociationAfterFind method
 *
 * @return void
 */
	public function testDeeperAssociationAfterFind() {
		$this->loadFixtures('Post', 'Author', 'Comment', 'Attachment', 'Article');

		$Post = new Post();
		$Post->bindModel([
			'hasMany' => [
				'Comment' => [
					'className' => 'ModifiedComment',
					'foreignKey' => 'article_id',
				]
		]]);
		$Post->Comment->bindModel([
			'hasOne' => [
				'Attachment' => [
					'className' => 'ModifiedAttachment',
				]
		]]);

		$result = $Post->find('first', [
			'conditions' => ['Post.id' => 2],
			'recursive' => 2
		]);
		$this->assertTrue(isset($result['Comment'][0]['callback']));
		$this->assertEquals('Fire', $result['Comment'][0]['callback']);
		$this->assertTrue(isset($result['Comment'][0]['Attachment']['callback']));
		$this->assertEquals('Fired', $result['Comment'][0]['Attachment']['callback']);
	}

/**
 * Tests that callbacks can be properly disabled
 *
 * @return void
 */
	public function testCallbackDisabling() {
		$this->loadFixtures('Author');
		$TestModel = new ModifiedAuthor();

		$result = Hash::extract($TestModel->find('all'), '{n}.Author.user');
		$expected = ['mariano (CakePHP)', 'nate (CakePHP)', 'larry (CakePHP)', 'garrett (CakePHP)'];
		$this->assertEquals($expected, $result);

		$result = Hash::extract($TestModel->find('all', ['callbacks' => 'after']), '{n}.Author.user');
		$expected = ['mariano (CakePHP)', 'nate (CakePHP)', 'larry (CakePHP)', 'garrett (CakePHP)'];
		$this->assertEquals($expected, $result);

		$result = Hash::extract($TestModel->find('all', ['callbacks' => 'before']), '{n}.Author.user');
		$expected = ['mariano', 'nate', 'larry', 'garrett'];
		$this->assertEquals($expected, $result);

		$result = Hash::extract($TestModel->find('all', ['callbacks' => false]), '{n}.Author.user');
		$expected = ['mariano', 'nate', 'larry', 'garrett'];
		$this->assertEquals($expected, $result);
	}

/**
 * testAssociationAfterFindCallbacksDisabled method
 *
 * @return void
 */
	public function testAssociationAfterFindCalbacksDisabled() {
		$this->loadFixtures('Post', 'Author', 'Comment');
		$TestModel = new Post();
		$result = $TestModel->find('all', [
			'callbacks' => false,
			'order' => ['Post.id' => 'ASC'],
		]);
		$expected = [
			[
				'Post' => [
					'id' => '1',
					'author_id' => '1',
					'title' => 'First Post',
					'body' => 'First Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				'Author' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31'
			]],
			[
				'Post' => [
					'id' => '2',
					'author_id' => '3',
					'title' => 'Second Post',
					'body' => 'Second Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31'
				],
				'Author' => [
					'id' => '3',
					'user' => 'larry',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23',
					'updated' => '2007-03-17 01:22:31'
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
				],
				'Author' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31'
		]]];
		$this->assertEquals($expected, $result);
		unset($TestModel);

		$Author = new Author();
		$Author->Post->bindModel([
			'hasMany' => [
				'Comment' => [
					'className' => 'ModifiedComment',
					'foreignKey' => 'article_id',
				]
		]]);
		$result = $Author->find('all', [
			'conditions' => ['Author.id' => 1],
			'recursive' => 2,
			'order' => ['Author.id' => 'ASC'],
			'callbacks' => false
		]);
		$expected = [
			'id' => 1,
			'article_id' => 1,
			'user_id' => 2,
			'comment' => 'First Comment for First Article',
			'published' => 'Y',
			'created' => '2007-03-18 10:45:23',
			'updated' => '2007-03-18 10:47:31'
		];
		$this->assertEquals($expected, $result[0]['Post'][0]['Comment'][0]);
	}

/**
 * Tests that the database configuration assigned to the model can be changed using
 * (before|after)Find callbacks
 *
 * @return void
 */
	public function testCallbackSourceChange() {
		$this->loadFixtures('Post');
		$TestModel = new Post();
		$this->assertEquals(3, count($TestModel->find('all')));
	}

/**
 * testCallbackSourceChangeUnknownDatasource method
 *
 * @expectedException MissingDatasourceConfigException
 * @return void
 */
	public function testCallbackSourceChangeUnknownDatasource() {
		$this->loadFixtures('Post', 'Author');
		$TestModel = new Post();
		$this->assertFalse($TestModel->find('all', ['connection' => 'foo']));
	}

/**
 * testMultipleBelongsToWithSameClass method
 *
 * @return void
 */
	public function testMultipleBelongsToWithSameClass() {
		$this->loadFixtures(
			'DeviceType',
			'DeviceTypeCategory',
			'FeatureSet',
			'ExteriorTypeCategory',
			'Document',
			'Device',
			'DocumentDirectory'
		);

		$DeviceType = new DeviceType();

		$DeviceType->recursive = 2;
		$result = $DeviceType->read(null, 1);

		$expected = [
			'DeviceType' => [
				'id' => 1,
				'device_type_category_id' => 1,
				'feature_set_id' => 1,
				'exterior_type_category_id' => 1,
				'image_id' => 1,
				'extra1_id' => 1,
				'extra2_id' => 1,
				'name' => 'DeviceType 1',
				'order' => 0
			],
			'Image' => [
				'id' => 1,
				'document_directory_id' => 1,
				'name' => 'Document 1',
				'DocumentDirectory' => [
					'id' => 1,
					'name' => 'DocumentDirectory 1'
			]],
			'Extra1' => [
				'id' => 1,
				'document_directory_id' => 1,
				'name' => 'Document 1',
				'DocumentDirectory' => [
					'id' => 1,
					'name' => 'DocumentDirectory 1'
			]],
			'Extra2' => [
				'id' => 1,
				'document_directory_id' => 1,
				'name' => 'Document 1',
				'DocumentDirectory' => [
					'id' => 1,
					'name' => 'DocumentDirectory 1'
			]],
			'DeviceTypeCategory' => [
				'id' => 1,
				'name' => 'DeviceTypeCategory 1'
			],
			'FeatureSet' => [
				'id' => 1,
				'name' => 'FeatureSet 1'
			],
			'ExteriorTypeCategory' => [
				'id' => 1,
				'image_id' => 1,
				'name' => 'ExteriorTypeCategory 1',
				'Image' => [
					'id' => 1,
					'device_type_id' => 1,
					'name' => 'Device 1',
					'typ' => 1
			]],
			'Device' => [
				[
					'id' => 1,
					'device_type_id' => 1,
					'name' => 'Device 1',
					'typ' => 1
				],
				[
					'id' => 2,
					'device_type_id' => 1,
					'name' => 'Device 2',
					'typ' => 1
				],
				[
					'id' => 3,
					'device_type_id' => 1,
					'name' => 'Device 3',
					'typ' => 2
		]]];

		$this->assertEquals($expected, $result);
	}

/**
 * testHabtmRecursiveBelongsTo method
 *
 * @return void
 */
	public function testHabtmRecursiveBelongsTo() {
		$this->loadFixtures('Portfolio', 'Item', 'ItemsPortfolio', 'Syfile', 'Image');
		$Portfolio = new Portfolio();

		$result = $Portfolio->find('first', ['conditions' => ['id' => 2], 'recursive' => 3]);
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
					'published' => false,
					'name' => 'Item 2',
					'ItemsPortfolio' => [
						'id' => 2,
						'item_id' => 2,
						'portfolio_id' => 2
					],
					'Syfile' => [
						'id' => 2,
						'image_id' => 2,
						'name' => 'Syfile 2',
						'item_count' => null,
						'Image' => [
							'id' => 2,
							'name' => 'Image 2'
						]
				]],
				[
					'id' => 6,
					'syfile_id' => 6,
					'published' => false,
					'name' => 'Item 6',
					'ItemsPortfolio' => [
						'id' => 6,
						'item_id' => 6,
						'portfolio_id' => 2
					],
					'Syfile' => [
						'id' => 6,
						'image_id' => null,
						'name' => 'Syfile 6',
						'item_count' => null,
						'Image' => []
		]]]];

		$this->assertEquals($expected, $result);
	}

/**
 * testNonNumericHabtmJoinKey method
 *
 * @return void
 */
	public function testNonNumericHabtmJoinKey() {
		$this->loadFixtures('Post', 'Tag', 'PostsTag', 'Author');
		$Post = new Post();
		$Post->bindModel([
			'hasAndBelongsToMany' => ['Tag']
		]);
		$Post->Tag->primaryKey = 'tag';

		$result = $Post->find('all', [
			'order' => 'Post.id ASC',
		]);
		$expected = [
			[
				'Post' => [
					'id' => '1',
					'author_id' => '1',
					'title' => 'First Post',
					'body' => 'First Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				'Author' => [
					'id' => 1,
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31',
					'test' => 'working'
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
			]]],
			[
				'Post' => [
					'id' => '2',
					'author_id' => '3',
					'title' => 'Second Post',
					'body' => 'Second Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31'
				],
				'Author' => [
					'id' => 3,
					'user' => 'larry',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23',
					'updated' => '2007-03-17 01:22:31',
					'test' => 'working'
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
			]]],
			[
				'Post' => [
					'id' => '3',
					'author_id' => '1',
					'title' => 'Third Post',
					'body' => 'Third Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				],
				'Author' => [
					'id' => 1,
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31',
					'test' => 'working'
				],
				'Tag' => []
		]];
		$this->assertEquals($expected, $result);
	}

/**
 * testHabtmFinderQuery method
 *
 * @return void
 */
	public function testHabtmFinderQuery() {
		$this->loadFixtures('Article', 'Tag', 'ArticlesTag');
		$Article = new Article();

		$sql = $this->db->buildStatement(
			[
				'fields' => $this->db->fields($Article->Tag, null, [
					'Tag.id', 'Tag.tag', 'ArticlesTag.article_id', 'ArticlesTag.tag_id'
				]),
				'table' => $this->db->fullTableName('tags'),
				'alias' => 'Tag',
				'limit' => null,
				'offset' => null,
				'group' => null,
				'joins' => [[
					'alias' => 'ArticlesTag',
					'table' => 'articles_tags',
					'conditions' => [
						["ArticlesTag.article_id" => '{$__cakeID__$}'],
						["ArticlesTag.tag_id" => $this->db->identifier('Tag.id')]
					]
				]],
				'conditions' => [],
				'order' => null
			],
			$Article
		);

		$Article->hasAndBelongsToMany['Tag']['finderQuery'] = $sql;
		$result = $Article->find('first');
		$expected = [
			[
				'id' => '1',
				'tag' => 'tag1'
			],
			[
				'id' => '2',
				'tag' => 'tag2'
		]];

		$this->assertEquals($expected, $result['Tag']);
	}

/**
 * testHabtmLimitOptimization method
 *
 * @return void
 */
	public function testHabtmLimitOptimization() {
		$this->loadFixtures('Article', 'User', 'Comment', 'Tag', 'ArticlesTag');
		$TestModel = new Article();

		$TestModel->hasAndBelongsToMany['Tag']['limit'] = 2;
		$result = $TestModel->read(null, 2);
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
		]]];

		$this->assertEquals($expected, $result);

		$TestModel->hasAndBelongsToMany['Tag']['limit'] = 1;
		$result = $TestModel->read(null, 2);
		unset($expected['Tag'][1]);

		$this->assertEquals($expected, $result);
	}

/**
 * testHasManyLimitOptimization method
 *
 * @return void
 */
	public function testHasManyLimitOptimization() {
		$this->loadFixtures('Project', 'Thread', 'Message', 'Bid');
		$Project = new Project();
		$Project->recursive = 3;

		$result = $Project->find('all', [
			'order' => 'Project.id ASC',
		]);
		$expected = [
			[
				'Project' => [
					'id' => 1,
					'name' => 'Project 1'
				],
				'Thread' => [
					[
						'id' => 1,
						'project_id' => 1,
						'name' => 'Project 1, Thread 1',
						'Project' => [
							'id' => 1,
							'name' => 'Project 1',
							'Thread' => [
								[
									'id' => 1,
									'project_id' => 1,
									'name' => 'Project 1, Thread 1'
								],
								[
									'id' => 2,
									'project_id' => 1,
									'name' => 'Project 1, Thread 2'
						]]],
						'Message' => [
							[
								'id' => 1,
								'thread_id' => 1,
								'name' => 'Thread 1, Message 1',
								'Bid' => [
									'id' => 1,
									'message_id' => 1,
									'name' => 'Bid 1.1'
					]]]],
					[
						'id' => 2,
						'project_id' => 1,
						'name' => 'Project 1, Thread 2',
						'Project' => [
							'id' => 1,
							'name' => 'Project 1',
							'Thread' => [
								[
									'id' => 1,
									'project_id' => 1,
									'name' => 'Project 1, Thread 1'
								],
								[
									'id' => 2,
									'project_id' => 1,
									'name' => 'Project 1, Thread 2'
						]]],
						'Message' => [
							[
								'id' => 2,
								'thread_id' => 2,
								'name' => 'Thread 2, Message 1',
								'Bid' => [
									'id' => 4,
									'message_id' => 2,
									'name' => 'Bid 2.1'
			]]]]]],
			[
				'Project' => [
					'id' => 2,
					'name' => 'Project 2'
				],
				'Thread' => [
					[
						'id' => 3,
						'project_id' => 2,
						'name' => 'Project 2, Thread 1',
						'Project' => [
							'id' => 2,
							'name' => 'Project 2',
							'Thread' => [
								[
									'id' => 3,
									'project_id' => 2,
									'name' => 'Project 2, Thread 1'
						]]],
						'Message' => [
							[
								'id' => 3,
								'thread_id' => 3,
								'name' => 'Thread 3, Message 1',
								'Bid' => [
									'id' => 3,
									'message_id' => 3,
									'name' => 'Bid 3.1'
			]]]]]],
			[
				'Project' => [
					'id' => 3,
					'name' => 'Project 3'
				],
				'Thread' => []
		]];

		$this->assertEquals($expected, $result);
	}

/**
 * testFindAllRecursiveSelfJoin method
 *
 * @return void
 */
	public function testFindAllRecursiveSelfJoin() {
		$this->loadFixtures('Home', 'AnotherArticle', 'Advertisement');
		$TestModel = new Home();
		$TestModel->recursive = 2;

		$result = $TestModel->find('all', [
			'order' => 'Home.id ASC',
		]);
		$expected = [
			[
				'Home' => [
					'id' => '1',
					'another_article_id' => '1',
					'advertisement_id' => '1',
					'title' => 'First Home',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				'AnotherArticle' => [
					'id' => '1',
					'title' => 'First Article',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
					'Home' => [
						[
							'id' => '1',
							'another_article_id' => '1',
							'advertisement_id' => '1',
							'title' => 'First Home',
							'created' => '2007-03-18 10:39:23',
							'updated' => '2007-03-18 10:41:31'
				]]],
				'Advertisement' => [
					'id' => '1',
					'title' => 'First Ad',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
					'Home' => [
						[
							'id' => '1',
							'another_article_id' => '1',
							'advertisement_id' => '1',
							'title' => 'First Home',
							'created' => '2007-03-18 10:39:23',
							'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => '2',
							'another_article_id' => '3',
							'advertisement_id' => '1',
							'title' => 'Second Home',
							'created' => '2007-03-18 10:41:23',
							'updated' => '2007-03-18 10:43:31'
			]]]],
			[
				'Home' => [
					'id' => '2',
					'another_article_id' => '3',
					'advertisement_id' => '1',
					'title' => 'Second Home',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31'
				],
				'AnotherArticle' => [
					'id' => '3',
					'title' => 'Third Article',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31',
					'Home' => [
						[
							'id' => '2',
							'another_article_id' => '3',
							'advertisement_id' => '1',
							'title' => 'Second Home',
							'created' => '2007-03-18 10:41:23',
							'updated' => '2007-03-18 10:43:31'
				]]],
				'Advertisement' => [
					'id' => '1',
					'title' => 'First Ad',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
					'Home' => [
						[
							'id' => '1',
							'another_article_id' => '1',
							'advertisement_id' => '1',
							'title' => 'First Home',
							'created' => '2007-03-18 10:39:23',
							'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => '2',
							'another_article_id' => '3',
							'advertisement_id' => '1',
							'title' => 'Second Home',
							'created' => '2007-03-18 10:41:23',
							'updated' => '2007-03-18 10:43:31'
		]]]]];

		$this->assertEquals($expected, $result);
	}

/**
 * testFindAllRecursiveWithHabtm method
 *
 * @return void
 */
	public function testFindAllRecursiveWithHabtm() {
		$this->loadFixtures(
			'MyCategoriesMyUsers',
			'MyCategoriesMyProducts',
			'MyCategory',
			'MyUser',
			'MyProduct'
		);

		$MyUser = new MyUser();
		$MyUser->recursive = 2;

		$result = $MyUser->find('all', [
			'order' => 'MyUser.id ASC'
		]);
		$expected = [
			[
				'MyUser' => ['id' => '1', 'firstname' => 'userA'],
				'MyCategory' => [
					[
						'id' => '1',
						'name' => 'A',
						'MyProduct' => [
							[
								'id' => '1',
								'name' => 'book'
					]]],
					[
						'id' => '3',
						'name' => 'C',
						'MyProduct' => [
							[
								'id' => '2',
								'name' => 'computer'
			]]]]],
			[
				'MyUser' => [
					'id' => '2',
					'firstname' => 'userB'
				],
				'MyCategory' => [
					[
						'id' => '1',
						'name' => 'A',
						'MyProduct' => [
							[
								'id' => '1',
								'name' => 'book'
					]]],
					[
						'id' => '2',
						'name' => 'B',
						'MyProduct' => [
							[
								'id' => '1',
								'name' => 'book'
							],
							[
								'id' => '2',
								'name' => 'computer'
		]]]]]];

		$this->assertEquals($expected, $result);
	}

/**
 * testReadFakeThread method
 *
 * @return void
 */
	public function testReadFakeThread() {
		$this->loadFixtures('CategoryThread');
		$TestModel = new CategoryThread();

		$fullDebug = $this->db->fullDebug;
		$this->db->fullDebug = true;
		$TestModel->recursive = 6;
		$TestModel->id = 7;
		$result = $TestModel->read();
		$expected = [
			'CategoryThread' => [
				'id' => 7,
				'parent_id' => 6,
				'name' => 'Category 2.1',
				'created' => '2007-03-18 15:30:23',
				'updated' => '2007-03-18 15:32:31'
			],
			'ParentCategory' => [
				'id' => 6,
				'parent_id' => 5,
				'name' => 'Category 2',
				'created' => '2007-03-18 15:30:23',
				'updated' => '2007-03-18 15:32:31',
				'ParentCategory' => [
					'id' => 5,
					'parent_id' => 4,
					'name' => 'Category 1.1.1.1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31',
					'ParentCategory' => [
						'id' => 4,
						'parent_id' => 3,
						'name' => 'Category 1.1.2',
						'created' => '2007-03-18 15:30:23',
						'updated' => '2007-03-18 15:32:31',
						'ParentCategory' => [
							'id' => 3,
							'parent_id' => 2,
							'name' => 'Category 1.1.1',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31',
							'ParentCategory' => [
								'id' => 2,
								'parent_id' => 1,
								'name' => 'Category 1.1',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31',
								'ParentCategory' => [
									'id' => 1,
									'parent_id' => 0,
									'name' => 'Category 1',
									'created' => '2007-03-18 15:30:23',
									'updated' => '2007-03-18 15:32:31'
		]]]]]]];

		$this->db->fullDebug = $fullDebug;
		$this->assertEquals($expected, $result);
	}

/**
 * testFindFakeThread method
 *
 * @return void
 */
	public function testFindFakeThread() {
		$this->loadFixtures('CategoryThread');
		$TestModel = new CategoryThread();

		$fullDebug = $this->db->fullDebug;
		$this->db->fullDebug = true;
		$TestModel->recursive = 6;
		$result = $TestModel->find('first', ['conditions' => ['CategoryThread.id' => 7]]);

		$expected = [
			'CategoryThread' => [
				'id' => 7,
				'parent_id' => 6,
				'name' => 'Category 2.1',
				'created' => '2007-03-18 15:30:23',
				'updated' => '2007-03-18 15:32:31'
			],
			'ParentCategory' => [
				'id' => 6,
				'parent_id' => 5,
				'name' => 'Category 2',
				'created' => '2007-03-18 15:30:23',
				'updated' => '2007-03-18 15:32:31',
				'ParentCategory' => [
					'id' => 5,
					'parent_id' => 4,
					'name' => 'Category 1.1.1.1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31',
					'ParentCategory' => [
						'id' => 4,
						'parent_id' => 3,
						'name' => 'Category 1.1.2',
						'created' => '2007-03-18 15:30:23',
						'updated' => '2007-03-18 15:32:31',
						'ParentCategory' => [
							'id' => 3,
							'parent_id' => 2,
							'name' => 'Category 1.1.1',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31',
							'ParentCategory' => [
								'id' => 2,
								'parent_id' => 1,
								'name' => 'Category 1.1',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31',
								'ParentCategory' => [
									'id' => 1,
									'parent_id' => 0,
									'name' => 'Category 1',
									'created' => '2007-03-18 15:30:23',
									'updated' => '2007-03-18 15:32:31'
		]]]]]]];

		$this->db->fullDebug = $fullDebug;
		$this->assertEquals($expected, $result);
	}

/**
 * testFindAllFakeThread method
 *
 * @return void
 */
	public function testFindAllFakeThread() {
		$this->loadFixtures('CategoryThread');
		$TestModel = new CategoryThread();

		$fullDebug = $this->db->fullDebug;
		$this->db->fullDebug = true;
		$TestModel->recursive = 6;
		$result = $TestModel->find('all');
		$expected = [
			[
				'CategoryThread' => [
				'id' => 1,
				'parent_id' => 0,
				'name' => 'Category 1',
				'created' => '2007-03-18 15:30:23',
				'updated' => '2007-03-18 15:32:31'
				],
				'ParentCategory' => [
					'id' => null,
					'parent_id' => null,
					'name' => null,
					'created' => null,
					'updated' => null,
					'ParentCategory' => []
			]],
			[
				'CategoryThread' => [
					'id' => 2,
					'parent_id' => 1,
					'name' => 'Category 1.1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'ParentCategory' => [
					'id' => 1,
					'parent_id' => 0,
					'name' => 'Category 1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31',
					'ParentCategory' => []
				]],
			[
				'CategoryThread' => [
					'id' => 3,
					'parent_id' => 2,
					'name' => 'Category 1.1.1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'ParentCategory' => [
					'id' => 2,
					'parent_id' => 1,
					'name' => 'Category 1.1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31',
					'ParentCategory' => [
						'id' => 1,
						'parent_id' => 0,
						'name' => 'Category 1',
						'created' => '2007-03-18 15:30:23',
						'updated' => '2007-03-18 15:32:31',
						'ParentCategory' => []
			]]],
			[
				'CategoryThread' => [
					'id' => 4,
					'parent_id' => 3,
					'name' => 'Category 1.1.2',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'ParentCategory' => [
					'id' => 3,
					'parent_id' => 2,
					'name' => 'Category 1.1.1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31',
					'ParentCategory' => [
						'id' => 2,
						'parent_id' => 1,
						'name' => 'Category 1.1',
						'created' => '2007-03-18 15:30:23',
						'updated' => '2007-03-18 15:32:31',
						'ParentCategory' => [
							'id' => 1,
							'parent_id' => 0,
							'name' => 'Category 1',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31',
							'ParentCategory' => []
			]]]],
			[
				'CategoryThread' => [
					'id' => 5,
					'parent_id' => 4,
					'name' => 'Category 1.1.1.1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'ParentCategory' => [
					'id' => 4,
					'parent_id' => 3,
					'name' => 'Category 1.1.2',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31',
					'ParentCategory' => [
						'id' => 3,
						'parent_id' => 2,
						'name' => 'Category 1.1.1',
						'created' => '2007-03-18 15:30:23',
						'updated' => '2007-03-18 15:32:31',
						'ParentCategory' => [
							'id' => 2,
							'parent_id' => 1,
							'name' => 'Category 1.1',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31',
							'ParentCategory' => [
								'id' => 1,
								'parent_id' => 0,
								'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31',
								'ParentCategory' => []
			]]]]],
			[
				'CategoryThread' => [
					'id' => 6,
					'parent_id' => 5,
					'name' => 'Category 2',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'ParentCategory' => [
					'id' => 5,
					'parent_id' => 4,
					'name' => 'Category 1.1.1.1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31',
					'ParentCategory' => [
						'id' => 4,
						'parent_id' => 3,
						'name' => 'Category 1.1.2',
						'created' => '2007-03-18 15:30:23',
						'updated' => '2007-03-18 15:32:31',
						'ParentCategory' => [
							'id' => 3,
							'parent_id' => 2,
							'name' => 'Category 1.1.1',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31',
							'ParentCategory' => [
								'id' => 2,
								'parent_id' => 1,
								'name' => 'Category 1.1',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31',
								'ParentCategory' => [
									'id' => 1,
									'parent_id' => 0,
									'name' => 'Category 1',
									'created' => '2007-03-18 15:30:23',
									'updated' => '2007-03-18 15:32:31',
									'ParentCategory' => []
			]]]]]],
			[
				'CategoryThread' => [
					'id' => 7,
					'parent_id' => 6,
					'name' => 'Category 2.1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				],
				'ParentCategory' => [
					'id' => 6,
					'parent_id' => 5,
					'name' => 'Category 2',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31',
					'ParentCategory' => [
						'id' => 5,
						'parent_id' => 4,
						'name' => 'Category 1.1.1.1',
						'created' => '2007-03-18 15:30:23',
						'updated' => '2007-03-18 15:32:31',
						'ParentCategory' => [
							'id' => 4,
							'parent_id' => 3,
							'name' => 'Category 1.1.2',
							'created' => '2007-03-18 15:30:23',
							'updated' => '2007-03-18 15:32:31',
							'ParentCategory' => [
								'id' => 3,
								'parent_id' => 2,
								'name' => 'Category 1.1.1',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31',
							'ParentCategory' => [
								'id' => 2,
								'parent_id' => 1,
								'name' => 'Category 1.1',
								'created' => '2007-03-18 15:30:23',
								'updated' => '2007-03-18 15:32:31',
								'ParentCategory' => [
									'id' => 1,
									'parent_id' => 0,
									'name' => 'Category 1',
									'created' => '2007-03-18 15:30:23',
									'updated' => '2007-03-18 15:32:31'
		]]]]]]]];

		$this->db->fullDebug = $fullDebug;
		$this->assertEquals($expected, $result);
	}

/**
 * testConditionalNumerics method
 *
 * @return void
 */
	public function testConditionalNumerics() {
		$this->loadFixtures('NumericArticle');
		$NumericArticle = new NumericArticle();
		$data = ['conditions' => ['title' => '12345abcde']];
		$result = $NumericArticle->find('first', $data);
		$this->assertTrue(!empty($result));

		$data = ['conditions' => ['title' => '12345']];
		$result = $NumericArticle->find('first', $data);
		$this->assertTrue(empty($result));
	}

/**
 * test buildQuery()
 *
 * @return void
 */
	public function testBuildQuery() {
		$this->loadFixtures('User');
		$TestModel = new User();
		$TestModel->cacheQueries = false;
		$TestModel->order = null;
		$expected = [
			'conditions' => [
				'user' => 'larry'
			],
			'fields' => null,
			'joins' => [],
			'limit' => null,
			'offset' => null,
			'order' => [],
			'page' => 1,
			'group' => null,
			'callbacks' => true,
			'returnQuery' => true
		];
		$result = $TestModel->buildQuery('all', ['returnQuery' => true, 'conditions' => ['user' => 'larry']]);
		$this->assertEquals($expected, $result);
	}

/**
 * test find('all') method
 *
 * @return void
 */
	public function testFindAll() {
		$this->loadFixtures('User');
		$TestModel = new User();
		$TestModel->cacheQueries = false;

		$result = $TestModel->find('all');
		$expected = [
			[
				'User' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31'
			]],
			[
				'User' => [
					'id' => '2',
					'user' => 'nate',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23',
					'updated' => '2007-03-17 01:20:31'
			]],
			[
				'User' => [
					'id' => '3',
					'user' => 'larry',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23',
					'updated' => '2007-03-17 01:22:31'
			]],
			[
				'User' => [
					'id' => '4',
					'user' => 'garrett',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23',
					'updated' => '2007-03-17 01:24:31'
		]]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', ['conditions' => 'User.id > 2']);
		$expected = [
			[
				'User' => [
					'id' => '3',
					'user' => 'larry',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23',
					'updated' => '2007-03-17 01:22:31'
			]],
			[
				'User' => [
					'id' => '4',
					'user' => 'garrett',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23',
					'updated' => '2007-03-17 01:24:31'
		]]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', [
			'conditions' => ['User.id !=' => '0', 'User.user LIKE' => '%arr%']
		]);
		$expected = [
			[
				'User' => [
					'id' => '3',
					'user' => 'larry',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23',
					'updated' => '2007-03-17 01:22:31'
			]],
			[
				'User' => [
					'id' => '4',
					'user' => 'garrett',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23',
					'updated' => '2007-03-17 01:24:31'
		]]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', ['conditions' => ['User.id' => '0']]);
		$expected = [];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', [
			'conditions' => ['or' => ['User.id' => '0', 'User.user LIKE' => '%a%']
		]]);

		$expected = [
			[
				'User' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31'
			]],
			[
				'User' => [
					'id' => '2',
					'user' => 'nate',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23',
					'updated' => '2007-03-17 01:20:31'
			]],
			[
				'User' => [
					'id' => '3',
					'user' => 'larry',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23',
					'updated' => '2007-03-17 01:22:31'
			]],
			[
				'User' => [
					'id' => '4',
					'user' => 'garrett',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23',
					'updated' => '2007-03-17 01:24:31'
		]]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', ['fields' => 'User.id, User.user']);
		$expected = [
				['User' => ['id' => '1', 'user' => 'mariano']],
				['User' => ['id' => '2', 'user' => 'nate']],
				['User' => ['id' => '3', 'user' => 'larry']],
				['User' => ['id' => '4', 'user' => 'garrett']]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', ['fields' => 'User.user', 'order' => 'User.user ASC']);
		$expected = [
				['User' => ['user' => 'garrett']],
				['User' => ['user' => 'larry']],
				['User' => ['user' => 'mariano']],
				['User' => ['user' => 'nate']]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', ['fields' => 'User.user', 'order' => 'User.user DESC']);
		$expected = [
				['User' => ['user' => 'nate']],
				['User' => ['user' => 'mariano']],
				['User' => ['user' => 'larry']],
				['User' => ['user' => 'garrett']]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', ['limit' => 3, 'page' => 1]);

		$expected = [
			[
				'User' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31'
			]],
			[
				'User' => [
					'id' => '2',
					'user' => 'nate',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23',
					'updated' => '2007-03-17 01:20:31'
			]],
			[
				'User' => [
					'id' => '3',
					'user' => 'larry',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23',
					'updated' => '2007-03-17 01:22:31'
		]]];
		$this->assertEquals($expected, $result);

		$ids = [4 => 1, 5 => 3];
		$result = $TestModel->find('all', [
			'conditions' => ['User.id' => $ids],
			'order' => 'User.id'
		]);
		$expected = [
			[
				'User' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31'
			]],
			[
				'User' => [
					'id' => '3',
					'user' => 'larry',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23',
					'updated' => '2007-03-17 01:22:31'
		]]];
		$this->assertEquals($expected, $result);

		// These tests are expected to fail on SQL Server since the LIMIT/OFFSET
		// hack can't handle small record counts.
		if (!($this->db instanceof Sqlserver)) {
			$result = $TestModel->find('all', ['limit' => 3, 'page' => 2]);
			$expected = [
				[
					'User' => [
						'id' => '4',
						'user' => 'garrett',
						'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
						'created' => '2007-03-17 01:22:23',
						'updated' => '2007-03-17 01:24:31'
			]]];
			$this->assertEquals($expected, $result);

			$result = $TestModel->find('all', ['limit' => 3, 'page' => 3]);
			$expected = [];
			$this->assertEquals($expected, $result);
		}
	}

/**
 * Test that find() with array conditions works when there is only one element.
 *
 * @return void
 */
	public function testFindAllArrayConditions() {
		$this->loadFixtures('User');
		$TestModel = new User();
		$TestModel->cacheQueries = false;

		$result = $TestModel->find('all', [
			'conditions' => ['User.id' => [3]],
		]);
		$expected = [
			[
				'User' => [
					'id' => '3',
					'user' => 'larry',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23',
					'updated' => '2007-03-17 01:22:31'
			]]
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', [
			'conditions' => ['User.user' => ['larry']],
		]);
		$this->assertEquals($expected, $result);
	}

/**
 * test find('list') method
 *
 * @return void
 */
	public function testFindList() {
		$this->loadFixtures('Article', 'Apple', 'Post', 'Author', 'User', 'Comment');

		$TestModel = new Article();
		$TestModel->displayField = 'title';

		$result = $TestModel->find('list', [
			'order' => 'Article.title ASC'
		]);

		$expected = [
			1 => 'First Article',
			2 => 'Second Article',
			3 => 'Third Article'
		];
		$this->assertEquals($expected, $result);

		$db = ConnectionManager::getDataSource('test');
		if ($db instanceof Mysql) {
			$result = $TestModel->find('list', [
				'order' => ['FIELD(Article.id, 3, 2) ASC', 'Article.title ASC']
			]);
			$expected = [
				1 => 'First Article',
				3 => 'Third Article',
				2 => 'Second Article'
			];
			$this->assertEquals($expected, $result);
		}

		$result = Hash::combine(
			$TestModel->find('all', [
				'order' => 'Article.title ASC',
				'fields' => ['id', 'title']
			]),
			'{n}.Article.id', '{n}.Article.title'
		);
		$expected = [
			1 => 'First Article',
			2 => 'Second Article',
			3 => 'Third Article'
		];
		$this->assertEquals($expected, $result);

		$result = Hash::combine(
			$TestModel->find('all', [
				'order' => 'Article.title ASC'
			]),
			'{n}.Article.id', '{n}.Article'
		);
		$expected = [
			1 => [
				'id' => 1,
				'user_id' => 1,
				'title' => 'First Article',
				'body' => 'First Article Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:39:23',
				'updated' => '2007-03-18 10:41:31'
			],
			2 => [
				'id' => 2,
				'user_id' => 3,
				'title' => 'Second Article',
				'body' => 'Second Article Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:41:23',
				'updated' => '2007-03-18 10:43:31'
			],
			3 => [
				'id' => 3,
				'user_id' => 1,
				'title' => 'Third Article',
				'body' => 'Third Article Body',
				'published' => 'Y',
				'created' => '2007-03-18 10:43:23',
				'updated' => '2007-03-18 10:45:31'
		]];

		$this->assertEquals($expected, $result);

		$result = Hash::combine(
			$TestModel->find('all', [
				'order' => 'Article.title ASC'
			]),
			'{n}.Article.id', '{n}.Article', '{n}.Article.user_id'
		);
		$expected = [
			1 => [
				1 => [
					'id' => 1,
					'user_id' => 1,
					'title' => 'First Article',
					'body' => 'First Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				3 => [
					'id' => 3,
					'user_id' => 1,
					'title' => 'Third Article',
					'body' => 'Third Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				]],
			3 => [
				2 => [
					'id' => 2,
					'user_id' => 3,
					'title' => 'Second Article',
					'body' => 'Second Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31'
		]]];

		$this->assertEquals($expected, $result);

		$result = Hash::combine(
			$TestModel->find('all', [
				'order' => 'Article.title ASC',
				'fields' => ['id', 'title', 'user_id']
			]),
			'{n}.Article.id', '{n}.Article.title', '{n}.Article.user_id'
		);

		$expected = [
			1 => [
				1 => 'First Article',
				3 => 'Third Article'
			],
			3 => [
				2 => 'Second Article'
		]];
		$this->assertEquals($expected, $result);

		$TestModel = new Apple();
		$expected = [
			1 => 'Red Apple 1',
			2 => 'Bright Red Apple',
			3 => 'green blue',
			4 => 'Test Name',
			5 => 'Blue Green',
			6 => 'My new apple',
			7 => 'Some odd color'
		];

		$this->assertEquals($expected, $TestModel->find('list'));
		$this->assertEquals($expected, $TestModel->Parent->find('list'));

		$TestModel = new Post();
		$result = $TestModel->find('list', [
			'fields' => 'Post.title'
		]);
		$expected = [
			1 => 'First Post',
			2 => 'Second Post',
			3 => 'Third Post'
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('list', [
			'fields' => 'title'
		]);
		$expected = [
			1 => 'First Post',
			2 => 'Second Post',
			3 => 'Third Post'
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('list', [
			'fields' => ['title', 'id']
		]);
		$expected = [
			'First Post' => '1',
			'Second Post' => '2',
			'Third Post' => '3'
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('list', [
			'fields' => ['title', 'id', 'created']
		]);
		$expected = [
			'2007-03-18 10:39:23' => [
				'First Post' => '1'
			],
			'2007-03-18 10:41:23' => [
				'Second Post' => '2'
			],
			'2007-03-18 10:43:23' => [
				'Third Post' => '3'
			],
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('list', [
			'fields' => ['Post.body']
		]);
		$expected = [
			1 => 'First Post Body',
			2 => 'Second Post Body',
			3 => 'Third Post Body'
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('list', [
			'fields' => ['Post.title', 'Post.body']
		]);
		$expected = [
			'First Post' => 'First Post Body',
			'Second Post' => 'Second Post Body',
			'Third Post' => 'Third Post Body'
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('list', [
			'fields' => ['Post.id', 'Post.title', 'Author.user'],
			'recursive' => 1
		]);
		$expected = [
			'mariano' => [
				1 => 'First Post',
				3 => 'Third Post'
			],
			'larry' => [
				2 => 'Second Post'
		]];
		$this->assertEquals($expected, $result);

		$TestModel = new User();
		$result = $TestModel->find('list', [
			'fields' => ['User.user', 'User.password']
		]);
		$expected = [
			'mariano' => '5f4dcc3b5aa765d61d8327deb882cf99',
			'nate' => '5f4dcc3b5aa765d61d8327deb882cf99',
			'larry' => '5f4dcc3b5aa765d61d8327deb882cf99',
			'garrett' => '5f4dcc3b5aa765d61d8327deb882cf99'
		];
		$this->assertEquals($expected, $result);

		$TestModel = new ModifiedAuthor();
		$result = $TestModel->find('list', [
			'fields' => ['Author.id', 'Author.user']
		]);
		$expected = [
			1 => 'mariano (CakePHP)',
			2 => 'nate (CakePHP)',
			3 => 'larry (CakePHP)',
			4 => 'garrett (CakePHP)'
		];
		$this->assertEquals($expected, $result);

		$TestModel = new Article();
		$TestModel->displayField = 'title';
		$result = $TestModel->find('list', [
			'conditions' => ['User.user' => 'mariano'],
			'recursive' => 0
		]);
		$expected = [
			1 => 'First Article',
			3 => 'Third Article'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test that find(list) works with array conditions that have only one element.
 *
 * @return void
 */
	public function testFindListArrayCondition() {
		$this->loadFixtures('User');
		$TestModel = new User();
		$TestModel->cacheQueries = false;

		$result = $TestModel->find('list', [
			'fields' => ['id', 'user'],
			'conditions' => ['User.id' => [3]],
		]);
		$expected = [
			3 => 'larry'
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('list', [
			'fields' => ['id', 'user'],
			'conditions' => ['User.user' => ['larry']],
		]);
		$this->assertEquals($expected, $result);
	}

/**
 * testFindField method
 *
 * @return void
 */
	public function testFindField() {
		$this->loadFixtures('User');
		$TestModel = new User();

		$TestModel->id = 1;
		$result = $TestModel->field('user');
		$this->assertEquals('mariano', $result);

		$result = $TestModel->field('User.user');
		$this->assertEquals('mariano', $result);

		$TestModel->id = false;
		$result = $TestModel->field('user', [
			'user' => 'mariano'
		]);
		$this->assertEquals('mariano', $result);
		$TestModel->order = null;
		$result = $TestModel->field('COUNT(*) AS count', true);
		$this->assertEquals(4, $result);

		$result = $TestModel->field('COUNT(*)', true);
		$this->assertEquals(4, $result);
	}

/**
 * testFindUnique method
 *
 * @return void
 */
	public function testFindUnique() {
		$this->loadFixtures('User');
		$TestModel = new User();

		$this->assertFalse($TestModel->isUnique([
			'user' => 'nate'
		]));
		$TestModel->id = 2;
		$this->assertTrue($TestModel->isUnique([
			'user' => 'nate'
		]));
		$this->assertFalse($TestModel->isUnique([
			'user' => 'nate',
			'password' => '5f4dcc3b5aa765d61d8327deb882cf99'
		]));
	}

/**
 * test find('count') method
 *
 * @return void
 */
	public function testFindCount() {
		$this->loadFixtures('User', 'Article', 'Comment', 'Tag', 'ArticlesTag');

		$TestModel = new User();
		$this->db->getLog(false, true);
		$result = $TestModel->find('count');
		$this->assertEquals(4, $result);

		$this->db->getLog(false, true);
		$fullDebug = $this->db->fullDebug;
		$this->db->fullDebug = true;
		$TestModel->order = 'User.id';
		$result = $TestModel->find('count');
		$this->db->fullDebug = $fullDebug;
		$this->assertEquals(4, $result);

		$log = $this->db->getLog();
		$this->assertTrue(isset($log['log'][0]['query']));
		$this->assertNotRegExp('/ORDER\s+BY/', $log['log'][0]['query']);

		$Article = new Article();
		$Article->order = null;
		$Article->recursive = -1;
		$expected = count($Article->find('all', [
			'fields' => ['Article.user_id'],
			'group' => 'Article.user_id']
		));
		$result = $Article->find('count', ['group' => ['Article.user_id']]);
		$this->assertEquals($expected, $result);

		$expected = count($Article->find('all', [
			'fields' => ['Article.user_id'],
			'conditions' => ['Article.user_id' => 1],
			'group' => 'Article.user_id']
		));
		$result = $Article->find('count', [
			'conditions' => ['Article.user_id' => 1],
			'group' => ['Article.user_id'],
		]);
		$this->assertEquals($expected, $result);
	}

/**
 * Test that find('first') does not use the id set to the object.
 *
 * @return void
 */
	public function testFindFirstNoIdUsed() {
		$this->loadFixtures('Project');

		$Project = new Project();
		$Project->id = 3;
		$result = $Project->find('first');

		$this->assertEquals('Project 1', $result['Project']['name'], 'Wrong record retrieved');
	}

/**
 * test find with COUNT(DISTINCT field)
 *
 * @return void
 */
	public function testFindCountDistinct() {
		$this->skipIf($this->db instanceof Sqlite, 'SELECT COUNT(DISTINCT field) is not compatible with SQLite.');
		$this->skipIf($this->db instanceof Sqlserver, 'This test is not compatible with SQL Server.');

		$this->loadFixtures('Project', 'Thread');
		$TestModel = new Project();
		$TestModel->create(['name' => 'project']) && $TestModel->save();
		$TestModel->create(['name' => 'project']) && $TestModel->save();
		$TestModel->create(['name' => 'project']) && $TestModel->save();

		$result = $TestModel->find('count', ['fields' => 'DISTINCT name']);
		$this->assertEquals(4, $result);
	}

/**
 * Test find(count) with Db::expression
 *
 * @return void
 */
	public function testFindCountWithDbExpressions() {
		$this->skipIf($this->db instanceof Postgres, 'testFindCountWithDbExpressions is not compatible with Postgres.');

		$this->loadFixtures('Project', 'Thread');
		$db = ConnectionManager::getDataSource('test');
		$TestModel = new Project();

		$result = $TestModel->find('count', ['conditions' => [
			$db->expression('Project.name = \'Project 3\'')
		]]);
		$this->assertEquals(1, $result);

		$result = $TestModel->find('count', ['conditions' => [
			'Project.name' => $db->expression('\'Project 3\'')
		]]);
		$this->assertEquals(1, $result);
	}

/**
 * testFindMagic method
 *
 * @return void
 */
	public function testFindMagic() {
		$this->loadFixtures('User', 'Comment', 'Article');
		$TestModel = new User();

		$result = $TestModel->findByUser('mariano');
		$expected = [
			'User' => [
				'id' => '1',
				'user' => 'mariano',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
				'created' => '2007-03-17 01:16:23',
				'updated' => '2007-03-17 01:18:31'
		]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->findByPassword('5f4dcc3b5aa765d61d8327deb882cf99');
		$expected = ['User' => [
			'id' => '1',
			'user' => 'mariano',
			'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
			'created' => '2007-03-17 01:16:23',
			'updated' => '2007-03-17 01:18:31'
		]];
		$this->assertEquals($expected, $result);

		$Comment = new Comment();
		$Comment->recursive = -1;
		$results = $Comment->findAllByUserId(1);
		$expected = [
			[
				'Comment' => [
					'id' => 3,
					'article_id' => 1,
					'user_id' => 1,
					'comment' => 'Third Comment for First Article',
					'published' => 'Y',
					'created' => '2007-03-18 10:49:23',
					'updated' => '2007-03-18 10:51:31'
				]
			],
			[
				'Comment' => [
					'id' => 4,
					'article_id' => 1,
					'user_id' => 1,
					'comment' => 'Fourth Comment for First Article',
					'published' => 'N',
					'created' => '2007-03-18 10:51:23',
					'updated' => '2007-03-18 10:53:31'
				]
			],
			[
				'Comment' => [
					'id' => 5,
					'article_id' => 2,
					'user_id' => 1,
					'comment' => 'First Comment for Second Article',
					'published' => 'Y',
					'created' => '2007-03-18 10:53:23',
					'updated' => '2007-03-18 10:55:31'
				]
			]
		];
		$this->assertEquals($expected, $results);

		$results = $Comment->findAllByUserIdAndPublished(1, 'Y');
		$expected = [
			[
				'Comment' => [
					'id' => 3,
					'article_id' => 1,
					'user_id' => 1,
					'comment' => 'Third Comment for First Article',
					'published' => 'Y',
					'created' => '2007-03-18 10:49:23',
					'updated' => '2007-03-18 10:51:31'
				]
			],
			[
				'Comment' => [
					'id' => 5,
					'article_id' => 2,
					'user_id' => 1,
					'comment' => 'First Comment for Second Article',
					'published' => 'Y',
					'created' => '2007-03-18 10:53:23',
					'updated' => '2007-03-18 10:55:31'
				]
			]
		];
		$this->assertEquals($expected, $results);

		$Article = new CustomArticle();
		$Article->recursive = -1;
		$results = $Article->findListByUserId(1);
		$expected = [
			1 => 'First Article',
			3 => 'Third Article'
		];
		$this->assertEquals($expected, $results);

		$results = $Article->findPublishedByUserId(1);
		$expected = [
			[
				'CustomArticle' => [
					'id' => 1,
					'user_id' => 1,
					'title' => 'First Article',
					'body' => 'First Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				]
			],
			[
				'CustomArticle' => [
					'id' => 3,
					'user_id' => 1,
					'title' => 'Third Article',
					'body' => 'Third Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				]
			]
		];
		$this->assertEquals($expected, $results);

		$results = $Article->findUnPublishedByUserId(1);
		$expected = [];
		$this->assertEquals($expected, $results);
	}

/**
 * testRead method
 *
 * @return void
 */
	public function testRead() {
		$this->loadFixtures('User', 'Article');
		$TestModel = new User();

		$result = $TestModel->read();
		$this->assertFalse($result);

		$TestModel->id = 2;
		$result = $TestModel->read();
		$expected = [
			'User' => [
				'id' => '2',
				'user' => 'nate',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
				'created' => '2007-03-17 01:18:23',
				'updated' => '2007-03-17 01:20:31'
		]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->read(null, 2);
		$expected = [
			'User' => [
				'id' => '2',
				'user' => 'nate',
				'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
				'created' => '2007-03-17 01:18:23',
				'updated' => '2007-03-17 01:20:31'
		]];
		$this->assertEquals($expected, $result);

		$TestModel->id = 2;
		$result = $TestModel->read(['id', 'user']);
		$expected = ['User' => ['id' => '2', 'user' => 'nate']];
		$this->assertEquals($expected, $result);

		$result = $TestModel->read('id, user', 2);
		$expected = [
			'User' => [
				'id' => '2',
				'user' => 'nate'
		]];
		$this->assertEquals($expected, $result);

		$result = $TestModel->bindModel(['hasMany' => ['Article']]);
		$this->assertTrue($result);

		$TestModel->id = 1;
		$result = $TestModel->read('id, user');
		$expected = [
			'User' => [
				'id' => '1',
				'user' => 'mariano'
			],
			'Article' => [
				[
					'id' => '1',
					'user_id' => '1',
					'title' => 'First Article',
					'body' => 'First Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				[
					'id' => '3',
					'user_id' => '1',
					'title' => 'Third Article',
					'body' => 'Third Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
		]]];
		$this->assertEquals($expected, $result);
	}

/**
 * testRecursiveRead method
 *
 * @return void
 */
	public function testRecursiveRead() {
		$this->loadFixtures(
			'User',
			'Article',
			'Comment',
			'Tag',
			'ArticlesTag',
			'Featured',
			'ArticleFeatured'
		);
		$TestModel = new User();

		$result = $TestModel->bindModel(['hasMany' => ['Article']], false);
		$this->assertTrue($result);

		$TestModel->recursive = 0;
		$result = $TestModel->read('id, user', 1);
		$expected = [
			'User' => ['id' => '1', 'user' => 'mariano'],
		];
		$this->assertEquals($expected, $result);

		$TestModel->recursive = 1;
		$result = $TestModel->read('id, user', 1);
		$expected = [
			'User' => [
				'id' => '1',
				'user' => 'mariano'
			],
			'Article' => [
				[
					'id' => '1',
					'user_id' => '1',
					'title' => 'First Article',
					'body' => 'First Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				[
					'id' => '3',
					'user_id' => '1',
					'title' => 'Third Article',
					'body' => 'Third Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
		]]];
		$this->assertEquals($expected, $result);

		$TestModel->recursive = 2;
		$result = $TestModel->read('id, user', 3);
		$expected = [
			'User' => [
				'id' => '3',
				'user' => 'larry'
			],
			'Article' => [
				[
					'id' => '2',
					'user_id' => '3',
					'title' => 'Second Article',
					'body' => 'Second Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31',
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
		]]]]];
		$this->assertEquals($expected, $result);
	}

	public function testRecursiveFindAll() {
		$this->loadFixtures(
			'User',
			'Article',
			'Comment',
			'Tag',
			'ArticlesTag',
			'Attachment',
			'ArticleFeatured',
			'ArticleFeaturedsTags',
			'Featured',
			'Category'
		);
		$TestModel = new Article();

		$result = $TestModel->find('all', ['conditions' => ['Article.user_id' => 1]]);
		$expected = [
			[
				'Article' => [
					'id' => '1',
					'user_id' => '1',
					'title' => 'First Article',
					'body' => 'First Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31'
				],
				'Comment' => [
					[
						'id' => '1',
						'article_id' => '1',
						'user_id' => '2',
						'comment' => 'First Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:45:23',
						'updated' => '2007-03-18 10:47:31'
					],
					[
						'id' => '2',
						'article_id' => '1',
						'user_id' => '4',
						'comment' => 'Second Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:47:23',
						'updated' => '2007-03-18 10:49:31'
					],
					[
						'id' => '3',
						'article_id' => '1',
						'user_id' => '1',
						'comment' => 'Third Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:49:23',
						'updated' => '2007-03-18 10:51:31'
					],
					[
						'id' => '4',
						'article_id' => '1',
						'user_id' => '1',
						'comment' => 'Fourth Comment for First Article',
						'published' => 'N',
						'created' => '2007-03-18 10:51:23',
						'updated' => '2007-03-18 10:53:31'
					]
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
			]]],
			[
				'Article' => [
					'id' => '3',
					'user_id' => '1',
					'title' => 'Third Article',
					'body' => 'Third Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31'
				],
				'Comment' => [],
				'Tag' => []
			]
		];
		$this->assertEquals($expected, $result);

		$result = $TestModel->find('all', [
			'conditions' => ['Article.user_id' => 3],
			'limit' => 1,
			'recursive' => 2
		]);

		$expected = [
			[
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
						'updated' => '2007-03-18 10:55:31',
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
							'id' => '1',
							'user' => 'mariano',
							'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23',
							'updated' => '2007-03-17 01:18:31'
						],
						'Attachment' => [
							'id' => '1',
							'comment_id' => 5,
							'attachment' => 'attachment.zip',
							'created' => '2007-03-18 10:51:23',
							'updated' => '2007-03-18 10:53:31'
						]
					],
					[
						'id' => '6',
						'article_id' => '2',
						'user_id' => '2',
						'comment' => 'Second Comment for Second Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:55:23',
						'updated' => '2007-03-18 10:57:31',
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
							'id' => '2',
							'user' => 'nate',
							'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:18:23',
							'updated' => '2007-03-17 01:20:31'
						],
						'Attachment' => []
					]
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
		]]]];

		$this->assertEquals($expected, $result);

		$Featured = new Featured();

		$Featured->recursive = 2;
		$Featured->bindModel([
			'belongsTo' => [
				'ArticleFeatured' => [
					'conditions' => "ArticleFeatured.published = 'Y'",
					'fields' => 'id, title, user_id, published'
				]
			]
		]);

		$Featured->ArticleFeatured->unbindModel([
			'hasMany' => ['Attachment', 'Comment'],
			'hasAndBelongsToMany' => ['Tag']]
		);

		$orderBy = 'ArticleFeatured.id ASC';
		$result = $Featured->find('all', [
			'order' => $orderBy, 'limit' => 3
		]);

		$expected = [
			[
				'Featured' => [
					'id' => '1',
					'article_featured_id' => '1',
					'category_id' => '1',
					'published_date' => '2007-03-31 10:39:23',
					'end_date' => '2007-05-15 10:39:23',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				'ArticleFeatured' => [
					'id' => '1',
					'title' => 'First Article',
					'user_id' => '1',
					'published' => 'Y',
					'User' => [
						'id' => '1',
						'user' => 'mariano',
						'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
						'created' => '2007-03-17 01:16:23',
						'updated' => '2007-03-17 01:18:31'
					],
					'Category' => [],
					'Featured' => [
						'id' => '1',
						'article_featured_id' => '1',
						'category_id' => '1',
						'published_date' => '2007-03-31 10:39:23',
						'end_date' => '2007-05-15 10:39:23',
						'created' => '2007-03-18 10:39:23',
						'updated' => '2007-03-18 10:41:31'
				]],
				'Category' => [
					'id' => '1',
					'parent_id' => '0',
					'name' => 'Category 1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
				]],
			[
				'Featured' => [
					'id' => '2',
					'article_featured_id' => '2',
					'category_id' => '1',
					'published_date' => '2007-03-31 10:39:23',
					'end_date' => '2007-05-15 10:39:23',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				'ArticleFeatured' => [
					'id' => '2',
					'title' => 'Second Article',
					'user_id' => '3',
					'published' => 'Y',
					'User' => [
						'id' => '3',
						'user' => 'larry',
						'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
						'created' => '2007-03-17 01:20:23',
						'updated' => '2007-03-17 01:22:31'
					],
					'Category' => [],
					'Featured' => [
						'id' => '2',
						'article_featured_id' => '2',
						'category_id' => '1',
						'published_date' => '2007-03-31 10:39:23',
						'end_date' => '2007-05-15 10:39:23',
						'created' => '2007-03-18 10:39:23',
						'updated' => '2007-03-18 10:41:31'
				]],
				'Category' => [
					'id' => '1',
					'parent_id' => '0',
					'name' => 'Category 1',
					'created' => '2007-03-18 15:30:23',
					'updated' => '2007-03-18 15:32:31'
		]]];
		$this->assertEquals($expected, $result);
	}

/**
 * testRecursiveFindAllWithLimit method
 *
 * @return void
 */
	public function testRecursiveFindAllWithLimit() {
		$this->loadFixtures('Article', 'User', 'Tag', 'ArticlesTag', 'Comment', 'Attachment');
		$TestModel = new Article();

		$TestModel->hasMany['Comment']['limit'] = 2;

		$result = $TestModel->find('all', [
			'conditions' => ['Article.user_id' => 1]
		]);
		$expected = [
			[
				'Article' => [
					'id' => '1',
					'user_id' => '1',
					'title' => 'First Article',
					'body' => 'First Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31'
				],
				'Comment' => [
					[
						'id' => '1',
						'article_id' => '1',
						'user_id' => '2',
						'comment' => 'First Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:45:23',
						'updated' => '2007-03-18 10:47:31'
					],
					[
						'id' => '2',
						'article_id' => '1',
						'user_id' => '4',
						'comment' => 'Second Comment for First Article',
						'published' => 'Y',
						'created' => '2007-03-18 10:47:23',
						'updated' => '2007-03-18 10:49:31'
					],
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
			]]],
			[
				'Article' => [
					'id' => '3',
					'user_id' => '1',
					'title' => 'Third Article',
					'body' => 'Third Article Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => '1',
					'user' => 'mariano',
					'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23',
					'updated' => '2007-03-17 01:18:31'
				],
				'Comment' => [],
				'Tag' => []
			]
		];
		$this->assertEquals($expected, $result);

		$TestModel->hasMany['Comment']['limit'] = 1;

		$result = $TestModel->find('all', [
			'conditions' => ['Article.user_id' => 3],
			'limit' => 1,
			'recursive' => 2
		]);
		$expected = [
			[
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
						'updated' => '2007-03-18 10:55:31',
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
							'id' => '1',
							'user' => 'mariano',
							'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23',
							'updated' => '2007-03-17 01:18:31'
						],
						'Attachment' => [
							'id' => '1',
							'comment_id' => 5,
							'attachment' => 'attachment.zip',
							'created' => '2007-03-18 10:51:23',
							'updated' => '2007-03-18 10:53:31'
						]
					]
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
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Testing availability of $this->findQueryType in Model callbacks
 *
 * @return void
 */
	public function testFindQueryTypeInCallbacks() {
		$this->loadFixtures('Comment');
		$Comment = new AgainModifiedComment();
		$comments = $Comment->find('all');
		$this->assertEquals('all', $comments[0]['Comment']['querytype']);
		$comments = $Comment->find('first');
		$this->assertEquals('first', $comments['Comment']['querytype']);
	}

/**
 * testVirtualFields()
 *
 * Test correct fetching of virtual fields
 * currently is not possible to do Relation.virtualField
 *
 * @return void
 */
	public function testVirtualFields() {
		$this->loadFixtures('Post', 'Author');
		$Post = ClassRegistry::init('Post');
		$Post->virtualFields = ['two' => "1 + 1"];
		$result = $Post->find('first');
		$this->assertEquals(2, $result['Post']['two']);

		// SQL Server does not support operators in expressions
		if (!($this->db instanceof Sqlserver)) {
			$Post->Author->virtualFields = ['false' => '1 = 2'];
			$result = $Post->find('first');
			$this->assertEquals(2, $result['Post']['two']);
			$this->assertFalse((bool)$result['Author']['false']);
		}

		$result = $Post->find('first', ['fields' => ['author_id']]);
		$this->assertFalse(isset($result['Post']['two']));
		$this->assertFalse(isset($result['Author']['false']));

		$result = $Post->find('first', ['fields' => ['author_id', 'two']]);
		$this->assertEquals(2, $result['Post']['two']);
		$this->assertFalse(isset($result['Author']['false']));

		$result = $Post->find('first', ['fields' => ['two']]);
		$this->assertEquals(2, $result['Post']['two']);

		$Post->id = 1;
		$result = $Post->field('two');
		$this->assertEquals(2, $result);

		$result = $Post->find('first', [
			'conditions' => ['two' => 2],
			'limit' => 1
		]);
		$this->assertEquals(2, $result['Post']['two']);

		$result = $Post->find('first', [
			'conditions' => ['two <' => 3],
			'limit' => 1
		]);
		$this->assertEquals(2, $result['Post']['two']);

		$result = $Post->find('first', [
			'conditions' => ['NOT' => ['two >' => 3]],
			'limit' => 1
		]);
		$this->assertEquals(2, $result['Post']['two']);

		$dbo = $Post->getDataSource();
		$Post->virtualFields = ['other_field' => 'Post.id + 1'];
		$result = $Post->find('first', [
			'conditions' => ['other_field' => 3],
			'limit' => 1
		]);
		$this->assertEquals(2, $result['Post']['id']);
		$Post->order = null;

		$Post->virtualFields = ['other_field' => 'Post.id + 1'];
		$result = $Post->find('all', [
			'fields' => [$dbo->calculate($Post, 'max', ['other_field'])]
		]);
		$this->assertEquals(4, $result[0][0]['other_field']);

		ClassRegistry::flush();
		$Writing = ClassRegistry::init(['class' => 'Post', 'alias' => 'Writing']);
		$Writing->virtualFields = ['two' => "1 + 1"];
		$result = $Writing->find('first');
		$this->assertEquals(2, $result['Writing']['two']);

		$Post->create();
		$Post->virtualFields = ['other_field' => 'COUNT(Post.id) + 1'];
		$result = $Post->field('other_field');
		$this->assertEquals(4, $result);
	}

/**
 * Test virtualfields that contain subqueries get correctly
 * quoted allowing reserved words to be used.
 *
 * @return void
 */
	public function testVirtualFieldSubqueryReservedWords() {
		$this->loadFixtures('User');
		$user = ClassRegistry::init('User');
		$user->cacheMethods = false;
		$ds = $user->getDataSource();

		$sub = $ds->buildStatement(
			[
				'fields' => ['Table.user'],
				'table' => $ds->fullTableName($user),
				'alias' => 'Table',
				'limit' => 1,
				'conditions' => [
					"Table.id > 1"
				]
			],
			$user
		);
		$user->virtualFields = [
			'sub_test' => $sub
		];

		$result = $user->find('first');
		$this->assertNotEmpty($result);
	}

/**
 * testVirtualFieldsOrder()
 *
 * Test correct order on virtual fields
 *
 * @return void
 */
	public function testVirtualFieldsOrder() {
		$this->loadFixtures('Post', 'Author');
		$Post = ClassRegistry::init('Post');
		$Post->virtualFields = ['other_field' => '10 - Post.id'];
		$result = $Post->find('list', ['order' => ['Post.other_field' => 'ASC']]);
		$expected = [
			'3' => 'Third Post',
			'2' => 'Second Post',
			'1' => 'First Post'
		];
		$this->assertEquals($expected, $result);

		$result = $Post->find('list', ['order' => ['Post.other_field' => 'DESC']]);
		$expected = [
			'1' => 'First Post',
			'2' => 'Second Post',
			'3' => 'Third Post'
		];
		$this->assertEquals($expected, $result);

		$Post->Author->virtualFields = ['joined' => 'Post.id * Author.id'];
		$result = $Post->find('all', [
			'order' => ['Post.id' => 'ASC']
		]);
		$result = Hash::extract($result, '{n}.Author.joined');
		$expected = [1, 6, 3];
		$this->assertEquals($expected, $result);

		$result = $Post->find('all', ['order' => ['Author.joined' => 'ASC']]);
		$result = Hash::extract($result, '{n}.Author.joined');
		$expected = [1, 3, 6];
		$this->assertEquals($expected, $result);

		$result = $Post->find('all', ['order' => ['Author.joined' => 'DESC']]);
		$result = Hash::extract($result, '{n}.Author.joined');
		$expected = [6, 3, 1];
		$this->assertEquals($expected, $result);
	}

/**
 * testVirtualFieldsMysql()
 *
 * Test correct fetching of virtual fields
 * currently is not possible to do Relation.virtualField
 *
 * @return void
 */
	public function testVirtualFieldsMysql() {
		$this->skipIf(!($this->db instanceof Mysql), 'The rest of virtualFields test only compatible with Mysql.');

		$this->loadFixtures('Post', 'Author');
		$Post = ClassRegistry::init('Post');

		$Post->create();
		$Post->virtualFields = [
			'low_title' => 'lower(Post.title)',
			'unique_test_field' => 'COUNT(Post.id)'
		];

		$expectation = [
			'Post' => [
				'low_title' => 'first post',
				'unique_test_field' => 1
			]
		];

		$result = $Post->find('first', [
			'fields' => array_keys($Post->virtualFields),
			'group' => ['low_title']
		]);

		$this->assertEquals($expectation, $result);

		$Author = ClassRegistry::init('Author');
		$Author->virtualFields = [
			'full_name' => 'CONCAT(Author.user, " ", Author.id)'
		];

		$result = $Author->find('first', [
			'conditions' => ['Author.user' => 'mariano'],
			'fields' => ['Author.password', 'Author.full_name'],
			'recursive' => -1
		]);
		$this->assertTrue(isset($result['Author']['full_name']));

		$result = $Author->find('first', [
			'conditions' => ['Author.user' => 'mariano'],
			'fields' => ['Author.full_name', 'Author.password'],
			'recursive' => -1
		]);
		$this->assertTrue(isset($result['Author']['full_name']));
	}

/**
 * test that virtual fields work when they don't contain functions.
 *
 * @return void
 */
	public function testVirtualFieldAsAString() {
		$this->loadFixtures('Post', 'Author');
		$Post = new Post();
		$Post->virtualFields = [
			'writer' => 'Author.user'
		];
		$result = $Post->find('first');
		$this->assertTrue(isset($result['Post']['writer']), 'virtual field not fetched %s');
	}

/**
 * test that isVirtualField will accept both aliased and non aliased fieldnames
 *
 * @return void
 */
	public function testIsVirtualField() {
		$this->loadFixtures('Post');
		$Post = ClassRegistry::init('Post');
		$Post->virtualFields = ['other_field' => 'COUNT(Post.id) + 1'];

		$this->assertTrue($Post->isVirtualField('other_field'));
		$this->assertTrue($Post->isVirtualField('Post.other_field'));
		$this->assertFalse($Post->isVirtualField('Comment.other_field'), 'Other models should not match.');
		$this->assertFalse($Post->isVirtualField('id'));
		$this->assertFalse($Post->isVirtualField('Post.id'));
		$this->assertFalse($Post->isVirtualField([]));
	}

/**
 * test that getting virtual fields works with and without model alias attached
 *
 * @return void
 */
	public function testGetVirtualField() {
		$this->loadFixtures('Post');
		$Post = ClassRegistry::init('Post');
		$Post->virtualFields = ['other_field' => 'COUNT(Post.id) + 1'];

		$this->assertEquals($Post->getVirtualField('other_field'), $Post->virtualFields['other_field']);
		$this->assertEquals($Post->getVirtualField('Post.other_field'), $Post->virtualFields['other_field']);
	}

/**
 * test that checks for error when NOT condition passed in key and a 1 element array value
 *
 * @return void
 */
	public function testNotInArrayWithOneValue() {
		$this->loadFixtures('Article');
		$Article = new Article();
		$Article->recursive = -1;

		$result = $Article->find(
			'all',
			[
				'conditions' => [
					'Article.id NOT' => [1]
				]
			]
		);
		$this->assertTrue(is_array($result) && !empty($result));
	}

/**
 * test to assert that != in key together with a single element array will work
 *
 * @return void
 */
	public function testNotEqualsInArrayWithOneValue() {
		$this->loadFixtures('Article');
		$Article = new Article();
		$Article->recursive = -1;

		$result = $Article->find(
			'all',
			[
				'conditions' => [
					'Article.id !=' => [1]
				]
			]
		);
		$this->assertTrue(is_array($result) && !empty($result));
	}

/**
 * test custom find method
 *
 * @return void
 */
	public function testfindCustom() {
		$this->loadFixtures('Article');
		$Article = new CustomArticle();
		$data = ['user_id' => 3, 'title' => 'Fourth Article', 'body' => 'Article Body, unpublished', 'published' => 'N'];
		$Article->create($data);
		$Article->save(null, false);
		$this->assertEquals(4, $Article->id);

		$result = $Article->find('published');
		$this->assertEquals(3, count($result));

		$result = $Article->find('unPublished');
		$this->assertEquals(1, count($result));
	}

/**
 * test after find callback on related model
 *
 * @return void
 */
	public function testRelatedAfterFindCallback() {
		$this->loadFixtures('Something', 'SomethingElse', 'JoinThing');
		$Something = new Something();

		$Something->bindModel([
			'hasMany' => [
				'HasMany' => [
					'className' => 'JoinThing',
					'foreignKey' => 'something_id'
				]
			],
			'hasOne' => [
				'HasOne' => [
					'className' => 'JoinThing',
					'foreignKey' => 'something_id'
				]
			]
		]);

		$results = $Something->find('all');

		$expected = [
			[
				'Something' => [
					'id' => '1',
					'title' => 'First Post',
					'body' => 'First Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31'
				],
				'HasOne' => [
					'id' => '1',
					'something_id' => '1',
					'something_else_id' => '2',
					'doomed' => true,
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
					'afterFind' => 'Successfully added by AfterFind'
				],
				'HasMany' => [
					[
						'id' => '1',
						'something_id' => '1',
						'something_else_id' => '2',
						'doomed' => true,
						'created' => '2007-03-18 10:39:23',
						'updated' => '2007-03-18 10:41:31',
						'afterFind' => 'Successfully added by AfterFind'
					]
				],
				'SomethingElse' => [
					[
						'id' => '2',
						'title' => 'Second Post',
						'body' => 'Second Post Body',
						'published' => 'Y',
						'created' => '2007-03-18 10:41:23',
						'updated' => '2007-03-18 10:43:31',
						'afterFind' => 'Successfully added by AfterFind',
						'JoinThing' => [
							'doomed' => true,
							'something_id' => '1',
							'something_else_id' => '2',
							'afterFind' => 'Successfully added by AfterFind'
						]
					]
				]
			],
			[
				'Something' => [
					'id' => '2',
					'title' => 'Second Post',
					'body' => 'Second Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31'
				],
				'HasOne' => [
					'id' => '2',
					'something_id' => '2',
					'something_else_id' => '3',
					'doomed' => false,
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31',
					'afterFind' => 'Successfully added by AfterFind'
				],
				'HasMany' => [
					[
						'id' => '2',
						'something_id' => '2',
						'something_else_id' => '3',
						'doomed' => false,
						'created' => '2007-03-18 10:41:23',
						'updated' => '2007-03-18 10:43:31',
						'afterFind' => 'Successfully added by AfterFind'
					]
				],
				'SomethingElse' => [
					[
						'id' => '3',
						'title' => 'Third Post',
						'body' => 'Third Post Body',
						'published' => 'Y',
						'created' => '2007-03-18 10:43:23',
						'updated' => '2007-03-18 10:45:31',
						'afterFind' => 'Successfully added by AfterFind',
						'JoinThing' => [
							'doomed' => false,
							'something_id' => '2',
							'something_else_id' => '3',
							'afterFind' => 'Successfully added by AfterFind'
						]
					]
				]
			],
			[
				'Something' => [
					'id' => '3',
					'title' => 'Third Post',
					'body' => 'Third Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				],
				'HasOne' => [
					'id' => '3',
					'something_id' => '3',
					'something_else_id' => '1',
					'doomed' => true,
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31',
					'afterFind' => 'Successfully added by AfterFind'
				],
				'HasMany' => [
					[
						'id' => '3',
						'something_id' => '3',
						'something_else_id' => '1',
						'doomed' => true,
						'created' => '2007-03-18 10:43:23',
						'updated' => '2007-03-18 10:45:31',
						'afterFind' => 'Successfully added by AfterFind'
					]
				],
				'SomethingElse' => [
					[
						'id' => '1',
						'title' => 'First Post',
						'body' => 'First Post Body',
						'published' => 'Y',
						'created' => '2007-03-18 10:39:23',
						'updated' => '2007-03-18 10:41:31',
						'afterFind' => 'Successfully added by AfterFind',
						'JoinThing' => [
							'doomed' => true,
							'something_id' => '3',
							'something_else_id' => '1',
							'afterFind' => 'Successfully added by AfterFind'
						]
					]
				]
			]
		];
		$this->assertEquals($expected, $results, 'Model related with has* afterFind callback fails');

		$JoinThing = new JoinThing();
		$JoinThing->unbindModel([
			'belongsTo' => [
				'Something'
			]
		]);
		$results = $JoinThing->find('all');

		$expected = [
			[
				'JoinThing' => [
					'id' => '1',
					'something_id' => '1',
					'something_else_id' => '2',
					'doomed' => true,
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
					'afterFind' => 'Successfully added by AfterFind'
				],
				'SomethingElse' => [
					'id' => '2',
					'title' => 'Second Post',
					'body' => 'Second Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31',
					'afterFind' => 'Successfully added by AfterFind'
				]
			],
			[
				'JoinThing' => [
					'id' => '2',
					'something_id' => '2',
					'something_else_id' => '3',
					'doomed' => false,
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31',
					'afterFind' => 'Successfully added by AfterFind'
				],
				'SomethingElse' => [
					'id' => '3',
					'title' => 'Third Post',
					'body' => 'Third Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31',
					'afterFind' => 'Successfully added by AfterFind'
				]
			],
			[
				'JoinThing' => [
					'id' => '3',
					'something_id' => '3',
					'something_else_id' => '1',
					'doomed' => true,
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31',
					'afterFind' => 'Successfully added by AfterFind'
				],
				'SomethingElse' => [
					'id' => '1',
					'title' => 'First Post',
					'body' => 'First Post Body',
					'published' => 'Y',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
					'afterFind' => 'Successfully added by AfterFind'
				]
			]
		];
		$this->assertEquals($expected, $results, 'Model related with belongsTo afterFind callback fails');
	}
}
