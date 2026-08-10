<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Test\TestCase\Lib;

use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\TestSuite\BcTestCase;
use BcBurgerEditor\Lib\BurgerEditorUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;

/**
 * BurgerEditorUtilTest
 */
class BurgerEditorUtilTest extends BcTestCase
{

    /**
     * test getGoogleMapApiKey
     */
    public function test_getGoogleMapApiKey()
    {
        SiteConfigFactory::make(['name' => 'google_maps_api_key', 'value' => 'test-api-key'])->persist();
        $this->assertEquals('test-api-key', BurgerEditorUtil::getGoogleMapApiKey());
    }

    /**
     * test getSuffix
     *
     * enableStaticFileSuffix が無効の場合は空文字を返す
     */
    public function test_getSuffix_disabled()
    {
        Configure::write('Bge.enableStaticFileSuffix', false);
        $path = Plugin::path('BcBurgerEditor') . 'composer.json';
        $this->assertSame('', BurgerEditorUtil::getSuffix($path));
    }

    /**
     * test getSuffix
     *
     * 有効の場合は更新日時のタイムスタンプを返す
     */
    public function test_getSuffix_enabled()
    {
        Configure::write('Bge.enableStaticFileSuffix', true);
        Configure::write('Bge.staticFileSuffix', '');
        $path = Plugin::path('BcBurgerEditor') . 'composer.json';
        $this->assertSame('?' . filemtime($path), BurgerEditorUtil::getSuffix($path));
    }

    /**
     * test getSuffix
     *
     * staticFileSuffix が指定されている場合は末尾に連結する
     */
    public function test_getSuffix_withSuffixText()
    {
        Configure::write('Bge.enableStaticFileSuffix', true);
        Configure::write('Bge.staticFileSuffix', 'v1');
        $path = Plugin::path('BcBurgerEditor') . 'composer.json';
        $this->assertSame('?' . filemtime($path) . '-v1', BurgerEditorUtil::getSuffix($path));
    }

    /**
     * test getAddonPath
     *
     * 標準ではプラグイン直下の Addon のみを返す
     */
    public function test_getAddonPath()
    {
        Configure::write('Bge.enableAddonPlugin', []);
        $result = BurgerEditorUtil::getAddonPath();
        $this->assertCount(1, $result);
        $this->assertSame(Plugin::path('BcBurgerEditor') . 'Addon' . DS, $result[0]);
    }

    /**
     * test getAddonPath
     *
     * 未ロードのプラグインを指定しても無視される
     */
    public function test_getAddonPath_withUnloadedPlugin()
    {
        Configure::write('Bge.enableAddonPlugin', ['BcNotExistsPluginForTest']);
        $this->assertCount(1, BurgerEditorUtil::getAddonPath());
    }

    /**
     * test getTypePath
     */
    public function test_getTypePath()
    {
        Configure::write('Bge.enableAddonPlugin', []);
        $expected = Plugin::path('BcBurgerEditor') . 'Addon' . DS . 'type' . DS . 'image' . DS;
        $this->assertSame($expected, BurgerEditorUtil::getTypePath('image'));
        $this->assertFalse(BurgerEditorUtil::getTypePath('not-exists-type'));
    }

    /**
     * test getBlockPath
     */
    public function test_getBlockPath()
    {
        Configure::write('Bge.enableAddonPlugin', []);
        $expected = Plugin::path('BcBurgerEditor') . 'Addon' . DS . 'block' . DS . 'image2' . DS;
        $this->assertSame($expected, BurgerEditorUtil::getBlockPath('image2'));
        $this->assertFalse(BurgerEditorUtil::getBlockPath('not-exists-block'));
    }

    /**
     * test getExtension
     *
     * @param string $filename
     * @param string $expected
     * @dataProvider getExtensionDataProvider
     */
    public function test_getExtension($filename, $expected)
    {
        $this->assertSame($expected, BurgerEditorUtil::getExtension($filename));
    }

    public static function getExtensionDataProvider()
    {
        return [
            ['sample.jpg', 'jpg'],
            ['sample.tar.gz', 'gz'],
            ['/path/to/sample.PNG', 'PNG'],
            // 拡張子がない場合はファイル名がそのまま返る
            ['sample', 'sample'],
        ];
    }

    /**
     * test getFileNameNoExtension
     *
     * @param string $filename
     * @param string $expected
     * @dataProvider getFileNameNoExtensionDataProvider
     */
    public function test_getFileNameNoExtension($filename, $expected)
    {
        $this->assertSame($expected, BurgerEditorUtil::getFileNameNoExtension($filename));
    }

    public static function getFileNameNoExtensionDataProvider()
    {
        return [
            ['sample.jpg', 'sample'],
            ['sample.tar.gz', 'sample.tar'],
            // 拡張子がない場合は空文字となる
            ['sample', ''],
        ];
    }

    /**
     * test mb_basename
     *
     * @param string $path
     * @param string $suffix
     * @param string $expected
     * @dataProvider mbBasenameDataProvider
     */
    public function test_mb_basename($path, $suffix, $expected)
    {
        $this->assertSame($expected, BurgerEditorUtil::mb_basename($path, $suffix));
    }

    public static function mbBasenameDataProvider()
    {
        return [
            ['/var/www/html/日本語ファイル.jpg', '', '日本語ファイル.jpg'],
            ['/var/www/html/日本語ファイル.jpg', '.jpg', '日本語ファイル'],
            // Windows 形式の区切り文字にも対応する
            ['C:\\path\\to\\サンプル.png', '', 'サンプル.png'],
            ['sample.jpg', '', 'sample.jpg'],
        ];
    }

    /**
     * test b64e / b64d
     *
     * エンコードした文字列がデコードで元に戻る
     *
     * @param string $value
     * @dataProvider b64DataProvider
     */
    public function test_b64e_b64d_roundTrip($value)
    {
        $this->assertSame($value, BurgerEditorUtil::b64d(BurgerEditorUtil::b64e($value)));
    }

    public static function b64DataProvider()
    {
        return [
            ['sample'],
            // base64 のパディングが 2 つ付き、連続ドットの変換が発生する
            ['a'],
            // base64 のパディングが 1 つ付く
            ['ab'],
            ['日本語のファイル名'],
            ['記号 + / = を含む'],
            [''],
        ];
    }

    /**
     * test b64e
     *
     * URLで扱えない文字と連続ドットが変換される
     */
    public function test_b64e_notContainsUnsafeCharacters()
    {
        foreach(['sample', 'a', 'ab', '日本語のファイル名'] as $value) {
            $encoded = BurgerEditorUtil::b64e($value);
            $this->assertDoesNotMatchRegularExpression('/[+\/=]/', $encoded, "{$value} のエンコード結果に使用できない文字が含まれています");
            $this->assertStringNotContainsString('..', $encoded, "{$value} のエンコード結果に連続ドットが含まれています");
        }
    }

}
