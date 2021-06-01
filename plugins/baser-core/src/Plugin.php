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

namespace BaserCore;

use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use BaserCore\ServiceProvider\BcServiceProvider;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use ReflectionClass;

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
     */
    public function bootstrap(PluginApplicationInterface $application): void
    {
        parent::bootstrap($application);

        if (!filter_var(env('USE_DEBUG_KIT', true), FILTER_VALIDATE_BOOLEAN)) {
            // 明示的に指定がない場合、DebugKitは重すぎるのでデバッグモードでも利用しない
            \Cake\Core\Plugin::getCollection()->remove('DebugKit');
        }

        $application->addPlugin('Authentication');
        $application->addPlugin('Migrations');
        $application->addPlugin('BcAdminThird');

        $plugins = BcUtil::getEnablePlugins();
        foreach($plugins as $plugin) {
            if (BcUtil::includePluginClass($plugin->name)) {
                $this->loadPlugin($application, $plugin->name, $plugin->priority);
            }
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
     */
    function loadPlugin(PluginApplicationInterface $application, $plugin, $priority)
    {
        $application->addPlugin($plugin);
        $pluginPath = BcUtil::getPluginPath($plugin);
        if (file_exists($pluginPath . 'Config' . DS . 'setting.php')) {
            // DBに接続できない場合、CakePHPのエラーメッセージが表示されてしまう為、 try を利用
            // ※ プラグインの setting.php で、DBへの接続処理が書かれている可能性がある為
            try {
                // TODO 未確認
                /* >>>
                Configure::load($plugin . '.setting');
                <<< */
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
            ->add(new AuthenticationMiddleware($this));

        // APIへのアクセスの場合、CSRFを強制的に利用しない設定に変更
        $ref = new ReflectionClass($middlewareQueue);
        $queue = $ref->getProperty('queue');
        $queue->setAccessible(true);
        foreach($queue->getValue($middlewareQueue) as $middleware) {
            if($middleware instanceof CsrfProtectionMiddleware) {
                $middleware->skipCheckCallback(function ($request) {
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
        $authSetting = Configure::read('BcPrefixAuth.Admin');
        if (!$authSetting) {
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
                    'algorithms' => ['RS256'],
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
                    ],
                ]);
                $service->loadAuthenticator('Authentication.' . $authSetting['type'], [
                    'fields' => [
                        'username' => is_array($authSetting['username'])? $authSetting['username'][0] : $authSetting['username'],
                        'password' => $authSetting['password']
                    ]
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
                    'contain' => 'UserGroups',
                ]);
                break;

            default:

                $service->setConfig([
                    'unauthenticatedRedirect' => Router::url($authSetting['loginAction'], true),
                    'queryParam' => 'redirect',
                    'contain' => 'UserGroups',
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
                    'contain' => 'UserGroups',
                ]);
                break;

        }

        return $service;
    }

    /**
     * Routes
     * App として管理画面を作成するためのルーティングを設定
     * @param RouteBuilder $routes
     */
    public function routes($routes): void
    {
        $routes->prefix(
            'Admin',
            ['path' => Configure::read('BcApp.baserCorePrefix') . Configure::read('BcApp.adminPrefix')],
            function(RouteBuilder $routes) {
                $routes->connect('/{controller}/index', [], ['routeClass' => InflectedRoute::class]);
                $routes->fallbacks(InflectedRoute::class);
            }
        );

        // JWTトークン検証用ルーティング
        // /baser/api/baser-core/.well-known/jwks.json でアクセス
        $routes->prefix(
            'Api',
            ['path' => Configure::read('BcApp.baserCorePrefix') . '/api'],
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
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcServiceProvider());
    }

}
