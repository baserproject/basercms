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
    public $fixtures = [
        'plugin.BcBlog.Factory/BlogComments',
    ];

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
    public function test空チェック()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogComment->create([
            'BlogComment' => [
                'name' => '',
                'message' => '',
            ]
        ]);

        $this->assertFalse($this->BlogComment->validates());

        $this->assertArrayHasKey('name', $this->BlogComment->validationErrors);
        $this->assertEquals('お名前を入力してください。', current($this->BlogComment->validationErrors['name']));

        $this->assertArrayHasKey('message', $this->BlogComment->validationErrors);
        $this->assertEquals('コメントを入力してください。', current($this->BlogComment->validationErrors['message']));
    }

    public function test桁数チェック異常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogComment->create([
            'BlogComment' => [
                'name' => '123456789012345678901234567890123456789012345678901',
                'email' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456@example.com',
                'url' => 'http://example.com/1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
            ]
        ]);
        $this->assertFalse($this->BlogComment->validates());

        $this->assertArrayHasKey('name', $this->BlogComment->validationErrors);
        $this->assertEquals('お名前は50文字以内で入力してください。', current($this->BlogComment->validationErrors['name']));

        $this->assertArrayHasKey('email', $this->BlogComment->validationErrors);
        $this->assertEquals('Eメールは255文字以内で入力してください。', current($this->BlogComment->validationErrors['email']));


        $this->assertArrayHasKey('url', $this->BlogComment->validationErrors);
        $this->assertEquals('URLは255文字以内で入力してください。', current($this->BlogComment->validationErrors['url']));
    }

    public function test桁数チェック正常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogComment->create([
            'BlogComment' => [
                'name' => '12345678901234567890123456789012345678901234567890',
                'email' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678@example.com',
                'url' => 'http://example.com/123456789012345678901234567890123456789012345678901234567890123456789012345678901234567567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456'
            ]
        ]);
        $this->assertTrue($this->BlogComment->validates());
    }

    public function testその他異常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // 形式チェック
        $this->BlogComment->create([
            'BlogComment' => [
                'email' => 'hoge',
                'url' => 'hoge'
            ]
        ]);

        $this->assertFalse($this->BlogComment->validates());

        $this->assertArrayHasKey('email', $this->BlogComment->validationErrors);
        $this->assertEquals('Eメールの形式が不正です。', current($this->BlogComment->validationErrors['email']));

        $this->assertArrayHasKey('url', $this->BlogComment->validationErrors);
        $this->assertEquals('URLの形式が不正です。', current($this->BlogComment->validationErrors['url']));
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
