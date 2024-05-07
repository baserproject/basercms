<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBlog\Test\TestCase\Model;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\Model\Table\BlogCommentsTable;

/**
 * Class BlogCommentsTableTest
 *
 * @property BlogCommentsTable $BlogCommentsTable
 */
class BlogCommentsTableTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogCommentsTable = $this->getTableLocator()->get('BcBlog.BlogComments');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogCommentsTable);
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertEquals('blog_comments', $this->BlogCommentsTable->getTable());
        $this->assertTrue($this->BlogCommentsTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->BlogCommentsTable->hasAssociation('BlogPosts'));
    }

    /*
	 * validate
	 */
    public function test_validationDefaultEmptyCheck()
    {
        $validator = $this->BlogCommentsTable->getValidator('default');
        $errors = $validator->validate([
            'name' => '',
            'message' => ''
        ]);
        $this->assertEquals('お名前を入力してください。', current($errors['name']));
        $this->assertEquals('コメントを入力してください。', current($errors['message']));
    }

    public function test_validationDefaultMaxLength()
    {
        $validator = $this->BlogCommentsTable->getValidator('default');
        $errors = $validator->validate([
            'name' => str_repeat('a', 51),
            'email' => str_repeat('a', 246) . '@gmail.com',
            'url' => 'http://example.com/' . str_repeat('a', 237)
        ]);

        $this->assertEquals('お名前は50文字以内で入力してください。', current($errors['name']));
        $this->assertEquals('Eメールは255文字以内で入力してください。', current($errors['email']));
        $this->assertEquals('URLは255文字以内で入力してください。', current($errors['url']));
    }

    public function test_validationDefaultNotError()
    {
        $validator = $this->BlogCommentsTable->getValidator('default');
        $errors = $validator->validate([
            'name' => str_repeat('a', 50),
            'email' => str_repeat('a', 245) . '@gmail.com',
            'url' => 'http://example.com/' . str_repeat('a', 236)
        ]);
        $this->assertCount(0, $errors);
    }

    public function test_validationDefaultOtherErrors()
    {
        $validator = $this->BlogCommentsTable->getValidator('default');
        $errors = $validator->validate([
            'name' => str_repeat('a', 5),
            'email' => str_repeat('a', 10),
            'url' => str_repeat('a', 10)
        ]);

        $this->assertEquals('Eメールの形式が不正です。', current($errors['email']));
        $this->assertEquals('URLの形式が不正です。', current($errors['url']));
    }

    /**
     * 初期値を取得する
     */
    public function testGetDefaultValue()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->assertEquals($this->BlogComment->getDefaultValue()['BlogComment']['name'], 'NO NAME');
    }

    /**
     * コメントを追加する
     */
    public function testAdd()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $data = ['BlogComment' => [
            'name' => 'test_name<',
            'email' => '-@example.com',
            'url' => 'http://example.com/-',
            'message' => 'test_message<',
        ]];
        $this->BlogComment->add($data, 1, 1, false);

        $result = $this->BlogComment->find('first', [
            'conditions' => ['id' => $this->BlogComment->getLastInsertID()]
        ]);

        $message = 'コメントを正しく追加できません';
        $this->assertEquals($result['BlogComment']['name'], 'test_name<', $message);
        $this->assertEquals($result['BlogComment']['email'], '-@example.com', $message);
        $this->assertEquals($result['BlogComment']['url'], 'http://example.com/-', $message);
        $this->assertEquals($result['BlogComment']['message'], 'test_message<', $message);
        $this->assertEquals($result['BlogComment']['no'], 2, $message);
        $this->assertEquals($result['BlogComment']['status'], 1, $message);
    }
}
