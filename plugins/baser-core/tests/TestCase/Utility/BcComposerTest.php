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

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcComposer;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;

/**
 * BcComposer Test
 */
class BcComposerTest extends BcTestCase
{

    /**
     * tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        if(file_exists(ROOT . DS . 'composer.json.bak')) {
            rename(ROOT . DS . 'composer.json.bak', ROOT . DS . 'composer.json');
        }
        if(file_exists(ROOT . DS . 'composer.lock.bak')) {
            rename(ROOT . DS . 'composer.lock.bak', ROOT . DS . 'composer.lock');
        }
    }

    /**
     * test setup
     */
    public function testSetup()
    {
        BcComposer::setup();
        $this->assertEquals('cd /var/www/html/;', BcComposer::$cd);
        $this->assertEquals('/var/www/html/composer/', BcComposer::$composerDir);
        $this->assertEquals('export HOME=/var/www/html/composer/;', BcComposer::$export);
        $this->assertEquals('php', BcComposer::$php);

        // 環境を変更
        BcComposer::setup('/usr/local/bin/php', '/var/www/html/tmp/update');
        $this->assertEquals('cd /var/www/html/tmp/update;', BcComposer::$cd);
        $this->assertEquals('/usr/local/bin/php', BcComposer::$php);
    }

    /**
     * installComposer
     */
    public function test_installComposer()
    {
        if(file_exists(ROOT . DS . 'composer' . DS . 'composer.phar')) {
            unlink(ROOT . DS . 'composer' . DS . 'composer.phar');
        }

        BcComposer::$composerDir = '';
        BcComposer::$export = '';
        BcComposer::installComposer();
        $this->assertFileDoesNotExist(BcComposer::$composerDir . 'composer.phar');

        BcComposer::setup();
        $result = BcComposer::installComposer();
        $this->assertEquals(0, $result['code']);
        $this->assertFileExists(BcComposer::$composerDir . 'composer.phar');
    }

    /**
     * test checkComposer
     */
    public function testCheckComposer()
    {
        BcComposer::$composerDir = '';

        BcComposer::setup();
        BcComposer::checkComposer();
        //実行問題なし場合、composer.pharが生成された
        $this->assertFileExists(BcComposer::$composerDir . 'composer.phar');
    }

    /**
     * test checkComposer エラーを発生した場合
     */
    public function testCheckComposerError()
    {
        BcComposer::$composerDir = '';

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('composer がインストールできません。All settings correct for using Composer');
        BcComposer::checkComposer();
    }

    /**
     * test setVersion
     */
    public function test_require()
    {
        $this->markTestIncomplete('こちらのテストはまだ未確認です');
        $orgPath = ROOT . DS . 'composer.json';
        $backupPath = ROOT . DS . 'composer.json.bak';
        $orgLockPath = ROOT . DS . 'composer.lock';
        $backupLockPath = ROOT . DS . 'composer.lock.bak';

        // バックアップ作成
        copy($orgPath, $backupPath);
        copy($orgLockPath, $backupLockPath);

        // replace を削除
        // baserCMS5.0.0が、CakePHP4.4.* に依存するため、一旦、CakePHP4.4.* に戻す
        $file = new BcFile($orgPath);
        $data = $file->read();
        $regex = '/("replace": {.+?},)/s';
        $data = str_replace('"cakephp/cakephp": "4.5.*"', '"cakephp/cakephp": "4.4.*"' , $data);
        $data = preg_replace($regex, '' , $data);
        $file->write($data);
        BcComposer::setup('php');
        BcComposer::update();

        // インストール
        BcComposer::setup();
        $result = BcComposer::require('baser-core', '5.0.0');
        $this->assertEquals(0, $result['code']);
        $file = new BcFile($orgPath);
        $data = $file->read();
        $this->assertNotFalse(strpos($data, '"baserproject/baser-core": "5.0.0"'));

        // アップデート
        BcComposer::setup();
        $result = BcComposer::require('baser-core', '5.0.1');
        $this->assertEquals(0, $result['code']);
        $file = new BcFile($orgPath);
        $data = $file->read();
        $this->assertNotFalse(strpos($data, '"baserproject/baser-core": "5.0.1"'));

        // ダウングレード
        BcComposer::setup();
        $result = BcComposer::require('baser-core', '5.0.0');
        $this->assertEquals(0, $result['code']);
        $file = new BcFile($orgPath);
        $data = $file->read();
        $this->assertNotFalse(strpos($data, '"baserproject/baser-core": "5.0.0"'));

        // エラー
        $result = BcComposer::require('bc-content-link', '100.0.0');
        $this->assertEquals(2, $result['code']);

        // バックアップ復元
        rename($backupPath, $orgPath);
        rename($backupLockPath, $orgLockPath);
        $folder = new BcFolder(ROOT . DS . 'vendor' . DS . 'baserproject');
        $folder->delete();
        BcComposer::update();
    }

    /**
     * test clearCache
     */
    public function testClearCache()
    {
        // キャッシュを作成
        BcComposer::setup();
        BcComposer::selfUpdate();
        $this->assertFileExists(ROOT . DS . 'composer' . DS . '.composer' . DS . 'cache' . DS . '.htaccess');
        BcComposer::clearCache();
        $this->assertFileDoesNotExist(ROOT . DS . 'composer' . DS . '.composer' . DS . 'cache' . DS . '.htaccess');
    }

    /**
     * test setupComposerForDistribution
     */
    public function testSetupComposerForDistribution()
    {
        // composer.json をバックアップ
        $composer = ROOT . DS . 'composer.json';
        copy($composer, ROOT . DS . 'composer.json.bak');

        // 実行
        BcComposer::setupComposerForDistribution(ROOT . DS);
        $file = new BcFile($composer);
        $data = $file->read();
        $this->assertNotFalse(strpos($data, '"baserproject/baser-core": '));
        $this->assertFalse(strpos($data, '"replace": {'));

        // バックアップをリストア
        rename(ROOT . DS . 'composer.json.bak', ROOT . DS . 'composer.json');
    }

}
