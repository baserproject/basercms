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

namespace BcUploader\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcUploader\Test\Scenario\UploaderCategoriesScenario;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class UploaderCategoriesControllerTest
 */
class UploaderCategoriesControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        ConnectionManager::alias('test', 'default');
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/baser-core/uploader_categories/');
        $this->loginAdmin($request);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $this->truncateTable('uploader_categories');
        $this->truncateTable('uploader_files');
    }

    /**
     * test index
     */
    public function test_index()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/admin/bc-uploader/uploader_categories/index');
        $this->assertResponseOk();
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcUploader.UploaderCategories.beforeAdd', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'beforeAdd';
            $event->setData('data', $data);
        });
        $data = [
            'name' => 'japan'
        ];
        $this->post('/baser/admin/bc-uploader/uploader_categories/add', $data);
        $uploaderCategories = $this->getTableLocator()->get('BcUploader.UploaderCategories');
        $query = $uploaderCategories->find()->where(['name' => 'beforeAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterAddEvent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcUploader.UploaderCategories.afterAdd', function (Event $event) {
            $UploaderCategory = $event->getData('data');
            $UploaderCategories = TableRegistry::getTableLocator()->get('UploaderCategories');
            $UploaderCategory->name = 'afterAdd';
            $UploaderCategories->save($UploaderCategory);
        });
        $data = [
            'name' => 'japan2'
        ];
        $this->post('/baser/admin/bc-uploader/uploader_categories/add', $data);
        $uploaderCategories = $this->getTableLocator()->get('BcUploader.UploaderCategories');
        $query = $uploaderCategories->find()->where(['name' => 'afterAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testBeforeEditEvent()
    {
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcUploader.UploaderCategories.beforeEdit', function (Event $event) {
            $data = $event->getData('data');
            $data['name'] = 'beforeAdd';
            $event->setData('data', $data);
        });
        $data = [
            'name' => 'japan'
        ];
        $this->post('/baser/admin/bc-uploader/uploader_categories/edit/1', $data);
        $uploaderCategories = $this->getTableLocator()->get('BcUploader.UploaderCategories');
        $query = $uploaderCategories->find()->where(['name' => 'beforeAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test beforeAddEvent
     */
    public function testAfterEditEvent()
    {
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->entryEventToMock(self::EVENT_LAYER_CONTROLLER, 'BcUploader.UploaderCategories.afterEdit', function (Event $event) {
            $UploaderCategory = $event->getData('data');
            $UploaderCategories = TableRegistry::getTableLocator()->get('UploaderCategories');
            $UploaderCategory->name = 'afterAdd';
            $UploaderCategories->save($UploaderCategory);
        });
        $data = [
            'name' => 'japan2'
        ];
        $this->post('/baser/admin/bc-uploader/uploader_categories/edit/1', $data);
        $uploaderCategories = $this->getTableLocator()->get('BcUploader.UploaderCategories');
        $query = $uploaderCategories->find()->where(['name' => 'afterAdd']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * test add
     */
    public function test_add()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //正常系実行
        $this->post('/baser/admin/bc-uploader/uploader_categories/add', ['name' => 'japan']);
        $this->assertResponseCode(302);
        $this->assertFlashMessage('アップロードカテゴリ「japan」を追加しました。');
        $this->assertRedirect('/baser/admin/bc-uploader/uploader_categories/index');

        //異常系実行
        $this->post('/baser/admin/bc-uploader/uploader_categories/add', ['name' => '']);
        $this->assertResponseCode(200);
        $errors = $this->_controller->viewBuilder()->getVars()['uploaderCategory']->getErrors();
        $this->assertEquals(['_empty' => 'カテゴリ名を入力してください。'], $errors['name']);
    }

    /**
     * test edit
     *
     */
    public function test_edit()
    {
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);

        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //正常系実行
        $this->post('/baser/admin/bc-uploader/uploader_categories/edit/1', ['name' => 'japan']);
        $this->assertResponseCode(302);
        $this->assertFlashMessage('アップロードカテゴリ「japan」を更新しました。');
        $this->assertRedirect('/baser/admin/bc-uploader/uploader_categories/index');

        //異常系実行
        $this->post('/baser/admin/bc-uploader/uploader_categories/edit/1', ['name' => null]);
        $this->assertResponseCode(200);
        $errors = $this->_controller->viewBuilder()->getVars()['uploaderCategory']->getErrors();
        $this->assertEquals(['_empty' => 'カテゴリ名を入力してください。'], $errors['name']);
    }


    /**
     * test delete
     */
    public function test_delete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);

        //正常系実行
        $this->post('/baser/admin/bc-uploader/uploader_categories/delete/1');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('アップロードカテゴリ「blog」を削除しました。');

        //異常系実行
        $this->post('/baser/admin/bc-uploader/uploader_categories/delete/10');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('データベース処理中にエラーが発生しました。Record not found in table `uploader_categories`.');
        $this->assertRedirect('/baser/admin/bc-uploader/uploader_categories/index');
    }

    /**
     * Test coppy
     */
    public function test_copy(){
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->loadFixtureScenario(UploaderCategoriesScenario::class);

        //正常系実行
        $this->post('/baser/admin/bc-uploader/uploader_categories/copy/1');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('アップロードカテゴリ「blog」をコピーしました。');

        //異常系実行
        $this->post('/baser/admin/bc-uploader/uploader_categories/copy/10');
        $this->assertResponseCode(302);
        $this->assertFlashMessage('データベース処理中にエラーが発生しました。__clone method called on non-object');
    }
}
