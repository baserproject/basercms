<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Utility;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;

class BcUtilTest extends BcTestCase
{
    /**
     * Test loginUser
     */
    public function testLoginUser ()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isSuperUser
     */
    public function testIsSuperUser ()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isAgentUser
     */
    public function testIsAgentUser ()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isInstallMode
     */
    public function testIsInstallMode ()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getVersion
     */
    public function testGetVersion ()
    {
        // BaserCore
        $file = new \Cake\Filesystem\File(BASER . DS . 'VERSION.txt');
        $expected = preg_replace('/(.+?)\n/', "$1", $file->read());
        $result = BcUtil::getVersion();
        $this->assertEquals($expected, $result);

        // プラグイン
        $file = new \Cake\Filesystem\File(\Cake\Core\Plugin::path('bc-admin-third') . DS . 'VERSION.txt');
        $expected = preg_replace('/(.+?)\n/', "$1", $file->read());
        $result = BcUtil::getVersion('BcAdminThird');
        $this->assertEquals($expected, $result);
    }

}
