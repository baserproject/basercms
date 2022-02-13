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
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
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
        $this->Contents = $this->getTableLocator()->get('Contents');
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
        $this->loginAdmin($this->getRequest('/'));
        $data = [
            'cotnents' => '<p>test</p>',
            'draft' => '<p>test</p>',
            'page_template' => 'test',
            'content' => [
                "parent_id" => "1",
                "title" => "新しい フォルダー",
                "plugin" => 'BaserCore',
                "type" => "ContentFolder",
                "site_id" => "0",
                "alias_id" => "",
                "entity_id" => "",
            ],
        ];
        $Page = $this->PageService->create($data);
        $savedPage = $this->Pages->get($Page->id);
        $this->assertEquals('test', $savedPage->page_template);
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        // 条件無しで一覧を取得した場合
        $pages = $this->PageService->getIndex();
        $this->assertEquals(6, $pages->all()->count());
        $this->assertRegExp('/<section class="mainHeadline">/', $pages->first()->contents);
        // 条件無しで数を制限し、一覧を取得した場合
        $pages2 = $this->PageService->getIndex(['limit' => 3]);
        $this->assertEquals(3, $pages2->all()->count());
        // // 条件ありで一覧を取得した場合
        $pages = $this->PageService->getIndex(['contents' => 'mainHeadline']);
        $this->assertEquals(1, $pages->all()->count());
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        // containsScriptを通すためアドミンとしてログイン
        $this->loginAdmin($this->getRequest());
        $newPage = $this->PageService->get(2);
        $newPage->draft = "testUpdate";
        $oldPage = $this->PageService->get(2);
        $result = $this->PageService->update($oldPage, $newPage->toArray());
        $this->assertEquals("testUpdate", $result->draft);
    }

    /**
     * Test delete
     *
     * @return void
     */
    public function testDelete()
    {
        $content = $this->Contents->find()->where(['type' => 'Page'])->first();
        $this->assertTrue($this->PageService->delete($content->entity_id));
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->PageService->get($content->entity_id);
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->Contents->get($content->id);
    }


    /**
     * 本文にbaserが管理するタグを追加する
     *
     * @param string $id ID
     * @param string $contents 本文
     * @param string $title タイトル
     * @param string $description 説明文
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider addBaserPageTagDataProvider
     */
    public function testAddBaserPageTag($id, $contents, $title, $description, $expected, $message = null)
    {
        $result = $this->PageService->addBaserPageTag($id, $contents, $title, $description);
        $this->assertRegExp('/' . $expected . '/s', $result, $message);
    }

    public function addBaserPageTagDataProvider()
    {
        return [
            [1, 'contentdayo', 'titledayo', 'descriptiondayo',
                "<!-- BaserPageTagBegin -->.*setTitle\('titledayo'\).*setDescription\('descriptiondayo'\).*setPageEditLink\(1\).*contentdayo",
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

        /**
     * ページデータをコピーする
     *
     * @param array $postData 送信するデータ
     * @dataProvider copyDataProvider
     */
    public function testCopy($postData)
    {
        $this->loginAdmin($this->getRequest());
        $result = $this->PageService->copy($postData);
        $page = $this->PageService->get($result->id);
        $this->assertStringContainsString("_2", $page->content->name);
        $this->assertEquals("hoge1", $page->content->title);
        $this->assertEquals(10, $page->content->author_id);
    }

    public function copyDataProvider()
    {
        return [
            [
                [
                'contentId' =>4,
                'entityId' =>2,
                'parentId' =>1,
                'title' => 'hoge1',
                'authorId' => 10,
                'siteId' =>1
                ],
            ],
            // [
            //     [
            //     'contentId' =>3,
            //     'parentId' =>1,
            //     'title' => 'hoge',
            //     'authorId' =>1,
            //     'siteId' =>0
            //     ],
            // ],
        ];
    }
}
