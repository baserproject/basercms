<?php
/**
 * test for BcTextHelper
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @since			baserCMS v 3.0.6
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcFreezeHelper', 'View/Helper');


/**
 * FormHelperTest class
 *
 * @package Cake.Test.Case.View.Helper
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
 * testFileUploadField method
 *
 * @return void
 */
	public function testFileUploadField() {
		
	}

}
