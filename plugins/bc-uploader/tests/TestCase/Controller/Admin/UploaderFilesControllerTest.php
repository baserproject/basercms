<?php

namespace BcUploader\Test\TestCase\Controller\Admin;

use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcUploader\Controller\Admin\UploaderFilesController;
use BcUploader\Test\Factory\UploaderConfigFactory;
use BcUploader\Test\Factory\UploaderFileFactory;
use BcUploader\Test\Scenario\UploaderFilesScenario;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class UploaderFilesControllerTest extends BcTestCase
{
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->UploaderFilesController = new UploaderFilesController($this->loginAdmin($this->getRequest()));
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test beforeFilter
     */
    public function test_beforeFilter(){
        $event = new Event('Controller.beforeFilter', $this->UploaderFilesController);
        $this->UploaderFilesController->beforeFilter($event);
        $this->assertNotEmpty($this->UploaderFilesController->viewBuilder()->getHelpers());
    }

    public function test_index()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
    }

    public function test_ajax_index()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
    }

    /**
     * test ajax_list
     */
    public function test_ajax_list()
    {
        $this->loadFixtureScenario(UploaderFilesScenario::class);
        SiteConfigFactory::make(['name' => 'admin_list_num', 'value' => 5])->persist();
        UploaderConfigFactory::make(['name' => 'layout_type', 'value' => 'panel'])->persist();

        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //正常系実行 $id != null
        $this->get("/baser/admin/bc-uploader/uploader_files/ajax_list/1");
        $this->assertResponseCode(200);

        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertFalse($this->_controller->viewBuilder()->isAutoLayoutEnabled());
        $this->assertEquals(1, $vars['listId']);
        $this->assertEquals("panel", $vars['layoutType']);
        $this->assertEquals(5, count($vars['uploaderFiles']));

        //正常系実行 $id == null
        $this->get("/baser/admin/bc-uploader/uploader_files/ajax_list");
        $this->assertResponseCode(200);

        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertFalse($this->_controller->viewBuilder()->isAutoLayoutEnabled());
        $this->assertEquals(null, $vars['listId']);
        $this->assertEquals("panel", $vars['layoutType']);
        $this->assertEquals(5, count($vars['uploaderFiles']));
    }

    public function test_ajax_image()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
    }

    public function test_ajax_exists_images()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
    }

    public function test_edit()
    {
        $this->loadFixtureScenario(UploaderFilesScenario::class);

        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //正常系実行
        $data = UploaderFileFactory::get(1);
        $data->alt = 'test edit';
        $this->post("/baser/admin/bc-uploader/uploader_files/edit/1", $data->toArray());
        $this->assertResponseCode(302);
        $this->assertFlashMessage('アップロードファイル「social_new.jpg」を更新しました。');
        $this->assertRedirect('/baser/admin/bc-uploader/uploader_files/index');

        //異常系実行
        $data->id = null;
        $this->post("/baser/admin/bc-uploader/uploader_files/edit/1", $data->toArray());
        $this->assertResponseCode(200);
        $errors = $this->_controller->viewBuilder()->getVars()['uploaderFile']->getErrors();
        $this->assertEquals(['_empty' => 'This field cannot be left empty'], $errors['id']);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->loadFixtureScenario(UploaderFilesScenario::class);

        //正常系実行
        $this->post("/baser/admin/bc-uploader/uploader_files/delete/1");
        $this->assertResponseCode(302);
        $this->assertFlashMessage('アップロードファイル「social_new.jpg」を削除しました。');
        $this->assertRedirect('/baser/admin/bc-uploader/uploader_files/index');

        //check after delete is not exist
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        UploaderFileFactory::get(1);
    }

    public function test_ajax_get_search_box()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
    }
}
