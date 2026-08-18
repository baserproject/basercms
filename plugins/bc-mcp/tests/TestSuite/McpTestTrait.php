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

namespace BcMcp\Test\TestSuite;

use BcMcp\Mcp\McpRequestHandler;
use Mcp\Server\Transport\Http\HttpMessage;

/**
 * MCP サーバーをプロセス内で実行するテスト用ヘルパ
 *
 * 本番と同じ McpRequestHandler を経由するため、テストが実装の実経路を検証する。
 * Modern（2026-07-28）と Legacy（initialize 方式）のどちらのリクエストも実行できる。
 */
trait McpTestTrait
{

    /**
     * Modern リクエストの _meta を取得する
     *
     * @param string $protocolVersion プロトコルバージョン
     * @return array
     */
    protected function modernMeta(string $protocolVersion = '2026-07-28'): array
    {
        return [
            'io.modelcontextprotocol/protocolVersion' => $protocolVersion,
            'io.modelcontextprotocol/clientInfo' => [
                'name' => 'BcMcpTestClient',
                'version' => '1.0.0',
            ],
            'io.modelcontextprotocol/clientCapabilities' => [],
        ];
    }

    /**
     * JSON-RPC リクエストをプロセス内で実行する
     *
     * @param array $request JSON-RPC リクエスト
     * @param array $headers HTTP ヘッダ（Modern の必須ヘッダを渡す）
     * @return array デコード済みのレスポンス
     */
    protected function callMcp(array $request, array $headers = []): array
    {
        $response = $this->callMcpRaw($request, $headers);
        return json_decode((string)$response->getBody(), true) ?? [];
    }

    /**
     * JSON-RPC リクエストを実行して HttpMessage を得る
     *
     * ステータスコードやヘッダを検証したい場合に使う。
     *
     * @param array $request JSON-RPC リクエスト
     * @param array $headers HTTP ヘッダ
     * @return \Mcp\Server\Transport\Http\HttpMessage
     */
    protected function callMcpRaw(array $request, array $headers = []): HttpMessage
    {
        $message = new HttpMessage(json_encode($request, JSON_UNESCAPED_UNICODE));
        $message->setMethod('POST');
        $message->setUri('/bc-mcp');
        $message->setHeader('Content-Type', 'application/json');
        $message->setHeader('Accept', 'application/json, text/event-stream');
        foreach($headers as $name => $value) {
            $message->setHeader($name, $value);
        }
        return (new McpRequestHandler())->handle($message);
    }

    /**
     * tools/call を実行する
     *
     * @param string $name ツール名
     * @param array $arguments 引数
     * @return array [デコード済みの戻り値, エラーかどうか]
     */
    protected function callMcpTool(string $name, array $arguments): array
    {
        $response = $this->callMcp([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'tools/call',
            'params' => [
                'name' => $name,
                'arguments' => $arguments,
                '_meta' => $this->modernMeta(),
            ],
        ], [
            'MCP-Protocol-Version' => '2026-07-28',
            'Mcp-Method' => 'tools/call',
            'Mcp-Name' => $name,
        ]);

        $text = $response['result']['content'][0]['text'] ?? '';
        $isError = $response['result']['isError'] ?? isset($response['error']);
        return [json_decode($text, true) ?? $text, (bool)$isError];
    }

}
