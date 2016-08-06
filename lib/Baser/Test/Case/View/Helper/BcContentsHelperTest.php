<?php
/**
 * test for BcContentsHelper
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since	       baserCMS v 3.0.6
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
		'baser.Default.Site',
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

}