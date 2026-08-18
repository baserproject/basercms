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
use BcMcp\Mcp\NegotiationLogger;

/**
 * NegotiationLoggerTest
 */
class NegotiationLoggerTest extends BcTestCase
{

    /**
     * test Modern リクエストを Modern と判定する
     */
    public function testDescribeModern()
    {
        $result = NegotiationLogger::describe([
            'method' => 'tools/call',
            'params' => [
                'name' => 'addBlogPost',
                '_meta' => [
                    'io.modelcontextprotocol/protocolVersion' => '2026-07-28',
                    'io.modelcontextprotocol/clientInfo' => ['name' => 'claude-ai', 'version' => '2.0.0'],
                ],
            ],
        ], '2026-07-28');

        $this->assertEquals('modern', $result['era']);
        $this->assertEquals('2026-07-28', $result['protocolVersion']);
        $this->assertEquals('claude-ai', $result['clientName']);
        $this->assertEquals('2.0.0', $result['clientVersion']);
        $this->assertEquals('tools/call', $result['method']);
    }

    /**
     * test Legacy の initialize を Legacy と判定する
     */
    public function testDescribeLegacy()
    {
        $result = NegotiationLogger::describe([
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2025-06-18',
                'clientInfo' => ['name' => 'legacy-client', 'version' => '1.0.0'],
            ],
        ], '');

        $this->assertEquals('legacy', $result['era']);
        $this->assertEquals('2025-06-18', $result['protocolVersion']);
        $this->assertEquals('legacy-client', $result['clientName']);
    }

    /**
     * test _meta を持たないリクエストはヘッダのバージョンを使う
     */
    public function testDescribeLegacyWithHeaderOnly()
    {
        $result = NegotiationLogger::describe([
            'method' => 'tools/list',
            'params' => [],
        ], '2025-03-26');

        $this->assertEquals('legacy', $result['era']);
        $this->assertEquals('2025-03-26', $result['protocolVersion']);
        $this->assertEquals('', $result['clientName']);
    }

    /**
     * test 引数の中身は記録対象に含まれない
     *
     * 機密情報がログに混入するのを防ぐ
     */
    public function testDescribeOmitsArguments()
    {
        $result = NegotiationLogger::describe([
            'method' => 'tools/call',
            'params' => [
                'name' => 'addBlogPost',
                'arguments' => ['title' => '秘密の記事'],
                '_meta' => ['io.modelcontextprotocol/protocolVersion' => '2026-07-28'],
            ],
        ], '2026-07-28');

        $this->assertStringNotContainsString('秘密の記事', json_encode($result, JSON_UNESCAPED_UNICODE));
        $this->assertArrayNotHasKey('arguments', $result);
    }

    /**
     * test readRecent が記録した接続状況を新しい順に読み出す
     *
     * 管理画面の「直近の接続状況」で使う
     */
    public function testReadRecent()
    {
        $logFile = TMP . 'test_mcp_negotiation.log';
        if (file_exists($logFile)) {
            unlink($logFile);
        }
        file_put_contents($logFile, implode("\n", [
            '2026-08-12 10:00:00 info: MCP negotiation: era=legacy protocolVersion=2025-06-18 client=old-client/1.0.0 method=initialize',
            '2026-08-12 11:00:00 info: MCP negotiation: era=modern protocolVersion=2026-07-28 client=claude-ai/2.0.0 method=tools/call',
        ]) . "\n");

        $recent = NegotiationLogger::readRecent(10, $logFile);

        // 新しい順に返る
        $this->assertCount(2, $recent);
        $this->assertEquals('modern', $recent[0]['era']);
        $this->assertEquals('2026-07-28', $recent[0]['protocolVersion']);
        $this->assertEquals('claude-ai', $recent[0]['clientName']);
        $this->assertEquals('2.0.0', $recent[0]['clientVersion']);
        $this->assertEquals('tools/call', $recent[0]['method']);
        $this->assertEquals('2026-08-12 11:00:00', $recent[0]['loggedAt']);
        $this->assertEquals('legacy', $recent[1]['era']);

        unlink($logFile);
    }

    /**
     * test readRecent は件数を制限する
     */
    public function testReadRecentLimit()
    {
        $logFile = TMP . 'test_mcp_negotiation_limit.log';
        $lines = [];
        for($i = 0; $i < 5; $i++) {
            $lines[] = sprintf(
                '2026-08-12 1%d:00:00 info: MCP negotiation: era=modern protocolVersion=2026-07-28 client=claude-ai/2.0.0 method=tools/list',
                $i
            );
        }
        file_put_contents($logFile, implode("\n", $lines) . "\n");

        $this->assertCount(2, NegotiationLogger::readRecent(2, $logFile));

        unlink($logFile);
    }

    /**
     * test readRecent はログが無い場合に空配列を返す
     */
    public function testReadRecentWithoutLog()
    {
        $this->assertSame([], NegotiationLogger::readRecent(10, TMP . 'not_exists_mcp.log'));
    }

    /**
     * test log で記録した内容が readRecent で読み出せる
     */
    public function testLogAndReadRecent()
    {
        $logFile = LOGS . 'mcp.log';
        $before = is_file($logFile) ? (string)file_get_contents($logFile) : '';

        NegotiationLogger::log([
            'method' => 'tools/list',
            'params' => [
                '_meta' => [
                    'io.modelcontextprotocol/protocolVersion' => '2026-07-28',
                    'io.modelcontextprotocol/clientInfo' => ['name' => 'log-test-client', 'version' => '3.0.0'],
                ],
            ],
        ], '2026-07-28');

        $recent = NegotiationLogger::readRecent(1);

        $this->assertNotEmpty($recent);
        $this->assertEquals('modern', $recent[0]['era']);
        $this->assertEquals('log-test-client', $recent[0]['clientName']);
        $this->assertNotEmpty($recent[0]['loggedAt']);

        // テストで追記した分を元に戻す
        file_put_contents($logFile, $before);
    }

}
