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
use Authentication\Middleware\AuthenticationMiddleware;
use BaserCore\Event\BcContainerEventListener;
use BaserCore\Event\BcControllerEventDispatcher;
use BaserCore\Event\BcModelEventDispatcher;
use BaserCore\Event\BcViewEventDispatcher;
use BaserCore\Middleware\BcAdminMiddleware;
use BaserCore\Middleware\BcFrontMiddleware;
use BaserCore\Middleware\BcRedirectSubSiteFilter;
use BaserCore\Middleware\BcRequestFilterMiddleware;
use BaserCore\ServiceProvider\BcServiceProvider;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Http\ServerRequestFactory;
use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use ReflectionClass;
use ReflectionProperty;

/**
 * Class plugin
 * @package BaserCore
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
        // composer インストール対応
        // composer でインストールした場合、プラグインは vendor 保存されるためパスを追加
        // bootstrap() で、setting.php の読み込みがあるので、その前に呼び出す必要あり
        Configure::write('App.paths.plugins', array_merge(
            Configure::read('App.paths.plugins'),
            [ROOT . DS . 'vendor' . DS . 'baserproject' . DS]
        ));

        parent::bootstrap($application);

        if(BcUtil::isInstalled()) {
            BcUtil::checkTmpFolders();
        }

        if (file_exists(CONFIG . 'setting.php')) {
            Configure::load('setting', 'baser');
        }

        if (!filter_var(env('USE_DEBUG_KIT', true), FILTER_VALIDATE_BOOLEAN)) {
            // 明示的に指定がない場合、DebugKitは重すぎるのでデバッグモードでも利用しない
            \Cake\Core\Plugin::getCollection()->remove('DebugKit');
        }

        $application->addPlugin('Authentication');
        $application->addPlugin('Migrations');
        $application->addPlugin(Inflector::camelize(Configure::read('BcApp.defaultAdminTheme'), '-'));
        $application->addPlugin(Inflector::camelize(Configure::read('BcApp.defaultFrontTheme'), '-'));

        $plugins = BcUtil::getEnablePlugins();
        if ($plugins) {
            foreach($plugins as $plugin) {
                if (BcUtil::includePluginClass($plugin->name)) {
                    $this->loadPlugin($application, $plugin->name, $plugin->priority);
                }
            }
        }
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
     * デフォルトテンプレートを設定する
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setupDefaultTemplatesPath()
    {
        if(BcUtil::isAdminSystem()) {
            $template = Configure::read('BcApp.defaultAdminTheme');
        } else {
            $template = Configure::read('BcApp.defaultFrontTheme');
        }
        Configure::write('App.paths.templates', array_merge(
            [ROOT . DS . 'plugins' . DS . $template . DS . 'templates' . DS],
            Configure::read('App.paths.templates')
        ));
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
        $application->addPlugin($plugin);
        $pluginPath = BcUtil::getPluginPath($plugin);
        if (file_exists($pluginPath . 'config' . DS . 'setting.php')) {
            // DBに接続できない場合、CakePHPのエラーメッセージが表示されてしまう為、 try を利用
            // ※ プラグインの setting.php で、DBへの接続処理が書かれている可能性がある為
            try {
                Configure::load($plugin . '.setting');
            } catch (Exception $ex) {
            }
        }
        // プラグインイベント登録
        $eventTargets = ['Controller', 'Model', 'View', 'Helper'];
        foreach($eventTargets as $eventTarget) {
            $eventClassName = $plugin . $eventTarget . 'EventListener';
            if (file_exists($pluginPath . 'src' . DS . 'Event' . DS . $eventClassName . '.php')) {
                $event = EventManager::instance();
                $class = '\\' . $plugin . '\\Event\\' . $eventClassName;
                $pluginEvent = new $class();
                foreach($pluginEvent->events as $key => $options) {
                    // プラグイン側で priority の設定がされてない場合に設定
                    if (is_array($options)) {
                        if (empty($options['priority'])) {
                            $options['priority'] = $priority;
                            $pluginEvent->events[$key] = $options;
                        }
                    } else {
                        unset($pluginEvent->events[$key]);
                        $pluginEvent->events[$options] = ['priority' => $priority];
                    }
                }
                $event->on($pluginEvent, null);
            }
        }
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
            // Authorization (AuthComponent to Authorization)
            ->add(new AuthenticationMiddleware($this))
            ->add(new BcAdminMiddleware())
            ->add(new BcFrontMiddleware())
//            ->add(new BcUpdateFilterMiddleware())
            ->add(new BcRequestFilterMiddleware())
            ->add(new BcRedirectSubSiteFilter());

        // APIへのアクセスの場合、CSRFを強制的に利用しない設定に変更
        $ref = new ReflectionClass($middlewareQueue);
        $queue = $ref->getProperty('queue');
        $queue->setAccessible(true);
        foreach($queue->getValue($middlewareQueue) as $middleware) {
            if ($middleware instanceof CsrfProtectionMiddleware) {
                $middleware->skipCheckCallback(function($request) {
                    if ($request->getParam('prefix') === 'Api') {
                        return true;
                    }
                    return false;
                });
            }
        }

        return $middlewareQueue;
    }

    /**
     * 認証サービスプロバイダ生成
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
        $prefix = $request->getParam('prefix');
        $authSetting = Configure::read('BcPrefixAuth.' . $prefix);
        if(!$authSetting && $prefix === 'Api') {
            $authSetting = Configure::read('BcPrefixAuth.Admin');
        }
        if (!$authSetting || !BcUtil::isInstalled()) {
            $service->loadAuthenticator('Authentication.Form');
            return $service;
        }

        switch($prefix) {

            case 'Api':
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
                        'className' => 'Authentication.Orm',
                        'userModel' => $authSetting['userModel'],
                        'finder' => 'available'
                    ],
                ]);
                $service->loadAuthenticator('Authentication.' . $authSetting['type'], [
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
                        'finder' => 'available'
                    ],
                ]);
                // ログインの際のみ、管理画面へのログイン状態を維持するためセッションの設定を追加
                // TODO ログインURLの判定方法を検討必要
                // Api用の認証設定をAdminと分けて loginAction と リクエストのURLで判定させる
                if($request->getParam('controller') === 'Users' && $request->getParam('action') === 'login') {
                    $service->loadAuthenticator('Authentication.Session', [
                        'sessionKey' => $authSetting['sessionKey'],
                    ]);
                }
                break;

            default:

                $service->setConfig([
                    'unauthenticatedRedirect' => Router::url($authSetting['loginAction'], true),
                    'queryParam' => 'redirect',
                ]);
                $service->loadAuthenticator('Authentication.Session', [
                    'sessionKey' => $authSetting['sessionKey'],
                ]);
                $service->loadAuthenticator('Authentication.' . $authSetting['type'], [
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
                        'finder' => 'available'
                    ],
                ]);
                break;

        }

        return $service;
    }

    /**
     * Routes
     *
     * 次のルートを設定するが、未インストール時はインストーラーのみ設定し他はスキップする。
     *
     * ### インストーラー
     * /
     * /install
     *
     * ### アップデーター
     * /{update-key}
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
        if(BcUtil::isMigrations()) {
            parent::routes($routes);
            return;
        }

        $request = Router::getRequest();
        if(!$request) {
            $request = ServerRequestFactory::fromGlobals();
        }
        if(!BcUtil::isConsole() && !preg_match('/^\/debug-kit\//', $request->getPath())) {
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
         * インストーラー
         */
        if (!BcUtil::isInstalled()) {
            $routes->connect('/', ['plugin' => 'BaserCore', 'controller' => 'Installations', 'action' => 'index']);
            $routes->connect('/install', ['plugin' => 'BaserCore', 'controller' => 'Installations', 'action' => 'index']);
            $routes->fallbacks(InflectedRoute::class);
            parent::routes($routes);
            return;
        }

        /**
         * アップデーター
         * /config/setting.php にて URLを変更することができる
         */
        $routes->connect('/' . Configure::read('BcApp.updateKey'), [
            'prefix' => 'Admin',
            'plugin' => 'BaserCore',
            'controller' => 'Plugins',
            'action' => 'update'
        ]);

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
            'Api',
            ['path' => '/' . Configure::read('BcApp.baserCorePrefix') . '/api'],
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

        /**
         * フィード出力
         * 拡張子rssの場合は、rssディレクトリ内のビューを利用する
         */
        if (!BcUtil::isAdminSystem()) {
            $routes->setExtensions('rss');
        }
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

}
