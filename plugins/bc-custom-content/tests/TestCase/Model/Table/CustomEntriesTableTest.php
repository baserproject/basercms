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
use BcCustomContent\Service\CustomEntriesService;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use BcCustomContent\Test\Scenario\CustomTablesScenario;
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
        $this->CustomEntriesService = $this->getService(CustomEntriesServiceInterface::class);
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
        $this->assertTrue($this->CustomEntriesTable->hasBehavior('BcContents'));
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

        //正常系実行

        //異常系実行


    }

    /**
     * test setUp
     */
    public function test_setUp()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setUseTable
     */
    public function test_setUseTable()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test getTableName
     */
    public function test_getTableName()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setLinks
     */
    public function test_setLinks()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setupValidate
     */
    public function test_setupValidate()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateMaxFileSize
     */
    public function test_setValidateMaxFileSize()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateFileExt
     */
    public function test_setValidateFileExt()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateEmailConfirm
     */
    public function test_setValidateEmailConfirm()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateRegex
     */
    public function test_setValidateRegex()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateEmail
     */
    public function test_setValidateEmail()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateNumber
     */
    public function test_setValidateNumber()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateHankaku
     */
    public function test_setValidateHankaku()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateZenkakuKatakana
     */
    public function test_setValidateZenkakuKatakana()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateZenkakuHiragana
     */
    public function test_setValidateZenkakuHiragana()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test setValidateDatetime
     */
    public function test_setValidateDatetime()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test validationDefault
     */
    public function test_validationDefault()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test beforeMarshal
     */
    public function test_beforeMarshal()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test autoConvert
     */
    public function test_autoConvert()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test findAll
     */
    public function test_findAll()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test decodeRow
     */
    public function test_decodeRow()
    {
        //準備

        //正常系実行

        //異常系実行


    }

    /**
     * test isJson
     */
    public function test_isJson()
    {
        //準備

        //正常系実行

        //異常系実行


    }


}
