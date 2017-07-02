<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.Model
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
		'baser.Default.SiteConfig',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Favorite',
		'baser.Default.Permission',
		'baser.Default.SearchIndex',
		'baser.Default.Content'
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->BcApp = ClassRegistry::init('BcApp');
		$this->Page = ClassRegistry::init('Page');
		$this->SiteConfig = ClassRegistry::init('SiteConfig');
		$this->Dblog = ClassRegistry::init('Dblog');
		$this->User = ClassRegistry::init('User');
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		unset($this->BcApp);
		unset($this->Page);
		unset($this->SiteConfig);
		unset($this->Dblog);
		parent::tearDown();
	}

/**
 * beforeSave
 *
 * @return	boolean
 * @access	public
 */
	public function testBeforeSave() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$this->Page->save(array(
			'Page' => array(
				'name' => 'test',
				'page_category_id' => null,
				'title' => '',
				'url' => '',
				'description' => '',
				'status' => 1,
				'modified' => '',
			)
		));

		$LastID = $this->Page->getLastInsertID();
		$result = $this->Page->find('first', array(
				'conditions' => array('id' => $LastID),
				'fields' => array('created'),
				'recursive' => -1
			)
		);
		var_dump($result);
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

		$this->Page->save(array(
			'Page' => array(
				'name' => 'test',
				'page_category_id' => null,
				'title' => '',
				'url' => '',
				'description' => '',
				'status' => 1,
				'modified' => '',
			)
		));
		$now = date('Y-m-d H');

		$LastID = $this->Page->getLastInsertID();
		$result = $this->Page->find('first', array(
				'conditions' => array('id' => $LastID),
				'fields' => array('created','modified'),
				'recursive' => -1
			)
		);
		$created = date('Y-m-d H', strtotime($result['Page']['created']));
		$modified = date('Y-m-d H', strtotime($result['Page']['modified']));

		$message = 'created,modifiedを更新できません';
		$this->assertEquals($now, $created, $message);
		$this->assertEquals($now, $modified, $message);
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

	public function convertEncodingByArrayDataProvider() {
		return array(
			array(array("テスト1"), "ASCII", "SJIS"),
			array(array("テスト1", "テスト2"), "UTF-8", "SJIS"),
			array(array("テスト1", "テスト2"), "SJIS-win", "UTF-8"),
		);
	}

/**
 * データベースログを記録する
 */
	public function testSaveDbLog() {

		// Dblogにログを追加
		$message = 'テストです';
		$this->BcApp->saveDblog($message);

		// 最後に追加したログを取得
		$LastID = $this->Dblog->getLastInsertID();
		$result = $this->Dblog->find('first', array(
				'conditions' => array('Dblog.id' => $LastID),
				'fields' => 'name',
			)
		);
		$this->assertEquals($message, $result['Dblog']['name']);

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
		$path = BASER_CONFIGS . 'Schema';
		$this->BcApp->loadSchema('test', $path);
		var_dump($result);
		// $this->assertEquals($expect, $result);
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

	public function maxLengthDataProvider() {
		return array(
			array("あいう", 4, true),
			array("あいう", 3, true),
			array("あいう", 2, false),
			array(array("あいう", "あいうえお"), 4, true),
		);
	}

/**
 * 最大のバイト数チェック
 *
 * @param mixed $check
 * @param int $min
 * @param boolean $expect
 * @dataProvider maxByteDataProvider
 */
	public function testMaxByte($check, $min, $expect) {
		$result = $this->BcApp->maxByte($check, $min);
		$this->assertEquals($expect, $result);
	}

	public function maxByteDataProvider() {
		return array(
			array("あいう", 10, true),
			array("あいう", 9, true),
			array("あいう", 8, false)
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
		$result = $this->Page->getMax('Page\.id');
		$this->assertEquals(11, $result, '指定フィールドのMAX値を取得できません');
	}

/**
 * テーブルにフィールドを追加する
 */
	public function testAddField() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$options = array(
			'field' => 'testField',
			'column' => array(
				'name' => 'testColumn',
			),
			'table' => 'pages',
		);
		$this->Page->addField($options);
		$columns = $this->Page->getColumnTypes();
		var_dump($columns);
	}

/**
 * フィールド構造を変更する
 */
	public function testEditField() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
		$options = array(
			'field' => 'testField',
			'column' => array(
				'name' => 'testColumn',
			),
		);
		$this->BcApp->editField($options);
		$columns = $this->Page->getColumnTypes();
	}

