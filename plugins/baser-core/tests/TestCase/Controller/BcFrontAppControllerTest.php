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
use BaserCore\Test\Factory\PluginFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainer;
use Cake\Core\Configure;
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
            ['name' => 'BaserCore'],
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
        $this->markTestIncomplete('このテストはまだ実装されていません。');
    }

    /**
     * Test beforeRender
     */
    public function testBeforeRender()
    {
        $this->markTestIncomplete('このテストはまだ実装されていません。');
        // TODO ucmitz 本体側の実装要
        /* >>>
        $this->BcFrontAppController->setRequest($this->getRequest('/en/サイトID3の固定ページ'));
        $this->BcFrontAppController->beforeRender(new Event('beforeRender'));
        $this->assertEquals('en', $this->BcFrontAppController->viewBuilder()->getLayoutPath());
        $this->assertEquals('en', $this->BcFrontAppController->viewBuilder()->getTemplatePath());
        <<< */
    }

}
