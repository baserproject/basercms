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
App::uses('BcHtmlHelper', 'View/Helper');

/**
 * Class BcHtmlHelperTest
 *
 * @property BcHtmlHelper $BcHtml
 */
class BcHtmlHelperTest extends CakeTestCase
{

	public function setUp()
	{
		parent::setUp();
		$View = new View();
		$this->BcHtml = new BcHtmlHelper($View);
	}

	public function tearDown()
	{
		unset($this->BcHtml);
		parent::tearDown();
	}

	/**
	 * タグにラッピングされていないパンくずデータを取得する
	 */
	public function testGetStripCrumbs()
	{
		$expected = 'abc';
		$this->BcHtml->_crumbs = [$expected];
		$crumbs = $this->BcHtml->getCrumbs();
		$this->assertEquals('<a href="/b" c>a</a>', $crumbs);
		$this->assertEquals([$expected], $this->BcHtml->getStripCrumbs());
	}
}
