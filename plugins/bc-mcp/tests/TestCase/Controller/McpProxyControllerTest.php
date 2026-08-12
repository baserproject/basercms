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

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Controller\McpProxyController;
use Cake\Http\ServerRequest;

/**
 * McpProxyControllerTest
 */
class McpProxyControllerTest extends BcTestCase
{

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

}
