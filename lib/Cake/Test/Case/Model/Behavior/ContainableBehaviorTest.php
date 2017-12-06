<?php
/**
 * ContainableBehaviorTest file
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
 * @package       Cake.Test.Case.Model.Behavior
 * @since         CakePHP(tm) v 1.2.0.5669
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');
App::uses('AppModel', 'Model');

require_once dirname(dirname(__FILE__)) . DS . 'models.php';

/**
 * ContainableTest class
 *
 * @package       Cake.Test.Case.Model.Behavior
 */
class ContainableBehaviorTest extends CakeTestCase {

/**
 * Fixtures associated with this test case
 *
 * @var array
 */
	public $fixtures = [
		'core.article', 'core.article_featured', 'core.article_featureds_tags',
		'core.articles_tag', 'core.attachment', 'core.category',
		'core.comment', 'core.featured', 'core.tag', 'core.user',
		'core.join_a', 'core.join_b', 'core.join_c', 'core.join_a_c', 'core.join_a_b'
	];

/**
 * Method executed before each test
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->User = ClassRegistry::init('User');
		$this->Article = ClassRegistry::init('Article');
		$this->Tag = ClassRegistry::init('Tag');

		$this->User->bindModel([
			'hasMany' => ['Article', 'ArticleFeatured', 'Comment']
		], false);
		$this->User->ArticleFeatured->unbindModel(['belongsTo' => ['Category']], false);
		$this->User->ArticleFeatured->hasMany['Comment']['foreignKey'] = 'article_id';

		$this->Tag->bindModel([
			'hasAndBelongsToMany' => ['Article']
		], false);

		$this->User->Behaviors->load('Containable');
		$this->Article->Behaviors->load('Containable');
		$this->Tag->Behaviors->load('Containable');
	}

/**
 * Method executed after each test
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Article);
		unset($this->User);
		unset($this->Tag);
		parent::tearDown();
	}

/**
 * testContainments method
 *
 * @return void
 */
	public function testContainments() {
		$r = $this->_containments($this->Article, ['Comment' => ['conditions' => ['Comment.user_id' => 2]]]);
		$this->assertTrue(Set::matches('/Article/keep/Comment/conditions[Comment.user_id=2]', $r));

		$r = $this->_containments($this->User, [
			'ArticleFeatured' => [
				'Featured' => [
					'id',
					'Category' => 'name'
				]
		]]);
		$this->assertEquals(['id'], Hash::extract($r, 'ArticleFeatured.keep.Featured.fields'));

		$r = $this->_containments($this->Article, [
			'Comment' => [
				'User',
				'conditions' => ['Comment' => ['user_id' => 2]],
			],
		]);
		$this->assertTrue(Set::matches('/User', $r));
		$this->assertTrue(Set::matches('/Comment', $r));
		$this->assertTrue(Set::matches('/Article/keep/Comment/conditions/Comment[user_id=2]', $r));

		$r = $this->_containments($this->Article, ['Comment(comment, published)' => 'Attachment(attachment)', 'User(user)']);
		$this->assertTrue(Set::matches('/Comment', $r));
		$this->assertTrue(Set::matches('/User', $r));
		$this->assertTrue(Set::matches('/Article/keep/Comment', $r));
		$this->assertTrue(Set::matches('/Article/keep/User', $r));
		$this->assertEquals(['comment', 'published'], Hash::extract($r, 'Article.keep.Comment.fields'));
		$this->assertEquals(['user'], Hash::extract($r, 'Article.keep.User.fields'));
		$this->assertTrue(Set::matches('/Comment/keep/Attachment', $r));
		$this->assertEquals(['attachment'], Hash::extract($r, 'Comment.keep.Attachment.fields'));

		$r = $this->_containments($this->Article, ['Comment' => ['limit' => 1]]);
		$this->assertEquals(['Comment', 'Article'], array_keys($r));
		$result = Hash::extract($r, 'Comment[keep]');
		$this->assertEquals(['keep' => []], array_shift($result));
		$this->assertTrue(Set::matches('/Article/keep/Comment', $r));
		$result = Hash::extract($r, 'Article.keep');
		$this->assertEquals(['limit' => 1], array_shift($result));

		$r = $this->_containments($this->Article, ['Comment.User']);
		$this->assertEquals(['User', 'Comment', 'Article'], array_keys($r));

		$result = Hash::extract($r, 'User[keep]');
		$this->assertEquals(['keep' => []], array_shift($result));

		$result = Hash::extract($r, 'Comment[keep]');
		$this->assertEquals(['keep' => ['User' => []]], array_shift($result));

		$result = Hash::extract($r, 'Article[keep]');
		$this->assertEquals(['keep' => ['Comment' => []]], array_shift($result));

		$r = $this->_containments($this->Tag, ['Article' => ['User' => ['Comment' => [
			'Attachment' => ['conditions' => ['Attachment.id >' => 1]]
		]]]]);
		$this->assertTrue(Set::matches('/Attachment', $r));
		$this->assertTrue(Set::matches('/Comment/keep/Attachment/conditions', $r));
		$this->assertEquals(['Attachment.id >' => 1], $r['Comment']['keep']['Attachment']['conditions']);
		$this->assertTrue(Set::matches('/User/keep/Comment', $r));
		$this->assertTrue(Set::matches('/Article/keep/User', $r));
		$this->assertTrue(Set::matches('/Tag/keep/Article', $r));
	}

/**
 * testInvalidContainments method
 *
 * @expectedException PHPUnit_Framework_Error
 * @return void
 */
	public function testInvalidContainments() {
		$this->_containments($this->Article, ['Comment', 'InvalidBinding']);
	}

/**
 * testInvalidContainments method with suppressing error notices
 *
 * @return void
 */
	public function testInvalidContainmentsNoNotices() {
		$this->Article->Behaviors->load('Containable', ['notices' => false]);
		$this->_containments($this->Article, ['Comment', 'InvalidBinding']);
	}

/**
 * testBeforeFind method
 *
 * @return void
 */
	public function testBeforeFind() {
		$r = $this->Article->find('all', ['contain' => ['Comment']]);
		$this->assertFalse(Set::matches('/User', $r));
		$this->assertTrue(Set::matches('/Comment', $r));
		$this->assertFalse(Set::matches('/Comment/User', $r));

		$r = $this->Article->find('all', ['contain' => 'Comment.User']);
		$this->assertTrue(Set::matches('/Comment/User', $r));
		$this->assertFalse(Set::matches('/Comment/Article', $r));

		$r = $this->Article->find('all', ['contain' => ['Comment' => ['User', 'Article']]]);
		$this->assertTrue(Set::matches('/Comment/User', $r));
		$this->assertTrue(Set::matches('/Comment/Article', $r));

		$r = $this->Article->find('all', ['contain' => ['Comment' => ['conditions' => ['Comment.user_id' => 2]]]]);
		$this->assertFalse(Set::matches('/Comment[user_id!=2]', $r));
		$this->assertTrue(Set::matches('/Comment[user_id=2]', $r));

		$r = $this->Article->find('all', ['contain' => ['Comment.user_id = 2']]);
		$this->assertFalse(Set::matches('/Comment[user_id!=2]', $r));

		$r = $this->Article->find('all', ['contain' => 'Comment.id DESC']);
		$ids = $descIds = Hash::extract($r, 'Comment[1].id');
		rsort($descIds);
		$this->assertEquals($ids, $descIds);

		$r = $this->Article->find('all', ['contain' => 'Comment']);
		$this->assertTrue(Set::matches('/Comment[user_id!=2]', $r));

		$r = $this->Article->find('all', ['contain' => ['Comment' => ['fields' => 'comment']]]);
		$this->assertFalse(Set::matches('/Comment/created', $r));
		$this->assertTrue(Set::matches('/Comment/comment', $r));
		$this->assertFalse(Set::matches('/Comment/updated', $r));

		$r = $this->Article->find('all', ['contain' => ['Comment' => ['fields' => ['comment', 'updated']]]]);
		$this->assertFalse(Set::matches('/Comment/created', $r));
		$this->assertTrue(Set::matches('/Comment/comment', $r));
		$this->assertTrue(Set::matches('/Comment/updated', $r));

		$r = $this->Article->find('all', ['contain' => ['Comment' => ['comment', 'updated']]]);
		$this->assertFalse(Set::matches('/Comment/created', $r));
		$this->assertTrue(Set::matches('/Comment/comment', $r));
		$this->assertTrue(Set::matches('/Comment/updated', $r));

		$r = $this->Article->find('all', ['contain' => ['Comment(comment,updated)']]);
		$this->assertFalse(Set::matches('/Comment/created', $r));
		$this->assertTrue(Set::matches('/Comment/comment', $r));
		$this->assertTrue(Set::matches('/Comment/updated', $r));

		$r = $this->Article->find('all', ['contain' => 'Comment.created']);
		$this->assertTrue(Set::matches('/Comment/created', $r));
		$this->assertFalse(Set::matches('/Comment/comment', $r));

		$r = $this->Article->find('all', ['contain' => ['User.Article(title)', 'Comment(comment)']]);
		$this->assertFalse(Set::matches('/Comment/Article', $r));
		$this->assertFalse(Set::matches('/Comment/User', $r));
		$this->assertTrue(Set::matches('/Comment/comment', $r));
		$this->assertFalse(Set::matches('/Comment/created', $r));
		$this->assertTrue(Set::matches('/User/Article/title', $r));
		$this->assertFalse(Set::matches('/User/Article/created', $r));

		$r = $this->Article->find('all', ['contain' => []]);
		$this->assertFalse(Set::matches('/User', $r));
		$this->assertFalse(Set::matches('/Comment', $r));
	}

/**
 * testBeforeFindWithNonExistingBinding method
 *
 * @expectedException PHPUnit_Framework_Error
 * @return void
 */
	public function testBeforeFindWithNonExistingBinding() {
		$this->Article->find('all', ['contain' => ['Comment' => 'NonExistingBinding']]);
	}

/**
 * testContain method
 *
 * @return void
 */
	public function testContain() {
		$this->Article->contain('Comment.User');
		$r = $this->Article->find('all');
		$this->assertTrue(Set::matches('/Comment/User', $r));
		$this->assertFalse(Set::matches('/Comment/Article', $r));

		$r = $this->Article->find('all');
		$this->assertFalse(Set::matches('/Comment/User', $r));
	}

/**
 * testContainFindList method
 *
 * @return void
 */
	public function testContainFindList() {
		$this->Article->contain('Comment.User');
		$result = $this->Article->find('list');
		$expected = [
			1 => 'First Article',
			2 => 'Second Article',
			3 => 'Third Article'
		];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('list', ['fields' => ['Article.id', 'User.id'], 'contain' => ['User']]);
		$expected = [
			1 => '1',
			2 => '3',
			3 => '1'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * Test that mixing contain() and the contain find option.
 *
 * @return void
 */
	public function testContainAndContainOption() {
		$this->Article->contain();
		$r = $this->Article->find('all', [
			'contain' => ['Comment']
		]);
		$this->assertTrue(isset($r[0]['Comment']), 'No comment returned');
	}

/**
 * testFindEmbeddedNoBindings method
 *
 * @return void
 */
	public function testFindEmbeddedNoBindings() {
		$result = $this->Article->find('all', ['contain' => false]);
		$expected = [
			['Article' => [
				'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
				'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
			]],
			['Article' => [
				'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
				'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
			]],
			['Article' => [
				'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
				'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
			]]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindFirstLevel method
 *
 * @return void
 */
	public function testFindFirstLevel() {
		$this->Article->contain('User');
		$result = $this->Article->find('all', ['recursive' => 1]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				]
			]
		];
		$this->assertEquals($expected, $result);

		$this->Article->contain('User', 'Comment');
		$result = $this->Article->find('all', ['recursive' => 1]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'Comment' => [
					[
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31'
					],
					[
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31'
					],
					[
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
					],
					[
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
					]
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'Comment' => [
					[
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
					],
					[
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31'
					]
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'Comment' => []
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindEmbeddedFirstLevel method
 *
 * @return void
 */
	public function testFindEmbeddedFirstLevel() {
		$result = $this->Article->find('all', ['contain' => ['User']]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				]
			]
		];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('all', ['contain' => ['User', 'Comment']]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'Comment' => [
					[
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31'
					],
					[
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31'
					],
					[
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
					],
					[
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
					]
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'Comment' => [
					[
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
					],
					[
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31'
					]
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'Comment' => []
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindSecondLevel method
 *
 * @return void
 */
	public function testFindSecondLevel() {
		$this->Article->contain(['Comment' => 'User']);
		$result = $this->Article->find('all', ['recursive' => 2]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'Comment' => [
					[
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
						'User' => [
							'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
						]
					],
					[
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
						'User' => [
							'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
						]
					],
					[
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
						'User' => [
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						]
					],
					[
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
						'User' => [
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'Comment' => [
					[
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
						'User' => [
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						]
					],
					[
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
						'User' => [
							'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'Comment' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->Article->contain(['User' => 'ArticleFeatured']);
		$result = $this->Article->find('all', ['recursive' => 2]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => [
						[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$this->Article->contain(['User' => ['id', 'ArticleFeatured']]);
		$result = $this->Article->find('all', ['recursive' => 2]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => 1,
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'User' => [
					'id' => 3,
					'ArticleFeatured' => [
						[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => 1,
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$this->Article->contain(['User' => ['ArticleFeatured', 'Comment']]);
		$result = $this->Article->find('all', ['recursive' => 2]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					],
					'Comment' => [
						[
							'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
							'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
						],
						[
							'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
							'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						],
						[
							'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
							'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => [
						[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						]
					],
					'Comment' => []
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					],
					'Comment' => [
						[
							'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
							'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
						],
						[
							'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
							'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						],
						[
							'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
							'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
						]
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$this->Article->contain(['User' => ['ArticleFeatured']], 'Tag', ['Comment' => 'Attachment']);
		$result = $this->Article->find('all', ['recursive' => 2]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					]
				],
				'Comment' => [
					[
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
						'Attachment' => []
					],
					[
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
						'Attachment' => []
					],
					[
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
						'Attachment' => []
					],
					[
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
						'Attachment' => []
					]
				],
				'Tag' => [
					['id' => 1, 'tag' => 'tag1', 'created' => '2007-03-18 12:22:23', 'updated' => '2007-03-18 12:24:31'],
					['id' => 2, 'tag' => 'tag2', 'created' => '2007-03-18 12:24:23', 'updated' => '2007-03-18 12:26:31']
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => [
						[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						]
					]
				],
				'Comment' => [
					[
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
						'Attachment' => [
							'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
							'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						]
					],
					[
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
						'Attachment' => []
					]
				],
				'Tag' => [
					['id' => 1, 'tag' => 'tag1', 'created' => '2007-03-18 12:22:23', 'updated' => '2007-03-18 12:24:31'],
					['id' => 3, 'tag' => 'tag3', 'created' => '2007-03-18 12:26:23', 'updated' => '2007-03-18 12:28:31']
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					]
				],
				'Comment' => [],
				'Tag' => []
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindEmbeddedSecondLevel method
 *
 * @return void
 */
	public function testFindEmbeddedSecondLevel() {
		$result = $this->Article->find('all', ['contain' => ['Comment' => 'User']]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'Comment' => [
					[
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
						'User' => [
							'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
						]
					],
					[
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
						'User' => [
							'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
						]
					],
					[
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
						'User' => [
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						]
					],
					[
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
						'User' => [
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'Comment' => [
					[
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
						'User' => [
							'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
						]
					],
					[
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
						'User' => [
							'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
							'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'Comment' => []
			]
		];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('all', ['contain' => ['User' => 'ArticleFeatured']]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => [
						[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('all', ['contain' => ['User' => ['ArticleFeatured', 'Comment']]]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					],
					'Comment' => [
						[
							'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
							'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
						],
						[
							'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
							'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						],
						[
							'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
							'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
						]
					]
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => [
						[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						]
					],
					'Comment' => []
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					],
					'Comment' => [
						[
							'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
							'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31'
						],
						[
							'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
							'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						],
						[
							'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
							'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31'
						]
					]
				]
			]
		];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('all', ['contain' => ['User' => 'ArticleFeatured', 'Tag', 'Comment' => 'Attachment']]);
		$expected = [
			[
				'Article' => [
					'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					]
				],
				'Comment' => [
					[
						'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
						'Attachment' => []
					],
					[
						'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
						'Attachment' => []
					],
					[
						'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
						'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
						'Attachment' => []
					],
					[
						'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
						'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
						'Attachment' => []
					]
				],
				'Tag' => [
					['id' => 1, 'tag' => 'tag1', 'created' => '2007-03-18 12:22:23', 'updated' => '2007-03-18 12:24:31'],
					['id' => 2, 'tag' => 'tag2', 'created' => '2007-03-18 12:24:23', 'updated' => '2007-03-18 12:26:31']
				]
			],
			[
				'Article' => [
					'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
				],
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31',
					'ArticleFeatured' => [
						[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
						]
					]
				],
				'Comment' => [
					[
						'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
						'Attachment' => [
							'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
							'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
						]
					],
					[
						'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
						'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
						'Attachment' => []
					]
				],
				'Tag' => [
					['id' => 1, 'tag' => 'tag1', 'created' => '2007-03-18 12:22:23', 'updated' => '2007-03-18 12:24:31'],
					['id' => 3, 'tag' => 'tag3', 'created' => '2007-03-18 12:26:23', 'updated' => '2007-03-18 12:28:31']
				]
			],
			[
				'Article' => [
					'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
					'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
				],
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31',
					'ArticleFeatured' => [
						[
							'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
						],
						[
							'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
							'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
						]
					]
				],
				'Comment' => [],
				'Tag' => []
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindThirdLevel method
 *
 * @return void
 */
	public function testFindThirdLevel() {
		$this->User->contain(['ArticleFeatured' => ['Featured' => 'Category']]);
		$result = $this->User->find('all', ['recursive' => 3]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->User->contain(['ArticleFeatured' => ['Featured' => 'Category', 'Comment' => ['Article', 'Attachment']]]);
		$result = $this->User->find('all', ['recursive' => 3]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => [],
						'Comment' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Article' => [
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								],
								'Attachment' => [
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								]
							],
							[
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Article' => [
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								],
								'Attachment' => []
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->User->contain(['ArticleFeatured' => ['Featured' => 'Category', 'Comment' => 'Attachment'], 'Article']);
		$result = $this->User->find('all', ['recursive' => 3]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'Article' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
					]
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Attachment' => []
							],
							[
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Attachment' => []
							],
							[
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Attachment' => []
							],
							[
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Attachment' => []
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => [],
						'Comment' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'Article' => [],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'Article' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
					]
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Attachment' => [
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								]
							],
							[
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Attachment' => []
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'Article' => [],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindEmbeddedThirdLevel method
 *
 * @return void
 */
	public function testFindEmbeddedThirdLevel() {
		$result = $this->User->find('all', ['contain' => ['ArticleFeatured' => ['Featured' => 'Category']]]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$result = $this->User->find('all', ['contain' => ['ArticleFeatured' => ['Featured' => 'Category', 'Comment' => ['Article', 'Attachment']]]]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => [],
						'Comment' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Article' => [
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								],
								'Attachment' => [
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								]
							],
							[
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Article' => [
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								],
								'Attachment' => []
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$result = $this->User->find('all', ['contain' => ['ArticleFeatured' => ['Featured' => 'Category', 'Comment' => 'Attachment'], 'Article']]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'Article' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
					]
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Attachment' => []
							],
							[
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Attachment' => []
							],
							[
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Attachment' => []
							],
							[
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Attachment' => []
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => [],
						'Comment' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'Article' => [],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'Article' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
					]
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Attachment' => [
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								]
							],
							[
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Attachment' => []
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'Article' => [],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testSettingsThirdLevel method
 *
 * @return void
 */
	public function testSettingsThirdLevel() {
		$result = $this->User->find('all', ['contain' => ['ArticleFeatured' => ['Featured' => ['Category' => ['id', 'name']]]]]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'name' => 'Category 1'
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'name' => 'Category 1'
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$r = $this->User->find('all', ['contain' => [
			'ArticleFeatured' => [
				'id', 'title',
				'Featured' => [
					'id', 'category_id',
					'Category' => ['id', 'name']
				]
			]
		]]);

		$this->assertTrue(Set::matches('/User[id=1]', $r));
		$this->assertFalse(Set::matches('/Article', $r) || Set::matches('/Comment', $r));
		$this->assertTrue(Set::matches('/ArticleFeatured', $r));
		$this->assertFalse(Set::matches('/ArticleFeatured/User', $r) || Set::matches('/ArticleFeatured/Comment', $r) || Set::matches('/ArticleFeatured/Tag', $r));
		$this->assertTrue(Set::matches('/ArticleFeatured/Featured', $r));
		$this->assertFalse(Set::matches('/ArticleFeatured/Featured/ArticleFeatured', $r));
		$this->assertTrue(Set::matches('/ArticleFeatured/Featured/Category', $r));
		$this->assertTrue(Set::matches('/ArticleFeatured/Featured[id=1]', $r));
		$this->assertTrue(Set::matches('/ArticleFeatured/Featured[id=1]/Category[id=1]', $r));
		$this->assertTrue(Set::matches('/ArticleFeatured/Featured[id=1]/Category[name=Category 1]', $r));

		$r = $this->User->find('all', ['contain' => [
			'ArticleFeatured' => [
				'title',
				'Featured' => [
					'id',
					'Category' => 'name'
				]
			]
		]]);

		$this->assertTrue(Set::matches('/User[id=1]', $r));
		$this->assertFalse(Set::matches('/Article', $r) || Set::matches('/Comment', $r));
		$this->assertTrue(Set::matches('/ArticleFeatured', $r));
		$this->assertFalse(Set::matches('/ArticleFeatured/User', $r) || Set::matches('/ArticleFeatured/Comment', $r) || Set::matches('/ArticleFeatured/Tag', $r));
		$this->assertTrue(Set::matches('/ArticleFeatured/Featured', $r));
		$this->assertFalse(Set::matches('/ArticleFeatured/Featured/ArticleFeatured', $r));
		$this->assertTrue(Set::matches('/ArticleFeatured/Featured/Category', $r));
		$this->assertTrue(Set::matches('/ArticleFeatured/Featured[id=1]', $r));
		$this->assertTrue(Set::matches('/ArticleFeatured/Featured[id=1]/Category[name=Category 1]', $r));

		$result = $this->User->find('all', ['contain' => [
			'ArticleFeatured' => [
				'title',
				'Featured' => [
					'category_id',
					'Category' => 'name'
				]
			]
		]]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'ArticleFeatured' => [
					[
						'title' => 'First Article', 'id' => 1, 'user_id' => 1,
						'Featured' => [
							'category_id' => 1, 'id' => 1,
							'Category' => [
								'name' => 'Category 1'
							]
						]
					],
					[
						'title' => 'Third Article', 'id' => 3, 'user_id' => 1,
						'Featured' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'ArticleFeatured' => [
					[
						'title' => 'Second Article', 'id' => 2, 'user_id' => 3,
						'Featured' => [
							'category_id' => 1, 'id' => 2,
							'Category' => [
								'name' => 'Category 1'
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$orders = [
			'title DESC', 'title DESC, published DESC',
			['title' => 'DESC'], ['title' => 'DESC', 'published' => 'DESC'],
		];
		foreach ($orders as $order) {
			$result = $this->User->find('all', ['contain' => [
				'ArticleFeatured' => [
					'title', 'order' => $order,
					'Featured' => [
						'category_id',
						'Category' => 'name'
					]
				]
			]]);
			$expected = [
				[
					'User' => [
						'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
						'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
					],
					'ArticleFeatured' => [
						[
							'title' => 'Third Article', 'id' => 3, 'user_id' => 1,
							'Featured' => []
						],
						[
							'title' => 'First Article', 'id' => 1, 'user_id' => 1,
							'Featured' => [
								'category_id' => 1, 'id' => 1,
								'Category' => [
									'name' => 'Category 1'
								]
							]
						]
					]
				],
				[
					'User' => [
						'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
						'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
					],
					'ArticleFeatured' => []
				],
				[
					'User' => [
						'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
						'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
					],
					'ArticleFeatured' => [
						[
							'title' => 'Second Article', 'id' => 2, 'user_id' => 3,
							'Featured' => [
								'category_id' => 1, 'id' => 2,
								'Category' => [
									'name' => 'Category 1'
								]
							]
						]
					]
				],
				[
					'User' => [
						'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
						'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
					],
					'ArticleFeatured' => []
				]
			];
			$this->assertEquals($expected, $result);
		}
	}

/**
 * testFindThirdLevelNonReset method
 *
 * @return void
 */
	public function testFindThirdLevelNonReset() {
		$this->User->contain(false, ['ArticleFeatured' => ['Featured' => 'Category']]);
		$result = $this->User->find('all', ['recursive' => 3]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->User->resetBindings();

		$this->User->contain(false, ['ArticleFeatured' => ['Featured' => 'Category', 'Comment' => ['Article', 'Attachment']]]);
		$result = $this->User->find('all', ['recursive' => 3]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => [],
						'Comment' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Article' => [
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								],
								'Attachment' => [
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								]
							],
							[
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Article' => [
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								],
								'Attachment' => []
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->User->resetBindings();

		$this->User->contain(false, ['ArticleFeatured' => ['Featured' => 'Category', 'Comment' => 'Attachment'], 'Article']);
		$result = $this->User->find('all', ['recursive' => 3]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'Article' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
					]
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Attachment' => []
							],
							[
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Attachment' => []
							],
							[
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Attachment' => []
							],
							[
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Attachment' => []
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => [],
						'Comment' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'Article' => [],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'Article' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
					]
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Attachment' => [
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								]
							],
							[
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Attachment' => []
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'Article' => [],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testFindEmbeddedThirdLevelNonReset method
 *
 * @return void
 */
	public function testFindEmbeddedThirdLevelNonReset() {
		$result = $this->User->find('all', ['reset' => false, 'contain' => ['ArticleFeatured' => ['Featured' => 'Category']]]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->_assertBindings($this->User, ['hasMany' => ['ArticleFeatured']]);
		$this->_assertBindings($this->User->ArticleFeatured, ['hasOne' => ['Featured']]);
		$this->_assertBindings($this->User->ArticleFeatured->Featured, ['belongsTo' => ['Category']]);

		$this->User->resetBindings();

		$this->_assertBindings($this->User, ['hasMany' => ['Article', 'ArticleFeatured', 'Comment']]);
		$this->_assertBindings($this->User->ArticleFeatured, ['belongsTo' => ['User'], 'hasOne' => ['Featured'], 'hasMany' => ['Comment'], 'hasAndBelongsToMany' => ['Tag']]);
		$this->_assertBindings($this->User->ArticleFeatured->Featured, ['belongsTo' => ['ArticleFeatured', 'Category']]);
		$this->_assertBindings($this->User->ArticleFeatured->Comment, ['belongsTo' => ['Article', 'User'], 'hasOne' => ['Attachment']]);

		$result = $this->User->find('all', ['reset' => false, 'contain' => ['ArticleFeatured' => ['Featured' => 'Category', 'Comment' => ['Article', 'Attachment']]]]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => [],
						'Comment' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Article' => [
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								],
								'Attachment' => [
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								]
							],
							[
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Article' => [
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								],
								'Attachment' => []
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->_assertBindings($this->User, ['hasMany' => ['ArticleFeatured']]);
		$this->_assertBindings($this->User->ArticleFeatured, ['hasOne' => ['Featured'], 'hasMany' => ['Comment']]);
		$this->_assertBindings($this->User->ArticleFeatured->Featured, ['belongsTo' => ['Category']]);
		$this->_assertBindings($this->User->ArticleFeatured->Comment, ['belongsTo' => ['Article'], 'hasOne' => ['Attachment']]);

		$this->User->resetBindings();
		$this->_assertBindings($this->User, ['hasMany' => ['Article', 'ArticleFeatured', 'Comment']]);
		$this->_assertBindings($this->User->ArticleFeatured, ['belongsTo' => ['User'], 'hasOne' => ['Featured'], 'hasMany' => ['Comment'], 'hasAndBelongsToMany' => ['Tag']]);
		$this->_assertBindings($this->User->ArticleFeatured->Featured, ['belongsTo' => ['ArticleFeatured', 'Category']]);
		$this->_assertBindings($this->User->ArticleFeatured->Comment, ['belongsTo' => ['Article', 'User'], 'hasOne' => ['Attachment']]);

		$result = $this->User->find('all', ['contain' => ['ArticleFeatured' => ['Featured' => 'Category', 'Comment' => ['Article', 'Attachment']], false]]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							],
							[
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Article' => [
									'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
								],
								'Attachment' => []
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => [],
						'Comment' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Article' => [
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								],
								'Attachment' => [
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								]
							],
							[
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Article' => [
									'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
									'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
								],
								'Attachment' => []
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->_assertBindings($this->User, ['hasMany' => ['ArticleFeatured']]);
		$this->_assertBindings($this->User->ArticleFeatured, ['hasOne' => ['Featured'], 'hasMany' => ['Comment']]);
		$this->_assertBindings($this->User->ArticleFeatured->Featured, ['belongsTo' => ['Category']]);
		$this->_assertBindings($this->User->ArticleFeatured->Comment, ['belongsTo' => ['Article'], 'hasOne' => ['Attachment']]);

		$this->User->resetBindings();
		$this->_assertBindings($this->User, ['hasMany' => ['Article', 'ArticleFeatured', 'Comment']]);
		$this->_assertBindings($this->User->ArticleFeatured, ['belongsTo' => ['User'], 'hasOne' => ['Featured'], 'hasMany' => ['Comment'], 'hasAndBelongsToMany' => ['Tag']]);
		$this->_assertBindings($this->User->ArticleFeatured->Featured, ['belongsTo' => ['ArticleFeatured', 'Category']]);
		$this->_assertBindings($this->User->ArticleFeatured->Comment, ['belongsTo' => ['Article', 'User'], 'hasOne' => ['Attachment']]);

		$result = $this->User->find('all', ['reset' => false, 'contain' => ['ArticleFeatured' => ['Featured' => 'Category', 'Comment' => 'Attachment'], 'Article']]);
		$expected = [
			[
				'User' => [
					'id' => 1, 'user' => 'mariano', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'
				],
				'Article' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'
					]
				],
				'ArticleFeatured' => [
					[
						'id' => 1, 'user_id' => 1, 'title' => 'First Article', 'body' => 'First Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
						'Featured' => [
							'id' => 1, 'article_featured_id' => 1, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 1, 'article_id' => 1, 'user_id' => 2, 'comment' => 'First Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:45:23', 'updated' => '2007-03-18 10:47:31',
								'Attachment' => []
							],
							[
								'id' => 2, 'article_id' => 1, 'user_id' => 4, 'comment' => 'Second Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:47:23', 'updated' => '2007-03-18 10:49:31',
								'Attachment' => []
							],
							[
								'id' => 3, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Third Comment for First Article',
								'published' => 'Y', 'created' => '2007-03-18 10:49:23', 'updated' => '2007-03-18 10:51:31',
								'Attachment' => []
							],
							[
								'id' => 4, 'article_id' => 1, 'user_id' => 1, 'comment' => 'Fourth Comment for First Article',
								'published' => 'N', 'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31',
								'Attachment' => []
							]
						]
					],
					[
						'id' => 3, 'user_id' => 1, 'title' => 'Third Article', 'body' => 'Third Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31',
						'Featured' => [],
						'Comment' => []
					]
				]
			],
			[
				'User' => [
					'id' => 2, 'user' => 'nate', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:18:23', 'updated' => '2007-03-17 01:20:31'
				],
				'Article' => [],
				'ArticleFeatured' => []
			],
			[
				'User' => [
					'id' => 3, 'user' => 'larry', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:20:23', 'updated' => '2007-03-17 01:22:31'
				],
				'Article' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'
					]
				],
				'ArticleFeatured' => [
					[
						'id' => 2, 'user_id' => 3, 'title' => 'Second Article', 'body' => 'Second Article Body',
						'published' => 'Y', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31',
						'Featured' => [
							'id' => 2, 'article_featured_id' => 2, 'category_id' => 1, 'published_date' => '2007-03-31 10:39:23',
							'end_date' => '2007-05-15 10:39:23', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31',
							'Category' => [
								'id' => 1, 'parent_id' => 0, 'name' => 'Category 1',
								'created' => '2007-03-18 15:30:23', 'updated' => '2007-03-18 15:32:31'
							]
						],
						'Comment' => [
							[
								'id' => 5, 'article_id' => 2, 'user_id' => 1, 'comment' => 'First Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:53:23', 'updated' => '2007-03-18 10:55:31',
								'Attachment' => [
									'id' => 1, 'comment_id' => 5, 'attachment' => 'attachment.zip',
									'created' => '2007-03-18 10:51:23', 'updated' => '2007-03-18 10:53:31'
								]
							],
							[
								'id' => 6, 'article_id' => 2, 'user_id' => 2, 'comment' => 'Second Comment for Second Article',
								'published' => 'Y', 'created' => '2007-03-18 10:55:23', 'updated' => '2007-03-18 10:57:31',
								'Attachment' => []
							]
						]
					]
				]
			],
			[
				'User' => [
					'id' => 4, 'user' => 'garrett', 'password' => '5f4dcc3b5aa765d61d8327deb882cf99',
					'created' => '2007-03-17 01:22:23', 'updated' => '2007-03-17 01:24:31'
				],
				'Article' => [],
				'ArticleFeatured' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->_assertBindings($this->User, ['hasMany' => ['Article', 'ArticleFeatured']]);
		$this->_assertBindings($this->User->Article);
		$this->_assertBindings($this->User->ArticleFeatured, ['hasOne' => ['Featured'], 'hasMany' => ['Comment']]);
		$this->_assertBindings($this->User->ArticleFeatured->Featured, ['belongsTo' => ['Category']]);
		$this->_assertBindings($this->User->ArticleFeatured->Comment, ['hasOne' => ['Attachment']]);

		$this->User->resetBindings();
		$this->_assertBindings($this->User, ['hasMany' => ['Article', 'ArticleFeatured', 'Comment']]);
		$this->_assertBindings($this->User->Article, ['belongsTo' => ['User'], 'hasMany' => ['Comment'], 'hasAndBelongsToMany' => ['Tag']]);
		$this->_assertBindings($this->User->ArticleFeatured, ['belongsTo' => ['User'], 'hasOne' => ['Featured'], 'hasMany' => ['Comment'], 'hasAndBelongsToMany' => ['Tag']]);
		$this->_assertBindings($this->User->ArticleFeatured->Featured, ['belongsTo' => ['ArticleFeatured', 'Category']]);
		$this->_assertBindings($this->User->ArticleFeatured->Comment, ['belongsTo' => ['Article', 'User'], 'hasOne' => ['Attachment']]);
	}

/**
 * testEmbeddedFindFields method
 *
 * @return void
 */
	public function testEmbeddedFindFields() {
		$result = $this->Article->find('all', [
			'contain' => ['User(user)'],
			'fields' => ['title'],
			'order' => ['Article.id' => 'ASC']
		]);
		$expected = [
			['Article' => ['title' => 'First Article'], 'User' => ['user' => 'mariano', 'id' => 1]],
			['Article' => ['title' => 'Second Article'], 'User' => ['user' => 'larry', 'id' => 3]],
			['Article' => ['title' => 'Third Article'], 'User' => ['user' => 'mariano', 'id' => 1]],
		];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('all', [
			'contain' => ['User(id, user)'],
			'fields' => ['title'],
			'order' => ['Article.id' => 'ASC']
		]);
		$expected = [
			['Article' => ['title' => 'First Article'], 'User' => ['user' => 'mariano', 'id' => 1]],
			['Article' => ['title' => 'Second Article'], 'User' => ['user' => 'larry', 'id' => 3]],
			['Article' => ['title' => 'Third Article'], 'User' => ['user' => 'mariano', 'id' => 1]],
		];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('all', [
			'contain' => [
				'Comment(comment, published)' => 'Attachment(attachment)', 'User(user)'
			],
			'fields' => ['title'],
			'order' => ['Article.id' => 'ASC']
		]);
		if (!empty($result)) {
			foreach ($result as $i => $article) {
				foreach ($article['Comment'] as $j => $comment) {
					$result[$i]['Comment'][$j] = array_diff_key($comment, ['id' => true]);
				}
			}
		}
		$expected = [
			[
				'Article' => ['title' => 'First Article', 'id' => 1],
				'User' => ['user' => 'mariano', 'id' => 1],
				'Comment' => [
					['comment' => 'First Comment for First Article', 'published' => 'Y', 'article_id' => 1, 'Attachment' => []],
					['comment' => 'Second Comment for First Article', 'published' => 'Y', 'article_id' => 1, 'Attachment' => []],
					['comment' => 'Third Comment for First Article', 'published' => 'Y', 'article_id' => 1, 'Attachment' => []],
					['comment' => 'Fourth Comment for First Article', 'published' => 'N', 'article_id' => 1, 'Attachment' => []],
				]
			],
			[
				'Article' => ['title' => 'Second Article', 'id' => 2],
				'User' => ['user' => 'larry', 'id' => 3],
				'Comment' => [
					['comment' => 'First Comment for Second Article', 'published' => 'Y', 'article_id' => 2, 'Attachment' => [
						'attachment' => 'attachment.zip', 'id' => 1
					]],
					['comment' => 'Second Comment for Second Article', 'published' => 'Y', 'article_id' => 2, 'Attachment' => []]
				]
			],
			[
				'Article' => ['title' => 'Third Article', 'id' => 3],
				'User' => ['user' => 'mariano', 'id' => 1],
				'Comment' => []
			],
		];
		$this->assertEquals($expected, $result);
	}

/**
 * test that hasOne and belongsTo fields act the same in a contain array.
 *
 * @return void
 */
	public function testHasOneFieldsInContain() {
		$this->Article->unbindModel([
			'hasMany' => ['Comment']
		], true);
		unset($this->Article->Comment);
		$this->Article->bindModel([
			'hasOne' => ['Comment']
		]);

		$result = $this->Article->find('all', [
			'fields' => ['title', 'body'],
			'contain' => [
				'Comment' => [
					'fields' => ['comment']
				],
				'User' => [
					'fields' => ['user']
				]
			],
			'order' => 'Article.id ASC',
		]);
		$this->assertTrue(isset($result[0]['Article']['title']), 'title missing %s');
		$this->assertTrue(isset($result[0]['Article']['body']), 'body missing %s');
		$this->assertTrue(isset($result[0]['Comment']['comment']), 'comment missing %s');
		$this->assertTrue(isset($result[0]['User']['user']), 'body missing %s');
		$this->assertFalse(isset($result[0]['Comment']['published']), 'published found %s');
		$this->assertFalse(isset($result[0]['User']['password']), 'password found %s');
	}

/**
 * testFindConditionalBinding method
 *
 * @return void
 */
	public function testFindConditionalBinding() {
		$this->Article->contain([
			'User(user)',
			'Tag' => [
				'fields' => ['tag', 'created'],
				'conditions' => ['created >=' => '2007-03-18 12:24']
			]
		]);
		$result = $this->Article->find('all', [
			'fields' => ['title'],
			'order' => ['Article.id' => 'ASC']
		]);
		$expected = [
			[
				'Article' => ['id' => 1, 'title' => 'First Article'],
				'User' => ['id' => 1, 'user' => 'mariano'],
				'Tag' => [['tag' => 'tag2', 'created' => '2007-03-18 12:24:23']]
			],
			[
				'Article' => ['id' => 2, 'title' => 'Second Article'],
				'User' => ['id' => 3, 'user' => 'larry'],
				'Tag' => [['tag' => 'tag3', 'created' => '2007-03-18 12:26:23']]
			],
			[
				'Article' => ['id' => 3, 'title' => 'Third Article'],
				'User' => ['id' => 1, 'user' => 'mariano'],
				'Tag' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->Article->contain(['User(id,user)', 'Tag' => ['fields' => ['tag', 'created']]]);
		$result = $this->Article->find('all', ['fields' => ['title'], 'order' => ['Article.id' => 'ASC']]);
		$expected = [
			[
				'Article' => ['id' => 1, 'title' => 'First Article'],
				'User' => ['id' => 1, 'user' => 'mariano'],
				'Tag' => [
					['tag' => 'tag1', 'created' => '2007-03-18 12:22:23'],
					['tag' => 'tag2', 'created' => '2007-03-18 12:24:23']
				]
			],
			[
				'Article' => ['id' => 2, 'title' => 'Second Article'],
				'User' => ['id' => 3, 'user' => 'larry'],
				'Tag' => [
					['tag' => 'tag1', 'created' => '2007-03-18 12:22:23'],
					['tag' => 'tag3', 'created' => '2007-03-18 12:26:23']
				]
			],
			[
				'Article' => ['id' => 3, 'title' => 'Third Article'],
				'User' => ['id' => 1, 'user' => 'mariano'],
				'Tag' => []
			]
		];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('all', [
			'fields' => ['title'],
			'contain' => ['User(id,user)', 'Tag' => ['fields' => ['tag', 'created']]],
			'order' => ['Article.id' => 'ASC']
		]);
		$expected = [
			[
				'Article' => ['id' => 1, 'title' => 'First Article'],
				'User' => ['id' => 1, 'user' => 'mariano'],
				'Tag' => [
					['tag' => 'tag1', 'created' => '2007-03-18 12:22:23'],
					['tag' => 'tag2', 'created' => '2007-03-18 12:24:23']
				]
			],
			[
				'Article' => ['id' => 2, 'title' => 'Second Article'],
				'User' => ['id' => 3, 'user' => 'larry'],
				'Tag' => [
					['tag' => 'tag1', 'created' => '2007-03-18 12:22:23'],
					['tag' => 'tag3', 'created' => '2007-03-18 12:26:23']
				]
			],
			[
				'Article' => ['id' => 3, 'title' => 'Third Article'],
				'User' => ['id' => 1, 'user' => 'mariano'],
				'Tag' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->Article->contain([
			'User(id,user)',
			'Tag' => [
				'fields' => ['tag', 'created'],
				'conditions' => ['created >=' => '2007-03-18 12:24']
			]
		]);
		$result = $this->Article->find('all', ['fields' => ['title'], 'order' => ['Article.id' => 'ASC']]);
		$expected = [
			[
				'Article' => ['id' => 1, 'title' => 'First Article'],
				'User' => ['id' => 1, 'user' => 'mariano'],
				'Tag' => [['tag' => 'tag2', 'created' => '2007-03-18 12:24:23']]
			],
			[
				'Article' => ['id' => 2, 'title' => 'Second Article'],
				'User' => ['id' => 3, 'user' => 'larry'],
				'Tag' => [['tag' => 'tag3', 'created' => '2007-03-18 12:26:23']]
			],
			[
				'Article' => ['id' => 3, 'title' => 'Third Article'],
				'User' => ['id' => 1, 'user' => 'mariano'],
				'Tag' => []
			]
		];
		$this->assertEquals($expected, $result);

		$this->assertTrue(empty($this->User->Article->hasAndBelongsToMany['Tag']['conditions']));

		$result = $this->User->find('all', ['contain' => [
			'Article.Tag' => ['conditions' => ['created >=' => '2007-03-18 12:24']]
		]]);

		$this->assertTrue(Set::matches('/User[id=1]', $result));
		$this->assertFalse(Set::matches('/Article[id=1]/Tag[id=1]', $result));
		$this->assertTrue(Set::matches('/Article[id=1]/Tag[id=2]', $result));
		$this->assertTrue(empty($this->User->Article->hasAndBelongsToMany['Tag']['conditions']));

		$this->assertTrue(empty($this->User->Article->hasAndBelongsToMany['Tag']['order']));

		$result = $this->User->find('all', ['contain' => [
			'Article.Tag' => ['order' => 'created DESC']
		]]);

		$this->assertTrue(Set::matches('/User[id=1]', $result));
		$this->assertTrue(Set::matches('/Article[id=1]/Tag[id=1]', $result));
		$this->assertTrue(Set::matches('/Article[id=1]/Tag[id=2]', $result));
		$this->assertTrue(empty($this->User->Article->hasAndBelongsToMany['Tag']['order']));
	}

/**
 * testOtherFinds method
 *
 * @return void
 */
	public function testOtherFinds() {
		$result = $this->Article->find('count');
		$expected = 3;
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('count', ['conditions' => ['Article.id >' => '1']]);
		$expected = 2;
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('count', ['contain' => []]);
		$expected = 3;
		$this->assertEquals($expected, $result);

		$this->Article->contain(['User(id,user)', 'Tag' => ['fields' => ['tag', 'created'], 'conditions' => ['created >=' => '2007-03-18 12:24']]]);
		$result = $this->Article->find('first', ['fields' => ['title']]);
		$expected = [
			'Article' => ['id' => 1, 'title' => 'First Article'],
			'User' => ['id' => 1, 'user' => 'mariano'],
			'Tag' => [['tag' => 'tag2', 'created' => '2007-03-18 12:24:23']]
		];
		$this->assertEquals($expected, $result);

		$this->Article->contain(['User(id,user)', 'Tag' => ['fields' => ['tag', 'created']]]);
		$result = $this->Article->find('first', ['fields' => ['title']]);
		$expected = [
			'Article' => ['id' => 1, 'title' => 'First Article'],
			'User' => ['id' => 1, 'user' => 'mariano'],
			'Tag' => [
				['tag' => 'tag1', 'created' => '2007-03-18 12:22:23'],
				['tag' => 'tag2', 'created' => '2007-03-18 12:24:23']
			]
		];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('first', [
			'fields' => ['title'],
			'order' => 'Article.id DESC',
			'contain' => ['User(id,user)', 'Tag' => ['fields' => ['tag', 'created']]]
		]);
		$expected = [
			'Article' => ['id' => 3, 'title' => 'Third Article'],
			'User' => ['id' => 1, 'user' => 'mariano'],
			'Tag' => []
		];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('list', [
			'contain' => ['User(id,user)'],
			'fields' => ['Article.id', 'Article.title']
		]);
		$expected = [
			1 => 'First Article',
			2 => 'Second Article',
			3 => 'Third Article'
		];
		$this->assertEquals($expected, $result);
	}

/**
 * testOriginalAssociations method
 *
 * @return void
 */
	public function testOriginalAssociations() {
		$this->Article->Comment->Behaviors->load('Containable');

		$options = [
			'conditions' => [
				'Comment.published' => 'Y',
			],
			'contain' => 'User',
			'recursive' => 1
		];

		$firstResult = $this->Article->Comment->find('all', $options);

		$this->Article->Comment->find('all', [
			'conditions' => [
				'User.user' => 'mariano'
			],
			'fields' => ['User.password'],
			'contain' => ['User.password'],
		]);

		$result = $this->Article->Comment->find('all', $options);
		$this->assertEquals($firstResult, $result);

		$this->Article->unbindModel(['hasMany' => ['Comment'], 'belongsTo' => ['User'], 'hasAndBelongsToMany' => ['Tag']], false);
		$this->Article->bindModel(['hasMany' => ['Comment'], 'belongsTo' => ['User']], false);

		$r = $this->Article->find('all', ['contain' => ['Comment(comment)', 'User(user)'], 'fields' => ['title']]);
		$this->assertTrue(Set::matches('/Article[id=1]', $r));
		$this->assertTrue(Set::matches('/User[id=1]', $r));
		$this->assertTrue(Set::matches('/Comment[article_id=1]', $r));
		$this->assertFalse(Set::matches('/Comment[id=1]', $r));

		$r = $this->Article->find('all');
		$this->assertTrue(Set::matches('/Article[id=1]', $r));
		$this->assertTrue(Set::matches('/User[id=1]', $r));
		$this->assertTrue(Set::matches('/Comment[article_id=1]', $r));
		$this->assertTrue(Set::matches('/Comment[id=1]', $r));

		$this->Article->bindModel(['hasAndBelongsToMany' => ['Tag']], false);

		$this->Article->contain(false, ['User(id,user)', 'Comment' => ['fields' => ['comment'], 'conditions' => ['created >=' => '2007-03-18 10:49']]]);
		$result = $this->Article->find('all', ['fields' => ['title'], 'limit' => 1, 'page' => 1, 'order' => 'Article.id ASC']);
		$expected = [[
			'Article' => ['id' => 1, 'title' => 'First Article'],
			'User' => ['id' => 1, 'user' => 'mariano'],
			'Comment' => [
				['comment' => 'Third Comment for First Article', 'article_id' => 1],
				['comment' => 'Fourth Comment for First Article', 'article_id' => 1]
			]
		]];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('all', ['fields' => ['title', 'User.id', 'User.user'], 'limit' => 1, 'page' => 2, 'order' => 'Article.id ASC']);
		$expected = [[
			'Article' => ['id' => 2, 'title' => 'Second Article'],
			'User' => ['id' => 3, 'user' => 'larry'],
			'Comment' => [
				['comment' => 'First Comment for Second Article', 'article_id' => 2],
				['comment' => 'Second Comment for Second Article', 'article_id' => 2]
			]
		]];
		$this->assertEquals($expected, $result);

		$result = $this->Article->find('all', ['fields' => ['title', 'User.id', 'User.user'], 'limit' => 1, 'page' => 3, 'order' => 'Article.id ASC']);
		$expected = [[
			'Article' => ['id' => 3, 'title' => 'Third Article'],
			'User' => ['id' => 1, 'user' => 'mariano'],
			'Comment' => []
		]];
		$this->assertEquals($expected, $result);

		$this->Article->contain(false, ['User' => ['fields' => 'user'], 'Comment']);
		$result = $this->Article->find('all');
		$this->assertTrue(Set::matches('/Article[id=1]', $result));
		$this->assertTrue(Set::matches('/User[user=mariano]', $result));
		$this->assertTrue(Set::matches('/Comment[article_id=1]', $result));
		$this->Article->resetBindings();

		$this->Article->contain(false, ['User' => ['fields' => ['user']], 'Comment']);
		$result = $this->Article->find('all');
		$this->assertTrue(Set::matches('/Article[id=1]', $result));
		$this->assertTrue(Set::matches('/User[user=mariano]', $result));
		$this->assertTrue(Set::matches('/Comment[article_id=1]', $result));
		$this->Article->resetBindings();
	}

/**
 * testResetAddedAssociation method
 *
 * @return void
 */
	public function testResetAddedAssociation() {
		$this->assertTrue(empty($this->Article->hasMany['ArticlesTag']));

		$this->Article->bindModel([
			'hasMany' => ['ArticlesTag']
		]);
		$this->assertTrue(!empty($this->Article->hasMany['ArticlesTag']));

		$result = $this->Article->find('first', [
			'conditions' => ['Article.id' => 1],
			'contain' => ['ArticlesTag']
		]);

		$expected = ['Article', 'ArticlesTag'];
		$this->assertTrue(!empty($result));
		$this->assertEquals('First Article', $result['Article']['title']);
		$this->assertTrue(!empty($result['ArticlesTag']));
		$this->assertEquals($expected, array_keys($result));

		$this->assertTrue(empty($this->Article->hasMany['ArticlesTag']));

		$this->JoinA = ClassRegistry::init('JoinA');
		$this->JoinB = ClassRegistry::init('JoinB');
		$this->JoinC = ClassRegistry::init('JoinC');

		$this->JoinA->Behaviors->load('Containable');
		$this->JoinB->Behaviors->load('Containable');
		$this->JoinC->Behaviors->load('Containable');

		$this->JoinA->JoinB->find('all', ['contain' => ['JoinA']]);
		$this->JoinA->bindModel(['hasOne' => ['JoinAsJoinC' => ['joinTable' => 'as_cs']]], false);
		$result = $this->JoinA->hasOne;
		$this->JoinA->find('all');
		$resultAfter = $this->JoinA->hasOne;
		$this->assertEquals($result, $resultAfter);
	}

/**
 * testResetAssociation method
 *
 * @return void
 */
	public function testResetAssociation() {
		$this->Article->Behaviors->load('Containable');
		$this->Article->Comment->Behaviors->load('Containable');
		$this->Article->User->Behaviors->load('Containable');

		$initialOptions = [
			'conditions' => [
				'Comment.published' => 'Y',
			],
			'contain' => 'User',
			'recursive' => 1,
		];

		$initialModels = $this->Article->Comment->find('all', $initialOptions);

		$findOptions = [
			'conditions' => [
				'User.user' => 'mariano',
			],
			'fields' => ['User.password'],
			'contain' => ['User.password']
		];
		$result = $this->Article->Comment->find('all', $findOptions);
		$result = $this->Article->Comment->find('all', $initialOptions);
		$this->assertEquals($initialModels, $result);
	}

/**
 * testResetDeeperHasOneAssociations method
 *
 * @return void
 */
	public function testResetDeeperHasOneAssociations() {
		$this->Article->User->unbindModel([
			'hasMany' => ['ArticleFeatured', 'Comment']
		], false);
		$userHasOne = ['hasOne' => ['ArticleFeatured', 'Comment']];

		$this->Article->User->bindModel($userHasOne, false);
		$expected = $this->Article->User->hasOne;
		$this->Article->find('all');
		$this->assertEquals($expected, $this->Article->User->hasOne);

		$this->Article->User->bindModel($userHasOne, false);
		$expected = $this->Article->User->hasOne;
		$this->Article->find('all', [
			'contain' => [
				'User' => ['ArticleFeatured', 'Comment']
			]
		]);
		$this->assertEquals($expected, $this->Article->User->hasOne);

		$this->Article->User->bindModel($userHasOne, false);
		$expected = $this->Article->User->hasOne;
		$this->Article->find('all', [
			'contain' => [
				'User' => [
					'ArticleFeatured',
					'Comment' => ['fields' => ['created']]
				]
			]
		]);
		$this->assertEquals($expected, $this->Article->User->hasOne);

		$this->Article->User->bindModel($userHasOne, false);
		$expected = $this->Article->User->hasOne;
		$this->Article->find('all', [
			'contain' => [
				'User' => [
					'Comment' => ['fields' => ['created']]
				]
			]
		]);
		$this->assertEquals($expected, $this->Article->User->hasOne);

		$this->Article->User->bindModel($userHasOne, false);
		$expected = $this->Article->User->hasOne;
		$this->Article->find('all', [
			'contain' => [
				'User.ArticleFeatured' => [
					'conditions' => ['ArticleFeatured.published' => 'Y']
				],
				'User.Comment'
			]
		]);
		$this->assertEquals($expected, $this->Article->User->hasOne);
	}

/**
 * testResetMultipleHabtmAssociations method
 *
 * @return void
 */
	public function testResetMultipleHabtmAssociations() {
		$articleHabtm = [
			'hasAndBelongsToMany' => [
				'Tag' => [
					'className' => 'Tag',
					'joinTable' => 'articles_tags',
					'foreignKey' => 'article_id',
					'associationForeignKey' => 'tag_id'
				],
				'ShortTag' => [
					'className' => 'Tag',
					'joinTable' => 'articles_tags',
					'foreignKey' => 'article_id',
					'associationForeignKey' => 'tag_id',
					// LENGTH function mysql-only, using LIKE does almost the same
					'conditions' => "ShortTag.tag LIKE '???'"
				]
			]
		];

		$this->Article->resetBindings();
		$this->Article->bindModel($articleHabtm, false);
		$expected = $this->Article->hasAndBelongsToMany;
		$this->Article->find('all');
		$this->assertEquals($expected, $this->Article->hasAndBelongsToMany);

		$this->Article->resetBindings();
		$this->Article->bindModel($articleHabtm, false);
		$expected = $this->Article->hasAndBelongsToMany;
		$this->Article->find('all', ['contain' => 'Tag.tag']);
		$this->assertEquals($expected, $this->Article->hasAndBelongsToMany);

		$this->Article->resetBindings();
		$this->Article->bindModel($articleHabtm, false);
		$expected = $this->Article->hasAndBelongsToMany;
		$this->Article->find('all', ['contain' => 'Tag']);
		$this->assertEquals($expected, $this->Article->hasAndBelongsToMany);

		$this->Article->resetBindings();
		$this->Article->bindModel($articleHabtm, false);
		$expected = $this->Article->hasAndBelongsToMany;
		$this->Article->find('all', ['contain' => ['Tag' => ['fields' => [null]]]]);
		$this->assertEquals($expected, $this->Article->hasAndBelongsToMany);

		$this->Article->resetBindings();
		$this->Article->bindModel($articleHabtm, false);
		$expected = $this->Article->hasAndBelongsToMany;
		$this->Article->find('all', ['contain' => ['Tag' => ['fields' => ['Tag.tag']]]]);
		$this->assertEquals($expected, $this->Article->hasAndBelongsToMany);

		$this->Article->resetBindings();
		$this->Article->bindModel($articleHabtm, false);
		$expected = $this->Article->hasAndBelongsToMany;
		$this->Article->find('all', ['contain' => ['Tag' => ['fields' => ['Tag.tag', 'Tag.created']]]]);
		$this->assertEquals($expected, $this->Article->hasAndBelongsToMany);

		$this->Article->resetBindings();
		$this->Article->bindModel($articleHabtm, false);
		$expected = $this->Article->hasAndBelongsToMany;
		$this->Article->find('all', ['contain' => 'ShortTag.tag']);
		$this->assertEquals($expected, $this->Article->hasAndBelongsToMany);

		$this->Article->resetBindings();
		$this->Article->bindModel($articleHabtm, false);
		$expected = $this->Article->hasAndBelongsToMany;
		$this->Article->find('all', ['contain' => 'ShortTag']);
		$this->assertEquals($expected, $this->Article->hasAndBelongsToMany);

		$this->Article->resetBindings();
		$this->Article->bindModel($articleHabtm, false);
		$expected = $this->Article->hasAndBelongsToMany;
		$this->Article->find('all', ['contain' => ['ShortTag' => ['fields' => [null]]]]);
		$this->assertEquals($expected, $this->Article->hasAndBelongsToMany);

		$this->Article->resetBindings();
		$this->Article->bindModel($articleHabtm, false);
		$expected = $this->Article->hasAndBelongsToMany;
		$this->Article->find('all', ['contain' => ['ShortTag' => ['fields' => ['ShortTag.tag']]]]);
		$this->assertEquals($expected, $this->Article->hasAndBelongsToMany);

		$this->Article->resetBindings();
		$this->Article->bindModel($articleHabtm, false);
		$expected = $this->Article->hasAndBelongsToMany;
		$this->Article->find('all', ['contain' => ['ShortTag' => ['fields' => ['ShortTag.tag', 'ShortTag.created']]]]);
		$this->assertEquals($expected, $this->Article->hasAndBelongsToMany);
	}

/**
 * test that bindModel and unbindModel work with find() calls in between.
 *
 * @return void
 */
	public function testBindMultipleTimesWithFind() {
		$binding = [
			'hasOne' => [
				'ArticlesTag' => [
					'foreignKey' => false,
					'type' => 'INNER',
					'conditions' => [
						'ArticlesTag.article_id = Article.id'
					]
				],
				'Tag' => [
					'type' => 'INNER',
					'foreignKey' => false,
					'conditions' => [
						'ArticlesTag.tag_id = Tag.id'
					]
				]
			]
		];
		$this->Article->unbindModel(['hasAndBelongsToMany' => ['Tag']]);
		$this->Article->bindModel($binding);
		$result = $this->Article->find('all', ['limit' => 1, 'contain' => ['ArticlesTag', 'Tag']]);

		$this->Article->unbindModel(['hasAndBelongsToMany' => ['Tag']]);
		$this->Article->bindModel($binding);
		$result = $this->Article->find('all', ['limit' => 1, 'contain' => ['ArticlesTag', 'Tag']]);

		$associated = $this->Article->getAssociated();
		$this->assertEquals('hasAndBelongsToMany', $associated['Tag']);
		$this->assertFalse(isset($associated['ArticleTag']));
	}

/**
 * test that autoFields doesn't splice in fields from other databases.
 *
 * @return void
 */
	public function testAutoFieldsWithMultipleDatabases() {
		$config = new DATABASE_CONFIG();

		$this->skipIf(
			!isset($config->test) || !isset($config->test2),
			'Primary and secondary test databases not configured, ' .
			'skipping cross-database join tests. ' .
			' To run these tests, you must define $test and $test2 ' .
			'in your database configuration.'
		);

		$db = ConnectionManager::getDataSource('test2');
		$this->fixtureManager->loadSingle('User', $db);

		$this->Article->User->setDataSource('test2');

		$result = $this->Article->find('all', [
			'fields' => ['Article.title'],
			'contain' => ['User']
		]);
		$this->assertTrue(isset($result[0]['Article']));
		$this->assertTrue(isset($result[0]['User']));
	}

/**
 * test that autoFields doesn't splice in columns that aren't part of the join.
 *
 * @return void
 */
	public function testAutoFieldsWithRecursiveNegativeOne() {
		$this->Article->recursive = -1;
		$result = $this->Article->field('title', ['Article.title' => 'First Article']);
		$this->assertNoErrors();
		$this->assertEquals('First Article', $result, 'Field is wrong');
	}

/**
 * test that find(all) doesn't return incorrect values when mixed with containable.
 *
 * @return void
 */
	public function testFindAllReturn() {
		$result = $this->Article->find('all', [
			'conditions' => ['Article.id' => 999999999]
		]);
		$this->assertEmpty($result, 'Should be empty.');
	}

/**
 * testLazyLoad method
 *
 * @return void
 */
	public function testLazyLoad() {
		// Local set up
		$this->User = ClassRegistry::init('User');
		$this->User->bindModel([
			'hasMany' => ['Article', 'ArticleFeatured', 'Comment']
		], false);

		try {
			$this->User->find('first', [
				'contain' => 'Comment',
				'lazyLoad' => true
			]);
		} catch (Exception $e) {
			$exceptions = true;
		}
		$this->assertTrue(empty($exceptions));
	}

/**
 * _containments method
 *
 * @param Model $Model
 * @param array $contain
 * @return void
 */
	protected function _containments($Model, $contain = []) {
		if (!is_array($Model)) {
			$result = $Model->containments($contain);
			return $this->_containments($result['models']);
		}
		$result = $Model;
		foreach ($result as $i => $containment) {
			$result[$i] = array_diff_key($containment, ['instance' => true]);
		}
		return $result;
	}

/**
 * _assertBindings method
 *
 * @param Model $Model
 * @param array $expected
 * @return void
 */
	protected function _assertBindings(Model $Model, $expected = []) {
		$expected = array_merge([
			'belongsTo' => [],
			'hasOne' => [],
			'hasMany' => [],
			'hasAndBelongsToMany' => []
		], $expected);
		foreach ($expected as $binding => $expect) {
			$this->assertEquals($expect, array_keys($Model->$binding));
		}
	}
}
