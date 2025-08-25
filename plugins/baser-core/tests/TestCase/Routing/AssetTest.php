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
     * webrootメソッドのカスタマイズ部分のテスト
     *
     * 1. フロントテーマ（BcThemeSample）が最優先
     * 2. フロントテーマ削除→ドキュメントルート直下が優先
     * 3. ドキュメントルート削除→フロントデフォルトテーマが優先
     * 4. フロントデフォルトテーマ削除→管理画面テーマが優先
     */
    public function testWebrootCustomize()
    {
        \Cake\Core\Configure::write('BcApp.coreFrontTheme', 'BcFront');
        \Cake\Core\Configure::write('BcApp.coreAdminTheme', 'BcAdminThird');

        $file = 'css/priority.css';
        $wwwRoot = ROOT . DS . 'webroot' . DS;
        $frontThemeDir = $wwwRoot . 'bc_theme_sample' . DS . 'css' . DS;
        $frontDefaultThemeDir = $wwwRoot . 'bc_front' . DS . 'css' . DS;
        $adminThemeDir = $wwwRoot . 'bc_admin_third' . DS . 'css' . DS;
        $docRootDir = $wwwRoot . 'css' . DS;

        // すべての場所にファイルを作成
        @mkdir($frontThemeDir, 0777, true);
        @mkdir($frontDefaultThemeDir, 0777, true);
        @mkdir($adminThemeDir, 0777, true);
        @mkdir($docRootDir, 0777, true);
        file_put_contents($frontThemeDir . 'priority.css', 'body{color:red;}');
        file_put_contents($frontDefaultThemeDir . 'priority.css', 'body{color:blue;}');
        file_put_contents($adminThemeDir . 'priority.css', 'body{color:green;}');
        file_put_contents($docRootDir . 'priority.css', 'body{color:yellow;}');

        // 1. フロントテーマ（BcThemeSample）が最優先
        $result = \Cake\Routing\Asset::webroot($file, ['theme' => 'BcThemeSample']);
        $this->assertEquals('/bc_theme_sample/css/priority.css', $result, 'フロントテーマ（BcThemeSample）が最優先');

        // 2. フロントテーマ削除→ドキュメントルート直下が優先
        unlink($frontThemeDir . 'priority.css');
        $result = \Cake\Routing\Asset::webroot($file, ['theme' => 'BcThemeSample']);
        $this->assertEquals('/css/priority.css', $result, 'ドキュメントルート直下が2番目');

        // 3. ドキュメントルート削除→フロントデフォルトテーマが優先
        unlink($docRootDir . 'priority.css');
        $result = \Cake\Routing\Asset::webroot($file, ['theme' => 'BcThemeSample']);
        $this->assertEquals('/bc_front/css/priority.css', $result, 'フロントデフォルトテーマが3番目');

        // 4. フロントデフォルトテーマ削除→管理画面テーマが優先
        unlink($frontDefaultThemeDir . 'priority.css');
        $result = \Cake\Routing\Asset::webroot($file, ['theme' => 'BcThemeSample']);
        $this->assertEquals('/bc_admin_third/css/priority.css', $result, '管理画面デフォルトテーマが4番目');

        // 後始末
        unlink($adminThemeDir . 'priority.css');
    }
}
