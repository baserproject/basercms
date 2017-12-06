<?php
/**
 * SetTest file
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
 * @package       Cake.Test.Case.Utility
 * @since         CakePHP(tm) v 1.2.0.4206
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Set', 'Utility');
App::uses('Model', 'Model');

/**
 * SetTest class
 *
 * @package       Cake.Test.Case.Utility
 */
class SetTest extends CakeTestCase {

/**
 * testNumericKeyExtraction method
 *
 * @return void
 */
	public function testNumericKeyExtraction() {
		$data = ['plugin' => null, 'controller' => '', 'action' => '', 1, 'whatever'];
		$this->assertEquals([1, 'whatever'], Set::extract($data, '{n}'));
		$this->assertEquals(['plugin' => null, 'controller' => '', 'action' => ''], Set::diff($data, Set::extract($data, '{n}')));
	}

/**
 * testEnum method
 *
 * @return void
 */
	public function testEnum() {
		$result = Set::enum(1, 'one, two');
		$this->assertEquals('two', $result);
		$result = Set::enum(2, 'one, two');
		$this->assertNull($result);

		$set = ['one', 'two'];
		$result = Set::enum(0, $set);
		$this->assertEquals('one', $result);
		$result = Set::enum(1, $set);
		$this->assertEquals('two', $result);

		$result = Set::enum(1, ['one', 'two']);
		$this->assertEquals('two', $result);
		$result = Set::enum(2, ['one', 'two']);
		$this->assertNull($result);

		$result = Set::enum('first', ['first' => 'one', 'second' => 'two']);
		$this->assertEquals('one', $result);
		$result = Set::enum('third', ['first' => 'one', 'second' => 'two']);
		$this->assertNull($result);

		$result = Set::enum('no', ['no' => 0, 'yes' => 1]);
		$this->assertEquals(0, $result);
		$result = Set::enum('not sure', ['no' => 0, 'yes' => 1]);
		$this->assertNull($result);

		$result = Set::enum(0);
		$this->assertEquals('no', $result);
		$result = Set::enum(1);
		$this->assertEquals('yes', $result);
		$result = Set::enum(2);
		$this->assertNull($result);
	}

/**
 * testFilter method
 *
 * @see Hash test cases, as Set::filter() is just a proxy.
 * @return void
 */
	public function testFilter() {
		$result = Set::filter(['0', false, true, 0, ['one thing', 'I can tell you', 'is you got to be', false]]);
		$expected = ['0', 2 => true, 3 => 0, 4 => ['one thing', 'I can tell you', 'is you got to be']];
		$this->assertSame($expected, $result);
	}

/**
 * testNumericArrayCheck method
 *
 * @see Hash test cases, as Set::numeric() is just a proxy.
 * @return void
 */
	public function testNumericArrayCheck() {
		$data = ['one'];
		$this->assertTrue(Set::numeric(array_keys($data)));
	}

/**
 * testKeyCheck method
 *
 * @return void
 */
	public function testKeyCheck() {
		$data = ['Multi' => ['dimensonal' => ['array']]];
		$this->assertTrue(Set::check($data, 'Multi.dimensonal'));
		$this->assertFalse(Set::check($data, 'Multi.dimensonal.array'));

		$data = [
			[
				'Article' => ['id' => '1', 'user_id' => '1', 'title' => 'First Article', 'body' => 'First Article Body', 'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'],
				'User' => ['id' => '1', 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'],
				'Comment' => [
					['id' => '1', 'article_id' => '1', 'user_id' => '2', 'comment' => 'First Comment for First Article', 'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31'],
					['id' => '2', 'article_id' => '1', 'user_id' => '4', 'comment' => 'Second Comment for First Article', 'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31'],
				],
				'Tag' => [
					['id' => '1', 'tag' => 'tag1', 'created' => '2007-03-18 12:22:23', 'updated' => '2007-03-18 12:24:31'],
					['id' => '2', 'tag' => 'tag2', 'created' => '2007-03-18 12:24:23', 'updated' => '2007-03-18 12:26:31']
				]
			],
			[
				'Article' => ['id' => '3', 'user_id' => '1', 'title' => 'Third Article', 'body' => 'Third Article Body', 'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'],
				'User' => ['id' => '1', 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'],
				'Comment' => [],
				'Tag' => []
			]
		];
		$this->assertTrue(Set::check($data, '0.Article.user_id'));
		$this->assertTrue(Set::check($data, '0.Comment.0.id'));
		$this->assertFalse(Set::check($data, '0.Comment.0.id.0'));
		$this->assertTrue(Set::check($data, '0.Article.user_id'));
		$this->assertFalse(Set::check($data, '0.Article.user_id.a'));
	}

/**
 * testMerge method
 *
 * @return void
 */
	public function testMerge() {
		$r = Set::merge(['foo']);
		$this->assertEquals(['foo'], $r);

		$r = Set::merge('foo');
		$this->assertEquals(['foo'], $r);

		$r = Set::merge('foo', 'bar');
		$this->assertEquals(['foo', 'bar'], $r);

		$r = Set::merge(['foo'], [], ['bar']);
		$this->assertEquals(['foo', 'bar'], $r);

		$r = Set::merge('foo', ['user' => 'bob', 'no-bar'], 'bar');
		$this->assertEquals(['foo', 'user' => 'bob', 'no-bar', 'bar'], $r);

		$a = ['foo', 'foo2'];
		$b = ['bar', 'bar2'];
		$this->assertEquals(['foo', 'foo2', 'bar', 'bar2'], Set::merge($a, $b));

		$a = ['foo' => 'bar', 'bar' => 'foo'];
		$b = ['foo' => 'no-bar', 'bar' => 'no-foo'];
		$this->assertEquals(['foo' => 'no-bar', 'bar' => 'no-foo'], Set::merge($a, $b));

		$a = ['users' => ['bob', 'jim']];
		$b = ['users' => ['lisa', 'tina']];
		$this->assertEquals(['users' => ['bob', 'jim', 'lisa', 'tina']], Set::merge($a, $b));

		$a = ['users' => ['jim', 'bob']];
		$b = ['users' => 'none'];
		$this->assertEquals(['users' => 'none'], Set::merge($a, $b));

		$a = ['users' => ['lisa' => ['id' => 5, 'pw' => 'secret']], 'cakephp'];
		$b = ['users' => ['lisa' => ['pw' => 'new-pass', 'age' => 23]], 'ice-cream'];
		$this->assertEquals(['users' => ['lisa' => ['id' => 5, 'pw' => 'new-pass', 'age' => 23]], 'cakephp', 'ice-cream'], Set::merge($a, $b));

		$c = ['users' => ['lisa' => ['pw' => 'you-will-never-guess', 'age' => 25, 'pet' => 'dog']], 'chocolate'];
		$expected = ['users' => ['lisa' => ['id' => 5, 'pw' => 'you-will-never-guess', 'age' => 25, 'pet' => 'dog']], 'cakephp', 'ice-cream', 'chocolate'];
		$this->assertEquals($expected, Set::merge($a, $b, $c));

		$this->assertEquals($expected, Set::merge($a, $b, [], $c));

		$r = Set::merge($a, $b, $c);
		$this->assertEquals($expected, $r);

		$a = ['Tree', 'CounterCache',
				'Upload' => ['folder' => 'products',
					'fields' => ['image_1_id', 'image_2_id', 'image_3_id', 'image_4_id', 'image_5_id']]];
		$b = ['Cacheable' => ['enabled' => false],
				'Limit',
				'Bindable',
				'Validator',
				'Transactional'];

		$expected = ['Tree', 'CounterCache',
				'Upload' => ['folder' => 'products',
					'fields' => ['image_1_id', 'image_2_id', 'image_3_id', 'image_4_id', 'image_5_id']],
				'Cacheable' => ['enabled' => false],
				'Limit',
				'Bindable',
				'Validator',
				'Transactional'];

		$this->assertEquals($expected, Set::merge($a, $b));

		$expected = ['Tree' => null, 'CounterCache' => null,
				'Upload' => ['folder' => 'products',
					'fields' => ['image_1_id', 'image_2_id', 'image_3_id', 'image_4_id', 'image_5_id']],
				'Cacheable' => ['enabled' => false],
				'Limit' => null,
				'Bindable' => null,
				'Validator' => null,
				'Transactional' => null];

		$this->assertEquals($expected, Set::normalize(Set::merge($a, $b)));
	}

/**
 * testSort method
 *
 * @return void
 */
	public function testSort() {
		$result = Set::sort([], '{n}.name', 'asc');
		$this->assertEquals([], $result);

		$a = [
			0 => ['Person' => ['name' => 'Jeff'], 'Friend' => [['name' => 'Nate']]],
			1 => ['Person' => ['name' => 'Tracy'], 'Friend' => [['name' => 'Lindsay']]]
		];
		$b = [
			0 => ['Person' => ['name' => 'Tracy'], 'Friend' => [['name' => 'Lindsay']]],
			1 => ['Person' => ['name' => 'Jeff'], 'Friend' => [['name' => 'Nate']]]

		];
		$a = Set::sort($a, '{n}.Friend.{n}.name', 'asc');
		$this->assertEquals($a, $b);

		$b = [
			0 => ['Person' => ['name' => 'Jeff'], 'Friend' => [['name' => 'Nate']]],
			1 => ['Person' => ['name' => 'Tracy'], 'Friend' => [['name' => 'Lindsay']]]
		];
		$a = [
			0 => ['Person' => ['name' => 'Tracy'], 'Friend' => [['name' => 'Lindsay']]],
			1 => ['Person' => ['name' => 'Jeff'], 'Friend' => [['name' => 'Nate']]]

		];
		$a = Set::sort($a, '{n}.Friend.{n}.name', 'desc');
		$this->assertEquals($a, $b);

		$a = [
			0 => ['Person' => ['name' => 'Jeff'], 'Friend' => [['name' => 'Nate']]],
			1 => ['Person' => ['name' => 'Tracy'], 'Friend' => [['name' => 'Lindsay']]],
			2 => ['Person' => ['name' => 'Adam'], 'Friend' => [['name' => 'Bob']]]
		];
		$b = [
			0 => ['Person' => ['name' => 'Adam'], 'Friend' => [['name' => 'Bob']]],
			1 => ['Person' => ['name' => 'Jeff'], 'Friend' => [['name' => 'Nate']]],
			2 => ['Person' => ['name' => 'Tracy'], 'Friend' => [['name' => 'Lindsay']]]
		];
		$a = Set::sort($a, '{n}.Person.name', 'asc');
		$this->assertEquals($a, $b);

		$a = [
			[7, 6, 4],
			[3, 4, 5],
			[3, 2, 1],
		];

		$b = [
			[3, 2, 1],
			[3, 4, 5],
			[7, 6, 4],
		];

		$a = Set::sort($a, '{n}.{n}', 'asc');
		$this->assertEquals($a, $b);

		$a = [
			[7, 6, 4],
			[3, 4, 5],
			[3, 2, [1, 1, 1]],
		];

		$b = [
			[3, 2, [1, 1, 1]],
			[3, 4, 5],
			[7, 6, 4],
		];

		$a = Set::sort($a, '{n}', 'asc');
		$this->assertEquals($a, $b);

		$a = [
			0 => ['Person' => ['name' => 'Jeff']],
			1 => ['Shirt' => ['color' => 'black']]
		];
		$b = [
			0 => ['Shirt' => ['color' => 'black']],
			1 => ['Person' => ['name' => 'Jeff']],
		];
		$a = Set::sort($a, '{n}.Person.name', 'ASC');
		$this->assertEquals($a, $b);

		$names = [
			['employees' => [['name' => ['first' => 'John', 'last' => 'Doe']]]],
			['employees' => [['name' => ['first' => 'Jane', 'last' => 'Doe']]]],
			['employees' => [['name' => []]]],
			['employees' => [['name' => []]]]
		];
		$result = Set::sort($names, '{n}.employees.0.name', 'asc', 1);
		$expected = [
			['employees' => [['name' => ['first' => 'John', 'last' => 'Doe']]]],
			['employees' => [['name' => ['first' => 'Jane', 'last' => 'Doe']]]],
			['employees' => [['name' => []]]],
			['employees' => [['name' => []]]]
		];
		$this->assertEquals($expected, $result);

		$menus = [
			'blogs' => ['title' => 'Blogs', 'weight' => 3],
			'comments' => ['title' => 'Comments', 'weight' => 2],
			'users' => ['title' => 'Users', 'weight' => 1],
			];
		$expected = [
			'users' => ['title' => 'Users', 'weight' => 1],
			'comments' => ['title' => 'Comments', 'weight' => 2],
			'blogs' => ['title' => 'Blogs', 'weight' => 3],
			];
		$result = Set::sort($menus, '{[a-z]+}.weight', 'ASC');
		$this->assertEquals($expected, $result);
	}

/**
 * test sorting with string keys.
 *
 * @return void
 */
	public function testSortString() {
		$toSort = [
			'four' => ['number' => 4, 'some' => 'foursome'],
			'six' => ['number' => 6, 'some' => 'sixsome'],
			'five' => ['number' => 5, 'some' => 'fivesome'],
			'two' => ['number' => 2, 'some' => 'twosome'],
			'three' => ['number' => 3, 'some' => 'threesome']
		];
		$sorted = Set::sort($toSort, '{s}.number', 'asc');
		$expected = [
			'two' => ['number' => 2, 'some' => 'twosome'],
			'three' => ['number' => 3, 'some' => 'threesome'],
			'four' => ['number' => 4, 'some' => 'foursome'],
			'five' => ['number' => 5, 'some' => 'fivesome'],
			'six' => ['number' => 6, 'some' => 'sixsome']
		];
		$this->assertEquals($expected, $sorted);
	}

/**
 * test sorting with out of order keys.
 *
 * @return void
 */
	public function testSortWithOutOfOrderKeys() {
		$data = [
			9 => ['class' => 510, 'test2' => 2],
			1 => ['class' => 500, 'test2' => 1],
			2 => ['class' => 600, 'test2' => 2],
			5 => ['class' => 625, 'test2' => 4],
			0 => ['class' => 605, 'test2' => 3],
		];
		$expected = [
			['class' => 500, 'test2' => 1],
			['class' => 510, 'test2' => 2],
			['class' => 600, 'test2' => 2],
			['class' => 605, 'test2' => 3],
			['class' => 625, 'test2' => 4],
		];
		$result = Set::sort($data, '{n}.class', 'asc');
		$this->assertEquals($expected, $result);

		$result = Set::sort($data, '{n}.test2', 'asc');
		$this->assertEquals($expected, $result);
	}

/**
 * testExtract method
 *
 * @return void
 */
	public function testExtract() {
		$a = [
			[
				'Article' => ['id' => '1', 'user_id' => '1', 'title' => 'First Article', 'body' => 'First Article Body', 'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'],
				'User' => ['id' => '1', 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'],
				'Comment' => [
					['id' => '1', 'article_id' => '1', 'user_id' => '2', 'comment' => 'First Comment for First Article', 'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31'],
					['id' => '2', 'article_id' => '1', 'user_id' => '4', 'comment' => 'Second Comment for First Article', 'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31'],
				],
				'Tag' => [
					['id' => '1', 'tag' => 'tag1', 'created' => '2007-03-18 12:22:23', 'updated' => '2007-03-18 12:24:31'],
					['id' => '2', 'tag' => 'tag2', 'created' => '2007-03-18 12:24:23', 'updated' => '2007-03-18 12:26:31']
				],
				'Deep' => [
					'Nesting' => [
						'test' => [
							1 => 'foo',
							2 => [
								'and' => ['more' => 'stuff']
							]
						]
					]
				]
			],
			[
				'Article' => ['id' => '3', 'user_id' => '1', 'title' => 'Third Article', 'body' => 'Third Article Body', 'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'],
				'User' => ['id' => '2', 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'],
				'Comment' => [],
				'Tag' => []
			],
			[
				'Article' => ['id' => '3', 'user_id' => '1', 'title' => 'Third Article', 'body' => 'Third Article Body', 'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'],
				'User' => ['id' => '3', 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'],
				'Comment' => [],
				'Tag' => []
			],
			[
				'Article' => ['id' => '3', 'user_id' => '1', 'title' => 'Third Article', 'body' => 'Third Article Body', 'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'],
				'User' => ['id' => '4', 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'],
				'Comment' => [],
				'Tag' => []
			],
			[
				'Article' => ['id' => '3', 'user_id' => '1', 'title' => 'Third Article', 'body' => 'Third Article Body', 'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'],
				'User' => ['id' => '5', 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'],
				'Comment' => [],
				'Tag' => []
			]
		];
		$b = ['Deep' => $a[0]['Deep']];
		$c = [
			['a' => ['I' => ['a' => 1]]],
			[
				'a' => [
					2
				]
			],
			['a' => ['II' => ['a' => 3, 'III' => ['a' => ['foo' => 4]]]]],
		];

		$expected = [['a' => $c[2]['a']]];
		$r = Set::extract('/a/II[a=3]/..', $c);
		$this->assertEquals($expected, $r);

		$expected = [1, 2, 3, 4, 5];
		$this->assertEquals($expected, Set::extract('/User/id', $a));

		$expected = [1, 2, 3, 4, 5];
		$this->assertEquals($expected, Set::extract('/User/id', $a));

		$expected = [
			['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4], ['id' => 5]
		];

		$r = Set::extract('/User/id', $a, ['flatten' => false]);
		$this->assertEquals($expected, $r);

		$expected = [['test' => $a[0]['Deep']['Nesting']['test']]];
		$this->assertEquals($expected, Set::extract('/Deep/Nesting/test', $a));
		$this->assertEquals($expected, Set::extract('/Deep/Nesting/test', $b));

		$expected = [['test' => $a[0]['Deep']['Nesting']['test']]];
		$r = Set::extract('/Deep/Nesting/test/1/..', $a);
		$this->assertEquals($expected, $r);

		$expected = [['test' => $a[0]['Deep']['Nesting']['test']]];
		$r = Set::extract('/Deep/Nesting/test/2/and/../..', $a);
		$this->assertEquals($expected, $r);

		$expected = [['test' => $a[0]['Deep']['Nesting']['test']]];
		$r = Set::extract('/Deep/Nesting/test/2/../../../Nesting/test/2/..', $a);
		$this->assertEquals($expected, $r);

		$expected = [2];
		$r = Set::extract('/User[2]/id', $a);
		$this->assertEquals($expected, $r);

		$expected = [4, 5];
		$r = Set::extract('/User[id>3]/id', $a);
		$this->assertEquals($expected, $r);

		$expected = [2, 3];
		$r = Set::extract('/User[id>1][id<=3]/id', $a);
		$this->assertEquals($expected, $r);

		$expected = [['I'], ['II']];
		$r = Set::extract('/a/@*', $c);
		$this->assertEquals($expected, $r);

		$single = [
			'User' => [
				'id' => 4,
				'name' => 'Neo',
			]
		];
		$tricky = [
			0 => [
				'User' => [
					'id' => 1,
					'name' => 'John',
				]
			],
			1 => [
				'User' => [
					'id' => 2,
					'name' => 'Bob',
				]
			],
			2 => [
				'User' => [
					'id' => 3,
					'name' => 'Tony',
				]
			],
			'User' => [
				'id' => 4,
				'name' => 'Neo',
			]
		];

		$expected = [1, 2, 3, 4];
		$r = Set::extract('/User/id', $tricky);
		$this->assertEquals($expected, $r);

		$expected = [4];
		$r = Set::extract('/User/id', $single);
		$this->assertEquals($expected, $r);

		$expected = [1, 3];
		$r = Set::extract('/User[name=/n/]/id', $tricky);
		$this->assertEquals($expected, $r);

		$expected = [4];
		$r = Set::extract('/User[name=/N/]/id', $tricky);
		$this->assertEquals($expected, $r);

		$expected = [1, 3, 4];
		$r = Set::extract('/User[name=/N/i]/id', $tricky);
		$this->assertEquals($expected, $r);

		$expected = [['id', 'name'], ['id', 'name'], ['id', 'name'], ['id', 'name']];
		$r = Set::extract('/User/@*', $tricky);
		$this->assertEquals($expected, $r);

		$common = [
			[
				'Article' => [
					'id' => 1,
					'name' => 'Article 1',
				],
				'Comment' => [
					[
						'id' => 1,
						'user_id' => 5,
						'article_id' => 1,
						'text' => 'Comment 1',
					],
					[
						'id' => 2,
						'user_id' => 23,
						'article_id' => 1,
						'text' => 'Comment 2',
					],
					[
						'id' => 3,
						'user_id' => 17,
						'article_id' => 1,
						'text' => 'Comment 3',
					],
				],
			],
			[
				'Article' => [
					'id' => 2,
					'name' => 'Article 2',
				],
				'Comment' => [
					[
						'id' => 4,
						'user_id' => 2,
						'article_id' => 2,
						'text' => 'Comment 4',
						'addition' => '',
					],
					[
						'id' => 5,
						'user_id' => 23,
						'article_id' => 2,
						'text' => 'Comment 5',
						'addition' => 'foo',
					],
				],
			],
			[
				'Article' => [
					'id' => 3,
					'name' => 'Article 3',
				],
				'Comment' => [],
			]
		];

		$r = Set::extract('/Comment/id', $common);
		$expected = [1, 2, 3, 4, 5];
		$this->assertEquals($expected, $r);

		$expected = [1, 2, 4, 5];
		$r = Set::extract('/Comment[id!=3]/id', $common);
		$this->assertEquals($expected, $r);

		$r = Set::extract('/', $common);
		$this->assertEquals($r, $common);

		$expected = [1, 2, 4, 5];
		$r = Set::extract($common, '/Comment[id!=3]/id');
		$this->assertEquals($expected, $r);

		$expected = [$common[0]['Comment'][2]];
		$r = Set::extract($common, '/Comment/2');
		$this->assertEquals($expected, $r);

		$expected = [$common[0]['Comment'][0]];
		$r = Set::extract($common, '/Comment[1]/.[id=1]');
		$this->assertEquals($expected, $r);

		$expected = [$common[1]['Comment'][1]];
		$r = Set::extract($common, '/1/Comment/.[2]');
		$this->assertEquals($expected, $r);

		$expected = [];
		$r = Set::extract('/User/id', []);
		$this->assertEquals($expected, $r);

		$expected = [5];
		$r = Set::extract('/Comment/id[:last]', $common);
		$this->assertEquals($expected, $r);

		$expected = [1];
		$r = Set::extract('/Comment/id[:first]', $common);
		$this->assertEquals($expected, $r);

		$expected = [3];
		$r = Set::extract('/Article[:last]/id', $common);
		$this->assertEquals($expected, $r);

		$expected = [['Comment' => $common[1]['Comment'][0]]];
		$r = Set::extract('/Comment[addition=]', $common);
		$this->assertEquals($expected, $r);

		$habtm = [
			[
				'Post' => [
					'id' => 1,
					'title' => 'great post',
				],
				'Comment' => [
					[
						'id' => 1,
						'text' => 'foo',
						'User' => [
							'id' => 1,
							'name' => 'bob'
						],
					],
					[
						'id' => 2,
						'text' => 'bar',
						'User' => [
							'id' => 2,
							'name' => 'tod'
						],
					],
				],
			],
			[
				'Post' => [
					'id' => 2,
					'title' => 'fun post',
				],
				'Comment' => [
					[
						'id' => 3,
						'text' => '123',
						'User' => [
							'id' => 3,
							'name' => 'dan'
						],
					],
					[
						'id' => 4,
						'text' => '987',
						'User' => [
							'id' => 4,
							'name' => 'jim'
						],
					],
				],
			],
		];

		$r = Set::extract('/Comment/User[name=/bob|dan/]/..', $habtm);
		$this->assertEquals('bob', $r[0]['Comment']['User']['name']);
		$this->assertEquals('dan', $r[1]['Comment']['User']['name']);
		$this->assertEquals(2, count($r));

		$r = Set::extract('/Comment/User[name=/bob|tod/]/..', $habtm);
		$this->assertEquals('bob', $r[0]['Comment']['User']['name']);

		$this->assertEquals('tod', $r[1]['Comment']['User']['name']);
		$this->assertEquals(2, count($r));

		$tree = [
			[
				'Category' => ['name' => 'Category 1'],
				'children' => [['Category' => ['name' => 'Category 1.1']]]
			],
			[
				'Category' => ['name' => 'Category 2'],
				'children' => [
					['Category' => ['name' => 'Category 2.1']],
					['Category' => ['name' => 'Category 2.2']]
				]
			],
			[
				'Category' => ['name' => 'Category 3'],
				'children' => [['Category' => ['name' => 'Category 3.1']]]
			]
		];

		$expected = [['Category' => $tree[1]['Category']]];
		$r = Set::extract('/Category[name=Category 2]', $tree);
		$this->assertEquals($expected, $r);

		$expected = [
			['Category' => $tree[1]['Category'], 'children' => $tree[1]['children']]
		];
		$r = Set::extract('/Category[name=Category 2]/..', $tree);
		$this->assertEquals($expected, $r);

		$expected = [
			['children' => $tree[1]['children'][0]],
			['children' => $tree[1]['children'][1]]
		];
		$r = Set::extract('/Category[name=Category 2]/../children', $tree);
		$this->assertEquals($expected, $r);

		$habtm = [
			[
				'Post' => [
					'id' => 1,
					'title' => 'great post',
				],
				'Comment' => [
					[
						'id' => 1,
						'text' => 'foo',
						'User' => [
							'id' => 1,
							'name' => 'bob'
						],
					],
					[
						'id' => 2,
						'text' => 'bar',
						'User' => [
							'id' => 2,
							'name' => 'tod'
						],
					],
				],
			],
			[
				'Post' => [
					'id' => 2,
					'title' => 'fun post',
				],
				'Comment' => [
					[
						'id' => 3,
						'text' => '123',
						'User' => [
							'id' => 3,
							'name' => 'dan'
						],
					],
					[
						'id' => 4,
						'text' => '987',
						'User' => [
							'id' => 4,
							'name' => 'jim'
						],
					],
				],
			],
		];

		$r = Set::extract('/Comment/User[name=/\w+/]/..', $habtm);
		$this->assertEquals('bob', $r[0]['Comment']['User']['name']);
		$this->assertEquals('tod', $r[1]['Comment']['User']['name']);
		$this->assertEquals('dan', $r[2]['Comment']['User']['name']);
		$this->assertEquals('dan', $r[3]['Comment']['User']['name']);
		$this->assertEquals(4, count($r));

		$r = Set::extract('/Comment/User[name=/[a-z]+/]/..', $habtm);
		$this->assertEquals('bob', $r[0]['Comment']['User']['name']);
		$this->assertEquals('tod', $r[1]['Comment']['User']['name']);
		$this->assertEquals('dan', $r[2]['Comment']['User']['name']);
		$this->assertEquals('dan', $r[3]['Comment']['User']['name']);
		$this->assertEquals(4, count($r));

		$r = Set::extract('/Comment/User[name=/bob|dan/]/..', $habtm);
		$this->assertEquals('bob', $r[0]['Comment']['User']['name']);
		$this->assertEquals('dan', $r[1]['Comment']['User']['name']);
		$this->assertEquals(2, count($r));

		$r = Set::extract('/Comment/User[name=/bob|tod/]/..', $habtm);
		$this->assertEquals('bob', $r[0]['Comment']['User']['name']);
		$this->assertEquals('tod', $r[1]['Comment']['User']['name']);
		$this->assertEquals(2, count($r));

		$mixedKeys = [
			'User' => [
				0 => [
					'id' => 4,
					'name' => 'Neo'
				],
				1 => [
					'id' => 5,
					'name' => 'Morpheus'
				],
				'stringKey' => []
			]
		];
		$expected = ['Neo', 'Morpheus'];
		$r = Set::extract('/User/name', $mixedKeys);
		$this->assertEquals($expected, $r);

		$f = [
			[
				'file' => [
					'name' => 'zipfile.zip',
					'type' => 'application/zip',
					'tmp_name' => '/tmp/php178.tmp',
					'error' => 0,
					'size' => '564647'
				]
			],
			[
				'file' => [
					'name' => 'zipfile2.zip',
					'type' => 'application/x-zip-compressed',
					'tmp_name' => '/tmp/php179.tmp',
					'error' => 0,
					'size' => '354784'
				]
			],
			[
				'file' => [
					'name' => 'picture.jpg',
					'type' => 'image/jpeg',
					'tmp_name' => '/tmp/php180.tmp',
					'error' => 0,
					'size' => '21324'
				]
			]
		];
		$expected = [['name' => 'zipfile2.zip', 'type' => 'application/x-zip-compressed', 'tmp_name' => '/tmp/php179.tmp', 'error' => 0, 'size' => '354784']];
		$r = Set::extract('/file/.[type=application/x-zip-compressed]', $f);
		$this->assertEquals($expected, $r);

		$expected = [['name' => 'zipfile.zip', 'type' => 'application/zip', 'tmp_name' => '/tmp/php178.tmp', 'error' => 0, 'size' => '564647']];
		$r = Set::extract('/file/.[type=application/zip]', $f);
		$this->assertEquals($expected, $r);

		$f = [
			[
				'file' => [
					'name' => 'zipfile.zip',
					'type' => 'application/zip',
					'tmp_name' => '/tmp/php178.tmp',
					'error' => 0,
					'size' => '564647'
				]
			],
			[
				'file' => [
					'name' => 'zipfile2.zip',
					'type' => 'application/x zip compressed',
					'tmp_name' => '/tmp/php179.tmp',
					'error' => 0,
					'size' => '354784'
				]
			],
			[
				'file' => [
					'name' => 'picture.jpg',
					'type' => 'image/jpeg',
					'tmp_name' => '/tmp/php180.tmp',
					'error' => 0,
					'size' => '21324'
				]
			]
		];
		$expected = [['name' => 'zipfile2.zip', 'type' => 'application/x zip compressed', 'tmp_name' => '/tmp/php179.tmp', 'error' => 0, 'size' => '354784']];
		$r = Set::extract('/file/.[type=application/x zip compressed]', $f);
		$this->assertEquals($expected, $r);

		$expected = [
			['name' => 'zipfile.zip', 'type' => 'application/zip', 'tmp_name' => '/tmp/php178.tmp', 'error' => 0, 'size' => '564647'],
			['name' => 'zipfile2.zip', 'type' => 'application/x zip compressed', 'tmp_name' => '/tmp/php179.tmp', 'error' => 0, 'size' => '354784']
		];
		$r = Set::extract('/file/.[tmp_name=/tmp\/php17/]', $f);
		$this->assertEquals($expected, $r);

		$hasMany = [
			'Node' => [
				'id' => 1,
				'name' => 'First',
				'state' => 50
			],
			'ParentNode' => [
				0 => [
					'id' => 2,
					'name' => 'Second',
					'state' => 60,
				]
			]
		];
		$result = Set::extract('/ParentNode/name', $hasMany);
		$expected = ['Second'];
		$this->assertEquals($expected, $result);

		$data = [
			[
				'Category' => [
					'id' => 1,
					'name' => 'First'
				],
				0 => [
					'value' => 50
				]
			],
			[
				'Category' => [
					'id' => 2,
					'name' => 'Second'
				],
				0 => [
					'value' => 60
				]
			]
		];
		$expected = [
			[
				'Category' => [
					'id' => 1,
					'name' => 'First'
				],
				0 => [
					'value' => 50
				]
			]
		];
		$result = Set::extract('/Category[id=1]/..', $data);
		$this->assertEquals($expected, $result);

		$data = [
			[
				'ChildNode' => ['id' => 1],
				['name' => 'Item 1']
			],
			[
				'ChildNode' => ['id' => 2],
				['name' => 'Item 2']
			],
		];

		$expected = [
			'Item 1',
			'Item 2'
		];
		$result = Set::extract('/0/name', $data);
		$this->assertEquals($expected, $result);

		$data = [
			['A1', 'B1'],
			['A2', 'B2']
		];
		$expected = ['A1', 'A2'];
		$result = Set::extract('/0', $data);
		$this->assertEquals($expected, $result);
	}

/**
 * test parent selectors with extract
 *
 * @return void
 */
	public function testExtractParentSelector() {
		$tree = [
			[
				'Category' => [
					'name' => 'Category 1'
				],
				'children' => [
					[
						'Category' => [
							'name' => 'Category 1.1'
						]
					]
				]
			],
			[
				'Category' => [
					'name' => 'Category 2'
				],
				'children' => [
					[
						'Category' => [
							'name' => 'Category 2.1'
						]
					],
					[
						'Category' => [
							'name' => 'Category 2.2'
						]
					],
				]
			],
			[
				'Category' => [
					'name' => 'Category 3'
				],
				'children' => [
					[
						'Category' => [
							'name' => 'Category 3.1'
						]
					]
				]
			]
		];
		$expected = [['Category' => $tree[1]['Category']]];
		$r = Set::extract('/Category[name=Category 2]', $tree);
		$this->assertEquals($expected, $r);

		$expected = [['Category' => $tree[1]['Category'], 'children' => $tree[1]['children']]];
		$r = Set::extract('/Category[name=Category 2]/..', $tree);
		$this->assertEquals($expected, $r);

		$expected = [['children' => $tree[1]['children'][0]], ['children' => $tree[1]['children'][1]]];
		$r = Set::extract('/Category[name=Category 2]/../children', $tree);
		$this->assertEquals($expected, $r);

		$single = [
			[
				'CallType' => [
					'name' => 'Internal Voice'
				],
				'x' => [
					'hour' => 7
				]
			]
		];

		$expected = [7];
		$r = Set::extract('/CallType[name=Internal Voice]/../x/hour', $single);
		$this->assertEquals($expected, $r);

		$multiple = [
			[
				'CallType' => [
					'name' => 'Internal Voice'
				],
				'x' => [
					'hour' => 7
				]
			],
			[
				'CallType' => [
					'name' => 'Internal Voice'
				],
				'x' => [
					'hour' => 2
				]
			],
			[
				'CallType' => [
					'name' => 'Internal Voice'
				],
				'x' => [
					'hour' => 1
				]
			]
		];

		$expected = [7, 2, 1];
		$r = Set::extract('/CallType[name=Internal Voice]/../x/hour', $multiple);
		$this->assertEquals($expected, $r);

		$a = [
			'Model' => [
				'0' => [
					'id' => 18,
					'SubModelsModel' => [
						'id' => 1,
						'submodel_id' => 66,
						'model_id' => 18,
						'type' => 1
					],
				],
				'1' => [
					'id' => 0,
					'SubModelsModel' => [
						'id' => 2,
						'submodel_id' => 66,
						'model_id' => 0,
						'type' => 1
					],
				],
				'2' => [
					'id' => 17,
					'SubModelsModel' => [
						'id' => 3,
						'submodel_id' => 66,
						'model_id' => 17,
						'type' => 2
					],
				],
				'3' => [
					'id' => 0,
					'SubModelsModel' => [
						'id' => 4,
						'submodel_id' => 66,
						'model_id' => 0,
						'type' => 2
					]
				]
			]
		];

		$expected = [
			[
				'Model' => [
					'id' => 17,
					'SubModelsModel' => [
						'id' => 3,
						'submodel_id' => 66,
						'model_id' => 17,
						'type' => 2
					],
				]
			],
			[
				'Model' => [
					'id' => 0,
					'SubModelsModel' => [
						'id' => 4,
						'submodel_id' => 66,
						'model_id' => 0,
						'type' => 2
					]
				]
			]
		];
		$r = Set::extract('/Model/SubModelsModel[type=2]/..', $a);
		$this->assertEquals($expected, $r);
	}

/**
 * test that extract() still works when arrays don't contain a 0 index.
 *
 * @return void
 */
	public function testExtractWithNonZeroArrays() {
		$nonZero = [
			1 => [
				'User' => [
					'id' => 1,
					'name' => 'John',
				]
			],
			2 => [
				'User' => [
					'id' => 2,
					'name' => 'Bob',
				]
			],
			3 => [
				'User' => [
					'id' => 3,
					'name' => 'Tony',
				]
			]
		];
		$expected = [1, 2, 3];
		$r = Set::extract('/User/id', $nonZero);
		$this->assertEquals($expected, $r);

		$expected = [
			['User' => ['id' => 1, 'name' => 'John']],
			['User' => ['id' => 2, 'name' => 'Bob']],
			['User' => ['id' => 3, 'name' => 'Tony']],
		];
		$result = Set::extract('/User', $nonZero);
		$this->assertEquals($expected, $result);

		$nonSequential = [
			'User' => [
				0 => ['id' => 1],
				2 => ['id' => 2],
				6 => ['id' => 3],
				9 => ['id' => 4],
				3 => ['id' => 5],
			],
		];

		$nonZero = [
			'User' => [
				2 => ['id' => 1],
				4 => ['id' => 2],
				6 => ['id' => 3],
				9 => ['id' => 4],
				3 => ['id' => 5],
			],
		];

		$expected = [1, 2, 3, 4, 5];
		$this->assertEquals($expected, Set::extract('/User/id', $nonSequential));

		$result = Set::extract('/User/id', $nonZero);
		$this->assertEquals($expected, $result, 'Failed non zero array key extract');

		$expected = [1, 2, 3, 4, 5];
		$this->assertEquals($expected, Set::extract('/User/id', $nonSequential));

		$result = Set::extract('/User/id', $nonZero);
		$this->assertEquals($expected, $result, 'Failed non zero array key extract');

		$startingAtOne = [
			'Article' => [
				1 => [
					'id' => 1,
					'approved' => 1,
				],
			]
		];

		$expected = [0 => ['Article' => ['id' => 1, 'approved' => 1]]];
		$result = Set::extract('/Article[approved=1]', $startingAtOne);
		$this->assertEquals($expected, $result);

		$items = [
			240 => [
				'A' => [
					'field1' => 'a240',
					'field2' => 'a240',
				],
				'B' => [
					'field1' => 'b240',
					'field2' => 'b240'
				],
			]
		];

		$expected = [
			0 => 'b240'
		];

		$result = Set::extract('/B/field1', $items);
		$this->assertSame($expected, $result);
		$this->assertSame($result, Set::extract('{n}.B.field1', $items));
	}

/**
 * testExtractWithArrays method
 *
 * @return void
 */
	public function testExtractWithArrays() {
		$data = [
			'Level1' => [
				'Level2' => ['test1', 'test2'],
				'Level2bis' => ['test3', 'test4']
			]
		];
		$this->assertEquals([['Level2' => ['test1', 'test2']]], Set::extract('/Level1/Level2', $data));
		$this->assertEquals([['Level2bis' => ['test3', 'test4']]], Set::extract('/Level1/Level2bis', $data));
	}

/**
 * test extract() with elements that have non-array children.
 *
 * @return void
 */
	public function testExtractWithNonArrayElements() {
		$data = [
			'node' => [
				['foo'],
				'bar'
			]
		];
		$result = Set::extract('/node', $data);
		$expected = [
			['node' => ['foo']],
			'bar'
		];
		$this->assertEquals($expected, $result);

		$data = [
			'node' => [
				'foo' => ['bar'],
				'bar' => ['foo']
			]
		];
		$result = Set::extract('/node', $data);
		$expected = [
			['foo' => ['bar']],
			['bar' => ['foo']],
		];
		$this->assertEquals($expected, $result);

		$data = [
			'node' => [
				'foo' => [
					'bar'
				],
				'bar' => 'foo'
			]
		];
		$result = Set::extract('/node', $data);
		$expected = [
			['foo' => ['bar']],
			'foo'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test that extract() + matching can hit null things.
 *
 * @return void
 */
	public function testExtractMatchesNull() {
		$data = [
			'Country' => [
				['name' => 'Canada'],
				['name' => 'Australia'],
				['name' => null],
			]
		];
		$result = Set::extract('/Country[name=/Canada|^$/]', $data);
		$expected = [
			[
				'Country' => [
					'name' => 'Canada',
				],
			],
			[
				'Country' => [
					'name' => null,
				],
			],
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testMatches method
 *
 * @return void
 */
	public function testMatches() {
		$a = [
			['Article' => ['id' => 1, 'title' => 'Article 1']],
			['Article' => ['id' => 2, 'title' => 'Article 2']],
			['Article' => ['id' => 3, 'title' => 'Article 3']]
		];

		$this->assertTrue(Set::matches(['id=2'], $a[1]['Article']));
		$this->assertFalse(Set::matches(['id>2'], $a[1]['Article']));
		$this->assertTrue(Set::matches(['id>=2'], $a[1]['Article']));
		$this->assertFalse(Set::matches(['id>=3'], $a[1]['Article']));
		$this->assertTrue(Set::matches(['id<=2'], $a[1]['Article']));
		$this->assertFalse(Set::matches(['id<2'], $a[1]['Article']));
		$this->assertTrue(Set::matches(['id>1'], $a[1]['Article']));
		$this->assertTrue(Set::matches(['id>1', 'id<3', 'id!=0'], $a[1]['Article']));

		$this->assertTrue(Set::matches(['3'], null, 3));
		$this->assertTrue(Set::matches(['5'], null, 5));

		$this->assertTrue(Set::matches(['id'], $a[1]['Article']));
		$this->assertTrue(Set::matches(['id', 'title'], $a[1]['Article']));
		$this->assertFalse(Set::matches(['non-existant'], $a[1]['Article']));

		$this->assertTrue(Set::matches('/Article[id=2]', $a));
		$this->assertFalse(Set::matches('/Article[id=4]', $a));
		$this->assertTrue(Set::matches([], $a));

		$r = [
			'Attachment' => [
				'keep' => []
			],
			'Comment' => [
				'keep' => [
					'Attachment' => [
						'fields' => [
							0 => 'attachment',
						],
					],
				]
			],
			'User' => [
				'keep' => []
			],
			'Article' => [
				'keep' => [
					'Comment' => [
						'fields' => [
							0 => 'comment',
							1 => 'published',
						],
					],
					'User' => [
						'fields' => [
							0 => 'user',
						],
					],
				]
			]
		];

		$this->assertTrue(Set::matches('/Article/keep/Comment', $r));
		$this->assertEquals(['comment', 'published'], Set::extract('/Article/keep/Comment/fields', $r));
		$this->assertEquals(['user'], Set::extract('/Article/keep/User/fields', $r));
	}

/**
 * testSetExtractReturnsEmptyArray method
 *
 * @return void
 */
	public function testSetExtractReturnsEmptyArray() {
		$this->assertEquals(Set::extract([], '/Post/id'), []);

		$this->assertEquals(Set::extract('/Post/id', []), []);

		$this->assertEquals(Set::extract('/Post/id', [
			['Post' => ['name' => 'bob']],
			['Post' => ['name' => 'jim']]
		]), []);

		$this->assertEquals(Set::extract([], 'Message.flash'), null);
	}

/**
 * testClassicExtract method
 *
 * @return void
 */
	public function testClassicExtract() {
		$a = [
			['Article' => ['id' => 1, 'title' => 'Article 1']],
			['Article' => ['id' => 2, 'title' => 'Article 2']],
			['Article' => ['id' => 3, 'title' => 'Article 3']]
		];

		$result = Set::extract($a, '{n}.Article.id');
		$expected = [1, 2, 3];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '{n}.Article.title');
		$expected = ['Article 1', 'Article 2', 'Article 3'];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '1.Article.title');
		$expected = 'Article 2';
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '3.Article.title');
		$expected = null;
		$this->assertEquals($expected, $result);

		$a = [
			[
				'Article' => ['id' => 1, 'title' => 'Article 1',
				'User' => ['id' => 1, 'username' => 'mariano.iglesias']]
			],
			[
				'Article' => ['id' => 2, 'title' => 'Article 2',
				'User' => ['id' => 1, 'username' => 'mariano.iglesias']]
			],
			[
				'Article' => ['id' => 3, 'title' => 'Article 3',
				'User' => ['id' => 2, 'username' => 'phpnut']]
			]
		];

		$result = Set::extract($a, '{n}.Article.User.username');
		$expected = ['mariano.iglesias', 'mariano.iglesias', 'phpnut'];
		$this->assertEquals($expected, $result);

		$a = [
			[
				'Article' => [
					'id' => 1, 'title' => 'Article 1',
					'Comment' => [
						['id' => 10, 'title' => 'Comment 10'],
						['id' => 11, 'title' => 'Comment 11'],
						['id' => 12, 'title' => 'Comment 12']
					]
				]
			],
			[
				'Article' => [
					'id' => 2, 'title' => 'Article 2',
					'Comment' => [
						['id' => 13, 'title' => 'Comment 13'],
						['id' => 14, 'title' => 'Comment 14']
					]
				]
			],
			['Article' => ['id' => 3, 'title' => 'Article 3']]
		];

		$result = Set::extract($a, '{n}.Article.Comment.{n}.id');
		$expected = [[10, 11, 12], [13, 14], null];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '{n}.Article.Comment.{n}.title');
		$expected = [
			['Comment 10', 'Comment 11', 'Comment 12'],
			['Comment 13', 'Comment 14'],
			null
		];
		$this->assertEquals($expected, $result);

		$a = [['1day' => '20 sales'], ['1day' => '2 sales']];
		$result = Set::extract($a, '{n}.1day');
		$expected = ['20 sales', '2 sales'];
		$this->assertEquals($expected, $result);

		$a = [
			'pages' => ['name' => 'page'],
			'fruites' => ['name' => 'fruit'],
			0 => ['name' => 'zero']
		];
		$result = Set::extract($a, '{s}.name');
		$expected = ['page', 'fruit'];
		$this->assertEquals($expected, $result);

		$a = [
			0 => ['pages' => ['name' => 'page']],
			1 => ['fruites' => ['name' => 'fruit']],
			'test' => [['name' => 'jippi']],
			'dot.test' => [['name' => 'jippi']]
		];

		$result = Set::extract($a, '{n}.{s}.name');
		$expected = [0 => ['page'], 1 => ['fruit']];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '{s}.{n}.name');
		$expected = [['jippi'], ['jippi']];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '{\w+}.{\w+}.name');
		$expected = [
			['pages' => 'page'],
			['fruites' => 'fruit'],
			'test' => ['jippi'],
			'dot.test' => ['jippi']
		];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '{\d+}.{\w+}.name');
		$expected = [['pages' => 'page'], ['fruites' => 'fruit']];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '{n}.{\w+}.name');
		$expected = [['pages' => 'page'], ['fruites' => 'fruit']];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '{s}.{\d+}.name');
		$expected = [['jippi'], ['jippi']];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '{s}');
		$expected = [[['name' => 'jippi']], [['name' => 'jippi']]];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '{[a-z]}');
		$expected = [
			'test' => [['name' => 'jippi']],
			'dot.test' => [['name' => 'jippi']]
		];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, '{dot\.test}.{n}');
		$expected = ['dot.test' => [['name' => 'jippi']]];
		$this->assertEquals($expected, $result);

		$a = new stdClass();
		$a->articles = [
			['Article' => ['id' => 1, 'title' => 'Article 1']],
			['Article' => ['id' => 2, 'title' => 'Article 2']],
			['Article' => ['id' => 3, 'title' => 'Article 3']]
		];

		$result = Set::extract($a, 'articles.{n}.Article.id');
		$expected = [1, 2, 3];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, 'articles.{n}.Article.title');
		$expected = ['Article 1', 'Article 2', 'Article 3'];
		$this->assertEquals($expected, $result);

