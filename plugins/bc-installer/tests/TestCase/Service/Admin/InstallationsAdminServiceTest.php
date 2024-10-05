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

namespace BcInstaller\Test\TestCase\Service\Admin;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcInstaller\Service\Admin\InstallationsAdminService;
use BcInstaller\Service\Admin\InstallationsAdminServiceInterface;
use Cake\Http\ServerRequest;
use Cake\Http\Session;

/**
 * InstallationsAdminServiceTest
 * @property InstallationsAdminService $Installations
 */
class InstallationsAdminServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Installations = $this->getService(InstallationsAdminServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->Installations);
    }

    /**
     * test getViewVarsForStep2
     */
    public function test_getViewVarsForStep2()
    {
        $this->assertNotEmpty($this->Installations->getViewVarsForStep2());
    }

    /**
     * test getViewVarsForStep3
     */
    public function test_getViewVarsForStep3()
    {
        $vars = $this->Installations->getViewVarsForStep3(true);
        $this->assertArrayHasKey('dbsource', $vars);
        $this->assertTrue($vars['blDBSettingsOK']);
        $this->assertArrayHasKey('dbDataPatterns', $vars);
    }

    /**
     * test getDefaultValuesStep3
     */
    public function test_getDefaultValuesStep3()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getDefaultValuesStep4
     * @param array $sessionData
     * @param array $expected
     * @dataProvider getDefaultValuesStep4DataProvider
     */
    public function test_getDefaultValuesStep4($sessionData, $expected)
    {
        $session = new Session();
        if (!empty($sessionData)) {
            foreach ($sessionData as $key => $value) {
                $session->write($key, $value);
            }
        }
        $request = new ServerRequest(['session' => $session]);

        $result = $this->Installations->getDefaultValuesStep4($request);
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for getDefaultValuesStep4
     *
     */
    public static function getDefaultValuesStep4DataProvider()
    {
        return [
             [
                [],
                [
                    'site_name' => 'My Site',
                    'admin_username' => '',
                    'admin_password' => '',
                    'admin_confirm_password' => '',
                    'admin_email' => ''
                ]
            ],
            [
                [
                    'Installation' => [
                        'site_name' => 'Custom Site',
                        'admin_username' => 'admin'
                    ]
                ],
                [
                    'site_name' => 'Custom Site',
                    'admin_username' => 'admin',
                    'admin_password' => '',
                    'admin_confirm_password' => '',
                    'admin_email' => ''
                ]
            ], [
                [
                    'Installation' => [
                        'site_name' => 'Custom Site',
                        'admin_username' => 'admin',
                        'admin_password' => '123456',
                        'admin_email' => 'admin@example.com'
                    ]
                ],
                [
                    'site_name' => 'Custom Site',
                    'admin_username' => 'admin',
                    'admin_password' => '123456',
                    'admin_confirm_password' => '123456',
                    'admin_email' => 'admin@example.com'
                ]
            ]
        ];
    }

    /**
     * test writeDbSettingToSession
     */
    public function test_writeDbSettingToSession()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test readDbSetting
     */
    public function test_readDbSetting()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test deleteAllTables
     */
    public function test_deleteAllTables()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test initAdmin
     */
    public function test_initAdmin()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test initFiles
     */
    public function test_initFiles()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test connectDb
     */
    public function test_connectDb()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test login
     */
    public function test_login()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test initDb
     */
    public function test_initDb()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
