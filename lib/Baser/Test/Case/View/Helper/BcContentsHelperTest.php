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
		$this->Page = ClassRegistry::init('BcContent');
		$this->BcContents  = $this->_View->BcContents;
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
		switch($level) {
			case 1:
				if(!empty($result[0]['Content']['title'])) {
					$resultTitle = $result[0]['Content']['title'];
					$resultCount = count($result);
				}
				break;
			case 2:
				if($result) {
					foreach($result as $data) {
						if($data['children']) {
							$resultTitle = $data['children'][0]['Content']['title'];
							$resultCount = count($data['children']);
						}
					}
				}
				break;
			case 3:
				if($result) {
					foreach($result as $data) {
						if($data['children']) {
							foreach($data['children'] as $data2) {
								if($data2['children']) {
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
 * @param string $type コンテンツを特定するID
 * @return bool
 * @dataProvider isActionAvailableDataProvider
*/
	public function testIsActionAvailable($type, $action, $entityId, $expect) {
//		$user = BcUtil::loginUser('admin');
//		$url = $this->settings[$type]['url'][$action] . '/' . $entityId;
//		return $this->_Permission->check($url, $user['user_group_id']);
                $this->BcContents->settings = $this->loadFixtures('ContentBcContentsHelper');
                var_dump($this->BcContents->settings);
		$result = $this->BcContents->isActionAvailable($type, $action, $entityId);
                var_dump($result);
                var_dump($expect);
                $this->assertEquals($expect, $result);
                }

        public function isActionAvailableDataProvider() {
		return array(
			array('Default', 'admin_index', 1, true),
			array('ContentFolder', 'admin_index', 2, true),
			array('Page', 'admin_delete', 2, true),
			array('MailContent', '_batch_del', 2, true),
			array('', 'settingForm', '', true),
			array('huga', 'hoge', -1, true),
		);
	}

}