<?php

/**
 * Model 拡張クラスのテスト
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('BcApp', 'Model');
/**
 * BcAppTest class
 * 
 * @package Baser.Test.Case.Model
 */

class BcAppTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.Page',
		'baser.Default.Dblog',
		'baser.Default.PageCategory',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->BcApp = ClassRegistry::init('BcApp');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Page);
		parent::tearDown();
	}

/**
 * beforeSave
 *
 * @return	boolean
 * @access	public
 */
	public function testBeforeSave($options = array()) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * Saves model data to the database. By default, validation occurs before save.
 *
 * @param	array	$data Data to save.
 * @param	boolean	$validate If set, validation will be done before the save
 * @param	array	$fieldList List of fields to allow to be written
 * @return	mixed	On success Model::$data if its not empty or true, false on failure
 */
	public function testSave($data = null, $validate = true, $fieldList = array()) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 配列の文字コードを変換する
 *
 * @param	array	変換前のデータ
 * @param	string	変換後の文字コード
 * @param	string 	変換元の文字コード
 * @dataProvider convertEncodingByArrayDataProvider
 */
	public function testConvertEncodingByArray($data, $outenc, $inenc) {
		$result = $this->BcApp->convertEncodingByArray($data, $outenc, $inenc);
		foreach ($result as $key => $value) {
			$encode = mb_detect_encoding($value);
			$this->assertEquals($outenc, $encode);
		}
	}

/**
 * convertEncodingByArray用データプロバイダ
 *
 * @return array
 */
	public function convertEncodingByArrayDataProvider() {
		return array(
			array(array("テスト1"), "ASCII", "SJIS"),
			array(array("テスト1", "テスト2"), "UTF-8", "SJIS"),
			array(array("テスト1", "テスト2"), "SJIS-win", "UTF-8"),
		);
	}

/**
 * データベースログを記録する
 *
 * @param  string $message
 * @dataProvider saveDbLogDataProvider
 */
	public function testSaveDbLog($message) {
		$this->markTestIncomplete('このテストは、まだ完成されていません。フィクスチャが利用できないためスキップしています。');

		$expect = array(
			"Dblog" => array(
				)
			);

		$result = $this->BcApp->saveDblog($message);
		$this->assertEquals($expect, $result);

	}

/**
 * convertEncodingByArray用データプロバイダ
 *
 * @return array
 */
	public function saveDbLogDataProvider() {
		return array(
			array("テスト"),
		);
	}

/**
 * コントロールソースを取得する
 *
 * 継承先でオーバーライドする事
 */
	public function testGetControlSource() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 子カテゴリのIDリストを取得する
 *
 * treeビヘイビア要
 *
 * @return 	array
 */
	public function testGetChildIdsList() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 機種依存文字の変換処理
 *
 * @param string 変換対象文字列
 * @param string 変換後予想文字列
 * @dataProvider replaceTextDataProvider
 */
	public function testReplaceText($str, $expect) {
		$result = $this->BcApp->replaceText($str);
		$this->assertEquals($expect, $result);
	}

/**
 * replaceText用データプロバイダ
 *
 * @return array
 */
	public function replaceTextDataProvider() {
		return array(
			array("\xE2\x85\xA0", "I"),
			array("\xE2\x91\xA0", "(1)"),
			array("\xE3\x8D\x89", "ミリ"),
			array("\xE3\x88\xB9", "(代)"),
		);
	}

/**
 * データベースを初期化
 */
	public function testInitDb() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * スキーマファイルを利用してデータベース構造を変更する
 */
	public function testLoadSchema() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * CSVを読み込む
 */
	public function testLoadCsv() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 最短の長さチェック
 *
 * @param mixed $check
 * @param int $min
 * @param boolean $expect
 * @dataProvider minLengthDataProvider
 */
	public function testMinLength($check, $min, $expect) {
		$result = $this->BcApp->minLength($check, $min);
		$this->assertEquals($expect, $result);
	}

/**
 * minLength用データプロバイダ
 *
 * @return array
 */
	public function minLengthDataProvider() {
		return array(
			array("あいう", 4, false),
			array("あいう", 3, true),
			array(array("あいう", "あいうえお"), 4, false),
		);
	}
/**
 * 最長の長さチェック
 *
 * @param mixed $check
 * @param int $min
 * @param boolean $expect
 * @dataProvider maxLengthDataProvider
 */
	public function testMaxLength($check, $min, $expect) {
		$result = $this->BcApp->maxLength($check, $min);
		$this->assertEquals($expect, $result);
	}

/**
 * maxLength用データプロバイダ
 *
 * @return array
 */
	public function maxLengthDataProvider() {
		return array(
			array("あいう", 4, true),
			array("あいう", 3, true),
			array("あいう", 2, false),
			array(array("あいう", "あいうえお"), 4, true),
		);
	}


