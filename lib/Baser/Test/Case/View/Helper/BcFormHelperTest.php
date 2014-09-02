<?php
/**
 * test for BcTextHelper
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.6-beta
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcFormHelper', 'View/Helper');


/**
 * FormHelperTest class
 *
 * @package Cake.Test.Case.View.Helper
 * @property BcFormHelper $Form
 */
class BcFormHelperTest extends CakeTestCase {

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

}
