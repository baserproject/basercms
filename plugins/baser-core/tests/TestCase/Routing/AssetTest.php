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
        $this->Asset = new Asset();
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
     * test inflectString
     * @param string $input
     * @param string $inflectionType
     * @param string $expected
     * @dataProvider inflectStringDataProvider
     */
    public function testInflectString(string $input, string $inflectionType, string $expected)
    {
        Asset::setInflectionType($inflectionType);
        $result = $this->execPrivateMethod($this->Asset, 'inflectString', [$input]);
        $this->assertSame($expected, $result);
    }

    /**
     * inflectStringDataProvider
     */
    public static function inflectStringDataProvider()
    {
        return [
            //underscore
            [
                'input' => 'TestString',
                'inflectionType' => 'underscore',
                'expected' => 'test_string'
            ],
            //dasherize
            [
                'input' => 'TestString',
                'inflectionType' => 'dasherize',
                'expected' => 'test-string'
            ],
            //camelize
            [
                'input' => 'test_string',
                'inflectionType' => 'camelize',
                'expected' => 'TestString'
            ],
            //humanize
            [
                'input' => 'test string',
                'inflectionType' => 'humanize',
                'expected' => 'Test String'
            ],
        ];
    }
}
