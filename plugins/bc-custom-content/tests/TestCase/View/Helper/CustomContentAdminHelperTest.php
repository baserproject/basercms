<?php

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\View\Helper\CustomContentAdminHelper;
use Cake\View\View;

class CustomContentAdminHelperTest extends BcTestCase
{

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new View($this->getRequest());
        $this->CustomContentAdminHelper = new CustomContentAdminHelper($view);
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
     * test isDisplayEntryList
     */
    public function test_isDisplayEntryList()
    {
        //customField is null
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'status' => 1,
            'custom_table_id' => 1
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->isDisplayEntryList($customLink);
        $this->assertFalse($rs);
        //customField is not null and empty children
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'status' => 1,
            'custom_table_id' => 1,
            'display_admin_list' => 1,
            'custom_field' => [
                'name' => 'test custom field',
                'status' => 1,
                'custom_table_id' => 1,
                'type' => 'group',
            ]
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->isDisplayEntryList($customLink);
        $this->assertFalse($rs);
        //customField is not null and not empty children
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'status' => 1,
            'custom_table_id' => 1,
            'display_admin_list' => 1,
            'custom_field' => [
                'name' => 'test custom field',
                'status' => 1,
                'custom_table_id' => 1,
                'type' => 'group',
            ],
            'children' => [
                'name' => 'test custom link',
                'status' => 1,
                'custom_table_id' => 1,
                'display_admin_list' => 1,
            ]
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->isDisplayEntryList($customLink);
        $this->assertTrue($rs);
    }

    /**
     * test getFieldName
     */
    public function test_getFieldName()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * test label
     */
    public function test_label()
    {
        /**
         * case customField type BcCcTextarea and customLink parent_id is true
         * and $options is not exist
         */
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'status' => 1,
            'custom_field' => [
                'type' => 'BcCcTextarea',
            ],
            'parent_id' => 1,
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->label($customLink);
        //check result return
        $this->assertEquals('<label for="test-custom-link">Test Custom Link</label><br>', $rs);
        /**
         * case customField type BcCcTextarea and customLink parent_id is true
         * and exist $options
         */
        $options = ['fieldName' => 'is fieldName'];
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'status' => 1,
            'display_admin_list' => 1,
            'custom_field' => [
                'name' => 'test custom field',
                'type' => 'BcCcTextarea',
            ],
            'parent_id' => 1,
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->label($customLink, $options);
        //check result return
        $this->assertEquals('<label for="is-fieldname">Is Field Name</label><br>', $rs);
        /**
         * case customField type BcCcTextarea and customLink parent_id is false
         * and options is not exist
         */
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'status' => 1,
            'display_admin_list' => 1,
            'custom_field' => [
                'name' => 'test custom field',
                'type' => 'BcCcTextarea',
            ],
            'parent_id' => 0,
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->label($customLink);
        //check result return
        $this->assertEquals('<label for="test-custom-link">Test Custom Link</label>', $rs);
        /**
         * case customField type BcCcTextarea and customLink parent_id is false
         * and exist options
         */
        $options = ['fieldName' => 'is fieldName is not empty'];
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'status' => 1,
            'display_admin_list' => 1,
            'custom_field' => [
                'name' => 'test custom field',
                'type' => 'BcCcTextarea',
            ],
            'parent_id' => 0,
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->label($customLink, $options);
        //check result return
        $this->assertEquals('<label for="is-fieldname-is-not-empty">Is Field Name Is Not Empty</label>', $rs);
    }

    /**
     * test required
     */
    public function test_required()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }
    /**
     * test attention
     */
    public function test_attention()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /*
     * test preview
     */
    public function test_preview()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /*
     * test getEntryIndexTitle
     */
    public function test_getEntryIndexTitle()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /*
     * test description
     */
    public function test_description()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /*
     * test beforeHead
     */
    public function test_beforeHead()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /*
     * test afterHead
     */
    public function test_afterHead()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

}