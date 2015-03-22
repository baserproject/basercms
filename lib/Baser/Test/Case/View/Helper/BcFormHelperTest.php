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
		'baser.Default.Page'
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
