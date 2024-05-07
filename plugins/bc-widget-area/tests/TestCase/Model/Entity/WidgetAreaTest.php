<?php

namespace BcWidgetArea\Test\TestCase\Model\Entity;

use BaserCore\TestSuite\BcTestCase;
use BcWidgetArea\Model\Entity\WidgetArea;

class WidgetAreaTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->WidgetArea = $this->getTableLocator()->get('BcWidgetArea.WidgetAreas');
    }

    public function tearDown(): void
    {
        unset($this->WidgetArea);
        parent::tearDown();
    }

    /**
     * test _getCount
     */
    public function test_getCount()
    {
        $widgetArea = new WidgetArea([
            'widgets' => 'a:1:{i:0;a:1:{s:4:"test";a:2:{s:4:"sort";i:1;s:4:"name";s:4:"test";}}}',
        ]);
        $getCount = $this->execPrivateMethod($widgetArea, '_getCount', []);
        $this->assertEquals(1, $getCount);
    }

    /**
     * test _getWidgetsArray
     */
    public function test_getWidgetsArray()
    {
        $widgetArea = new WidgetArea([
            'widgets' => 'a:1:{i:0;a:1:{s:4:"test";a:2:{s:4:"sort";i:1;s:4:"name";s:4:"test";}}}',
        ]);
        //正常系実行
        $getWidgetsArray = $this->execPrivateMethod($widgetArea, '_getWidgetsArray', []);
        $this->assertEquals([
            0 => [
                'test' => [
                    'sort' => 1,
                    'name' => 'test',
                ],
            ],
        ], $getWidgetsArray);

        //case empty fields widgets
        $widgetArea = new WidgetArea([
            'widgets' => '',
        ]);
        $getWidgetsArray = $this->execPrivateMethod($widgetArea, '_getWidgetsArray', []);
        $this->assertEquals([], $getWidgetsArray);
    }

    /**
     * test _setWidgets
     */
    public function test_setWidgets()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
    }
}