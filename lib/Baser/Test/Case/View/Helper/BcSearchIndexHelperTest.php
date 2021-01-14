<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.View.Helper
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcAppView', 'View');
App::uses('BcSearchIndexHelper', 'View/Helper');

/**
 * Class BcSearchIndexHelperTest
 * @property BcSearchIndexHelper $BcSearchIndex
 */
class BcSearchIndexHelperTest extends BaserTestCase
{

	/**
	 * Fixtures
	 * @var array
	 */
	public $fixtures = [
		'baser.Default.Content',
		'baser.Default.User',
		'baser.Default.Site',
		'baser.Default.SiteConfig',
	];

	/**
	 * setUp
	 *
	 * @return void
	 */
	public function setUp()
	{
		parent::setUp();
		$this->_View = new BcAppView();
		$this->_View->request = $this->_getRequest('/');
		$this->_View->helpers = ['BcSearchIndex'];
		$this->_View->loadHelpers();
		$this->BcSearchIndex = $this->_View->BcSearchIndex;
	}

	/**
	 * 公開状態確認
	 *
	 * 詳細なテストは、SearchIndex::allowPublish() のテストに委ねる
	 */
	public function testAllowPublish()
	{
		$result = $this->BcSearchIndex->allowPublish([
			'status' => true,
			'publish_begin' => date('Y-m-d H:i:s', strtotime("+1 hour")),
			'publish_end' => ''
		]);
		$this->assertEquals(false, $result);
	}
}
