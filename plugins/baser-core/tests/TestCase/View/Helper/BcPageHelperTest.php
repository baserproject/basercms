<?php
// TODO : コード確認要
return;

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

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
        'baser.View.Helper.BcPageHelper.PageBcPageHelper',
        'baser.Default.SearchIndex',
        'baser.Default.SiteConfig',
        'baser.Default.User',
        'baser.Default.UserGroup',
        'baser.Default.Favorite',
        'baser.Default.Permission',
        'baser.Default.ThemeConfig',
        'baser.View.Helper.BcContentsHelper.ContentBcContentsHelper',
        'baser.Default.Site',
    ];

    /**
     * View
     *
     * @var View
     */
    protected $_View;

    /**
     * __construct
     *
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->_View = new BcAppView();
        $this->_View->helpers = ['BcBaser', 'BcPage'];
        $this->_View->loadHelpers();
        $this->Page = ClassRegistry::init('Page');
        $this->BcContents = $this->_View->BcContents;
        $this->BcBaser = $this->_View->BcBaser;
        $this->BcPage = $this->_View->BcPage;
        $this->BcPage->BcBaser = $this->_View->BcBaser;
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        Router::reload();
        parent::tearDown();
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
     * ページカテゴリ間の次の記事へのリンクを取得する
     * @param string $url
     * @param string $title
     * @param array $options オプション（初期値 : array()）
     *    - `class` : CSSのクラス名（初期値 : 'next-link'）
     *    - `arrow` : 表示文字列（初期値 : ' ≫'）
     *    - `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
     * @param string $expected
     *
     * @dataProvider getNextLinkDataProvider
     */
    public function testGetNextLink($url, $title, $options, $expected)
    {
        $this->BcPage->request = $this->_getRequest($url);
        $result = $this->BcPage->getNextLink($title, $options);
        $this->assertEquals($expected, $result);
    }

    public function getNextLinkDataProvider()
    {
        return [
            ['/company', '', ['overCategory' => false], false], // PC
            ['/company', '次のページへ', ['overCategory' => false], false], // PC
            ['/about', '', ['overCategory' => true], '<a href="/icons" class="next-link">アイコンの使い方 ≫</a>'], // PC
            ['/about', '次のページへ', ['overCategory' => true], '<a href="/icons" class="next-link">次のページへ</a>'], // PC
            ['/s/about', '', ['overCategory' => false], '<a href="/s/icons" class="next-link">アイコンの使い方 ≫</a>'], // smartphone
            ['/s/about', '次のページへ', ['overCategory' => false], '<a href="/s/icons" class="next-link">次のページへ</a>'], // smartphone
            ['/s/sitemap', '', ['overCategory' => true], '<a href="/s/contact/" class="next-link">お問い合わせ ≫</a>'], // smartphone
            ['/s/sitemap', '次のページへ', ['overCategory' => true], '<a href="/s/contact/" class="next-link">次のページへ</a>'], // smartphone
        ];
    }
    /**
     * ページカテゴリ間の次の記事へのリンクを出力する
     *
     *    public function testNextLink($url, $title, $options, $expected) { }
     */

    /**
     * ページカテゴリ間の前の記事へのリンクを取得する
     * @param string $url
     * @param string $title
     * @param array $options オプション（初期値 : array()）
     *    - `class` : CSSのクラス名（初期値 : 'next-link'）
     *    - `arrow` : 表示文字列（初期値 : ' ≫'）
     *    - `overCategory` : 固定ページのカテゴリをまたいで次の記事のリンクを取得するかどうか（初期値 : false）
     * @param string $expected
     *
     * @dataProvider getPrevLinkDataProvider
     */
    public function testGetPrevLink($url, $title, $options, $expected)
    {
        $this->BcPage->request = $this->_getRequest($url);
        $result = $this->BcPage->getPrevLink($title, $options);
        $this->assertEquals($expected, $result);
    }

    public function getPrevLinkDataProvider()
    {
        return [
            ['/company', '', ['overCategory' => false], false], // PC
            ['/company', '前のページへ', ['overCategory' => false], false], // PC
            ['/about', '', ['overCategory' => true], '<a href="/" class="prev-link">≪ トップページ</a>'], // PC
            ['/about', '前のページへ', ['overCategory' => true], '<a href="/" class="prev-link">前のページへ</a>'], // PC
            ['/s/about', '', ['overCategory' => false], '<a href="/s/" class="prev-link">≪ トップページ</a>'], // smartphone
            ['/s/about', '前のページへ', ['overCategory' => false], '<a href="/s/" class="prev-link">前のページへ</a>'], // smartphone
            ['/s/sitemap', '', ['overCategory' => true], '<a href="/s/icons" class="prev-link">≪ アイコンの使い方</a>'], // smartphone
            ['/s/sitemap', '前のページへ', ['overCategory' => true], '<a href="/s/icons" class="prev-link">前のページへ</a>'], // smartphone
        ];
    }

    /**
     * ページカテゴリ間の前の記事へのリンクを出力する
     *
     * public function testPrevLink($url, $title, $options, $expected) { }
     */

    /**
     * ページリストを取得する
     *
     * @dataProvider getPageListDataProvider
     */
    public function testGetPageList($id, $expects)
    {
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
