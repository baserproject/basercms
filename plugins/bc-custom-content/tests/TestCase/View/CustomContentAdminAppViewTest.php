<?php

namespace BcCustomContent\Test\TestCase\View;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\View\CustomContentAdminAppView;

class CustomContentAdminAppViewTest extends BcTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_initialize(): void
    {
        $this->getRequest();
        $customContentAdminAppView = new CustomContentAdminAppView($this->getRequest());
        $this->assertNotEmpty($customContentAdminAppView->CustomContentAdmin);
    }
}