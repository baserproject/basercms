<?php

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Entity\CustomField;
use BcCustomContent\Model\Entity\CustomLink;
use BcCustomContent\View\Helper\CustomContentAdminHelper;
use Cake\View\Helper;
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
        $customLink = new CustomLink([
            'id' => 1,
            'name' => 'test custom link',
            'status' => 1,
            'custom_table_id' => 1
        ]);
        $rs = $this->CustomContentAdminHelper->isDisplayEntryList($customLink);
        $this->assertFalse($rs);
        //customField is not null and empty children
        $customField = new CustomField([
            'id' => 1,
            'name' => 'test custom field',
            'status' => 1,
            'custom_table_id' => 1,
            'type' => 'group',
        ]);
        $customLink = new CustomLink([
            'id' => 1,
            'name' => 'test custom link',
            'status' => 1,
            'custom_table_id' => 1,
            'display_admin_list' => 1,
            'custom_field' => $customField
        ]);
        $rs = $this->CustomContentAdminHelper->isDisplayEntryList($customLink);
        $this->assertFalse($rs);
        //customField is not null and not empty children
        $customField = new CustomField([
            'id' => 1,
            'name' => 'test custom field',
            'status' => 1,
            'custom_table_id' => 1,
            'type' => 'group',
        ]);
        $customLink = new CustomLink([
            'id' => 1,
            'name' => 'test custom link',
            'status' => 1,
            'custom_table_id' => 1,
            'display_admin_list' => 1,
            'custom_field' => $customField,
            'children' => [
                new CustomLink([
                    'id' => 1,
                    'name' => 'test custom link',
                    'status' => 1,
                    'custom_table_id' => 1,
                    'display_admin_list' => 1,
                    'custom_field' => $customField
                ])
            ]
        ]);
        $rs = $this->CustomContentAdminHelper->isDisplayEntryList($customLink);
        $this->assertTrue($rs);
    }
}