/**
 * 範囲を指定しての長さチェック
 *
 * @param mixed $check
 * @param int $min
 * @param int $max
 * @param boolean $expect
 * @dataProvider betweenDataProvider
 */
	public function testBetween($check, $min, $max, $expect) {
		$result = $this->BcApp->between($check, $min, $max);
		$this->assertEquals($expect, $result);
	}

/**
 * between用データプロバイダ
 *
 * @return array
 */
	public function betweenDataProvider() {
		return array(
			array("あいう", 2, 4, true),
			array("あいう", 3, 3, true),
			array("あいう", 4, 3, false),
			array(array("あいう", "あいうえお"), 2, 4, true),
		);
	}

/**
 * 指定フィールドのMAX値を取得する
 */
	public function testGetMax() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * テーブルにフィールドを追加する
 */
	public function testAddField() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * フィールド構造を変更する
 */
	public function testEditField() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * テーブルの存在チェックを行う
 * @param string $tableName
 * @param boolean $expect
 * @dataProvider tableExistsDataProvider
 */
	public function testTableExists($tableName, $expect) {
		$db = ConnectionManager::getDataSource('baser');
		$prefix = $db->config['prefix'];

		$result = $this->BcApp->tableExists($prefix . $tableName);
		$this->assertEquals($expect, $result);
	}

/**
 * tableExists用データプロバイダ
 *
 * @return array
 */
	public function tableExistsDataProvider() {
		return array(
			array("users", true),
			array("notexist", false),
		);
	}


/**
 * 英数チェック
 *
 * @param string $check チェック対象文字列
 * @param boolean $expect 
 * @dataProvider alphaNumericDataProvider
 */
	public function testAlphaNumeric($check, $expect) {
		$result = $this->BcApp->alphaNumeric($check);
		$this->assertEquals($expect, $result);		
	}

/**
 * alphaNumeric用データプロバイダ
 *
 * @return array
 */
	public function alphaNumericDataProvider() {
		return array(
			array(array("aiueo"), true),
			array(array("12345"), true),
			array(array("あいうえお"), false),
		);
	}

/**
 * データの重複チェックを行う
 */
	public function testDuplicate() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ファイルサイズチェック
 * 
 * @param string $fileName チェック対象ファイル名
 * @param string $fileSize チェック対象ファイルサイズ
 * @param boolean $expect
 * @dataProvider fileSizeDataProvider
 */
	public function testFileSize($fileName, $fileSize, $expect) {
		$check = array(
			array (
				"name" => $fileName,
				"size" => $fileSize,
			)
		);
		$size = 1000;

		$result = $this->BcApp->fileSize($check, $size);
		$this->assertEquals($expect, $result);		
	}

/**
 * fileSize用データプロバイダ
 *
 * @return array
 */
	public function fileSizeDataProvider() {
		return array(
			array("test.jpg", 1000, true),
			array("test.jpg", 1001, false),
			array("", 1000, true),
			array("test.jpg", null, false),
		);
	}

/**
 * ファイルの拡張子チェック
 * 
 * @param string $fileName チェック対象ファイル名
 * @param string $fileSize チェック対象ファイルタイプ
 * @param boolean $expect
 * @dataProvider fileExtDataProvider
 */
	public function testFileExt($fileName, $fileType, $expect) {
		$check = array(
			array (
				"name" => $fileName,
				"type" => $fileType,
			)
		);
		$ext = "jpg,png";

		$result = $this->BcApp->fileExt($check, $ext);
		$this->assertEquals($expect, $result);		
	}

/**
 * fileExt用データプロバイダ
 *
 * @return array
 */
	public function fileExtDataProvider() {
		return array(
			array("test.jpg", "image/jpeg", true),
			array("test.png", "image/png", true),
			array("test.gif", "image/gif", false),
			array("test", "image/png", true),
		);
	}

/**
 * 半角チェック
 * 
 * @param array $check
 * @param boolean $expect
 * @dataProvider halfTextDataProvider
 */
	public function testHalfText($check, $expect) {
		$result = $this->BcApp->halfText($check);
		$this->assertEquals($expect, $result);
	}

/**
 * halfText用データプロバイダ
 *
 * @return array
 */
	public function halfTextDataProvider() {
		return array(
			array(array("test"), true),
			array(array("テスト"), false),
			array(array("test", "テスト"), true),
			array(array("テスト", "test"), false),
		);
	}

/**
 * 一つ位置を上げる
 */
	public function testSortup() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

	}

