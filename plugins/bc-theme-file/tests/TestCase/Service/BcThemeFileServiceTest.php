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

namespace BcThemeFile\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\Service\BcThemeFileService;

/**
 * BcThemeFileServiceTest
 */
class BcThemeFileServiceTest extends BcTestCase
{

    public $BcThemeFileService = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcThemeFileService = new BcThemeFileService();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        unset($this->BcThemeFileService);
        parent::tearDown();
    }

    /**
     * test getFullpath
     */
    public function test_getFullpath()
    {
        //typeが$assetsではない場合、
        $themePath = $this->BcThemeFileService->getFullpath('BcThemeSample', 'BaserCore', 'layout', 'default.php');
        $this->assertEquals('/var/www/html/plugins/BcThemeSample/templates/layout/default.php', $themePath);

        //typeがimgの場合、
        $themePath = $this->BcThemeFileService->getFullpath('BcFront', 'BaserCore', 'img', 'logo.png');
        $this->assertEquals('/var/www/html/plugins/bc-front/webroot/img/logo.png', $themePath);

        //$pluginがないの場合、
        $themePath = $this->BcThemeFileService->getFullpath('BcThemeSample', '', 'layout', 'default.php');
        $this->assertEquals('/var/www/html/plugins/BcThemeSample/templates/layout/default.php', $themePath);
    }

    /**
     * test getFullpath - パストラバーサル攻撃を拒否する
     * @dataProvider provideGetFullpathPathTraversal
     */
    public function test_getFullpath_pathTraversal(string $path): void
    {
        $this->expectException('BaserCore\Error\BcException');
        $this->BcThemeFileService->getFullpath('BcThemeSample', '', 'layout', $path);
    }

    public static function provideGetFullpathPathTraversal(): array
    {
        return [
            'webrootへの traversal' => ['../../../../webroot/shell.php'],
            'configへの traversal' => ['../../../../config/app.php'],
            '相対パスのみ' => ['../../../evil.php'],
        ];
    }

    /**
     * test getFullpath - $type のトラバーサルを拒否する（GHSA-2pj4-v76f-wjvx）
     *
     * $type が許可リスト外（トラバーサルを含む等）の場合、ベースディレクトリの移動を
     * 許さず BcException で拒否すること。
     *
     * @dataProvider provideGetFullpathInvalidType
     */
    public function test_getFullpath_invalidType(string $type): void
    {
        $this->expectException('BaserCore\Error\BcException');
        $this->expectExceptionMessage('テンプレートタイプが不正です。');
        $this->BcThemeFileService->getFullpath('BcThemeSample', '', $type, 'pwned.php');
    }

    public static function provideGetFullpathInvalidType(): array
    {
        return [
            // 実在ディレクトリへ移動する traversal（realpath が解決し旧チェックを回避していた本丸）
            'tmpへ移動するtraversal' => ['../../../../../../../../tmp'],
            'webrootへ移動するtraversal' => ['../../../../webroot'],
            '許可外の任意文字列' => ['invalid'],
            '空文字' => [''],
        ];
    }

    /**
     * test getFullpath - 正規の $type は全て通過する（回帰防止）
     * @dataProvider provideValidType
     */
    public function test_getFullpath_validType(string $type): void
    {
        $path = $this->BcThemeFileService->getFullpath('BcThemeSample', '', $type, 'sample');
        $this->assertStringStartsWith('/var/www/html/plugins/', $path);
    }

    public static function provideValidType(): array
    {
        return [['layout'], ['element'], ['email'], ['etc'], ['css'], ['js'], ['img']];
    }
}
