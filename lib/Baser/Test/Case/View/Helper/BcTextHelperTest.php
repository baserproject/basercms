<?php

/**
 * test for BcTextHelper
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */
App::uses('View', 'View');
App::uses('BcTextHelper', 'View/Helper');
App::uses('BcTimeHelper', 'View/Helper');
App::uses('TextHelper', 'View/Helper');
App::uses('AppHelper', 'View/Helper');

/**
 * text helper library.
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcTextHelper $Helper
 */
class BcTextHelperTest extends BaserTestCase {

	public function setUp() {
		parent::setUp();
		$this->Helper = new BcTextHelper(new View(null));
	}

	public function tearDown() {
		unset($this->Helper);
		parent::tearDown();
	}

/**
 * boolean型を○―マークで出力するヘルパーのテスト
 *
 * */
	public function testBooleanMark() {
		$result = $this->Helper->booleanMark(true);
		$this->assertEquals("○", $result);

		$result = $this->Helper->booleanMark(false);
		$this->assertEquals("―", $result);
	}

/**
 * boolean型用のリストを有無で出力するヘルパーのテスト
 *
 * */
	public function testBooleanMarkList() {
		$result = $this->Helper->booleanMarkList();
		$expect = array(
			0 => "―",
			1 => "○"
		);
		$this->assertEquals($expect, $result);
	}

/**
 * boolean型用のリストを有無で出力するヘルパーのテスト
 * */
	public function testBooleanExistsList() {
		$result = $this->Helper->booleanExistsList();
		$expect = array(
			0 => "無",
			1 => "有"
		);
		$this->assertEquals($expect, $result);
	}

/**
 * boolean型用のリストを可、不可で出力するヘルパーのテスト
 * */
	public function testBooleanAllowList() {
		$result = $this->Helper->booleanAllowList();
		$expect = array(
			0 => "不可",
			1 => "可"
		);
		$this->assertEquals($expect, $result);
	}

/**
 * boolean型用のリストを[〜する/〜しない]形式でリスト出力するヘルパーのテスト
 */
	public function testBooleanDoList() {
		$result = $this->Helper->booleanDoList('baserCMSを');
		$expect = array(
			0 => 'baserCMSをしない',
			1 => 'baserCMSをする'
		);
		$this->assertEquals($expect, $result);
	}

/**
 * boolean型用のリストを[〜する/〜しない]形式で出力するヘルパーのテスト
 */
	public function testBooleanDo() {
		$result = $this->Helper->booleanDo(0, 'baserCMSを');
		$result2 = $this->Helper->booleanDo(1, 'baserCMSを');
		$this->assertEquals('baserCMSをしない', $result);
		$this->assertEquals('baserCMSをする', $result2);
	}

/**
 * 都道府県のリストを出力するヘルパーのテスト
 */
	public function testPrefList() {
		$expect = array(
			'' => '都道府県',
			1 => "北海道", 2 => "青森県", 3 => "岩手県", 4 => "宮城県", 5 => "秋田県", 6 => "山形県", 7 => "福島県",
			8 => "茨城県", 9 => "栃木県", 10 => "群馬県", 11 => "埼玉県", 12 => "千葉県", 13 => "東京都", 14 => "神奈川県",
			15 => "新潟県", 16 => "富山県", 17 => "石川県", 18 => "福井県", 19 => "山梨県", 20 => "長野県", 21 => "岐阜県",
			22 => "静岡県", 23 => "愛知県", 24 => "三重県", 25 => "滋賀県", 26 => "京都府", 27 => "大阪府", 28 => "兵庫県",
			29 => "奈良県", 30 => "和歌山県", 31 => "鳥取県", 32 => "島根県", 33 => "岡山県", 34 => "広島県", 35 => "山口県",
			36 => "徳島県", 37 => "香川県", 38 => "愛媛県", 39 => "高知県", 40 => "福岡県", 41 => "佐賀県", 42 => "長崎県",
			43 => "熊本県", 44 => "大分県", 45 => "宮崎県", 46 => "鹿児島県", 47 => "沖縄県"
		);
		$result = $this->Helper->prefList();
		$this->assertEquals($expect, $result);

		$expect2 = array(
			'' => '',
			1 => "北海道", 2 => "青森県", 3 => "岩手県", 4 => "宮城県", 5 => "秋田県", 6 => "山形県", 7 => "福島県",
			8 => "茨城県", 9 => "栃木県", 10 => "群馬県", 11 => "埼玉県", 12 => "千葉県", 13 => "東京都", 14 => "神奈川県",
			15 => "新潟県", 16 => "富山県", 17 => "石川県", 18 => "福井県", 19 => "山梨県", 20 => "長野県", 21 => "岐阜県",
			22 => "静岡県", 23 => "愛知県", 24 => "三重県", 25 => "滋賀県", 26 => "京都府", 27 => "大阪府", 28 => "兵庫県",
			29 => "奈良県", 30 => "和歌山県", 31 => "鳥取県", 32 => "島根県", 33 => "岡山県", 34 => "広島県", 35 => "山口県",
			36 => "徳島県", 37 => "香川県", 38 => "愛媛県", 39 => "高知県", 40 => "福岡県", 41 => "佐賀県", 42 => "長崎県",
			43 => "熊本県", 44 => "大分県", 45 => "宮崎県", 46 => "鹿児島県", 47 => "沖縄県"
		);

		$result2 = $this->Helper->prefList(false);
		$this->assertEquals($expect2, $result2);
	}

/**
 * 性別を出力するヘルパーのテスト
 */
	public function testSex() {
		$this->assertEquals('男', $this->Helper->sex(1));
		$this->assertEquals('女', $this->Helper->sex(2));
		$this->assertEquals('', $this->Helper->sex(0));
		$this->assertEquals('', $this->Helper->sex(3));
	}

/**
 * 郵便番号にハイフンをつけて出力するヘルパーのテスト
 */
	public function testZipFormat() {
		//ハイフン無し
		$result = $this->Helper->zipFormat('8190002');
		$expect = '〒 819-0002';
		$this->assertEquals($expect, $result);

		//ハイフン有り
		$result = $this->Helper->zipFormat('819-0002');
		$expect = '〒 819-0002';
		$this->assertEquals($expect, $result);

		//〒を無しにする
		$result = $this->Helper->zipFormat('8190002', null);
		$expect = '819-0002';
		$this->assertEquals($expect, $result);

		//〒を適当なマークにする
		$result = $this->Helper->zipFormat('8190002', '(^ ^)!');
		$expect = '(^ ^)!819-0002';
		$this->assertEquals($expect, $result);
	}

/**
 * 番号を都道府県に変換して出力するヘルパーのテスト
 */
	public function testPref() {
		$this->assertEquals('', $this->Helper->pref(0));
		$this->assertEquals('北海道', $this->Helper->pref(1));
		$this->assertEquals('沖縄県', $this->Helper->pref(47));
		$this->assertEquals('', $this->Helper->pref(48));
	}

/**
 * データをチェックして空の場合に指定した値を返すヘルパーのテスト
 */
	public function testNoValue() {
		// データあり
		$this->assertEquals('x', $this->Helper->noValue('x', 1));
		// データなし
		$this->assertEquals(1, $this->Helper->noValue('', 1));
	}

/**
 * boolean型用を可、不可で出力するヘルパーのテスト
 */
	public function testBooleanAllow() {

	}


/**
 * form::dateTimeで取得した和暦データを文字列データに変換するヘルパーのテスト
 */
	public function testDateTimeWareki() {

	}

/**
 * 通貨表示するヘルパーのテスト
 */
	public function testMoneyFormat() {

		// 適当な数値を入力
		$result = $this->Helper->moneyFormat('1234567');
		$expect = '¥1,234,567';
		$this->assertEquals($expect, $result);

		//¥を適当なマークにする
		$result = $this->Helper->moneyFormat('1234567', '(^ ^)!');
		$expect = '(^ ^)!1,234,567';
		$this->assertEquals($expect, $result);
	}

/**
 * form::dateTimeで取得したデータを文字列データに変換するヘルパーのテスト
 */
	public function dateTime() {

	}

/**
 * 文字をフォーマット形式で出力するヘルパーのテスト
 */
	public function testFormat() {

		// $valueが1の場合
		$result = $this->Helper->format('valueは%d', 1);
		$expect = 'valueは1';
		$this->assertEquals($expect, $result);
		
		// $valueが空の場合
		$result = $this->Helper->format('valueは%d', '');
		$expect = '';
		$this->assertEquals($expect, $result);

		// $valueがnullの場合
		$result = $this->Helper->format('valueは%d', null);
		$expect = '';
		$this->assertEquals($expect, $result);

		// $valueが空であり、返り値を変更する場合
		$result = $this->Helper->format('valueは%d', '', 'データは空です');
		$expect = 'データは空です';
		$this->assertEquals($expect, $result);

	}

/**
 * モデルのコントロールソースより表示用データを取得するヘルパーのテスト
 */
	public function listValue() {

	}

/**
 * 区切り文字で区切られたテキストを配列に変換するヘルパーのテスト
 */
	public function testToArray() {

		// $separatorが「,」の場合
		$result = $this->Helper->toArray(",", "a,i,u,e,o");
		$expect = array("a","i","u","e","o");
		$this->assertEquals($expect, $result);

		// $separatorが「"」の場合
		$result = $this->Helper->toArray('"', 'a"i"u"e"o');
		$expect = array("a","i","u","e","o");
		$this->assertEquals($expect, $result);

		// $separatorが「'」の場合
		$result = $this->Helper->toArray("'", "a'i'u'e'o");
		$expect = array("a","i","u","e","o");
		$this->assertEquals($expect, $result);
	}

/**
 * 配列とキーを指定して値を取得するヘルパーのテスト
 */
	public function testArrayValue() {

		// 適当な配列
		$array = array("a","i","u","e","o");

		// $keyが2
		$result = $this->Helper->arrayValue(2, $array);
		$expect = "u";
		$this->assertEquals($expect, $result);

		// $keyが5(存在しない値)
		$result = $this->Helper->arrayValue(5, $array);
		$expect = "";
		$this->assertEquals($expect, $result);	
	}

/**
 * 連想配列とキーのリストより値のリストを取得し文字列で返すヘルパーのテスト
 * 文字列に結合する際、指定した区切り文字を指定できる
 */
	public function arrayValues() {

	}

/**
 * 日付より年齢を取得するヘルパーのテスト
 */
	public function age() {

	}

/**
 * boolean型用のリストを有効、無効で出力するヘルパーのテスト
 */
	public function booleanStatusList() {

	}

/**
 * boolean型用を無効・有効で出力するヘルパーのテスト
 */
	public function booleanStatus() {

	}

}
