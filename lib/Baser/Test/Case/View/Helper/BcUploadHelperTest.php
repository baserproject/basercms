<?php
/**
 * test for BcUploadHelper
 *
 * PHP versions 5
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright       Copyright 2008 - 2013, baserCMS Users Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         http://basercms.net/license/index.html
 */

App::uses('View', 'View');
App::uses('Model', 'Model');
App::uses('Controller','Controller');
App::uses('BcAppController','Controller');
App::uses('BcUploadHelper', 'View/Helper');
App::uses('AppHelper', 'View/Helper');
App::uses('EditorTemplate', 'Model');

/**
 * TestController class
 *
 */
class TestController extends BcAppController {
	public $name = 'Test';
	public $uses = null;
}
class BcUploadHelperTest extends CakeTestCase {

	public $fixtures = array(
		'baser.SiteConfig',
		'baser.EditorTemplate',
	);

	public function setUp() {
		parent::setUp();
		Configure::write('App.base', '');
		$this->Controller = new TestController();
		$this->View = new View($this->Controller);

		$this->BcUpload = new BcUploadHelper($this->View);
		$this->BcUpload->request = new CakeRequest('/', false);
		$this->BcUpload->request->here = '/';
		$this->BcUpload->request->webroot = '/';
		$this->BcUpload->request->base = '/';

		ClassRegistry::addObject('EditorTemplTate',new EditorTemplate());

	}

	public function tearDown() {
		unset($this->BcUpload);
		parent::tearDown();
	}
/**
 * [test_file description]
 * @return [type] [description]
 */
	public function test_file() {
		$result = $this->BcUpload->file('EditorTemplate.image');
		$expects = '<div class="upload-file"><input type="file" name="data[EditorTemplate][image]"  id="EditorTemplateImage"/></div>';
		$this->assertEqual($expects,$result);
	}

/**
 * [test_fileLink description]
 * @return [type] [description]
 */
	public function test_fileLink() {
		$this->BcUpload->request->data = array(
			'EditorTemplate' => array(
				'id' => '1',
				'name' => '画像（左）とテキスト',
				'image' => 'template1.jpg',
				'description' => '説明文',
				'modified' => '2013-07-21 01:41:12', 'created' => '2013-07-21 00:53:42',
			)
		);
		$result = $this->BcUpload->fileLink('EditorTemplate.image');
		$this->assertRegExp('/<a href=\"\/files\/editor\/template1\.jpg/',$result);
	}
/**
 * [test_uploadImage description]
 * @return [type] [description]
 */
	public function test_uploadImage1() {
		$options = array(
			'imgsize'	=> 'midium',
			'link'		=> false,
			'escape'	=> false,
			'mobile'	=> false,
			'alt'		=> '',
			'width'		=> '',
			'height'	=> '',
			'noimage'	=> '',
			'tmp'		=> false
		);
		$result = $this->BcUpload->uploadImage('EditorTemplate.image','template1.jpg',$options);
		$this->assertRegExp('/^<img src=\"\/files\/editor\/template1\.jpg/',$result);
	}
/**
 * [test_uploadImage description]
 * @return [type] [description]
 */
	public function test_uploadImage2() {
		$options = array(
			'imgsize'	=> 'midium',
			'link'		=> false,
			'escape'	=> false,
			'mobile'	=> true,
			'alt'		=> '',
			'width'		=> '100',
			'height'	=> '80',
			'noimage'	=> '',
			'tmp'		=> true
		);
		$result = $this->BcUpload->uploadImage('EditorTemplate.image','template1.jpg',$options);
		$expects = '<img src="/uploads/tmp/midium/template1.jpg" alt="" width="100" height="80" />';
		$this->assertEqual($expects,$result);
	}
}
