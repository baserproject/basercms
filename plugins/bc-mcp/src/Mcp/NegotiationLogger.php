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

namespace BcMcp\Mcp;

use Cake\Log\Log;

/**
 * MCP のネゴシエーション内容を記録する
 *
 * クライアントがどの世代（Modern / Legacy）でどのプロトコルバージョンを
 * 要求してきたかを残す事で、クライアント側の移行を検知できるようにする。
 * 常駐プロセスの死活監視が不要になった代わりに、これが運用時の主要な
 * 確認手段になる。
 *
 * 引数やトークンの中身は記録しない。
 */
class NegotiationLogger
{

    /**
     * 記録する内容を組み立てる
     *
     * Modern（2026-07-28 以降）はリクエストごとの _meta でバージョンを伝え、
     * Legacy は initialize の params でバージョンを伝える。
     *
     * @param array $mcpRequest MCP リクエスト
     * @param string $protocolVersionHeader MCP-Protocol-Version ヘッダの値
     * @return array
     */
    public static function describe(array $mcpRequest, string $protocolVersionHeader): array
    {
        $meta = $mcpRequest['params']['_meta'] ?? [];
        $isModern = isset($meta['io.modelcontextprotocol/protocolVersion']);

        if ($isModern) {
            $protocolVersion = $meta['io.modelcontextprotocol/protocolVersion'];
            $clientInfo = $meta['io.modelcontextprotocol/clientInfo'] ?? [];
        } else {
            $protocolVersion = $mcpRequest['params']['protocolVersion'] ?? $protocolVersionHeader;
            $clientInfo = $mcpRequest['params']['clientInfo'] ?? [];
        }

        return [
            'era' => $isModern? 'modern' : 'legacy',
            'protocolVersion' => (string)$protocolVersion,
            'clientName' => (string)($clientInfo['name'] ?? ''),
            'clientVersion' => (string)($clientInfo['version'] ?? ''),
            'method' => (string)($mcpRequest['method'] ?? ''),
        ];
    }

    /**
     * ネゴシエーション内容をログに記録する
     *
     * @param array $mcpRequest MCP リクエスト
     * @param string $protocolVersionHeader MCP-Protocol-Version ヘッダの値
     * @return void
     */
    public static function log(array $mcpRequest, string $protocolVersionHeader): void
    {
        $info = self::describe($mcpRequest, $protocolVersionHeader);
        Log::write('info', sprintf(
            'MCP negotiation: era=%s protocolVersion=%s client=%s/%s method=%s',
            $info['era'],
            $info['protocolVersion'],
            $info['clientName'],
            $info['clientVersion'],
            $info['method']
        // Log::write() は $context['scope'] でスコープを判定するため、
        // 配列を直接渡してはならない（scope が空になり mcp.log へ書かれない）
        ), ['scope' => ['mcp']]);
    }

    /**
     * 直近の接続状況をログから読み出す
     *
     * 管理画面で「クライアントがどの世代で接続しているか」を確認できるようにする。
     *
     * @param int $limit 取得件数
     * @param string|null $logFile ログファイルのパス（テスト用）
     * @return array 新しい順の接続状況
     */
    public static function readRecent(int $limit = 10, ?string $logFile = null): array
    {
        $logFile ??= LOGS . 'mcp.log';
        if (!is_readable($logFile)) {
            return [];
        }

        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return [];
        }

        $pattern = '/^(?<loggedAt>[\d\-]+ [\d:]+).*MCP negotiation: era=(?<era>\S+) '
            . 'protocolVersion=(?<protocolVersion>\S*) client=(?<clientName>[^\/]*)\/(?<clientVersion>\S*) '
            . 'method=(?<method>\S*)$/';

        $result = [];
        foreach(array_reverse($lines) as $line) {
            if (!str_contains($line, 'MCP negotiation:')) {
                continue;
            }
            if (!preg_match($pattern, $line, $matches)) {
                continue;
            }
            $result[] = [
                'loggedAt' => $matches['loggedAt'],
                'era' => $matches['era'],
                'protocolVersion' => $matches['protocolVersion'],
                'clientName' => $matches['clientName'],
                'clientVersion' => $matches['clientVersion'],
                'method' => $matches['method'],
            ];
            if (count($result) >= $limit) {
                break;
            }
        }
        return $result;
    }

}
