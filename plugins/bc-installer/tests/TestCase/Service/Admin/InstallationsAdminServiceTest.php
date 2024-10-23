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

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\ContentFolderFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcInstaller\Service\Admin\InstallationsAdminService;
use BcInstaller\Service\Admin\InstallationsAdminServiceInterface;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\ORM\Exception\PersistenceFailedException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

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
    use ScenarioAwareTrait;

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
     * @param array $sessionData
     * @param array $installationData
     * @param array $expected
     * @dataProvider readDbSettingDataProvider
     */
    public function test_readDbSetting($sessionData, $installationData, $expected)
    {
        $session = new Session();
        $request = new ServerRequest(['session' => $session]);

        if (!empty($sessionData)) {
            $session->write('Installation', $sessionData);
        }

        $result = $this->Installations->readDbSetting($request, $installationData);
        $this->assertEquals($expected, $result);
    }

    public static function readDbSettingDataProvider()
    {
        return [
            [
                [],
                [],
                [
                    'className' => 'Cake\Database\Connection',
                    'datasource' => '',
                    'driver' => null,
                    'host' => '',
                    'port' => '',
                    'username' => '',
                    'password' => '',
                    'prefix' => '',
                    'database' => '',
                    'schema' => '',
                    'encoding' => '',
                    'dataPattern' => '',
                    'persistent' => false,
                ]
            ],
            [
                [
                    'dbType' => 'mysql',
                    'dbHost' => 'localhost',
                    'dbPort' => '3306',
                    'dbUsername' => 'root',
                    'dbPassword' => 'password',
                    'dbPrefix' => 'bc_',
                    'dbName' => 'test_db',
                    'dbSchema' => '',
                    'dbEncoding' => 'utf8mb4',
                    'dbDataPattern' => 'BcSample.default'
                ],
                [],
                [
                    'className' => 'Cake\Database\Connection',
                    'datasource' => 'mysql',
                    'driver' => 'Cake\Database\Driver\Mysql',
                    'host' => 'localhost',
                    'port' => '3306',
                    'username' => 'root',
                    'password' => 'password',
                    'prefix' => 'bc_',
                    'database' => 'test_db',
                    'schema' => '',
                    'encoding' => 'utf8mb4',
                    'dataPattern' => 'BcSample.default',
                    'persistent' => false,
                ]
            ],
            [
                [],
                [
                    'dbType' => 'postgres',
                    'dbHost' => 'localhost',
                    'dbPort' => '5432',
                    'dbUsername' => 'postgres_user',
                    'dbPassword' => 'password123',
                    'dbPrefix' => 'pg_',
                    'dbName' => 'pg_database',
                    'dbSchema' => 'public',
                    'dbEncoding' => 'UTF8',
                    'dbDataPattern' => 'PgSample.default'
                ],
                [
                    'className' => 'Cake\Database\Connection',
                    'datasource' => 'postgres',
                    'driver' => 'Cake\Database\Driver\Postgres',
                    'host' => 'localhost',
                    'port' => '5432',
                    'username' => 'postgres_user',
                    'password' => 'password123',
                    'prefix' => 'pg_',
                    'database' => 'pg_database',
                    'schema' => 'public',
                    'encoding' => 'UTF8',
                    'dataPattern' => 'PgSample.default',
                    'persistent' => false,
                ]
            ]
        ];
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
        //データを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        ContentFolderFactory::make(['id' => 1])->persist();
        ContentFactory::make([
            'id' => 1,
            'plugin' => 'BaserCore',
            'type' => 'ContentFolder',
            'site_id' => 1,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 10,
            'entity_id' => 1,
            'site_root' => true,
            'status' => true
        ])->persist();

        //準備
        $request = $this->loginAdmin($this->getRequest());
        $request = $request->withParsedBody([
            'admin_email' => 'basertest@example.com',
            'site_name' => 'basertest',
            'admin_password' => '123456Abcdef@',
            'admin_confirm_password' => '123456Abcdef@',
        ]);
        //正常テスト
        $this->Installations->initAdmin($request);

        //SESSIONにデータが設定できるか確認
        $this->assertEquals(2, $_SESSION["Installation"]["id"]);
        //新しいユーザーが保存できるか確認
        $user = UserFactory::get(2);
        $this->assertEquals('basertest@example.com', $user->email);

        //異常テスト
        $request = $request->withParsedBody([
            'admin_email' => 'basertest@example.com',
            'site_name' => 'basertest',
            'admin_password' => '123456Abcdef@',
            'admin_confirm_password' => '123456Abcdef@',
        ]);
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (email.nameUnique: "既に登録のあるEメールです。');
        $this->Installations->initAdmin($request);
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
