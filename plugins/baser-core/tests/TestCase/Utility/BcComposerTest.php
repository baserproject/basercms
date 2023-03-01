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
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

/**
 * BcComposer Test
 */
class BcComposerTest extends BcTestCase
{

    /**
     * installComposer
     */
    public function test_installComposer()
    {
        unlink(ROOT . DS . 'composer' . DS . 'composer.phar');
        BcComposer::installComposer();
        $this->assertFileDoesNotExist(BcComposer::$composerDir . 'composer.phar');

        BcComposer::$composerDir = ROOT . DS . 'composer' . DS;
        BcComposer::$export = "export HOME=" . BcComposer::$composerDir . ";";
        BcComposer::$php = '/usr/local/bin/php';
        $result = BcComposer::installComposer();
        $this->assertEquals(0, $result['code']);
        $this->assertFileExists(BcComposer::$composerDir . 'composer.phar');
    }

    /**
     * test setVersion
     */
    public function test_require()
    {
        $orgPath = ROOT . DS . 'composer.json';
        $backupPath = ROOT . DS . 'composer.json.bak';
        $orgLockPath = ROOT . DS . 'composer.lock';
        $backupLockPath = ROOT . DS . 'composer.lock.bak';

        // バックアップ作成
        copy($orgPath, $backupPath);
        copy($orgLockPath, $backupLockPath);

        // replace を削除
        $file = new File($orgPath);
        $data = $file->read();
        $regex = '/("replace": {.+?},)/s';
        $data = preg_replace($regex, '' , $data);
        $file->write($data);
        $file->close();

        // インストール
        BcComposer::setup();
        $result = BcComposer::require('baser-core', '3.0.10');
        $this->assertEquals(0, $result['code']);
        $file = new File($orgPath);
        $data = $file->read();
        $this->assertNotFalse(strpos($data, '"baserproject/baser-core": "3.0.10"'));

        // アップデート
        BcComposer::setup();
        $result = BcComposer::require('baser-core', '3.0.24');
        $this->assertEquals(0, $result['code']);
        $file = new File($orgPath);
        $data = $file->read();
        $this->assertNotFalse(strpos($data, '"baserproject/baser-core": "3.0.24"'));

        // ダウングレード
        BcComposer::setup();
        $result = BcComposer::require('baser-core', '3.0.10');
        $this->assertEquals(0, $result['code']);
        $file = new File($orgPath);
        $data = $file->read();
        $this->assertNotFalse(strpos($data, '"baserproject/baser-core": "3.0.10"'));

        // エラー
        $result = BcComposer::require('bc-content-link', '3.0.24');
        $this->assertEquals(2, $result['code']);

        // バックアップ復元
        rename($backupPath, $orgPath);
        rename($backupLockPath, $orgLockPath);
        $folder = new Folder();
        $folder->delete(ROOT . DS . 'vendor' . DS . 'baserproject');
    }

}
