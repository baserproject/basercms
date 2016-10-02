<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('Helper', 'View');
App::uses('BcAdminHelper', 'View/Helper');

/**
 * BcAdminHelper Test Case
 *
 */
class BcAdminHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'baser.Default.UserGroup',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$View = new View();
		$this->BcAdmin = new BcAdminHelper($View);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BcAdmin);
		parent::tearDown();
	}

/**
 * 管理システムグローバルメニューの利用可否確認
 *
 * @param mixed $admin request->params['admin']の値
 * @param int $groupId ユーザーグループID
 * @param boolean $expected 期待値
 * @param string $message テストが失敗した場合に表示されるメッセージ
 * @dataProvider isAdminGlobalmenuUsedDataProvider
 */
	public function testIsAdminGlobalmenuUsed($admin, $groupId, $expected, $message = null) {

		$this->BcAdmin->request->params['admin'] = $admin;
		$this->BcAdmin->_View->viewVars['user'] = array(
			'user_group_id' => $groupId
		);

		$result = $this->BcAdmin->isAdminGlobalmenuUsed();
		$this->assertEquals($expected, $result, $message);
	}

	public function isAdminGlobalmenuUsedDataProvider() {
		return array(
			array('', null, false, '管理システムグローバルメニューの利用可否確認が正しくありません'),
			array(1, null, false, '管理システムグローバルメニューの利用可否確認が正しくありません'),
			array('', 1, true, '管理システムグローバルメニューの利用可否確認が正しくありません'),
			array('1', 1, true, '管理ユーザーの管理システムグローバルメニューの利用可否確認が正しくありません'),
			array('1', 2, 0, '運営ユーザーの管理システムグローバルメニューの利用可否確認が正しくありません'),
		);
	}

/**
 * testIsSystemAdmin method
 *
 * @param mixed $admin request->params['admin']の値
 * @param int $groupId ユーザーグループID
 * @param boolean $expected 期待値
 * @param string $message テストが失敗した場合に表示されるメッセージ
 * @dataProvider isSystemAdminDataProvider
 */
	public function testIsSystemAdmin($admin, $groupId, $expected, $message = null) {
		$this->BcAdmin->request->params['admin'] = $admin;
		$this->BcAdmin->_View->viewVars['user'] = array(
			'user_group_id' => $groupId
		);

		$result = $this->BcAdmin->isSystemAdmin();
		$this->assertEquals($expected, $result, $message);
	}

	public function isSystemAdminDataProvider() {
		return array(
			array('', null, false, 'ログインユーザーのシステム管理者チェックが正しくありません'),
			array(1, null, false, 'ログインユーザーのシステム管理者チェックが正しくありません'),
			array('', 1, false, 'ログインユーザーのシステム管理者チェックが正しくありません'),
			array('1', 1, true, '管理ユーザーのシステム管理者チェックが正しくありません'),
			array('1', 2, false, '運営ユーザーのシステム管理者チェックが正しくありません'),
		);
	}
}