		$a = new ArrayObject();
		$a['articles'] = [
			['Article' => ['id' => 1, 'title' => 'Article 1']],
			['Article' => ['id' => 2, 'title' => 'Article 2']],
			['Article' => ['id' => 3, 'title' => 'Article 3']]
		];

		$result = Set::extract($a, 'articles.{n}.Article.id');
		$expected = [1, 2, 3];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, 'articles.{n}.Article.title');
		$expected = ['Article 1', 'Article 2', 'Article 3'];
		$this->assertEquals($expected, $result);

		$result = Set::extract($a, 'articles.0.Article.title');
		$expected = 'Article 1';
		$this->assertEquals($expected, $result);
	}

/**
 * test classicExtract with keys that exceed 32bit max int.
 *
 * @return void
 */
	public function testClassicExtractMaxInt() {
		$data = [
			'Data' => [
				'13376924712' => 'abc'
			]
		];
		$this->assertEquals('abc', Set::classicExtract($data, 'Data.13376924712'));
	}

/**
 * testInsert method
 *
 * @see Hash tests, as Set::insert() is just a proxy.
 * @return void
 */
	public function testInsert() {
		$a = [
			'pages' => ['name' => 'page']
		];

		$result = Set::insert($a, 'files', ['name' => 'files']);
		$expected = [
			'pages' => ['name' => 'page'],
			'files' => ['name' => 'files']
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testRemove method
 *
 * @return void
 */
	public function testRemove() {
		$a = [
			'pages' => ['name' => 'page'],
			'files' => ['name' => 'files']
		];

		$result = Set::remove($a, 'files');
		$expected = [
			'pages' => ['name' => 'page']
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testCheck method
 *
 * @return void
 */
	public function testCheck() {
		$set = [
			'My Index 1' => ['First' => 'The first item']
		];
		$this->assertTrue(Set::check($set, 'My Index 1.First'));
		$this->assertTrue(Set::check($set, 'My Index 1'));
		$this->assertEquals(Set::check($set, []), $set);

		$set = [
			'My Index 1' => ['First' => ['Second' => ['Third' => ['Fourth' => 'Heavy. Nesting.']]]]
		];
		$this->assertTrue(Set::check($set, 'My Index 1.First.Second'));
		$this->assertTrue(Set::check($set, 'My Index 1.First.Second.Third'));
		$this->assertTrue(Set::check($set, 'My Index 1.First.Second.Third.Fourth'));
		$this->assertFalse(Set::check($set, 'My Index 1.First.Seconds.Third.Fourth'));
	}

/**
 * testWritingWithFunkyKeys method
 *
 * @return void
 */
	public function testWritingWithFunkyKeys() {
		$set = Set::insert([], 'Session Test', "test");
		$this->assertEquals('test', Set::extract($set, 'Session Test'));

		$set = Set::remove($set, 'Session Test');
		$this->assertFalse(Set::check($set, 'Session Test'));

		$expected = ['Session Test' => ['Test Case' => 'test']];
		$this->assertEquals($expected, Set::insert([], 'Session Test.Test Case', "test"));
		$this->assertTrue(Set::check($expected, 'Session Test.Test Case'));
	}

/**
 * testDiff method
 *
 * @return void
 */
	public function testDiff() {
		$a = [
			0 => ['name' => 'main'],
			1 => ['name' => 'about']
		];
		$b = [
			0 => ['name' => 'main'],
			1 => ['name' => 'about'],
			2 => ['name' => 'contact']
		];

		$result = Set::diff($a, $b);
		$expected = [
			2 => ['name' => 'contact']
		];
		$this->assertEquals($expected, $result);

		$result = Set::diff($a, []);
		$expected = $a;
		$this->assertEquals($expected, $result);

		$result = Set::diff([], $b);
		$expected = $b;
		$this->assertEquals($expected, $result);

		$b = [
			0 => ['name' => 'me'],
			1 => ['name' => 'about']
		];

		$result = Set::diff($a, $b);
		$expected = [
			0 => ['name' => 'main']
		];
		$this->assertEquals($expected, $result);

		$a = [];
		$b = ['name' => 'bob', 'address' => 'home'];
		$result = Set::diff($a, $b);
		$this->assertEquals($b, $result);

		$a = ['name' => 'bob', 'address' => 'home'];
		$b = [];
		$result = Set::diff($a, $b);
		$this->assertEquals($a, $result);

		$a = ['key' => true, 'another' => false, 'name' => 'me'];
		$b = ['key' => 1, 'another' => 0];
		$expected = ['name' => 'me'];
		$result = Set::diff($a, $b);
		$this->assertEquals($expected, $result);

		$a = ['key' => 'value', 'another' => null, 'name' => 'me'];
		$b = ['key' => 'differentValue', 'another' => null];
		$expected = ['key' => 'value', 'name' => 'me'];
		$result = Set::diff($a, $b);
		$this->assertEquals($expected, $result);

		$a = ['key' => 'value', 'another' => null, 'name' => 'me'];
		$b = ['key' => 'differentValue', 'another' => 'value'];
		$expected = ['key' => 'value', 'another' => null, 'name' => 'me'];
		$result = Set::diff($a, $b);
		$this->assertEquals($expected, $result);

		$a = ['key' => 'value', 'another' => null, 'name' => 'me'];
		$b = ['key' => 'differentValue', 'another' => 'value'];
		$expected = ['key' => 'differentValue', 'another' => 'value', 'name' => 'me'];
		$result = Set::diff($b, $a);
		$this->assertEquals($expected, $result);

		$a = ['key' => 'value', 'another' => null, 'name' => 'me'];
		$b = [0 => 'differentValue', 1 => 'value'];
		$expected = $a + $b;
		$result = Set::diff($a, $b);
		$this->assertEquals($expected, $result);
	}

/**
 * testContains method
 *
 * @return void
 */
	public function testContains() {
		$a = [
			0 => ['name' => 'main'],
			1 => ['name' => 'about']
		];
		$b = [
			0 => ['name' => 'main'],
			1 => ['name' => 'about'],
			2 => ['name' => 'contact'],
			'a' => 'b'
		];

		$this->assertTrue(Set::contains($a, $a));
		$this->assertFalse(Set::contains($a, $b));
		$this->assertTrue(Set::contains($b, $a));
	}

/**
 * testCombine method
 *
 * @return void
 */
	public function testCombine() {
		$result = Set::combine([], '{n}.User.id', '{n}.User.Data');
		$this->assertTrue(empty($result));
		$result = Set::combine('', '{n}.User.id', '{n}.User.Data');
		$this->assertTrue(empty($result));

		$a = [
			['User' => ['id' => 2, 'group_id' => 1,
				'Data' => ['user' => 'mariano.iglesias', 'name' => 'Mariano Iglesias']]],
			['User' => ['id' => 14, 'group_id' => 2,
				'Data' => ['user' => 'phpnut', 'name' => 'Larry E. Masters']]],
			['User' => ['id' => 25, 'group_id' => 1,
				'Data' => ['user' => 'gwoo', 'name' => 'The Gwoo']]]];
		$result = Set::combine($a, '{n}.User.id');
		$expected = [2 => null, 14 => null, 25 => null];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, '{n}.User.id', '{n}.User.non-existant');
		$expected = [2 => null, 14 => null, 25 => null];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, '{n}.User.id', '{n}.User.Data');
		$expected = [
			2 => ['user' => 'mariano.iglesias', 'name' => 'Mariano Iglesias'],
			14 => ['user' => 'phpnut', 'name' => 'Larry E. Masters'],
			25 => ['user' => 'gwoo', 'name' => 'The Gwoo']];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, '{n}.User.id', '{n}.User.Data.name');
		$expected = [
			2 => 'Mariano Iglesias',
			14 => 'Larry E. Masters',
			25 => 'The Gwoo'];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, '{n}.User.id', '{n}.User.Data', '{n}.User.group_id');
		$expected = [
			1 => [
				2 => ['user' => 'mariano.iglesias', 'name' => 'Mariano Iglesias'],
				25 => ['user' => 'gwoo', 'name' => 'The Gwoo']],
			2 => [
				14 => ['user' => 'phpnut', 'name' => 'Larry E. Masters']]];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, '{n}.User.id', '{n}.User.Data.name', '{n}.User.group_id');
		$expected = [
			1 => [
				2 => 'Mariano Iglesias',
				25 => 'The Gwoo'],
			2 => [
				14 => 'Larry E. Masters']];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, '{n}.User.id');
		$expected = [2 => null, 14 => null, 25 => null];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, '{n}.User.id', '{n}.User.Data');
		$expected = [
			2 => ['user' => 'mariano.iglesias', 'name' => 'Mariano Iglesias'],
			14 => ['user' => 'phpnut', 'name' => 'Larry E. Masters'],
			25 => ['user' => 'gwoo', 'name' => 'The Gwoo']];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, '{n}.User.id', '{n}.User.Data.name');
		$expected = [2 => 'Mariano Iglesias', 14 => 'Larry E. Masters', 25 => 'The Gwoo'];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, '{n}.User.id', '{n}.User.Data', '{n}.User.group_id');
		$expected = [
			1 => [
				2 => ['user' => 'mariano.iglesias', 'name' => 'Mariano Iglesias'],
				25 => ['user' => 'gwoo', 'name' => 'The Gwoo']],
			2 => [
				14 => ['user' => 'phpnut', 'name' => 'Larry E. Masters']]];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, '{n}.User.id', '{n}.User.Data.name', '{n}.User.group_id');
		$expected = [
			1 => [
				2 => 'Mariano Iglesias',
				25 => 'The Gwoo'],
			2 => [
				14 => 'Larry E. Masters']];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, '{n}.User.id', ['{0}: {1}', '{n}.User.Data.user', '{n}.User.Data.name'], '{n}.User.group_id');
		$expected = [
			1 => [
				2 => 'mariano.iglesias: Mariano Iglesias',
				25 => 'gwoo: The Gwoo'],
			2 => [14 => 'phpnut: Larry E. Masters']];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, ['{0}: {1}', '{n}.User.Data.user', '{n}.User.Data.name'], '{n}.User.id');
		$expected = ['mariano.iglesias: Mariano Iglesias' => 2, 'phpnut: Larry E. Masters' => 14, 'gwoo: The Gwoo' => 25];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, ['{1}: {0}', '{n}.User.Data.user', '{n}.User.Data.name'], '{n}.User.id');
		$expected = ['Mariano Iglesias: mariano.iglesias' => 2, 'Larry E. Masters: phpnut' => 14, 'The Gwoo: gwoo' => 25];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, ['%1$s: %2$d', '{n}.User.Data.user', '{n}.User.id'], '{n}.User.Data.name');
		$expected = ['mariano.iglesias: 2' => 'Mariano Iglesias', 'phpnut: 14' => 'Larry E. Masters', 'gwoo: 25' => 'The Gwoo'];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, ['%2$d: %1$s', '{n}.User.Data.user', '{n}.User.id'], '{n}.User.Data.name');
		$expected = ['2: mariano.iglesias' => 'Mariano Iglesias', '14: phpnut' => 'Larry E. Masters', '25: gwoo' => 'The Gwoo'];
		$this->assertEquals($expected, $result);

		$b = new stdClass();
		$b->users = [
			['User' => ['id' => 2, 'group_id' => 1,
				'Data' => ['user' => 'mariano.iglesias', 'name' => 'Mariano Iglesias']]],
			['User' => ['id' => 14, 'group_id' => 2,
				'Data' => ['user' => 'phpnut', 'name' => 'Larry E. Masters']]],
			['User' => ['id' => 25, 'group_id' => 1,
				'Data' => ['user' => 'gwoo', 'name' => 'The Gwoo']]]];
		$result = Set::combine($b, 'users.{n}.User.id');
		$expected = [2 => null, 14 => null, 25 => null];
		$this->assertEquals($expected, $result);

		$result = Set::combine($b, 'users.{n}.User.id', 'users.{n}.User.non-existant');
		$expected = [2 => null, 14 => null, 25 => null];
		$this->assertEquals($expected, $result);

		$result = Set::combine($a, 'fail', 'fail');
		$this->assertSame([], $result);
	}

