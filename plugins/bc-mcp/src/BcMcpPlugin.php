<?php
declare(strict_types=1);

namespace BcMcp;

use BaserCore\BcPlugin;
use BaserCore\Service\SiteConfigsService;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Utility\BcApiUtil;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Console\CommandCollection;
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
        $siteConfigsService->putEnv('OAUTH2_ENC_KEY', $oauth2EncKey);
        $siteConfigsService->putEnv('USE_CORE_API', "true");
        $siteConfigsService->putEnv('USE_CORE_ADMIN_API', "true");
        if (!file_exists(CONFIG . 'jwt.pem')) {
            BcApiUtil::createJwt();
        }
        return true;
    }

    /**
     * Add commands for the plugin.
     *
     * @param \Cake\Console\CommandCollection $commands The command collection to update.
     * @return \Cake\Console\CommandCollection
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        // MCPサーバーコマンドを追加
        $commands->add('bc_mcp.server', \BcMcp\Command\McpServerCommand::class);
        $commands = parent::console($commands);
        return $commands;
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
                $routes->get('/mcp-server-manager', ['controller' => 'McpServerManager', 'action' => 'index']);
                $routes->get('/mcp-server-manager/configure', ['controller' => 'McpServerManager', 'action' => 'configure']);
                $routes->post('/mcp-server-manager/configure', ['controller' => 'McpServerManager', 'action' => 'configure']);
                $routes->post('/mcp-server-manager/start', ['controller' => 'McpServerManager', 'action' => 'start']);
                $routes->post('/mcp-server-manager/stop', ['controller' => 'McpServerManager', 'action' => 'stop']);
                $routes->post('/mcp-server-manager/restart', ['controller' => 'McpServerManager', 'action' => 'restart']);
            });
        });

        parent::routes($routes);
    }

}
