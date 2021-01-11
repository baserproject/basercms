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

App::uses('BcAppView', 'View');
App::uses('BcFormHelper', 'View/Helper');

/**
 * Class Contact
 *
 * @package        Baser.Test.Case.View.Helper
 */
class Contact extends CakeTestModel
{

	/**
	 * useTable property
	 *
	 * @var bool
	 */
	public $useTable = false;

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = [
		'BcSearchIndexManager',
		'BcCache',
		'BcUpload' => [
			'subdirDateFormat' => 'Y/m/',
			'fields' => [
				'eye_catch' => [
					'type' => 'image',
					'namefield' => 'no',
					'nameformat' => '%08d'
				]
			]
		]
	];
}

/**
 * Class FormHelperTest
 *
 * @package Baser.Test.Case.View.Helper
 * @property BcFormHelper $BcForm
 * @property BcTimeHelper $BcTime
 * @property BcUploadHelper $BcUpload
 * @property View $_View
 */
class BcFormHelperTest extends BaserTestCase
{

	/**
	 * Fixtures
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.Page',
		'baser.Default.Plugin',
		'baser.Default.Content',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
		'baser.Default.User',
		'baser.Default.UserGroup',
		'baser.Default.Favorite'
	];

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
		$this->_View = new BcAppView();
		$this->_View->request = $this->_getRequest('/contacts/add');
		$this->BcForm = new BcFormHelper($this->_View);
		$this->BcTime = new BcTimeHelper($this->_View);
	}

	/**
	 * tearDown method
	 *
	 * @return void
	 */
	public function tearDown()
	{
		parent::tearDown();
		unset($this->BcForm);
		unset($this->BcTime);
	}

