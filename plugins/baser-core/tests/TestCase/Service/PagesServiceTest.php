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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Model\Entity\Content;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Service\PagesService;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\ContentFolderFactory;
use BaserCore\Test\Factory\PageFactory;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PagesServiceTest
 * @property PagesService $PagesService
 * @property PagesTable $Pages
 */
class PagesServiceTest extends BcTestCase
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
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PagesService = new PagesService();
        $this->Pages = $this->getTableLocator()->get('BaserCore.Pages');
        $this->Contents = $this->getTableLocator()->get('BaserCore.Contents');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PagesService);
        unset($this->Pages);
        parent::tearDown();
    }

    /**
     * test construct()
     * @return void
     */
    public function testConstruct()
    {
        $this->assertTrue(isset($this->PagesService->Pages));
        $this->assertTrue(isset($this->PagesService->Contents));
        $this->assertTrue(isset($this->PagesService->Users));
    }

    /**
     * Test get
     *
     * @return void
     */
    public function testGet()
    {
        $page = $this->PagesService->get(2);
        $this->assertMatchesRegularExpression('/<section class="mainHeadline">/', $page->contents);
        $this->expectExceptionMessage('Record not found in table "pages"');
        $this->PagesService->getTrash(1);
    }

    /**
     * Test getTrash
     *
     * @return void
     */
    public function testGetTrash()
    {
        $page = $this->PagesService->getTrash(3);
        $this->assertMatchesRegularExpression('/<div class="articleArea bgGray" id="service">/', $page->contents);
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->expectExceptionMessage('Record not found in table "pages"');
        $this->PagesService->getTrash(2);
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
                "site_id" => "1",
                "alias_id" => "",
                "entity_id" => "",
            ],
        ];
        $Page = $this->PagesService->create($data);
        $savedPage = $this->Pages->get($Page->id);
        $this->assertEquals('test', $savedPage->page_template);
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        // 条件無しで一覧を取得した場合
        $pages = $this->PagesService->getIndex();
        $this->assertEquals(8, $pages->all()->count());
        $this->assertMatchesRegularExpression('/<section class="mainHeadline">/', $pages->first()->contents);
        // 条件無しで数を制限し、一覧を取得した場合
        $pages2 = $this->PagesService->getIndex(['limit' => 3]);
        $this->assertEquals(3, $pages2->all()->count());
        // // 条件ありで一覧を取得した場合
        $pages = $this->PagesService->getIndex(['contents' => 'mainHeadline']);
        $this->assertEquals(1, $pages->all()->count());
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        // containsScriptを通すためアドミンとしてログイン
        $this->loginAdmin($this->getRequest());
        $newPage = $this->PagesService->get(2);
        $newPage->draft = "testUpdate";
        $oldPage = $this->PagesService->get(2);
        $result = $this->PagesService->update($oldPage, $newPage->toArray());
        $this->assertEquals("testUpdate", $result->draft);
    }

    /**
     * Test delete
     *
     * @return void
     */
    public function testDelete()
    {
        /* @var Content $content */
        $content = $this->Contents->find()->where(['type' => 'Page'])->first();
        $this->assertTrue($this->PagesService->delete($content->entity_id));
        $this->assertEquals(0, $this->Pages->find()->where(['Pages.id' => $content->entity_id])->count());
        $this->assertEquals(0, $this->Contents->find()
            ->where(['Contents.id' => $content->id])
            ->applyOptions(['withDeleted'])
            ->count()
        );
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
        $result = $this->PagesService->getPageTemplateList($contetnId, $plugin);
        $this->assertEquals($expected, $result);
    }

    public function getPageTemplateListDataProvider()
    {
        return [
            [1, 'BcFront', ['default' => 'default']],
            [4, 'BcFront', ['' => '親フォルダの設定に従う（default）']],
            [4, ['BcFront', 'BaserCore'], ['' => '親フォルダの設定に従う（default）']],
            [11, ['BcFront', 'BcAdminThird'], ['' => '親フォルダの設定に従う（サービスページ）', 'default' => 'default']]
        ];
    }

    /**
     * コントロールソースを取得する
     *
     * MEMO: $optionのテストについては、UserTest でテスト済み
     *
     * @param string $field フィールド名
     * @param array $options
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getControlSourceDataProvider
     */
    public function testGetControlSource($field, $expected, $message = null)
    {
        $result = $this->PagesService->getControlSource($field);
        $this->assertEquals($expected, $result, $message);
    }

    public function getControlSourceDataProvider()
    {
        return [
            ['author_id', [1 => 'ニックネーム1', 2 => 'ニックネーム2', 3 => 'ニックネーム3'], 'コントロールソースを取得できません'],
        ];
    }

    /**
     * test getEditLink
     */
    public function testGetEditLink()
    {
        $request = $this->getRequest('/about');
        $this->assertEquals([
            'prefix' => 'Admin',
            'controller' => 'Pages',
            'action' =>
                'edit', 16
        ], $this->PagesService->getEditLink($request));
        $request = $this->getRequest('/hoge');
        $this->assertEmpty($this->PagesService->getEditLink($request));
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $this->assertEquals([], $this->PagesService->getNew()->toArray());
    }

    public function test_getList()
    {
        $this->assertContains('会社案内', $this->PagesService->getList());
    }

    /**
     * test getPageTemplate
     */
    public function test_getPageTemplate()
    {
        ContentFactory::make([
            'id' => 100,
            'url' => '/',
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'site_id' => 1,
            'parent_id' => null,
            'lft' => 101,
            'rght' => 106,
            'entity_id' => 100,
            'site_root' => true,
            'status' => true
        ])->persist();
        ContentFactory::make([
            'id' => 101,
            'url' => '/parent/',
            'plugin' => 'BaserCore',
            'type' => 'Page',
            'site_id' => 1,
            'parent_id' => 100,
            'lft' => 102,
            'rght' => 103,
            'entity_id' => 101,
            'site_root' => false,
            'status' => true
        ])->persist();
        ContentFactory::make([
            'id' => 102,
            'url' => '/parent/child',
            'plugin' => 'BaserCore',
            'type' => 'Page',
            'site_id' => 1,
            'parent_id' => 101,
            'lft' => 104,
            'rght' => 105,
            'entity_id' => 102,
            'site_root' => false,
            'status' => true
        ])->persist();
        ContentFolderFactory::make(['id' => 100, 'page_template' => 'default 1'])->persist();
        PageFactory::make(['id' => 101, 'page_template' => 'test 1'])->persist();
        PageFactory::make(['id' => 102])->persist();

        //対象に値が入っている場合
        $rs = $this->PagesService->getPageTemplate($this->PagesService->get(101));
        $this->assertEquals('test 1', $rs);

        //親となる ContentFolder を作成

        $rs = $this->PagesService->getPageTemplate($this->PagesService->get(102));
        $this->assertEquals('default 1', $rs);
    }
}
