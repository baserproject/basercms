<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Test.Case.Model
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */
namespace BcBlog\Test\TestCase\Model;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Model\Table\BlogContentsTable;
use BcBlog\Test\Factory\BlogContentsFactory;

/**
 * Class BlogContentsTableTest
 *
 * @property BlogContentsTable $BlogContentsTable
 */
class BlogContentsTableTest extends BcTestCase
{

    public $fixtures = [
        'plugin.BcBlog.Factory/BlogContents',
    ];

    /**
     * Setup
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogContentsTable = new BlogContentsTable();
    }

    /**
     * Tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogContentsTable);
        parent::tearDown();
    }

    /*
	 * validate
	 */
    public function test空チェック()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogContent->create([
            'BlogContent' => [
                'list_direction' => ''
            ]
        ]);
        $this->assertFalse($this->BlogContent->validates());
        $this->assertArrayHasKey('list_direction', $this->BlogContent->validationErrors);
        $this->assertEquals('一覧に表示する順番を指定してください。', current($this->BlogContent->validationErrors['list_direction']));
    }

    public function test桁数チェック異常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogContent->create([
            'BlogContent' => [
                'layout' => '123456789012345678901',
                'template' => '123456789012345678901',
            ]
        ]);
        $this->assertFalse($this->BlogContent->validates());

        $this->assertArrayHasKey('layout', $this->BlogContent->validationErrors);
        $this->assertEquals('レイアウトテンプレート名は20文字以内で入力してください。', current($this->BlogContent->validationErrors['layout']));

        $this->assertArrayHasKey('template', $this->BlogContent->validationErrors);
        $this->assertEquals('レイアウトテンプレート名は20文字以内で入力してください。', current($this->BlogContent->validationErrors['template']));
    }

    public function test桁数チェック正常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogContent->create([
            'BlogContent' => [
                'layout' => '12345678901234567890',
                'template' => '12345678901234567890',
            ]
        ]);

        $this->assertTrue($this->BlogContent->validates());
    }

    public function testその他異常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // 半角チェック
        $this->BlogContent->create([
            'BlogContent' => [
                'template' => 'テスト',
                'list_count' => 'テスト',
                'eye_catch_size' => BcUtil::serialize([
                    'thumb_width' => 300,
                    'thumb_height' => 200,
                    'mobile_thumb_width' => 30,
                    'mobile_thumb_height' => 20
                ])
            ]
        ]);

        $this->assertFalse($this->BlogContent->validates());

        $this->assertArrayHasKey('template', $this->BlogContent->validationErrors);
        $this->assertEquals('コンテンツテンプレート名は半角で入力してください。', current($this->BlogContent->validationErrors['template']));

        $this->assertArrayHasKey('list_count', $this->BlogContent->validationErrors);
        $this->assertEquals('一覧表示件数は半角で入力してください。', current($this->BlogContent->validationErrors['list_count']));

        // eye_catch_sizeチェック
        $this->BlogContent->create([
            'BlogContent' => [
                'eye_catch_size' => BcUtil::serialize([
                    'thumb_width' => 0,
                    'thumb_height' => 0,
                    'mobile_thumb_width' => 0,
                    'mobile_thumb_height' => 0
                ])
            ]
        ]);

        $this->assertFalse($this->BlogContent->validates());
        $this->assertArrayHasKey('eye_catch_size', $this->BlogContent->validationErrors);
        $this->assertEquals('アイキャッチ画像のサイズが不正です。', current($this->BlogContent->validationErrors['eye_catch_size']));
    }

    public function testその他正常系()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        // eye_catch_sizeチェック
        $data = [
            'thumb_width' => 1,
            'thumb_height' => 1,
            'mobile_thumb_width' => 1,
            'mobile_thumb_height' => 1,
        ];

        $this->BlogContent->create([
            'BlogContent' => [
                'eye_catch_size' => BcUtil::serialize($data)
            ]
        ]);

        $this->assertTrue($this->BlogContent->validates());
        $this->assertArrayNotHasKey('eye_catch_size', $this->BlogContent->validationErrors);
    }

    /**
     * 英数チェック
     *
     * @dataProvider alphaNumericDataProvider
     */
    public function testAlphaNumeric($key, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->assertEquals($this->BlogContent->alphaNumeric($key), $expected);
    }

    public function alphaNumericDataProvider()
    {
        return [
            [['key' => 'abc'], true],
            [['key' => 'ほげ'], false],
            [['key' => '01234'], true],
            [['key' => '０１２３４'], false],
            [['key' => '$'], false],
            [['key' => '<>'], false],
            [['key' => '?'], false],
            [['key' => '^'], false],
            [['key' => '-'], false]
        ];
    }

    /**
     * コントロールソースを取得する
     *
     * @dataProvider getControlSourceDataProvider
     */
    public function testGetControlSource($field, $expected)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $result = $this->BlogContent->getControlSource($field);
        $this->assertEquals($result, $expected);
    }

    public function getControlSourceDataProvider()
    {
        return [
            [null, false],
            ['', false],
            ['hoge', false],
            ['id', ['1' => '新着情報']],
        ];
    }

    /**
     * afterSave
     *
     * @param mixed $id
     * @param int $exclude_search
     * @dataProvider afterSaveDataProvider
     */
    public function testAfterSave($id, $exclude_search)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->_loginAdmin();
        $this->BlogContent->create([
            'BlogContent' => [
                'id' => $id,
                'description' => 'test-description',
            ],
            'Content' => [
                'id' => $id,
                'name' => 'test-name',
                'parent_id' => 1,
                'title' => 'test-title',
                'exclude_search' => $exclude_search,
                'status' => 1,
                'site_id' => 1,
                'entity_id' => $id
            ]
        ]);

        $this->BlogContent->save();

        if (!$exclude_search) {
            $BlogContent = ClassRegistry::init('BcBlog.BlogContent');
            $result = $BlogContent->find('count', [
                'conditions' => ['Content.name' => 'test-name'],
            ]);
            $this->assertEquals($result, 1, '検索用テーブルへ登録できません');
            unset($BlogContent);
        } else {
            $SearchIndex = ClassRegistry::init('SearchIndex');
            $result = $SearchIndex->find('count', [
                'conditions' => ['SearchIndex.model' => 'BlogContent'],
            ]);
            $this->assertEquals($result, 0, '検索用テーブルから削除できません');
            unset($SearchIndex);
        }
    }

    public function afterSaveDataProvider()
    {
        return [
            ['', 0],
            [1, 1],
        ];
    }

    /**
     * beforeDelete
     */
    public function testBeforeDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 検索用データを生成する
     */
    public function testCreateSearchIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $data = [
            'name' => 'test-name',
            'title' => 'test-title',
            'description' => 'test-description',
        ];
        $result = $this->BlogContent->createContent($data);

        $expected = [
            'Content' => [
                'type' => 'ブログ',
                'model_id' => false,
                'category' => '',
                'title' => 'test-title',
                'detail' => 'test-description',
                'url' => '/test-name/index',
                'status' => true
            ]
        ];
        $this->assertEquals($expected, $result, '正しく検索用データを生成でません');
    }

    /**
     * ブログコンテンツデータをコピーする
     */
    public function testCopy()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->_loginAdmin();
        $this->BlogContent->copy(1, 1, 'hoge1', 1, 0);
        $result = $this->BlogContent->find('first', [
            'conditions' => ['BlogContent.id' => $this->BlogContent->getLastInsertID()]
        ]);
        $this->assertEquals($result['Content']['title'], 'hoge1');
        $this->BlogContent->copy(1, 1, 'test-title', 1, 0);
        $result = $this->BlogContent->find('first', [
            'conditions' => ['BlogContent.id' => $this->BlogContent->getLastInsertID()]
        ]);
        $this->assertEquals($result['Content']['title'], 'test-title');
    }

    /**
     * フォームの初期値を取得する
     */
    public function testGetDefaultValue()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $data = $this->BlogContent->getDefaultValue();
        $this->assertEquals($data['BlogContent']['comment_use'], true);
        $this->assertEquals($data['BlogContent']['comment_approve'], false);
        $this->assertEquals($data['BlogContent']['layout'], 'default');
        $this->assertEquals($data['BlogContent']['template'], 'default');
        $this->assertEquals($data['BlogContent']['list_count'], 10);
        $this->assertEquals($data['BlogContent']['list_direction'], 'DESC');
        $this->assertEquals($data['BlogContent']['feed_count'], 10);
        $this->assertEquals($data['BlogContent']['auth_captcha'], 1);
        $this->assertEquals($data['BlogContent']['tag_use'], false);
        $this->assertEquals($data['BlogContent']['status'], false);
        $this->assertEquals($data['BlogContent']['eye_catch_size_thumb_width'], 600);
        $this->assertEquals($data['BlogContent']['eye_catch_size_thumb_height'], 600);
        $this->assertEquals($data['BlogContent']['eye_catch_size_mobile_thumb_width'], 150);
        $this->assertEquals($data['BlogContent']['eye_catch_size_mobile_thumb_height'], 150);
        $this->assertEquals($data['BlogContent']['use_content'], true);
    }

    /**
     * アイキャッチサイズフィールドの値をDB用に変換する
     */
    public function test_deconstructEyeCatchSize()
    {
        BlogContentsFactory::make([
            'id' => 1,
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
        ])->persist();
        $data = $this->BlogContentsTable->deconstructEyeCatchSize($this->BlogContentsTable->get(1));
        $this->assertEquals(
            $data['eye_catch_size'],
            'YTo0OntzOjExOiJ0aHVtYl93aWR0aCI7TjtzOjEyOiJ0aHVtYl9oZWlnaHQiO047czoxODoibW9iaWxlX3RodW1iX3dpZHRoIjtOO3M6MTk6Im1vYmlsZV90aHVtYl9oZWlnaHQiO047fQ=='
        );
    }

    /**
     * アイキャッチサイズフィールドの値をフォーム用に変換する
     */
    public function testConstructEyeCatchSize()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $data = $this->BlogContent->constructEyeCatchSize($this->BlogContent->deconstructEyeCatchSize($this->BlogContent->getDefaultValue()));
        $this->assertEquals($data['BlogContent']['eye_catch_size_thumb_width'], 600);
        $this->assertEquals($data['BlogContent']['eye_catch_size_thumb_height'], 600);
        $this->assertEquals($data['BlogContent']['eye_catch_size_mobile_thumb_width'], 150);
        $this->assertEquals($data['BlogContent']['eye_catch_size_mobile_thumb_height'], 150);
    }
}
