<?php

namespace BcUploader\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class UploaderConfigsControllerTest extends BcTestCase
{

    use ScenarioAwareTrait;

    public $UploaderConfigsController;

    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
    }

    public function tearDown(): void
    {
        unset($this->UploaderConfigsController);
        parent::tearDown();
    }

    /**
     * test index
     */
    public function test_index()
    {
        $this->loginAdmin($this->getRequest('/'));
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