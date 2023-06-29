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
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\CustomLinksService;
use BcCustomContent\Service\CustomLinksServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomLinksServiceTest
 */
class CustomLinksServiceTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomLinksService
     */
    public $CustomLinksService;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BcCustomContent.Factory/CustomFields',
        'plugin.BcCustomContent.Factory/CustomLinks',
        'plugin.BcCustomContent.Factory/CustomTables',
        'plugin.BcCustomContent.Factory/CustomContents',
        'plugin.BaserCore.Factory/Contents',
    ];

    /**
     * Set up
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->CustomLinksService = $this->getService(CustomLinksServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->CustomLinksService);
        parent::tearDown();
    }

    /**
     * Test __construct
     */
    public function test__construct()
    {
        // テーブルがセットされている事を確認
        $this->assertEquals('CustomLinks', $this->CustomLinksService->CustomLinks->getAlias());
    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test get
     */
    public function test_get()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);
        //サービスメソッドを呼ぶ
        $result = $this->CustomLinksService->get(1);
        //戻る値を確認
        $this->assertEquals('求人分類', $result->title);
        $this->assertArrayHasKey('custom_field', $result);
        $this->assertArrayHasKey('custom_table', $result);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');

        //存在しないIDを指定した場合、
        $this->expectException(RecordNotFoundException::class);
        $this->expectExceptionMessage('Record not found in table "custom_links"');
        $this->CustomLinksService->get(11);
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test create
     */
    public function test_create()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);

        //postDataを用意
        $data = [
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'lft' => 1,
            'rght' => 2,
            'name' => 'contact_column',
            'title' => 'お問い合わせ',
            'type' => 'text'
        ];
        //サービスメソッドを呼ぶ
        $result = $this->CustomLinksService->create($data);
        //戻る値を確認
        $this->assertEquals('contact_column', $result->name);
        $this->assertEquals('お問い合わせ', $result->title);
        //custom_entryテーブルにフィルドが生成されたか確認
        $this->assertTrue($dataBaseService->columnExists('custom_entry_1_contact', 'contact_column'));
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * test create
     */
    public function test_update()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $result = $this->CustomLinksService->getList(1);
        $this->assertEquals('求人分類', $result[1]);
        $this->assertEquals('この仕事の特徴', $result[2]);
    }

}
