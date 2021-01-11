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
App::uses('BcListTableHelper', 'View/Helper');

/**
 * Class BcLIstTableHelperTest
 *
 * @property BcListTableHelper $BcListTable
 */
class BcLIstTableHelperTest extends CakeTestCase
{

	public function setUp()
	{
		parent::setUp();
		$View = new View();
		$this->BcListTable = new BcListTableHelper($View);
	}

	public function tearDown()
	{
		unset($this->BcListTable);
		parent::tearDown();
	}

	public function testDispatchShowHead()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	public function testDispatchShowRow()
	{
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

	/**
	 * @param $isPublished
	 *
	 * @dataProvider rowClassDataProvider
	 */
	public function testRowClass($isPublished, $expected)
	{
		$this->BcListTable->rowClass($isPublished);
		$this->expectOutputRegex('/' . $expected . '/s');
	}

	public function rowClassDataProvider()
	{
		return [
			[true, 'class="publish bca-table-listup__tbody-tr"'],
			[false, 'class="unpublish disablerow bca-table-listup__tbody-tr"']
		];
	}

	/**
	 * カラム数をセットする
	 */
	public function testSetColumnNumber()
	{
		$this->BcListTable->setColumnNumber(1);
		$this->assertEquals(1, $this->BcListTable->getColumnNumber());
		$this->BcListTable->setColumnNumber('hoge');
		$this->assertEquals('hoge', $this->BcListTable->getColumnNumber());
	}

	/**
	 * カラム数を取得する
	 */
	public function testGetColumnNumber()
	{
		$this->assertEquals(0, $this->BcListTable->getColumnNumber());
	}
}
