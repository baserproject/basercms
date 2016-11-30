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
 * @param int $entityId コンテンツを特定するID
 * @return bool
 * @dataProvider isActionAvailableDataProvider
 */
	public function testIsActionAvailable($type, $action, $entityId, $userGroup, $expect) {
		$_SESSION['Auth'][BcUtil::authSessionKey('admin')]['user_group_id'] = $userGroup;
		App::uses('BcContentsComponent', 'Controller/Component');
		$BcContentsComponent = new BcContentsComponent(new ComponentCollection());
		$BcContentsComponent->setupAdmin();
		$View = new BcAppView();
		$View->set('contentsSettings', $BcContentsComponent->settings['items']);
		$View->helpers = array('BcContents');
		$View->loadHelpers();
		$View->BcContents->setup();
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
 * 
 */
	public function testGetUrlById() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}

/**
 * フルURLを取得する
 * getUrl
 *
 * @param string $url
 * @param bool $prefix
 * @param bool $useSubDomain
 * @return mixed
 * 
 */
	public function testGetUrl() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}

/**
 * プレフィックスなしのURLを取得する
 * getPureUrl
 *
 * @param string $url
 * @param int $siteId
 * @return mixed
 * 
 */
	public function testGetPureUrl() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
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

/**
 * コンテンツ設定を Json 形式で取得する
 * getJsonSettings
 * @return string 
*/
	public function testGetJsonSettings() {
		App::uses('BcContentsComponent', 'Controller/Component');
		$BcContentsComponent = new BcContentsComponent(new ComponentCollection());
		$BcContentsComponent->setupAdmin();
		$View = new BcAppView();
		$View->set('contentsSettings', $BcContentsComponent->settings['items']);
		$View->helpers = array('BcContents');
		$View->loadHelpers();
		$View->BcContents->setup();
		$result = $View->BcContents->getJsonSettings();
		// JSON形式が正しいかどうか		
		return is_string($result) && is_array(json_decode($result, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;		
	}
/**
 * @dataProvider getJsonSettingsDataProvider
*/
	public function testGetJsonSettingsEquals($expect) {
		App::uses('BcContentsComponent', 'Controller/Component');
		$BcContentsComponent = new BcContentsComponent(new ComponentCollection());
		$BcContentsComponent->setupAdmin();
		$View = new BcAppView();
		$View->set('contentsSettings', $BcContentsComponent->settings['items']);
		$View->helpers = array('BcContents');
		$View->loadHelpers();
		$View->BcContents->setup();
		// 　getJsonSettingsで取得した値がsettingsの値と等しいかどうか
		$result = json_decode($View->BcContents->getJsonSettings(),true);
		$result = $result['Default']['title'];
		$this->assertEquals($expect, $result);      
	}
	public function getJsonSettingsDataProvider() {
		return [
			['無所属コンテンツ'],
		];
	}

/**
 * データが公開状態にあるか確認する
 *
 * @param array $data コンテンツデータ
 * @param bool $self コンテンツ自身の公開状態かどうか 
 * @return mixed
 */
	public function isAllowPublish() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}

/**
 * 親コンテンツを取得する
 * 
 * @param int $contentId
 * @return mixed
 */
	public function getParent() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}	
/**
 * フォルダリストを取得する
 * 
 * @param int $siteId
 * @param array $options
 * @return array|bool
 */	
	public function getContentFolderList() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}
	
	public function getSiteRoot() {
		$this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	}
	
/**
 * サイトIDからコンテンツIDを取得する
 * getSiteRootId
 * 
 * @param int $siteId
 * @return string|bool
 * @dataProvider getSiteRootIdDataProvider
 */	
	public function testGetSiteRootId($siteId,$expect) {
		$result = $this->BcContents->getSiteRootId($siteId);
		$this->assertEquals($expect, $result);                       
	}
	public function getSiteRootIdDataProvider() {
		return [
			[1,2],
			// 存在しないサイトIDを指定した場合
			[4,false],
		];
	}
}
