<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcInstaller\Test\TestCase\Controller\Admin;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcInstaller\Controller\Admin\InstallationsController;
use Cake\Core\Configure;
use Cake\Event\Event;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class InstallationsControllerTest
 *
 * @property  InstallationsController $InstallationsController
 */
class InstallationsControllerTest extends BcTestCase
{
    use ScenarioAwareTrait;

    use BcContainerTrait;

    /**
     * setup
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest());
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->markTestSkipped('このテストは未確認です。');
        $this->InstallationsController = new InstallationsController($this->getRequest());
        $event = new Event('Controller.beforeFilter', $this->InstallationsController);
        $this->InstallationsController->beforeFilter($event);
        $this->assertEquals(300, ini_get("max_execution_time"));
    }

    /**
     * Step 1: ウェルカムページ
     */
    public function testIndex()
    {
        Configure::write("BcEnv.isInstalled", false);

        $this->get('/');

        //CSRFがあるか確認すること
        $_cookies = $this->getPrivateProperty($this->_response, '_cookies');
        $cookies = $this->getPrivateProperty($_cookies, 'cookies');
        $this->assertNotEmpty($cookies['csrfToken;;/']);

        Configure::write("BcEnv.isInstalled", true);
    }

    /**
     * Step 2: 必須条件チェック
     */
    public function testStep2()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        Configure::write("BcEnv.isInstalled", false);

        $this->get('/baser/admin/bc-installer/installations/step2');
        $this->assertResponseCode(200);

        $this->post('/baser/admin/bc-installer/installations/step2', ['mode'=>'next']);
        $this->assertResponseCode(302);

        Configure::write("BcEnv.isInstalled", true);
    }

    /**
     * Step 3: データベースの接続設定
     */
    public function testStep3()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        Configure::write("BcEnv.isInstalled", false);

        $this->get('/baser/admin/bc-installer/installations/step3');
        $this->assertResponseCode(200);

        $config = [
            'mode' => 'back',
            'dbType' => 'mysql',
            'dbHost' => 'localhost',
            'dbPrefix' => '',
            'dbPort' => '3306',
            'dbUsername' => 'dbUsername',
            'dbPassword' => 'dbPassword',
            'dbSchema' => 'dbSchema',
            'dbName' => 'basercms',
            'dbEncoding' => 'utf-8',
            'dbDataPattern' => 'BcThemeSample.default'
        ];

        $this->post('/baser/admin/bc-installer/installations/step3', $config);
        $this->assertResponseCode(302);
        $this->assertRedirect('/baser/admin/bc-installer/installations/step2');

        $config['mode'] = 'checkDb';
        $this->post('/baser/admin/bc-installer/installations/step3', $config);
        $this->assertResponseCode(200);

        $config['mode'] = 'createDb';
        $this->post('/baser/admin/bc-installer/installations/step3', $config);
        $this->assertResponseCode(200);


        Configure::write("BcEnv.isInstalled", true);
    }

    /**
     * Step 4: データベース生成／管理者ユーザー作成
     */
    public function testStep4()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Step 5: 設定ファイルの生成
     * データベース設定ファイル[database.php]
     * インストールファイル[install.php]
     */
    public function testStep5()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * インストール不能警告メッセージを表示
     */
    public function testAlert()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * baserCMSを初期化する
     * debug フラグが -1 の場合のみ実行可能
     */
    public function testReset()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 全てのテーブルを削除する
     */
    public function test_deleteAllTables()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
