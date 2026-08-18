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

use Cake\Core\Configure;
use Mcp\Server\HttpServerRunner;
use Mcp\Server\Transport\Http\BufferedIo;
use Mcp\Server\Transport\Http\FileSessionStore;
use Mcp\Server\Transport\Http\HttpMessage;

/**
 * MCP リクエストをプロセス内で処理する
 *
 * SDK の HTTP トランスポートは「1リクエストを処理して終わる」モデルであり、
 * 常駐プロセスを必要としない。BufferedIo により出力が SAPI へ直接書き出される
 * のを防ぎ、レスポンスを CakePHP のレスポンスに載せられるようにする。
 *
 * 本番（McpProxyController）とテストがこの経路を共有する。
 */
class McpRequestHandler
{

    /**
     * MCP リクエストを処理する
     *
     * @param \Mcp\Server\Transport\Http\HttpMessage $request リクエスト
     * @return \Mcp\Server\Transport\Http\HttpMessage レスポンス
     */
    public function handle(HttpMessage $request): HttpMessage
    {
        $logger = new McpLogger(LOGS . 'bc_mcp_error.log');
        $coreServer = (new McpServer())->getServer()->getServer();

        $runner = new HttpServerRunner(
            $coreServer,
            $coreServer->createInitializationOptions(),
            $this->getHttpOptions(),
            $logger,
            new FileSessionStore($this->getSessionStorePath()),
            new BufferedIo()
        );

        return $runner->handleRequest($request);
    }

    /**
     * HTTP トランスポートのオプションを取得する
     *
     * allowed_origins は SDK 側の DNS リバインディング対策。
     * プロキシでも検証しているため二重に効かせる。
     *
     * @return array
     */
    public function getHttpOptions(): array
    {
        $options = [];
        $allowedOrigins = (array)Configure::read('BcMcp.allowedOrigins', []);
        if ($allowedOrigins) {
            $options['allowed_origins'] = $allowedOrigins;
        }
        return $options;
    }

    /**
     * Legacy セッションの保存先を取得する
     *
     * Modern（2026-07-28）はセッションを使わないが、Legacy 世代のクライアントは
     * セッションを必要とするためディスクへ永続する。
     *
     * @return string
     */
    public function getSessionStorePath(): string
    {
        $path = TMP . 'bc_mcp_sessions';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

}
