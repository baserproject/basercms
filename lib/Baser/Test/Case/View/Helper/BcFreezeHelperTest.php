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

App::uses('BcFreezeHelper', 'View/Helper');

/**
 * FormHelperTest class
 *
 * @property BcFormHelper $Form
 */
class BcFreezeHelperTest extends BaserTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		Configure::write('Config.language', 'jp');
		Configure::write('App.base', '');
		Configure::delete('Asset');
		$this->BcFreeze = new BcFreezeHelper(new View);
		$this->BcFreeze->request = new CakeRequest('contacts/add', false);
		$this->BcFreeze->request->here = '/contacts/add';
		$this->BcFreeze->request['action'] = 'add';
		$this->BcFreeze->request->webroot = '';
		$this->BcFreeze->request->base = '';
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->BcFreeze);
	}
	
/**
 * テキストボックスを表示する
 * 
 * @param boolean $freezed フォームを凍結させる
 * @param string $fieldName フィールド文字列
 * @param array $attributes html属性
 * @param string $expexted 期待値(htmlタグ)
 * @dataProvider textDataProvider
 */
	public function testText($freezed, $fieldName, $attributes, $expected) {

		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();
			list($model, $field) = explode('.', $fieldName);
			$this->BcFreeze->request->data[$model][$field] = 'BaserCMS';
		}

		$result = $this->BcFreeze->text($fieldName, $attributes);
		$this->assertEquals($expected, $result);
	}

	public function textDataProvider() {
		return array(
			array(false, 'baser', array(), '<input name="data[baser]" type="text" id="baser"/>'),
			array(false, 'baser', array('class' => 'bcclass'), '<input name="data[baser]" class="bcclass" type="text" id="baser"/>'),
			array(false, 'baser', array('class' => 'bcclass', 'id' => 'bcid'), '<input name="data[baser]" class="bcclass" id="bcid" type="text"/>'),
			array(false, 'baser', array('type' => 'hidden'), '<input name="data[baser]" type="hidden" id="baser"/>'),
			array(true, 'baser.freezed', array(), '<input name="data[baser][freezed]" type="hidden" value="BaserCMS" id="baserFreezed"/>BaserCMS'),
			array(true, 'baser.freezed', array('value' => 'BaserCMS2'), '<input name="data[baser][freezed]" value="BaserCMS2" type="hidden" id="baserFreezed"/>BaserCMS2'),
			array(true, 'baser.freezed', array('type' => 'hidden'), '<input name="data[baser][freezed]" type="hidden" value="BaserCMS" id="baserFreezed"/>BaserCMS'),
		);
	}

