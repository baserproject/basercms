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

namespace BcThemeConfig\Test\TestCase\Service;

use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcThemeConfig\Service\ThemeConfigsService;
use BcThemeConfig\Service\ThemeConfigsServiceInterface;
use BcThemeConfig\Test\Scenario\ThemeConfigsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\Filesystem\File;

/**
 * ThemeConfigsServiceTest
 */
class ThemeConfigsServiceTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Test subject
     *
     * @var ThemeConfigsService
     */
    public $ThemeConfigsService;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BcThemeConfig.Factory/ThemeConfigs',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemeConfigsService = $this->getService(ThemeConfigsServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        unset($this->ThemeConfigsService);
        parent::tearDown();
    }

    /**
     * Test __construct
     */
    public function test__construct()
    {
        // テーブルがセットされている事を確認
        $this->assertEquals('theme_configs', $this->ThemeConfigsService->ThemeConfigs->getTable());
    }

    /**
     * test get
     */
    public function test_get()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test clearCache
     */
    public function test_clearCache()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test update
     */
    public function test_update()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test saveImage
     */
    public function test_saveImage()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test deleteImage
     */
    public function test_deleteImage()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test updateColorConfig
     */
    public function test_updateColorConfig()
    {
        //データを生成
        $this->getRequest()->getAttribute('currentSite');
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'bc-column'])->persist();
        $this->loadFixtureScenario(ThemeConfigsScenario::class);

        //元に戻るため、変更する前内容を取得する
        $configPath = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'config.css';
        $fileContentBefore = file_get_contents($configPath);

        //テストメソッドをコール
        $rs = $this->ThemeConfigsService->updateColorConfig($this->ThemeConfigsService->get());
        //戻る値を確認
        $this->assertTrue($rs);

        //WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'config.css'の中身を確認
        $fileContentAfter = file_get_contents($configPath);

        $this->assertTextContains('.main-color {
	background: #001800 !important;
}', $fileContentAfter);

        $this->assertTextContains('.sub-color {
	background: #001800 !important;
}', $fileContentAfter);

        $this->assertTextContains('a {
	color:#2B7BB9;
}', $fileContentAfter);

        $this->assertTextContains('.btn {
    background-color: #001800 !important;
}', $fileContentAfter);

        //config.cssの内容を元に戻る
        $File = new File($configPath, true, 0666);
        $File->write($fileContentBefore);
        $File->close();
    }

}
