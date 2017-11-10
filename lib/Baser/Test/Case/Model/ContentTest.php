<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Model
 * @since			baserCMS v 4.0.1
 * @license			http://basercms.net/license/index.html
 */

App::uses('Content', 'Model');

/**
 * ContentTest class
 *
 * @package Baser.Test.Case.Model
 * @property Content $Content
 */
class ContentTest extends BaserTestCase {

	public $fixtures = [
		'baser.Model.Content.ContentStatusCheck',
		'baser.Routing.Route.BcContentsRoute.SiteBcContentsRoute',
		'baser.Routing.Route.BcContentsRoute.ContentBcContentsRoute',
		'baser.Default.SiteConfig',
		'baser.Default.User',
	];

/**
 * set up
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Content = ClassRegistry::init('Content');
		BcSite::flash();
	}

/**
 * Implemented Events
 */
	public function testImplementedEvents() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 関連するサブサイトで、関連コンテンツを作成する際、同階層に重複名称のコンテンツがないか確認する
 *
 * 新規の際は、存在するだけでエラー
 * 編集の際は、main_site_content_id が自身のIDでない、alias_id が自身のIDでない場合エラー
 */
	public function testDuplicateRelatedSiteContent() {

	}

/**
 * Before Validate
 */
	public function testBeforeValidate() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * After Validate
 */
	public function testAfterValidate() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 一意の name 値を取得する
 */
	public function testGetUniqueName() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * Before Save
 */
	public function testBeforeSave() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * After Save
 */
	public function testAfterSave() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 関連するコンテンツ本体のデータキャッシュを削除する
 */
	public function testDeleteAssocCache() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * Before Delete
 */
	public function testBeforeDelete() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * After Delete
 *
 * 関連コンテンツのキャッシュを削除する
 */
	public function testAfterDelete() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 自データのエイリアスを削除する
 *
 * 全サイトにおけるエイリアスを全て削除
 */
	public function testDeleteAlias() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * メインサイトの場合、連携設定がされている子サイトのエイリアス削除する
 */
	public function testDeleteRelateSubSiteContent() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * メインサイトの場合、連携設定がされている子サイトのエイリアスを追加・更新する
 */
	public function testUpdateRelateSubSiteContent() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * サブサイトのプレフィックスがついていない純粋なURLを取得
 */
	public function testPureUrl() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * Content data を作成して保存する
 */
	public function testCreateContent() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * testCreateUrl
 *
 * @param int $id コンテンツID
 * @param string $expects 期待するURL
 * @dataProvider createUrlDataProvider
 */
	public function testCreateUrl($id, $expects) {
		$this->assertEquals($this->Content->createUrl($id), $expects);
	}

