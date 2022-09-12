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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\ThemesService;
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * ThemesServiceTest
 * @property ThemesService $ThemesService
 */
class ThemesServiceTest extends \BaserCore\TestSuite\BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ThemesService = $this->getService(ThemesServiceInterface::class);
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
     * 初期データのセットを取得する
     */
    public function testGetDefaultDataPatterns()
    {
        $options = ['useTitle' => false];
        $result = $this->ThemesService->getDefaultDataPatterns('BcFront', $options);
        $expected = [
            'BcFront.default' => 'default',
            'BcFront.empty' => 'empty'
        ];
        $this->assertEquals($expected, $result, '初期データのセットのタイトルを外して取得できません');
        $result = $this->ThemesService->getDefaultDataPatterns('BcFront');
        $expected = [
            'BcFront.default' => 'フロントテーマ ( default )',
            'BcFront.empty' => 'フロントテーマ ( empty )'
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * test copy
     * @return void
     */
    public function testCopy()
    {
        $rs = $this->ThemesService->copy('BcFront');
        $this->assertTrue($rs);
        //コピーを確認
        $this->assertTrue(is_dir(BASER_THEMES . 'BcFrontCopy'));

        $pluginPath = BcUtil::getPluginPath('BcFrontCopy');
        $file = new File($pluginPath . 'src' . DS . 'Plugin.php');
        $data = $file->read();
        //namespaceの書き換えを確認
        $this->assertTrue(str_contains($data, 'namespace BcFrontCopy;'));
        $file->close();

        $this->ThemesService->delete('BcFrontCopy');
    }
    /**
     * test delete
     * @return void
     */
    public function testDelete()
    {
        $this->ThemesService->copy('BcFront');
        $rs = $this->ThemesService->delete('BcFrontCopy');
        $this->assertTrue($rs);
        $this->assertTrue(!is_dir(BASER_THEMES . 'BcFrontCopy'));
    }
    /**
     * test getThemesDefaultDataInfo
     * @return void
     */
    public function testGetThemesDefaultDataInfo()
    {
        $theme = 'BcSpaSample';
        $themePath = BcUtil::getPluginPath($theme);

        mkdir($themePath . 'Plugin', 0777, true);
        mkdir($themePath . 'Plugin/test', 0777, true);

        $file = new File($themePath . 'Plugin/test/test.txt');
        $file->write('test file plugin');
        $file->close();

        $file = new File($themePath . 'Plugin/test2.txt');
        $file->write('test file 2');
        $file->close();


        $rs = $this->execPrivateMethod($this->ThemesService, 'getThemesPluginsInfo', [$theme]);
        $this->assertEquals('このテーマは下記のプラグインを同梱しています。', $rs[0]);
        $this->assertEquals('	・test', $rs[1]);

        $folder = new Folder();
        $folder->delete($themePath . 'Plugin');
    }

    /**
     * test getMarketThemes
     * @return void
     */
    public function testGetMarketThemes()
    {
        $themes = $this->ThemesService->getMarketThemes();
        $this->assertEquals(true, count($themes) > 0);
        $this->assertEquals('multiverse', $themes[0]['title']);
        $this->assertEquals('1.0.0', $themes[0]['version']);
        $this->assertEquals('テーマ', $themes[0]['category']);
    }

    /**
     * 一覧データ取得
     */
    public function testGetIndex()
    {
        $themes = $this->ThemesService->getIndex();
        $this->assertEquals('BcFront', $themes[1]->name);
    }

    /**
     * テーマを適用する
     */
    public function testApply()
    {
        $beforeTheme = 'BcSpaSample';
        $afterTheme = 'BcFront';
        SiteFactory::make(['id' => 1, 'title' => 'Test Title', 'name' => 'Test Site', 'theme'=> $beforeTheme, 'status' => 1])->persist();
        $site = SiteFactory::get(1);
        Router::setRequest($this->getRequest());
        $result = $this->ThemesService->apply($site, $afterTheme);
        $site = SiteFactory::get(1);
        $this->assertNotEquals($beforeTheme, $site->theme);
        $this->assertCount(2, $result);
        $this->assertEquals('このテーマは初期データを保有しています。', $result[0]);
        $this->assertEquals('Webサイトにテーマに合ったデータを適用するには、初期データ読込を実行してください。', $result[1]);
    }
}
