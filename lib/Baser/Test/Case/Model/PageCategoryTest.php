<?php

/**
 * PageCategoryモデルのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS PageCategorys Community <http://sites.google.com/site/baserPageCategorys/>
 *
 * @copyright   Copyright 2008 - 2015, baserCMS PageCategorys Community
 * @link      http://basercms.net baserCMS Project
 * @since     baserCMS v 3.0.0-beta
 * @license     http://basercms.net/license/index.html
 */
App::uses('PageCategory', 'Model');

/**
 * PageCategoryTest class
 * 
 * class NonAssosiationPageCategory extends PageCategory {
 *  public $name = 'PageCategory';
 *  public $belongsTo = array();
 *  public $hasMany = array();
 * }
 * 
 * @package Baser.Test.Case.Model
 */
class PageCategoryTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.Page',
		'baser.Default.PageCategory',
	);

	public function setUp() {
		parent::setUp();
		$this->PageCategory = ClassRegistry::init('PageCategory');
	}

	public function tearDown() {
		unset($this->PageCategory);
		parent::tearDown();
	}

/**
 * validate
 */
	public function test必須チェック() {
		$this->PageCategory->create(array(
			'PageCategory' => array()
		));
		$this->assertFalse($this->PageCategory->validates());
		$this->assertArrayHasKey('name', $this->PageCategory->validationErrors);
		$this->assertEquals('ページカテゴリ名を入力してください。', current($this->PageCategory->validationErrors['name']));
		$this->assertArrayHasKey('title', $this->PageCategory->validationErrors);
		$this->assertEquals('ページカテゴリタイトルを入力してください。', current($this->PageCategory->validationErrors['title']));
	}

	public function test桁数チェック正常系() {
		$this->PageCategory->create(array(
			'PageCategory' => array(
				'name' => '12345678901234567890123456789012345678901234567890',
				'title' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345',
				'parent_id' => null,
			)
		));
		$this->assertTrue($this->PageCategory->validates());
	}

	public function test桁数チェック異常系() {
		$this->PageCategory->create(array(
			'PageCategory' => array(
				'name' => '123456789012345678901234567890123456789012345678901',
				'title' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
				'parent_id' => null,
			)
		));
		$this->assertFalse($this->PageCategory->validates());
		$this->assertArrayHasKey('name', $this->PageCategory->validationErrors);
		$this->assertEquals('ページカテゴリ名は50文字以内で入力してください。', current($this->PageCategory->validationErrors['name']));
		$this->assertArrayHasKey('title', $this->PageCategory->validationErrors);
		$this->assertEquals('ページカテゴリタイトルは255文字以内で入力してください。', current($this->PageCategory->validationErrors['title']));
	}

	public function test半角英数チェック異常系() {
		$this->PageCategory->create(array(
			'PageCategory' => array(
				'name' => '１２３ａｂｃ',
				'title' => 'hoge',
				'parent_id' => null,
			)
		));
		$this->assertFalse($this->PageCategory->validates());
		$this->assertArrayHasKey('name', $this->PageCategory->validationErrors);
		$this->assertEquals('ページカテゴリ名は半角英数字とハイフン、アンダースコアのみで入力してください。', current($this->PageCategory->validationErrors['name']));
	}

	public function test重複チェック親カテゴリなし異常系() {
		$this->PageCategory->create(array(
			'PageCategory' => array(
				'name' => 'mobile',
				'parent_id' => null,
			)
		));
		$this->assertFalse($this->PageCategory->validates());
		$this->assertArrayHasKey('name', $this->PageCategory->validationErrors);
		$this->assertEquals('入力されたページカテゴリー名は、同一階層に既に登録されています。', current($this->PageCategory->validationErrors['name']));
	}

	public function test重複チェック親カテゴリあり異常系() {
		$this->PageCategory->create(array(
			'PageCategory' => array(
				'name' => 'garaphone',
				'parent_id' => 2,
			)
		));
		$this->assertFalse($this->PageCategory->validates());
		$this->assertArrayHasKey('name', $this->PageCategory->validationErrors);
		$this->assertEquals('入力されたページカテゴリー名は、同一階層に既に登録されています。', current($this->PageCategory->validationErrors['name']));
	}

	/**
 * コントロールソースを取得する
 *
 * @param string フィールド名
 * @return array コントロールソース
 * @access public
 */
	public function getControlSource($field, $options = array()) {

	}

/**
 * beforeSave
 * 
 * @return boolean
 * @access public
 */
	public function beforeSave($options = array()) {

	}

/**
 * afterSave
 * 
 * @param boolean $created
 * @return void
 * @access public
 */
	public function afterSave($created, $options = array()) {

	}

