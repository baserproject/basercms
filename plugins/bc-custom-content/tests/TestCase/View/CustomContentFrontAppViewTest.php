<?php

namespace BcCustomContent\Test\TestCase\View;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\View\CustomContentFrontAppView;

class CustomContentFrontAppViewTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->getRequest();
        $customContentFrontAppView = new CustomContentFrontAppView($this->getRequest());
        $this->assertNotEmpty($customContentFrontAppView->CustomContent);
    }
}