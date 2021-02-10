<?php

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.View.Helper
 * @since           baserCMS v 3.0.6
 * @license         https://basercms.net/license/index.html
 */
App::uses('BcAppView', 'View');
App::uses('BcContentsHelper', 'View/Helper');

/**
 * BcPage helper library.
 *
 * @package Baser.Test.Case
 * @property BcContentsHelper $BcContents
 */
class BcContentsHelperTest extends BaserTestCase
{

	/**
	 * Fixtures
	 * @var array
	 */
	public $fixtures = [
		'baser.View.Helper.BcContentsHelper.ContentBcContentsHelper',
		'baser.Default.SiteConfig',
		'baser.Default.Site',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
		'baser.Default.Permission',
		'baser.Default.ThemeConfig',
	];

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
	public function setUp()
	{
		parent::setUp();
		$this->_View = new BcAppView();
		$this->_View->helpers = ['BcContents'];
		$this->_View->loadHelpers();
		$this->BcContents = $this->_View->BcContents;
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
	 * ページリストを取得する
	 *
	 * @param int $pageCategoryId カテゴリID
	 * @param int $level 関連データの階層
	 * @param int $expectedCount 期待値
	 * @param string $expectedTitle
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider getPageListDataProvider
	 */
	public function testGetTree($id, $level, $expectedCount, $expectedTitle, $message = null)
	{
		$result = $this->BcContents->getTree($id, $level);
		$resultTitle = null;
		$resultCount = null;
		switch($level) {
			case 1:
				if (!empty($result[0]['Content']['title'])) {
					$resultTitle = $result[0]['Content']['title'];
					$resultCount = count($result);
				}
				break;
			case 2:
				if ($result) {
					foreach($result as $data) {
						if ($data['children']) {
							$resultTitle = $data['children'][0]['Content']['title'];
							$resultCount = count($data['children']);
						}
					}
				}
				break;
			case 3:
				if ($result) {
					foreach($result as $data) {
						if ($data['children']) {
							foreach($data['children'] as $data2) {
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

	public function getPageListDataProvider()
	{
		return [
			// PC版
			[1, 1, 7, 'トップページ', 'PC版１階層目のデータが正常に取得できません'],
			[1, 2, 4, 'サービス', 'PC版２階層目のデータが正常に取得できません'],
			[1, 3, 1, 'サブサービス１', 'PC版３階層目のデータが正常に取得できません'],
			// ケータイ
			[2, 1, 3, 'トップページ', 'ケータイ版１階層目のデータが正常に取得できません'],
			// スマホ
			[3, 1, 7, 'トップページ', 'スマホ版１階層目のデータが正常に取得できません'],
			[3, 2, 1, 'サービス１', 'スマホ版２階層目のデータが正常に取得できません']
		];
	}

	/**
	 * @dataProvider isSiteRelatedDataProvider
	 */
	public function testIsSiteRelated($expect, $data)
	{
		$result = $this->BcContents->isSiteRelated($data);
		$this->assertEquals($expect, $result);
	}

	public function isSiteRelatedDataProvider()
	{
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
	 * @param bool $expect 期待値
	 * @dataProvider isActionAvailableDataProvider
	 */
	public function testIsActionAvailable($type, $action, $entityId, $userGroup, $expect)
	{
		$_SESSION['Auth'][BcUtil::authSessionKey('admin')]['user_group_id'] = $userGroup;
		App::uses('BcContentsComponent', 'Controller/Component');
		$BcContentsComponent = new BcContentsComponent(new ComponentCollection());
		$BcContentsComponent->setupAdmin();
		$View = new BcAppView();
		$View->set('contentsSettings', $BcContentsComponent->settings['items']);
		$View->helpers = ['BcContents'];
		$View->loadHelpers();
		$View->BcContents->setup();
		$result = $View->BcContents->isActionAvailable($type, $action, $entityId);
		$this->assertEquals($expect, $result);
	}

	public function isActionAvailableDataProvider()
	{
		return [
			// 管理ユーザー
			['Default', 'admin_index', 1, 1, false], // 存在しないアクション
			['ContentFolder', 'add', 1, 1, true], // 存在するアクション
			['ContentFolder', 'edit', 1, 1, true], // 存在するアクション
			['ContentFolder', 'delete', 1, 1, true], // 存在するアクション
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
	 * public function testGetUrlById() {
	 * $this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	 * }
	 */

	/**
	 * フルURLを取得する
	 * getUrl
	 *
	 * public function testGetUrl() {
	 * $this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	 * }
	 */

	/**
	 * プレフィックスなしのURLを取得する
	 * getPureUrl
	 *
	 * public function testGetPureUrl() {
	 * $this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	 * }
	 */

	/**
	 * 現在のURLを元に指定したサブサイトのURLを取得する
	 * getCurrentRelatedSiteUrl
	 * フロントエンド専用メソッド
	 * @param string $siteName
	 * @param mixed|string $expect 期待値
	 * @dataProvider getCurrentRelatedSiteUrlDataProvider
	 */
	public function testGetCurrentRelatedSiteUrl($siteName, $expect)
	{
		$this->BcContents->request = $this->_getRequest('/');
		$_SERVER['HTTP_USER_AGENT'] = 'iPhone';
		$result = $this->BcContents->getCurrentRelatedSiteUrl($siteName);
		$this->assertEquals($expect, $result);
	}

	public function getCurrentRelatedSiteUrlDataProvider()
	{
		return [
			// 戻り値が空でないもの（）
			['smartphone', '/s/'],
			['mobile', '/m/'],
			// $siteNameの値が空の場合、返り値も空
			['', ''],
			['hoge', ''],
		];
	}

	/**
	 * 関連サイトのコンテンツを取得
	 * getRelatedSiteContents
	 * フロントエンド専用メソッド
	 * @param int $id コンテンツID = Null
	 * @param array $options
	 * @param array | false $expect 期待値
	 * @dataProvider getRelatedSiteContentsDataProvider
	 */
	public function testGetRelatedSiteContents($id, $options, $expect)
	{
		$this->BcContents->request = $this->_getRequest('/');
		$_SERVER['HTTP_USER_AGENT'] = 'iPhone';
		$result = $this->BcContents->getRelatedSiteContents($id, $options);
		if (!empty($result[1]['Content']['id'])) {
			$result = $result[1]['Content']['id'];
		}
		$this->assertEquals($expect, $result);
	}

	public function getRelatedSiteContentsDataProvider()
	{
		return [
			// コンテンツIDが空 オプションも空
			[null, [], 9],
			// コンテンツIDが空  オプション excludeIds 0~1
			['', ['excludeIds' => [0]], 10],
			['', ['excludeIds' => [1]], 10],
			// コンテンツIDが空  オプション excludeIds 2~
			['', ['excludeIds' => [2]], 9],
			['', ['excludeIds' => [99]], 9],
			// コンテンツIDに値が入っていれば、false
			[1, ['excludeIds' => []], 2],
			[99, [], []],
		];
	}

	/**
	 * 関連サイトのリンク情報を取得する
	 * フロントエンド専用メソッド
	 * getRelatedSiteLinks
	 * @param int $id
	 * @param array $options
	 * @param array $expect 期待値
	 * @dataProvider getRelatedSiteLinksDataProvider
	 */
	public function testGetRelatedSiteLinks($id, $options, $expect)
	{
		$this->BcContents->request = $this->_getRequest('/');
		$_SERVER['HTTP_USER_AGENT'] = 'iPhone';
		$result = $this->BcContents->getRelatedSiteLinks($id, $options);
		$this->assertEquals($expect, $result);
	}

	public function getRelatedSiteLinksDataProvider()
	{
		return [
			// IDが空 オプションも空
			[null, [], [['prefix' => '', 'name' => 'パソコン', 'url' => '/index'], ['prefix' => 'mobile', 'name' => 'ケータイ', 'url' => '/m/index'], ['prefix' => 'smartphone', 'name' => 'スマートフォン', 'url' => '/s/index']]],
			// IDが空  オプション excludeIds 0~2
			['', ['excludeIds' => [0]], [0 => ['prefix' => 'mobile', 'name' => 'ケータイ', 'url' => '/m/index'], 1 => ['prefix' => 'smartphone', 'name' => 'スマートフォン', 'url' => '/s/index']]],
			[false, ['excludeIds' => [1]], [['prefix' => '', 'name' => 'パソコン', 'url' => '/index'], ['prefix' => 'smartphone', 'name' => 'スマートフォン', 'url' => '/s/index']]],
			[0, ['excludeIds' => [2]], [['prefix' => '', 'name' => 'パソコン', 'url' => '/index'], ['prefix' => 'mobile', 'name' => 'ケータイ', 'url' => '/m/index']]],
			// IDが空  オプション excludeIds 3~
			[0, ['excludeIds' => [3]], [['prefix' => '', 'name' => 'パソコン', 'url' => '/index'], ['prefix' => 'mobile', 'name' => 'ケータイ', 'url' => '/m/index'], ['prefix' => 'smartphone', 'name' => 'スマートフォン', 'url' => '/s/index']]],
			[0, ['excludeIds' => [99]], [['prefix' => '', 'name' => 'パソコン', 'url' => '/index'], ['prefix' => 'mobile', 'name' => 'ケータイ', 'url' => '/m/index'], ['prefix' => 'smartphone', 'name' => 'スマートフォン', 'url' => '/s/index']]],
			// IDに値が入っていれば、false
			[1, ['excludeIds' => [0]], [['prefix' => 'mobile', 'name' => 'ケータイ', 'url' => '/m/'], ['prefix' => 'smartphone', 'name' => 'スマートフォン', 'url' => '/s/']]],
			[99, [], []],
		];
	}

	/**
	 * コンテンツ設定を Json 形式で取得する
	 * getJsonSettings
	 */
	public function testGetJsonSettings()
	{
		$this->_loginAdmin();
		App::uses('BcContentsComponent', 'Controller/Component');
		$BcContentsComponent = new BcContentsComponent(new ComponentCollection());
		$BcContentsComponent->setupAdmin();
		$View = new BcAppView();
		$View->set('contentsSettings', $BcContentsComponent->settings['items']);
		$View->helpers = ['BcContents'];
		$View->loadHelpers();
		$View->BcContents->setup();
		$result = $View->BcContents->getJsonSettings();
		// JSON形式が正しいかどうか
		$this->assertTrue(is_string($result) && is_array(json_decode($result, true)) && (json_last_error() == JSON_ERROR_NONE)? true : false);
	}

	/**
	 * @param string $expect 期待値
	 * @param string $no
	 * @dataProvider getJsonSettingsDataProvider
	 */
	public function testGetJsonSettingsEquals($expect, $no)
	{
		$this->_loginAdmin();
		App::uses('BcContentsComponent', 'Controller/Component');
		$BcContentsComponent = new BcContentsComponent(new ComponentCollection());
		$BcContentsComponent->setupAdmin();
		$View = new BcAppView();
		$View->set('contentsSettings', $BcContentsComponent->settings['items']);
		$View->helpers = ['BcContents'];
		$View->loadHelpers();
		$View->BcContents->setup();
		// 　getJsonSettingsで取得した値がsettingsの値と等しいかどうか
		$result = json_decode($View->BcContents->getJsonSettings(), true);
		$result = $result[$no]['title'];
		$this->assertEquals($expect, $result);
	}

	public function getJsonSettingsDataProvider()
	{
		return [
			['無所属コンテンツ', 'Default'],
			['フォルダー', 'ContentFolder'],
			['ブログ', 'BlogContent'],
		];
	}

	/**
	 * データが公開状態にあるか確認する
	 *
	 * public function testIsAllowPublish() {
	 * $this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	 * }
	 */

	/**
	 * 親フォルダを取得する
	 *
	 * @param $expected
	 * @param $id
	 * @param $direct
	 * @dataProvider getParentDataProvider
	 */
	public function testGetParent($expected, $id, $direct)
	{
		if (is_string($id)) {
			$this->BcContents->request = $this->_getRequest($id);
			$id = null;
		}
		$result = $this->BcContents->getParent($id, $direct);
		if ($direct) {
			if ($result) {
				$result = $result['Content']['id'];
			}
		} else {
			if ($result) {
				$result = Hash::extract($result, '{n}.Content.id');
			}
		}
		$this->assertEquals($expected, $result);
	}

	public function getParentDataProvider()
	{
		return [
			[1, 4, true],            // ダイレクト ROOT直下
			[21, 22, true],            // ダイレクト フォルダ内
			[false, 1, true],        // ダイレクト ルートフォルダ
			[false, 100, true],        // ダイレクト 存在しないコンテンツ
			[[1, 21], 24, false],    // パス ２階層配下
			[[1, 21, 24], 25, false],    // パス ３階層配下
			[[3, 26], 12, false],    // パス スマホ２階層配下
			[false, 100, false],    // パス 存在しないコンテンツ
			[[1, 21, 24], '/service/sub_service/sub_service_1', false] // パス URLで解決
		];
	}

	/**
	 * フォルダリストを取得する
	 *
	 * public function testGetContentFolderList() {
	 * $this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	 * }
	 */

	/**
	 * サイトIDからサイトルートとなるコンテンツを取得する
	 *
	 * public function testGetSiteRoot() {
	 * $this->markTestIncomplete('このメソッドは、モデルをラッピングしているメソッドの為スキップします。');
	 * }
	 */

	/**
	 * サイトIDからコンテンツIDを取得する
	 * getSiteRootId
	 *
	 * @param int $siteId
	 * @param string|bool $expect 期待値
	 * @dataProvider getSiteRootIdDataProvider
	 */
	public function testGetSiteRootId($siteId, $expect)
	{
		$result = $this->BcContents->getSiteRootId($siteId);
		$this->assertEquals($expect, $result);
	}

	public function getSiteRootIdDataProvider()
	{
		return [
			// 存在するサイトID（0~2）を指定した場合
			[0, 1],
			[1, 2],
			[2, 3],
			// 存在しないサイトIDを指定した場合
			[3, false],
			[99, false],
		];
	}

	/**
	 * エンティティIDからコンテンツの情報を取得
	 * getContentByEntityId
	 *
	 * @param string $contentType コンテンツタイプ
	 * ('Page','MailContent','BlogContent','ContentFolder')
	 * @param int $id エンティティID
	 * @param string $field 取得したい値
	 *  'name','url','title'など　初期値：Null
	 *  省略した場合配列を取得
	 * @param string|bool $expect 期待値
	 * @dataProvider getContentByEntityIdDataProvider
	 */
	public function testgetContentByEntityId($expect, $id, $contentType, $field)
	{
		$result = $this->BcContents->getContentByEntityId($id, $contentType, $field);
		$this->assertEquals($expect, $result);
	}

	public function getContentByEntityIdDataProvider()
	{
		return [
			// 存在するID（0~2）を指定した場合
			['/news/', '1', 'BlogContent', 'url'],
			['/contact/', '1', 'MailContent', 'url'],
			['/index', '1', 'Page', 'url'],
			['/service/', '4', 'ContentFolder', 'url'],
			['/service/sub_service/sub_service_1', '14', 'Page', 'url'],
			['サービス２', '12', 'Page', 'title'],
			// 存在しないIDを指定した場合
			[false, '5', 'BlogContent', 'name'],
			//指定がおかしい場合
			[false, '5', 'Blog', 'url'],
		];
	}

	/**
	 * urlからコンテンツの情報を取得
	 * getContentByUrl
	 *
	 * @param string $contentType コンテンツタイプ
	 * ('Page','MailContent','BlogContent','ContentFolder')
	 * @param string $url
	 * @param string $field 取得したい値
	 *  'name','url','title'など　初期値：Null
	 *  省略した場合配列を取得
	 * @param string|bool $expect 期待値
	 * @dataProvider getContentByUrlDataProvider
	 */
	public function testgetContentByUrl($expect, $url, $contentType, $field)
	{
		$result = $this->BcContents->getContentByUrl($url, $contentType, $field);
		$this->assertEquals($expect, $result);
	}

	public function getContentByUrlDataProvider()
	{
		return [
			// 存在するURL（0~2）を指定した場合
			['1', '/news/', 'BlogContent', 'entity_id'],
			['/contact/', '/contact/', 'MailContent', 'url'],
			['1', '/index', 'Page', 'entity_id'],
			['4', '/service/', 'ContentFolder', 'entity_id'],
			['14', '/service/sub_service/sub_service_1', 'Page', 'entity_id'],
			['サービス２', '/service/service2', 'Page', 'title'],
			// 存在しないURLを指定した場合
			[false, '/blog/', 'BlogContent', 'name'],
			//指定がおかしい場合
			[false, '/blog/', 'Blog', 'url'],
		];
	}

	/**
	 * IDがコンテンツ自身の親のIDかを判定する
	 * @param $id
	 * @param $parentId
	 * @param $expects
	 * @dataProvider isParentIdDataProvider
	 */
	public function testIsParentId($id, $parentId, $expects)
	{
		$this->assertEquals($expects, $this->BcContents->isParentId($id, $parentId));
	}

	public function isParentIdDataProvider()
	{
		return [
			[2, 1, true],
			[5, 1, true],
			[5, 2, false],
			[6, 21, true]
		];
	}

	public function test__construct()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	public function test_getIconUrl()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * 現在のページがコンテンツフォルダかどうか確認する
	 * @param $url
	 * @param $expects
	 * @dataProvider isFolderDataProvider
	 */
	public function testIsFolder($url, $expects)
	{
		$this->BcContents->request = $this->_getRequest($url);
		$this->assertEquals($expects, $this->BcContents->isFolder());
	}

	public function isFolderDataProvider()
	{
		return [
			['/', false],    // index あり
			['/about', false],
			['/service/', false],    // index あり
			['/service/index', false],
			['/contact/', false],
			['/service/sub_service/', true],    // index なし
			['/service/hoge', false]    // 存在しない
		];
	}

	/**
	 * 対象コンテンツが属するフォルダまでのフルパスを取得する
	 * フォルダ名称部分にはフォルダ編集画面へのリンクを付与する
	 * @param int $id コンテンツID
	 * @param string $expected 期待値
	 * @dataProvider getFolderLinkedUrlDataProvider
	 */
	public function testGetFolderLinkedUrl($url, $expected)
	{
		$content = ClassRegistry::init('Content')->find('first', [
			'conditions' => ['url' => $url],
			'recursive' => 0
		]);
		$this->assertEquals($expected, $this->BcContents->getFolderLinkedUrl($content));
	}

	public function getFolderLinkedUrlDataProvider()
	{
		return [
			['/', 'https://localhost/'],
			['/about', 'https://localhost/'],
			['/service/index', 'https://localhost/<a href="/admin/content_folders/edit/4">service</a>/'],
			['/s/service/index', 'https://localhost/<a href="/admin/content_folders/edit/3">s</a>/<a href="/admin/content_folders/edit/6">service</a>/'],
		];
	}

}
