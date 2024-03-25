<?php

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use BcCustomContent\View\Helper\CustomContentAdminHelper;
use Cake\View\View;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class CustomContentAdminHelperTest extends BcTestCase
{

    use ScenarioAwareTrait;
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
        //case option is empty
        $customLink = CustomLinkFactory::make([
            'name' => 'test',
        ])->getEntity();
        $result = $this->CustomContentAdminHelper->getFieldName($customLink);
        //check result return
        $this->assertEquals('test', $result);
        //case option is not empty
        $options = [
            'fieldName' => 'fieldName option',
        ];
        $result = $this->CustomContentAdminHelper->getFieldName($customLink, $options);
        //check result return
        $this->assertEquals('fieldName option', $result);
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
            'attention' => ''
        ])->getEntity();
        $result = $this->CustomContentAdminHelper->attention($customLink);
        $this->assertEquals('', $result);
        //case attention is not empty
        $customLink['attention'] = 'test attention';
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
        $customLink = CustomLinkFactory::make([
            'after_head' => ''
        ])->getEntity();
        //case after_head is empty
        $result = $this->CustomContentAdminHelper->afterHead($customLink);
        $this->assertEquals('', $result);
        //case after_head is not empty
        $customLink['after_head'] = 'test after head';
        $result = $this->CustomContentAdminHelper->afterHead($customLink);
        $this->assertEquals('&nbsp;test after head', $result);
    }

    /**
     * test isAllowPublishEntry
     */
    public function test_isAllowPublishEntry()
    {
        //サービスクラス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => 'title',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //データ生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        $customEntriesService->setup(1);
        //case customEntry exists
        $rs = $this->CustomContentAdminHelper->isAllowPublishEntry($customEntriesService->get(1));
        $this->assertTrue($rs);
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

}