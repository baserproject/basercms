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

namespace BcCustomContent\Test\TestCase\Controller\Admin;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Controller\Admin\CustomEntriesController;
use BcCustomContent\Service\Admin\CustomEntriesAdminServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomEntriesControllerTest
 */
class CustomEntriesControllerTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * UsersController
     * @var CustomEntriesController
     */
    public $CustomEntriesController;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/bc-custom-content/custom_entries/');
        $request = $this->loginAdmin($request);
        $this->CustomEntriesController = new CustomEntriesController($request);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
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
        // イベントテスト
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomEntries.searchIndex', function (Event $event) {
            $request = $event->getData('request');
            return $request->withQueryParams(['num' => 1]);
        });
        $this->get('/baser/admin/bc-custom-content/custom_entries/1');
        $this->CustomEntriesController->beforeFilter(new Event('beforeFilter'));
        $this->CustomEntriesController->index($this->getService(CustomEntriesAdminServiceInterface::class), 1);
        $this->assertEquals(1, $this->CustomEntriesController->getRequest()->getQuery('num'));
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
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

        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomEntries.beforeAdd', function (Event $event) {
            $data = $event->getData('data');
            $data['title'] = 'beforeAdd';
            $event->setData('data', $data);
        });
        //追加データを準備
        $data = [
            'custom_table_id' => 1,
            'name' => 'プログラマー',
            'title' => 'プログラマー',
            'creator_id' => 1,
            'lft' => 1,
            'rght' => 2,
            'level' => 0,
            'status' => 1
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_entries/add/1', $data);
        //イベントに入るかどうか確認
        $customEntries = $this->getTableLocator()->get('BcCustomContent.CustomEntries');
        $query = $customEntries->find()->where(['title' => 'beforeAdd']);
        $this->assertEquals(1, $query->count());

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
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
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomEntries.afterAdd', function (Event $event) {
            $data = $event->getData('data');
            $contentLinks = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
            $data->title = 'afterAdd';
            $contentLinks->save($data);
        });
        //Postデータを生成
        $data = [
            'custom_table_id' => 1,
            'name' => 'プログラマー2',
            'title' => 'プログラマー2',
            'creator_id' => 1,
            'lft' => 3,
            'rght' => 4,
            'level' => 0,
            'status' => 1
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_entries/add/1', $data);
        //イベントに入るかどうか確認
        $customEntries = $this->getTableLocator()->get('BcCustomContent.CustomEntries');
        $query = $customEntries->find()->where(['title' => 'afterAdd']);
        $this->assertEquals(1, $query->count());
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
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
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomEntries.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['title'] = 'beforeEdit';
            $event->setData('data', $data);
        });
        //Postデータを生成
        $data['title'] = 'test edit title';
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_entries/edit/1/1', $data);
        //イベントに入るかどうか確認
        $customEntries = $this->getTableLocator()->get('BcCustomContent.CustomEntries');
        $query = $customEntries->find()->where(['title' => 'beforeEdit']);
        $this->assertEquals(1, $query->count());
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
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
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomEntries.afterEdit', function (Event $event) {
            $data = $event->getData('data');
            $contentLinks = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries');
            $data->title = 'afterEdit';
            $contentLinks->save($data);
        });
        //Postデータを生成
        $data['title'] = 'test edit title';
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_entries/edit/1/1', $data);
        //イベントに入るかどうか確認
        $customEntries = $this->getTableLocator()->get('BcCustomContent.CustomEntries');
        $query = $customEntries->find()->where(['title' => 'afterEdit']);
        $this->assertEquals(1, $query->count());
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

}