/**
 * 一つ位置を下げる
 */
	public function testSortdown() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 並び順を変更する
 */
	public function testChangeSort() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * Modelキャッシュを削除する
 */
	public function testDeleteModelCache() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * Key Value 形式のテーブルよりデータを取得して
 * １レコードとしてデータを展開する
 */
	public function testFindExpanded() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * Key Value 形式のテーブルにデータを保存する
 */
	public function testSaveKeyValue() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * リストチェック
 * リストに含む場合はエラー
 * 
 * @param string $check Value to check
 * @param array $list List to check against
 * @param boolean $expect
 * @dataProvider notInListDataProvider
 */
	public function testNotInList($check, $list, $expect) {
		$result = $this->BcApp->notInList($check, $list);
		$this->assertEquals($expect, $result);
	}

/**
 * notInList用データプロバイダ
 *
 * @return array
 */
	public function notInListDataProvider() {
		return array(
			array(array("test1"), array("test1", "test2"), false),
			array(array("test3"), array("test1", "test2"), true),
		);
	}

/**
 * Deconstructs a complex data type (array or object) into a single field value.
 */
	public function testDeconstruct() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ２つのフィールド値を確認する
 */
	public function testConfirm() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 指定したモデル以外のアソシエーションを除外する
 */
	public function testExpects() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');		
	}

/**
 * 複数のEメールチェック（カンマ区切り）
 * 
 * @param array $check 複数のメールアドレス
 * @param boolean $expect
 * @dataProvider emailsDataProvider
 */
	public function testEmails($check, $expect) {
		$result = $this->BcApp->emails($check);
		$this->assertEquals($expect, $result);
	}

/**
 * emails用データプロバイダ
 *
 * @return array
 */
	public function emailsDataProvider() {
		return array(
			array(array("test1@co.jp"), true),
			array(array("test1@co.jp,test2@cp.jp"), true),
			array(array("test1@cojp,test2@cp.jp"), false),
			array(array("test1@co.jp,test2@cpjp"), false),
		);
	}

/**
 * Deletes multiple model records based on a set of conditions.
 */
	public function testDeleteAll() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * Updates multiple model records based on a set of conditions.
 */
	public function testUpdateAll() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * Used to report user friendly errors.
 * If there is a file app/error.php or app/app_error.php this file will be loaded
 * error.php is the AppError class it should extend ErrorHandler class.
 */
	public function testCakeError() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * Queries the datasource and returns a result set array.
 */
	public function testFind() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * イベントを発火
 */
	public function testDispatchEvent() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * データが公開済みかどうかチェックする
 *
 * @param boolean $status 公開ステータス
 * @param string $publishBegin 公開開始日時
 * @param string $publishEnd 公開終了日時
 * @param boolean $expect
 * @dataProvider isPublishDataProvider
 */
	public function testIsPublish($status, $publishBegin, $publishEnd, $expect) {
		$result = $this->BcApp->isPublish($status, $publishBegin, $publishEnd);
		$this->assertEquals($expect, $result);
	}

/**
 * isPublish用データプロバイダ
 *
 * @return array
 */
	public function isPublishDataProvider() {
		return array(
			array(true, null, null, true),
			array(false, null, null, false),
			array(true, '2015-01-01 00:00:00', null, true),
			array(true, '3000-01-01 00:00:00', null, false),
			array(true, null, '2015-01-01 00:00:00', false),
			array(true, null, '3000-01-01 00:00:00', true),
			array(true, '2015-01-01 00:00:00', '3000-01-01 00:00:00', true),
			array(true, '2015-01-01 00:00:00', '2015-01-02 00:00:00', false),
		);
	}

/**
 * 日付の正当性チェック
 * 
 * @param array $check
 * @param boolean $expect
 * @dataProvider checkDateDataProvider
 */
	public function testCheckDate($check, $expect) {
		$result = $this->BcApp->checkDate($check);
		$this->assertEquals($expect, $result);	
	}

/**
 * checkDate用データプロバイダ
 *
 * @return array
 */
	public function checkDateDataProvider() {
		return array(
			array(array('2015-01-01'), true),
			array(array('201511'), false),
			array(array('2015-01-01 00:00:00'), true),
			array(array('2015-0101 00:00:00'), false),
			array(array('1970-01-01 09:00:00'), false),
		);
	}


/**
 * ツリーより再帰的に削除する
 */
	public function testRemoveFromTreeRecursive() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * ファイルが送信されたかチェックするバリデーション
 * 
 * @param array $check
 * @param boolean $expect
 * @dataProvider notFileEmptyDataProvider
 */
	// public function testNotFileEmpty($check,$expect) {
	// 	$file = array(

	// 	);
	// 	$result = $this->BcApp->notFileEmpty($file);
	// 	$this->assertEquals($expect, $result);
	// }

/**
 * checkDate用データプロバイダ
 *
 * @return array
 */
	// public function notFileEmptyDataProvider() {
	// 	return array(
	// 		array(array(), false),
	// 	);
	// }


}