<?php

namespace BcCustomContent\Test\TestCase\View\Helper;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BcCustomContent\Model\Entity\CustomEntry;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomEntryFactory;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
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
        $view = new View($this->getRequest('/baser/admin'));
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
        /**
         * case customField type BcCcTextarea and customLink parent_id is true
         * and $options is not exist
         */
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'title' => null,
            'display_admin_list' => 1,
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
        $rs = $this->CustomContentAdminHelper->label($customLink, $options);
        //check result return
        $this->assertEquals('<label for="is-fieldname">Is Field Name</label><br>', $rs);
        /**
         * case customField type BcCcTextarea and customLink parent_id is false
         * and options is not exist
         */
        $customLink['parent_id'] = 0;
        $rs = $this->CustomContentAdminHelper->label($customLink);
        //check result return
        $this->assertEquals('<label for="test-custom-link">Test Custom Link</label>', $rs);
        /**
         * case customField type BcCcTextarea and customLink parent_id is false
         * and exist options
         */
        $options = ['fieldName' => 'is fieldName is not empty'];
        $rs = $this->CustomContentAdminHelper->label($customLink, $options);
        //check result return
        $this->assertEquals('<label for="is-fieldname-is-not-empty">Is Field Name Is Not Empty</label>', $rs);
        /**
         * case customField type not is BcCcTextarea and customLink parent_id is true
         * and options is not exist
         */
        $customLink['custom_field']['type'] = 'BcCcText';
        $rs = $this->CustomContentAdminHelper->label($customLink);
        //check result return
        $this->assertEquals('<label for="test-custom-link">Test Custom Link</label>', $rs);
    }

    /**
     * test required
     */
    public function test_required()
    {
        /**
         * case children is not exists
         * and required is false
         */
        $customLink = CustomLinkFactory::make([
            'required' => 0
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->required($customLink);
        $this->assertEquals('', $rs);
        /**
         * case children is not exists
         * and required is true
         */
        $customLink->required = 1;
        $rs = $this->CustomContentAdminHelper->required($customLink);
        $this->assertEquals("<span class=\"bca-label\" data-bca-label-type=\"required\">必須</span>\n", $rs);
        /**
         * case children is exists
         * and required of children is false
         */
        $customLink->children = [CustomLinkFactory::make([
            'required' => 0
        ])->getEntity()];
        $rs = $this->CustomContentAdminHelper->required($customLink);
        $this->assertEquals('', $rs);
        /**
         * case children is exists
         * and required of children is true
         */
        $customLink->children = [CustomLinkFactory::make([
            'required' => 1
        ])->getEntity()];
        $rs = $this->CustomContentAdminHelper->required($customLink);
        $this->assertEquals("<span class=\"bca-label\" data-bca-label-type=\"required\">必須</span>\n", $rs);
    }

    /**
     * test control
     */
    public function testControl()
    {
        $customLink = CustomLinkFactory::make([
            'name' => 'test custom link',
            'title' => null,
            'display_admin_list' => 1,
            'status' => 1,
            'custom_field' => [
                'type' => 'BcCcTextarea',
            ],
            'parent_id' => 1,
        ])->getEntity();
        $rs = $this->CustomContentAdminHelper->control($customLink);
        $this->assertTextContains('<textarea name="test custom link" class="bca-textarea__textarea" id="test-custom-link">',$rs);
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
    public function testPreview()
    {
        $customField = CustomFieldFactory::make([
            'name' => 'test custom field',
            'type' => 'BcCcDate',
            'status' => 1,
        ])->getEntity();

        $rs = $this->CustomContentAdminHelper->preview('preview.BcCcDate', 'BcCcDate', $customField);
        $this->assertTextContains('<span class="bca-textbox"><input type="text" name="preview[BcCcDate]" autocomplete="off"', $rs);
    }

    /*
     * test getEntryIndexTitle
     */
    public function test_getEntryIndexTitle()
    {
        //サービスクラス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTableService = $this->getService(CustomTablesServiceInterface::class);
        $customEntriesService = $this->getService(CustomEntriesServiceInterface::class);
        /**
         * case has_child is true
         * and customTable with display_field is title
         */
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTableService->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'display_field' => 'title',
            'has_child' => 1
        ]);
        $customTableService->create([
            'id' => 2,
            'name' => 'recruit_categories_2',
            'display_field' => 'text',
            'has_child' => 0
        ]);
        //データ生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        $customEntriesService->setup(1);
        $customTable = $customTableService->get(1);
        $customEntry = $customEntriesService->get(1);
        //case customEntry exists
        $rs = $this->CustomContentAdminHelper->getEntryIndexTitle($customTable, $customEntry);
        //check result return
        $this->assertEquals('<a href="/baser/admin/baser-core/dashboard/edit/1/1">Webエンジニア・Webプログラマー</a>', $rs);
        /**
         * case has_child is false
         * and customTable with display_field is not title
         */
        CustomEntryFactory::make([
            'id' => 4,
            'custom_table_id' => 2,
            'title' => 'プログラマー 4',
        ])->persist();
        $customTable = $customTableService->get(2);
        $customEntry = $customEntriesService->get(4);
        $rs = $this->CustomContentAdminHelper->getEntryIndexTitle($customTable, $customEntry);
        //check result return
        $this->assertEquals('プログラマー 4', $rs);
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
        $dataBaseService->dropTable('custom_entry_2_recruit_categories_2');
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
        //case before_head is empty
        $customLink = CustomLinkFactory::make([
            'before_head' => ''
        ])->getEntity();
        $result = $this->CustomContentAdminHelper->beforeHead($customLink);
        $this->assertEquals('', $result);
        //case before_head is not empty
        $customLink = CustomLinkFactory::make([
            'before_head' => 'test before head',
        ])->getEntity();
        $result = $this->CustomContentAdminHelper->beforeHead($customLink);
        $this->assertEquals('test before head&nbsp;', $result);
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

    /*
     * test getFields
     */
    public function test_getFields()
    {
        //case customFields is empty
        $rs = $this->CustomContentAdminHelper->getFields();
        //check result return
        $this->assertEquals(0, $rs->count());
        /**
         * case customFields is not empty
         * load fixture scenario
         */
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $rs = $this->CustomContentAdminHelper->getFields();
        //check result return
        $this->assertEquals(2, $rs->count());
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

    /**
     * test isEnabledMoveUpEntry
     */
    public function test_isEnabledMoveUpEntry()
    {
        $entries = new \ArrayObject([
            new CustomEntry(['id' => 1, 'level' => 1]),
            new CustomEntry(['id' => 2, 'level' => 1]),
            new CustomEntry(['id' => 3, 'level' => 1])
        ]);
        $currentEntry = new CustomEntry(['id' => 2, 'level' => 1]);
        $result = $this->CustomContentAdminHelper->isEnabledMoveUpEntry($entries, $currentEntry);
        $this->assertTrue($result);

        //with move not possible
        $currentEntry = new CustomEntry(['id' => 1, 'level' => 1]);
        $result = $this->CustomContentAdminHelper->isEnabledMoveUpEntry($entries, $currentEntry);
        $this->assertFalse($result);
    }

    /**
     * test isEnabledMoveDownEntry
     */

    public function test_isEnabledMoveDownEntry()
    {
        $entries = new \ArrayObject([
            new CustomEntry(['id' => 1, 'level' => 1]),
            new CustomEntry(['id' => 2, 'level' => 1]),
            new CustomEntry(['id' => 3, 'level' => 1])
        ]);
        $currentEntry = new CustomEntry(['id' => 2, 'level' => 1]);
        $result = $this->CustomContentAdminHelper->isEnabledMoveDownEntry($entries, $currentEntry);
        $this->assertTrue($result);

        //with move not possible
        $currentEntry = new CustomEntry(['id' => 3, 'level' => 1]);
        $result = $this->CustomContentAdminHelper->isEnabledMoveDownEntry($entries, $currentEntry);
        $this->assertFalse($result);
    }
}
