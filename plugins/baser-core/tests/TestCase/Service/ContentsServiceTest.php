<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\ContentsService;

/**
 * BaserCore\Model\Table\ContentsTable Test Case
 *
 * @property ContentsService $ContentsService
 */
class ContentsServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var ContentsService
     */
    public $Contents;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
    ];

        /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentsService = new ContentsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentsService);
        parent::tearDown();
    }

    /**
     * testGetTreeIndex
     *
     * @return void
     */
    public function testGetTreeIndex(): void
    {
        $site_id = 0;
        $result = $this->ContentsService->getTreeIndex($site_id);
        $this->assertEquals("baserCMSサンプル", $result->first()->title);
    }

    /**
     * testGetTableConditions
     *
     * @return void
     */
    public function testGetTableConditions()
    {

        $request = $this->getRequest()->withQueryParams([
            'site_id' => 0,
            'open' => '1',
            'folder_id' => '6',
            'name' => 'テスト',
            'type' => 'ContentFolder',
            'self_status' => '1',
            'author_id' => '',
        ]);
        $result = $this->ContentsService->getTableConditions($request->getQueryParams());
        $this->assertEquals([
            'OR' => [
            'name LIKE' => '%テスト%',
            'title LIKE' => '%テスト%',
            ],
            'rght <' => (int) 15,
            'lft >' => (int) 8,
            'self_status' => '1',
            'type' => 'ContentFolder',
            'site_id' => 0
            ], $result);
    }

    /**
     * testgetIndex
     *
     * @return void
     * @dataProvider getIndexDataProvider
     */
    public function testgetIndex($conditions, $expected): void
    {
        $result = $this->ContentsService->getIndex($conditions);
        $this->assertEquals($expected, $result->count());
    }
    public function getIndexDataProvider()
    {
        return [
            [[
                'site_id' => 0,
                'open' => '1',
                'folder_id' => '',
                'name' => '',
                'type' => '',
                'self_status' => '1',
                'author_id' => '',
            ], 10],
            [[
                'site_id' => 0,
                'open' => '1',
                'folder_id' => '',
                'name' => '',
                'type' => 'ContentFolder',
                'self_status' => '1',
                'author_id' => '',
            ], 2],
        ];
    }
    /**
     * testGetTrashIndex
     *
     * @return void
     */
    public function testGetTrashIndex(): void
    {
        $result = $this->ContentsService->getTrashIndex();
        $this->assertTrue($result->isEmpty());
    }

    /**
     * コンテンツフォルダーのリストを取得
     * コンボボックス用
     */
    public function testGetContentFolderList()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $siteId = 1;

        $this->ContentsService->getContentFolderList();
    }

    /**
     * ツリー構造のデータを コンボボックスのデータ用に変換する
     */
    public function testConvertTreeList()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
