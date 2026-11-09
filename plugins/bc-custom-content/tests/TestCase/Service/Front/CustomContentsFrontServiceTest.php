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

namespace BcCustomContent\Test\TestCase\Service\Front;


use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Factory\PluginFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Controller\CustomContentController;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Service\Front\CustomContentFrontService;
use BcCustomContent\Service\Front\CustomContentFrontServiceInterface;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Factory\CustomLinkFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use BcCustomContent\Test\Scenario\CustomTablesScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomContentsFrontServiceTest
 */
class CustomContentsFrontServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomContentFrontService
     */
    public $CustomContentFrontService;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->CustomContentFrontService = $this->getService(CustomContentFrontServiceInterface::class);
        PluginFactory::make([
            'name' => 'BcCustomContent',
            'title' => 'カスタムコンテンツ',
            'version' => '1.0.0',
            'status' => '1',
            'db_init' => '1',
            'priority' => '1',
            'created' => '2021-05-03 10:57:07',
            'modified' => '2021-05-03 10:57:07'
        ]);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomContentFrontService);
        parent::tearDown();
        $this->truncateTable('custom_tables');
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->CustomContentFrontService->EntriesService));
        $this->assertTrue(isset($this->CustomContentFrontService->ContentsService));
    }

    /**
     * test getCustomContent
     */
    public function test_getCustomContent()
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
        $this->loadFixtureScenario(CustomContentsScenario::class);

        //対象メソッドをコール
        $rs = $this->CustomContentFrontService->getCustomContent(1);
        $this->assertEquals('サービステスト', $rs->description);
        $this->assertEquals('サービスタイトル', $rs->content->title);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getCustomEntries
     */
    public function test_getCustomEntries()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customContent = $this->getService(CustomContentsServiceInterface::class);

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
        $this->loadFixtureScenario(CustomTablesScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        //対象メソッドをコール
        $rs = $this->CustomContentFrontService->getCustomEntries($customContent->get(1));
        //戻る値を確認
        $this->assertEquals(3, $rs->count());

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
        $customContent = $this->getService(CustomContentsServiceInterface::class);
        $customEntry = $this->getService(CustomEntriesServiceInterface::class);
        $controller = new CustomContentController($this->getRequest());
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
        $this->loadFixtureScenario(CustomTablesScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin/'));
        //対象メソッドをコール
        $customEntry->setup(1);
        $rs = $this->CustomContentFrontService->getViewVarsForIndex($customContent->get(1), $controller->paginate($customEntry->getIndex([])));

        //戻る値を確認
        $this->assertArrayHasKey('customContent', $rs);
        $this->assertArrayHasKey('customEntries', $rs);
        $this->assertArrayHasKey('currentWidgetAreaId', $rs);
        $this->assertArrayHasKey('editLink', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getViewVarsForView
     */
    public function test_getViewVarsForView()
    {
        //サービスクラス
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customContent = $this->getService(CustomContentsServiceInterface::class);

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
        $this->loginAdmin($this->getRequest('/baser/admin/'));
        //対象メソッドをコール
        $rs = $this->CustomContentFrontService->getViewVarsForView($customContent->get(1), 1, true);

        //戻る値を確認
        $this->assertArrayHasKey('customContent', $rs);
        $this->assertArrayHasKey('customEntry', $rs);
        $this->assertArrayHasKey('currentWidgetAreaId', $rs);
        $this->assertArrayHasKey('editLink', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getIndexTemplate
     */
    public function test_getIndexTemplate()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customContent = $this->getService(CustomContentsServiceInterface::class);

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
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //対象メソッドをコール
        $rs = $this->CustomContentFrontService->getIndexTemplate($customContent->get(1));

        //戻る値を確認
        $this->assertEquals('CustomContent' . DS . 'default' . DS . 'index', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getViewTemplate
     */
    public function test_getViewTemplate()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customContent = $this->getService(CustomContentsServiceInterface::class);

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
        $this->loadFixtureScenario(CustomTablesScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //対象メソッドをコール
        $rs = $this->CustomContentFrontService->getViewTemplate($customContent->get(1));

        //戻る値を確認
        $this->assertEquals('CustomContent' . DS . 'default' . DS . 'view', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test setupPreviewForView
     */
    public function test_setupPreviewForView()
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

        CustomLinkFactory::make(['name' => 'file'])->persist();
        CustomFieldFactory::make(['id' => 1, 'type' => 'BcCcFile'])->persist();

        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        //対象メソッドをコール
        $request = $this->getRequest('/baser/admin')
            ->withParam('pass.0', 1)
            ->withParam('entityId', 1);
        $controller = new CustomContentController($request);
        $this->CustomContentFrontService->setupPreviewForView($controller);

        //戻る値を確認
        $this->assertEquals('CustomContent/default/view', $controller->viewBuilder()->getTemplate());
        $this->assertArrayHasKey('customEntry', $controller->viewBuilder()->getVars());
        $this->assertArrayHasKey('customContent', $controller->viewBuilder()->getVars());

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test setupPreviewForIndex
     */
    public function test_setupPreviewForIndex()
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
        $this->loadFixtureScenario(CustomTablesScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        $this->loadFixtureScenario(CustomContentsScenario::class);

        //対象メソッドをコール
        $request = $this->getRequest('/baser/admin')
            ->withParam('entityId', 1);
        $controller = new CustomContentController($request);
        $this->CustomContentFrontService->setupPreviewForIndex($controller);

        //戻る値を確認
        $this->assertEquals('CustomContent/default/index', $controller->viewBuilder()->getTemplate());
        $this->assertArrayHasKey('customContent', $controller->viewBuilder()->getVars());
        $this->assertArrayHasKey('customEntries', $controller->viewBuilder()->getVars());
        $this->assertArrayHasKey('customTable', $controller->viewBuilder()->getVars());
        $this->assertArrayHasKey('currentWidgetAreaId', $controller->viewBuilder()->getVars());

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getViewVarsForArchives
     */
    public function testGetViewVarsForArchives()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $this->loadFixtureScenario(InitAppScenario::class);

        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customContent = $this->getService(CustomContentsServiceInterface::class);
        $customEntry = $this->getService(CustomEntriesServiceInterface::class);
        $controller = new CustomContentController($this->getRequest());

        $customLink = CustomLinkFactory::make([
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'name' => 'category',
        ])->persist();
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
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $customEntry->addFields(1, [$customLink]);

        //対象メソッドをコール
        $customEntry->setup(1);
        $rs = $this->CustomContentFrontService->getViewVarsForArchives(
            $customContent->get(1),
            $controller->paginate($customEntry->getIndex([])),
            'test'
        );

        //戻る値を確認
        $this->assertArrayHasKey('customContent', $rs);
        $this->assertArrayHasKey('customEntries', $rs);
        $this->assertArrayHasKey('customTable', $rs);
        $this->assertArrayHasKey('currentWidgetAreaId', $rs);
        $this->assertArrayHasKey('archivesName', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getViewVarsForYear
     */
    public function testGetViewVarsForYear()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $this->loadFixtureScenario(InitAppScenario::class);

        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customContent = $this->getService(CustomContentsServiceInterface::class);
        $customEntry = $this->getService(CustomEntriesServiceInterface::class);
        $controller = new CustomContentController($this->getRequest());

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
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //対象メソッドをコール
        $customEntry->setup(1);
        $rs = $this->CustomContentFrontService->getViewVarsForYear(
            $customContent->get(1),
            $controller->paginate($customEntry->getIndex([])),
            '2021'
        );

        //戻る値を確認
        $this->assertArrayHasKey('customContent', $rs);
        $this->assertArrayHasKey('customEntries', $rs);
        $this->assertArrayHasKey('customTable', $rs);
        $this->assertArrayHasKey('currentWidgetAreaId', $rs);
        $this->assertArrayHasKey('archivesName', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getArchivesTemplate
     */
    public function testGetArchivesTemplate()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customContent = $this->getService(CustomContentsServiceInterface::class);

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
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //対象メソッドをコール
        $rs = $this->CustomContentFrontService->getArchivesTemplate($customContent->get(1));

        //戻る値を確認
        $this->assertEquals('CustomContent' . DS . 'default' . DS . 'archives', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test getYearTemplate
     */
    public function testGetYearTemplate()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customContent = $this->getService(CustomContentsServiceInterface::class);

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
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //対象メソッドをコール
        $rs = $this->CustomContentFrontService->getYearTemplate($customContent->get(1));

        //戻る値を確認
        $this->assertEquals('CustomContent' . DS . 'default' . DS . 'year', $rs);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }
}
