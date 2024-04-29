<?php

namespace BcWidgetArea\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcWidgetArea\View\Helper\BcWidgetAreaBaserHelper;
use Cake\View\View;

class BcWidgetAreaBaserHelperTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcWidgetAreaBaserHelper = new BcWidgetAreaBaserHelper(new View());
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test methods
     */

    public function test_methods()
    {
        $methods = $this->BcWidgetAreaBaserHelper->methods();
        $this->assertEquals(['BcWidgetArea', 'widgetArea'], $methods['widgetArea']);
        $this->assertEquals(['BcWidgetArea', 'getWidgetArea'], $methods['getWidgetArea']);
    }
}