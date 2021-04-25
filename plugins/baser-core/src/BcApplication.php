<?php
declare(strict_types=1);

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

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Event\EventManager;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceInterface;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Routing\Router;
use Exception;
use Psr\Http\Message\ServerRequestInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcApplication
 * @package BaserCore
 */
class BcApplication extends BaseApplication implements AuthenticationServiceProviderInterface
{
    /**
     * Load all the application configuration and bootstrap logic.
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function bootstrap(): void
    {
        parent::bootstrap();

        if (PHP_SAPI === 'cli') {
            $this->bootstrapCli();
        }

        if (Configure::read('debug') && env('USE_DEBUG_KIT', false)) {
            // 明示的に指定がない場合、DebugKitは重すぎるのでデバッグモードでも利用しない
            $this->addPlugin('DebugKit');
        }

        $this->addPlugin('BaserCore');
        $this->addPlugin('Authentication');
        $this->addPlugin('Migrations');
        $this->addPlugin('BcAdminThird');

        $plugins = BcUtil::getEnablePlugins();
        foreach($plugins as $plugin) {
            if (BcUtil::includePluginClass($plugin->name)) {
                $this->loadPlugin($plugin->name, $plugin->priority);
            }
        }
    }

    /**
     * プラグインを読み込む
     *
     * @param string $plugin
     * @return bool
     * @checked
     */
    function loadPlugin($plugin, $priority)
    {
        $this->addPlugin($plugin);
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
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(new ErrorHandlerMiddleware(Configure::read('Error')))

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime'),
            ]))

            // Add routing middleware.
            // If you have a large number of routes connected, turning on routes
            // caching in production could improve performance. For that when
            // creating the middleware instance specify the cache config name by
            // using it's second constructor argument:
            // `new RoutingMiddleware($this, '_cake_routes_')`
            ->add(new RoutingMiddleware($this))

            // Parse various types of encoded request bodies so that they are
            // available as array through $request->getData()
            // https://book.cakephp.org/4/en/controllers/middleware.html#body-parser-middleware
            ->add(new BodyParserMiddleware())

            // Authorization (AuthComponent to Authorization)
            ->add(new AuthenticationMiddleware($this));

        return $middlewareQueue;
    }

    /**
     * Bootrapping for CLI application.
     *
     * That is when running commands.
     *
     * @return void
     * @checked
     * @noTodo
     */
    protected function bootstrapCli(): void
    {
        try {
            $this->addPlugin('Bake');
        } catch (MissingPluginException $e) {
            // Do not halt if the plugin is missing
        }

        $this->addPlugin('Migrations');

        // Load more plugins here
    }

    /**
     * 認証サービスプロバイダ生成
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request
     * @return \Authentication\AuthenticationServiceInterface
     * @checked
     * @noTodo
     */
    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationServiceInterface
    {
        $service = new AuthenticationService();
        $prefix = $request->getParam('prefix');

        if ($prefix) {
            $authSetting = Configure::read('BcPrefixAuth.' . $prefix);
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
                ],
                'contain' => 'UserGroups',
            ]);
        } else {
            $service->loadAuthenticator('Authentication.Form');
        }

        return $service;
    }
}