/**
 * testMapReverse method
 *
 * @return void
 */
	public function testMapReverse() {
		$result = Set::reverse(null);
		$this->assertEquals(null, $result);

		$result = Set::reverse(false);
		$this->assertEquals(false, $result);

		$expected = [
		'Array1' => [
				'Array1Data1' => 'Array1Data1 value 1', 'Array1Data2' => 'Array1Data2 value 2'],
		'Array2' => [
				0 => ['Array2Data1' => 1, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				1 => ['Array2Data1' => 2, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				2 => ['Array2Data1' => 3, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				3 => ['Array2Data1' => 4, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				4 => ['Array2Data1' => 5, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4']],
		'Array3' => [
				0 => ['Array3Data1' => 1, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				1 => ['Array3Data1' => 2, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				2 => ['Array3Data1' => 3, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				3 => ['Array3Data1' => 4, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				4 => ['Array3Data1' => 5, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4']]];
		$map = Set::map($expected, true);
		$this->assertEquals($expected['Array1']['Array1Data1'], $map->Array1->Array1Data1);
		$this->assertEquals($expected['Array2'][0]['Array2Data1'], $map->Array2[0]->Array2Data1);

		$result = Set::reverse($map);
		$this->assertEquals($expected, $result);

		$expected = [
			'Post' => ['id' => 1, 'title' => 'First Post'],
			'Comment' => [
				['id' => 1, 'title' => 'First Comment'],
				['id' => 2, 'title' => 'Second Comment']
			],
			'Tag' => [
				['id' => 1, 'title' => 'First Tag'],
				['id' => 2, 'title' => 'Second Tag']
			],
		];
		$map = Set::map($expected);
		$this->assertEquals($expected['Post']['title'], $map->title);
		foreach ($map->Comment as $comment) {
			$ids[] = $comment->id;
		}
		$this->assertEquals([1, 2], $ids);

		$expected = [
		'Array1' => [
				'Array1Data1' => 'Array1Data1 value 1', 'Array1Data2' => 'Array1Data2 value 2', 'Array1Data3' => 'Array1Data3 value 3', 'Array1Data4' => 'Array1Data4 value 4',
				'Array1Data5' => 'Array1Data5 value 5', 'Array1Data6' => 'Array1Data6 value 6', 'Array1Data7' => 'Array1Data7 value 7', 'Array1Data8' => 'Array1Data8 value 8'],
		'string' => 1,
		'another' => 'string',
		'some' => 'thing else',
		'Array2' => [
				0 => ['Array2Data1' => 1, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				1 => ['Array2Data1' => 2, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				2 => ['Array2Data1' => 3, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				3 => ['Array2Data1' => 4, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				4 => ['Array2Data1' => 5, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4']],
		'Array3' => [
				0 => ['Array3Data1' => 1, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				1 => ['Array3Data1' => 2, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				2 => ['Array3Data1' => 3, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				3 => ['Array3Data1' => 4, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				4 => ['Array3Data1' => 5, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4']]];
		$map = Set::map($expected, true);
		$result = Set::reverse($map);
		$this->assertEquals($expected, $result);

		$expected = [
		'Array1' => [
				'Array1Data1' => 'Array1Data1 value 1', 'Array1Data2' => 'Array1Data2 value 2', 'Array1Data3' => 'Array1Data3 value 3', 'Array1Data4' => 'Array1Data4 value 4',
				'Array1Data5' => 'Array1Data5 value 5', 'Array1Data6' => 'Array1Data6 value 6', 'Array1Data7' => 'Array1Data7 value 7', 'Array1Data8' => 'Array1Data8 value 8'],
		'string' => 1,
		'another' => 'string',
		'some' => 'thing else',
		'Array2' => [
				0 => ['Array2Data1' => 1, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				1 => ['Array2Data1' => 2, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				2 => ['Array2Data1' => 3, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				3 => ['Array2Data1' => 4, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4'],
				4 => ['Array2Data1' => 5, 'Array2Data2' => 'Array2Data2 value 2', 'Array2Data3' => 'Array2Data3 value 2', 'Array2Data4' => 'Array2Data4 value 4']],
		'string2' => 1,
		'another2' => 'string',
		'some2' => 'thing else',
		'Array3' => [
				0 => ['Array3Data1' => 1, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				1 => ['Array3Data1' => 2, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				2 => ['Array3Data1' => 3, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				3 => ['Array3Data1' => 4, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4'],
				4 => ['Array3Data1' => 5, 'Array3Data2' => 'Array3Data2 value 2', 'Array3Data3' => 'Array3Data3 value 2', 'Array3Data4' => 'Array3Data4 value 4']],
		'string3' => 1,
		'another3' => 'string',
		'some3' => 'thing else'];
		$map = Set::map($expected, true);
		$result = Set::reverse($map);
		$this->assertEquals($expected, $result);

		$expected = ['User' => ['psword' => 'whatever', 'Icon' => ['id' => 851]]];
		$map = Set::map($expected);
		$result = Set::reverse($map);
		$this->assertEquals($expected, $result);

		$expected = ['User' => ['psword' => 'whatever', 'Icon' => ['id' => 851]]];
		$class = new stdClass;
		$class->User = new stdClass;
		$class->User->psword = 'whatever';
		$class->User->Icon = new stdClass;
		$class->User->Icon->id = 851;
		$result = Set::reverse($class);
		$this->assertEquals($expected, $result);

		$expected = ['User' => ['psword' => 'whatever', 'Icon' => ['id' => 851], 'Profile' => ['name' => 'Some Name', 'address' => 'Some Address']]];
		$class = new stdClass;
		$class->User = new stdClass;
		$class->User->psword = 'whatever';
		$class->User->Icon = new stdClass;
		$class->User->Icon->id = 851;
		$class->User->Profile = new stdClass;
		$class->User->Profile->name = 'Some Name';
		$class->User->Profile->address = 'Some Address';

		$result = Set::reverse($class);
		$this->assertEquals($expected, $result);

		$expected = ['User' => ['psword' => 'whatever',
						'Icon' => ['id' => 851],
						'Profile' => ['name' => 'Some Name', 'address' => 'Some Address'],
						'Comment' => [
								['id' => 1, 'article_id' => 1, 'user_id' => 1, 'comment' => 'First Comment for First Article', 'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31'],
								['id' => 2, 'article_id' => 1, 'user_id' => 2, 'comment' => 'Second Comment for First Article', 'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31']]]];

		$class = new stdClass;
		$class->User = new stdClass;
		$class->User->psword = 'whatever';
		$class->User->Icon = new stdClass;
		$class->User->Icon->id = 851;
		$class->User->Profile = new stdClass;
		$class->User->Profile->name = 'Some Name';
		$class->User->Profile->address = 'Some Address';
		$class->User->Comment = new stdClass;
		$class->User->Comment->{'0'} = new stdClass;
		$class->User->Comment->{'0'}->id = 1;
		$class->User->Comment->{'0'}->article_id = 1;
		$class->User->Comment->{'0'}->user_id = 1;
		$class->User->Comment->{'0'}->comment = 'First Comment for First Article';
		$class->User->Comment->{'0'}->published = 'Y';
		$class->User->Comment->{'0'}->created = '2007-03-18 10:47:23';
		$class->User->Comment->{'0'}->updated = '2007-03-18 10:49:31';
		$class->User->Comment->{'1'} = new stdClass;
		$class->User->Comment->{'1'}->id = 2;
		$class->User->Comment->{'1'}->article_id = 1;
		$class->User->Comment->{'1'}->user_id = 2;
		$class->User->Comment->{'1'}->comment = 'Second Comment for First Article';
		$class->User->Comment->{'1'}->published = 'Y';
		$class->User->Comment->{'1'}->created = '2007-03-18 10:47:23';
		$class->User->Comment->{'1'}->updated = '2007-03-18 10:49:31';

		$result = Set::reverse($class);
		$this->assertEquals($expected, $result);

		$expected = ['User' => ['psword' => 'whatever',
						'Icon' => ['id' => 851],
						'Profile' => ['name' => 'Some Name', 'address' => 'Some Address'],
						'Comment' => [
								['id' => 1, 'article_id' => 1, 'user_id' => 1, 'comment' => 'First Comment for First Article', 'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31'],
								['id' => 2, 'article_id' => 1, 'user_id' => 2, 'comment' => 'Second Comment for First Article', 'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31']]]];

		// @codingStandardsIgnoreStart
		$class = new stdClass;
		$class->User = new stdClass;
		$class->User->psword = 'whatever';
		$class->User->Icon = new stdClass;
		$class->User->Icon->id = 851;
		$class->User->Profile = new stdClass;
		$class->User->Profile->name = 'Some Name';
		$class->User->Profile->address = 'Some Address';
		$class->User->Comment = [];
		$comment = new stdClass;
		$comment->id = 1;
		$comment->article_id = 1;
		$comment->user_id = 1;
		$comment->comment = 'First Comment for First Article';
		$comment->published = 'Y';
		$comment->created = '2007-03-18 10:47:23';
		$comment->updated = '2007-03-18 10:49:31';
		$comment2 = new stdClass;
		$comment2->id = 2;
		$comment2->article_id = 1;
		$comment2->user_id = 2;
		$comment2->comment = 'Second Comment for First Article';
		$comment2->published = 'Y';
		$comment2->created = '2007-03-18 10:47:23';
		$comment2->updated = '2007-03-18 10:49:31';
		// @codingStandardsIgnoreEnd
		$class->User->Comment = [$comment, $comment2];
		$result = Set::reverse($class);
		$this->assertEquals($expected, $result);

		$class = new stdClass;
		$class->User = new stdClass;
		$class->User->id = 100;
		$class->someString = 'this is some string';
		$class->Profile = new stdClass;
		$class->Profile->name = 'Joe Mamma';

		$result = Set::reverse($class);
		$expected = [
			'User' => ['id' => '100'],
			'someString' => 'this is some string',
			'Profile' => ['name' => 'Joe Mamma']
		];
		$this->assertEquals($expected, $result);

		// @codingStandardsIgnoreStart
		$class = new stdClass;
		$class->User = new stdClass;
		$class->User->id = 100;
		$class->User->_name_ = 'User';
		$class->Profile = new stdClass;
		$class->Profile->name = 'Joe Mamma';
		$class->Profile->_name_ = 'Profile';
		// @codingStandardsIgnoreEnd

		$result = Set::reverse($class);
		$expected = ['User' => ['id' => '100'], 'Profile' => ['name' => 'Joe Mamma']];
		$this->assertEquals($expected, $result);
	}

/**
 * testFormatting method
 *
 * @return void
 */
	public function testFormatting() {
		$data = [
			['Person' => ['first_name' => 'Nate', 'last_name' => 'Abele', 'city' => 'Boston', 'state' => 'MA', 'something' => '42']],
			['Person' => ['first_name' => 'Larry', 'last_name' => 'Masters', 'city' => 'Boondock', 'state' => 'TN', 'something' => '{0}']],
			['Person' => ['first_name' => 'Garrett', 'last_name' => 'Woodworth', 'city' => 'Venice Beach', 'state' => 'CA', 'something' => '{1}']]];

		$result = Set::format($data, '{1}, {0}', ['{n}.Person.first_name', '{n}.Person.last_name']);
		$expected = ['Abele, Nate', 'Masters, Larry', 'Woodworth, Garrett'];
		$this->assertEquals($expected, $result);

		$result = Set::format($data, '{0}, {1}', ['{n}.Person.last_name', '{n}.Person.first_name']);
		$this->assertEquals($expected, $result);

		$result = Set::format($data, '{0}, {1}', ['{n}.Person.city', '{n}.Person.state']);
		$expected = ['Boston, MA', 'Boondock, TN', 'Venice Beach, CA'];
		$this->assertEquals($expected, $result);

		$result = Set::format($data, '{{0}, {1}}', ['{n}.Person.city', '{n}.Person.state']);
		$expected = ['{Boston, MA}', '{Boondock, TN}', '{Venice Beach, CA}'];
		$this->assertEquals($expected, $result);

		$result = Set::format($data, '{{0}, {1}}', ['{n}.Person.something', '{n}.Person.something']);
		$expected = ['{42, 42}', '{{0}, {0}}', '{{1}, {1}}'];
		$this->assertEquals($expected, $result);

		$result = Set::format($data, '{%2$d, %1$s}', ['{n}.Person.something', '{n}.Person.something']);
		$expected = ['{42, 42}', '{0, {0}}', '{0, {1}}'];
		$this->assertEquals($expected, $result);

		$result = Set::format($data, '{%1$s, %1$s}', ['{n}.Person.something', '{n}.Person.something']);
		$expected = ['{42, 42}', '{{0}, {0}}', '{{1}, {1}}'];
		$this->assertEquals($expected, $result);

		$result = Set::format($data, '%2$d, %1$s', ['{n}.Person.first_name', '{n}.Person.something']);
		$expected = ['42, Nate', '0, Larry', '0, Garrett'];
		$this->assertEquals($expected, $result);

		$result = Set::format($data, '%1$s, %2$d', ['{n}.Person.first_name', '{n}.Person.something']);
		$expected = ['Nate, 42', 'Larry, 0', 'Garrett, 0'];
		$this->assertEquals($expected, $result);
	}

/**
 * testFormattingNullValues method
 *
 * @return void
 */
	public function testFormattingNullValues() {
		$data = [
			['Person' => ['first_name' => 'Nate', 'last_name' => 'Abele', 'city' => 'Boston', 'state' => 'MA', 'something' => '42']],
			['Person' => ['first_name' => 'Larry', 'last_name' => 'Masters', 'city' => 'Boondock', 'state' => 'TN', 'something' => null]],
			['Person' => ['first_name' => 'Garrett', 'last_name' => 'Woodworth', 'city' => 'Venice Beach', 'state' => 'CA', 'something' => null]]];

		$result = Set::format($data, '%s', ['{n}.Person.something']);
		$expected = ['42', '', ''];
		$this->assertEquals($expected, $result);

		$result = Set::format($data, '{0}, {1}', ['{n}.Person.city', '{n}.Person.something']);
		$expected = ['Boston, 42', 'Boondock, ', 'Venice Beach, '];
		$this->assertEquals($expected, $result);
	}

/**
 * testCountDim method
 *
 * @return void
 */
	public function testCountDim() {
		$data = ['one', '2', 'three'];
		$result = Set::countDim($data);
		$this->assertEquals(1, $result);

		$data = ['1' => '1.1', '2', '3'];
		$result = Set::countDim($data);
		$this->assertEquals(1, $result);

		$data = ['1' => ['1.1' => '1.1.1'], '2', '3' => ['3.1' => '3.1.1']];
		$result = Set::countDim($data);
		$this->assertEquals(2, $result);

		$data = ['1' => '1.1', '2', '3' => ['3.1' => '3.1.1']];
		$result = Set::countDim($data);
		$this->assertEquals(1, $result);

		$data = ['1' => '1.1', '2', '3' => ['3.1' => '3.1.1']];
		$result = Set::countDim($data, true);
		$this->assertEquals(2, $result);

		$data = ['1' => ['1.1' => '1.1.1'], '2', '3' => ['3.1' => ['3.1.1' => '3.1.1.1']]];
		$result = Set::countDim($data);
		$this->assertEquals(2, $result);

		$data = ['1' => ['1.1' => '1.1.1'], '2', '3' => ['3.1' => ['3.1.1' => '3.1.1.1']]];
		$result = Set::countDim($data, true);
		$this->assertEquals(3, $result);

		$data = ['1' => ['1.1' => '1.1.1'], ['2' => ['2.1' => ['2.1.1' => '2.1.1.1']]], '3' => ['3.1' => ['3.1.1' => '3.1.1.1']]];
		$result = Set::countDim($data, true);
		$this->assertEquals(4, $result);

		$data = ['1' => ['1.1' => '1.1.1'], ['2' => ['2.1' => ['2.1.1' => ['2.1.1.1']]]], '3' => ['3.1' => ['3.1.1' => '3.1.1.1']]];
		$result = Set::countDim($data, true);
		$this->assertEquals(5, $result);

		$data = ['1' => ['1.1' => '1.1.1'], ['2' => ['2.1' => ['2.1.1' => ['2.1.1.1' => '2.1.1.1.1']]]], '3' => ['3.1' => ['3.1.1' => '3.1.1.1']]];
		$result = Set::countDim($data, true);
		$this->assertEquals(5, $result);

		$set = ['1' => ['1.1' => '1.1.1'], ['2' => ['2.1' => ['2.1.1' => ['2.1.1.1' => '2.1.1.1.1']]]], '3' => ['3.1' => ['3.1.1' => '3.1.1.1']]];
		$result = Set::countDim($set, false, 0);
		$this->assertEquals(2, $result);

		$result = Set::countDim($set, true);
		$this->assertEquals(5, $result);
	}

/**
 * testMapNesting method
 *
 * @return void
 */
	public function testMapNesting() {
		$expected = [
			[
				"IndexedPage" => [
					"id" => 1,
					"url" => 'http://blah.com/',
					'hash' => '68a9f053b19526d08e36c6a9ad150737933816a5',
					'headers' => [
							'Date' => "Wed, 14 Nov 2007 15:51:42 GMT",
							'Server' => "Apache",
							'Expires' => "Thu, 19 Nov 1981 08:52:00 GMT",
							'Cache-Control' => "private",
							'Pragma' => "no-cache",
							'Content-Type' => "text/html; charset=UTF-8",
							'X-Original-Transfer-Encoding' => "chunked",
							'Content-Length' => "50210",
					],
					'meta' => [
							'keywords' => ['testing', 'tests'],
							'description' => 'describe me',
					],
					'get_vars' => '',
					'post_vars' => [],
					'cookies' => ['PHPSESSID' => "dde9896ad24595998161ffaf9e0dbe2d"],
					'redirect' => '',
					'created' => "1195055503",
					'updated' => "1195055503",
				]
			],
			[
				"IndexedPage" => [
					"id" => 2,
					"url" => 'http://blah.com/',
					'hash' => '68a9f053b19526d08e36c6a9ad150737933816a5',
					'headers' => [
						'Date' => "Wed, 14 Nov 2007 15:51:42 GMT",
						'Server' => "Apache",
						'Expires' => "Thu, 19 Nov 1981 08:52:00 GMT",
						'Cache-Control' => "private",
						'Pragma' => "no-cache",
						'Content-Type' => "text/html; charset=UTF-8",
						'X-Original-Transfer-Encoding' => "chunked",
						'Content-Length' => "50210",
					],
					'meta' => [
							'keywords' => ['testing', 'tests'],
							'description' => 'describe me',
					],
					'get_vars' => '',
					'post_vars' => [],
					'cookies' => ['PHPSESSID' => "dde9896ad24595998161ffaf9e0dbe2d"],
					'redirect' => '',
					'created' => "1195055503",
					'updated' => "1195055503",
				],
			]
		];

		$mapped = Set::map($expected);
		$ids = [];

		foreach ($mapped as $object) {
			$ids[] = $object->id;
		}
		$this->assertEquals([1, 2], $ids);
		$this->assertEquals($expected[0]['IndexedPage']['headers'], get_object_vars($mapped[0]->headers));

		$result = Set::reverse($mapped);
		$this->assertEquals($expected, $result);

		$data = [
			[
				"IndexedPage" => [
					"id" => 1,
					"url" => 'http://blah.com/',
					'hash' => '68a9f053b19526d08e36c6a9ad150737933816a5',
					'get_vars' => '',
					'redirect' => '',
					'created' => "1195055503",
					'updated' => "1195055503",
				]
			],
			[
				"IndexedPage" => [
					"id" => 2,
					"url" => 'http://blah.com/',
					'hash' => '68a9f053b19526d08e36c6a9ad150737933816a5',
					'get_vars' => '',
					'redirect' => '',
					'created' => "1195055503",
					'updated' => "1195055503",
				],
			]
		];
		$mapped = Set::map($data);

		// @codingStandardsIgnoreStart
		$expected = new stdClass();
		$expected->_name_ = 'IndexedPage';
		$expected->id = 2;
		$expected->url = 'http://blah.com/';
		$expected->hash = '68a9f053b19526d08e36c6a9ad150737933816a5';
		$expected->get_vars = '';
		$expected->redirect = '';
		$expected->created = '1195055503';
		$expected->updated = '1195055503';
		// @codingStandardsIgnoreEnd
		$this->assertEquals($expected, $mapped[1]);

		$ids = [];

		foreach ($mapped as $object) {
			$ids[] = $object->id;
		}
		$this->assertEquals([1, 2], $ids);

		$result = Set::map(null);
		$expected = null;
		$this->assertEquals($expected, $result);
	}

/**
 * testNestedMappedData method
 *
 * @return void
 */
	public function testNestedMappedData() {
		$result = Set::map([
				[
					'Post' => ['id' => '1', 'author_id' => '1', 'title' => 'First Post', 'body' => 'First Post Body', 'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'],
					'Author' => ['id' => '1', 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31', 'test' => 'working'],
				],
				[
					'Post' => ['id' => '2', 'author_id' => '3', 'title' => 'Second Post', 'body' => 'Second Post Body', 'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'],
					'Author' => ['id' => '3', 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31', 'test' => 'working'],
				]
			]);

		// @codingStandardsIgnoreStart
		$expected = new stdClass;
		$expected->_name_ = 'Post';
		$expected->id = '1';
		$expected->author_id = '1';
		$expected->title = 'First Post';
		$expected->body = 'First Post Body';
		$expected->published = 'Y';
		$expected->created = "2007-03-18 10:39:23";
		$expected->updated = "2007-03-18 10:41:31";

		$expected->Author = new stdClass;
		$expected->Author->id = '1';
		$expected->Author->user = 'mariano';
		$expected->Author->password = '5f4dcc3b5aa765d61d8327deb882cf99';
		$expected->Author->created = '2007-03-17 01:16:23';
		$expected->Author->updated = '2007-03-17 01:18:31';
		$expected->Author->test = 'working';
		$expected->Author->_name_ = 'Author';

		$expected2 = new stdClass;
		$expected2->_name_ = 'Post';
		$expected2->id = '2';
		$expected2->author_id = '3';
		$expected2->title = 'Second Post';
		$expected2->body = 'Second Post Body';
		$expected2->published = 'Y';
		$expected2->created = "2007-03-18 10:41:23";
		$expected2->updated = "2007-03-18 10:43:31";

		$expected2->Author = new stdClass;
		$expected2->Author->id = '3';
		$expected2->Author->user = 'larry';
		$expected2->Author->password = '5f4dcc3b5aa765d61d8327deb882cf99';
		$expected2->Author->created = '2007-03-17 01:20:23';
		$expected2->Author->updated = '2007-03-17 01:22:31';
		$expected2->Author->test = 'working';
		$expected2->Author->_name_ = 'Author';
		// @codingStandardsIgnoreEnd

		$test = [];
		$test[0] = $expected;
		$test[1] = $expected2;

		$this->assertEquals($test, $result);

		$result = Set::map(
			[
				'Post' => ['id' => '1', 'author_id' => '1', 'title' => 'First Post', 'body' => 'First Post Body', 'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'],
				'Author' => ['id' => '1', 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99', 'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31', 'test' => 'working'],
			]
		);
		// @codingStandardsIgnoreStart
		$expected = new stdClass;
		$expected->_name_ = 'Post';
		$expected->id = '1';
		$expected->author_id = '1';
		$expected->title = 'First Post';
		$expected->body = 'First Post Body';
		$expected->published = 'Y';
		$expected->created = "2007-03-18 10:39:23";
		$expected->updated = "2007-03-18 10:41:31";

		$expected->Author = new stdClass;
		$expected->Author->id = '1';
		$expected->Author->user = 'mariano';
		$expected->Author->password = '5f4dcc3b5aa765d61d8327deb882cf99';
		$expected->Author->created = "2007-03-17 01:16:23";
		$expected->Author->updated = "2007-03-17 01:18:31";
		$expected->Author->test = 'working';
		$expected->Author->_name_ = 'Author';
		// @codingStandardsIgnoreEnd
		$this->assertEquals($expected, $result);

		//Case where extra HABTM fields come back in a result
		$data = [
			'User' => [
				'id' => 1,
				'email' => 'user@example.com',
				'first_name' => 'John',
				'last_name' => 'Smith',
			],
			'Piece' => [
				[
					'id' => 1,
					'title' => 'Moonlight Sonata',
					'composer' => 'Ludwig van Beethoven',
					'PiecesUser' => [
						'id' => 1,
						'created' => '2008-01-01 00:00:00',
						'modified' => '2008-01-01 00:00:00',
						'piece_id' => 1,
						'user_id' => 2,
					]
				],
				[
					'id' => 2,
					'title' => 'Moonlight Sonata 2',
					'composer' => 'Ludwig van Beethoven',
					'PiecesUser' => [
						'id' => 2,
						'created' => '2008-01-01 00:00:00',
						'modified' => '2008-01-01 00:00:00',
						'piece_id' => 2,
						'user_id' => 2,
					]
				]
			]
		];

		$result = Set::map($data);

		// @codingStandardsIgnoreStart
		$expected = new stdClass();
		$expected->_name_ = 'User';
		$expected->id = 1;
		$expected->email = 'user@example.com';
		$expected->first_name = 'John';
		$expected->last_name = 'Smith';

		$piece = new stdClass();
		$piece->id = 1;
		$piece->title = 'Moonlight Sonata';
		$piece->composer = 'Ludwig van Beethoven';

		$piece->PiecesUser = new stdClass();
		$piece->PiecesUser->id = 1;
		$piece->PiecesUser->created = '2008-01-01 00:00:00';
		$piece->PiecesUser->modified = '2008-01-01 00:00:00';
		$piece->PiecesUser->piece_id = 1;
		$piece->PiecesUser->user_id = 2;
		$piece->PiecesUser->_name_ = 'PiecesUser';

		$piece->_name_ = 'Piece';

		$piece2 = new stdClass();
		$piece2->id = 2;
		$piece2->title = 'Moonlight Sonata 2';
		$piece2->composer = 'Ludwig van Beethoven';

		$piece2->PiecesUser = new stdClass();
		$piece2->PiecesUser->id = 2;
		$piece2->PiecesUser->created = '2008-01-01 00:00:00';
		$piece2->PiecesUser->modified = '2008-01-01 00:00:00';
		$piece2->PiecesUser->piece_id = 2;
		$piece2->PiecesUser->user_id = 2;
		$piece2->PiecesUser->_name_ = 'PiecesUser';

		$piece2->_name_ = 'Piece';
		// @codingStandardsIgnoreEnd

		$expected->Piece = [$piece, $piece2];

		$this->assertEquals($expected, $result);

		//Same data, but should work if _name_ has been manually defined:
		$data = [
			'User' => [
				'id' => 1,
				'email' => 'user@example.com',
				'first_name' => 'John',
				'last_name' => 'Smith',
				'_name_' => 'FooUser',
			],
			'Piece' => [
				[
					'id' => 1,
					'title' => 'Moonlight Sonata',
					'composer' => 'Ludwig van Beethoven',
					'_name_' => 'FooPiece',
					'PiecesUser' => [
						'id' => 1,
						'created' => '2008-01-01 00:00:00',
						'modified' => '2008-01-01 00:00:00',
						'piece_id' => 1,
						'user_id' => 2,
						'_name_' => 'FooPiecesUser',
					]
				],
				[
					'id' => 2,
					'title' => 'Moonlight Sonata 2',
					'composer' => 'Ludwig van Beethoven',
					'_name_' => 'FooPiece',
					'PiecesUser' => [
						'id' => 2,
						'created' => '2008-01-01 00:00:00',
						'modified' => '2008-01-01 00:00:00',
						'piece_id' => 2,
						'user_id' => 2,
						'_name_' => 'FooPiecesUser',
					]
				]
			]
		];

		$result = Set::map($data);

		// @codingStandardsIgnoreStart
		$expected = new stdClass();
		$expected->_name_ = 'FooUser';
		$expected->id = 1;
		$expected->email = 'user@example.com';
		$expected->first_name = 'John';
		$expected->last_name = 'Smith';

		$piece = new stdClass();
		$piece->id = 1;
		$piece->title = 'Moonlight Sonata';
		$piece->composer = 'Ludwig van Beethoven';
		$piece->_name_ = 'FooPiece';
		$piece->PiecesUser = new stdClass();
		$piece->PiecesUser->id = 1;
		$piece->PiecesUser->created = '2008-01-01 00:00:00';
		$piece->PiecesUser->modified = '2008-01-01 00:00:00';
		$piece->PiecesUser->piece_id = 1;
		$piece->PiecesUser->user_id = 2;
		$piece->PiecesUser->_name_ = 'FooPiecesUser';

		$piece2 = new stdClass();
		$piece2->id = 2;
		$piece2->title = 'Moonlight Sonata 2';
		$piece2->composer = 'Ludwig van Beethoven';
		$piece2->_name_ = 'FooPiece';
		$piece2->PiecesUser = new stdClass();
		$piece2->PiecesUser->id = 2;
		$piece2->PiecesUser->created = '2008-01-01 00:00:00';
		$piece2->PiecesUser->modified = '2008-01-01 00:00:00';
		$piece2->PiecesUser->piece_id = 2;
		$piece2->PiecesUser->user_id = 2;
		$piece2->PiecesUser->_name_ = 'FooPiecesUser';
		// @codingStandardsIgnoreEnd

		$expected->Piece = [$piece, $piece2];

		$this->assertEquals($expected, $result);
	}

/**
 * testPushDiff method
 *
 * @return void
 */
	public function testPushDiff() {
		$array1 = ['ModelOne' => ['id' => 1001, 'field_one' => 'a1.m1.f1', 'field_two' => 'a1.m1.f2']];
		$array2 = ['ModelTwo' => ['id' => 1002, 'field_one' => 'a2.m2.f1', 'field_two' => 'a2.m2.f2']];

		$result = Set::pushDiff($array1, $array2);

		$this->assertEquals($array1 + $array2, $result);

		$array3 = ['ModelOne' => ['id' => 1003, 'field_one' => 'a3.m1.f1', 'field_two' => 'a3.m1.f2', 'field_three' => 'a3.m1.f3']];
		$result = Set::pushDiff($array1, $array3);

		$expected = ['ModelOne' => ['id' => 1001, 'field_one' => 'a1.m1.f1', 'field_two' => 'a1.m1.f2', 'field_three' => 'a3.m1.f3']];
		$this->assertEquals($expected, $result);

		$array1 = [
				0 => ['ModelOne' => ['id' => 1001, 'field_one' => 's1.0.m1.f1', 'field_two' => 's1.0.m1.f2']],
				1 => ['ModelTwo' => ['id' => 1002, 'field_one' => 's1.1.m2.f2', 'field_two' => 's1.1.m2.f2']]];
		$array2 = [
				0 => ['ModelOne' => ['id' => 1001, 'field_one' => 's2.0.m1.f1', 'field_two' => 's2.0.m1.f2']],
				1 => ['ModelTwo' => ['id' => 1002, 'field_one' => 's2.1.m2.f2', 'field_two' => 's2.1.m2.f2']]];

		$result = Set::pushDiff($array1, $array2);
		$this->assertEquals($array1, $result);

		$array3 = [0 => ['ModelThree' => ['id' => 1003, 'field_one' => 's3.0.m3.f1', 'field_two' => 's3.0.m3.f2']]];

		$result = Set::pushDiff($array1, $array3);
		$expected = [
					0 => ['ModelOne' => ['id' => 1001, 'field_one' => 's1.0.m1.f1', 'field_two' => 's1.0.m1.f2'],
						'ModelThree' => ['id' => 1003, 'field_one' => 's3.0.m3.f1', 'field_two' => 's3.0.m3.f2']],
					1 => ['ModelTwo' => ['id' => 1002, 'field_one' => 's1.1.m2.f2', 'field_two' => 's1.1.m2.f2']]];
		$this->assertEquals($expected, $result);

		$result = Set::pushDiff($array1, null);
		$this->assertEquals($array1, $result);

		$result = Set::pushDiff($array1, $array2);
		$this->assertEquals($array1 + $array2, $result);
	}

/**
 * testSetApply method
 * @return void
 */
	public function testApply() {
		$data = [
			['Movie' => ['id' => 1, 'title' => 'movie 3', 'rating' => 5]],
			['Movie' => ['id' => 1, 'title' => 'movie 1', 'rating' => 1]],
			['Movie' => ['id' => 1, 'title' => 'movie 2', 'rating' => 3]]
		];

		$result = Set::apply('/Movie/rating', $data, 'array_sum');
		$expected = 9;
		$this->assertEquals($expected, $result);

		$result = Set::apply('/Movie/rating', $data, 'array_product');
		$expected = 15;
		$this->assertEquals($expected, $result);

		$result = Set::apply('/Movie/title', $data, 'ucfirst', ['type' => 'map']);
		$expected = ['Movie 3', 'Movie 1', 'Movie 2'];
		$this->assertEquals($expected, $result);

		$result = Set::apply('/Movie/title', $data, 'strtoupper', ['type' => 'map']);
		$expected = ['MOVIE 3', 'MOVIE 1', 'MOVIE 2'];
		$this->assertEquals($expected, $result);

		$result = Set::apply('/Movie/rating', $data, ['SetTest', 'method'], ['type' => 'reduce']);
		$expected = 9;
		$this->assertEquals($expected, $result);

		$result = Set::apply('/Movie/rating', $data, 'strtoupper', ['type' => 'non existing type']);
		$expected = null;
		$this->assertEquals($expected, $result);
	}

/**
 * Helper method to test Set::apply()
 *
 * @return void
 */
	public static function method($val1, $val2) {
		$val1 += $val2;
		return $val1;
	}

/**
 * testXmlSetReverse method
 *
 * @return void
 */
	public function testXmlSetReverse() {
		App::uses('Xml', 'Utility');

		$string = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
		<rss version="2.0">
			<channel>
			<title>Cake PHP Google Group</title>
			<link>http://groups.google.com/group/cake-php</link>
			<description>Search this group before posting anything. There are over 20,000 posts and it&amp;#39;s very likely your question was answered before. Visit the IRC channel #cakephp at irc.freenode.net for live chat with users and developers of Cake. If you post, tell us the version of Cake, PHP, and database.</description>
			<language>en</language>
				<item>
				<title>constructng result array when using findall</title>
				<link>http://groups.google.com/group/cake-php/msg/49bc00f3bc651b4f</link>
				<description>i&#39;m using cakephp to construct a logical data model array that will be &lt;br&gt; passed to a flex app. I have the following model association: &lt;br&gt; ServiceDay-&amp;gt;(hasMany)ServiceTi me-&amp;gt;(hasMany)ServiceTimePrice. So what &lt;br&gt; the current output from my findall is something like this example: &lt;br&gt; &lt;p&gt;Array( &lt;br&gt; [0] =&amp;gt; Array(</description>
				<guid isPermaLink="true">http://groups.google.com/group/cake-php/msg/49bc00f3bc651b4f</guid>
				<author>bmil...@gmail.com(bpscrugs)</author>
				<pubDate>Fri, 28 Dec 2007 00:44:14 UT</pubDate>
				</item>
				<item>
				<title>Re: share views between actions?</title>
				<link>http://groups.google.com/group/cake-php/msg/8b350d898707dad8</link>
				<description>Then perhaps you might do us all a favour and refrain from replying to &lt;br&gt; things you do not understand. That goes especially for asinine comments. &lt;br&gt; Indeed. &lt;br&gt; To sum up: &lt;br&gt; No comment. &lt;br&gt; In my day, a simple &amp;quot;RTFM&amp;quot; would suffice. I&#39;ll keep in mind to ignore any &lt;br&gt; further responses from you. &lt;br&gt; You (and I) were referring to the *online documentation*, not other</description>
				<guid isPermaLink="true">http://groups.google.com/group/cake-php/msg/8b350d898707dad8</guid>
				<author>subtropolis.z...@gmail.com(subtropolis zijn)</author>
				<pubDate>Fri, 28 Dec 2007 00:45:01 UT</pubDate>
			 </item>
		</channel>
		</rss>';
		$xml = Xml::build($string);
		$result = Set::reverse($xml);
		$expected = ['rss' => [
			'@version' => '2.0',
			'channel' => [
				'title' => 'Cake PHP Google Group',
				'link' => 'http://groups.google.com/group/cake-php',
				'description' => 'Search this group before posting anything. There are over 20,000 posts and it&#39;s very likely your question was answered before. Visit the IRC channel #cakephp at irc.freenode.net for live chat with users and developers of Cake. If you post, tell us the version of Cake, PHP, and database.',
				'language' => 'en',
				'item' => [
					[
						'title' => 'constructng result array when using findall',
						'link' => 'http://groups.google.com/group/cake-php/msg/49bc00f3bc651b4f',
						'description' => "i'm using cakephp to construct a logical data model array that will be <br> passed to a flex app. I have the following model association: <br> ServiceDay-&gt;(hasMany)ServiceTi me-&gt;(hasMany)ServiceTimePrice. So what <br> the current output from my findall is something like this example: <br> <p>Array( <br> [0] =&gt; Array(",
						'guid' => ['@isPermaLink' => 'true', '@' => 'http://groups.google.com/group/cake-php/msg/49bc00f3bc651b4f'],
						'author' => 'bmil...@gmail.com(bpscrugs)',
						'pubDate' => 'Fri, 28 Dec 2007 00:44:14 UT',
					],
					[
						'title' => 'Re: share views between actions?',
						'link' => 'http://groups.google.com/group/cake-php/msg/8b350d898707dad8',
						'description' => 'Then perhaps you might do us all a favour and refrain from replying to <br> things you do not understand. That goes especially for asinine comments. <br> Indeed. <br> To sum up: <br> No comment. <br> In my day, a simple &quot;RTFM&quot; would suffice. I\'ll keep in mind to ignore any <br> further responses from you. <br> You (and I) were referring to the *online documentation*, not other',
						'guid' => ['@isPermaLink' => 'true', '@' => 'http://groups.google.com/group/cake-php/msg/8b350d898707dad8'],
						'author' => 'subtropolis.z...@gmail.com(subtropolis zijn)',
						'pubDate' => 'Fri, 28 Dec 2007 00:45:01 UT'
					]
				]
			]
		]];
		$this->assertEquals($expected, $result);
		$string = '<data><post title="Title of this post" description="cool"/></data>';

		$xml = Xml::build($string);
		$result = Set::reverse($xml);
		$expected = ['data' => ['post' => ['@title' => 'Title of this post', '@description' => 'cool']]];
		$this->assertEquals($expected, $result);

		$xml = Xml::build('<example><item><title>An example of a correctly reversed SimpleXMLElement</title><desc/></item></example>');
		$result = Set::reverse($xml);
		$expected = ['example' =>
			[
				'item' => [
					'title' => 'An example of a correctly reversed SimpleXMLElement',
					'desc' => '',
				]
			]
		];
		$this->assertEquals($expected, $result);

		$xml = Xml::build('<example><item attr="123"><titles><title>title1</title><title>title2</title></titles></item></example>');
		$result = Set::reverse($xml);
		$expected =
			['example' => [
				'item' => [
					'@attr' => '123',
					'titles' => [
						'title' => ['title1', 'title2']
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$xml = Xml::build('<example attr="ex_attr"><item attr="123"><titles>list</titles>textforitems</item></example>');
		$result = Set::reverse($xml);
		$expected =
			['example' => [
				'@attr' => 'ex_attr',
				'item' => [
					'@attr' => '123',
					'titles' => 'list',
					'@' => 'textforitems'
				]
			]
		];
		$this->assertEquals($expected, $result);

		$string = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
		<rss version="2.0" xmlns:dc="http://www.cakephp.org/">
			<channel>
			<title>Cake PHP Google Group</title>
			<link>http://groups.google.com/group/cake-php</link>
			<description>Search this group before posting anything. There are over 20,000 posts and it&amp;#39;s very likely your question was answered before. Visit the IRC channel #cakephp at irc.freenode.net for live chat with users and developers of Cake. If you post, tell us the version of Cake, PHP, and database.</description>
			<language>en</language>
				<item>
				<title>constructng result array when using findall</title>
				<link>http://groups.google.com/group/cake-php/msg/49bc00f3bc651b4f</link>
				<description>i&#39;m using cakephp to construct a logical data model array that will be &lt;br&gt; passed to a flex app. I have the following model association: &lt;br&gt; ServiceDay-&amp;gt;(hasMany)ServiceTi me-&amp;gt;(hasMany)ServiceTimePrice. So what &lt;br&gt; the current output from my findall is something like this example: &lt;br&gt; &lt;p&gt;Array( &lt;br&gt; [0] =&amp;gt; Array(</description>
					<dc:creator>cakephp</dc:creator>
				<category><![CDATA[cakephp]]></category>
				<category><![CDATA[model]]></category>
				<guid isPermaLink="true">http://groups.google.com/group/cake-php/msg/49bc00f3bc651b4f</guid>
				<author>bmil...@gmail.com(bpscrugs)</author>
				<pubDate>Fri, 28 Dec 2007 00:44:14 UT</pubDate>
				</item>
				<item>
				<title>Re: share views between actions?</title>
				<link>http://groups.google.com/group/cake-php/msg/8b350d898707dad8</link>
				<description>Then perhaps you might do us all a favour and refrain from replying to &lt;br&gt; things you do not understand. That goes especially for asinine comments. &lt;br&gt; Indeed. &lt;br&gt; To sum up: &lt;br&gt; No comment. &lt;br&gt; In my day, a simple &amp;quot;RTFM&amp;quot; would suffice. I&#39;ll keep in mind to ignore any &lt;br&gt; further responses from you. &lt;br&gt; You (and I) were referring to the *online documentation*, not other</description>
					<dc:creator>cakephp</dc:creator>
				<category><![CDATA[cakephp]]></category>
				<category><![CDATA[model]]></category>
				<guid isPermaLink="true">http://groups.google.com/group/cake-php/msg/8b350d898707dad8</guid>
				<author>subtropolis.z...@gmail.com(subtropolis zijn)</author>
				<pubDate>Fri, 28 Dec 2007 00:45:01 UT</pubDate>
			 </item>
		</channel>
		</rss>';

		$xml = Xml::build($string);
		$result = Set::reverse($xml);

		$expected = ['rss' => [
			'@version' => '2.0',
			'channel' => [
				'title' => 'Cake PHP Google Group',
				'link' => 'http://groups.google.com/group/cake-php',
				'description' => 'Search this group before posting anything. There are over 20,000 posts and it&#39;s very likely your question was answered before. Visit the IRC channel #cakephp at irc.freenode.net for live chat with users and developers of Cake. If you post, tell us the version of Cake, PHP, and database.',
				'language' => 'en',
				'item' => [
					[
						'title' => 'constructng result array when using findall',
						'link' => 'http://groups.google.com/group/cake-php/msg/49bc00f3bc651b4f',
						'description' => "i'm using cakephp to construct a logical data model array that will be <br> passed to a flex app. I have the following model association: <br> ServiceDay-&gt;(hasMany)ServiceTi me-&gt;(hasMany)ServiceTimePrice. So what <br> the current output from my findall is something like this example: <br> <p>Array( <br> [0] =&gt; Array(",
						'dc:creator' => 'cakephp',
						'category' => ['cakephp', 'model'],
						'guid' => ['@isPermaLink' => 'true', '@' => 'http://groups.google.com/group/cake-php/msg/49bc00f3bc651b4f'],
						'author' => 'bmil...@gmail.com(bpscrugs)',
						'pubDate' => 'Fri, 28 Dec 2007 00:44:14 UT',
					],
					[
						'title' => 'Re: share views between actions?',
						'link' => 'http://groups.google.com/group/cake-php/msg/8b350d898707dad8',
						'description' => 'Then perhaps you might do us all a favour and refrain from replying to <br> things you do not understand. That goes especially for asinine comments. <br> Indeed. <br> To sum up: <br> No comment. <br> In my day, a simple &quot;RTFM&quot; would suffice. I\'ll keep in mind to ignore any <br> further responses from you. <br> You (and I) were referring to the *online documentation*, not other',
						'dc:creator' => 'cakephp',
						'category' => ['cakephp', 'model'],
						'guid' => ['@isPermaLink' => 'true', '@' => 'http://groups.google.com/group/cake-php/msg/8b350d898707dad8'],
						'author' => 'subtropolis.z...@gmail.com(subtropolis zijn)',
						'pubDate' => 'Fri, 28 Dec 2007 00:45:01 UT'
					]
				]
			]
		]];
		$this->assertEquals($expected, $result);

		$text = '<?xml version="1.0" encoding="UTF-8"?>
		<XRDS xmlns="xri://$xrds">
		<XRD xml:id="oauth" xmlns="xri://$XRD*($v*2.0)" version="2.0">
			<Type>xri://$xrds*simple</Type>
			<Expires>2008-04-13T07:34:58Z</Expires>
			<Service>
				<Type>http://oauth.net/core/1.0/endpoint/authorize</Type>
				<Type>http://oauth.net/core/1.0/parameters/auth-header</Type>
				<Type>http://oauth.net/core/1.0/parameters/uri-query</Type>
				<URI priority="10">https://ma.gnolia.com/oauth/authorize</URI>
				<URI priority="20">http://ma.gnolia.com/oauth/authorize</URI>
			</Service>
		</XRD>
		<XRD xmlns="xri://$XRD*($v*2.0)" version="2.0">
			<Type>xri://$xrds*simple</Type>
				<Service priority="10">
					<Type>http://oauth.net/discovery/1.0</Type>
					<URI>#oauth</URI>
				</Service>
		</XRD>
		</XRDS>';

		$xml = Xml::build($text);
		$result = Set::reverse($xml);

		$expected = ['XRDS' => [
			'XRD' => [
				[
					'@xml:id' => 'oauth',
					'@version' => '2.0',
					'Type' => 'xri://$xrds*simple',
					'Expires' => '2008-04-13T07:34:58Z',
					'Service' => [
						'Type' => [
							'http://oauth.net/core/1.0/endpoint/authorize',
							'http://oauth.net/core/1.0/parameters/auth-header',
							'http://oauth.net/core/1.0/parameters/uri-query'
						],
						'URI' => [
							[
								'@' => 'https://ma.gnolia.com/oauth/authorize',
								'@priority' => '10',
							],
							[
								'@' => 'http://ma.gnolia.com/oauth/authorize',
								'@priority' => '20'
							]
						]
					]
				],
				[
					'@version' => '2.0',
					'Type' => 'xri://$xrds*simple',
					'Service' => [
						'@priority' => '10',
						'Type' => 'http://oauth.net/discovery/1.0',
						'URI' => '#oauth'
					]
				]
			]
		]];
		$this->assertEquals($expected, $result);
	}

/**
 * testStrictKeyCheck method
 *
 * @return void
 */
	public function testStrictKeyCheck() {
		$set = ['a' => 'hi'];
		$this->assertFalse(Set::check($set, 'a.b'));
	}

/**
 * Tests Set::flatten
 *
 * @see Hash test cases, as Set::flatten() is just a proxy.
 * @return void
 */
	public function testFlatten() {
		$data = ['Larry', 'Curly', 'Moe'];
		$result = Set::flatten($data);
		$this->assertEquals($data, $result);

		$data[9] = 'Shemp';
		$result = Set::flatten($data);
		$this->assertEquals($data, $result);

		$data = [
			[
				'Post' => ['id' => '1', 'author_id' => null, 'title' => 'First Post'],
				'Author' => [],
			]
		];
		$result = Set::flatten($data);
		$expected = [
			'0.Post.id' => '1',
			'0.Post.author_id' => null,
			'0.Post.title' => 'First Post',
			'0.Author' => []
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Tests Set::expand
 *
 * @return void
 */
	public function testExpand() {
		$data = ['My', 'Array', 'To', 'Flatten'];
		$flat = Set::flatten($data);
		$result = Set::expand($flat);
		$this->assertEquals($data, $result);
	}

/**
 * test normalization
 *
 * @return void
 */
	public function testNormalizeStrings() {
		$result = Set::normalize('one,two,three');
		$expected = ['one' => null, 'two' => null, 'three' => null];
		$this->assertEquals($expected, $result);

		$result = Set::normalize('one two three', true, ' ');
		$expected = ['one' => null, 'two' => null, 'three' => null];
		$this->assertEquals($expected, $result);

		$result = Set::normalize('one  ,  two   ,  three   ', true, ',', true);
		$expected = ['one' => null, 'two' => null, 'three' => null];
		$this->assertEquals($expected, $result);
	}

/**
 * test normalizing arrays
 *
 * @return void
 */
	public function testNormalizeArrays() {
		$result = Set::normalize(['one', 'two', 'three']);
		$expected = ['one' => null, 'two' => null, 'three' => null];
		$this->assertEquals($expected, $result);

		$result = Set::normalize(['one', 'two', 'three'], false);
		$expected = ['one', 'two', 'three'];
		$this->assertEquals($expected, $result);

		$result = Set::normalize(['one' => 1, 'two' => 2, 'three' => 3, 'four'], false);
		$expected = ['one' => 1, 'two' => 2, 'three' => 3, 'four' => null];
		$this->assertEquals($expected, $result);

		$result = Set::normalize(['one' => 1, 'two' => 2, 'three' => 3, 'four']);
		$expected = ['one' => 1, 'two' => 2, 'three' => 3, 'four' => null];
		$this->assertEquals($expected, $result);

		$result = Set::normalize(['one' => ['a', 'b', 'c' => 'cee'], 'two' => 2, 'three']);
		$expected = ['one' => ['a', 'b', 'c' => 'cee'], 'two' => 2, 'three' => null];
		$this->assertEquals($expected, $result);
	}

/**
 * test Set nest with a normal model result set. For kicks rely on Set nest detecting the key names
 * automatically
 *
 * @return void
 */
	public function testNestModel() {
		$input = [
			[
				'ModelName' => [
					'id' => 1,
					'parent_id' => null
				],
			],
			[
				'ModelName' => [
					'id' => 2,
					'parent_id' => 1
				],
			],
			[
				'ModelName' => [
					'id' => 3,
					'parent_id' => 1
				],
			],
			[
				'ModelName' => [
					'id' => 4,
					'parent_id' => 1
				],
			],
			[
				'ModelName' => [
					'id' => 5,
					'parent_id' => 1
				],
			],
			[
				'ModelName' => [
					'id' => 6,
					'parent_id' => null
				],
			],
			[
				'ModelName' => [
					'id' => 7,
					'parent_id' => 6
				],
			],
			[
				'ModelName' => [
					'id' => 8,
					'parent_id' => 6
				],
			],
			[
				'ModelName' => [
					'id' => 9,
					'parent_id' => 6
				],
			],
			[
				'ModelName' => [
					'id' => 10,
					'parent_id' => 6
				]
			]
		];
		$expected = [
			[
				'ModelName' => [
					'id' => 1,
					'parent_id' => null
				],
				'children' => [
					[
						'ModelName' => [
							'id' => 2,
							'parent_id' => 1
						],
						'children' => []
					],
					[
						'ModelName' => [
							'id' => 3,
							'parent_id' => 1
						],
						'children' => []
					],
					[
						'ModelName' => [
							'id' => 4,
							'parent_id' => 1
						],
						'children' => []
					],
					[
						'ModelName' => [
							'id' => 5,
							'parent_id' => 1
						],
						'children' => []
					],

				]
			],
			[
				'ModelName' => [
					'id' => 6,
					'parent_id' => null
				],
				'children' => [
					[
						'ModelName' => [
							'id' => 7,
							'parent_id' => 6
						],
						'children' => []
					],
					[
						'ModelName' => [
							'id' => 8,
							'parent_id' => 6
						],
						'children' => []
					],
					[
						'ModelName' => [
							'id' => 9,
							'parent_id' => 6
						],
						'children' => []
					],
					[
						'ModelName' => [
							'id' => 10,
							'parent_id' => 6
						],
						'children' => []
					]
				]
			]
		];
		$result = Set::nest($input);
		$this->assertEquals($expected, $result);
	}

/**
 * test Set nest with a normal model result set, and a nominated root id
 *
 * @return void
 */
	public function testNestModelExplicitRoot() {
		$input = [
			[
				'ModelName' => [
					'id' => 1,
					'parent_id' => null
				],
			],
			[
				'ModelName' => [
					'id' => 2,
					'parent_id' => 1
				],
			],
			[
				'ModelName' => [
					'id' => 3,
					'parent_id' => 1
				],
			],
			[
				'ModelName' => [
					'id' => 4,
					'parent_id' => 1
				],
			],
			[
				'ModelName' => [
					'id' => 5,
					'parent_id' => 1
				],
			],
			[
				'ModelName' => [
					'id' => 6,
					'parent_id' => null
				],
			],
			[
				'ModelName' => [
					'id' => 7,
					'parent_id' => 6
				],
			],
			[
				'ModelName' => [
					'id' => 8,
					'parent_id' => 6
				],
			],
			[
				'ModelName' => [
					'id' => 9,
					'parent_id' => 6
				],
			],
			[
				'ModelName' => [
					'id' => 10,
					'parent_id' => 6
				]
			]
		];
		$expected = [
			[
				'ModelName' => [
					'id' => 6,
					'parent_id' => null
				],
				'children' => [
					[
						'ModelName' => [
							'id' => 7,
							'parent_id' => 6
						],
						'children' => []
					],
					[
						'ModelName' => [
							'id' => 8,
							'parent_id' => 6
						],
						'children' => []
					],
					[
						'ModelName' => [
							'id' => 9,
							'parent_id' => 6
						],
						'children' => []
					],
					[
						'ModelName' => [
							'id' => 10,
							'parent_id' => 6
						],
						'children' => []
					]
				]
			]
		];
		$result = Set::nest($input, ['root' => 6]);
		$this->assertEquals($expected, $result);
	}

/**
 * test Set nest with a 1d array - this method should be able to handle any type of array input
 *
 * @return void
 */
	public function testNest1Dimensional() {
		$input = [
			[
				'id' => 1,
				'parent_id' => null
			],
			[
				'id' => 2,
				'parent_id' => 1
			],
			[
				'id' => 3,
				'parent_id' => 1
			],
			[
				'id' => 4,
				'parent_id' => 1
			],
			[
				'id' => 5,
				'parent_id' => 1
			],
			[
				'id' => 6,
				'parent_id' => null
			],
			[
				'id' => 7,
				'parent_id' => 6
			],
			[
				'id' => 8,
				'parent_id' => 6
			],
			[
				'id' => 9,
				'parent_id' => 6
			],
			[
				'id' => 10,
				'parent_id' => 6
			]
		];
		$expected = [
			[
				'id' => 1,
				'parent_id' => null,
				'children' => [
					[
						'id' => 2,
						'parent_id' => 1,
						'children' => []
					],
					[
						'id' => 3,
						'parent_id' => 1,
						'children' => []
					],
					[
						'id' => 4,
						'parent_id' => 1,
						'children' => []
					],
					[
						'id' => 5,
						'parent_id' => 1,
						'children' => []
					],

				]
			],
			[
				'id' => 6,
				'parent_id' => null,
				'children' => [
					[
						'id' => 7,
						'parent_id' => 6,
						'children' => []
					],
					[
						'id' => 8,
						'parent_id' => 6,
						'children' => []
					],
					[
						'id' => 9,
						'parent_id' => 6,
						'children' => []
					],
					[
						'id' => 10,
						'parent_id' => 6,
						'children' => []
					]
				]
			]
		];
		$result = Set::nest($input, ['idPath' => '/id', 'parentPath' => '/parent_id']);
		$this->assertEquals($expected, $result);
	}

/**
 * test Set nest with no specified parent data.
 *
 * The result should be the same as the input.
 * For an easier comparison, unset all the empty children arrays from the result
 *
 * @return void
 */
	public function testMissingParent() {
		$input = [
			[
				'id' => 1,
			],
			[
				'id' => 2,
			],
			[
				'id' => 3,
			],
			[
				'id' => 4,
			],
			[
				'id' => 5,
			],
			[
				'id' => 6,
			],
			[
				'id' => 7,
			],
			[
				'id' => 8,
			],
			[
				'id' => 9,
			],
			[
				'id' => 10,
			]
		];

		$result = Set::nest($input, ['idPath' => '/id', 'parentPath' => '/parent_id']);
		foreach ($result as &$row) {
			if (empty($row['children'])) {
				unset($row['children']);
			}
		}
		$this->assertEquals($input, $result);
	}
}
