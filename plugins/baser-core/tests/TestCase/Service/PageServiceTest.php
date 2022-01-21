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

use BaserCore\Model\Table\PagesTable;
use BaserCore\Service\PageService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PageServiceTest
 * @property PageService $PageService
 * @property PagesTable $Pages
 */
class PageServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.ContentFolders',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PageService = new PageService();
        $this->Pages = $this->getTableLocator()->get('Pages');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PageService);
        unset($this->Pages);
        parent::tearDown();
    }

    /**
     * Test get
     *
     * @return void
     */
    public function testGet()
    {
        $page = $this->PageService->get(2);
        $this->assertRegExp('/<section class="mainHeadline">/', $page->contents);
        $this->expectExceptionMessage('Record not found in table "pages"');
        $page = $this->PageService->getTrash(1);
    }
    /**
     * Test getTrash
     *
     * @return void
     */
    public function testGetTrash()
    {
        $page = $this->PageService->getTrash(3);
        $this->assertRegExp('/<div class="articleArea bgGray" id="service">/', $page->contents);
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->expectExceptionMessage('Record not found in table "contents"');
        $this->PageService->getTrash(2);
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->getRequest();
        $Page = $this->PageService->create('Test Message');
        $savedPage = $this->Pages->get($Page->id);
        $this->assertEquals('Test Message', $savedPage->message);
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $request = $this->getRequest('/');
        $Pages = $this->PageService->getIndex($request->getQueryParams());
        $this->assertEquals('Pages test message1', $Pages->first()->message);

        $request = $this->getRequest('/?message=message2');
        $Pages = $this->PageService->getIndex($request->getQueryParams());
        $this->assertEquals('Pages test message2', $Pages->first()->message);

        $request = $this->getRequest('/?user_id=3');
        $Pages = $this->PageService->getIndex($request->getQueryParams());
        $this->assertEquals('Pages test message3', $Pages->first()->message);
    }


    /**
     * 本文にbaserが管理するタグを追加する
     *
     * @param string $id ID
     * @param string $contents 本文
     * @param string $title タイトル
     * @param string $description 説明文
     * @param string $code コード
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider addBaserPageTagDataProvider
     */
    public function testAddBaserPageTag($id, $contents, $title, $description, $code, $expected, $message = null)
    {
        $result = $this->PageService->addBaserPageTag($id, $contents, $title, $description, $code);
        $this->assertRegExp('/' . $expected . '/s', $result, $message);
    }

    public function addBaserPageTagDataProvider()
    {
        return [
            [1, 'contentdayo', 'titledayo', 'descriptiondayo', 'codedayo',
                "<!-- BaserPageTagBegin -->.*setTitle\('titledayo'\).*setDescription\('descriptiondayo'\).*setPageEditLink\(1\).*codedayo.*contentdayo",
                '本文にbaserが管理するタグを追加できません'],
        ];
    }

    /**
     * 固定ページテンプレートリストを取得する
     *
     * @param int $contetnId
     * @param mixed $plugin
     * @param $expected
     * @dataProvider getPageTemplateListDataProvider
     */
    public function testGetPageTemplateList($contetnId, $plugin, $expected)
    {
        // BC frontに変更
        $result = $this->PageService->getPageTemplateList($contetnId, $plugin);
        $this->assertEquals($expected, $result);
    }

    public function getPageTemplateListDataProvider()
    {
        return [
            [1, 'BcFront', ['default' => 'default']],
            [4, 'BcFront', ['' => '親フォルダの設定に従う（default）']],
            [4, ['BcFront', 'BaserCore'], ['' => '親フォルダの設定に従う（default）']],
            [11, ['BcFront', 'BcAdminThrid'], ['' => '親フォルダの設定に従う（サービスページ）', 'default' => 'default']]
        ];
    }
}
