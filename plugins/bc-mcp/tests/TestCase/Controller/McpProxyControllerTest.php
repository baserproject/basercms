<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Controller;

use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Factory\UsersUserGroupFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcMcp\Controller\McpProxyController;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * McpProxyControllerTest
 */
class McpProxyControllerTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * test toMcpMessage が MCP の必須ヘッダを引き継ぐ
     *
     * 2026-07-28 では MCP-Protocol-Version / Mcp-Method / Mcp-Name が必須ヘッダで、
     * SDK がヘッダとボディの一致を検証する
     */
    public function testToMcpMessageCarriesRequiredHeaders()
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
                'HTTP_MCP_PROTOCOL_VERSION' => '2026-07-28',
                'HTTP_MCP_METHOD' => 'tools/call',
                'HTTP_MCP_NAME' => 'addBlogPost',
                'HTTP_AUTHORIZATION' => 'Bearer secret-token',
            ],
        ]);
        $controller = new McpProxyController($request);

        $message = $controller->toMcpMessage(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/call']);

        $this->assertEquals('2026-07-28', $message->getHeader('MCP-Protocol-Version'));
        $this->assertEquals('tools/call', $message->getHeader('Mcp-Method'));
        $this->assertEquals('addBlogPost', $message->getHeader('Mcp-Name'));
        // 認証はプロキシで完結しているため SDK へ渡さない
        $this->assertNull($message->getHeader('Authorization'));
        $this->assertEquals('POST', $message->getMethod());
    }

    /**
     * test toMcpMessage は識別子として妥当でない Mcp-Session-Id を拒否する
     *
     * SDK はセッション ID をそのままファイル名に連結するため、パストラバーサルに悪用できる
     */
    public function testToMcpMessageRejectsInvalidSessionId()
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
                'HTTP_MCP_SESSION_ID' => '../../config/app_local',
            ],
        ]);
        $controller = new McpProxyController($request);
        $this->expectException(BadRequestException::class);
        $controller->toMcpMessage(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/list']);
    }

    /**
     * test toMcpMessage は妥当な Mcp-Session-Id を引き継ぐ
     */
    public function testToMcpMessageAcceptsValidSessionId()
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
                'HTTP_MCP_SESSION_ID' => 'abc-DEF_123',
            ],
        ]);
        $controller = new McpProxyController($request);
        $message = $controller->toMcpMessage(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/list']);
        $this->assertEquals('abc-DEF_123', $message->getHeader('Mcp-Session-Id'));
    }

    /**
     * test hasRequiredScope - 読み取り専用でないツールには mcp:write が必要
     */
    public function testHasRequiredScope()
    {
        $controller = new McpProxyController(new ServerRequest());
        $this->assertTrue($controller->hasRequiredScope('GET', ['mcp:read']));
        $this->assertTrue($controller->hasRequiredScope('GET', ['mcp:write']));
        $this->assertFalse($controller->hasRequiredScope('GET', []));
        $this->assertFalse($controller->hasRequiredScope('POST', ['mcp:read']));
        $this->assertTrue($controller->hasRequiredScope('POST', ['mcp:read', 'mcp:write']));
        $this->assertFalse($controller->hasRequiredScope('POST', []));
        // JWT の claim は空白区切りの文字列
        $this->assertTrue($controller->hasRequiredScope('POST', 'mcp:read mcp:write'));
        $this->assertFalse($controller->hasRequiredScope('POST', 'mcp:read'));
        $this->assertFalse($controller->hasRequiredScope('GET', ''));
        $this->assertFalse($controller->hasRequiredScope('GET', null));
    }

    /**
     * test hasApiAdminPrefix - グループの auth_prefix に Api/Admin が必要
     */
    public function testHasApiAdminPrefix()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        UserGroupFactory::make(['id' => 2, 'name' => 'admin_only', 'title' => 'admin only', 'auth_prefix' => 'Admin'])->persist();
        UserFactory::make(['id' => 2, 'name' => 'adminonly', 'email' => 'adminonly@example.com', 'status' => true])->persist();
        UsersUserGroupFactory::make(['user_id' => 2, 'user_group_id' => 2])->persist();
        UserGroupFactory::make(['id' => 3, 'name' => 'api_ok', 'title' => 'api ok', 'auth_prefix' => 'Admin,Api/Admin'])->persist();
        UserFactory::make(['id' => 3, 'name' => 'apiok', 'email' => 'apiok@example.com', 'status' => true])->persist();
        UsersUserGroupFactory::make(['user_id' => 3, 'user_group_id' => 3])->persist();

        $controller = new McpProxyController(new ServerRequest());
        $users = TableRegistry::getTableLocator()->get('BaserCore.Users');
        $this->assertTrue($controller->hasApiAdminPrefix($users->get(1, contain: ['UserGroups'])));
        $this->assertFalse($controller->hasApiAdminPrefix($users->get(2, contain: ['UserGroups'])));
        $this->assertTrue($controller->hasApiAdminPrefix($users->get(3, contain: ['UserGroups'])));
    }

    /**
     * test toMcpMessage は存在しないヘッダを引き継がない
     */
    public function testToMcpMessageOmitsAbsentHeaders()
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
                'HTTP_MCP_METHOD' => 'tools/list',
            ],
        ]);
        $controller = new McpProxyController($request);

        $message = $controller->toMcpMessage(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/list']);

        $this->assertEquals('tools/list', $message->getHeader('Mcp-Method'));
        $this->assertNull($message->getHeader('Mcp-Name'));
    }

    /**
     * test toMcpMessage はボディを改変しない
     *
     * 2026-07-28 ではヘッダとボディの一致が検証されるため、
     * ログインユーザーの注入などでボディを書き換えてはならない
     */
    public function testToMcpMessageKeepsBodyIntact()
    {
        $request = new ServerRequest(['environment' => ['REQUEST_METHOD' => 'POST']]);
        $controller = new McpProxyController($request);
        $mcpRequest = [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => ['name' => 'addBlogPost', 'arguments' => ['title' => 'テスト']],
        ];

        $message = $controller->toMcpMessage($mcpRequest);

        $this->assertEquals($mcpRequest, json_decode((string)$message->getBody(), true));
    }

    /**
     * test GET と DELETE は 405 を返す
     *
     * 2026-07-28 では GET ストリームが廃止されている
     */
    public function testGetReturnsMethodNotAllowed()
    {
        $this->get('/bc-mcp');
        $this->assertResponseCode(405);

        $this->delete('/bc-mcp');
        $this->assertResponseCode(405);
    }

    /**
     * test 許可オリジンの判定
     */
    public function testIsAllowedOrigin()
    {
        Configure::write('BcMcp.allowedOrigins', ['https://claude.ai']);
        $controller = new McpProxyController(new ServerRequest());

        $this->assertTrue($controller->isAllowedOrigin('https://claude.ai'));
        $this->assertFalse($controller->isAllowedOrigin('https://evil.example.com'));
        // 部分一致で通してはならない
        $this->assertFalse($controller->isAllowedOrigin('https://claude.ai.evil.example.com'));
    }

    /**
     * test 設定が空の場合は自サイトのオリジンのみを許可する
     */
    public function testIsAllowedOriginFallbackToSiteUrl()
    {
        Configure::write('BcMcp.allowedOrigins', []);
        $controller = new McpProxyController(new ServerRequest());

        $siteUrl = rtrim((string)env('SITE_URL', ''), '/');
        if ($siteUrl) {
            $parts = parse_url($siteUrl);
            $origin = $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port'])? ':' . $parts['port'] : '');
            $this->assertTrue($controller->isAllowedOrigin($origin));
        }
        $this->assertFalse($controller->isAllowedOrigin('https://evil.example.com'));
    }

    /**
     * test 許可されないオリジンからのリクエストは 403 になる
     *
     * Origin 検証は DNS リバインディング対策であり、認証より前に効かせる。
     * そのため認証エラーの 401 ではなく 403 が返る
     */
    public function testDisallowedOriginReturnsForbidden()
    {
        Configure::write('BcMcp.allowedOrigins', ['https://claude.ai']);

        $this->configRequest([
            'headers' => [
                'Origin' => 'https://evil.example.com',
                'Content-Type' => 'application/json',
            ]
        ]);
        $this->post('/bc-mcp', json_encode(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/list']));

        $this->assertResponseCode(403);
    }

    /**
     * test Origin ヘッダが無いリクエストは検証対象外
     *
     * サーバー間通信では Origin が送られないため通す（認証で弾かれる）
     */
    public function testRequestWithoutOriginIsNotBlocked()
    {
        Configure::write('BcMcp.allowedOrigins', ['https://claude.ai']);

        $this->configRequest(['headers' => ['Content-Type' => 'application/json']]);
        $this->post('/bc-mcp', json_encode(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/list']));

        // Origin 検証では弾かれず、認証エラーになる
        $this->assertResponseCode(401);
    }

}
