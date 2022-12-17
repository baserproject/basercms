<?php
// TODO : コード確認要
use BaserCore\Utility\BcUtil;

return;
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case
 * @since           baserCMS v 3.0.0-beta
 * @license         https://basercms.net/license/index.html
 */

/**
 * test for basics.php
 *
 * @package Baser.Test.Case
 */
class BcBasicsTest extends BcTestCase
{

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'baser.Default.BlogContent',
        'baser.Default.Page',
        'baser.Default.Plugin',
        'baser.Default.SiteConfig',
        'baser.Default.Site',
        'baser.Default.Content',
    ];

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * リビジョンを取得する
     */
    public function testRevision()
    {
        $version = 'baserCMS 3.0.6.1 beta';
        $result = revision($version);
        $this->assertEquals('1', $result, '正しくリビジョンを取得できません');
    }

    /**
     * バージョンを特定する一意の数値を取得する
     */
    public function testVerpoint()
    {
        $version = 'baserCMS 3.0.6.1';
        $result = verpoint($version);
        $this->assertEquals(3000006001, $result, '正しくバージョンを特定する一意の数値を取得できません');

        $version = 'baserCMS 3.0.6.1 beta';
        $result = verpoint($version);
        $this->assertEquals(false, $result, '正しくバージョンを特定する一意の数値を取得できません');
    }

    /**
     * 拡張子を取得する
     *
     * @param string $content mimeタイプ
     * @param string $fileName ファイル名
     * @param string $expect 期待値
     * @dataProvider decodeContentDataProvider
     */
    public function testDecodeContent($content, $fileName, $expect)
    {
        $result = decodeContent($content, $fileName);
        $this->assertEquals($expect, $result, '正しく拡張子を取得できません');
    }

    public function decodeContentDataProvider()
    {
        return [
            ['image/jpeg', null, 'jpg'],
            [null, 'hoge.png', 'png'],
            [null, 'hoge', false],
            [null, null, false],
        ];
    }

    /**
     * 環境変数よりURLパラメータを取得する
     *
     * @param string $agentAlias BcRequest.agentAliasの値
     * @param string $url URL
     * @param string $expect 期待値
     * @param string $message テスト失敗時に表示するメッセージ
     * @dataProvider getUrlParamFromEnvDataProvider
     */
    public function testGetUrlParamFromEnv($agentAlias, $url, $expect, $message = null)
    {
        Configure::write('BcRequest.agentAlias', $agentAlias);
        $_GET['url'] = $url;
        $result = getUrlParamFromEnv();
        $this->assertEquals($expect, $result, $message);
    }

    public function getUrlParamFromEnvDataProvider()
    {
        return [
            [null, '/s/test/', 's/test/', 'URLパラメータのモバイルプレフィックスを正しく除外できません']
        ];
    }

    /**
     * 環境変数よりURLを取得する
     *
     * @param string $url $_GET['url']の値
     * @param string $request $_SERVER['REQUEST_URI']の値
     * @param string $baseUrl App.BaseUrlの値
     * @param string $expect 期待値
     * @param string $message テスト失敗時に表示するメッセージ
     * @dataProvider getUrlFromEnvDataProvider
     */
    public function testGetUrlFromEnv($get, $request, $baseUrl, $expect, $message = null)
    {
        // 初期化
        $_GET['url'] = $get;
        $_SERVER['REQUEST_URI'] = $request;
        Configure::write('App.baseUrl', $baseUrl);

        $result = getUrlFromEnv();
        $this->assertEquals($expect, $result, $message);
    }

    public function getUrlFromEnvDataProvider()
    {
        return [
            ['/get/', null, null, 'get/', '$_GET["url"]からURLを正しく取得できません'],
            ['/get/url/test', null, null, 'get/url/test', '$_GET["url"]からURLを正しく取得できません'],
            [null, '/req/', null, 'req/', '$_SERVER["REQUEST_URI"]からURLを正しく取得できません'],
            [null, '/req/test.php?a=aaa&b=bbb', null, 'req/test.php', '$_SERVER["REQUEST_URI"]からURLを正しく取得できません'],
            [null, BcUtil::baseUrl() . '/req/', null, 'req/', '$_SERVER["REQUEST_URI"]からURLを正しく取得できません'],
            [null, '/base/req/', '/base/', 'req/', '$_SERVER["REQUEST_URI"]からURLを正しく取得できません'],
            [null, '/base/req/', '/base/url/', 'req/', '$_SERVER["REQUEST_URI"]からURLを正しく取得できません'],
        ];
    }

    /**
     * モバイルプレフィックスは除外したURLを取得する
     */
    public function testGetPureUrl()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Viewキャッシュを削除する
     * TODO basics.php 295行目 $homesにバグ？あり
     *            app/tmp/cache/views/のキャッシュファイルを複数回削除している
     *
     * @param string $url
     * @param string $ext
     * @dataProvider clearViewCacheDataProvider
     */
    public function testClearViewCache($url, $ext)
    {
        $viewCachePath = CACHE . 'views' . DS;
        if ($url == '/' || $url == '/index' || $url == '/index.html' || $url == '/m/' || $url == '/m/index' || $url == '/m/index.html') {
            $cache = new File($viewCachePath . DS . strtolower(Inflector::slug($url)) . $ext, true);
            // 削除実行
            clearViewCache($url, $ext);

        } elseif ($url) {
            // ダミーのキャッシュファイルを生成
            $cache = new File($viewCachePath . DS . strtolower(Inflector::slug($url)) . $ext, true);
            $cacheHoge = new File($viewCachePath . DS . strtolower(Inflector::slug($url)) . '.hoge', true);
            if (preg_match('/\/index$/', $url)) {
                $replacedUrl = preg_replace('/\/index$/', '', $url);
                $replacedCache = new File($viewCachePath . DS . strtolower(Inflector::slug($replacedUrl)) . $ext, true);
            }
            // 削除実行
            clearViewCache($url, $ext);

            $this->assertTrue($cacheHoge->exists(), '指定されていない拡張子のファイルが削除されてしまいます');
            $cacheHoge->delete();
            $cacheHoge->close();

            if (preg_match('/\/index$/', $url)) {
                $this->assertFalse($replacedCache->exists(), '置換された名前のキャッシュファイルを削除できません');
                $replacedCache->delete();
                $replacedCache->close();
            }

        } else {
            // ダミーのキャッシュファイルを生成
            $cache = new File($viewCachePath . DS . 'cache', true);
            $empty = new File($viewCachePath . DS . 'empty', true);

            // 削除実行
            clearViewCache($url, $ext);

            $this->assertTrue($empty->exists(), 'emptyファイルが削除されてしまいます');
            $empty->delete();
            $empty->close();
        }

        $this->assertFalse($cache->exists(), 'キャッシュを削除できません');
        $cache->delete();
        $cache->close();

    }

    public function clearViewCacheDataProvider()
    {
        return [
            [null, null],
            ['/test/', '.ext'],
            ['/test/index', '.ext'],
            ['/index.html', '.php'],
            ['/m/index.html', '.php'],
        ];
    }

    /**
     * データキャッシュを削除する
     */
    public function testClearDataCache()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * キャッシュファイルを全て削除する
     */
    public function testClearAllCache()
    {
        // ダミーのキャッシュファイルを生成
        $coreConf = Cache::config('_cake_core_');
        $coreConf = $coreConf['settings'];
        $modelConf = Cache::config('_cake_model_');
        $modelConf = $modelConf['settings'];
        $envConf = Cache::config('_bc_env_');
        $envConf = $envConf['settings'];

        $coreCache = new File($coreConf['path'] . $coreConf['prefix'] . 'cache', true);
        $modelCache = new File($modelConf['path'] . $modelConf['prefix'] . 'cache', true);
        $envCache = new File($envConf['path'] . $envConf['prefix'] . 'cache', true);
        $viewCache = new File(CACHE . 'views' . DS . 'cache', true);
        $dataCache = new File(CACHE . 'datas' . DS . 'cache', true);

        // キャッシュ削除
        BcUtil::clearAllCache();

        $this->assertFalse($coreCache->exists());
        $this->assertFalse($modelCache->exists());
        $this->assertFalse($envCache->exists());
        $this->assertFalse($viewCache->exists());
        $this->assertFalse($dataCache->exists());

        $coreCache->close();
        $modelCache->close();
        $envCache->close();
        $viewCache->close();
        $dataCache->close();
    }

    /**
     * baserCMSのインストールが完了しているかチェックする
     */
    public function testIsInstalled()
    {
        $installedPath = APP . 'Config' . DS . 'install.php';

        // app/Config/installed.phpが存在しない場合
        if (rename($installedPath, $installedPath . '_copy')) {
            $result = isInstalled();
            $this->assertFalse($result, 'app/Config/installed.phpが存在していない場合にtrueが返ってきます');
        } else {
            $this->markTestIncomplete('app/Config/installed.phpのファイル名変更に失敗したのでテストをスキップしました。');
        }

        // app/Config/installed.phpが存在する場合
        if (rename($installedPath . '_copy', $installedPath)) {
            $result = isInstalled();
            $this->assertTrue($result, 'app/Config/installed.phpが存在している場合にfalseが返ってきます');
        } else {
            $this->markTestIncomplete('app/Config/installed.phpのファイル名変更に失敗したのでテストをスキップしました。');
        }

    }

    /**
     * DBセッティングが存在するかチェックする
     */
    public function testGetDbConfig()
    {
        $dbconfigPath = APP . 'Config' . DS . 'database.php';

        // app/Config/database.phpが存在しない場合
        if (rename($dbconfigPath, $dbconfigPath . '_copy')) {
            $result = getDbConfig();
            $this->assertFalse($result, 'app/Config/database.php が存在していない場合にtrueが返ってきます');
        } else {
            $this->markTestIncomplete('app/Config/database.php のファイル名変更に失敗したのでテストをスキップしました。');
        }

        // app/Config/database.phpが存在する場合
        if (rename($dbconfigPath . '_copy', $dbconfigPath)) {
            $result = getDbConfig();
            $this->assertContains('utf8', $result, 'app/Config/database.php が存在している場合にデータベースの情報が返ってきません');

            $result = getDbConfig('hoge');
            $this->assertFalse($result, '存在しないデータベースの設定名を入力した場合にfalseが返ってきます');

        } else {
            $this->markTestIncomplete('app/Config/database.php のファイル名変更に失敗したのでテストをスキップしました。');
        }

    }

    /**
     * 現在のビューディレクトリのパスを取得する
     */
    public function testGetViewPath()
    {
        // テーマが設定されている場合
        Configure::write('BcSite.theme', 'hoge');
        $result = getViewPath();
        $expect = WWW_ROOT . 'theme' . DS . 'hoge' . DS;
        $this->assertEquals($expect, $result, '取得した現在のビューディレクトリのパスが正しくありません');

        // テーマが未設定の場合
        Configure::write('BcSite.theme', null);
        $result = getViewPath();
        $expect = APP . 'View' . DS;
        $this->assertEquals($expect, $result, '取得した現在のビューディレクトリのパスが正しくありません');
    }

    /**
     * 配列を再帰的に上書きする
     */
    public function testAmr()
    {
        $a = ['a1', 'a2', 'a3'];
        $b = ['b1', 'b2'];

        // 1次元配列
        $this->assertEquals(['b1', 'b2', 'a3'], amr($a, $b));

        // 2次元配列
        $b = [['b1']];
        $this->assertEquals([['b1'], 'a2', 'a3'], amr($a, $b));

        // 3次元配列
        $a = [['a1'], 'a2', 'a3'];
        $b = [[['b1']]];
        $this->assertEquals([[['b1']], 'a2', 'a3'], amr($a, $b));
    }

    /**
     * URLにセッションIDを付加する
     */
    public function testAddSessionId()
    {
        // 初期化
        $sessionId = session_id();
        $sessionName = session_name();
        $_SERVER['REQUEST_URI'] = '/m/';
        $message = 'URLにセッションIDを正しく付加できません';
        $this->assertEquals('/?' . $sessionName . '=' . $sessionId, addSessionId('/', true), $message);
        $this->assertEquals('/?id=1&' . $sessionName . '=' . $sessionId, addSessionId('/?id=1', true), $message);
        $this->assertEquals('/?id=1&' . $sessionName . '=' . $sessionId, addSessionId('/?id=1&BASERCMS=1', true), $message);

        // urlが配列の場合
        $url = [
            0 => '/',
            '?' => [
                'id' => 1,
                'BASERCMS' => 1
            ]
        ];
        $expect = [
            0 => '/',
            '?' => [
                'id' => 1,
                $sessionName => $sessionId
            ]
        ];
        $this->assertEquals($expect, addSessionId($url, true), $message);
    }

    /**
     * 利用可能なプラグインのリストを取得する
     */
    public function testGetEnablePlugins()
    {
        $result = getEnablePlugins();
        $pluginNames = [
            $result[0]['Plugin']['name'],
            $result[1]['Plugin']['name']
        ];
        $expect = ['BcBlog', 'BcFeed', 'BcMail'];
        $this->assertEquals($expect, $pluginNames, '利用可能なプラグインのリストを正しく取得できません');
    }

    /**
     * アップデートのURLを記載したメールを送信する
     */
    public function testSendUpdateMail()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 展開出力
     */
    public function testP()
    {
        ob_start();
        p(['test']);
        $result = ob_get_clean();
        $expect = 'array.*int.*0.*=&gt; &#039;test&#039;';
        $this->assertMatchesRegularExpression('/' . $expect . '/s', $result);
    }

    /**
     * データベースのドライバー名を取得する
     */
    public function testGetDbDriver()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Constructs associative array from pairs of arguments.
     */
    public function testAa()
    {
        $result = aa('a', 'b', 'c');
        $expect = ['a' => 'b', 'c' => null];
        $this->assertEquals($expect, $result);
    }

    /**
     * 後方互換のための非推奨メッセージを生成する
     */
    public function testDeprecatedMessage()
    {
        $result = deprecatedMessage('target', 'since', 'remove', 'note');
        $expect = 'target は、バージョン since より非推奨となりました。バージョン remove で削除される予定です。note';
        $this->assertEquals($expect, $result);

        // デバックモードではない場合
        Configure::write('debug', 0);
        $result = deprecatedMessage('target', 'since', 'remove', 'note');
        $this->assertNull($result);
    }

    /**
     * パーセントエンコーディングされないURLセーフなbase64エンコード
     */
    public function testBase64UrlsafeEncodeDecode()
    {
        // encode
        $text = 'ふぁsdlfdfがgふぁsdlpfs'; // base64エンコードすると + と = が含まれる文字列
        $enc = base64UrlsafeEncode($text);
        $result = urlencode($enc);
        // %が含まれてないかチェック
        $this->assertMatchesRegularExpression('/^(?!.*%)/', $result, 'パーセントエンコーディングされています');

        // decode
        $dec = base64UrlsafeDecode($enc);
        $this->assertEquals($dec, $text, '正しくデコードできません');
    }

    /**
     * 実行環境のOSがWindowsであるかどうかを返す
     */
    public function testIsWindows()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }


    /**
     * 時刻の有効性チェックを行う
     */
    public function testChecktime()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 関連するテーブルリストを取得する
     */
    public function testGetTableList()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
