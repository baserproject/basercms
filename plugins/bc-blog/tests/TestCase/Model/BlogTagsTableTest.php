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
use BcBlog\Model\Table\BlogTagsTable;

/**
 * Class BlogTagsTableTest
 *
 * @property BlogTagsTable $BlogTagsTable
 */
class BlogTagsTableTest extends BcTestCase
{

    public $fixtures = [
        'plugin.BcBlog.Factory/BlogTags',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogTagsTable = $this->getTableLocator()->get('BcBlog.BlogTags');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogTagsTable);
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertEquals('blog_tags', $this->BlogTagsTable->getTable());
        $this->assertTrue($this->BlogTagsTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->BlogTagsTable->hasAssociation('BlogPosts'));
    }

    /*
	 * validate
	 */
    public function test空チェック()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogTag->create([
            'BlogTag' => [
                'name' => ''
            ]
        ]);

        $this->assertFalse($this->BlogTag->validates());

        $this->assertArrayHasKey('name', $this->BlogTag->validationErrors);
        $this->assertEquals('ブログタグを入力してください。', current($this->BlogTag->validationErrors['name']));
    }

    public function test重複チェック()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogTag->create([
            'BlogTag' => [
                'name' => 'タグ１'
            ]
        ]);

        $this->assertFalse($this->BlogTag->validates());

        $this->assertArrayHasKey('name', $this->BlogTag->validationErrors);
        $this->assertEquals('既に登録のあるタグです。', current($this->BlogTag->validationErrors['name']));
    }

    public function test正常チェック()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $this->BlogTag->create([
            'BlogTag' => [
                'name' => 'test'
            ]
        ]);

        $this->assertTrue($this->BlogTag->validates());
    }

    /**
     * ブログタグリスト取得
     *
     * @param string $type
     * @param mixed $expected
     * @param array $options
     * @dataProvider findCustomParamsDataProvider
     */
    public function testFindCustomParams($type, $expected, $options = [])
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $result = $this->BlogTag->find('customParams', $options);
        if ($type == 'count') {
            if ($result) {
                $result = count($result);
            }
        } elseif ($type == 'id') {
            if ($result) {
                $result = Hash::extract($result, '{n}.BlogTag.id');
            }
        }
        $this->assertEquals($expected, $result);
    }

    public function findCustomParamsDataProvider()
    {
        return [
            ['count', 5, []],
            ['count', 2, ['siteId' => 0]],                                        // サイト指定
            ['count', 3, ['siteId' => [0, 2]]],                                    // サイト指定（復数）
            ['count', 2, ['contentId' => 2]],                                    // ブログコンテンツID指定
            ['count', 3, ['contentId' => [2, 3]]],                                // ブログコンテンツID指定（復数）
            ['count', 2, ['contentUrl' => ['/blog1/', '/blog2/']]],                // コンテンツURL指定
            ['count', 3, ['contentUrl' => ['/blog1/', '/blog2/', '/s/blog3/']]], // コンテンツURL指定（復数）
            ['id', [5, 4, 3, 2, 1], ['sort' => 'id', 'direction' => 'DESC']],    // 並び替え指定
        ];
    }

    /**
     * 指定した名称のブログタグ情報を取得する
     * @dataProvider getByNameDataProvider
     * @param string $name
     * @param bool $expects
     */
    public function testGetByName($name, $expects)
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $result = $this->BlogTag->getByName($name);
        $this->assertEquals($expects, (bool)$result);
    }

    public function getByNameDataProvider()
    {
        return [
            ['タグ１', true],
            ['タグ２', true],
            ['新製品', false],
            ['%90V%90%BB%95i', false], // 文字列 新製品 をURLエンコード化
            ['hoge', false],
        ];
    }
}
