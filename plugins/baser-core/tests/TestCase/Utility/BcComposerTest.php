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
        $this->assertEquals('cd /var/www/html/tmp/update/;', BcComposer::$cd);
        $this->assertEquals('/usr/local/bin/php', BcComposer::$php);
    }

    /**
     * test checkEnv
     */
    public function testCheckEnv()
    {
        $this->assertNull(BcComposer::checkEnv());
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
     * test update
     */
    public function testUpdate()
    {
        $orgPath = ROOT . DS . 'composer.json';
        $backupPath = ROOT . DS . 'composer.json.bak';
        $orgLockPath = ROOT . DS . 'composer.lock';
        $backupLockPath = ROOT . DS . 'composer.lock.bak';

        // バックアップ作成
        copy($orgPath, $backupPath);
        copy($orgLockPath, $backupLockPath);

        // replace を削除
        // baserCMS5.0.0が、CakePHP5.0.10 に依存するため、一旦、CakePHP5.0.10 に戻す
        $file = new BcFile($orgPath);
        $data = $file->read();
        $regex = '/("replace": {.+?},)/s';
        $data = str_replace('"cakephp/cakephp": "5.0.*"', '"cakephp/cakephp": "5.0.10"', $data);
        $data = preg_replace($regex, '', $data);
        $file->write($data);
        BcComposer::setup('php');

        $rs = BcComposer::update();
        //戻り値を確認
        $this->assertEquals(0, $rs['code']);
        $this->assertEquals('A script named install would override a Composer command and has been skipped', $rs['out'][0]);

        // バックアップ復元
        rename($backupPath, $orgPath);
        rename($backupLockPath, $orgLockPath);
        $folder = new BcFolder(ROOT . DS . 'vendor' . DS . 'baserproject');
        $folder->delete();
        BcComposer::install();
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
     * test install
     */
    public function testInstall()
    {
        BcComposer::setup('php');

        $rs = BcComposer::install();
        //戻り値を確認
        $this->assertEquals(0, $rs['code']);
        $this->assertEquals('A script named install would override a Composer command and has been skipped', $rs['out'][0]);
    }

    /**
     * test selfUpdate
     */
    public function testSelfUpdate()
    {
        BcComposer::setup();
        $rs = BcComposer::selfUpdate();

        $this->assertEquals(0, $rs['code']);
        $this->assertEquals("A script named install would override a Composer command and has been skipped", $rs['out'][0]);
        $this->assertStringContainsString("You are already using the latest available Composer version", $rs['out'][1]);
    }

    /**
     * test setupComposerForDistribution
     */
    public function testSetupComposerForDistribution()
    {
        // composer.json をバックアップ
        $srcComposerJsonPath = __DIR__ . DS . 'assets' . DS . 'composer-5.1.1.json';
        $srcComposerLockPath = __DIR__ . DS . 'assets' . DS . 'composer-5.1.1.lock';
        $composerJson = TMP_TESTS . 'composer.json';
        $composerLock = TMP_TESTS . 'composer.lock';
        copy($srcComposerJsonPath, $composerJson);
        copy($srcComposerLockPath, $composerLock);

        // 実行
        BcComposer::setup('', TMP_TESTS);
        BcComposer::setupComposerForDistribution('5.1.1');
        $file = new BcFile($composerJson);
        $data = $file->read();
        $this->assertNotFalse(strpos($data, '"baserproject/baser-core": '));
        $this->assertFalse(strpos($data, '"replace": {'));
        $file = new BcFile($composerLock);
        $data = $file->read();
        $this->assertNotFalse(strpos($data, '"baserproject/baser-core"'));

        // バックアップをリストア
        unlink($composerJson);
        unlink($composerLock);
        (new BcFolder(TMP_TESTS . 'vendor'))->delete();
    }

    /**
     * test createCommand
     * @param $inputCommand
     * @param $expectedCommand
     * @dataProvider createCommandDataProvider
     */
    public function testCreateCommand($inputCommand, $expectedCommand)
    {
        BcComposer::$cd = 'cd /var/www/html/;';
        BcComposer::$export = 'export HOME=/var/www/html/composer/;';
        BcComposer::$php = 'php';
        BcComposer::$composerDir = '/var/www/html/composer/';

        $result = BcComposer::createCommand($inputCommand);
        $this->assertEquals($expectedCommand, $result);
    }

    public static function createCommandDataProvider()
    {
        return [
            [
                'self-update',
                "cd /var/www/html/; export HOME=/var/www/html/composer/; echo y | php /var/www/html/composer/composer.phar self-update 2>&1"
            ],
            [
                'install',
                "cd /var/www/html/; export HOME=/var/www/html/composer/; echo y | php /var/www/html/composer/composer.phar install 2>&1"
            ],
            [
                'require vendor/package',
                "cd /var/www/html/; export HOME=/var/www/html/composer/; echo y | php /var/www/html/composer/composer.phar require vendor/package 2>&1"
            ],
        ];
    }

    /**
     * test deleteReplace
     * @return void
     */
    public function testDeleteReplace()
    {
        $orgPath = ROOT . DS . 'composer.json';
        $backupPath = ROOT . DS . 'composer.json.bak';

        // バックアップ作成
        copy($orgPath, $backupPath);
        BcComposer::setup();
        BcComposer::deleteReplace();
        $file = new BcFile($orgPath);
        $data = $file->read();
        $this->assertFalse(strpos($data, '"replace": {'));

        // バックアップ復元
        rename($backupPath, $orgPath);
    }

    /**
     * test execCommand
     */
    public function testExecCommand()
    {
        BcComposer::setup();
        $rs = BcComposer::execCommand('update --with-all-dependencies --ignore-platform-req=ext-xdebug');

        $this->assertEquals(0, $rs['code']);
        $this->assertEquals("A script named install would override a Composer command and has been skipped", $rs['out'][0]);
        $this->assertStringContainsString("Loading composer repositories with package information", $rs['out'][1]);
    }
}
