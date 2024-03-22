<?php

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\View\Helper\CustomContentAppHelper;
use Cake\View\View;

class CustomContentAppHelperTest extends BcTestCase
{
    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentAppHelper = new CustomContentAppHelper(new View());
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
     * test loadPluginHelper
     */
    public function test_loadPluginHelper()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }
    /**
     * test isEnableField
     *
     */
    public function test_isEnableField()
    {
        /**
         * case customField is not exists
         */
        $customLink = CustomLinkFactory::make([
            'name' => 'custom link'
        ])->getEntity();
        $rs = $this->CustomContentAppHelper->isEnableField($customLink);
        $this->assertFalse($rs);
        /**
         * case customField is exists
         * and children is empty
         */
        $customLink['customField'] = [
            'type' => 'group'
        ];
        $rs = $this->CustomContentAppHelper->isEnableField($customLink);
        $this->assertFalse($rs);
        /**
         * case customField is exists
         * and children is not empty
         */
        $customLink['status'] = 1;
        $customLink['children'] = ['name' => 'child'];
        $customLink['custom_field'] = ['type' => 'group'];
        $rs = $this->CustomContentAppHelper->isEnableField($customLink);
        $this->assertTrue($rs);
    }

    /**
     * test getEntryUrl
     *
     */
    public function test_getEntryUrl()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test searchControl
     */
    public function test_searchControl()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test isDisplayEntrySearch
     */
    public function test_isDisplayEntrySearch()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }
}