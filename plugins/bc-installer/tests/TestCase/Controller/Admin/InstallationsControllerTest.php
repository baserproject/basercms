<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcInstaller\Test\TestCase\Controller\Admin;
use BaserCore\TestSuite\BcTestCase;
use BcInstaller\Controller\Admin\InstallationsController;
use Cake\Event\Event;

/**
 * Class InstallationsControllerTest
 *
 * @property  InstallationsController $InstallationsController
 */
class InstallationsControllerTest extends BcTestCase
{

    /**
    /**
     * setup
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->InstallationsController = new InstallationsController($this->getRequest());
        $event = new Event('Controller.beforeFilter', $this->InstallationsController);
        $this->InstallationsController->beforeFilter($event);
        $this->assertEquals(300, ini_get("max_execution_time"));
    }

    /**
     * Step 1: ウェルカムページ
     */
    public function testIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Step 2: 必須条件チェック
     */
    public function testStep2()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Step 3: データベースの接続設定
     */
    public function testStep3()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Step 4: データベース生成／管理者ユーザー作成
     */
    public function testStep4()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Step 5: 設定ファイルの生成
     * データベース設定ファイル[database.php]
     * インストールファイル[install.php]
     */
    public function testStep5()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * インストール不能警告メッセージを表示
     */
    public function testAlert()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * baserCMSを初期化する
     * debug フラグが -1 の場合のみ実行可能
     */
    public function testReset()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 全てのテーブルを削除する
     */
    public function test_deleteAllTables()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
