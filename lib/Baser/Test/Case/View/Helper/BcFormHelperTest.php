<?php
/**
 * test for BcTextHelper
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.6-beta
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcFormHelper', 'View/Helper');

/**
 * Contact class
 *
 * @package		Baser.Test.Case.View.Helper
 */
class Contact extends CakeTestModel {

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
 * @access public
 */
	public $actsAs = array(
		'BcContentsManager',
		'BcCache',
		'BcUpload' => array(
			'subdirDateFormat' => 'Y/m/',
			'fields' => array(
				'eye_catch' => array(
					'type' => 'image',
					'namefield' => 'no',
					'nameformat' => '%08d'
				)
			)
		)
	);
}

/**
 * FormHelperTest class
 *
 * @package		Baser.Test.Case.View.Helper
 * @property	BcFormHelper $BcForm
 */
class BcFormHelperTest extends BaserTestCase {

/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'baser.Default.Page',
		'baser.Default.Plugin',
		'baser.Default.PluginContent',
	);
	
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
		$this->BcForm = new BcFormHelper(new View);
		$this->BcForm->request = new CakeRequest('contacts/add', false);
		$this->BcForm->request->here = '/contacts/add';
		$this->BcForm->request['action'] = 'add';
		$this->BcForm->request->webroot = '';
		$this->BcForm->request->base = '';
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->BcForm);
	}

/**
 * Returns a set of SELECT elements for a full datetime setup: day, month and year, and then time.
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param string $dateFormat DMY, MDY, YMD, or null to not generate date inputs.
 * @param string $timeFormat 12, 24, or null to not generate time inputs.
 * @param array $attributes Array of Attributes
 * @param string $expected 期待値
 * @dataProvider dateTimeDataProvider
 */
	public function testDateTime($fieldName, $dateFormat, $timeFormat, $attributes, $expected) {
		$result = $this->BcForm->dateTime($fieldName, $dateFormat, $timeFormat, $attributes);
    $this->assertRegExp('/' . $expected . '/s', $result);
	}

/**
 * dateTime用のデータプロバイダ
 *
 * @return array
 */
  public function dateTimeDataProvider() {
    return array(
      array('test', 'W', '12', array('maxYear' => 2010), 'id="testWareki".*<option value="h-22">平成 22'),
      array('test', 'WY', '12', array(), '年.*年'),
      array('test', 'WM', '12', array(), '月'),
      array('test', 'WD', '12', array(), '日'),
    );
  }

/**
 * Creates a checkbox input widget.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @param string $expected 期待値
 * @dataProvider checkboxDataProvider
 */
	public function testCheckbox($fieldName, $options, $expected) {
		$result = $this->BcForm->checkbox($fieldName, $options);
    $this->assertRegExp('/' . $expected . '/s', $result);
	}

/**
 * checkbox用のデータプロバイダ
 *
 * @return array
 */
  public function checkboxDataProvider() {
    return array(
      array('test', array(), 'type="hidden"'),
      array('test', array('label' => 'testLabel'), '<label for="test"><input type="checkbox".*label="testLabel"'),    );
  }

/**
 * Creates a hidden input field.
 *
 * @param string $fieldName Name of a field, in the form of "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @param string $expected 期待値
 * @dataProvider hiddenDataProvider
 */
	public function testHidden($fieldName, $options, $expected) {
		$result = $this->BcForm->hidden($fieldName, $options);
    $this->assertRegExp('/' . $expected . '/s', $result);
	}

/**
 * hidden用のデータプロバイダ
 *
 * @return array
 */
  public function hiddenDataProvider() {
    return array(
      array('test', array(), 'id="test"'),
      array('test', array('multiple' => 'checkbox'), 'name="data\[test\]"\/>$'),
      array('test', array('multiple' => 'checkbox', 'value' => array('value1','value2')), 'name="data\[test\]\[\]".* value="value1".*value="value2"'),
    );
  }


/**
 * create
 * フック用にラッピング
 * 
 * @param array $model
 * @param array $options
 * @return string
 * @access public
 */
	public function create($model, $options) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


/**
 * end
 * フック用にラッピング
 *
 * @param	array	$options
 * @return	string
 * @access	public
 */
	public function end($options, $secureAttributes) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


/**
 * Generates a form input element complete with label and wrapper div
 *
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param array $options Each type of input takes different options.
 * @return string Completed form widget
 */
	public function input($fieldName, $options = array()) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}


/**
 * CKEditorを出力する
 *
 * @param	string	$fieldName
 * @param	array	$options
 * @param	array	$editorOptions
 * @param	array	$styles
 * @param string $expected 期待値
 * @dataProvider ckeditorDataProvider
 */
	public function testCkeditor($fieldName, $options, $expected) {
    $result = $this->BcForm->ckeditor($fieldName, $options);
    $this->assertRegExp('/' . $expected . '/', $result);
	}

