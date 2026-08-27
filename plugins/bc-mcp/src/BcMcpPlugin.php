<?php
declare(strict_types=1);

namespace BcMcp;

use BaserCore\BcPlugin;
use BaserCore\Service\SiteConfigsService;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcApiUtil;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcMcp\Service\RegistrationRateLimiter;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Log\Log;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\InflectedRoute;

/**
 * Plugin for BcMcp
 */
class BcMcpPlugin extends BcPlugin
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Bootstrap
     *
     * @param \Cake\Core\PluginApplicationInterface $app アプリケーション
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        // プラグインの setting.php は baser-core が Configure::consume('Log') を
        // 実行した後に読み込まれるため、mcp スコープのロガーが Log へ登録されない。
        // ここで登録し、MCP のログが logs/mcp.log に記録されるようにする。
        if (!Log::getConfig('mcp') && Configure::check('Log.mcp')) {
            Log::setConfig('mcp', Configure::read('Log.mcp'));
        }

        // setting.php の Cache 設定は baser-core の読み込み順の都合で
        // Cache へ登録されないため、ここで登録する
        if (!Cache::getConfig(RegistrationRateLimiter::CACHE_CONFIG)
            && Configure::check('Cache.' . RegistrationRateLimiter::CACHE_CONFIG)) {
            Cache::setConfig(
                RegistrationRateLimiter::CACHE_CONFIG,
                Configure::read('Cache.' . RegistrationRateLimiter::CACHE_CONFIG)
            );
        }
    }

    /**
     * Install
     * @param $options
     * @return bool
     * @throws \Random\RandomException
     */
    public function install($options = []): bool
    {
        parent::install($options);
        /* @var SiteConfigsService $siteConfigsService */
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $oauth2EncKey = base64_encode(random_bytes(32));
        // 暗号化キーを書けないまま完了させると、OAuth2 が停止した状態で
        // インストール済みに見えてしまうため、失敗として扱う
        if (!$siteConfigsService->putEnv('OAUTH2_ENC_KEY', $oauth2EncKey)) {
            $this->log('OAUTH2_ENC_KEY を config/.env に書き込めませんでした。書き込み権限を確認してください。');
            return false;
        }
        $siteConfigsService->putEnv('USE_CORE_API', "true");
        $siteConfigsService->putEnv('USE_CORE_ADMIN_API', "true");
        if (!file_exists(CONFIG . 'jwt.pem')) {
            BcApiUtil::createJwt();
        }
        return true;
    }

    /**
     * Add routes for the plugin.
     *
     * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        // .well-known エンドポイントをルートレベルで設定（認証不要の通常コントローラーを指定）
        $routes->scope('/', function(RouteBuilder $builder) {
            $builder->setRouteClass(InflectedRoute::class);

            $builder->connect('/mcp', ['plugin' => 'BcMcp', 'controller' => 'McpProxy', 'action' => 'index'], ['routeClass' => InflectedRoute::class]);
            $builder->connect('/bc-mcp', ['plugin' => 'BcMcp', 'controller' => 'McpProxy', 'action' => 'index'], ['routeClass' => InflectedRoute::class]);

            // OAuth 2.0 保護リソースメタデータエンドポイント (RFC 9728)
            $builder->connect('/.well-known/oauth-protected-resource', ['plugin' => 'BcMcp', 'controller' => 'Oauth2', 'action' => 'options'])->setMethods(['OPTIONS']);
            $builder->connect('/.well-known/oauth-protected-resource', ['plugin' => 'BcMcp', 'controller' => 'Oauth2', 'action' => 'protectedResourceMetadata'])->setMethods(['GET']);
            $builder->connect('/.well-known/oauth-protected-resource/bc-mcp', ['plugin' => 'BcMcp', 'controller' => 'Oauth2', 'action' => 'options'])->setMethods(['OPTIONS']);
            $builder->connect('/.well-known/oauth-protected-resource/bc-mcp', ['plugin' => 'BcMcp', 'controller' => 'Oauth2', 'action' => 'protectedResourceMetadata'])->setMethods(['GET']);

            // OAuth 2.0 認可サーバーメタデータエンドポイント (RFC 8414)
            $builder->connect('/.well-known/oauth-authorization-server', ['plugin' => 'BcMcp', 'controller' => 'Oauth2', 'action' => 'options'])->setMethods(['OPTIONS']);
            $builder->connect('/.well-known/oauth-authorization-server', ['plugin' => 'BcMcp', 'controller' => 'Oauth2', 'action' => 'authorizationServerMetadata'])->setMethods(['GET']);
            $builder->connect('/.well-known/oauth-authorization-server/bc-mcp', ['plugin' => 'BcMcp', 'controller' => 'Oauth2', 'action' => 'options'])->setMethods(['OPTIONS']);
            $builder->connect('/.well-known/oauth-authorization-server/bc-mcp', ['plugin' => 'BcMcp', 'controller' => 'Oauth2', 'action' => 'authorizationServerMetadata'])->setMethods(['GET']);
        });

        $routes->plugin('BcMcp', ['path' => '/bc-mcp'], function(RouteBuilder $builder) {
            $builder->setRouteClass(InflectedRoute::class);

            // Oauth2エンドポイント（認証不要）
            // トークン発行エンドポイント
            $builder->connect('/oauth2/token', ['controller' => 'Oauth2', 'action' => 'options'])->setMethods(['OPTIONS']);
            $builder->connect('/oauth2/token', ['controller' => 'Oauth2', 'action' => 'token'])->setMethods(['POST']);

            // トークン検証エンドポイント
            $builder->connect('/oauth2/verify', ['controller' => 'Oauth2', 'action' => 'options'])->setMethods(['OPTIONS']);
            $builder->connect('/oauth2/verify', ['controller' => 'Oauth2', 'action' => 'verify'])->setMethods(['POST', 'GET']);

            // クライアント情報取得エンドポイント
            $builder->connect('/oauth2/client-info', ['controller' => 'Oauth2', 'action' => 'options'])->setMethods(['OPTIONS']);
            $builder->connect('/oauth2/client-info', ['controller' => 'Oauth2', 'action' => 'clientInfo'])->setMethods(['GET']);

            // RFC 7591 動的クライアント登録プロトコル（認証不要）
            $builder->connect('/oauth2/register', ['controller' => 'Oauth2', 'action' => 'options'])->setMethods(['OPTIONS']);
            $builder->connect('/oauth2/register', ['controller' => 'Oauth2', 'action' => 'register'])->setMethods(['POST']);

            // クライアント設定エンドポイント（RFC 7591）
            $builder->connect('/oauth2/register/{client_id}', ['controller' => 'Oauth2', 'action' => 'options'])->setMethods(['OPTIONS'])->setPass(['client_id']);
            $builder->connect('/oauth2/register/{client_id}', ['controller' => 'Oauth2', 'action' => 'clientConfiguration'])->setMethods(['GET', 'PUT', 'DELETE'])->setPass(['client_id']);

            // Authorization Code Grant 認可エンドポイント（認証必要）
            $builder->connect('/oauth2/authorize', ['prefix' => 'Admin', 'controller' => 'Oauth2', 'action' => 'options'])->setMethods(['OPTIONS']);
            $builder->connect('/oauth2/authorize', ['prefix' => 'Admin', 'controller' => 'Oauth2', 'action' => 'authorize'])->setMethods(['GET', 'POST']);

            // その他のルート
            $builder->fallbacks(\Cake\Routing\Route\DashedRoute::class);
        });

        // Admin prefix routes for Oauth2 endpoints（認証が必要なエンドポイントのみ）
        $routes->prefix('Admin', ['path' => BcUtil::getPrefix()], function(RouteBuilder $builder) {
            $builder->plugin('BcMcp', ['path' => '/bc-mcp'], function(RouteBuilder $routes) {
                $routes->setRouteClass(InflectedRoute::class);

                // MCPサーバー管理
                // 常駐プロセスを廃止したため、起動・停止・再起動・設定のルートは持たない
                $routes->get('/mcp-server-manager', ['controller' => 'McpServerManager', 'action' => 'index']);
            });
        });

        parent::routes($routes);
    }

}
