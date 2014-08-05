<?php
/**
 * test for BcUploadHelper
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Test.Case.View.Helper
 * @since           baserCMS v 3.0.0-beta
 * @license			http://basercms.net/license/index.html
 */

App::uses('BcUploadHelper', 'View/Helper');

/**
 * test for BcUploadHelper
 *
 * @package			Baser.Test.Case.View.Helper
 */
class BcUploadHelperTest extends CakeTestCase {
	
/**
 * Fixtures
 * @var array 
 */
	public $fixtures = array(
		'baser.PluginContent'
	);

/**
 * setUp
 */
	public function setUp() {
		parent::setUp();
		$this->BcUpload = new BcUploadHelper(new View);
		$this->BcUpload->request = new CakeRequest('/', false);
	}

/**
 * tearDown
 */
	public function tearDown() {
		unset($this->BcUpload);
		parent::tearDown();
	}

/**
 * ファイルタグを出力する
 */
	public function test_file() {
		$result = $this->BcUpload->file('EditorTemplate.image');
		$expects = '<div class="upload-file"><input type="file" name="data[EditorTemplate][image]"  id="EditorTemplateImage"/></div>';
		$this->assertEqual($expects, $result);
	}

/**
 * ファイルへのリンクタグを出力する
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
		$this->assertRegExp('/<a href=\"\/files\/editor\/template1\.jpg/', $result);
	}

/**
 * アップロードした画像のタグを出力する
 */
	public function test_uploadImage() {
		
		// オプションなし
		$result = $this->BcUpload->uploadImage('EditorTemplate.image', 'template1.jpg');
		$this->assertRegExp('/^<a href=\"\/files\/editor\/template1\.jpg[^>]+?\"[^>]+?><img src=\"\/files\/editor\/template1\.jpg[^>]+?\"[^>]+?><\/a>/', $result);
		
		// サイズ指定あり
		$options = array(
			'width' => '100',
			'height' => '80',
		);
		$result = $this->BcUpload->uploadImage('EditorTemplate.image', 'template1.jpg', $options);
		$expects = '<img src="/uploads/tmp/midium/template1.jpg" alt="" width="100" height="80" />';
		$this->assertRegExp('/^<a href=\"\/files\/editor\/template1\.jpg[^>]+?\"[^>]+?><img src=\"\/files\/editor\/template1\.jpg[^>]+?\"[^>]+?alt="" width="100" height="80"[^>]+?><\/a>/', $result);

		// 一時ファイルへのリンク（デフォルトがリンク付だが、Aタグが出力されないのが正しい挙動）
		$options = array(
			'tmp' => true
		);
		$result = $this->BcUpload->uploadImage('EditorTemplate.image', 'template1.jpg', $options);
		$expects = '<img src="/uploads/tmp/midium/template1.jpg" alt="" />';
		$this->assertEqual($expects, $result);

	}

}