/**
 * ページカテゴリのフォルダを生成してパスを返す
 * 
 * @param array $data ページカテゴリデータ
 * @return mixid カテゴリのパス / false
 * @access public
 */
	public function createPageCategoryFolder($data) {

	}

/**
 * カテゴリフォルダのパスを取得する
 * 
 * @param array $data ページカテゴリデータ
 * @return string $path
 * @access public
 */
	public function getPageCategoryFolderPath($data) {

	}

/**
 * 同一階層に同じニックネームのカテゴリがないかチェックする
 * 同じテーマが条件
 * 
 * @param array $check
 * @return boolean
 * @access public
 */
	public function duplicatePageCategory($check) {

	}

/**
 * 関連するページデータをカテゴリ無所属に変更し保存する
 * 
 * @param boolean $cascade
 * @return boolean
 * @access public
 */
	public function beforeDelete($cascade = true) {

	}

/**
 * 関連するページのカテゴリを解除する（再帰的）
 * 
 * @param int $categoryId
 * @return boolean
 * @access public
 */
	public function releaseRelatedPagesRecursive($categoryId) {

	}

/**
 * 関連するページのカテゴリを解除する
 * 
 * @param int $categoryId
 * @return boolean
 * @access public
 */
	public function releaseRelatedPages($categoryId) {

	}

/**
 * 関連するページデータのURLを更新する
 * 
 * @param string $id
 * @return void
 * @access public
 */
	public function updateRelatedPageUrlRecursive($categoryId) {

	}

/**
 * 関連するページデータのURLを更新する
 * 
 * @param string $id
 * @return void
 * @access public
 */
	public function updateRelatedPageUrl($id) {

	}

/**
 * カテゴリフォルダのパスから対象となるデータが存在するかチェックする
 * 存在する場合は id を返す
 * 
 * @param string $path カテゴリフォルダのパス
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getIdByPathDataProvider
 */
	public function testGetIdByPath($path, $expected, $message = null) {
		$path = getViewPath() . 'Pages' . $path;
		$result = $this->PageCategory->getIdByPath($path);
		$this->assertEquals($expected, $result, $message);
	}

	public function getIdByPathDataProvider() {
		return array(
			array('/mobile', 1, 'カテゴリフォルダのパスからページカテゴリーIDを取得できません'),
			array('/smartphone', 2, 'カテゴリフォルダのパスからページカテゴリーIDを取得できません'),
			array('/smartphone/garaphone', 3, 'カテゴリフォルダのパスから、親カテゴリをもったページカテゴリーIDを取得できません'),
		);
	}

/**
 * モバイル用のカテゴリIDをリストで取得する
 * 
 * @param boolean $top
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getAgentCategoryIdsDataProvider
 */
	public function testGetAgentCategoryIds($type, $top, $expected, $message = null) {
		$result = $this->PageCategory->getAgentCategoryIds($type, $top);
		$this->assertEquals($expected, $result, $message);
	}

	public function getAgentCategoryIdsDataProvider() {
		return array(
			array('mobile', true, array(1), 'モバイル用のカテゴリIDをリストで正しく取得できません'),
			array('mobile', false, array(), 'モバイル用のカテゴリIDをリストで正しく取得できません'),
			array('smartphone', true, array(2, 3), 'モバイル用の子カテゴリをもったカテゴリIDをリストで正しく取得できません'),
			array('smartphone', false, array(3), 'モバイル用のカテゴリIDをリストで正しく取得できません'),
			array('garaphone', true, array(3), 'モバイル用のカテゴリIDをリストで正しく取得できません'),
			array('garaphone', false, array(), 'モバイル用のカテゴリIDをリストで正しく取得できません'),
		);
	}

/**
 * エージェントカテゴリのIDを取得する
 * 
 * @param int $targetId
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getAgentIdDataProvider
 */
	public function testGetAgentId($type, $expected, $message = null) {
		$result = $this->PageCategory->getAgentId($type);
		$this->assertEquals($expected, $result, $message);
	}

	public function getAgentIdDataProvider() {
		return array(
			array('mobile', 1, 'エージェントカテゴリのIDを正しく取得できません'),
			array('smartphone', 2, 'エージェントカテゴリのIDを正しく取得できません'),
			array('garaphone', 3, 'エージェントカテゴリのIDを正しく取得できません'),
		);
	}

/**
 * PCのIDを元にモバイル・スマホの相対階層のIDを取得する
 * 
 * @param type $id
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getAgentRelativeIdDataProvider
 */
	public function testGetAgentRelativeId($type, $id, $expected, $message = null) {
		$result = $this->PageCategory->getAgentRelativeId($type, $id);
		$this->assertEquals($expected, $result, $message);
	}

	public function getAgentRelativeIdDataProvider() {
		return array(
			array('mobile', false, 1, 'モバイルの相対階層のIDを取得できません'),
			array('smartphone', false, 2, 'スマートフォンの相対階層のIDを取得できません'),
			array('garaphone', false, 3, 'ガラホの相対階層のIDを取得できません'),
			array('smartphone', 3, 3, 'PCのIDを元にモバイルの相対階層のIDを取得できません'),
		);
	}

