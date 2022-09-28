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

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\Service\BcDatabaseService;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * class UtilitiesControllerTest
 * @package BaserCore\Controller\Admin\UtilitiesController;
 */
class UtilitiesControllerTest extends BcTestCase
{
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Contents'
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/baser-core/utilities/');
        $this->loginAdmin($request);
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
     * test clear_cache
     *
     * @return void
     */
    public function testClear_cache(): void
    {

        $this->get('/baser/admin/baser-core/utilities/clear_cache');
        $this->assertResponseCode(302);
    }

    /**
     * test ajax_save_search_box
     *
     * @return void
     */
    public function testAjax_save_search_box(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test index
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test reset_contents_tree
     *
     * @return void
     */
    public function testReset_contents_tree(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        ContentFactory::make(['id' => 1, 'name' => 'BaserCore root', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 1, 'rght' => 2])->persist();
        ContentFactory::make(['name' => 'BaserCore 1', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 11, 'rght' => 12])->persist();
        ContentFactory::make(['name' => 'BaserCore 2', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 13, 'rght' => 14])->persist();

        $this->post('/baser/admin/baser-core/utilities/reset_contents_tree/');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'index'
        ]);
        $this->assertFlashMessage("コンテンツのツリー構造をリセットしました。");
    }

    /**
     * test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test credit
     *
     * @return void
     */
    public function testCredit(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test log_maintenance
     *
     * @return void
     */
    public function testLog_maintenance(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test info
     */
    public function test_info()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/admin/baser-core/utilities/info');
        $this->assertResponseOk();
    }

    /**
     * test info
     */
    public function test_verity_contents_tree()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        // コンテンツのデータ作成
        ContentFactory::make([
            'id' => 1,
            'url' => '/',
            'name' => '',
            'plugin' =>
                'BaserCore',
            'type' =>
                'ContentFolder',
            'site_id' => 1,
            'parent_id' => null,
            'entity_id' => 1
        ])->persist();

        // コンテンツのツリー構造に問題がある場合
        $this->post('/baser/admin/baser-core/utilities/verity_contents_tree/');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'index'
        ]);
        $this->assertFlashMessage("コンテンツのツリー構造に問題があります。ログを確認してください。");

        // コンテンツのツリー構造に問題がない場合
        $BcDatabaseService = new BcDatabaseService();
        $BcDatabaseService->truncate('contents');
        $this->post('/baser/admin/baser-core/utilities/verity_contents_tree/');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'index'
        ]);
        $this->assertFlashMessage("コンテンツのツリー構造に問題はありません。");
    }

}
