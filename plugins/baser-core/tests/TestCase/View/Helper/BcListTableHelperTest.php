<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\View\AppView;
use BaserCore\View\Helper\BcListTableHelper;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class BcLIstTableHelperTest
 *
 * @property BcListTableHelper $BcListTable
 */
class BcLIstTableHelperTest extends BcTestCase
{
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $View = new AppView();
        $this->BcListTable = new BcListTableHelper($View);
    }

    public function tearDown(): void
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // $this->BcListTable->rowClass($isPublished);
        // $this->expectOutputRegex('/' . $expected . '/s');
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
