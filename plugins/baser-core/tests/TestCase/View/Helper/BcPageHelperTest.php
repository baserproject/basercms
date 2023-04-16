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

namespace BaserCore\Test\TestCase\View\Helper;
use BaserCore\View\AppView;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BcPageHelper;

/**
 * BcPage helper library.
 */
class BcPageHelperTest extends BcTestCase
{

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        // 'baser.View.Helper.BcPageHelper.PageBcPageHelper',
        // 'baser.Default.Favorite',
        // 'baser.Default.ThemeConfig',
        // 'baser.View.Helper.BcContentsHelper.ContentBcContentsHelper',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
    ];

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Pages = $this->getTableLocator()->get('BaserCore.Pages');
        $this->BcPage = new BcPageHelper(new AppView());
        // $this->AppView = new AppView();
        // $this->BcContents = $this->AppView->BcContents;
        // $this->BcBaser = $this->AppView->BcBaser;
        // $this->BcPage = $this->AppView->BcPage;
        // $this->BcPage->BcBaser = $this->AppView->BcBaser;
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Pages, $this->BcPage);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcPage->ContentsService);
    }

    /**
     * テスト用に固定ページのデータを取得する
     *
     * @return array 固定ページのデータ
     */
    public function getPageData($conditions = [], $fields = [])
    {
        $options = [
            'conditions' => $conditions,
            'fields' => $fields,
            'recursive' => 0
        ];
        $pages = $this->Page->find('all', $options);
        if (empty($pages)) {
            return false;
        } else {
            return $pages[0];
        }
    }

    /**
     * ページ機能用URLを取得する
     *
     * @param array $pageId 固定ページID
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getUrlDataProvider
     */
    public function testGetUrl($pageId, $expected, $message = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // 固定ページのデータ取得
        $conditions = ['Page.id' => $pageId];
        $fields = ['Content.url'];
        $page = $this->getPageData($conditions, $fields);

        $result = $this->BcPage->getUrl($page);
        $this->assertEquals($expected, $result, $message);
    }

    public function getUrlDataProvider()
    {
        return [
            [1, '/index'],
            [2, '/about'],
            [3, '/service/index'],
            [4, '/icons'],
            [5, '/sitemap'],
            [6, '/m/index'],
        ];
    }

    /**
     * 公開状態を取得する
     *
     * @param boolean $status 公開状態
     * @param mixed $begin 公開開始日時
     * @param mixed $end 公開終了日時
     * @param string $expected 期待値
     * @param string $message テスト失敗時、表示するメッセージ
     * @dataProvider allowPublishDataProvider
     */
    public function testAllowPublish($status, $begin, $end, $expected, $message)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $data = [
            'Page' => [
                'status' => $status,
                'publish_begin' => $begin,
                'publish_end' => $end,
            ]
        ];
        $result = $this->BcPage->allowPublish($data);
        $this->assertEquals($expected, $result, $message);
    }

    public function allowPublishDataProvider()
    {
        return [
            [true, 0, 0, true, 'statusの値がそのままかえってきません'],
            [true, '2200-1-1', 0, false, '公開開始日時の前に公開されています'],
            [true, 0, '1999-1-1', false, '公開終了日時の後に公開されています'],
            [true, '2199-1-1', '2200-1-1', false, '公開開始日時の前に公開されています'],
            [true, '1999-1-1', '2000-1-1', false, '公開開始日時の後に公開されています'],
            [false, '1999-1-1', 0, false, '非公開になっていません'],
        ];
    }

    /**
     * ページリストを取得する
     *
     * @dataProvider getPageListDataProvider
     */
    public function testGetPageList($id, $expects)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $result = $this->BcPage->GetPageList($id);
        $result = Hash::extract($result, '{n}.Content.type');
        $this->assertEquals($expects, $result);
    }

    public function getPageListDataProvider()
    {
        return [
            [1, ['Page', 'Page', 'Page', 'Page', 'ContentFolder']],    // トップフォルダ
            [21, ['Page', 'Page', 'Page', 'ContentFolder']],    // 下層フォルダ
            [4, []]    // ターゲットがフォルダでない
        ];
    }

    public function test__construct()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
