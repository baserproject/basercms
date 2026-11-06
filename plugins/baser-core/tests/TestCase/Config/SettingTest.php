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

namespace BaserCore\Test\TestCase\Config;

use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;

/**
 * Class SettingTest
 *
 * config/setting.php の重要設定の回帰テスト
 */
class SettingTest extends BcTestCase
{

    /**
     * セッションCookieに CSRF 緩和のための SameSite/HttpOnly が設定されていること
     *
     * @return void
     */
    public function testSessionCookieSecurityIni(): void
    {
        $ini = Configure::read('Session.ini');
        $this->assertEquals('Lax', $ini['session.cookie_samesite'] ?? null);
        $this->assertEquals(1, $ini['session.cookie_httponly'] ?? null);
    }

    /**
     * BASER_CORE_PREFIX が空の場合は例外を出すこと
     *
     * @return void
     */
    public function testEmptyBaserCorePrefix(): void
    {
        $settingPath = ROOT . DS . 'plugins' . DS . 'baser-core' . DS . 'config' . DS . 'setting.php';
        // .env で空の値を設定した場合と同じく、$_SERVER と環境変数の両方を空にする
        $serverBackup = $_SERVER['BASER_CORE_PREFIX'] ?? null;
        $envBackup = getenv('BASER_CORE_PREFIX');
        $_SERVER['BASER_CORE_PREFIX'] = '';
        putenv('BASER_CORE_PREFIX=');
        try {
            $this->expectException(\Cake\Core\Exception\CakeException::class);
            $this->expectExceptionMessage('BASER_CORE_PREFIX に空の値は設定できません');
            require $settingPath;
        } finally {
            if (is_null($serverBackup)) {
                unset($_SERVER['BASER_CORE_PREFIX']);
            } else {
                $_SERVER['BASER_CORE_PREFIX'] = $serverBackup;
            }
            putenv($envBackup === false ? 'BASER_CORE_PREFIX' : 'BASER_CORE_PREFIX=' . $envBackup);
        }
    }

}
