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

namespace BaserCore\Test\TestCase\Controller;

use BaserCore\Controller\BcErrorController;
use BaserCore\Test\Scenario\InitAppScenario;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BcErrorControllerTest Test Case
 */
class BcErrorControllerTest extends BcTestCase
{
    /**
     * Trait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->BcErrorController = new BcErrorController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test beforeRender
     */
    public function testBeforeRender()
    {
        $this->BcErrorController->beforeRender(new Event('beforeRender'));
        $this->assertEquals('BcFront', $this->BcErrorController->viewBuilder()->getTheme());
    }

    /**
     * Test beforeRender
     * サブサイトの URL のエラー画面は、サブサイトのテーマで表示する
     */
    public function testBeforeRender_subSite()
    {
        SiteFactory::make()->english(3)->setField('theme', 'EnglishTheme')->persist();
        $request = $this->getRequest('/en/not_exists');
        // ルーティングに失敗した場合、Router のリクエストにはサイトの情報が設定されていない
        Router::setRequest(new ServerRequest(['url' => '/en/not_exists']));
        $controller = new BcErrorController($request);
        $controller->beforeRender(new Event('beforeRender'));
        $this->assertEquals('EnglishTheme', $controller->viewBuilder()->getTheme());
    }

    /**
     * Test missing connection
     * データベースに接続できない際、管理画面のデザインでエラーを表示すること
     */
    public function testMissingConnection()
    {
        // 準備
        $config = ConnectionManager::getConfig('default');
        $config['username'] = 'test';
        ConnectionManager::drop('default');
        ConnectionManager::setConfig('default', $config);

        // 実行
        $this->get('/');
        $this->assertResponseContains('サイト表示');
        $this->assertResponseContains('bca-main');

        // 後処理
        $config['username'] = 'root';
        ConnectionManager::drop('default');
        ConnectionManager::setConfig('default', $config);
    }

}
