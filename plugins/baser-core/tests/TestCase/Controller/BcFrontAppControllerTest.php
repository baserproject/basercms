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

use BaserCore\Controller\BcFrontAppController;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\PluginFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainer;
use Cake\Event\Event;
use Cake\I18n\I18n;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BcFrontAppControllerTest
 * @property BcFrontAppController $BcFrontAppController
 */
class BcFrontAppControllerTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->BcFrontAppController = new BcFrontAppController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->BcFrontAppController);
    }

    /**
     * test Not Found
     *
     * ルーティングが存在しない場合に、404エラーが返ることを確認する
     * エラー画面の表示の際に、内部的にエラーが発生すると 500エラーになるため確認する
     * @return void
     */
    public function testNotFound()
    {
        // setUp でコンテナの初期化が行われるため、ここで再度初期化する
        BcContainer::clear();
        // どのプラグインが影響を与えるかわからないので全プラグイン有効化する
        PluginFactory::make([
            ['name' => 'BcBlog'],
            ['name' => 'BcContentLink'],
            ['name' => 'BcCustomContent'],
            ['name' => 'BcEditorTemplate'],
            ['name' => 'BcFavorite'],
            ['name' => 'BcMail'],
            ['name' => 'BcSearchIndex'],
            ['name' => 'BcThemeConfig'],
            ['name' => 'BcThemeFile'],
            ['name' => 'BcUploader'],
            ['name' => 'BcWidgetArea']
        ])->persist();
        $this->get('/aaa');
        $this->assertResponseCode(404);
        // BcCustomContentを削除しておかないと、他のテストに影響を与えるため削除する
        $this->Application->getPlugins()->remove('BcCustomContent');
    }

    /**
     * test beforeFilter
     */
    public function testBeforeFilter()
    {
        //準備
        ContentFactory::make(['url' => '/', 'site_id' => 2])->persist();
        SiteFactory::make(['id' => 2, 'lang' => 'english'])->persist();
        $this->BcFrontAppController->setRequest($this->loginAdmin($this->getRequest('/')));
        //テスト
        $this->BcFrontAppController->beforeFilter(new Event('Controller.beforeFilter', $this->BcFrontAppController));
        //language check
        $this->assertEquals('en', I18n::getLocale());
    }

    /**
     * Test beforeRender
     */
    public function testBeforeRender()
    {
        $this->BcFrontAppController->setRequest($this->getRequest());
        $this->BcFrontAppController->beforeRender(new Event('beforeRender'));
        $viewBuilder = $this->BcFrontAppController->viewBuilder();
        $this->assertEquals('BaserCore.BcFrontApp', $viewBuilder->getClassName());
        $this->assertEquals('BcFront', $viewBuilder->getTheme());
    }

}
