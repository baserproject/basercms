<?php
/**
 * test for BcTextHelper
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
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

/**
 * text用のデータプロバイダ
 *
 * @return array
 */
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

/**
 * select用のデータプロバイダ
 *
 * @return array
 */
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
 * @param	string $fieldName フィールド文字列
 * @param	string $dateFormat 日付フォーマット
 * @param	string $timeFormat 時間フォーマット
 * @param	array	$attributes html属性
 */
	public function dateTime($fieldName, $dateFormat, $timeFormat, $attributes) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 和暦年
 *
 * MEMO : $selectedまわりの挙動に変更が必要と思われます
 *
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
		
		$this->markTestIncomplete('このテストは、一部未完成です。');


		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();
		}

		$result = $this->BcFreeze->wyear($fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty);
    $this->assertRegExp('/' . $expected . '/s', $result);
	}

/**
 * wyear用のデータプロバイダ
 *
 * @return array
 */
  public function wyearDataProvider() {
    return array(
      array(false, 'test', null, null, null, array(), true, 'id="testWareki".*value="h-47".*<option value="h-7">平成 7<\/option>.<\/select>$'),
      array(false, 'test', 2010, null, null, array(), true, '<option value="h-22">平成 22<\/option>.<\/select>$'),
      array(false, 'test', null, 2010, null, array(), true, '<option value=""><\/option>.<option value="h-22">'),
      // array(false, 'test', null, null, '平成 22', array(), true, '<fasd>'),
      array(false, 'test', null, null, null, array('type' => 'hidden'), true, 'type="hidden"'),
      array(false, 'test', null, null, null, array(), false, 'id="testYear">.<option value="h-47">'),
      array(true, 'test', null, null, null, array(), true, 'type="hidden"'),
      // array(true, 'test', null, null, '平成 27', array(), true, 'gsfdd'),
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

/**
 * checkbox用のデータプロバイダ
 *
 * @return array
 */
  public function checkboxDataProvider() {
    return array(
      array(false, 'baser', array(), "<input type=\"hidden\" name=\"data\[baser\]\" id=\"baser_\" value=\"0\"\/><input type=\"checkbox\" name=\"data\[baser\]\"  value=\"1\" id=\"baser\""),
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

/**
 * textarea用のデータプロバイダ
 *
 * @return array
 */
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

/**
 * radio用のデータプロバイダ
 *
 * @return array
 */
  public function radioDataProvider() {
    return array(
      array(false, 'baser', array(), array(), "<input type=\"hidden\" name=\"data\[baser\]\" id=\"baser_\" value=\"\""),
      array(false, 'baser', array('test1' => 'testValue1'), array(), 'for="baserTest1".*id="baserTest1".*testValue1'),
      array(false, 'baser', array('test1' => 'testValue1'), array('class' => 'bcclass'), 'class="bcclass"'),
      array(true, 'baser.freezed', array(), array(), 'type="hidden"'),
      array(true, 'baser.freezed', array('test1' => 'testValue1'), array('class' => 'bcclass'), 'class="bcclass"'),
    );
  }

/**
 * ファイルタグを出力
 * 
 * MEMO : 3番目のテストは、以下のエラーがでるためコメントアウトしています。
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
			$this->markTestIncomplete('このテストは、一部未完成です。');
			$this->BcFreeze->freeze();
		}

		$result = $this->BcFreeze->file($fieldName, $options);
    $this->assertRegExp('/' . $expected . '/s', $result);
	}

/**
 * file用のデータプロバイダ
 *
 * @return array
 */
  public function fileDataProvider() {
    return array(
      array(false, 'baser', array(), '<input type="file" name="data\[baser\]"  id="baser"'),
      array(false, 'baser', array('size' => 100), ' size="100"'),
      array(true, 'baser.freezed', array(), ' size="100"'),
    );
  }

/**
 * ファイルコントロール（画像）を表示する
 * TODO 確認画面には未チェック
 * 
 * @param boolean $freezed フォームを凍結させる
 * @param string $fieldName フィールド文字列
 * @param	array $attributes html属性
 * @param array $imageAttributes 画像属性
 * @param string $expexted 期待値
 * @dataProvider imageDataProvider
 */
	public function testImage($freezed, $fieldName, $attributes, $imageAttributes, $expected) {
		
		$this->markTestIncomplete('このテストは、一部未完成です。');

		// 凍結させる
		if($freezed) {
			$this->BcFreeze->freeze();
			list($model, $field) = explode('.', $fieldName);
			$this->BcFreeze->request->data[$model][$field] = 'BaserCMS';
		}

		$result = $this->BcFreeze->file($fieldName, $attributes, $imageAttributes);
    $this->assertRegExp('/' . $expected . '/s', $result);
	}

/**
 * image用のデータプロバイダ
 *
 * @return array
 */
  public function imageDataProvider() {
    return array(
      array(false, 'test', array(), array(), '<input type="file" name="data\[test\]"  id="test"'),
      // array(false, 'test', array('size' => 100), array(), 'size="100"'),
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

/**
 * jsonList用のデータプロバイダ
 *
 * @return array
 */
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

/**
 * datepicker用のデータプロバイダ
 *
 * @return array
 */
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
 * @param	string	フィールド文字列
 * @param	array	コントロールソース
 * @param	array	html属性
 */
	public function freezeControll($freezed, $fieldName, $options, $attributes, $expected) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}
	
	public function upload($freezed, $fieldName, $options, $expected) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}












}
