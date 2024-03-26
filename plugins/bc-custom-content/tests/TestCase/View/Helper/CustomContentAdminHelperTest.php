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
        $this->markTestIncomplete('このテストはまだ実装されていません。');
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
        //case attention is empty
        $customLink = CustomLinkFactory::make([
            'attention' => '',
        ])->getEntity();
        $result = $this->CustomContentAdminHelper->attention($customLink);
        $this->assertEquals('', $result);
        //case attention is not empty
        $customLink = CustomLinkFactory::make([
            'attention' => 'test attention',
        ])->getEntity();
        $result = $this->CustomContentAdminHelper->attention($customLink);
        $this->assertEquals('<div class="bca-attention"><small>test attention</small></div>', $result);
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
        //case description is not empty
        $customLink = CustomLinkFactory::make([
            'description' => 'test description',
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->description($customLink);
        //check result return
        $this->assertEquals('<i class="bca-icon--question-circle bca-help"></i><div class="bca-helptext">test description</div>', $rs);
        //case description is empty
        $customLink = CustomLinkFactory::make([
            'description' => '',
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->description($customLink);
        //check result return
        $this->assertEquals('', $rs);

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

    /*
     * test getEntryColumnsNum
     */
    public function test_getEntryColumnsNum()
    {
        //case isDisplayEntryList is false
        $arrCustomLink = [
            CustomLinkFactory::make([
                'display_admin_list' => 1
            ])->getEntity()
        ];
        $rs = $this->CustomContentAdminHelper->getEntryColumnsNum($arrCustomLink);
        $this->assertEquals(6, $rs);
        //case isDisplayEntryList is true
        $arrCustomLink = [
            CustomLinkFactory::make([
                'display_admin_list' => 1,
                'custom_field' => [
                    'type' => 'group'
                ],
                'children' => [
                    'name' => 'test children'
                ]
            ])->getEntity()
        ];
        $rs = $this->CustomContentAdminHelper->getEntryColumnsNum($arrCustomLink);
        $this->assertEquals(7, $rs);
    }
}