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
use BcCustomContent\Controller\Admin\CustomContentsController;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomContentsControllerTest
 */
class CustomContentsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var CustomContentsController
     */
    public $CustomContentsController;

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
        $this->request = $this->loginAdmin($this->getRequest('/baser/admin/bc-custom-content/custom_contents/'));
        $this->CustomContentsController = new CustomContentsController($this->request);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->CustomContentsController, $this->request);
    }

    /**
     * test initialize
     */
    public function test_initialize()
    {
        $this->assertNotEmpty($this->CustomContentsController->BcAdminContents);
    }

    /**
     * test edit
     */
    public function test_edit()
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
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $data = [
            'custom_table_id' => 1,
            'list_count' => 1,
            'content' => [
                'title' => 'custom content change'
            ]
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_contents/edit/1', $data);
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertFlashMessage('カスタムコンテンツ「custom content change」を更新しました。');
        $this->assertRedirect(['action' => 'edit/1']);

        //エラーを発生するとき
        $data = [
            'custom_table_id' => 1,
            'list_count' => 1,
            'content' => [
                'url' => '/test',
                'title' => null
            ]
        ];
        $this->post('/baser/admin/bc-custom-content/custom_contents/edit/1', $data);
        //ステータスを確認
        $this->assertResponseCode(200);
        //エラーを確認
        $vars = $this->_controller->viewBuilder()->getVars();
        $error = $vars['entity']->getErrors();
        $this->assertEquals(
            ['content' => ['title' => ['_empty' => "タイトルを入力してください。"]]],
            $vars['entity']->getErrors()
        );
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test beforeEdit
     */
    public function test_beforeEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        $this->loadFixtureScenario(CustomContentsScenario::class);

        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomContents.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['description'] = 'beforeEdit';
            $event->setData('data', $data);
        });
        $data = [
            'custom_table_id' => 1,
            'list_count' => 1,
            'description' => 'description edit',
            'content' => [
                'title' => 'custom content change'
            ]
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_contents/edit/1', $data);
        //イベントに入るかどうか確認
        $customContents = $this->getTableLocator()->get('BcCustomContent.CustomContents');
        $query = $customContents->find()->where(['description' => 'beforeEdit']);
        $this->assertEquals(1, $query->count());

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test afterEdit
     */
    public function test_afterEdit()
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
        $this->loadFixtureScenario(CustomContentsScenario::class);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomContents.afterEdit', function (Event $event) {
            $data = $event->getData('data');
            $customContents = TableRegistry::getTableLocator()->get('BcCustomContent.CustomContents');
            $data->description = 'afterEdit';
            $customContents->save($data);
        });
        //Postデータを生成
        $data = [
            'custom_table_id' => 1,
            'list_count' => 1,
            'description' => 'description edit',
            'content' => [
                'title' => 'custom content change'
            ]
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_contents/edit/1', $data);
        //イベントに入るかどうか確認
        $customContents = $this->getTableLocator()->get('BcCustomContent.CustomContents');
        $query = $customContents->find()->where(['description' => 'afterEdit']);
        $this->assertEquals(1, $query->count());
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test index
     */
    public function test_index()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);

        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_contents/index/1');
        //戻る値を確認
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'controller' => 'CustomEntries',
            'action' => 'index',
            1
        ]);

        //存在しないIDを指定した場合、
        $this->post('/baser/admin/bc-custom-content/custom_contents/index/11111');
        //戻る値を確認
        $this->assertResponseCode(404);
    }

}
