<?php

namespace BcUploader\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcUploader\Controller\Admin\UploaderConfigsController;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class UploaderConfigsControllerTest extends BcTestCase
{
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->UploaderConfigsController = new UploaderConfigsController($this->loginAdmin($this->getRequest()));
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test index
     */
    public function test_index()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        //正常系実行
        $this->get("/baser/admin/bc-uploader/uploader_configs/index");
        $this->assertResponseCode(200);

        $this->post("/baser/admin/bc-uploader/uploader_configs/index", ['name_add' => 'value_add']);
        $this->assertResponseCode(302);
        $this->assertFlashMessage('アップローダー設定を保存しました。');
        $this->assertRedirect("/baser/admin/bc-uploader/uploader_configs/index");
    }
}