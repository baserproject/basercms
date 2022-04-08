<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\View\AppView;
use BaserCore\View\Helper\BcListTableHelper;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\Event;

/**
 * Class BcLIstTableHelperTest
 *
 * @property BcListTableHelper $BcListTable
 */
class BcListTableHelperTest extends BcTestCase
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

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->BcListTable);
        parent::tearDown();
    }

    /**
     * test dispatchShowHead
     */
    public function testDispatchShowHead()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test dispatchShowRow
     */
    public function testDispatchShowRow()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test rowClass
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
            [true, 'class="bca-table-listup__tbody-tr publish"'],
            [false, 'class="bca-table-listup__tbody-tr unpublish disablerow"']
        ];
    }

    /**
     * test Dispatch event rowClass
     */
    public function testDispatchEventRowClass()
    {
        $this->entryEventToMock(self::EVENT_LAYER_HELPER, 'BcListTable.rowClass', function(Event $event){
            $data = $event->getData();
            $this->assertTrue(isset($data['record']));
            $this->assertTrue(isset($data['class']));
            $event->setData('class', ['test1', 'test2']);
        });
        $this->BcListTable->rowClass(true);
        $this->expectOutputRegex('/class="test1 test2"/s');
    }

    /**
     * test setColumnNumber
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
     * test getColumnNumber
     * カラム数を取得する
     */
    public function testGetColumnNumber()
    {
        $this->assertEquals(0, $this->BcListTable->getColumnNumber());
    }
}
