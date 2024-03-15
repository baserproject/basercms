<?php

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\View\Helper\BcCustomContentBaserHelper;
use Cake\View\View;

class BcCustomContentBaserHelperTest extends BcTestCase
{
    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcCustomContentBaserHelper = new BcCustomContentBaserHelper(new View());
    }
    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
    /**
     * test methods
     */
    public function test_methods()
    {
        $methods = $this->BcCustomContentBaserHelper->methods();
        $this->assertEquals(['CustomContent', 'isDisplayEntrySearch'], $methods['isDisplayCustomEntrySearch']);
        $this->assertEquals(['CustomContent', 'searchControl'], $methods['customSearchControl']);
        $this->assertEquals(['CustomContent', 'description'], $methods['customContentDescription']);
        $this->assertEquals(['CustomContent', 'entryTitle'], $methods['customEntryTitle']);
        $this->assertEquals(['CustomContent', 'published'], $methods['customEntryPublished']);
        $this->assertEquals(['CustomContent', 'getLinks'], $methods['getCustomLinks']);
        $this->assertEquals(['CustomContent', 'isDisplayField'], $methods['isDisplayCustomField']);
        $this->assertEquals(['CustomContent', 'getFieldTitle'], $methods['getCustomFieldTitle']);
        $this->assertEquals(['CustomContent', 'getFieldValue'], $methods['getCustomFieldValue']);
    }
}