/**
 * ckeditor用のデータプロバイダ
 *
 * @return array
 */
  public function ckeditorDataProvider() {
    return array(
      array('test', array(), '<textarea name="data\[test\]"'),
      array('test', array('editorLanguage' => 'en'), '"language":"en"'),
  	);
  }	

/**
 * エディタを表示する
 * 
 * @param string $fieldName
 * @param array $options
 * @param string $expected 期待値
 * @dataProvider editorDataProvider
 */
	public function testEditor($fieldName, $options, $expected) {
    $result = $this->BcForm->editor($fieldName, $options);
    $this->assertRegExp('/' . $expected . '/', $result);
	}

/**
 * editor用のデータプロバイダ
 *
 * @return array
 */
  public function editorDataProvider() {
    return array(
      array('test', array(), '<textarea name="data\[test\]"'),
      array('test', array('editorLanguage' => 'en'), '"language":"en"'),
  	);
  }


/**
 * 都道府県用のSELECTタグを表示する
 *
 * @param string $fieldName Name attribute of the SELECT
 * @param mixed $selected Selected option
 * @param array $attributes Array of HTML options for the opening SELECT element
 * @param string $expected 期待値
 * @dataProvider prefTagDataProvider
 */
	public function testPrefTag($fieldName, $selected, $attributes, $expected) {
    $result = $this->BcForm->prefTag($fieldName, $selected, $attributes);
    $this->assertRegExp('/' . $expected . '/s', $result);
	}

