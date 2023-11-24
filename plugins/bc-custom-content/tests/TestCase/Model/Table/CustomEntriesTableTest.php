<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcCustomContent\Test\TestCase\Model\Table;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Model\Entity\CustomEntry;
use BcCustomContent\Model\Table\CustomEntriesTable;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Service\CustomEntriesService;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomEntriesTableTest
 * @property CustomEntriesTable $CustomEntriesTable
 * @property CustomEntriesService $CustomEntriesService
 */
class CustomEntriesTableTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomEntriesTable = new CustomEntriesTable();
        $this->loadFixtureScenario(InitAppScenario::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertTrue($this->CustomEntriesTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->CustomEntriesTable->hasBehavior('BcSearchIndexManager'));
    }

    /**
     * test createSearchIndex
     */
    public function test_createSearchIndex()
    {
        //準備
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customTable->create([
            'id' => 1,
            'name' => 'recruit',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $entry = new CustomEntry(
            [
                'id' => 1,
                'custom_table_id' => 1,
                'published' => '2023-02-14 13:57:29',
                'modified' => '2023-02-14 13:57:29',
                'created' => '2023-01-30 07:09:22',
                'name' => 'プログラマー',
                'recruit_category' => '1',
            ]
        );
        //正常系実行
        $result = $this->CustomEntriesTable->createSearchIndex($entry);
        $this->assertEquals('カスタムコンテンツ', $result['type']);
        $this->assertEquals(1, $result['model_id']);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit');

    }

    /**
     * test createSearchDetail
     */
    public function test_createSearchDetail()
    {
        //準備
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customTable->create([
            'id' => 1,
            'name' => 'recruit',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
        ]);
        $entry = new CustomEntry(
            [
                'id' => 1,
                'custom_table_id' => 1,
                'published' => '2023-02-14 13:57:29',
                'modified' => '2023-02-14 13:57:29',
                'created' => '2023-01-30 07:09:22',
                'name' => 'プログラマー',
                'recruit_category' => '1',
            ]
        );
        //正常系実行: links = null
        $result = $this->CustomEntriesTable->createSearchDetail($entry);
        $this->assertEquals('プログラマー', $result);
        //正常系実行: links != null
        CustomFieldFactory::make([
            'id' => 1,
            'title' => '求人分類',
            'name' => 'recruit_category',
            'type' => 'text',
            'status' => 1,
            'validate' => '',
            'regex' => '',
            'regex_error_message' => '',
            'counter' => 0,
            'auto_convert' => '',
            'placeholder' => '',
            'size' => NULL,
            'max_length' => NULL,
            'source' => '',
            'created' => '2023-01-30 06:22:47',
            'modified' => '2023-02-20 11:18:32',
            'line' => NULL,
        ])->persist();
        CustomLinkFactory::make([
            'id' => 1,
            'no' => NULL,
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'parent_id' => NULL,
            'lft' => 1,
            'rght' => 2,
            'level' => 0,
            'name' => 'recruit_category',
            'title' => '求人分類',
            'group_valid' => 0,
            'created' => '2023-01-30 06:45:08',
            'modified' => '2023-02-12 23:31:04',
            'use_loop' => 0,
            'display_admin_list' => 1,
            'use_api' => 1,
            'search_target_front' => 1,
            'before_linefeed' => 0,
            'after_linefeed' => 0,
            'display_front' => 1,
            'search_target_admin' => 1,
            'description' => NULL,
            'attention' => NULL,
            'before_head' => NULL,
            'after_head' => NULL,
            'options' => NULL,
            'class' => NULL,
            'status' => 1,
            'required' => NULL,
        ])->persist();
        CustomFieldFactory::make([
            'id' => 2,
            'title' => 'この仕事の特徴',
            'name' => 'feature',
            'type' => 'text',
            'status' => 1,
            'default_value' => '',
            'validate' => '',
            'regex' => '',
            'regex_error_message' => '',
            'counter' => 0,
            'auto_convert' => '',
            'placeholder' => '',
            'size' => NULL,
            'max_length' => NULL,
            'created' => '2023-01-30 06:23:41',
            'modified' => '2023-02-20 11:21:03',
            'line' => NULL,
        ])->persist();
        CustomLinkFactory::make([
            'id' => 2,
            'no' => NULL,
            'custom_table_id' => 1,
            'custom_field_id' => 2,
            'parent_id' => NULL,
            'lft' => 1,
            'rght' => 2,
            'level' => 0,
            'name' => 'feature',
            'title' => 'この仕事の特徴',
            'group_valid' => 0,
            'created' => '2023-01-30 06:45:08',
            'modified' => '2023-02-12 23:31:04',
            'use_loop' => 0,
            'display_admin_list' => 0,
            'use_api' => 1,
            'search_target_front' => 1,
            'before_linefeed' => 0,
            'after_linefeed' => 0,
            'display_front' => 1,
            'search_target_admin' => 1,
            'description' => NULL,
            'attention' => NULL,
            'before_head' => NULL,
            'after_head' => NULL,
            'options' => NULL,
            'class' => NULL,
            'status' => 1,
            'required' => 1,
        ])->persist();
        $this->CustomEntriesTable->setLinks(1);
        $entry = new CustomEntry(
            [
                'id' => 1,
                'custom_table_id' => 1,
                'published' => '2023-02-14 13:57:29',
                'modified' => '2023-02-14 13:57:29',
                'created' => '2023-01-30 07:09:22',
                'name' => 'プログラマー',
                'recruit_category' => 'recruit_category',
                'feature' => 'feature',
            ]
        );
        $result = $this->CustomEntriesTable->createSearchDetail($entry);
        $this->assertEquals('プログラマー', $result);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit');

    }

    /**
     * test setUp
     */
    public function test_setUp()
    {
        //準備
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        CustomFieldFactory::make([
            'name' => 'test',
            'type' => 'text',
        ])->persist();
        CustomLinkFactory::make([
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'name' => 'recruit_category',
            'title' => '求人分類',
            'display_admin_list' => 1,
            'status' => 1,
        ])->persist();
        $customTable->create([
            'id' => 1,
            'name' => 'recruit',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
        ]);
        //正常系実行
        $result = $this->CustomEntriesTable->setUp(1, []);
        $this->assertTrue($result);
        $this->assertEquals(1, $this->CustomEntriesTable->tableId);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit');

    }

    /**
     * test setUseTable
     */
    public function test_setUseTable()
    {
        //準備
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customTable->create([
            'id' => 1,
            'name' => 'recruit',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
        ]);
        $this->CustomEntriesTable->setUseTable(1);
        $result = $this->CustomEntriesTable->getTable();
        $this->assertEquals('custom_entry_1_recruit', $result);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit');
    }

    /**
     * test getTableName
     */
    public function test_getTableName()
    {
        //準備
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customTable->create([
            'id' => 1,
            'name' => 'recruit',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
        ]);
        //正常系実行: name パラメータなし
        $result = $this->CustomEntriesTable->getTableName(1);
        $this->assertEquals('custom_entry_1_recruit', $result);
        //正常系実行: name パラメータあり
        $result = $this->CustomEntriesTable->getTableName(1, 'Nghiem');
        $this->assertEquals('custom_entry_1_Nghiem', $result);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit');

    }

    /**
     * test setLinks
     */
    public function test_setLinks()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setupValidate
     */
    public function test_setupValidate()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateMaxFileSize
     */
    public function test_setValidateMaxFileSize()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateFileExt
     */
    public function test_setValidateFileExt()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateEmailConfirm
     */
    public function test_setValidateEmailConfirm()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateRegex
     */
    public function test_setValidateRegex()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateEmail
     */
    public function test_setValidateEmail()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateNumber
     */
    public function test_setValidateNumber()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateHankaku
     */
    public function test_setValidateHankaku()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateZenkakuKatakana
     */
    public function test_setValidateZenkakuKatakana()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateZenkakuHiragana
     */
    public function test_setValidateZenkakuHiragana()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateDatetime
     */
    public function test_setValidateDatetime()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test beforeMarshal
     */
    public function test_beforeMarshal()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test autoConvert
     */
    public function test_autoConvert()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test findAll
     */
    public function test_findAll()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test decodeRow
     */
    public function test_decodeRow()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test isJson
     */
    public function test_isJson()
    {
        $this->markTestIncomplete('このテストは未実装です。');
        //準備

        //正常系実行

        //異常系実行


    }


}
