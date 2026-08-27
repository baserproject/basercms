<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMcp\Controller\Admin\McpServerManagerController;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\Http\ServerRequest;

/**
 * McpServerManagerControllerTest
 */
class McpServerManagerControllerTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * test getRegisteredTools が登録済みツールを名前と説明付きで返す
     *
     * 移植前は「利用可能な機能」がテンプレートに手書きされており実態とずれていた
     */
    public function testGetRegisteredTools()
    {
        $controller = new McpServerManagerController(new ServerRequest());

        $tools = $controller->getRegisteredTools();

        $this->assertNotEmpty($tools);
        $names = array_column($tools, 'name');
        $this->assertContains('addBlogPost', $names);
        $this->assertContains('addCustomEntry', $names);
        $this->assertContains('serverInfo', $names);

        // 名前だけでなく説明も表示するため、説明が空でない事を確認する
        foreach($tools as $tool) {
            $this->assertNotEmpty($tool['name']);
            $this->assertNotEmpty($tool['description'], "ツール {$tool['name']} の説明が空です");
        }
    }

    /**
     * test 管理画面が表示される
     */
    public function testIndex()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin/bc-mcp/mcp-server-manager'));

        $this->get('/baser/admin/bc-mcp/mcp-server-manager');

        $this->assertResponseSuccess();
        // 接続情報と対応プロトコルバージョンが表示される
        $this->assertResponseContains('/bc-mcp');
        $this->assertResponseContains('2026-07-28');
        // 登録済みツールが表示される
        $this->assertResponseContains('addBlogPost');
        // 起動・停止の操作は無くなっている
        $this->assertResponseNotContains('mcp_server_manager/start');
        $this->assertResponseNotContains('mcp_server_manager/stop');
    }

    /**
     * 暗号化キー未設定なら管理画面に警告が出る
     *
     * @return void
     */
    public function testIndexShowsWarningWhenEncryptionKeyIsMissing(): void
    {
        $original = env('OAUTH2_ENC_KEY');
        putenv('OAUTH2_ENC_KEY');
        unset($_ENV['OAUTH2_ENC_KEY'], $_SERVER['OAUTH2_ENC_KEY']);
        try {
            $this->loadFixtureScenario(InitAppScenario::class);
            $this->loginAdmin($this->getRequest());
            $this->get('/baser/admin/bc-mcp/mcp-server-manager');
            $this->assertResponseOk();
            $this->assertResponseContains('OAUTH2_ENC_KEY');
            $this->assertResponseContains('停止しています');
        } finally {
            if ($original !== null) {
                putenv('OAUTH2_ENC_KEY=' . $original);
                $_ENV['OAUTH2_ENC_KEY'] = $original;
            }
        }
    }

}