/**
 * フィールド名を変更する
 */
	public function testRenameField() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


/**
 * フィールドを削除する
 */
	public function testDelField() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * テーブルの存在チェックを行う
 * 
 * @param string $tableName
 * @param boolean $expect
 * @dataProvider tableExistsDataProvider
 */
	public function testTableExists($tableName, $expect) {
		$db = ConnectionManager::getDataSource('default');
		$prefix = $db->config['prefix'];

		$result = $this->BcApp->tableExists($prefix . $tableName);
		$this->assertEquals($expect, $result);
	}

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
		$check = array('id' => 1);
		$result = $this->Page->duplicate($check);
		$this->assertEquals(false, $result);

		$check = array('id' => 100);
		$result = $this->Page->duplicate($check);
		$this->assertEquals(true, $result);
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

	public function halfTextDataProvider() {
		return array(
			array(array("test"), true),
			array(array("テスト"), false),
			array(array("test", "テスト"), true),
			array(array("テスト", "test"), false),
		);
	}

/**
 * Modelキャッシュを削除する
 */
	public function testDeleteModelCache() {
		$path = CACHE . 'models' . DS . 'dummy';

		// ダミーファイルをModelキャッシュフォルダに作成
		if (touch($path)) {
			$this->BcApp->deleteModelCache();
			$result = !file_exists($path);
			$this->assertTrue($result, 'Modelキャッシュを削除できません');

		} else {
			$this->markTestIncomplete('ダミーのキャッシュファイルの作成に失敗しました。');

		}
	}

/**
 * Key Value 形式のテーブルよりデータを取得して
 * １レコードとしてデータを展開する
 */
	public function testFindExpanded() {
		$result = $this->SiteConfig->findExpanded();

		$message = 'Key Value 形式のテーブルよりデータを取得して１レコードとしてデータを展開することができません';
		$this->assertEquals('baserCMS inc. [デモ]', $result['name'], $message);
		$this->assertEquals('baser,CMS,コンテンツマネジメントシステム,開発支援', $result['keyword'], $message);
	}

/**
 * Key Value 形式のテーブルにデータを保存する
 */
	public function testSaveKeyValue() {
		$data = array(
			'SiteConfig' => array(
				'test1' => 'テストです1',
				'test2' => 'テストです2',
			)
		);
		$this->SiteConfig->saveKeyValue($data);
		$result = $this->SiteConfig->findExpanded();

		$message = 'Key Value 形式のテーブルにデータを保存することができません';
		$this->assertEquals('テストです1', $result['test1'], $message);
		$this->assertEquals('テストです2', $result['test2'], $message);

	}

/**
 * リストチェック
 * 対象となる値がリストに含まれる場合はエラー
 * 
 * @param string $check 対象となる値
 * @param array $list リスト
 * @param boolean $expect
 * @dataProvider notInListDataProvider
 */
	public function testNotInList($check, $list, $expect) {
		$result = $this->BcApp->notInList($check, $list);
		$this->assertEquals($expect, $result);
	}

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
		$field = 'Page.contents';
		$data = array(
			'wareki' => true,
			'year' => 'h-27',
		);
		$result = $this->Page->deconstruct($field, $data);

		$expected = array(
			'wareki' => true,
			'year' => 2015
		);

		$this->assertEquals($expected, $result, 'deconstruct が 和暦に対応していません');
	}