	public function createUrlDataProvider() {
		return [
			["hogehoge'/@<>1",''],
			[1, '/'],
			[2, '/m/'],
			[3, '/s/'],
			[4, '/index'],
			[5, '/service/'],
			[6, '/m/index'],
			[7, '/service/contact/'],
			[8, '/news/'],
			[9, '/service/service1'],
			[10, '/s/index'],
			[11, '/s/news/'],
			[12, '/s/service/'],
			[13, '/en/'],
			[14, '/sub/'],
			[15, '/another.com/'],
			[16, '/s/service/contact/'],
			[17, '/m/news/'],
			[18, '/en/news/'],
			[19, '/sub/news/'],
			[20, '/another.com/news/'],
			[21, '/en/service/'],
			[22, '/en/service/service1'],
			[23, '/sub/service/'],
			[24, '/sub/service/service1'],
			[25, '/another.com/service/'],
			[26, '/m/service/'],
			[27, '/m/service/contact/'],
			[28, '/en/service/contact/'],
			[29, '/sub/service/contact/'],
			[30, '/another.com/service/contact/'],
			[31, '/m/service/service1'],
			[32, '/s/service/service1'],
			[33, '/another.com/service/service1'],
			[34, '/en/index'],
			[35, '/sub/index'],
			[36, '/another.com/index'],
			[37, '/another.com/s/'],
			[38, '/another.com/s/index'],
			[39, '/another.com/s/news/'],
			[40, '/another.com/s/service/'],
			[41, '/another.com/s/service/service1'],
			[42, '/another.com/s/service/contact/'],
		];
	}

/**
 * システムデータを更新する
 *
 * URL / 公開状態 / メインサイトの関連コンテンツID
 */
	public function testUpdateSystemData() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ID を指定して公開状態かどうか判定する
 */
	public function testIsPublishById() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 子ノードのURLを全て更新する
 */
	public function testUpdateChildren() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * タイプよりコンテンツを取得する
 */
	public function testFindByType() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * コンテンツフォルダーのリストを取得
	 * コンボボックス用
	 */
	public function testGetContentFolderList() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * ツリー構造のデータを コンボボックスのデータ用に変換する
	 */
	public function testConvertTreeList() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ツリー構造より論理削除する
 */
	public function testSoftDeleteFromTree() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 再帰的に削除
 *
 * エイリアスの場合
 */
	public function testDeleteRecursive() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ゴミ箱より元に戻す
 */
	public function testTrashReturn() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 再帰的にゴミ箱より元に戻す
 */
	public function testTrashReturnRecursive() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * タイプよりコンテンツを削除する
 */
	public function testDeleteByType() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * コンテンツIDよりURLを取得する
 */
	public function testGetUrlById() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * testGetUrl
 *
 * $param string $host ホスト名
 * $param string $ua ユーザーエージェント名
 * @param string $url 変換前URL
 * @param boolean $full フルURLで出力するかどうか
 * @param boolean $useSubDomain サブドメインを利用するかどうか
 * @param string $expects 期待するURL
 * @dataProvider getUrlDataProvider
 */
	public function testGetUrl($host, $ua, $url, $full, $useSubDomain, $expects) {
		$siteUrl = Configure::read('BcEnv.siteUrl');
		Configure::write('BcEnv.siteUrl', 'http://main.com');
		if($ua) {
			$_SERVER['HTTP_USER_AGENT'] = $ua;
		}
		if($host) {
			Configure::write('BcEnv.host', $host);
		}
		Router::setRequestInfo($this->_getRequest('/m/'));
		$result = $this->Content->getUrl($url, $full, $useSubDomain);
		$this->assertEquals($result, $expects);
		Configure::write('BcEnv.siteUrl', $siteUrl);
	}

	public function getUrlDataProvider() {
		return [
			// ノーマルURL
			['main.com', '', '/', false, false, '/'],
			['main.com', '', '/index', false, false, '/'],
			['main.com', '', '/news/archives/1', false, false, '/news/archives/1'],
			['main.com', 'SoftBank', '/m/news/archives/1', false, false, '/m/news/archives/1'],
			['main.com', 'iPhone', '/news/archives/1', false, false, '/news/archives/1'],	// 同一URL
			['sub.main.com', '', '/sub/', false, true, '/'],
			['sub.main.com', '', '/sub/index', false, true, '/'],
			['sub.main.com', '', '/sub/news/archives/1', false, true, '/news/archives/1'],
			['another.com', '', '/another.com/', false, true, '/'],
			['another.com', '', '/another.com/index', false, true, '/'],
			['another.com', '', '/another.com/news/archives/1', false, true, '/news/archives/1'],
			['another.com', 'iPhone', '/another.com/s/news/archives/1', false, true, '/news/archives/1'],
			// フルURL
			['main.com', '', '/', true, false, 'http://main.com/'],
			['main.com', '', '/index', true, false, 'http://main.com/'],
			['main.com', '', '/news/archives/1', true, false, 'http://main.com/news/archives/1'],
			['main.com', 'SoftBank', '/m/news/archives/1', true, false, 'http://main.com/m/news/archives/1'],
			['main.com', 'iPhone', '/news/archives/1', true, false, 'http://main.com/news/archives/1'],	// 同一URL
			['sub.main.com', '', '/sub/', true, true, 'http://sub.main.com/'],
			['sub.main.com', '', '/sub/index', true, true, 'http://sub.main.com/'],
			['sub.main.com', '', '/sub/news/archives/1', true, true, 'http://sub.main.com/news/archives/1'],
			['another.com', '', '/another.com/', true, true, 'http://another.com/'],
			['another.com', '', '/another.com/index', true, true, 'http://another.com/'],
			['another.com', '', '/another.com/news/archives/1', true, true, 'http://another.com/news/archives/1'],
			['another.com', 'iPhone', '/another.com/s/news/archives/1', true, true, 'http://another.com/news/archives/1'],
		];
	}

/**
 * testGetUrl の base テスト
 * 
 * @param $url
 * @param $base
 * @param $expects
 * @dataProvider getUrlBaseDataProvider
 */
	public function testGetUrlBase($url, $base, $useBase, $expects) {
		Configure::write('app.baseUrl', $base);
		$request = $this->_getRequest('/');
		$request->base = $base;
		Router::setRequestInfo($request);
		$result = $this->Content->getUrl($url, false, false, $useBase);
		$this->assertEquals($result, $expects);
	}
	