	/**
	 * Returns a set of SELECT elements for a full datetime setup: day, month and year, and then time.
	 *
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param string $dateFormat DMY, MDY, YMD, or null to not generate date inputs.
	 * @param string $timeFormat 12, 24, or null to not generate time inputs.
	 * @param array $attributes Array of Attributes
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider dateTimeDataProvider
	 */
	public function testDateTime($fieldName, $dateFormat, $timeFormat, $attributes, $expected, $message)
	{
		$result = $this->BcForm->dateTime($fieldName, $dateFormat, $timeFormat, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function dateTimeDataProvider()
	{
		return [
			['test', 'W', '12', ['maxYear' => 2010], 'id="testWareki".*<option value="h-22">平成 22', 'datetime()を出力できません'],
			['test', 'WY', '12', [], '年.*年', '年の接尾辞を出力できません'],
			['test', 'WM', '12', [], '月', '月の接尾辞を出力できません'],
			['test', 'WD', '12', [], '日', '日の接尾辞を出力できません'],
		];
	}

	/**
	 * Creates a checkbox input widget.
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $options Array of HTML attributes.
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider checkboxDataProvider
	 */
	public function testCheckbox($fieldName, $options, $expected, $message)
	{
		$result = $this->BcForm->checkbox($fieldName, $options);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function checkboxDataProvider()
	{
		return [
			['test', [], '<input type="checkbox" name="data\[test\]" value="1" id="test"', 'checkbox()を出力できません'],
			['test', ['label' => 'testLabel'], '<input type="checkbox".*label for="test">testLabel', '属性を付与できません'],
		];
	}

	/**
	 * Creates a hidden input field.
	 *
	 * @param string $fieldName Name of a field, in the form of "Modelname.fieldname"
	 * @param array $options Array of HTML attributes.
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider hiddenDataProvider
	 */
	public function testHidden($fieldName, $options, $expected, $message)
	{
		$result = $this->BcForm->hidden($fieldName, $options);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function hiddenDataProvider()
	{
		return [
			['test', [], '<input type="hidden" name="data\[test\]" id="test"', 'hidden()を出力できません'],
			['test', ['class' => 'bcclass'], 'class="bcclass"', '属性を付与できません'],
			['test', ['multiple' => 'checkbox'], 'name="data\[test\]"\/>$', 'セキュリティコンポーネントに対応していません'],
			['test', ['multiple' => 'checkbox', 'value' => ['value1', 'value2']], 'name="data\[test\]\[\]".* value="value1".*value="value2"', '値を複数追加できません'],
		];
	}


	/**
	 * create
	 * フック用にラッピング
	 *
	 * @param array $model
	 * @param array $options
	 * @return string
	 */
	public function testCreate()
	{
		$result = $this->BcForm->create();
		$this->assertRegExp('/<form action="\/contacts\/add" novalidate="novalidate" id="addForm" method="post" accept-charset="utf-8"><div style="display:none;">.*/', $result);
	}


	/**
	 * end
	 * フック用にラッピング
	 *
	 * @param array $options
	 * @return    string
	 * @access    public
	 * @dataProvider endProvider
	 */
	public function testEnd($array1, $array2, $expected)
	{
		$result = $this->BcForm->end($array1, $array2);
		$this->assertEquals($expected, $result);
	}

	public function endProvider()
	{
		return [
			[null, null, '</form>'],
			[[1, 2], null, '<div class="submit"><input 1="1" 2="2" type="submit" value="Submit"/></div></form>'],
			[null, [1, 2], '</form>'],
			[[1, 2], [1, 2], '<div class="submit"><input 1="1" 2="2" type="submit" value="Submit"/></div></form>']
		];
	}


	/**
	 * Generates a form input element complete with label and wrapper div
	 *
	 * @param string $fieldName This should be "Modelname.fieldname"
	 * @param array $options Each type of input takes different options.
	 * @return string Completed form widget
	 * @dataProvider inputDataProvider
	 *
	 * maxlength値がsqliteでは8、その他は11と返り値が異なるため、4,5番目のテストでは.*を使用
	 */

	public function testInput($optionsField, $optionsData, $fieldName, $options, $expected)
	{
		$this->attachEvent(['Helper.Form.beforeInput' => ['callable' => function(CakeEvent $event) use ($optionsField, $optionsData) {
			if (!empty($optionsField)) {
				$event->data['options'][$optionsField] = $optionsData;
			}
		}]]);
		if (@$options['type'] === 'file') {
			$this->BcForm->BcUpload->request->data = [
				'Contact' => [
					'id' => '1',
					'eye_catch' => 'template1.jpg',
					'modified' => '2013-07-21 01:41:12', 'created' => '2013-07-21 00:53:42',
				]
			];
		}
		$result = $this->BcForm->Input($fieldName, $options);
		$this->assertRegExp('/' . $expected . '/s', $result);
		$this->resetEvent();
	}

	public function inputDataProvider()
	{
		$beginYear = date('Y') - 20;
		$endYear = date('Y') + 20;
		return [
			['value', '', 'User.id', ['type' => 'dateTimePicker'], '<span class="bca-datetimepicker".+?<span class="bca-datetimepicker__date".+?<label .+?class="bca-datetimepicker__date-label".+?<input .+?class="bca-datetimepicker__date-input".+?<span class="bca-datetimepicker__time".+?<label .+?class="bca-datetimepicker__time-label".+?<input .+?class="bca-datetimepicker__time-input".+?'],
			['value', [1], 'Contact.eye_catch', ['type' => 'file'], '<span class="bca-file".+?<input type="file".+?class="bca-file__input".+?<span class="bca-file__delete".+?<input type="checkbox".+?class="bca-file__delete-input".+?<label.+?class="bca-file__delete-label".+?<figure class="bca-file__figure".+?<a .+?class="bca-file__link".+?<img .+?class="bca-file__img".+?<figcaption class="bca-file__figcaption file-name"'],
			['value', [1], 'User.id', ['type' => 'select', 'multiple' => 'checkbox', 'options' => [1 => 'abc', 2 => 'def']], '<span class="bca-checkbox-group"><input type="hidden".*?<span class="bca-checkbox"><input type="checkbox".+?class="bca-checkbox__input" \/>&nbsp;<label.+?class="bca-checkbox__label"'],
			['value', [1], 'User.id', ['type' => 'select', 'options' => [1 => 'abc', 2 => 'def']], '<span class="bca-select"><select.+?class="bca-select__select"'],
			['value', 'hoge', 'User.id', ['type' => 'textarea'], '<span class="bca-textarea"><textarea name="data\[User\]\[id\]" class="bca-textarea__textarea" maxlength="[0-9]+?" cols="30" rows="6" id="UserId">hoge<\/textarea><\/span>'],
			['value', 'hoge', 'User.id', ['type' => 'hidden'], '<input type="hidden" name="data\[User\]\[id\]" value="hoge" class="bca-hidden__input" id="UserId"\/>'],
			['value', 'hoge', 'User.id', ['div' => 'true'], '<input type="hidden" name="data\[User\]\[id\]" value="hoge" id="UserId"\/>'],
			['value', 'hoge', 'User.id', ['error' => 'true'], '<input type="hidden" name="data\[User\]\[id\]" value="hoge" id="UserId"\/>'],
			['value', 'hoge', 'User.id', ['type' => 'text'], '<span class="bca-textbox"><input name="data\[User\]\[id\]" value="hoge" class="bca-textbox__input" maxlength=".*" type="text" id="UserId"\/><\/span>'],
			['value', 'hoge', 'User.id', ['type' => 'text', 'label' => true], '<span class="bca-textbox"><label.+?class="bca-textbox__label">.+?<input.+?class="bca-textbox__input"'],
			['value', 'hoge', 'User.id', ['type' => 'radio', 'options' => []], ''],
			['value', 'hoge', 'User.id', ['type' => 'radio', 'options' => [1, 2]], '<span class="bca-radio-group"><span class="bca-radio"><input type="radio" name="data\[User\]\[id\]" id="UserId0" value="0" class="bca-radio__input" \/><label for="UserId0" class="bca-radio__label">1<\/label><\/span>　<span class="bca-radio"><input type="radio" name="data\[User\]\[id\]" id="UserId1" value="1" class="bca-radio__input" \/><label for="UserId1" class="bca-radio__label">2<\/label><\/span><\/span>'],
			['value', 'hoge', 'User.id', ['type' => 'radio', 'options' => [], 'legend' => true], '<fieldset><legend>1<\/legend><\/fieldset>'],
			['value', 'hoge', 'User.id', ['type' => 'radio', 'options' => [], 'separator' => 'aaa'], ''],
			['value', 'hoge', 'User.id', ['type' => 'checkbox', 'label' => 'hoge'], '<span class="bca-checkbox">.+?"checkbox".+?class="bca-checkbox__input".+?<label.+?class="bca-checkbox__label"'],
			['value', 'hoge', 'User.id', ['type' => 'input', 'error' => true], '<input type="hidden" name="data\[User\]\[id\]" value="hoge" id="UserId"\/>'],
			['value', 'hoge', 'User.id', ['type' => 'input', 'errorMessage' => 'hogehoge'], '<input type="hidden" name="data\[User\]\[id\]" value="hoge" id="UserId"\/>'],
			['value', 'hoge', 'User.id', ['type' => 'input', 'selected' => true], '<input type="hidden" name="data\[User\]\[id\]" value="hoge" id="UserId"\/>'],
			['value', 'hoge', 'User.id', ['type' => 'date', 'options' => []], '<select name="data\[User\]\[id\]\[month\].*id="UserIdMonth">.*01<\/op.*12<\/op.*\/se.*id="UserIdDay">.*1<\/op.*31<\/op.*\n<\/se.*id="UserIdYear">.*' . $endYear . '<\/op.*' . $beginYear . '<\/option>\n<\/select>'],
			['value', 'hoge', 'User.id', ['type' => 'time', 'options' => []], '<select name="data\[User\]\[id\]\[hour\].*id="UserIdHour".*1<\/op.*12<\/op.*\/se.*id="UserIdMin">.*00<\/op.*59<\/op.*\/se.*id="UserIdMeridian">.*selected="selected">am<\/op.*value="pm">pm<\/option>\n<\/select>'],
			['value', 'hoge', 'User.id', ['type' => 'datetime', 'options' => []], '<select name="data\[User\]\[id\]\[month\].*id="UserIdMonth.*01<\/op.*12<\/op.*<\/sel.*id="UserIdDay">.*1<\/option>.*31<\/op.*<\/se.*id="UserIdYear">.*' . $endYear . '<\/op.*' . $beginYear . '<\/op.*<\/se.*id="UserIdHour">.*1<\/op.*12<\/op.*<\/se.*id="UserIdMin">.*00<\/op.*59<\/op.*<\/se.*id="UserIdMeridian">.*selected="selected">am<\/op.*pm<\/op.*ect>'],
			['value', 'hoge', 'User.id', ['type' => 'input', 'div' => 'true'], '<div class="true"><input type="hidden" name="data\[User\]\[id\]" value="hoge" id="UserId"\/><\/div>'],
			['value', 'hoge', 'User.id', ['type' => 'input', 'counter' => 'true'], '<input type="hidden".+?<span id="UserIdCounter" class="bca-size-counter size-counter">.+?<script.+?<\/script>'],
			['', '', 'BlogTag.BlogTag', '', "<input type=\"hidden\" name=\"data\[BlogTag\]\[BlogTag\]\" value=\"\" id=\"BlogTagBlogTag_\"\/>\n<select name=\"data\[BlogTag\]\[BlogTag\]\[\]\" multiple=\"multiple\" id=\"BlogTagBlogTag\">\n<\/select>"],
			['', '', 'hoge', '', "<input name=\"data\[hoge\]\" type=\"text\" id=\"hoge\"\/>"],
			['', '', 'hoge', ['a' => 'hogege'], "<input name=\"data\[hoge\]\" a=\"hogege\" type=\"text\" id=\"hoge\"\/>"]
		];
	}

	/**
	 * ラベルを取得する
	 */
	public function testLabel()
	{
		$expected = 'class="bca-label"';
		$result = $this->BcForm->label('User.id', 'id', ['class' => 'bca-label']);
		$this->assertRegExp('/' . $expected . '/s', $result, 'ラベルに正しいクラスが付与できません');
	}

	/**
	 * CKEditorを出力する
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @param array $editorOptions
	 * @param array $styles
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider ckeditorDataProvider
	 */
	public function testCkeditor($fieldName, $options, $expected, $message)
	{
		$result = $this->BcForm->ckeditor($fieldName, $options);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function ckeditorDataProvider()
	{
		return [
			['test', [], '<textarea name="data\[test\]".*load.*CKEDITOR', 'CKEditorを出力できません'],
			['test', ['editorLanguage' => 'en'], '"language":"en"', 'オプションを設定できません'],
		];
	}

	/**
	 * エディタを表示する
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider editorDataProvider
	 */
	public function testEditor($fieldName, $options, $expected, $message)
	{
		$result = $this->BcForm->editor($fieldName, $options);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function editorDataProvider()
	{
		return [
			['test', [], '<textarea name="data\[test\]".*load.*CKEDITOR', 'CKEditorを出力できません'],
			['test', ['editorLanguage' => 'en'], '"language":"en"', 'オプションを設定できません'],
		];
	}


	/**
	 * 都道府県用のSELECTタグを表示する
	 *
	 * @param string $fieldName Name attribute of the SELECT
	 * @param mixed $selected Selected option
	 * @param array $attributes Array of HTML options for the opening SELECT element
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider prefTagDataProvider
	 */
	public function testPrefTag($fieldName, $selected, $attributes, $expected, $message)
	{
		$result = $this->BcForm->prefTag($fieldName, $selected, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function prefTagDataProvider()
	{
		return [
			['test', null, [], '<select name="data\[test\]" id="test">.<option value="">都道府県.*<option value="1">北海道.*<option value="47">沖縄県', 'prefTag()を出力できません'],
			['test', '40', [], '<option value="40" selected="selected">', '要素を選択状態にできません'],
			['test', null, ['class' => 'testclass'], ' class="testclass"', '要素に属性を付与できません'],
		];
	}


	/**
	 * 和暦年
	 *
	 * @param string $fieldName Prefix name for the SELECT element
	 * @param integer $minYear First year in sequence
	 * @param integer $maxYear Last year in sequence
	 * @param string $selected Option which is selected.
	 * @param array $attributes Attribute array for the select elements.
	 * @param boolean $showEmpty Show/hide the empty select option
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @param string $expected 期待値
	 * @dataProvider wyearDataProvider
	 */
	public function testWyear($fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty, $expected, $message)
	{
		$result = $this->BcForm->wyear($fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function wyearDataProvider()
	{
		return [
			['test', null, null, null, [], true, '<input type="hidden" name="data\[test\]\[wareki\].*<option value="h-', 'wyear()がされません	'],
			['test', 2010, null, null, [], true, '<option value="h-22">平成 22<\/option>.<\/select>$', '最小の年を指定できません'],
			['test', null, 2010, null, [], true, 'id="testYear">.<option value=""><\/option>.<option value="h-22">', '最大の年を指定できません'],
			['test', null, null, '2035-1-1', [], true, 'value="r-17" selected', '要素を選択状態にできません(Y-m-d形式)'],
			['test', null, null, 'r-17', [], true, 'value="r-17" selected', '要素を選択状態にできません(和暦形式)'],
			['test', null, null, null, ['class' => 'testclass'], true, 'class="testclass"', '属性を付与できません'],
			['test', null, null, null, ['class' => 'testclass', 'size' => '5'], true, 'size="5"', '属性を複数付与できません'],
			['test', null, null, null, [], false, 'id="testYear">.<option value="r-', '空の要素を非表示にできません'],
		];
	}

	/**
	 * 和暦年
	 *
	 * wyear()の今年を選択状態にするテスト
	 */
	public function testWyearNow()
	{
		// 現在の和暦
		$wareki = $this->BcTime->convertToWareki(date('Y-m-d'));
		$w = $this->BcTime->wareki($wareki);
		$wyear = $this->BcTime->wyear($wareki);
		$now = $w . '-' . $wyear;

		$result = $this->BcForm->wyear('test', null, null, 'now');
		$this->assertRegExp('/' . $now . '" selected/s', $result, '今年を選択状態にできません');
	}

	/**
	 * コントロールソースを取得する
	 * Model側でメソッドを用意しておく必要がある
	 *
	 * @param string $field フィールド名
	 * @param array $options
	 * @return array コントロールソース
	 * @dataProvider getControlSourceProvider
	 */
	public function testGetControlSource($field, $expected)
	{
		$result = $this->BcForm->getControlSource($field);
		$this->assertEquals($expected, $result);
	}

	public function getControlSourceProvider()
	{
		return [
			['hoge', []],
			['', []]
		];
	}

	/**
	 * モデルよりリストを生成する
	 *
	 * @param string $modelName
	 * @param mixed $conditions
	 * @param mixed $fields
	 * @param mixed $order
	 * @return mixed リストまたは、false
	 * @dataProvider generateListProvider
	 */
	public function testGenerateList($modelName, $conditions, $fields, $expected)
	{
		$result = $this->BcForm->generateList($modelName, $conditions, $fields);
		$this->assertEquals($result, $expected);
	}

	public function generateListProvider()
	{
		return [
			['hoge', '', '', ''],
			['User', '', ['id', 'name'], [1 => 'basertest', 2 => 'basertest2']],
			['User', '', ['name', 'id'], ['basertest' => 1, 'basertest2' => 2]],
			['User', true, ['name', 'id'], ['basertest' => 1, 'basertest2' => 2]],
			['User', false, ['name', 'id'], null]
		];
	}


	/**
	 * JsonList
	 *
	 * @param string $field フィールド文字列
	 * @param string $attributes
	 * @param string $expected 期待値
	 * @dataProvider jsonListDataProvider
	 */
	public function testJsonList($field, $attributes, $expected, $message)
	{

		$attributes_default = [
			'imgSrc' => null,
			'ajaxAddAction' => null,
			'ajaxDelAction' => null,
		];

		$attributes = $attributes + $attributes_default;

		$result = $this->BcForm->jsonList($field, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function jsonListDataProvider()
	{
		return [
			['test', [], 'id="JsonTestDb".*jQuery\(function\(\)', 'jsonList()を出力できません'],
			['test', ['imgSrc' => 'test'], '"deleteButtonSrc":"test"', 'imgSrc属性を付与できません'],
			['test', ['ajaxAddAction' => 'test'], '"ajaxAddAction":"test"', 'ajaxAddAction属性を付与できません'],
			['test', ['ajaxDelAction' => 'test'], '"ajaxDelAction":"test"', 'ajaxDelAction属性を付与できません'],
		];
	}

	/**
	 * カレンダーコントロール付きのテキストフィールド
	 * jquery-ui-1.7.2 必須
	 *
	 * @param string フィールド文字列
	 * @param array HTML属性
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider datepickerDataProvider
	 */
	public function testDatepicker($fieldName, $attributes, $expected, $message)
	{
		$result = $this->BcForm->datepicker($fieldName, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function datepickerDataProvider()
	{
		return [
			['baser', [], 'type="text".*id="baser".*("#baser")', 'datepicker()が出力できません'],
			['baser', ['test1' => 'testValue1'], 'test1="testValue1"', '要素に属性を付与できません'],
			['baser', ['value' => '2010-4-1'], 'value="2010\/4\/1"', '時間を指定できません'],
		];
	}

	/**
	 * 日付カレンダーと時間フィールド
	 *
	 * @param string $fieldName
	 * @param array $attributes
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider dateTimePickerDataProvider
	 */
	public function testDateTimePicker($fieldName, $attributes, $expected, $message)
	{
		$result = $this->BcForm->dateTimePicker($fieldName, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function dateTimePickerDataProvider()
	{
		return [
			['baser', [], '<span><label for="baser_date">日付.+?<input .+?<span><label for="baser_time">時間.+?<input .+?<input type="hidden".+?', 'dateTimePicker()が出力されません'],
			['baser', ['value' => '2010-4-1 11:22:33'], 'value="2010\/4\/1".*value="11:22:33".*value="2010-4-1 11:22:33"', '時間指定が正しく出力できません'],
			['baser', ['value' => '2010-04-01 11:22:33'], 'value="2010\/04\/01".*value="11:22:33".*value="2010-04-01 11:22:33"', '時間指定が正しく出力できません'],
			['baser', ['value' => '2010-4-1 '], 'value="2010\/4\/1".*value="".* value="2010-4-1 "', '時間を指定いない場合出力できません'],
			['baser', ['value' => '2010 hogehoge'], 'value="2010".*value="hogehoge".*value="2010 hogehoge"', '時間指定が不適切でない場合出力できません'],
			['baser', ['value' => 'hoge hogehoge'], 'value="hoge".*value="hogehoge".*value="hoge hogehoge"', '時間指定が不適切でない場合出力できません']
		];
	}

	/**
	 * 文字列保存用複数選択コントロール
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @param mixed $selected
	 * @param array $attributes
	 * @param mixed $showEmpty
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider selectTextDataProvider
	 */
	public function testSelectText($fieldName, $options, $selected, $attributes, $showEmpty, $expected, $message)
	{
		$result = $this->BcForm->selectText($fieldName, $options, $selected, $attributes, $showEmpty);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function selectTextDataProvider()
	{
		return [
			['baser', [], null, [], '', '<div id="baser_"><input type="hidden" name="data\[baser_\]" value="" id="baser_"', 'selectText()を出力できません'],
			['baser', ['BaserCMS1'], null, [], '', 'id="Baser0".*<label for="Baser0">BaserCMS1', 'optionを出力できません'],
			['baser', ['BaserCMS1', 'BaserCMS2'], null, [], '', '<label for="Baser0">.*BaserCMS1.*<label for="Baser1">.*BaserCMS2', 'optionを複数出力できません'],
			['baser', ['BaserCMS1', 'BaserCMS2'], '1', [], '', 'checked="checked".*for="Baser1" class="selected">BaserCMS2', 'checkboxを選択状態にできません'],
			['baser', ['BaserCMS1'], '1', ['class' => 'bcclass'], '', 'div class="bcclass"', '要素に属性を付与できません'],
			['baser', ['BaserCMS1'], '1', ['multiple' => 'select'], '', '<select', 'selectを出力できません'],
			['baser', ['BaserCMS1'], '1', [], true, 'value="".*>&nbsp;<label for="Baser">', '空要素を出力できません'],
			['baser', ['BaserCMS1'], '1', [], '選択してください', 'value="" id="Baser".+?&nbsp;<.*選択してください', '空要素のテキストを指定できません'],
			['baser', ['BaserCMS1'], '1', [], ['未選択' => '選択してください'], ' value="未選択" id="Baser未選択".+?&nbsp;<.+選択してください', '空要素のテキストと値を指定できません'],
		];
	}

	/**
	 * ファイルインプットボックス出力
	 *
	 * @param string $fieldName
	 * @param array $options
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider fileDataProvider
	 */
	public function testFile($fieldName, $options, $expected, $message = null)
	{
		$result = $this->BcForm->file($fieldName, $options);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function fileDataProvider()
	{
		return [
			['hoge', [], '<input type="file" name="data\[hoge\]" id="hoge"', 'ファイルインプットボックス出力できません'],
			['hoge', ['imgsize' => '50'], 'imgsize="50"', 'ファイルインプットボックス出力できません'],
			['hoge', ['link' => 'page'], 'link="page"', 'ファイルインプットボックス出力できません'],
			['hoge', ['delCheck' => 'page'], 'delCheck="page"', 'ファイルインプットボックス出力できません'],
			['hoge', ['force' => 'page'], 'force="page"', 'ファイルインプットボックス出力できません'],
			['hoge', ['rel' => 'page'], 'rel="page"', 'ファイルインプットボックス出力できません'],
			['hoge', ['title' => 'page'], 'title="page"', 'ファイルインプットボックス出力できません'],
			['hoge', ['width' => 'page'], 'width="page"', 'ファイルインプットボックス出力できません'],
			['hoge', ['height' => 'page'], 'height="page"', 'ファイルインプットボックス出力できません'],
			['hoge', ['value' => 'page'], '<input type="file" name="data\[hoge\]" id="hoge"', 'ファイルインプットボックス出力できません'],
			['hoge', ['hoge' => 'page'], 'hoge="page"', 'ファイルインプットボックス出力できません']
		];
	}

	/**
	 * フォームの最後のフィールドの後に発動する前提としてイベントを発動する
	 *
	 * @param string $type フォームのタイプ タイプごとにイベントの登録ができる
	 * @return string 行データ
	 * @dataProvider dispatchAfterFormDataProvider
	 */
	public function testDispatchAfterForm($type, $fields, $res, $expected)
	{
		$event = $this->attachEvent(['Helper.Form.after' . $type . 'Form' => ['callable' => function(CakeEvent $event) use ($fields, $res) {
			$event->data['fields'] = $fields;
			return $res;
		}]]);
		$result = $this->BcForm->dispatchAfterForm($type);
		$this->assertRegExp('/' . $expected . '/s', $result);
		$this->resetEvent();
	}

	public function dispatchAfterFormDataProvider()
	{
		return [
			['Hoge', [['title' => '1', 'input' => '2']], true, '<tr><th class="col-head bca-form-table__label">1<\/th>\n<td class="col-input bca-form-table__input">2<\/td>\n<\/tr>'],
			['Hoge', [['title' => '1', 'input' => '2']], false, '<tr><th class="col-head bca-form-table__label">1<\/th>\n<td class="col-input bca-form-table__input">2<\/td>\n<\/tr>'],
			['Hoge', '', true, ''],
			['Hoge', '', false, ''],
		];
	}

	/**
	 * Creates a set of radio widgets. Will create a legend and fieldset
	 * by default. Use $options to control this
	 *
	 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
	 * @param array $options Radio button options array.
	 * @param array $attributes Array of HTML attributes, and special attributes above.
	 * @param string $expected 期待値
	 * @param string $message テストが失敗した時に表示されるメッセージ
	 * @dataProvider radioDataProvider
	 */
	public function testRadio($fieldName, $options, $attributes, $expected, $message)
	{
		$result = $this->BcForm->radio($fieldName, $options, $attributes);
		$this->assertRegExp('/' . $expected . '/s', $result, $message);
	}

	public function radioDataProvider()
	{
		return [
			['baser', [], [], '<input type="hidden" name="data\[baser\]" id="baser_" value=""', 'radio()を出力できません'],
			['baser', ['BaserCMS1'], [], 'id="baser0" value="0" \/><label for="baser0">BaserCMS1<', 'optionを出力できません'],
			['baser', ['BaserCMS1', 'BaserCMS2'], [], '<legend>Baser.*value="0".*BaserCMS1.*value="1".*BaserCMS2', 'optionを複数出力できません'],
			['baser', ['BaserCMS1', 'BaserCMS2'], ['between' => 'test'], '<\/legend>test', ' legend と最初の要素の間に挿入されるコンテンツを出力できません'],
			['baser', ['BaserCMS1', 'BaserCMS2'], ['between' => ['test1', 'test2']], 'BaserCMS1<\/label>test1.*BaserCMS2<\/label>test2', '要素の後に挿入されるコンテンツを出力できません'],
			['baser', ['BaserCMS1'], ['label' => ['class' => 'bcclass']], 'class="bcclass"', 'labelに属性を付与できません'],
		];
	}

	/**
	 * testFormCreateWithSecurity method
	 *
	 * Test BcForm->create() with security key.
	 *
	 * @return void
	 */
	public function testCreateWithSecurity()
	{
		$this->BcForm->request['_Token'] = ['key' => 'testKey'];
		$encoding = strtolower(Configure::read('App.encoding'));
		$result = $this->BcForm->create('Contact', ['url' => '/contacts/add']);
		$expected = [
			'form' => ['action' => '/contacts/add', 'novalidate' => 'novalidate', 'id' => 'ContactAddForm', 'method' => 'post', 'accept-charset' => $encoding],
			'div' => ['style' => 'display:none;'],
			['input' => ['type' => 'hidden', 'name' => '_method', 'value' => 'POST']],
			['input' => [
				'type' => 'hidden', 'name' => 'data[_Token][key]', 'value' => 'testKey', 'id', 'autocomplete' => 'off'
			]],
			'/div'
		];
		$this->assertTags($result, $expected);
		$result = $this->BcForm->create('Contact', ['url' => '/contacts/add', 'id' => 'MyForm']);
		$expected['form']['id'] = 'MyForm';
		$this->assertTags($result, $expected);
	}

	/**
	 * testFileUploadField method
	 *
	 * @return void
	 */
	public function testFileUploadField()
	{
		$fieldName = 'Contact.upload';
		$this->BcForm->setEntity($fieldName);
		// 通常
		$result = $this->BcForm->file($fieldName);
		$expected = [
			['input' => ['type' => 'file', 'name' => 'data[Contact][upload]', 'id' => 'ContactUpload']],
		];
		$this->assertTags($result, $expected);
	}

	/**
	 * ファイルアップロードフィールドのテスト
	 *
	 * データと画像が既に存在する場合について
	 *
	 * @return void
	 */
	public function testFileUploadFieldWithImageFile()
	{
		$fieldName = 'Contact.eye_catch';
		$this->BcForm->setEntity($fieldName);
		$this->BcForm->BcUpload->request->data = [
			'Contact' => [
				'id' => '1',
				'eye_catch' => 'template1.jpg',
				'modified' => '2013-07-21 01:41:12', 'created' => '2013-07-21 00:53:42',
			]
		];

		$result = $this->BcForm->file($fieldName);
		$expected = [
			['input' => ['type' => 'file', 'name' => 'data[Contact][eye_catch]', 'id' => 'ContactEyeCatch']],
			'&nbsp;',
			['span' => []],
			['input' => ['type' => 'hidden', 'name' => 'data[Contact][eye_catch_delete]', 'id' => 'ContactEyeCatchDelete_', 'value' => '0']],
			['input' => ['type' => 'checkbox', 'name' => 'data[Contact][eye_catch_delete]', 'value' => '1', 'id' => 'ContactEyeCatchDelete']],
			['label' => ['for' => 'ContactEyeCatchDelete']],
			'削除する',
			'/label',
			'/span',
			['input' => ['type' => 'hidden', 'name' => 'data[Contact][eye_catch_]', 'value' => 'template1.jpg', 'id' => 'ContactEyeCatch']],
			['br' => true],
			['figure' => []],
			['a' => ['href' => 'preg:/' . preg_quote('/files/template1.jpg?', '/') . '\d+/', 'rel' => 'colorbox', 'title' => '']],
			['img' => ['src' => 'preg:/' . preg_quote('/files/template1.jpg?', '/') . '\d+/', 'alt' => '']],
			'/a',
			['br' => true],
			['figcaption' => ['class' => 'file-name']],
			'template1.jpg',
			'/figcaption',
			'/figure',
		];

		$this->assertTags($result, $expected);
	}

	/**
	 * ファイルアップロードフィールドのテスト（hasMany対応）
	 *
	 * @return void
	 */
	public function testFileUploadFieldHasManyField()
	{
		$fieldName = 'Contact.0.upload';
		$this->BcForm->setEntity($fieldName);

		// 通常
		$result = $this->BcForm->file($fieldName);

		$expected = [
			['input' => ['type' => 'file', 'name' => 'data[Contact][0][upload]', 'id' => 'Contact0Upload']],
		];
		$this->assertTags($result, $expected);
	}

	/**
	 * ファイルアップロードフィールドのテスト（hasMany対応）
	 *
	 * データと画像が既に存在する場合について
	 *
	 * @return void
	 */
	public function testFileUploadFieldHasManyFieldWithImageFile()
	{
		$fieldName = 'Contact.0.eye_catch';
		$this->BcForm->setEntity($fieldName);
		$this->BcForm->BcUpload->request->data = [
			'Contact' => [
				[
					'id' => '1',
					'eye_catch' => 'template1.jpg',
					'modified' => '2013-07-21 01:41:12', 'created' => '2013-07-21 00:53:42',
				],
			]
		];

		$result = $this->BcForm->file($fieldName);

		$expected = [
			['input' => ['type' => 'file', 'name' => 'data[Contact][0][eye_catch]', 'id' => 'Contact0EyeCatch']],
			'&nbsp;',
			['span' => []],
			['input' => ['type' => 'hidden', 'name' => 'data[Contact][0][eye_catch_delete]', 'id' => 'Contact0EyeCatchDelete_', 'value' => '0']],
			['input' => ['type' => 'checkbox', 'name' => 'data[Contact][0][eye_catch_delete]', 'value' => '1', 'id' => 'Contact0EyeCatchDelete']],
			'label' => ['for' => 'Contact0EyeCatchDelete'],
			'削除する',
			'/label',
			'/span',
			['input' => ['type' => 'hidden', 'name' => 'data[Contact][0][eye_catch_]', 'value' => 'template1.jpg', 'id' => 'Contact0EyeCatch']],
			['br' => true],
			['figure' => []],
			'a' => ['href' => 'preg:/' . preg_quote('/files/template1.jpg?', '/') . '\d+/', 'rel' => 'colorbox', 'title' => ''],
			['img' => ['src' => 'preg:/' . preg_quote('/files/template1.jpg?', '/') . '\d+/', 'alt' => '']],
			'/a',
			['br' => true],
			['figcaption' => ['class' => 'file-name']],
			'template1.jpg',
			'/figcaption',
			'/figure',
		];

		$this->assertTags($result, $expected);
	}

	/**
	 * フォームのIDを取得する
	 *
	 * @dataProvider getIdDataProvider
	 */
	public function testGetId($Model, $expected)
	{
		$this->BcForm->create($Model);
		$this->assertEquals($expected, $this->BcForm->getId());
	}

	public function getIdDataProvider()
	{
		return [
			['', 'addForm'],
			['hogehoge', 'hogehogeAddForm'],
			['CakeSchema', 'CakeSchemaAddForm'],
			['Content', 'ContentAddForm'],
			['EditTemplate', 'EditTemplateAddForm'],
			['Favorite', 'FavoriteAddForm'],
			['Member', 'MemberAddForm'],
			['Page', 'PageAddForm'],
			['Plugin', 'PluginAddForm'],
			['Site', 'SiteAddForm'],
			['SiteConfig', 'SiteConfigAddForm'],
			['Theme', 'ThemeAddForm'],
			['ThemeFile', 'ThemeFileAddForm'],
			['ThemeFolder', 'ThemeFolderAddForm'],
			['Tool', 'ToolAddForm'],
			['Updater', 'UpdaterAddForm'],
			['User', 'UserAddForm'],
			['UserGroup', 'UserGroupAddForm']
		];
	}
}
