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
use BcCustomContent\Controller\Admin\CustomTablesController;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Scenario\CustomTablesScenario;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomTablesControllerTest
 */
class CustomTablesControllerTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomTablesController
     */
    public $CustomTablesController;
    /**
     * Test subject
     *
     * @var ServerRequest
     */
    public $request;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->request = $this->loginAdmin($this->getRequest('/baser/admin/bc-custom-content/custom_tables/'));
        $this->CustomTablesController = new CustomTablesController($this->request);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
        unset($this->CustomTablesController, $this->request);
    }

    /**
     * test beforeFilter
     */
    public function test_beforeFilter()
    {
        //action ！== delete 場合、validatePostはTrueを返す
        $event = new Event('Controller.beforeFilter', $this->CustomTablesController);
        $this->CustomTablesController->beforeFilter($event);
        $config = $this->CustomTablesController->Security->getConfig('validatePost');
        $this->assertTrue($config);

        //action == delete 場合、validatePostをFalseに設定する
        $this->CustomTablesController->setRequest($this->request->withParam('action', 'delete'));
        $event = new Event('Controller.beforeFilter', $this->CustomTablesController);
        $this->CustomTablesController->beforeFilter($event);
        $config = $this->CustomTablesController->Security->getConfig('validatePost');
        $this->assertFalse($config);
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomTables.beforeAdd', function (Event $event) {
            $data = $event->getData('data');
            $data['title'] = 'beforeAdd';
            $event->setData('data', $data);
        });
        //Postデータを生成
        $data = [
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_tables/add', $data);
        //イベントに入るかどうか確認
        $customTables = $this->getTableLocator()->get('BcCustomContent.CustomTables');
        $query = $customTables->find()->where(['title' => 'beforeAdd']);
        $this->assertEquals(1, $query->count());
        //不要なテーブルを削除
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomTables.afterAdd', function (Event $event) {
            $data = $event->getData('data');
            $contentLinks = TableRegistry::getTableLocator()->get('BcCustomContent.CustomTables');
            $data->title = 'afterAdd';
            $contentLinks->save($data);
        });
        //Postデータを生成
        $data = [
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_tables/add', $data);
        //イベントに入るかどうか確認
        $customTables = $this->getTableLocator()->get('BcCustomContent.CustomTables');
        $query = $customTables->find()->where(['title' => 'afterAdd']);
        $this->assertEquals(1, $query->count());
        //不要なテーブルを削除
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $dataBaseService->dropTable('custom_entry_1_contact');
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //テストデータを生成
        $data = [
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ];
        $customTable->create($data);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomTables.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['title'] = 'beforeEdit';
            $event->setData('data', $data);
        });
        //Postデータを生成
        $data = CustomFieldFactory::get(1);
        $data['title'] = 'test edit title';
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_tables/edit/1', $data);
        //イベントに入るかどうか確認
        $customTables = $this->getTableLocator()->get('BcCustomContent.CustomTables');
        $query = $customTables->find()->where(['title' => 'beforeEdit']);
        $this->assertEquals(1, $query->count());
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact_edit');
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //テストデータを生成
        $data = [
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ];
        $customTable->create($data);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomTables.afterEdit', function (Event $event) {
            $data = $event->getData('data');
            $contentLinks = TableRegistry::getTableLocator()->get('BcCustomContent.CustomTables');
            $data->title = 'afterEdit';
            $contentLinks->save($data);
        });
        //Postデータを生成
        $data = CustomFieldFactory::get(1);
        $data['title'] = 'test edit title';
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_tables/edit/1', $data);
        //イベントに入るかどうか確認
        $customTables = $this->getTableLocator()->get('BcCustomContent.CustomTables');
        $query = $customTables->find()->where(['title' => 'afterEdit']);
        $this->assertEquals(1, $query->count());
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact_edit');
    }
}
