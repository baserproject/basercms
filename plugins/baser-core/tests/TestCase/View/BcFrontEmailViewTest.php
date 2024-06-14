<?php

namespace BaserCore\Test\TestCase\View;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcFrontEmailView;

class BcFrontEmailViewTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcFrontEmailView = new BcFrontEmailView($this->getRequest());
    }

    public function tearDown(): void
    {
        unset($this->BcFrontEmailView);
        parent::tearDown();
    }

    public function testInitialize(): void
    {
        $this->assertNotEmpty($this->BcFrontEmailView->BcHtml);
    }
}