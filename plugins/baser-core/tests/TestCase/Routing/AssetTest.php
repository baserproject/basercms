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

namespace BaserCore\Test\TestCase\Routing;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFolder;
use Cake\Routing\Asset;

/**
 * Class AssetTest
 */
class AssetTest extends BcTestCase
{

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test webroot
     *
     * 適用テーマにアセットが存在する場合はそちらのパスを返し
     * 存在しない場合は、デフォルトテーマのアセットのパスを返す
     */
    public function testWebroot()
    {
        $result = Asset::webroot('css/style.css', ['theme' => 'BcThemeSample']);
        $this->assertEquals('/bc_front/css/style.css', $result);
        $cssDir = ROOT . DS . 'plugins' . DS . 'BcPluginSample' . DS . 'webroot' . DS . 'css' . DS;
        $folder = new BcFolder($cssDir);
        $folder->create();
        touch($cssDir . 'style.css');
        $result = Asset::webroot('css/style.css', ['theme' => 'BcPluginSample']);
        $this->assertEquals('/bc_plugin_sample/css/style.css', $result);
        $folder->delete();
    }


    /**
     * test webroot
     * @param string $path
     * @param array $options
     * @param string $expected
     * @dataProvider scriptUrlDataProvider
     */
    public function testScriptUrl(string $path, array $options, string $expected)
    {
        $result = Asset::scriptUrl($path, $options);
        $this->assertSame($expected, $result);
    }

    /**
     * scriptUrlDataProvider
     */
    public static function scriptUrlDataProvider(): array
    {
        return [
            [
                'path' => 'script.js',
                'options' => [],
                'expected' => '/js/script.js'
            ],
            [
                'path' => 'https://localhost//bc_plugin_sample/js/script.js',
                'options' => [],
                'expected' => 'https://localhost//bc_plugin_sample/js/script.js'
            ],
            [
                'path' => 'js/script.js',
                'options' => ['pathPrefix' => 'admin'],
                'expected' => '/adminjs/script.js'
            ],
        ];
    }
}