/**
 * select プルダウンメニューを表示
 * 
 * @param boolean $freezed フォームを凍結させる
 * @param	string $fieldName フィールド文字列
 * @param	array $options コントロールソース
 * @param	array $attributes html属性
 * @param	array	空データの表示有無
 * @param string $expexted 期待値(htmlタグ)
 * @dataProvider selectDataProvider
 */
	public function testSelect($freezed, $fieldName, $options, $attributes, $expected) {

		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();
		}

		$result = $this->BcFreeze->select($fieldName, $options, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function selectDataProvider() {
		return array(
			array(false, 'baser', array(), array(), "<select name=\"data\[baser\]\" id=\"baser\">.<option value=\"\">"),
			array(false, 'baser', array('1' => 'ラーメン'), array(), '<option value="1">ラーメン'),
			array(false, 'baser', array('1' => 'ラーメン', '2' => '寿司'), array(), '<option value="1">ラーメン.*<option value="2">寿司'),
			array(false, 'baser', array(), array('class' => 'bcclass'), "<select name=\"data\[baser\]\" class=\"bcclass\" id=\"baser\">"),
			array(false, 'baser', array('1' => 'ラーメン'), array('class' => 'bcclass'), 'class="bcclass".*<option value="1">ラーメン'),
			array(false, 'baser', array('1' => 'ラーメン', '2' => '寿司'), array('cols' => 10), 'cols="10".*<option value="1">ラーメン　　　　　　.*<option value="2">寿司　　　　　　　　'),
			array(true, 'baser.freezed', array('1' => 'ラーメン'), array('class' => 'bcclass'), "<input type=\"hidden\" name=\"data\[baser\]\[freezed\]\" class=\"bcclass\" id=\"baserFreezed\""),
	 );
	}


/**
 * 日付タグを表示
 * 
 * @param boolean $freezed フォームを凍結させる
 * @param	string $fieldName フィールド文字列
 * @param	string $dateFormat 日付フォーマット
 * @param	string $timeFormat 時間フォーマット
 * @param	array	$attributes html属性
 * @param string $expexted 期待値
 * @dataProvider dateTimeDataProvider
 */
	public function testDateTime($freezed, $fieldName, $dateFormat, $timeFormat, $attributes, $expected) {
		
		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();
		}

		$result = $this->BcFreeze->dateTime($fieldName, $dateFormat, $timeFormat, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function dateTimeDataProvider() {
		return array(
			array(false, 'test', 'YMD', '12', array(), 'id="testYear".*id="testMonth".*id="testDay".*id="testHour".*id="testMin".*id="testMeridian"'),
			array(false, 'test', 'DMY', '12', array(), 'id="testDay".*id="testMonth".*id="testYear"'),
			array(false, 'test', 'YMD', '24', array(), '59<\/option>.<\/select>$'),
			array(false, 'test', 'YMD', '12', array('class' => 'bcclass'), 'class="bcclass"'),
			array(false, 'test', 'YMD', '12', array('empty' => false), '^(?!value="").*$'),
			array(false, 'test', 'YMD', '12', array('empty' => array('day' => '選択されていません')), '<option value="">選択されていません'),
			array(true, 'test', 'YMD', '12', array(), 'type="hidden"'),
			array(true, 'test', 'YMD', '12', array('selected' => array('year' => '2010', 'month' => '4', 'day' => '1')), '2010年.*4月.*1日'),
			array(true, 'test', 'YMD', '12', array('selected' => '2010-4-1 11:22:33'), '2010年.*4月.*1日.*11時.*22分'),
			array(true, 'test', 'YMD', '12', array('selected' => array('day' => '100')), 'value="100"'),
			array(true, 'test', 'YMD', '12', array('empty' => true), '^((?!value="").)*$'),
		);
	}

/**
 * 和暦年
 *
 * @param boolean $freezed フォームを凍結させる
 * @param string $fieldName Prefix name for the SELECT element
 * @param integer $minYear First year in sequence
 * @param integer $maxYear Last year in sequence
 * @param string $selected Option which is selected.
 * @param array $attributes Attribute array for the select elements.
 * @param boolean $showEmpty Show/hide the empty select option
 * @param string $expexted 期待値
 * @dataProvider wyearDataProvider
 */
	public function testWyear($freezed, $fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty, $expected) {

		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();

		}

		$result = $this->BcFreeze->wyear($fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function wyearDataProvider() {
		return array(
			array(false, 'test', null, null, null, array(), true, 'id="testYear">.*<option value="h-47">'),
			array(false, 'test', 2010, null, null, array(), true, '<option value="h-22">平成 22<\/option>.<\/select>$'),
			array(false, 'test', null, 2010, null, array(), true, '<option value=""><\/option>.<option value="h-22">'),
			array(false, 'test', null, null, 'h-47', array(), true, 'value="h-47" selected="selected"'),
			array(false, 'test', null, null, null, array('type' => 'hidden'), true, 'type="hidden"'),
			array(false, 'test', null, null, null, array(), false, 'id="testYear">.*<option value="h-47">'),
			array(true, 'test', null, null, null, array(), true, 'type="hidden"'),
			array(true, 'test', null, null, '2035-1-1', array(), true, '平成 47'),
		);
	}

/**
 * チェックボックスを表示する
 * 
 * @param boolean $freezed フォームを凍結させる
 * @param string $fieldName フィールド文字列
 * @param title $title タイトル
 * @param array $attributes html属性
 * @param string $expexted 期待値
 * @dataProvider checkboxDataProvider
 */
	public function testCheckbox($freezed, $fieldName, $attributes, $expected) {

		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();
		}

		$result = $this->BcFreeze->checkbox($fieldName, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function checkboxDataProvider() {
		return array(
			array(false, 'baser', array(), "<input type=\"hidden\" name=\"data\[baser\]\" id=\"baser_\" value=\"0\"\/><input type=\"checkbox\" name=\"data\[baser\]\" value=\"1\" id=\"baser\""),
			array(false, 'baser', array('class' => 'bcclass'), 'class="bcclass"'),
			array(true, 'baser.freezed', array(), "name=\"data\[baser\]\[freezed\]\".*id=\"baserFreezed\""),
			array(true, 'baser.freezed', array('label' => 'test'), 'label="test"'),
		);
	}

/**
 * テキストエリアを表示する
 * 
 * @param boolean $freezed フォームを凍結させる
 * @param string フィールド文字列
 * @param array html属性
 * @param string $expexted 期待値
 * @dataProvider textareaDataProvider
 */
	public function testTextarea($freezed, $fieldName, $attributes, $expected) {

		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();
			list($model, $field) = explode('.', $fieldName);
			$this->BcFreeze->request->data[$model][$field] = 'BaserCMS';
		}

		$result = $this->BcFreeze->textarea($fieldName, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function textareaDataProvider() {
		return array(
			array(false, 'baser', array(), "<textarea name=\"data\[baser\]\" id=\"baser\"><\/textarea>"),
			array(false, 'baser', array('class' => 'bcclass'), 'class="bcclass"'),
			array(true, 'baser.freezed', array(), 'value="BaserCMS"'),
			array(true, 'baser.freezed', array('value' => 'BaserCMS2'), 'value="BaserCMS2"'),
			array(true, 'baser.freezed', array('class' => 'bcclass'), 'class="bcclass"'),
		);
	}

/**
 * ラジオボタンを表示する
 * 
 * @param boolean $freezed フォームを凍結させる
 * @param string $fieldName フィールド文字列
 * @param array $options コントロールソース
 * @param array $attributes html属性
 * @param string $expexted 期待値
 * @dataProvider radioDataProvider
 */
	public function testRadio($freezed, $fieldName, $options, $attributes, $expected) {

		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();
		}

		$result = $this->BcFreeze->radio($fieldName, $options, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function radioDataProvider() {
		return array(
			array(false, 'baser', array(), array(), "<input type=\"hidden\" name=\"data\[baser\]\" id=\"baser_\" value=\"\""),
			array(false, 'baser', array('test1' => 'testValue1'), array(), 'id="baserTest1".*for="baserTest1">testValue1'),
			array(false, 'baser', array('test1' => 'testValue1'), array('class' => 'bcclass'), 'class="bcclass"'),
			array(true, 'baser.freezed', array(), array(), 'type="hidden"'),
			array(true, 'baser.freezed', array('test1' => 'testValue1'), array('class' => 'bcclass'), 'class="bcclass"'),
		);
	}

/**
 * ファイルタグを出力
 * 
 * MEMO : 3番目のテストは、以下のエラーに対応できなかったためスキップしています。
 * BcUploadHelper を利用するには、モデルで BcUploadBehavior の利用設定が必要です。
 * 
 * @param boolean $freezed フォームを凍結させる
 * @param string $fieldName
 * @param array $options
 * @param string $expexted 期待値
 * @dataProvider fileDataProvider
 */
	public function testFile($freezed, $fieldName, $options, $expected) {

		// 凍結させる
		if($freezed) {
			// $this->markTestIncomplete('このテストは、一部未完成です。');
			$this->BcFreeze->freeze();
			$this->EditorTemplate = ClassRegistry::init('EditorTemplate');
			$this->EditorTemplate->BcFreeze = new BcFreezeHelper(new View);
			$result = $this->EditorTemplate->BcFreeze->file($fieldName, $options);
			$this->assertRegExp('/' . $expected . '/s', $result);

		
		} else {
			$result = $this->BcFreeze->file($fieldName, $options);
			$this->assertRegExp('/' . $expected . '/s', $result);
		
		}

	}

	public function fileDataProvider() {
		return array(
			array(false, 'baser', array(), '<input type="file" name="data\[baser\]" id="baser"'),
			array(false, 'baser', array('size' => 100), 'size="100"'),
			array(true, 'baser.freezed', array(), '<input type="file" name="data\[baser\]\[freezed\]" id="baserFreezed"\/>'),
		);
	}

/**
 * ファイルコントロール（画像）を表示する
 * TODO 確認画面には未チェック
 * 
 * @param boolean $freezed フォームを凍結させる
 * @param string $name $this->request->data[$model][$field]['name']に格納する値
 * @param string $exist フィールド文字列 $this->request->data[$model][$field . '_exists'] に格納する値
 * @param string $fieldName フィールド文字列
 * @param	array $attributes html属性
 * @param array $imageAttributes 画像属性
 * @param string $expexted 期待値
 * @dataProvider imageDataProvider
 */
	public function testImage($freezed, $name, $exist, $fieldName, $attributes, $imageAttributes, $expected) {

		list($model, $field) = explode('.', $fieldName);
		$this->BcFreeze->request->data[$model][$field]['name'] = $name;
		$this->BcFreeze->request->data[$model][$field . '_exists'] = $exist;
		
		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();

		}

		$result = $this->BcFreeze->image($fieldName, $attributes, $imageAttributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function imageDataProvider() {
		return array(
			array(false, null, null, 'test.image', array(), array(), '<input type="file" name="data\[test\]\[image\]" id="testImage"'),
			array(false, null, null, 'test.image', array('size' => 100), array(), 'size="100"'),
			array(false, null, 'testexist', 'test.image', array(), array(), 'src="\/\/tests.*label="削除する"'),
			array(false, null, 'testexist', 'test.image', array(), array('dir'=>'testdir'), 'src="\/testdir\/tests'),
			array(true, null, null, 'test.image', array(), array(), '&nbsp;'),
			array(true, 'testname', null, 'test.image', array(), array(), 'id="testImageExists".*src="tmp\/test\/img'),
			array(true, null, null, 'test.image', array(), array('alt' => 'testalt'), '&nbsp;'),
			array(true, null, 'testexist', 'test.image', array(), array(), 'dir=""'),
			array(true, null, 'testexist', 'test.image', array(), array('dir'=>'testdir'), 'dir="testdir"'),
		);
	}

/**
 * JsonList
 * TODO 確認画面用の実装は全くしてない
 * 
 * @param boolean $freezed フォームを凍結させる
 * @param array $data 凍結させたjsonのデータ
 * @param string $fieldName フィールド文字列
 * @param array $attributes html属性
 * @param string $expexted 期待値
 * @dataProvider jsonListDataProvider
 */
	public function testJsonList($freezed, $data, $fieldName, $attributes, $expected) {

		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();
			$this->BcFreeze->request->data[$fieldName] = $data;
		}

		// indexを作る
		$attributes_default = array(
			'imgSrc' => null,
			'ajaxAddAction' => null,
			'ajaxDelAction' => null,
		);
		$attributes = $attributes + $attributes_default;

		$result = $this->BcFreeze->jsonList($fieldName, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function jsonListDataProvider() {
		return array(
			array(false, null, 'baser', array(), 'id="JsonBaserDb".*jQuery\(function\(\)'), 
			array(false, array(), 'baser', array('ajaxAddAction' => 'test'), '"ajaxAddAction":"test"'), 
			array(false, array(), 'baser', array('ajaxDelAction' => 'test'), '"ajaxDelAction":"test"'), 
			array(true, array(array('name' => 'test')), 'baser', array(), '<li>test'), 
			array(true, array(array('name' => 'test1'), array('name' =>'test2')), 'baser', array(), '<li>test1.*<li>test2'), 
			array(true, null, 'baser', array(), '^$'), 
		);
	}


/**
 * カレンダーコントロール付きのテキストフィールド
 * jquery-ui-1.7.2 必須
 * 
 * @param boolean $freezed フォームを凍結させる
 * @param string $date 凍結させた日時
 * @param string $fieldName フィールド文字列
 * @param array $attributes HTML属性
 * @param string $expexted 期待値
 * @dataProvider datepickerDataProvider
 */
	public function testDatepicker($freezed, $date, $fieldName, $attributes, $expected) {

		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();
			list($model, $field) = explode('.', $fieldName);
			$this->BcFreeze->request->data[$model][$field] = $date;
		}

		$result = $this->BcFreeze->datepicker($fieldName, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function datepickerDataProvider() {
		return array(
			array(false, null, 'baser', array(), 'type="text".*id="baser".*("#baser")'), 
			array(false, null, 'baser', array('test1' => 'testValue1'), 'test1="testValue1"'),
			array(true, '2015-4-1', 'baser.freezed', array(), 'type="hidden".*2015\/04\/01'),
			array(true, null,'baser.freezed', array(), '>$'),
			array(true, null,'baser.freezed', array('test1' => 'testValue1'), 'test1="testValue1"'),
		);
	}

/**
 * 凍結時用のコントロールを取得する
 * 
 * MEMO : freezeControlの547行目~559行目あたりのテストが実装されていません。
 * if (!empty($attributes["multiple"]) && $attributes["multiple"] !== 'checkbox') { ...
 * 
 * @param	string	フィールド文字列
 * @param	array	コントロールソース
 * @param	array	html属性
 * @param string $expexted 期待値
 * @dataProvider freezeControllDataProvider
 */
	public function testFreezeControll($fieldName, $options, $attributes, $expected) {
		$result = $this->BcFreeze->freezeControll($fieldName, $options, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function freezeControllDataProvider() {
		return array(
			array('baser.freezed', array(), array(), '<input type="hidden" name="data\[baser\]\[freezed\]" class="" id="baserFreezed"'),
			array('baser.freezed', array(), array('value' => 'BaserCMS'), 'value="BaserCMS"'),
			array('baser.freezed', array(), array('value' => 'BaserCMS', 'multiple' => 'select'), 'value="BaserCMS"\/>BaserCMS'),
			array('baser.freezed', array(), array('value' => 'BaserCMS', 'multiple' => 'checkbox'), 'value="BaserCMS"\/>BaserCMS'),
			array('baser.freezed', array('1' => 'BaserCMS1','2' => 'BaserCMS2','3' => 'BaserCMS3',), array('value' => array(1,2,3), 'multiple' => 'checkbox'), '<li>BaserCMS1.*<li>BaserCMS2.*<li>BaserCMS3.*value="1".*value="2".*value="3"'),
			array('baser.freezed', array('1' => 'BaserCMS1'), array('value' => array(1), 'multiple' => 'hoge'), '<input type="hidden" name="data\[baser\]\[freezed\]\[\]"  class="" value="1" \/><ul class="" value="1"  ><\/ul>'),
			array('baser.freezed', array('1' => 'BaserCMS1','2' => 'BaserCMS2','3' => 'BaserCMS3',), array('value' => array(1,2,3), 'multiple' => 'checkbox'), '<li>BaserCMS1.*<li>BaserCMS2.*<li>BaserCMS3.*value="1".*value="2".*value="3"')
		);
	}

/**
 * @param $fieldName
 * @param $options
 * @param $expected
 * @dataProvider uploadProvider
 */
	public function testUpload($fieldName, $options, $expected) {
		$result = $this->BcFreeze->upload($fieldName, $options);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function uploadProvider() {
		return array(
			array('hoge', array(), '<input name="data\[hoge\]" type="upload" id="hoge"\/>'),
			array('hoge', array('type' => 'gege'), '<input name="data\[hoge\]" type="gege" id="hoge"\/>'),
			array('hoge', array('class' => 'gege'), '<input name="data\[hoge\]" class="gege" type="upload" id="hoge"\/>')
		);
	}
}