	public function getUrlBaseDataProvider() {
		return [
			['/news/archives/1', '', true, '/news/archives/1'],
			['/news/archives/1', '', false, '/news/archives/1'],
			['/news/archives/1', '/sub', true, '/sub/news/archives/1'],
			['/news/archives/1', '/sub', false, '/news/archives/1'],
		];
	}

/**
 * 現在のフォルダのURLを元に別サイトにフォルダを生成する
 * 最下層のIDを返却する
 */
	public function testCopyContentFolderPath() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * コピーする
 */
	public function testCopy() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 公開済の conditions を取得
 */
	public function testGetConditionAllowPublish() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 公開状態を取得する
 */
	public function testIsAllowPublish() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 指定したURLのパス上のコンテンツでフォルダ以外が存在するか確認
 */
	public function testExistsContentByUrl() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 公開されたURLが存在するか確認する
 */
	public function testExistsPublishUrl() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * データが公開済みかどうかチェックする
 */
	public function testIsPublish() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 移動元のコンテンツと移動先のディレクトリから移動が可能かチェックする
 */
	public function testIsMovable() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * タイトル、URL、公開状態が更新されているか確認する
 */
	public function testIsChangedStatus() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * サイトルートコンテンツを取得する
 */
	public function testGetSiteRoot() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 親のテンプレートを取得する
 */
	public function testGetParentTemplate() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * コンテンツを移動する
 *
 * 基本的に targetId の上に移動する前提となる
 * targetId が空の場合は、同親中、一番下に移動する
 */
	public function testMove() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * メインサイトの場合、連携設定がされている子サイトも移動する
 */
	public function testMoveRelateSubSiteContent() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * オフセットを元にコンテンツを移動する
 */
	public function testMoveOffset() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 同じ階層における並び順を取得
 *
 * id が空の場合は、一番最後とみなす
 */
	public function testGetOrderSameParent() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 関連サイトの関連コンテンツを取得する
 */
	public function testGetRelatedSiteContents() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * キャッシュ時間を取得する
 */
	public function testGetCacheTime() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 全てのURLをデータの状況に合わせ更新する
 */
	public function testUpdateAllUrl() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 指定したコンテンツ配下のコンテンツのURLを一括更新する
 */
	public function testUpdateChildrenUrl() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * コンテンツ管理のツリー構造をリセットする
 */
	public function testResetTree() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * URLからコンテンツを取得する
 *
 * TODO sameUrl / useSubDomain のテストが書けていない
 * Siteのデータを用意する必要がある
 * 
 * @param string $url
 * @param string $publish
 * @param bool $extend
 * @param bool $sameUrl
 * @param bool $useSubDomain
 * @param bool $expected
 * @dataProvider findByUrlDataProvider
 */
	public function testFindByUrl($expected, $url, $publish = true, $extend = false, $sameUrl = false, $useSubDomain = false) {
		$this->loadFixtures('ContentStatusCheck');
		$result = (bool) $this->Content->findByUrl($url, $publish, $extend, $sameUrl, $useSubDomain);
		$this->assertEquals($expected, $result);
	}

	public function findByUrlDataProvider() {
		return [
			[true, '/about', true],
			[false, '/service', true],
			[true, '/service', false],
			[false, '/hoge', false],
			[true, '/news/archives/1', true, true],
			[false, '/news/archives/1', true, false],
		];
	}

}