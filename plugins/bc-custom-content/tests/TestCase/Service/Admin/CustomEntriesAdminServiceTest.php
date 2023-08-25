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

namespace BcCustomContent\Test\TestCase\Service;


use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\Admin\CustomEntriesAdminService;
use BcCustomContent\Service\Admin\CustomEntriesAdminServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomEntryFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomEntriesAdminServiceTest
 */
class CustomEntriesAdminServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomEntriesAdminService
     */
    public $CustomEntriesAdminService;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomEntriesAdminService = $this->getService(CustomEntriesAdminServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomEntriesAdminService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);

        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        //対象メソッドをコール
        $rs = $this->CustomEntriesAdminService->getViewVarsForEdit(1, $this->CustomEntriesAdminService->get(1));

        //戻る値を確認
        $this->assertEquals(1, $rs['entity']->id);
        $this->assertArrayHasKey(5, $rs['tableId']);
        $this->assertArrayHasKey('customTable', $rs);
        $this->assertArrayHasKey('publishLink', $rs);
        $this->assertArrayHasKey('availablePreview', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getTableWithLinksByAll
     */
    public function test_getTableWithLinksByAll()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);

        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        $rs = $this->CustomEntriesAdminService->getTableWithLinksByAll(1);

        //戻る値を確認
        $this->assertEquals('求人情報', $rs->title);
        $this->assertCount(2, $rs->custom_links);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getViewVarsForAdd
     */
    public function test_getViewVarsForAdd()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);

        //GetNewを使うのでログインIDが必要にあります
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin'));

        //対象メソッドをコール
        $rs = $this->CustomEntriesAdminService->getViewVarsForAdd(1, $this->CustomEntriesAdminService->getNew(1));

        //戻る値を確認
        $this->assertEquals(1, $rs['entity']->custom_table_id);
        $this->assertEquals(1, $rs['tableId']);
        $this->assertArrayHasKey('customTable', $rs);
        $this->assertArrayHasKey('availablePreview', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getViewVarsForIndex
     */
    public function test_getViewVarsForIndex()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);

        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        //対象メソッドをコール
        $rs = $this->CustomEntriesAdminService->getViewVarsForIndex($customTable->get(1), $this->CustomEntriesAdminService->get(1));

        //戻る値を確認
        $this->assertEquals(1, $rs['tableId']);
        $this->assertArrayHasKey('customTable', $rs);
        $this->assertArrayHasKey('entities', $rs);
        $this->assertArrayHasKey('publishLink', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getPublishLinkForIndex
     */
    public function test_getPublishLinkForIndex()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);

        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);

        //対象メソッドをコール
        $rs = $this->CustomEntriesAdminService->getPublishLinkForIndex($customTable->getWithContentAndLinks(1));

        //戻る値を確認
        $this->assertEquals('/', $rs);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getPublishLinkForEdit
     */
    public function test_getPublishLinkForEdit()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'publish_begin' => '2021-10-01 00:00:00',
            'publish_end' => '9999-11-30 23:59:59',
            'has_child' => 0
        ]);

        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        //対象メソッドをコール
        $rs = $this->CustomEntriesAdminService->getPublishLinkForEdit(ContentFactory::get(1), CustomEntryFactory::get(1));

        //戻る値を確認
        $this->assertEquals('https://localhost/view/プログラマー', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }
}
