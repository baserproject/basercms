<?php
declare(strict_types=1);

namespace BcMcp\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use BcMcp\Mcp\McpServer;

/**
 * MCPサーバー起動コマンド
 */
class McpServerCommand extends Command
{

    /**
     * コマンドの説明を設定
     *
     * @param \Cake\Console\ConsoleOptionParser $parser
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('baserCMS MCP サーバーを起動します')
            ->addOption('transport', [
                'short' => 't',
                'help' => 'トランスポートタイプ (stdio, sse)',
                'default' => 'stdio'
            ])
            ->addOption('host', [
                'help' => 'SSEモード時のホスト名',
                'default' => '127.0.0.1'
            ])
            ->addOption('port', [
                'short' => 'p',
                'help' => 'SSEモード時のポート番号',
                'default' => '3000'
            ])
            ->addOption('config', [
                'short' => 'c',
                'help' => '設定ファイルのパス',
                'default' => null
            ])
            ->addOption('connection', [
                'help' => 'サーバーが使用する DB 接続名。default 以外を指定すると default にエイリアスする（主にテストで test 接続を使う用途）。'
                    . 'プラグインのロード自体は bootstrap で環境変数 BC_CONNECTION により切り替わる。',
                'default' => 'default'
            ]);

        return $parser;
    }

    /**
     * コマンドの実行
     *
     * @param \Cake\Console\Arguments $args
     * @param \Cake\Console\ConsoleIo $io
     * @return int|null|void
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->out('baserCMS MCP サーバーを起動しています...');

        $transport = $args->getOption('transport');
        $host = $args->getOption('host');
        $port = (int)$args->getOption('port');
        $configPath = $args->getOption('config');
        $connection = (string)$args->getOption('connection');

        // default 以外の接続が指定された場合は default にエイリアスし、サーバーの全 DB 操作を
        // その接続へ向ける。bootstrap 時の env(BC_MCP_CONNECTION) と同じ切替を冪等に行う保険。
        if ($connection !== '' && $connection !== 'default' && \Cake\Datasource\ConnectionManager::getConfig($connection)) {
            \Cake\Datasource\ConnectionManager::alias($connection, 'default');
            $io->out("DB 接続: {$connection}（default にエイリアス）");
        }

        try {
            // MCPサーバーのインスタンス作成
            $server = new McpServer();

            // 設定ファイルがある場合は読み込み
            if ($configPath && file_exists($configPath)) {
                $config = require $configPath;
                $server->setConfig($config);
            }

            $io->out("Transport: {$transport}");

            if ($transport === 'stdio') {
                $io->out('STDIO モードで起動中...');
                $io->out('クライアントからの接続を待機しています...');

                // STDIOモードで実行
                $server->runStdio();
            } elseif ($transport === 'sse') {
                $io->out("SSE モードで起動中... (http://{$host}:{$port})");

                // SSEモードで実行
                $server->runSse($host, $port);
            } else {
                $io->error("サポートされていないトランスポートタイプ: {$transport}");
                return self::CODE_ERROR;
            }

        } catch (\Exception $e) {
            $io->error('MCPサーバーの起動中にエラーが発生しました:');
            $io->error($e->getMessage());
            $io->error($e->getTraceAsString());
            return self::CODE_ERROR;
        }

        return self::CODE_SUCCESS;
    }
}
