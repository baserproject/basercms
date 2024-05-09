<?php

namespace BcUploader\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcUploader\Controller\Admin\UploaderFilesController;
use BcUploader\Test\Factory\UploaderFileFactory;
use BcUploader\Test\Scenario\UploaderFilesScenario;
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
        $this->truncateTable('uploader_categories');
        $this->truncateTable('uploader_files');
    }

    /**
     * test beforeFilter
     */
    public function test_beforeFilter(){
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
    }

    public function test_index()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
    }

    public function test_ajax_index()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
    }

    public function test_ajax_list()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
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
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        
        //$pathImg = WWW_ROOT . DS . 'files' . DS . 'uploads' . DS;
        //(new BcFile($pathImg . 'social_new.jpg'))->create();

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