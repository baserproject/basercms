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

use BaserCore\Model\Entity\SiteConfig;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BcThemeConfig\Model\Entity\ThemeConfig;
use BcThemeConfig\Service\ThemeConfigsService;
use BcThemeConfig\Service\ThemeConfigsServiceInterface;
use BcThemeConfig\Test\Scenario\ThemeConfigsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Laminas\Diactoros\UploadedFile;

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
        //データを生成
        $this->loadFixtureScenario(ThemeConfigsScenario::class);
        //テスト対象メソッドをコール
        $rs = $this->ThemeConfigsService->get();
        //戻る値を確認
        $this->assertEquals('001800', $rs->color_main);
        $this->assertEquals('001800', $rs->color_sub);
        $this->assertEquals('2B7BB9', $rs->color_link);
        $this->assertEquals('2B7BB9', $rs->color_hover);
    }

    /**
     * test clearCache
     */
    public function test_clearCache()
    {
        //データを生成
        $this->loadFixtureScenario(ThemeConfigsScenario::class);
        //$entityが値を設定する
        $this->ThemeConfigsService->get();
        //テストメソッドをコール前に$entityの値があるか確認
        $this->assertNotNull($this->getPrivateProperty($this->ThemeConfigsService, 'entity'));
        //テストメソッドをコール
        $this->ThemeConfigsService->clearCache();
        //戻る値を確認
        $this->assertNull($this->getPrivateProperty($this->ThemeConfigsService, 'entity'));
    }

    /**
     * test update
     */
    public function test_update()
    {
        //データを生成
        $this->getRequest()->getAttribute('currentSite');
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'BcThemeSample'])->persist();
        //テストメソッドをコール
        $this->ThemeConfigsService->update(['logo_alt' => 'baserCMS']);
        //テーマ設定のデータが更新されたか確認すること
        $themeConfigs = $this->ThemeConfigsService->get();
        $this->assertEquals($themeConfigs->logo_alt, 'baserCMS');
    }

    /**
     * test saveImage
     */
    public function test_saveImage()
    {
        // データを生成
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'BcColumn'])->persist();
        $this->loadFixtureScenario(ThemeConfigsScenario::class);

        // ThemeConfigsService::entity の値を設定する
        $this->ThemeConfigsService->get();

        // アップロードファイルを準備
        $filePath = TMP . 'test_upload' . DS;
        (new BcFolder($filePath))->create();
        $testFile = $filePath . 'logo.png';
        copy(ROOT . '/plugins/bc-front/webroot/img/logo.png', $testFile);
        $this->setUploadFileToRequest('file', $testFile);
        // 実行
        $rs = $this->ThemeConfigsService->saveImage(new ThemeConfig([
            'logo' => new UploadedFile(
                $testFile,
                1000,
                UPLOAD_ERR_OK,
                'logo.png',
                "image/png",
            )
        ]));
        $uploadedPath = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'logo.png';
        $uploadedThumbPath = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'logo_thumb.png';

        // 戻り値を確認
        $this->assertEquals($rs['logo'], 'logo.png');
        // サムネイルが作成されたことを確認
        $this->assertFileExists($uploadedPath);
        $this->assertFileExists($uploadedThumbPath);

        // 初期化処理
        unlink($uploadedPath);
        unlink($uploadedThumbPath);
    }

    /**
     * test deleteImage
     */
    public function test_deleteImage()
    {
        $this->loadFixtureScenario(ThemeConfigsScenario::class);

        //$entityが値を設定する
        $this->ThemeConfigsService->get();
        //テーマロゴを設定する
        $logoPath = '/var/www/html/plugins/BcColumn/webroot/img/logo.png';
        $pathTest = WWW_ROOT . 'files' . DS . 'theme_configs' . DS . 'logo.png';
        copy($logoPath, $pathTest);

        //テストサービスをコール
        $rs = $this->ThemeConfigsService->deleteImage(new SiteConfig([
            'logo_delete' => 1
        ]));

        //戻る値を確認
        $this->assertEquals($rs['logo'], '');
        //fileが存在しないか確認すること
        $this->assertFalse(file_exists($pathTest));
    }

    /**
     * test updateColorConfig
     */
    public function test_updateColorConfig()
    {
        //データを生成
        $this->getRequest()->getAttribute('currentSite');
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'BcColumn'])->persist();
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
        $File = new BcFile($configPath);
        $File->create();
        $File->write($fileContentBefore);
    }

}