/**
 * ツリーリストを取得する
 * 
 * @param array $fields　ページカテゴリーのフィールド名
 * @param int $id 指定したフィールドの値
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getTreeListDataProvider
 */
	public function testGetTreeList($fields, $id, $expected, $message = null) {

		if ($id != 3) { // ID:3は親カテゴリを持つ
			$expected = array( array( 'PageCategory' => $expected ));
		} else {
			$expected = array(
				0 => array( 'PageCategory' => $expected ),
				1 => array( 'PageCategory' => array('parent_id' => '2', 'id' => '3') ),
			);
		}

		$result = $this->PageCategory->getTreeList($fields, $id);
		$this->assertEquals($expected, $result, $message);
	}

	public function getTreeListDataProvider() {
		return array(
			array('id', 1, array('id' => 1), 'ツリーリストを取得できません'),
			array(array('parent_id', 'id'), 1, array('id' => '1', 'parent_id' => null), 'ツリーリストを取得できません'),
			array(array('parent_id', 'id'), 3, array('id' => '2', 'parent_id' => null), '親カテゴリをもったツリーリストを取得できません'),
			array('parent_id', 1, array('parent_id' => null), 'ツリーリストを取得できません'),
			array('name', 1, array('name' => 'mobile'), 'ツリーリストを取得できません'),
		);
	}

/**
 * 新しいカテゴリが追加できる状態かチェックする
 * 
 * @param int $userGroupId
 * @param boolean $rootEditable
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider checkNewCategoryAddableDataProvider
 */
	public function testCheckNewCategoryAddable($userGroupId, $rootEditable, $expected, $message = null) {
		$result = $this->PageCategory->checkNewCategoryAddable($userGroupId, $rootEditable);
		$this->assertEquals($expected, $result, $message);
	}

	public function checkNewCategoryAddableDataProvider() {
		return array(
			array(1, false, true, '新しいカテゴリが追加できる状態かチェックできません'),
			array(null, true, true, '新しいカテゴリが追加できる状態かチェックできません'),
			array(99, false, false, '新しいカテゴリが追加できる状態かチェックできません'),
			array(2, false, false, '新しいカテゴリが追加できる状態かチェックできません'),
		);
	}

/**
 * ページカテゴリーをコピーする
 * 
 * @param int $id ページカテゴリのID
 * @param array $data ページカテゴリーのデータ
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider copyDataProvider
 */
	public function testCopy($id, $data, $expected, $message = null) {
		$result = $this->PageCategory->copy($id, $data);
		$this->assertEquals($expected, $result['PageCategory']['name'], $message);
		@rmdir(BASER_THEMES . 'nada-icons/Pages/' . $expected);
	}

	public function copyDataProvider() {
		return array(
			array(1, array(), 'mobile_copy', '$id指定でページカテゴリーをコピーできません'),
			array(3, array(), 'garaphone_copy', '$id指定で親カテゴリを持ったページカテゴリーをコピーできません'),
			array(null,
						array(
							'PageCategory' => array(
								'name' => 'hoge',
								'title' => 'ほげ',
								'parent_id' => null,
							)
						),
						'hoge_copy',
						'$data指定でページカテゴリーをコピーできません'),
			array(null,
						array(
							'PageCategory' => array(
								'name' => 'hoge',
								'title' => 'ほげ',
								'parent_id' => 1,
							)
						),
						'hoge_copy',
						'$data指定で親カテゴリを持ったページカテゴリーをコピーできません'),
			array(null,
						array(
							'PageCategory' => array(
								'name' => null,
								'title' => null,
								'parent_id' => 1,
							)
						),
						'_copy',
						'$data指定でnameが空白のページカテゴリーをコピーできません'),
		);
	}

/**
 * ページカテゴリタイプを取得する
 * 1:PC / 2:ケータイ / 3:スマフォ
 * 
 * @param int $id
 * @param array $expected 期待値
 * @param string $message テストが失敗した時に表示されるメッセージ
 * @dataProvider getTypeDataProvider
 */
	public function testGetType($id, $expected, $message = null) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$result = $this->PageCategory->getType($id);
		$this->assertEquals($expected, $result, $message);
	}

	public function getTypeDataProvider() {
		return array(
			array(1, 1, 'ページカテゴリタイプを正しく取得できません'),
		);
	}
}
