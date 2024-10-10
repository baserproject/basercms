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
use Cake\Core\Configure;
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
     * @param array $sessionData
     * @param array $expected
     * @dataProvider defaultValuesStep3DataProvider
     */
    public function test_getDefaultValuesStep3($sessionData, $expected, $defaultFrontTheme = null)
    {
        if ($defaultFrontTheme) {
            Configure::write('BcApp.defaultFrontTheme', $defaultFrontTheme);
        }

        $session = new Session();
        $request = new ServerRequest(['session' => $session]);

        $session->write('Installation', $sessionData);

        $result = $this->Installations->getDefaultValuesStep3($request);

        $this->assertEquals($expected, $result);
    }

    public static function defaultValuesStep3DataProvider()
    {
        return [
            [
                [],
                [
                    'dbType' => 'mysql',
                    'dbHost' => 'localhost',
                    'dbPrefix' => '',
                    'dbPort' => '3306',
                    'dbName' => 'basercms',
                    'dbDataPattern' => 'BcThemeSample.default',
                ],
                'bc-theme-sample'
            ],
            [
                [
                    'dbType' => 'postgres',
                    'dbHost' => 'localhost',
                    'dbPrefix' => 'bc_',
                    'dbPort' => '5432',
                    'dbName' => 'test_database',
                    'dbSchema' => '',
                    'dbEncoding' => 'utf8mb4',
                    'dbDataPattern' => 'BcSample.default',
                    'dbUsername' => 'root',
                    'dbPassword' => 'password'
                ],
                [
                    'dbType' => 'postgres',
                    'dbHost' => 'localhost',
                    'dbPrefix' => 'bc_',
                    'dbPort' => '5432',
                    'dbName' => 'test_database',
                    'dbDataPattern' => 'BcSample.default',
                    'dbUsername' => 'root',
                    'dbPassword' => 'password'
                ],
            ]
        ];
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
     * @param array $data
     * @param array $expected
     * @dataProvider writeDbSettingToSessionDataProvider
     */
    public function test_writeDbSettingToSession($data, $expected)
    {
        $session = new Session();
        $request = new ServerRequest(['session' => $session]);

        $this->Installations->writeDbSettingToSession($request, $data);

        $result = $session->read();
        $this->assertEquals($expected, $result);
    }

    public static function writeDbSettingToSessionDataProvider()
    {
        return [
            [
                [
                    'dbType' => 'mysql',
                    'dbHost' => 'localhost',
                    'dbPort' => '3306',
                    'dbUsername' => 'root',
                    'dbPassword' => 'password',
                    'dbPrefix' => 'bc_',
                    'dbName' => 'test_database_mysql',
                    'dbDataPattern' => 'BcSample.default',
                ],
                [
                    'Installation' => [
                        'dbType' => 'mysql',
                        'dbHost' => 'localhost',
                        'dbPort' => '3306',
                        'dbUsername' => 'root',
                        'dbPassword' => 'password',
                        'dbPrefix' => 'bc_',
                        'dbName' => 'test_database_mysql',
                        'dbSchema' => '',
                        'dbEncoding' => 'utf8mb4',
                        'dbDataPattern' => 'BcSample.default',
                    ]
                ]
            ],
           [
                [
                    'dbType' => 'postgres',
                    'dbHost' => 'localhost',
                    'dbPort' => '5432',
                    'dbUsername' => 'root',
                    'dbPassword' => 'password',
                    'dbPrefix' => 'bc_',
                    'dbName' => 'test_database',
                    'dbDataPattern' => 'BcSample.default',
                ],
                [
                    'Installation' => [
                        'dbType' => 'postgres',
                        'dbHost' => 'localhost',
                        'dbPort' => '5432',
                        'dbUsername' => 'root',
                        'dbPassword' => 'password',
                        'dbPrefix' => 'bc_',
                        'dbName' => 'test_database',
                        'dbSchema' => 'public',
                        'dbEncoding' => 'utf8',
                        'dbDataPattern' => 'BcSample.default'
                    ]
                ]
            ],
            [
                [
                    'dbType' => 'sqlite',
                    'dbHost' => '',
                    'dbPort' => '',
                    'dbUsername' => '',
                    'dbPassword' => '',
                    'dbPrefix' => '',
                    'dbName' => 'test_database_sqlite',
                    'dbDataPattern' => 'BcSample.default',
                ],
                [
                    'Installation' => [
                        'dbType' => 'sqlite',
                        'dbHost' => '',
                        'dbPort' => '',
                        'dbUsername' => '',
                        'dbPassword' => '',
                        'dbPrefix' => '',
                        'dbName' => 'test_database_sqlite',
                        'dbSchema' => '',
                        'dbEncoding' => 'utf8',
                        'dbDataPattern' => 'BcSample.default',
                    ]
                ]
            ],
        ];
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
