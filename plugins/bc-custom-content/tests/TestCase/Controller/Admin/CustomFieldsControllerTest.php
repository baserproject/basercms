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
use BcCustomContent\Controller\Admin\CustomFieldsController;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomFieldsControllerTest
 *  * @property CustomFieldsController $CustomFieldsController
 */
class CustomFieldsControllerTest extends BcTestCase
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
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/bc-custom-content/custom_fields/');
        $this->loginAdmin($request);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test index
     */
    public function testIndex()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);

        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/');
        //戻る値を確認
        $this->assertResponseCode(200);
        $entities = $this->_controller->viewBuilder()->getVars() ['entities']->toArray();
        $this->assertCount(2, $entities);
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomFields.beforeAdd', function (Event $event) {
            $data = $event->getData('data');
            $data['title'] = 'beforeAdd';
            $event->setData('data', $data);
        });
        //Postデータを生成
        $data = [
            'title' => '求人分類',
            'name' => 'recruit_category',
            'type' => 'BcCcRelated',
            'status' => 1,
            'default_value' => '新卒採用',
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/add', $data);
        //イベントに入るかどうか確認
        $customFields = $this->getTableLocator()->get('BcCustomContent.CustomFields');
        $query = $customFields->find()->where(['title' => 'beforeAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * test add
     */
    public function test_add()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //Postデータを生成
        $data = [
            'title' => '求人分類',
            'name' => 'recruit_category',
            'type' => 'BcCcRelated',
            'status' => 1,
            'default_value' => '新卒採用',
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/add', $data);
        $this->assertResponseCode(302);
        $this->assertFlashMessage('フィールド「求人分類」を追加しました');
        $this->assertRedirect(['action' => 'edit/1']);
        //DBにデータが保存できるか確認すること
        $customFields = $this->getTableLocator()->get('BcCustomContent.CustomFields');
        $query = $customFields->find()->where(['title' => '求人分類']);
        $this->assertEquals(1, $query->count());

        //タイトルを指定しない場合、
        $this->post('/baser/admin/bc-custom-content/custom_fields/add', ['name' => '']);
        $this->assertResponseCode(200);
        //エラーを確認
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals(
            ['name' => ['_empty' => "フィールド名を入力してください。"]],
            $vars['entity']->getErrors()
        );
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomFields.afterAdd', function (Event $event) {
            $data = $event->getData('data');
            $contentLinks = TableRegistry::getTableLocator()->get('BcCustomContent.CustomFields');
            $data->title = 'afterAdd';
            $contentLinks->save($data);
        });
        //Postデータを生成
        $data = [
            'title' => '求人分類',
            'name' => 'recruit_category_2',
            'type' => 'BcCcRelated',
            'status' => 1,
            'default_value' => '新卒採用',
        ];
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/add', $data);
        //イベントに入るかどうか確認
        $customFields = $this->getTableLocator()->get('BcCustomContent.CustomFields');
        $query = $customFields->find()->where(['title' => 'afterAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * test edit
     */
    public function testEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //Postデータを生成
        $data = CustomFieldFactory::get(1);
        $data['title'] = 'test edit title';
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/edit/1', $data->toArray());
        $this->assertResponseCode(302);
        $this->assertFlashMessage('フィールド「test edit title」を更新しました。');
        $this->assertRedirect(['action' => 'edit/1']);
        //DBにデータが保存できるか確認すること
        $customFields = $this->getTableLocator()->get('BcCustomContent.CustomFields');
        $query = $customFields->find()->where(['title' => 'test edit title']);
        $this->assertEquals(1, $query->count());

        //タイトルを指定しない場合、
        $this->post('/baser/admin/bc-custom-content/custom_fields/edit/1', ['title' => '']);
        $this->assertResponseCode(200);
        //エラーを確認
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals(
            ['title' => ['_empty' => "項目見出しを入力してください。"]],
            $vars['entity']->getErrors()
        );
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomFields.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['title'] = 'beforeEdit';
            $event->setData('data', $data);
        });
        //Postデータを生成
        $data = CustomFieldFactory::get(1);
        $data['title'] = 'test edit title';
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/edit/1', $data->toArray());
        //イベントに入るかどうか確認
        $customFields = $this->getTableLocator()->get('BcCustomContent.CustomFields');
        $query = $customFields->find()->where(['title' => 'beforeEdit']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterEditEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データーを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcCustomContent.CustomFields.afterEdit', function (Event $event) {
            $data = $event->getData('data');
            $contentLinks = TableRegistry::getTableLocator()->get('BcCustomContent.CustomFields');
            $data->title = 'afterEdit';
            $contentLinks->save($data);
        });
        //Postデータを生成
        $data = CustomFieldFactory::get(1);
        $data['title'] = 'test edit title';
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/edit/1', $data->toArray());
        //イベントに入るかどうか確認
        $customFields = $this->getTableLocator()->get('BcCustomContent.CustomFields');
        $query = $customFields->find()->where(['title' => 'afterEdit']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * test delete
     */
    public function testDelete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        $dataBaseService->addColumn('custom_entry_1_recruit', 'recruit_category', 'text');
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //対象URLをコル
        $this->post('/baser/admin/bc-custom-content/custom_fields/delete/1');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('フィールド「求人分類」を削除しました。');
        $this->assertRedirect(['action' => 'index']);
        //DBにデータが存在しないか確認すること
        $customFields = $this->getTableLocator()->get('BcCustomContent.CustomFields');
        $query = $customFields->find()->where(['title' => '求人分類']);
        $this->assertEquals(0, $query->count());

        //存在しないIDを指定した場合、
        $this->post('/baser/admin/bc-custom-content/custom_fields/delete/1111');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('データベース処理中にエラーが発生しました。Record not found in table "custom_fields"');
        $this->assertRedirect(['action' => 'index']);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit');
    }
}
