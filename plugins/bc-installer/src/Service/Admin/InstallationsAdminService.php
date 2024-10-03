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

namespace BcInstaller\Service\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcApiUtil;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcInstaller\Service\InstallationsService;
use Cake\Core\Configure;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Utility\Inflector;

/**
 * InstallationsAdminService
 */
class InstallationsAdminService extends InstallationsService implements InstallationsAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * ステップ２用の view 変数を取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForStep2(): array
    {
        return $this->checkEnv();
    }

    /**
     * ステップ３用の view 変数を取得する
     *
     * @param bool $blDBSettingsOK
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForStep3(bool $blDBSettingsOK): array
    {
        return [
            'dbsource' => $this->_getDbSource(),
            'blDBSettingsOK' => $blDBSettingsOK,
            'dbDataPatterns' => $this->getAllDefaultDataPatterns()
        ];
    }

    /**
     * ステップ３用のフォーム初期値を取得する
     *
     * @param ServerRequest $request
     * @return array
     * @checked
     * @noTodo
     */
    public function getDefaultValuesStep3(ServerRequest $request): array
    {
        if (!$request->getSession()->read('Installation.dbType')) {
            return [
                'dbType' => 'mysql',
                'dbHost' => 'localhost',
                'dbPrefix' => '',
                'dbPort' => '3306',
                'dbName' => 'basercms',
                'dbDataPattern' => Inflector::camelize(Configure::read('BcApp.defaultFrontTheme'), '-') . '.default'
            ];
        }
        $setting = $this->readDbSetting($request);
        return [
            'dbType' => $setting['datasource'],
            'dbHost' => $setting['host'],
            'dbPrefix' => $setting['prefix'],
            'dbPort' => $setting['port'],
            'dbName' => basename(str_replace(['.csv', '.db'], '', $setting['database'])),
            'dbDataPattern' => $setting['dataPattern'],
            'dbUsername' => $setting['username'],
            'dbPassword' => $setting['password'],
        ];
    }

    /**
     * ステップ４用のフォーム初期値を取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDefaultValuesStep4(ServerRequest $request): array
    {
        $data = [];
        if ($request->getSession()->check('Installation')) {
            $data = $request->getSession()->read('Installation');
        }
        if (!empty($data['admin_password'])) {
            $data['admin_confirm_password'] = $data['admin_password'];
        }
        return array_merge([
            'site_name' => 'My Site',
            'admin_username' => '',
            'admin_password' => '',
            'admin_confirm_password' => '',
            'admin_email' => ''
        ], $data);
    }

    /**
     * DB設定をセッションに保存
     *
     * @param ServerRequest $request
     * @param array $data
     * @return void
     * @checked
     * @noTodo
     */
    public function writeDbSettingToSession(ServerRequest $request, array $data): void
    {
        $data['dbSchema'] = '';
        if(!empty($data['dbType'])) {
            switch($data['dbType']) {
                case 'mysql':
                    $data['dbEncoding'] = 'utf8mb4';
                    break;
                case 'postgres':
                    $data['dbSchema'] = 'public'; // TODO とりあえずpublic固定
                    $data['dbEncoding'] = 'utf8';
                    break;
                case 'sqlite':
                default:
                    $data['dbEncoding'] = 'utf8';
                    break;
            }
        }

        $sessionData = $request->getSession()->read();
        $sessionInstallation = [];
        if(!empty($sessionData['Installation'])) $sessionInstallation = $sessionData['Installation'];
        $installation = ['Installation' => array_merge($sessionInstallation, [
            'dbType' => $data['dbType'],
            'dbHost' => $data['dbHost'],
            'dbPort' => $data['dbPort'],
            'dbUsername' => $data['dbUsername'],
            'dbPassword' => $data['dbPassword'],
            'dbPrefix' => $data['dbPrefix'],
            'dbName' => $data['dbName'],
            'dbSchema' => $data['dbSchema'],
            'dbEncoding' => $data['dbEncoding'],
            'dbDataPattern' => $data['dbDataPattern']
        ])];
        $request->getSession()->write($installation);
    }

    /**
     * DB設定をセッションから取得
     *
     * @param ServerRequest $request
     * @param array $installationData
     * @return array
     * @checked
     * @noTodo
     */
    public function readDbSetting(ServerRequest $request, array $installationData = []): array
    {
        if (!$installationData) {
            $session = $request->getSession();
            if ($session->check('Installation')) {
                $installationData = $session->read('Installation');
            } else {
                $installationData = [
                    'dbType' => '',
                    'dbHost' => '',
                    'dbPort' => '',
                    'dbUsername' => '',
                    'dbPassword' => '',
                    'dbPrefix' => '',
                    'dbName' => '',
                    'dbSchema' => '',
                    'dbEncoding' => '',
                    'dbDataPattern' => ''
                ];
            }
        }
        $data = [
            'className' => Connection::class,
            'datasource' => $installationData['dbType'],
            'driver' => $this->BcDatabase->getDatasourceName($installationData['dbType']),
            'host' => $installationData['dbHost'],
            'port' => $installationData['dbPort'],
            'username' => $installationData['dbUsername'],
            'password' => $installationData['dbPassword'],
            'prefix' => $installationData['dbPrefix'],
            'database' => $this->getRealDbName($installationData['dbType'], $installationData['dbName']),
            'schema' => $installationData['dbSchema'],
            'encoding' => $installationData['dbEncoding'],
            'dataPattern' => $installationData['dbDataPattern'],
            'persistent' => false,
        ];
        return $data;
    }

    /**
     * 全てのテーブルを削除する
     *
     * @return bool
     * @checked
     * @noTodo
     */
    public function deleteAllTables(ServerRequest $request): bool
    {
        $dbConfig = $this->readDbSetting($request);
        if (!$dbConfig) {
            $dbConfig = ConnectionManager::getConfig('default');
        }
        return $this->BcDatabase->deleteTables('default', $dbConfig);
    }

    /**
     * 管理情報を初期化する
     *
     * - 初期ユーザー情報
     * - サイト名
     *
     * @param ServerRequest $request
     * @throws PersistenceFailedException
     * @checked
     * @noTodo
     */
    public function initAdmin(ServerRequest $request): void
    {
        // サイト基本設定登録
        $this->setAdminEmailAndVersion($request->getData('admin_email'));
        $this->setSiteName($request->getData('site_name'));

        // 管理ユーザー登録
        $user = [
            'password_1' => $request->getData('admin_password'),
            'password_2' => $request->getData('admin_confirm_password'),
            'email' => $request->getData('admin_email')
        ];

        try {
            $user = $this->addDefaultUser($user);
            $request->getSession()->write('Installation.id', $user->id);
        } catch (PersistenceFailedException $e) {
            throw $e;
        }
    }

    /**
     * インストールに関するファイルやフォルダを初期化する
     *
     * @param ServerRequest $request
     * @checked
     * @noTodo
     */
    public function initFiles(ServerRequest $request): void
    {
        // インストールファイルを生成する
        $this->createInstallFile($this->readDbSetting($request));
        // JWTキーを作成する
        BcApiUtil::createJwt();
        // アップロード用初期フォルダを作成する
        $this->createDefaultFiles();
        // エディタテンプレート用の画像を配置
        $this->deployEditorTemplateImage();
    }

    /**
     * データベースに接続する
     *
     * @param ServerRequest $request
     * @return \Cake\Datasource\ConnectionInterface
     * @checked
     * @noTodo
     */
    public function connectDb(ServerRequest $request): \Cake\Datasource\ConnectionInterface
    {
        $dbConfig = $this->readDbSetting($request);
        return $this->BcDatabase->connectDb($dbConfig);
    }

    /**
     * 管理画面にログインする
     *
     * @param ServerRequest $request
     * @param Response $response
     * @return void
     * @checked
     * @noTodo
     */
    public function login(ServerRequest $request, Response $response): void
    {
        // ログインするとセッションが初期化されてしまうので一旦取得しておく
        $installationSetting = $request->getSession()->read('Installation');
        /* @var UsersService $usersService */
        $usersService = $this->getService(UsersServiceInterface::class);
        $usersService->login($request, $response, $installationSetting['id']);
    }

    /**
     * データベースを初期化する
     *
     * @param ServerRequest $request
     * @checked
     * @noTodo
     */
    public function initDb(ServerRequest $request): void
    {
        // データベースのデータを初期設定に更新
        $this->executeDefaultUpdates();

        // アクセスルールの初期データを構築
        $this->buildPermissions();

        // SITE_URL更新
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfigsService->putEnv('SITE_URL', BcUtil::siteUrl());

        // シーケンスを更新する
        $dbConfig = ConnectionManager::getConfig('default');
        $datasource = strtolower(str_replace('Cake\\Database\\Driver\\', '', $dbConfig['driver']));
        if ($datasource === 'postgres') {
            $this->BcDatabase->updateSequence();
        }
    }

}
