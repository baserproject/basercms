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

namespace BaserCore;

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Authenticator\SessionAuthenticator;
use Authentication\Middleware\AuthenticationMiddleware;
use BaserCore\Command\ComposerCommand;
use BaserCore\Command\CreateReleaseCommand;
use BaserCore\Command\SetupInstallCommand;
use BaserCore\Command\SetupTestCommand;
use BaserCore\Command\UpdateCommand;
use BaserCore\Event\BcContainerEventListener;
use BaserCore\Event\BcControllerEventDispatcher;
use BaserCore\Event\BcModelEventDispatcher;
use BaserCore\Event\BcViewEventDispatcher;
use BaserCore\Middleware\BcAdminMiddleware;
use BaserCore\Middleware\BcFrontMiddleware;
use BaserCore\Middleware\BcRedirectSubSiteFilter;
use BaserCore\Middleware\BcRequestFilterMiddleware;
use BaserCore\ServiceProvider\BcServiceProvider;
use BaserCore\Utility\BcEvent;
use BaserCore\Utility\BcLang;
use BaserCore\Utility\BcUtil;
use Cake\Console\CommandCollection;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Core\Exception\MissingPluginException;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequestFactory;
use Cake\I18n\I18n;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
use Psr\Http\Message\ServerRequestInterface;
use Cake\Core\Plugin as CorePlugin;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class plugin
 */
class Plugin extends BcPlugin implements AuthenticationServiceProviderInterface
{

    /**
     * bootstrap
     *
     * @param PluginApplicationInterface $application
     *
     * @checked
     * @unitTest
     * @noTodo
     */
    public function bootstrap(PluginApplicationInterface $application): void
    {
        /**
         * composer インストール対応
         * composer でインストールした場合、プラグインは vendor 保存されるためパスを追加
         * bootstrap() で、setting.php の読み込みがあるので、その前に呼び出す必要あり
         */
        Configure::write('App.paths.plugins', array_merge(
            Configure::read('App.paths.plugins'),
            [ROOT . DS . 'vendor' . DS . 'baserproject' . DS]
        ));

        /**
         * インストール状態の設定
         * インストールされてない場合のテストをできるようにするため、Configure の設定を優先する
         */
        $hasInstall = file_exists(CONFIG . 'install.php');
        if (is_null(Configure::read('BcRequest.isInstalled'))) {
            Configure::write('BcRequest.isInstalled', $hasInstall);
        }

        /**
         * コンソール判定
         * BcUtil::isConsole で利用
         */
        $_ENV['IS_CONSOLE'] = (substr(php_sapi_name(), 0, 3) === 'cli');

        /**
         * 言語設定
         * ブラウザよりベースとなる言語を設定
         */
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && !BcUtil::isTest()) {
            I18n::setLocale(BcLang::parseLang($_SERVER['HTTP_ACCEPT_LANGUAGE']));
        }

        /**
         * インストール状態による初期化設定
         * インストールされている場合は、TMP フォルダの設定を行い、
         * されていない場合は、インストールプラグインをロードする。
         */
        if (BcUtil::isInstalled()) {
            BcUtil::checkTmpFolders();
        } else {
            $application->addPlugin('BcInstaller');
            Configure::load('BcInstaller.setting');
        }

        /**
         * プラグインごとの設定ファイル読み込み
         */
        parent::bootstrap($application);

        /**
         * 文字コードの検出順を指定
         */
        if (function_exists('mb_detect_order')) {
            mb_detect_order(implode(',', Configure::read('BcEncode.detectOrder')));
        }

        /**
         * 設定ファイル読み込み
         * baserCMSの各種設定は、ここで上書きできる事を想定
         */
        if (file_exists(CONFIG . 'setting.php')) Configure::load('setting', 'baser');

        /**
         * ログ設定
         * ユニットテストの際、複数回設定するとエラーになるため
         * 設定済かチェックを実施
         */
        if (!Log::getConfig('update')) {
            Log::setConfig(Configure::consume('Log'));
        }

        /**
         * プラグインロード
         */
        if (BcUtil::isTest()) $application->addPlugin('CakephpFixtureFactories');
        $application->addPlugin('Authentication');
        $application->addPlugin('Migrations');

        $this->addTheme($application);

        if (!filter_var(env('USE_DEBUG_KIT', true), FILTER_VALIDATE_BOOLEAN)) {
            // 明示的に指定がない場合、DebugKitは重すぎるのでデバッグモードでも利用しない
            \Cake\Core\Plugin::getCollection()->remove('DebugKit');
        }
        $plugins = BcUtil::getEnablePlugins();
        if ($plugins) {
            foreach($plugins as $plugin) {
                if (BcUtil::includePluginClass($plugin->name)) {
                    $this->loadPlugin($application, $plugin->name, $plugin->priority);
                }
            }
        }