/**
 * ２つのフィールド値を確認する
 * 
 * @param mixed $check 対象となる値
 * @param	mixed	$fields フィールド名
 * @param	mixed	$data 値データ
 * @param boolean $expected 期待値
 * @param boolean $message テストが失敗した場合に表示されるメッセージ
 * @dataProvider confirmDataProvider
 */
	public function testConfirm($check, $fields, $data, $expected, $message = null) {
		$this->BcApp->data['BcApp'] = $data;
		$result = $this->BcApp->confirm($check, $fields);
		$this->assertEquals($expected, $result, $message);

	}

	public function confirmDataProvider() {
		return array(
			array('', array('test1', 'test2'), array('test1' => 'value','test2' => 'value'), true, '2つのフィールドが同じ値の場合の判定が正しくありません'),
			array('', array('test1', 'test2'), array('test1' => 'value','test2' => 'other_value'), false, '2つのフィールドが異なる値の場合の判定が正しくありません'),
			array(array('value'=>'value'), 'test', array('test' => 'value'), true, 'フィールド名が一つで同じ値の場合の判定が正しくありません'),
			array(array('value'=>'value'), 'test', array('test' => 'other_value'), false, 'フィールド名が一つで異なる値の場合の判定が正しくありません'),
		);
	}

/**
 * 指定したモデル以外のアソシエーションを除外する
 *
 * @param array $auguments アソシエーションを除外しないモデル
 * @param array $expectedHasKey 期待する存在するキー
 * @param array $expectedNotHasKey 期待する存在しないキー
 * @dataProvider expectsDataProvider
 */
	public function testExpects($arguments, $expectedHasKeys, $expectedNotHasKeys) {
		$this->User->expects($arguments);
		$result = $this->User->find('first', ['recursive' => 1]);

		// 存在するキー
		foreach ($expectedHasKeys as $key) {
			$this->assertArrayHasKey($key, $result, '指定したモデル以外のアソシエーションを除外できません');
		}

		// 存在しないキー
		foreach ($expectedNotHasKeys as $key) {
			$this->assertArrayNotHasKey($key, $result, '指定したモデル以外のアソシエーションを除外できません');
		}
	}

	public function expectsDataProvider() {
		return array(
			array(array(), array('User'), array('UserGroup', 'Favorite')),
			array(array('UserGroup'), array('User', 'UserGroup'), array('Favorite')),
		);
	}

/**
 * 複数のEメールチェック（カンマ区切り）
 * 
 * @param array $check 複数のメールアドレス
 * @param boolean $expect
 * @dataProvider emailsDataProvider
 */
	public function testEmails($check, $expect) {
		$message = '複数のEメールのバリデーションチェックができません';
		$result = $this->BcApp->emails($check);
		$this->assertEquals($expect, $result, $message);
	}

	public function emailsDataProvider() {
		return array(
			array(array("test1@co.jp"), true),
			array(array("test1@co.jp,test2@cp.jp"), true),
			array(array("test1@cojp,test2@cp.jp"), false),
			array(array("test1@co.jp,test2@cpjp"), false),
		);
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
 * @param array $check 確認する値
 * @param boolean $expect
 * @dataProvider checkDateDataProvider
 */
	public function testCheckDate($check, $expect) {
		$result = $this->BcApp->checkDate($check);
		$this->assertEquals($expect, $result);	
	}

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
 * @param array $check ファイルのデータ
 * @param boolean $expect　
 * @dataProvider notFileEmptyDataProvider
 */
	public function testNotFileEmpty($check,$expect) {
		$file = array($check);
		$result = $this->BcApp->notFileEmpty($file);
		$this->assertEquals($expect, $result);
	}

	public function notFileEmptyDataProvider() {
		return array(
			array(array('size' => 0), false),
			array(array('size' => 100), true),
			array(array(), false),
		);
	}

/**
 * BcContentsRoute::getUrlPattern
 *
 * @param string $url URL文字列
 * @param string $expect 期待値
 * @return void
 * @dataProvider getUrlPatternDataProvider
 */
	public function testGetUrlPattern($url, $expects) {
		$this->assertEquals($expects, $this->BcApp->getUrlPattern($url));
	}

	public function getUrlPatternDataProvider() {
		return [
			['/news', ['/news']],
			['/news/', ['/news/', '/news/index']],
			['/news/index', ['/news/index', '/news/']],
			['/news/archives/1', ['/news/archives/1']],
			['/news/archives/index', ['/news/archives/index', '/news/archives/']]
		];
	}

}