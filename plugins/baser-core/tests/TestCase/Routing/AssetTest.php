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
     * test testCssUrl
     * @param string $path
     * @param array $options
     * @param string $expected
     * @dataProvider cssUrlDataProvider
     */
    public function testCssUrl(string $path, array $options, string $expected)
    {
        $result = Asset::cssUrl($path, $options);
        $this->assertEquals($expected, $result);
    }

    public static function cssUrlDataProvider(): array
    {
        return [
            //basic path
            [
                'path' => 'styles.css',
                'options' => [],
                'expected' => '/css/styles.css'
            ],
            //full base URL
            [
                'path' => 'http://localhost/css/styles.css',
                'options' => [],
                'expected' => 'http://localhost/css/styles.css'
            ],
            //custom pathPrefix
            [
                'path' => 'styles.css',
                'options' => ['pathPrefix' => '/custom/'],
                'expected' => '/custom/styles.css'
            ],
        ];
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

}
