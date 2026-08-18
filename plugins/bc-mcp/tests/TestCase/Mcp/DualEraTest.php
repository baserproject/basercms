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

namespace BcMcp\Test\TestCase\Mcp;

use BaserCore\TestSuite\BcTestCase;
use BcMcp\Test\TestSuite\McpTestTrait;

/**
 * DualEraTest
 *
 * Modern（2026-07-28）と Legacy（initialize 方式）の両世代が同一サーバーで
 * 動作することを検証する。本移植の受け入れテストにあたる。
 */
class DualEraTest extends BcTestCase
{

    use McpTestTrait;

    /**
     * test server/discover が対応バージョンと capabilities を返す
     *
     * 2026-07-28 ではサーバーの実装が MUST とされている
     */
    public function testServerDiscover()
    {
        $response = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'server/discover',
            'params' => ['_meta' => $this->modernMeta()],
        ], [
            'MCP-Protocol-Version' => '2026-07-28',
            'Mcp-Method' => 'server/discover',
        ]);

        $this->assertArrayNotHasKey('error', $response, json_encode($response, JSON_UNESCAPED_UNICODE));

        // 対応するプロトコルバージョンを列挙する
        $this->assertContains('2026-07-28', $response['result']['supportedVersions']);
        $this->assertArrayHasKey('capabilities', $response['result']);
        // tools を提供している事が申告される
        $this->assertArrayHasKey('tools', $response['result']['capabilities']);

        // サーバーの識別情報は _meta の io.modelcontextprotocol/serverInfo に入る（仕様どおり）
        $serverInfo = $response['result']['_meta']['io.modelcontextprotocol/serverInfo'] ?? null;
        $this->assertNotNull($serverInfo, json_encode($response['result'], JSON_UNESCAPED_UNICODE));
        $this->assertEquals('baserCMS MCP Server', $serverInfo['name']);

        // 一覧結果にはキャッシュヒントが付与される
        $this->assertArrayHasKey('ttlMs', $response['result']);
        $this->assertArrayHasKey('cacheScope', $response['result']);
    }

    /**
     * test Modern で tools/call が実行できる
     */
    public function testModernToolsCall()
    {
        $response = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'serverInfo',
                'arguments' => [],
                '_meta' => $this->modernMeta(),
            ],
        ], [
            'MCP-Protocol-Version' => '2026-07-28',
            'Mcp-Method' => 'tools/call',
            'Mcp-Name' => 'serverInfo',
        ]);

        $this->assertArrayNotHasKey('error', $response, json_encode($response, JSON_UNESCAPED_UNICODE));
        // 2026-07-28 の必須項目
        $this->assertEquals('complete', $response['result']['resultType']);
        $this->assertNotEmpty($response['result']['content']);
    }

    /**
     * test Legacy の initialize がセッションを払い出し、tools/call まで通る
     *
     * Legacy 世代はセッションを必要とするため、initialize で払い出された
     * Mcp-Session-Id を以降のリクエストに付けるのが正規のフローになる
     */
    public function testLegacyFlow()
    {
        $init = $this->callMcpRaw([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2025-06-18',
                'capabilities' => [],
                'clientInfo' => ['name' => 'LegacyTestClient', 'version' => '1.0.0'],
            ],
        ], ['MCP-Protocol-Version' => '2025-06-18', 'Mcp-Method' => 'initialize']);

        $this->assertEquals(200, $init->getStatusCode());
        $initResult = json_decode((string)$init->getBody(), true);
        $this->assertArrayHasKey('protocolVersion', $initResult['result']);
        $this->assertArrayHasKey('capabilities', $initResult['result']);

        // セッションIDが払い出される
        $sessionId = $init->getHeader('Mcp-Session-Id');
        $this->assertNotEmpty($sessionId, 'Legacy 世代では Mcp-Session-Id が払い出される');

        // 払い出されたセッションIDで tools/list が通る
        $list = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 2,
            'method' => 'tools/list',
        ], ['Mcp-Session-Id' => $sessionId]);
        $this->assertArrayNotHasKey('error', $list, json_encode($list, JSON_UNESCAPED_UNICODE));
        $this->assertContains('addBlogPost', array_column($list['result']['tools'], 'name'));

        // 同じセッションで tools/call も通る
        $call = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 3,
            'method' => 'tools/call',
            'params' => ['name' => 'serverInfo', 'arguments' => []],
        ], ['Mcp-Session-Id' => $sessionId]);
        $this->assertArrayNotHasKey('error', $call, json_encode($call, JSON_UNESCAPED_UNICODE));
        $this->assertNotEmpty($call['result']['content']);
    }

    /**
     * test Legacy はセッションID無しでは拒否される
     */
    public function testLegacyRequiresSession()
    {
        $response = $this->callMcpRaw([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/list',
        ]);

        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * test 未対応バージョンは UnsupportedProtocolVersionError になる
     */
    public function testUnsupportedProtocolVersion()
    {
        $response = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/list',
            'params' => ['_meta' => $this->modernMeta('1900-01-01')],
        ], [
            'MCP-Protocol-Version' => '1900-01-01',
            'Mcp-Method' => 'tools/list',
        ]);

        $this->assertEquals(-32022, $response['error']['code']);
        $this->assertArrayHasKey('supported', $response['error']['data']);
    }

    /**
     * test ヘッダとボディの不一致は HeaderMismatch になる
     */
    public function testHeaderMismatch()
    {
        $response = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => 'serverInfo',
                'arguments' => [],
                '_meta' => $this->modernMeta(),
            ],
        ], [
            'MCP-Protocol-Version' => '2026-07-28',
            'Mcp-Method' => 'tools/call',
            // ボディの params.name と一致しない
            'Mcp-Name' => 'getBlogPosts',
        ]);

        $this->assertEquals(-32020, $response['error']['code']);
    }

}