        /**
         * デフォルトテンプレートを設定する
         */
        $this->setupDefaultTemplatesPath();

        /**
         * グローバルイベント登録
         */
        $event = EventManager::instance();
        $event->on(new BcControllerEventDispatcher());
        $event->on(new BcModelEventDispatcher());
        $event->on(new BcViewEventDispatcher());
        $event->on(new BcContainerEventListener());
    }

    /**
     * テーマを追加する
     *
     * テーマ内のプラグインも追加する
     *
     * @param PluginApplicationInterface $application
     * @noTodo
     * @checked
     */
    public function addTheme(PluginApplicationInterface $application)
    {
        $application->addPlugin(Inflector::camelize(Configure::read('BcApp.coreAdminTheme'), '-'));
        $application->addPlugin(Inflector::camelize(Configure::read('BcApp.coreFrontTheme'), '-'));
        if (!BcUtil::isInstalled()) return;
        $sitesTable = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $sites = $sitesTable->find()->where(['Sites.status' => true]);
        $path = [];
        foreach($sites as $site) {
            if ($site->theme) {
                BcUtil::includePluginClass($site->theme);
                $application->addPlugin($site->theme);
                $pluginPath = CorePlugin::path($site->theme) . 'plugins' . DS;
                if(!is_dir($pluginPath)) continue;
                $path[] = $pluginPath;
            }
        }
        // テーマプラグインを追加
        if($path) {
            Configure::write('App.paths.plugins', array_merge(
                Configure::read('App.paths.plugins'),
                $path
            ));
        }
    }

    /**
     * デフォルトテンプレートを設定する
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setupDefaultTemplatesPath()
    {
        $admin = Configure::read('BcApp.coreAdminTheme');
        $front = Configure::read('BcApp.coreFrontTheme');
        if (BcUtil::isAdminSystem() && empty($_REQUEST['preview'])) {
            Configure::write('App.paths.templates', array_merge([
                ROOT . DS . 'plugins' . DS . $admin . DS . 'templates' . DS,
                ROOT . DS . 'vendor' . DS . 'baserproject' . DS . $admin . DS . 'templates' . DS
            ], Configure::read('App.paths.templates')));
        } else {
            Configure::write('App.paths.templates', array_merge([
                ROOT . DS . 'plugins' . DS . $front . DS . 'templates' . DS,
                ROOT . DS . 'vendor' . DS . 'baserproject' . DS . $front . DS . 'templates' . DS,
                ROOT . DS . 'plugins' . DS . $admin . DS . 'templates' . DS,
                ROOT . DS . 'vendor' . DS . 'baserproject' . DS . $admin . DS . 'templates' . DS
            ], Configure::read('App.paths.templates')));
        }

    }

    /**
     * プラグインを読み込む
     *
     * @param PluginApplicationInterface $application
     * @param string $plugin
     * @return bool
     * @unitTest
     * @checked
     * @noTodo
     */
    function loadPlugin(PluginApplicationInterface $application, $plugin, $priority)
    {
        try {
            $application->addPlugin($plugin);
        } catch (MissingPluginException $e) {
            $this->log($e->getMessage());
            return false;
        }
        BcEvent::registerPluginEvent($plugin, $priority);
        return true;
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            ->insertBefore(CsrfProtectionMiddleware::class, new AuthenticationMiddleware($this))
            ->add(new BcAdminMiddleware())
            ->add(new BcFrontMiddleware())
            ->add(new BcRequestFilterMiddleware())
            ->add(new BcRedirectSubSiteFilter());

        // APIへのアクセスの場合、セッションによる認証以外は、CSRFを利用しない設定とする
        $ref = new ReflectionClass($middlewareQueue);
        $queue = $ref->getProperty('queue');
        $queue->setAccessible(true);

        foreach($queue->getValue($middlewareQueue) as $middleware) {
            if ($middleware instanceof CsrfProtectionMiddleware) {

                $middleware->skipCheckCallback(function($request) {
                    $prefix = $request->getParam('prefix');
                    $authSetting = Configure::read('BcPrefixAuth.' . $prefix);

                    // 領域が REST API でない場合はスキップしない
                    if (empty($authSetting['isRestApi'])) return false;

                    // 設定ファイルでスキップの定義がされている場合はスキップ
                    if(in_array($request->getPath(), $this->getSkipCsrfUrl())) return true;

                    $authenticator = $request->getAttribute('authentication')->getAuthenticationProvider();
                    if($authenticator) {
                        // 認証済の際、セッション認証以外はスキップ
                        if(!$authenticator instanceof SessionAuthenticator) return true;
                    } else {
                        // 認証できていない場合、領域がJwt認証前提の場合はスキップ
                        if($authSetting['type'] === 'Jwt') return true;
                    }
                    return false;
                });

            }
        }

        return $middlewareQueue;
    }

    /**
     * Web API のPOST送信において CSRF をスキップするURLについて、
     * Router で変換した上で取得
     *
     * @return array
     * @noTodo
     * @checked
     */
    protected function getSkipCsrfUrl(): array
    {
        $skipUrl = [];
        $skipUrlSrc = Configure::read('BcApp.skipCsrfUrlInPostApi');
        foreach($skipUrlSrc as $url) {
            $skipUrl[] = Router::url($url);
        }
        return $skipUrl;
    }

    /**
     * 認証サービスプロバイダ生成
     *
     * - インストール前の場合は、設定なしで、Session のみ読み込む
     *   （インストールの動作テストを複数回行う場合にセッションが残ってしまい内部的なエラーを吐いてしまうため）
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authentication\AuthenticationServiceInterface
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $service = new AuthenticationService();
        $prefix = BcUtil::getRequestPrefix($request);
        $authSetting = Configure::read('BcPrefixAuth.' . $prefix);

        if (!$this->isRequiredAuthentication($authSetting)) {
            $service->loadAuthenticator('Authentication.Form');
            if (!empty($authSetting['sessionKey']) && empty($authSetting['disabled'])) {
                $service->loadAuthenticator('Authentication.Session', [
                    'sessionKey' => $authSetting['sessionKey'],
                ]);
            }
            return $service;
        }

        switch($authSetting['type']) {
            case 'Session':
                $this->setupSessionAuth($service, $authSetting);
                break;
            case 'Jwt':
                if ($this->isEnabledCoreApi($prefix)) {
                    $this->setupJwtAuth($service, $authSetting);
                    if($prefix === 'Api/Admin') {
                        // セッションを持っている場合もログイン状態とみなす
                        $service->loadAuthenticator('Authentication.Session', [
                            'sessionKey' => $authSetting['sessionKey'],
                        ]);
                    }
                } else {
                    throw new ForbiddenException(__d('baser_core', 'Web APIは許可されていません。'));
                }
                break;
        }

        return $service;
    }

    /**
     * 認証が必要か判定する
     *
     * @param array $authSetting
     * @return bool
     */
    public function isRequiredAuthentication(array $authSetting)
    {
        if(!$authSetting || empty($authSetting['type'])) return false;
        if(!empty($authSetting['disabled'])) return false;
        if(!BcUtil::isInstalled()) return false;
        return true;
    }

    /**
     * APIが利用できるか確認する
     *
     * @param string $prefix
     * @return bool
     */
    public static function isEnabledCoreApi(string $prefix): bool
    {
        if (!filter_var(env('USE_CORE_API', false), FILTER_VALIDATE_BOOLEAN)) {
            if ($prefix === 'Api/Admin') {
                return BcUtil::isSameReferrerAsCurrent();
            }
        } else {
            return true;
        }
        return false;
    }

    /**
     * セッション認証のセットアップ
     *
     * @param AuthenticationService $service
     * @param array $authSetting
     * @return AuthenticationService
     */
    public function setupSessionAuth(AuthenticationService $service, array $authSetting)
    {
        $service->setConfig([
            'unauthenticatedRedirect' => Router::url($authSetting['loginAction'], true),
            'queryParam' => 'redirect',
        ]);
        $service->loadAuthenticator('Authentication.Session', [
            'sessionKey' => $authSetting['sessionKey'],
        ]);
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => [
                'username' => is_array($authSetting['username'])? $authSetting['username'][0] : $authSetting['username'],
                'password' => $authSetting['password']
            ],
            'loginUrl' => Router::url($authSetting['loginAction']),
        ]);
        $service->loadIdentifier('Authentication.Password', [
            'fields' => [
                'username' => $authSetting['username'],
                'password' => $authSetting['password']
            ],
            'resolver' => [
                'className' => 'Authentication.Orm',
                'userModel' => $authSetting['userModel'],
                'finder' => $authSetting['finder']?? 'available'
            ],
        ]);
        return $service;
    }

    /**
     * JWT 認証のセットアップ
     *
     * @param AuthenticationService $service
     * @param array $authSetting
     * @return AuthenticationService
     */
    public function setupJwtAuth(AuthenticationService $service, array $authSetting)
    {
        if (Configure::read('Jwt.algorithm') === 'HS256') {
            $secretKey = Security::getSalt();
        } elseif (Configure::read('Jwt.algorithm') === 'RS256') {
            $secretKey = file_get_contents(Configure::read('Jwt.publicKeyPath'));
        } else {
            return $service;
        }

        $service->loadAuthenticator('Authentication.Jwt', [
            'secretKey' => $secretKey,
            'algorithm' => 'RS256',
            'returnPayload' => false,
            'resolver' => [
                'className' => 'Authentication.Orm',
                'userModel' => $authSetting['userModel'],
            ],
        ]);
        $service->loadIdentifier('Authentication.JwtSubject', [
            'resolver' => [
                'className' => 'BaserCore.PrefixOrm',
                'userModel' => $authSetting['userModel'],
                'finder' => $authSetting['finder']?? 'available'
            ],
        ]);
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => [
                'username' => is_array($authSetting['username'])? $authSetting['username'][0] : $authSetting['username'],
                'password' => $authSetting['password']
            ],
        ]);
        $service->loadIdentifier('Authentication.Password', [
            'returnPayload' => false,
            'fields' => [
                'username' => $authSetting['username'],
                'password' => $authSetting['password']
            ],
            'resolver' => [
                'className' => 'Authentication.Orm',
                'userModel' => $authSetting['userModel'],
                'finder' => $authSetting['finder']?? 'available'
            ],
        ]);
        return $service;
    }

    /**
     * Routes
     *
     * 次のルートを設定するが、未インストール時はインストーラーのみ設定し他はスキップする。
     *
     * ### コンテンツルーティング
     * /*
     *
     * ### 管理画面ダッシュボード
     * /baser/admin
     *
     * ### JWTトークン検証用
     * /baser/api/baser-core/.well-known/jwks.json
     *
     *
     * @param RouteBuilder $routes
     * @checked
     * @noTodo
     * @unitTest
     */
    public function routes($routes): void
    {

        // migrations コマンドの場合は実行しない
        if (BcUtil::isMigrations()) {
            parent::routes($routes);
            return;
        }

        // インストールされていない場合は実行しない
        if (!BcUtil::isInstalled()) {
            parent::routes($routes);
            return;
        }

        $request = Router::getRequest();
        if (!$request) {
            $request = ServerRequestFactory::fromGlobals();
        }
        if (!BcUtil::isConsole() && !preg_match('/^\/debug-kit\//', $request->getPath())) {
            // ユニットテストでは実行しない
            $property = new ReflectionProperty(get_class($routes), '_collection');
            $property->setAccessible(true);
            $collection = $property->getValue($routes);
            $property = new ReflectionProperty(get_class($collection), '_routeTable');
            $property->setAccessible(true);
            $property->setValue($collection, []);
            $property = new ReflectionProperty(get_class($collection), '_paths');
            $property->setAccessible(true);
            $property->setValue($collection, []);
        }

        /**
         * フィード出力
         * 拡張子rssの場合は、rssディレクトリ内のビューを利用する
         */
        if (!BcUtil::isAdminSystem()) {
            $routes->setExtensions('rss');
        }

        /**
         * メンテナンス
         */
        $routes->connect('/maintenance', ['plugin' => 'BaserCore', 'controller' => 'Maintenance', 'action' => 'index']);

        /**
         * コンテンツルーティング
         */
        $routes->connect('/*', [], ['routeClass' => 'BaserCore.BcContentsRoute']);

        /**
         * 管理画面
         */
        $routes->prefix(
            'Admin',
            ['path' => BcUtil::getPrefix()],
            function(RouteBuilder $routes) {
                // ダッシュボード
                $routes->connect('', ['plugin' => 'BaserCore', 'controller' => 'Dashboard', 'action' => 'index']);
            }
        );

        /**
         * JWTトークン検証用ルーティング
         * /baser/api/baser-core/.well-known/jwks.json でアクセス
         */
        $routes->prefix(
            'Api/Admin',
            ['path' => '/' . Configure::read('BcApp.baserCorePrefix') . '/', Configure::read('BcApp.apiPrefix')],
            function(RouteBuilder $routes) {
                $routes->plugin(
                    'BaserCore',
                    ['path' => '/baser-core'],
                    function(RouteBuilder $routes) {
                        $routes->setExtensions(['json']);
                        $routes->connect('/.well-known/:controller/*', ['action' => 'index'], ['controller' => '(jwks)']);
                    }
                );
            }
        );

        parent::routes($routes);
    }

    /**
     * services
     * @param ContainerInterface $container
     * @checked
     * @noTodo
     * @unitTest
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcServiceProvider());
    }

    /**
     * コマンド定義
     *
     * @param CommandCollection $commands
     * @return CommandCollection
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        $commands->add('setup test', SetupTestCommand::class);
        $commands->add('composer', ComposerCommand::class);
        $commands->add('update', UpdateCommand::class);
        $commands->add('create release', CreateReleaseCommand::class);
        $commands->add('setup install', SetupInstallCommand::class);
        return $commands;
    }

}
