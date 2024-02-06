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
     * @return void
     */
    public function testAdd()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/admin/bc-uploader/uploader_categories/add');
        $this->assertResponseOk();

        $this->post('/baser/admin/bc-uploader/uploader_categories/add', [
            'name' => 'uploader add test',
        ]);

        //Redirect url
        $this->assertResponseCode(302);
        $this->assertRedirect(['action' => 'index']);

        //check message
        $this->assertFlashMessage('アップロードカテゴリ「uploader add test」を追加しました。');

        //get data after when add and compare data
        $uploads = $this->getTableLocator()->get('BcUploader.UploaderCategories');
        $item = $uploads->find()->where([
            'name' => 'uploader add test',
        ])->first();
        $this->assertEquals($item['name'], 'uploader add test');

        $data = [
            'name' => null
        ];
        $this->post('/baser/admin/bc-uploader/uploader_categories/add', $data);
        //check message
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertEquals(['name' => ['_empty' => "カテゴリ名を入力してください。"]], $vars['uploaderCategory']->getErrors());
        $this->assertResponseCode(200);
    }
}
