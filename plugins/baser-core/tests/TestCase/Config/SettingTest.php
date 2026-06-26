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

}
