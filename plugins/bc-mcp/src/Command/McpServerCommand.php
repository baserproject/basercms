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
            ->setDescription('baserCMS MCP サーバーを標準入出力で起動します')
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

            // HTTP 経由の利用は /bc-mcp エンドポイントが担う。常駐プロセスを立てず
            // CakePHP のリクエスト内で処理するため、コマンドは標準入出力のみを提供する。
            $io->out('STDIO モードで起動中...');
            $io->out('クライアントからの接続を待機しています...');
            $server->runStdio();

        } catch (\Exception $e) {
            $io->error('MCPサーバーの起動中にエラーが発生しました:');
            $io->error($e->getMessage());
            $io->error($e->getTraceAsString());
            return self::CODE_ERROR;
        }

        return self::CODE_SUCCESS;
    }
}
