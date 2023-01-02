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

namespace BcInstaller\Command;

use BaserCore\Error\BcException;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcInstaller\Service\InstallationsService;
use BcInstaller\Service\InstallationsServiceInterface;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;

/**
 * InstallCommand
 *
 * bin/cake install https://localhost "管理者メールアドレス" "管理者パスワード" "データベース名" --datasource "データベースの種類" --host "DBホスト名" --username="DBユーザー名" --password="DBパスワード"
 *
 * example) default db MySQL
 * bin/cake install https://localhost webmaster@example.org basercms basercms --host localhost --username root --password root
 */
class InstallCommand extends Command
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * buildOptionParser
     *
     * @param \Cake\Console\ConsoleOptionParser $parser
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(\Cake\Console\ConsoleOptionParser $parser): \Cake\Console\ConsoleOptionParser
    {
        $parser->addArgument('siteurl', [
            'help' => __d('baser', 'サイトURL'),
            'default' => '',
            'required' => true
        ])->addArgument('adminemail', [
            'help' => __d('baser', '管理者メールアドレス'),
            'default' => '',
            'required' => true
        ])->addArgument('adminpassword', [
            'help' => __d('baser', '管理者パスワード'),
            'default' => '',
            'required' => true
        ])->addArgument('database', [
            'help' => __d('baser', 'データベース名'),
            'default' => 'basercms',
            'required' => true
        ]);

        $parser->addOption('datasource', [
            'help' => __d('baser', 'データベースタイプ ( mysql or postgresql or sqlite )'),
            'default' => 'mysql',
        ])->addOption('host', [
            'help' => __d('baser', 'データベースホスト名'),
            'default' => 'localhost',
        ])->addOption('username', [
            'help' => __d('baser', 'データベースログインユーザー名'),
            'default' => '',
        ])->addOption('password', [
            'help' => __d('baser', 'データベースログインパスワード'),
            'default' => '',
        ])->addOption('prefix', [
            'help' => __d('baser', 'データベーステーブルプレフィックス'),
            'default' => ''
        ])->addOption('port', [
            'help' => __d('baser', 'データベースポート番号'),
            'default' => ''
        ])->addOption('baseurl', [
            'help' => __d('baser', 'ベースとなるURL'),
            'default' => '/'
        ])->addOption('sitename', [
            'help' => __d('baser', 'サイト名'),
            'default' => 'My Site'
        ])->addOption('data', [
            'help' => __d('baser', '初期データパターン'),
            'default' => Configure::read('BcApp.defaultFrontTheme') . '.default'
        ]);
        return $parser;
    }

    /**
     * execute
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return int|void|null
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        try {
            $dbConfig = $this->getDbParams($args);
        } catch (BcException $e) {
            $io->err($e->getMessage());
            return;
        }

        if (BcUtil::isInstalled()) {
            $io->err(__d('baser', '既にインストール済です。 cake install reset を実行してください。'));
            return;
        }
        if (!BcUtil::isInstallMode()) {
            $io->err(__d('baser', 'baserCMSのインストールを行うには、.env の　INSTALL_MODE を true に設定する必要があります。'));
            return;
        }
        if (!$this->install($args, $io, $dbConfig)) {
            $io->err(__d('baser', 'baserCMSのインストールに失敗しました。ログファイルを確認してください。'));
        }

        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfigsService->putEnv('INSTALL_MODE', 'false');

        BcUtil::clearAllCache();
        $io->out(__d('baser', 'baserCMSのインストールが完了しました。'));
    }

    /**
     * インストール実行
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @param array $dbConfig
     * @return bool
     */
    public function install(Arguments $args, ConsoleIo $io, array $dbConfig)
    {
        $siteUrl = $args->getArgument('siteurl');
        if (!preg_match('/\/$/', $siteUrl)) $siteUrl .= '/';
        // TODO ucmitz baseUrl 未実装
//        if ($args->getOption('baseurl')) {
//            $baseUrl = $args->getOption('baseurl');
//        } else {
//            $baseUrl = '';
//        }

        /** @var InstallationsService $service */
        $service = $this->getService(InstallationsServiceInterface::class);
        $service->BcDatabase->connectDb($dbConfig);

        // Construction db
        $service->BcDatabase->deleteTables('default', $dbConfig);
        $service->constructionDb($dbConfig, $args->getOption('data'));

        // Init admin
        $service->setAdminEmail($args->getArgument('adminemail'));
        $service->setSiteName($args->getOption('sitename'));
        $salt = $service->setSecuritySalt();
        $service->addDefaultUser([
            'password_1' => $args->getArgument('adminpassword'),
            'password_2' => $args->getArgument('adminpassword'),
            'email' => $args->getArgument('adminemail')
        ]);

        // Init files
        $service->createInstallFile($dbConfig, $salt);
        $service->createJwt();

        // Init db
        $service->createDefaultFiles();
        $service->deployEditorTemplateImage();
        $service->installCorePlugin($args->getOption('data'));
        $service->executeDefaultUpdates();
        $service->buildPermissions();
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $siteConfigsService->putEnv('SITE_URL', $siteUrl);
        $siteConfigsService->putEnv('SSL_URL', $siteUrl);
        if ($dbConfig['datasource'] === 'postgres') {
            $service->BcDatabase->updateSequence();
        }
        return true;
    }

    /**
     * パラメーターからDBの設定を取得する
     *
     * @param Arguments $args
     * @return bool[]|false|int[]|null[]|string[]
     */
    protected function getDbParams(Arguments $args)
    {
        $dbConfig = array_merge([
            'datasource' => '',
            'host' => '',
            'database' => $args->getArgument('database'),
            'login' => '',
            'password' => '',
            'prefix' => '',
            'port' => '',
            'persistent' => false,
            'schema' => '',
            'encoding' => 'utf8'
        ], $args->getOptions());

        $drivers = ['mysql', 'postgres', 'sqlite'];
        if (!in_array($dbConfig['datasource'], $drivers)) return false;
        if ((!$dbConfig['username'] || !$dbConfig['password'] || !$dbConfig['host']) &&
            ($dbConfig['datasource'] == 'mysql' || $dbConfig['datasource'] == 'postgres')) {
            throw new BcException(__d('baser', '{0} の場合は、host / username / password をオプションで指定する必要があります。', $dbConfig['datasource']));
        }
        if (empty($localhost['port'])) {
            if ($dbConfig['datasource'] === 'mysql') {
                $dbConfig['port'] = '3306';
            } elseif ($dbConfig['datasource'] === 'postgres') {
                $dbConfig['port'] = '5432';
            }
        }
        if (empty($dbConfig['database'])) return false;
        if ($dbConfig['datasource'] == 'postgres') $dbConfig['schema'] = 'public';
        /** @var InstallationsService $service */
        $service = $this->getService(InstallationsServiceInterface::class);
        $dbConfig['driver'] = $service->BcDatabase->getDatasourceName($dbConfig['datasource']);
        return $dbConfig;
    }

}
