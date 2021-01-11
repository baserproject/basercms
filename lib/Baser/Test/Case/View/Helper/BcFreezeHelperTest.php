<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.View.Helper
 * @since           baserCMS v 3.0.6
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcFreezeHelper', 'View/Helper');

/**
 * Class FormHelperTest
 *
 * @property BcFormHelper $Form
 * @property BcFreezeHelper $BcFreeze
 */
class BcFreezeHelperTest extends BaserTestCase
{

	/**
	 * setUp method
	 *
	 * @return void
	 */
	public function setUp()
	{
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
	public function tearDown()
	{
		unset($this->BcFreeze);
		parent::tearDown();
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
	public function testText($freezed, $fieldName, $attributes, $expected)
	{

		// 凍結させる
		if ($freezed) {
			$this->BcFreeze->freeze();
			list($model, $field) = explode('.', $fieldName);
			$this->BcFreeze->request->data[$model][$field] = 'BaserCMS';
		}

		$result = $this->BcFreeze->text($fieldName, $attributes);
		$this->assertEquals($expected, $result);
	}

	public function textDataProvider()
	{
		return [
			[false, 'baser', [], '<input name="data[baser]" type="text" id="baser"/>'],
			[false, 'baser', ['class' => 'bcclass'], '<input name="data[baser]" class="bcclass" type="text" id="baser"/>'],
			[false, 'baser', ['class' => 'bcclass', 'id' => 'bcid'], '<input name="data[baser]" class="bcclass" id="bcid" type="text"/>'],
			[false, 'baser', ['type' => 'hidden'], '<input name="data[baser]" type="hidden" id="baser"/>'],
			[true, 'baser.freezed', [], '<input name="data[baser][freezed]" type="hidden" value="BaserCMS" id="baserFreezed"/>BaserCMS'],
			[true, 'baser.freezed', ['value' => 'BaserCMS2'], '<input name="data[baser][freezed]" value="BaserCMS2" type="hidden" id="baserFreezed"/>BaserCMS2'],
			[true, 'baser.freezed', ['type' => 'hidden'], '<input name="data[baser][freezed]" type="hidden" value="BaserCMS" id="baserFreezed"/>BaserCMS'],
		];
	}

	/**
	 * select プルダウンメニューを表示
	 *
	 * @param boolean $freezed フォームを凍結させる
	 * @param string $fieldName フィールド文字列
	 * @param array $options コントロールソース
	 * @param array $attributes html属性
	 * @param array    空データの表示有無
	 * @param string $expexted 期待値(htmlタグ)
	 * @dataProvider selectDataProvider
	 */
	public function testSelect($freezed, $fieldName, $options, $attributes, $expected)
	{

		// 凍結させる
		if ($freezed) {
			$this->BcFreeze->freeze();
		}

		$result = $this->BcFreeze->select($fieldName, $options, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function selectDataProvider()
	{
		return [
			[false, 'baser', [], [], "<select name=\"data\[baser\]\" id=\"baser\">.<option value=\"\">"],
			[false, 'baser', ['1' => 'ラーメン'], [], '<option value="1">ラーメン'],
			[false, 'baser', ['1' => 'ラーメン', '2' => '寿司'], [], '<option value="1">ラーメン.*<option value="2">寿司'],
			[false, 'baser', [], ['class' => 'bcclass'], "<select name=\"data\[baser\]\" class=\"bcclass\" id=\"baser\">"],
			[false, 'baser', ['1' => 'ラーメン'], ['class' => 'bcclass'], 'class="bcclass".*<option value="1">ラーメン'],
			[false, 'baser', ['1' => 'ラーメン', '2' => '寿司'], ['cols' => 10], 'cols="10".*<option value="1">ラーメン　　　　　　.*<option value="2">寿司　　　　　　　　'],
			[true, 'baser.freezed', ['1' => 'ラーメン'], ['class' => 'bcclass'], "<input type=\"hidden\" name=\"data\[baser\]\[freezed\]\" class=\"bcclass\" id=\"baserFreezed\""],
		];
	}


	/**
	 * 日付タグを表示
	 *
	 * @param boolean $freezed フォームを凍結させる
	 * @param string $fieldName フィールド文字列
	 * @param string $dateFormat 日付フォーマット
	 * @param string $timeFormat 時間フォーマット
	 * @param array $attributes html属性
	 * @param string $expexted 期待値
	 * @dataProvider dateTimeDataProvider
	 */
	public function testDateTime($freezed, $fieldName, $dateFormat, $timeFormat, $attributes, $expected)
	{

		// 凍結させる
		if ($freezed) {
			$this->BcFreeze->freeze();
		}

		$result = $this->BcFreeze->dateTime($fieldName, $dateFormat, $timeFormat, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function dateTimeDataProvider()
	{
		return [
			[false, 'test', 'YMD', '12', [], 'id="testYear".*id="testMonth".*id="testDay".*id="testHour".*id="testMin".*id="testMeridian"'],
			[false, 'test', 'DMY', '12', [], 'id="testDay".*id="testMonth".*id="testYear"'],
			[false, 'test', 'YMD', '24', [], '59<\/option>.<\/select>$'],
			[false, 'test', 'YMD', '12', ['class' => 'bcclass'], 'class="bcclass"'],
			[false, 'test', 'YMD', '12', ['empty' => false], '^(?!value="").*$'],
			[false, 'test', 'YMD', '12', ['empty' => ['day' => '選択されていません']], '<option value="">選択されていません'],
			[true, 'test', 'YMD', '12', [], 'type="hidden"'],
			[true, 'test', 'YMD', '12', ['selected' => ['year' => '2010', 'month' => '4', 'day' => '1']], '2010年.*4月.*1日'],
			[true, 'test', 'YMD', '12', ['selected' => '2010-4-1 11:22:33'], '2010年.*4月.*1日.*11時.*22分'],
			[true, 'test', 'YMD', '12', ['selected' => ['day' => '100']], 'value="100"'],
			[true, 'test', 'YMD', '12', ['empty' => true], '^((?!value="").)*$'],
		];
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
	public function testWyear($freezed, $fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty, $expected)
	{

		// 凍結させる
		if ($freezed) {
			$this->BcFreeze->freeze();

		}

		$result = $this->BcFreeze->wyear($fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function wyearDataProvider()
	{
		return [
			[false, 'test', null, null, null, [], true, 'id="testYear">.*<option value="r-17">'],
			[false, 'test', 2010, null, null, [], true, '<option value="h-22">平成 22<\/option>.<\/select>$'],
			[false, 'test', null, 2010, null, [], true, '<option value=""><\/option>.<option value="h-22">'],
			[false, 'test', null, null, 'r-17', [], true, 'value="r-17" selected="selected"'],
			[false, 'test', null, null, null, ['type' => 'hidden'], true, 'type="hidden"'],
			[false, 'test', null, null, null, [], false, 'id="testYear">.*<option value="r-17">'],
			[true, 'test', null, null, null, [], true, 'type="hidden"'],
			[true, 'test', null, null, '2035-1-1', [], true, '令和 17'],
		];
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
	public function testCheckbox($freezed, $fieldName, $attributes, $expected)
	{

		// 凍結させる
		if ($freezed) {
			$this->BcFreeze->freeze();
		}

		$result = $this->BcFreeze->checkbox($fieldName, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function checkboxDataProvider()
	{
		return [
			[false, 'baser', [], "<input type=\"hidden\" name=\"data\[baser\]\" id=\"baser_\" value=\"0\"\/><input type=\"checkbox\" name=\"data\[baser\]\" value=\"1\" id=\"baser\""],
			[false, 'baser', ['class' => 'bcclass'], 'class="bcclass"'],
			[true, 'baser.freezed', [], "name=\"data\[baser\]\[freezed\]\".*id=\"baserFreezed\""],
			[true, 'baser.freezed', ['label' => 'test'], 'label="test"'],
		];
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
	public function testTextarea($freezed, $fieldName, $attributes, $expected)
	{

		// 凍結させる
		if ($freezed) {
			$this->BcFreeze->freeze();
			list($model, $field) = explode('.', $fieldName);
			$this->BcFreeze->request->data[$model][$field] = 'BaserCMS';
		}

		$result = $this->BcFreeze->textarea($fieldName, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function textareaDataProvider()
	{
		return [
			[false, 'baser', [], "<textarea name=\"data\[baser\]\" id=\"baser\"><\/textarea>"],
			[false, 'baser', ['class' => 'bcclass'], 'class="bcclass"'],
			[true, 'baser.freezed', [], 'value="BaserCMS"'],
			[true, 'baser.freezed', ['value' => 'BaserCMS2'], 'value="BaserCMS2"'],
			[true, 'baser.freezed', ['class' => 'bcclass'], 'class="bcclass"'],
		];
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
	public function testRadio($freezed, $fieldName, $options, $attributes, $expected)
	{

		// 凍結させる
		if ($freezed) {
			$this->BcFreeze->freeze();
		}

		$result = $this->BcFreeze->radio($fieldName, $options, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function radioDataProvider()
	{
		return [
			[false, 'baser', [], [], "<input type=\"hidden\" name=\"data\[baser\]\" id=\"baser_\" value=\"\""],
			[false, 'baser', ['test1' => 'testValue1'], [], 'id="baserTest1".*for="baserTest1">testValue1'],
			[false, 'baser', ['test1' => 'testValue1'], ['class' => 'bcclass'], 'class="bcclass"'],
			[true, 'baser.freezed', [], [], 'type="hidden"'],
			[true, 'baser.freezed', ['test1' => 'testValue1'], ['class' => 'bcclass'], 'class="bcclass"'],
		];
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
	public function testFile($freezed, $fieldName, $options, $expected)
	{

		// 凍結させる
		if ($freezed) {
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

	public function fileDataProvider()
	{
		return [
			[false, 'baser', [], '<input type="file" name="data\[baser\]" id="baser"'],
			[false, 'baser', ['size' => 100], 'size="100"'],
			[true, 'baser.freezed', [], '<input type="file" name="data\[baser\]\[freezed\]" id="baserFreezed"\/>'],
		];
	}

	/**
	 * ファイルコントロール（画像）を表示する
	 * TODO 確認画面には未チェック
	 *
	 * @param boolean $freezed フォームを凍結させる
	 * @param string $name $this->request->data[$model][$field]['name']に格納する値
	 * @param string $exist フィールド文字列 $this->request->data[$model][$field . '_exists'] に格納する値
	 * @param string $fieldName フィールド文字列
	 * @param array $attributes html属性
	 * @param array $imageAttributes 画像属性
	 * @param string $expexted 期待値
	 * @dataProvider imageDataProvider
	 */
	public function testImage($freezed, $name, $exist, $fieldName, $attributes, $imageAttributes, $expected)
	{

		list($model, $field) = explode('.', $fieldName);
		$this->BcFreeze->request->data[$model][$field]['name'] = $name;
		$this->BcFreeze->request->data[$model][$field . '_exists'] = $exist;

		// 凍結させる
		if ($freezed) {
			$this->BcFreeze->freeze();

		}

		$result = $this->BcFreeze->image($fieldName, $attributes, $imageAttributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function imageDataProvider()
	{
		return [
			[false, null, null, 'test.image', [], [], '<input type="file" name="data\[test\]\[image\]" id="testImage"'],
			[false, null, null, 'test.image', ['size' => 100], [], 'size="100"'],
			[false, null, 'testexist', 'test.image', [], [], 'src="\/\/tests.*<label.+?削除する<'],
			[false, null, 'testexist', 'test.image', [], ['dir' => 'testdir'], 'src="\/testdir\/tests'],
			[true, null, null, 'test.image', [], [], '&nbsp;'],
			[true, 'testname', null, 'test.image', [], [], 'id="testImageExists".*src="tmp\/test\/img'],
			[true, null, null, 'test.image', [], ['alt' => 'testalt'], '&nbsp;'],
			[true, null, 'testexist', 'test.image', [], [], 'dir=""'],
			[true, null, 'testexist', 'test.image', [], ['dir' => 'testdir'], 'dir="testdir"'],
		];
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
	public function testJsonList($freezed, $data, $fieldName, $attributes, $expected)
	{

		// 凍結させる
		if ($freezed) {
			$this->BcFreeze->freeze();
			$this->BcFreeze->request->data[$fieldName] = $data;
		}

		// indexを作る
		$attributes_default = [
			'imgSrc' => null,
			'ajaxAddAction' => null,
			'ajaxDelAction' => null,
		];
		$attributes = $attributes + $attributes_default;

		$result = $this->BcFreeze->jsonList($fieldName, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function jsonListDataProvider()
	{
		return [
			[false, null, 'baser', [], 'id="JsonBaserDb".*jQuery\(function\(\)'],
			[false, [], 'baser', ['ajaxAddAction' => 'test'], '"ajaxAddAction":"test"'],
			[false, [], 'baser', ['ajaxDelAction' => 'test'], '"ajaxDelAction":"test"'],
			[true, [['name' => 'test']], 'baser', [], '<li>test'],
			[true, [['name' => 'test1'], ['name' => 'test2']], 'baser', [], '<li>test1.*<li>test2'],
			[true, null, 'baser', [], '^$'],
		];
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
	public function testDatepicker($freezed, $date, $fieldName, $attributes, $expected)
	{

		// 凍結させる
		if ($freezed) {
			$this->BcFreeze->freeze();
			list($model, $field) = explode('.', $fieldName);
			$this->BcFreeze->request->data[$model][$field] = $date;
		}

		$result = $this->BcFreeze->datepicker($fieldName, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function datepickerDataProvider()
	{
		return [
			[false, null, 'baser', [], 'type="text".*id="baser".*("#baser")'],
			[false, null, 'baser', ['test1' => 'testValue1'], 'test1="testValue1"'],
			[true, '2015-4-1', 'baser.freezed', [], 'type="hidden".*2015\/04\/01'],
			[true, null, 'baser.freezed', [], '>$'],
			[true, null, 'baser.freezed', ['test1' => 'testValue1'], 'test1="testValue1"'],
		];
	}

	public function testFreeze()
	{
		$this->assertFalse($this->BcFreeze->freezed);
		$this->BcFreeze->freeze();
		$this->assertTrue($this->BcFreeze->freezed);
	}

	/**
	 * 凍結時用のコントロールを取得する
	 *
	 * @param string    フィールド文字列
	 * @param array    コントロールソース
	 * @param array    html属性
	 * @param string $expexted 期待値
	 * @dataProvider freezeControllDataProvider
	 */
	public function testFreezeControll($fieldName, $options, $attributes, $expected)
	{
		$result = $this->BcFreeze->freezeControll($fieldName, $options, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result);
	}

	public function freezeControllDataProvider()
	{
		return [
			['baser.freezed', [], [], '<input type="hidden" name="data\[baser\]\[freezed\]" class="" id="baserFreezed"'],
			['baser.freezed', [], ['value' => 'BaserCMS'], 'value="BaserCMS"'],
			['baser.freezed', [], ['value' => 'BaserCMS', 'multiple' => 'select'], 'value="BaserCMS"\/>BaserCMS'],
			['baser.freezed', [], ['value' => 'BaserCMS', 'multiple' => 'checkbox'], 'value="BaserCMS"\/>BaserCMS'],
			['baser.freezed', ['1' => 'BaserCMS1', '2' => 'BaserCMS2', '3' => 'BaserCMS3',], ['value' => [1, 2, 3], 'multiple' => 'checkbox'], '<li>BaserCMS1.*<li>BaserCMS2.*<li>BaserCMS3.*value="1".*value="2".*value="3"'],
			['baser.freezed', ['1' => 'BaserCMS1'], ['value' => [1], 'multiple' => 'hoge'], '<input type="hidden" name="data\[baser\]\[freezed\]\[\]"  class="" value="1" \/><ul class="" value="1"  ><\/ul>'],
			['baser.freezed', ['1' => 'BaserCMS1', '2' => 'BaserCMS2', '3' => 'BaserCMS3',], ['value' => [1, 2, 3], 'multiple' => 'checkbox'], '<li>BaserCMS1.*<li>BaserCMS2.*<li>BaserCMS3.*value="1".*value="2".*value="3"'],
			['baser.freezed', ['1' => 'BaserCMS1'], ['value' => 1], '<input type="hidden" name="data\[baser\]\[freezed\]" class="" value="1" id="baserFreezed"\/>BaserCMS1'],
			['baser.freezed.hoge', ['1' => 'BaserCMS1'], ['value' => 1], '<input type="hidden" name="data\[baser\]\[freezed\]\[hoge\]".*value="1" id="baserFreezedHoge"\/>'],
		];
	}
}