/**
 * prefTag用のデータプロバイダ
 *
 * @return array
 */
  public function prefTagDataProvider() {
    return array(
      array('test', null, array(), '<select name="data\[test\]" id="test">.<option value="">都道府県.*<option value="1">北海道.*<option value="47">沖縄県'),
      array('test', '40', array(), '<option value="40" selected="selected">'),
      array('test', null, array('class' => 'testclass'), ' class="testclass"'),
  	);
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
 * @param string $expected 期待値
 * @dataProvider wyearDataProvider
 */
	public function testWyear($fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty, $expected) {
    $result = $this->BcForm->wyear($fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty);
    $this->assertRegExp('/' . $expected . '/s', $result);
	}

/**
 * wyear用のデータプロバイダ
 *
 * @return array
 */
  public function wyearDataProvider() {
    return array(
      array('test', null, null, null, array(), true, '<input type="hidden" name="data\[test\]\[wareki\].*<option value="h-'),
      array('test', 2010, null, null, array(), true, '<option value="h-22">平成 22<\/option>.<\/select>$'),
      array('test', null, 2010, null, array(), true, 'id="testYear">.<option value=""><\/option>.<option value="h-22">'),
      array('test', null, null, 'h-47', array(), true, 'value="h-47" selected'),
      array('test', null, null, null, array('class' => 'testclass'), true, 'class="testclass"'),
      array('test', null, null, null, array('class' => 'testclass', 'size' => '5'), true, 'size="5"'),
      array('test', null, null, null, array(), false, 'id="testYear">.<option value="h-'),
  	);
  }
	
/**
 * コントロールソースを取得する
 * Model側でメソッドを用意しておく必要がある
 *
 * @param string $field フィールド名
 * @param array $options
 * @return array コントロールソース
 * @access public
 */
	public function getControlSource($field, $options) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * モデルよりリストを生成する
 *
 * @param string $modelName
 * @param mixed $conditions
 * @param mixed $fields
 * @param mixed $order
 * @return mixed リストまたは、false
 * @access public
 */
	public function generateList($modelName, $conditions, $fields, $order) {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * JsonList
 *
 * @param string $field フィールド文字列
 * @param string $attributes
 * @param string $expected 期待値
 * @dataProvider jsonListDataProvider
 */
	public function testJsonList($field, $attributes, $expected) {

		$attributes_default = array(
			'imgSrc' => null,
			'ajaxAddAction' => null,
			'ajaxDelAction' => null,
		);

		$attributes = $attributes + $attributes_default;


    $result = $this->BcForm->jsonList($field, $attributes);
    $this->assertRegExp('/' . $expected . '/s', $result);
	}

/**
 * jsonList用のデータプロバイダ
 *
 * @return array
 */
  public function jsonListDataProvider() {
    return array(
      array('test', array(), 'id="JsonTestDb".*jQuery\(function\(\)'), 
      array('test', array('imgSrc' => 'test'), '"deleteButtonSrc":"test"'), 
      array('test', array('ajaxAddAction' => 'test'), '"ajaxAddAction":"test"'), 
      array('test', array('ajaxDelAction' => 'test'), '"ajaxDelAction":"test"'), 
  	);
  }

/**
 * カレンダーコントロール付きのテキストフィールド
 * jquery-ui-1.7.2 必須
 *
 * @param string フィールド文字列
 * @param array HTML属性
 * @param string $expected 期待値
 * @dataProvider datepickerDataProvider
 */
	public function testDatepicker($fieldName, $attributes, $expected) {
    $result = $this->BcForm->datepicker($fieldName, $attributes);
    $this->assertRegExp('/' . $expected . '/s', $result);
	}

/**
 * jsonList用のデータプロバイダ
 *
 * @return array
 */
  public function datepickerDataProvider() {
    return array(
      array('baser', array(), 'type="text".*id="baser".*("#baser")'), 
      array('baser', array('test1' => 'testValue1'), 'test1="testValue1"'),
      array('baser', array('value' => '2010-4-1'), 'value="2010\/04\/01"'),
  	);
  }

/**
 * 日付カレンダーと時間フィールド
 * 
 * @param string $fieldName
 * @param array $attributes
 * @param string $expected 期待値
 * @dataProvider dateTimePickerDataProvider
 */
	public function testDateTimePicker($fieldName, $attributes, $expected) {
    $result = $this->BcForm->dateTimePicker($fieldName, $attributes);
    $this->assertRegExp('/' . $expected . '/s', $result);
	}

/**
 * jsonList用のデータプロバイダ
 *
 * @return array
 */
  public function dateTimePickerDataProvider() {
    return array(
      array('baser', array(), 'id="baser_date".*\$\("#baser_date"\)\.datepicker\(\);'), 
      array('baser', array('value' => '2010-4-1 11:22:33'), 'value="2010\/04\/01".* value="11:22:33".*value="2010-4-1 11:22:33"'), 
      array('baser', array('value' => '2010-4-1'), 'value="2010\/04\/01".*value="00:00:00".* value="2010-4-1"'), 
      array('baser', array('value' => '2010-'), 'value="1970\/01\/01".*value="09:00:00".*value="2010-"'), 
  	);
  }

/**
 * 文字列保存用複数選択コントロール
 * 
 * @param string $fieldName
 * @param array $options
 * @param mixed $selected
 * @param array $attributes
 * @param mixed $showEmpty
 * @return string
 * @access public
 */
	public function selectText($fieldName, $options, $selected, $attributes, $showEmpty) {

	}



/**
 * ファイルインプットボックス出力
 * 
 * @param string $fieldName
 * @param array $options
 * @return string
 */
	public function file($fieldName, $options = array()) {

	}

/**
 * フォームの最後のフィールドの後に発動する前提としてイベントを発動する
 * 
 * @param string $type フォームのタイプ タイプごとにイベントの登録ができる
 * @return string 行データ
 */
	public function dispatchAfterForm($type = '') {

	}

/**
 * Creates a set of radio widgets. Will create a legend and fieldset
 * by default. Use $options to control this
 * 
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Radio button options array.
 * @param array $attributes Array of HTML attributes, and special attributes above.
 * @return string Completed radio widget set.
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#options-for-select-checkbox-and-radio-inputs
 */
	public function radio($fieldName, $options = array(), $attributes = array()) {

	}

/**
 * testFormCreateWithSecurity method
 *
 * Test BcForm->create() with security key.
 * 
 * @return void
 */
	public function testCreateWithSecurity() {
		$this->BcForm->request['_Token'] = array('key' => 'testKey');
		$encoding = strtolower(Configure::read('App.encoding'));
		$result = $this->BcForm->create('Contact', array('url' => '/contacts/add'));
		// CUSTOMIZE 2014/09/02 ryuring
		// ブラウザの妥当性のチェックを除外する為、novalidate 属性をデフォルトで追加するように変更した
		$expected = array(
			'form' => array('method' => 'post', 'action' => '/contacts/add', 'accept-charset' => $encoding, 'id' => 'ContactAddForm', 'novalidate' => 'novalidate'),
			'div' => array('style' => 'display:none;'),
			array('input' => array('type' => 'hidden', 'name' => '_method', 'value' => 'POST')),
			array('input' => array(
				'type' => 'hidden', 'name' => 'data[_Token][key]', 'value' => 'testKey', 'id'
			)),
			'/div'
		);
		
		$this->assertTags($result, $expected);
		$result = $this->BcForm->create('Contact', array('url' => '/contacts/add', 'id' => 'MyForm'));
		$expected['form']['id'] = 'MyForm';
		$this->assertTags($result, $expected);
	}

/**
 * testFileUploadField method
 *
 * @return void
 */
	public function testFileUploadField() {
		
		$fieldName = 'Contact.upload';
		$this->BcForm->setEntity($fieldName);
		// 通常
		$result = $this->BcForm->file($fieldName);
		$expected = array(
			'div'	=> array('class' => 'upload-file'),
			array('input'	=> array('type' => 'file', 'name' => 'data[Contact][upload]', 'id' => 'ContactUpload')), 
			'/div'
		);
		$this->assertTags($result, $expected);

	}

/**
 * ファイルアップロードフィールドのテスト
 *
 * データと画像が既に存在する場合について
 *
 * @return void
 */
	public function testFileUploadFieldWithImageFile() {
		$fieldName = 'Contact.eye_catch';
		$this->BcForm->setEntity($fieldName);
		$this->BcForm->BcUpload->request->data = array(
			'Contact' => array(
				'id' => '1',
				'eye_catch' => 'template1.jpg',
				'modified' => '2013-07-21 01:41:12', 'created' => '2013-07-21 00:53:42',
			)
		);

		$result = $this->BcForm->file($fieldName);

		$expected = array(
			'div'	=> array('class' => 'upload-file'),
			array('input' => array('type' => 'file', 'name' => 'data[Contact][eye_catch]', 'id' => 'ContactEyeCatch')),
			'&nbsp;',
			array('input' => array('type' => 'hidden', 'name' => 'data[Contact][eye_catch_delete]', 'id' => 'ContactEyeCatchDelete_', 'value' => '0')),
			array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][eye_catch_delete]', 'value' => '1', 'id' => 'ContactEyeCatchDelete')),
			'label' => array('for' => 'ContactEyeCatchDelete'),
			'削除する',
			'/label',
			array('input'	=> array('type' => 'hidden', 'name' => 'data[Contact][eye_catch_]', 'id' => 'ContactEyeCatch')),
			array('br' => true),
			'a' => array('href' => 'preg:/' . preg_quote('/files/template1.jpg?', '/') . '\d+/', 'rel' => 'colorbox', 'title' => ''),
			array('img' => array('src' => 'preg:/' . preg_quote('/files/template1.jpg?', '/') . '\d+/', 'alt' => '')),
			'/a',
			array('br' => true),
			'span' => array('class' => 'file-name'),
			'template1.jpg',
			'/span',
			'/div'
		);

		$this->assertTags($result, $expected);
	}

/**
 * ファイルアップロードフィールドのテスト（hasMany対応）
 *
 * @return void
 */
	public function testFileUploadFieldHasManyField() {
		$fieldName = 'Contact.0.upload';
		$this->BcForm->setEntity($fieldName);

		// 通常
		$result = $this->BcForm->file($fieldName);

		$expected = array(
			'div'	=> array('class' => 'upload-file'),
			array('input'	=> array('type' => 'file', 'name' => 'data[Contact][0][upload]', 'id' => 'Contact0Upload')),
			'/div'
		);
		$this->assertTags($result, $expected);
	}

/**
 * ファイルアップロードフィールドのテスト（hasMany対応）
 *
 * データと画像が既に存在する場合について
 *
 * @return void
 */
	public function testFileUploadFieldHasManyFieldWithImageFile() {
		$fieldName = 'Contact.0.eye_catch';
		$this->BcForm->setEntity($fieldName);
		$this->BcForm->BcUpload->request->data = array(
			'Contact' => array(
				array(
					'id' => '1',
					'eye_catch' => 'template1.jpg',
					'modified' => '2013-07-21 01:41:12', 'created' => '2013-07-21 00:53:42',
				),
			)
		);

		$result = $this->BcForm->file($fieldName);

		$expected = array(
			'div'	=> array('class' => 'upload-file'),
			array('input' => array('type' => 'file', 'name' => 'data[Contact][0][eye_catch]', 'id' => 'Contact0EyeCatch')),
			'&nbsp;',
			array('input' => array('type' => 'hidden', 'name' => 'data[Contact][0][eye_catch_delete]', 'id' => 'Contact0EyeCatchDelete_', 'value' => '0')),
			array('input' => array('type' => 'checkbox', 'name' => 'data[Contact][0][eye_catch_delete]', 'value' => '1', 'id' => 'Contact0EyeCatchDelete')),
			'label' => array('for' => 'Contact0EyeCatchDelete'),
			'削除する',
			'/label',
			array('input'	=> array('type' => 'hidden', 'name' => 'data[Contact][0][eye_catch_]', 'id' => 'Contact0EyeCatch')),
			array('br' => true),
			'a' => array('href' => 'preg:/' . preg_quote('/files/template1.jpg?', '/') . '\d+/', 'rel' => 'colorbox', 'title' => ''),
			array('img' => array('src' => 'preg:/' . preg_quote('/files/template1.jpg?', '/') . '\d+/', 'alt' => '')),
			'/a',
			array('br' => true),
			'span' => array('class' => 'file-name'),
			'template1.jpg',
			'/span',
			'/div'
		);

		$this->assertTags($result, $expected);
	}

}
