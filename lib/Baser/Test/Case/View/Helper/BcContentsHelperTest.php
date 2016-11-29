<?php

/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */
App::uses('BcAppView', 'View');
App::uses('BcContentsHelper', 'View/Helper');

/**
 * BcPage helper library.
 *
 * @package Baser.Test.Case
 * @property BcContentsHelper $BcContents
 */
class BcContentsHelperTest extends BaserTestCase {

    /**
     * Fixtures
     * @var array 
     */
    public $fixtures = array(
        'baser.View.Helper.BcContentsHelper.ContentBcContentsHelper',
        'baser.Default.SiteConfig',
        'baser.Default.Site',
        'baser.Default.User',
        'baser.Default.UserGroup',
        'baser.Default.Favorite',
        'baser.Default.Permission',
        'baser.Default.ThemeConfig',
    );

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
    public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);
    }

    /**
     * setUp
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();
        $this->_View = new BcAppView();
        $this->_View->helpers = array('BcContents');
        $this->_View->loadHelpers();
        $this->BcContents = $this->_View->BcContents;
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown() {
        Router::reload();
        parent::tearDown();
    }

    /**
     * ページリストを取得する
     * 
     * @param int $pageCategoryId カテゴリID
     * @param int $level 関連データの階層	
     * @param int $expectedCount 期待値
     * @param string $expectedTitle  
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider getPageListDataProvider
     */
    public function testGetTree($id, $level, $expectedCount, $expectedTitle, $message = null) {
        $result = $this->BcContents->getTree($id, $level);
        $resultTitle = null;
        $resultCount = null;
        switch ($level) {
            case 1:
                if (!empty($result[0]['Content']['title'])) {
                    $resultTitle = $result[0]['Content']['title'];
                    $resultCount = count($result);
                }
                break;
            case 2:
                if ($result) {
                    foreach ($result as $data) {
                        if ($data['children']) {
                            $resultTitle = $data['children'][0]['Content']['title'];
                            $resultCount = count($data['children']);
                        }
                    }
                }
                break;
            case 3:
                if ($result) {
                    foreach ($result as $data) {
                        if ($data['children']) {
                            foreach ($data['children'] as $data2) {
                                if ($data2['children']) {
                                    $resultTitle = $data2['children'][0]['Content']['title'];
                                    $resultCount = count($data2['children']);
                                }
                            }
                        }
                    }
                }
                break;
        }
        $this->assertEquals($expectedCount, $resultCount, 'カウントエラー：' . $message);
        $this->assertEquals($expectedTitle, $resultTitle, 'タイトルエラー：' . $message);
    }

    public function getPageListDataProvider() {
        return array(
            // PC版
            array(1, 1, 7, 'トップページ', 'PC版１階層目のデータが正常に取得できません'),
            array(1, 2, 4, 'サービス', 'PC版２階層目のデータが正常に取得できません'),
            array(1, 3, 1, 'サブサービス１', 'PC版３階層目のデータが正常に取得できません'),
            // ケータイ
            array(2, 1, 3, 'トップページ', 'ケータイ版１階層目のデータが正常に取得できません'),
            // スマホ
            array(3, 1, 7, 'トップページ', 'スマホ版１階層目のデータが正常に取得できません'),
            array(3, 2, 1, 'サービス１', 'スマホ版２階層目のデータが正常に取得できません')
        );
    }

    /**
     * @dataProvider isSiteRelatedDataProvider
     */
    public function testIsSiteRelated($expect, $data) {
        $result = $this->BcContents->isSiteRelated($data);
        $this->assertEquals($expect, $result);
    }

    public function isSiteRelatedDataProvider() {
        return [
            [true, ['Site' => ['relate_main_site' => true], 'Content' => ['main_site_content_id' => 1, 'alias_id' => 1, 'type' => 'BlogContent']]],
            [false, ['Site' => ['relate_main_site' => false], 'Content' => ['main_site_content_id' => 1, 'alias_id' => 1, 'type' => 'BlogContent']]],
            [false, ['Site' => ['relate_main_site' => true], 'Content' => ['main_site_content_id' => null, 'alias_id' => 1, 'type' => 'BlogContent']]],
            [false, ['Site' => ['relate_main_site' => true], 'Content' => ['main_site_content_id' => 1, 'alias_id' => null, 'type' => 'BlogContent']]],
            [true, ['Site' => ['relate_main_site' => true], 'Content' => ['main_site_content_id' => 1, 'alias_id' => null, 'type' => 'ContentFolder']]]
        ];
    }

    /**
     * アクションが利用可能かどうか確認する
     * isActionAvailable
     *
     * @param string $type コンテンツタイプ
     * @param string $action アクション
     * @param string $entityId コンテンツを特定するID
     * @return bool
     * @dataProvider isActionAvailableDataProvider
     */
    public function testIsActionAvailable($type, $action, $entityId, $userGroup, $expect) {
//	$user = BcUtil::loginUser('admin');
//	$url = $this->settings[$type]['url'][$action] . '/' . $entityId;
//	return $this->_Permission->check($url, $user['user_group_id']);
        //$this->BcContents->settings = $this->loadFixtures('ContentBcContentsHelper');
        $_SESSION['Auth'][BcUtil::authSessionKey('admin')]['user_group_id'] = $userGroup;
        App::uses('BcContentsComponent', 'Controller/Component');
        $BcContentsComponent = new BcContentsComponent(new ComponentCollection());
        $BcContentsComponent->setupAdmin();
        $View = new BcAppView();
        $View->set('contentsSettings', $BcContentsComponent->settings['items']);
        $View->helpers = array('BcContents');
        $View->loadHelpers();
        $View->BcContents->setup();
//        print_r($View->BcContents->settings);
        $result = $View->BcContents->isActionAvailable($type, $action, $entityId);
        $this->assertEquals($expect, $result);
    }

    public function isActionAvailableDataProvider() {
        return [
            // 管理ユーザー
            ['Default', 'admin_index', 1, 1, false], // 存在しないアクション
            ['ContentFolder', 'icon', 1, 1, true], // 存在するアクション
            ['ContentFolder', 'add', 1, 1, true], // 存在するアクション
            ['ContentFolder', 'edit', 1, 1, true], // 存在するアクション
            ['ContentFolder', 'delete', 1, 1, true], // 存在するアクション
            ['ContentAlias', 'icon', 1, 1, true], // 存在するアクション
            ['BlogContent', 'manage', 1, 1, true], // 存在するアクション
            ['MailContent', 'manage', 1, 1, true], // 存在するアクション
            ['Page', 'copy', 1, 1, true], // 存在するアクション
            // 運営ユーザー
            ['ContentFolder', 'hoge', 2, 2, false], // 存在しないアクション
            ['Page', 'add', 2, 2, true], // 存在するアクション（権限あり）
            ['Page', 'edit', 2, 2, true], // 存在するアクション（権限あり）
            ['Page', 'delete', 1, 2, true], // 存在するアクション（権限あり）
            ['ContentFolder', 'edit', 1, 2, false], // 存在するアクション（権限なし）
            ['ContentAlias', 'add', 1, 2, false], // 存在するアクション（権限なし）
            ['ContentLink', 'add', 1, 2, false], // 存在するアクション（権限なし）
            ['BlogContent', 'add', 1, 2, false], // 存在するアクション（権限なし）
            ['MailContent', 'edit', 2, 2, false], // 存在するアクション（権限なし）
        ];
    }

    /**
     * コンテンツIDよりURLを取得する
     * getUrlById
     *
     * @param $id
     * @return string
     * @dataProvider getUrlByIdDataProvider
     */
    public function testGetUrlById($id, $full, $expect) {
        $this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
//		$data = $this->find('first', ['conditions' => ['Content.id' => $id]]);
//		return $this->getUrl($data['Content']['url'], $full, $data['Site']['use_subdomain']);
        $result = $this->BcContents->getUrlById($id, $full);
        var_dump($result);
        var_dump($expect);
        $this->assertEquals($expect, $result);
    }

    public function getUrlByIdDataProvider() {
        return array(
            array(1, false, '/'),
            array(1, true, 'http://localhost/'),
            array(2, false, '/m/'),
            array(2, true, 'http://localhost/m/'),
            array(9, true, 'http://localhost/m/index'),
        );
    }

    /**
     * フルURLを取得する
     * getUrl
     *
     * @param string $url
     * @param bool $prefix
     * @param bool $useSubDomain
     * @return mixed
     * @dataProvider getUrlDataProvider
     */
    public function testGetUrl($url, $full, $useSubDomain, $expect) {
        $this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
//          return $this->_Content->getUrl($url, $full, $useSubDomain);
        $result = $this->BcContents->getUrl($url, $full, $useSubDomain);
        var_dump($result);
        $this->assertEquals($expect, $result);
    }

    public function getUrlDataProvider() {
        return array(
            ['/', false, false, '/'],
            ['/', true, false, 'http://localhost/'],
            ['/', true, true, 'http://localhost/'],
            ['/admin/', true, true, 'http://localhost/admin/'],
            ['/admin/', false, true, '/admin/'],
            ['/admin/', false, false, '/admin/'],
        );
    }

    /**
     * プレフィックスなしのURLを取得する
     * getPureUrl
     *
     * @param string $url
     * @param string $siteId
     * @return mixed
     * @dataProvider getPureUrlDataProvider
     */
    public function testGetPureUrl($url, $siteId, $expect) {
        $this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
//          return $this->_Content->pureUrl($url, $siteId);
        $result = $this->BcContents->getPureUrl($url, $siteId);
        $this->assertEquals($expect, $result);
    }

    public function getPureUrlDataProvider() {
        return array(
            ['/', 1, '/'],
            ['', '', '/'],
            ['http://192.168.33.10/admin/contents/', 1, '/http://192.168.33.10/admin/contents/'],
            ['admin/contents/', 1, '/admin/contents/'],
        );
    }

    /**
     * 現在のURLを元に指定したサブサイトのURLを取得する
     * getCurrentRelatedSiteUrl
     * フロントエンド専用メソッド
     * @param string $siteName
     * @return mixed|string
     * @dataProvider getCurrentRelatedSiteUrlDataProvider
     */
    public function testGetCurrentRelatedSiteUrl($siteName, $expect) {
        $this->BcContents->request = $this->_getRequest('/');  
        $_SERVER['HTTP_USER_AGENT'] = 'iPhone';
        $result = $this->BcContents->getCurrentRelatedSiteUrl($siteName);
        $this->assertEquals($expect, $result);
    }

    public function getCurrentRelatedSiteUrlDataProvider() {
        return [
            ['smartphone', '/s/'],
            ['hoge', ''],
        ];
    }
    
    /**
     * 関連サイトのコンテンツを取得
     * getRelatedSiteContents
     * フロントエンド専用メソッド
     * @param int $id コンテンツID = Null
     * @return array | false
     * @dataProvider getRelatedSiteContentsDataProvider
    */
    public function testGetRelatedSiteContents($id, $options, $expect) {
        $this->BcContents->request = $this->_getRequest('/');
        $_SERVER['HTTP_USER_AGENT'] = 'iPhone';
        $result = $this->BcContents->getRelatedSiteContents($id, $options);
        $this->assertEquals($expect, $result[0]['Content']['id']);                       
    }
    public function getRelatedSiteContentsDataProvider() {
        return [
            [null,['Content'],4],
            ['1',[], False],
        ];
    }

	/**
	 * 関連サイトのリンク情報を取得する
	 * フロントエンド専用メソッド
	 * getRelatedSiteLinks
	 * @param int $id
	 * @return array
	 * @dataProvider getRelatedSiteLinksDataProvider
	*/
	public function testGetRelatedSiteLinks($id, $options, $expect) {
        $this->BcContents->request = $this->_getRequest('/');
        $_SERVER['HTTP_USER_AGENT'] = 'iPhone';
        $result = $this->BcContents->getRelatedSiteLinks($id, $options);
        $this->assertEquals($expect, $result);      
		}	
    public function getRelatedSiteLinksDataProvider() {
        return [
            [null,['Content'],[['prefix' => '','name' => 'パソコン', 'url'=>'/index'],['prefix' => 'mobile','name' => 'ケータイ', 'url'=>'/m/index'],['prefix' => 'smartphone','name' => 'スマートフォン', 'url'=>'/s/index']]],
            [1,['Content'], []],
        ];
    }	
}
