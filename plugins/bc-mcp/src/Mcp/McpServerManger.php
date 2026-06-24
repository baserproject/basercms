<?php

namespace BcMcp\Mcp;

use BaserCore\Utility\BcUtil;
use Cake\Routing\Router;

/**
 * MCPサーバーマネージャークラス
 *
 * MCPサーバーの起動、停止、状態確認、設定管理を行う
 */
class McpServerManger
{

    /**
     * MCPサーバーを起動
     */
    public function startMcpServer(array $config): array
    {
        try {
            $pidFile = $this->getPidFilePath();
            $logFile = $this->getLogFilePath();
            $cakeCommand = ROOT . DS . 'bin' . DS . 'cake';

            // 任意の DB 接続を指定された場合は、子プロセスのプラグインロード（bootstrap）に効く
            // 環境変数 BC_CONNECTION と、起動後の DB 操作に効く --connection オプションを併用する。
            $envPrefix = '';
            $connectionOption = '';
            if (!empty($config['connection']) && $config['connection'] !== 'default') {
                $envPrefix = 'BC_CONNECTION=' . escapeshellarg($config['connection']) . ' ';
                $connectionOption = ' --connection=' . escapeshellarg($config['connection']);
            }

            // バックグラウンドでMCPサーバーを起動。
            // nohup で SIGHUP から切り離しつつ、stdin/stdout/stderr をすべて親から切り離す
            // （stdin は /dev/null、stdout/stderr はログファイルへ）。これをしないと SSE 常駐プロセスが
            // 親プロセス（CI のステップ等）の継承 fd を掴んだままになり、出力パイプが EOF にならず
            // ジョブが次ステップへ進めずハングする。$! は nohup 実行なので php プロセスの PID と一致する。
            $command = sprintf(
                'cd %s && %snohup %s bc_mcp.server --transport=sse --host=%s --port=%s%s < /dev/null > %s 2>&1 & echo $! > %s',
                ROOT,
                $envPrefix,
                $cakeCommand,
                escapeshellarg($config['host']),
                escapeshellarg($config['port']),
                $connectionOption,
                escapeshellarg($logFile),
                escapeshellarg($pidFile)
            );

            shell_exec($command);

            // 起動確認（最大10秒待機）
            $attempts = 0;
            while($attempts < 20 && !$this->isServerRunning()) {
                usleep(500000); // 0.5秒待機
                $attempts++;
            }

            if ($this->isServerRunning()) {
                return ['success' => true, 'message' => 'MCPサーバーが正常に起動しました'];
            } else {
                $logContent = file_exists($logFile)? file_get_contents($logFile) : 'ログファイルが見つかりません';
                return ['success' => false, 'message' => 'サーバーの起動を確認できませんでした。ログ: ' . $logContent];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * MCPサーバーを停止
     */
    public function stopMcpServer(): array
    {
        try {
            $pidFile = $this->getPidFilePath();

            if (!file_exists($pidFile)) {
                return ['success' => false, 'message' => 'PIDファイルが見つかりません（サーバーが起動していない可能性があります）'];
            }

            $pid = trim(file_get_contents($pidFile));

            if (!$pid || !$this->isProcessRunning($pid)) {
                unlink($pidFile);
                return ['success' => false, 'message' => 'MCPサーバーのプロセスが見つかりません'];
            }

            // プロセスを停止
            shell_exec("kill {$pid} 2>&1");

            // 停止確認
            sleep(1);
            if (!$this->isProcessRunning($pid)) {
                unlink($pidFile);
                return ['success' => true, 'message' => 'MCPサーバーを正常に停止しました'];
            } else {
                // 強制終了
                shell_exec("kill -9 {$pid} 2>&1");
                unlink($pidFile);
                return ['success' => true, 'message' => 'MCPサーバーを強制終了しました'];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * サーバーの状態を取得
     */
    public function getServerStatus(): array
    {
        $pidFile = $this->getPidFilePath();
        $config = $this->getServerConfig();

        $isRunning = $this->isServerRunning();
        $pid = file_exists($pidFile)? trim(file_get_contents($pidFile)) : null;
        $request = Router::getRequest();
        $protocol = ($request->is('https'))? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return [
            'running' => $isRunning,
            'pid' => $pid,
            'proxy_url' => "{$protocol}://{$host}/bc-mcp",
            'internal_url' => "http://{$config['host']}:{$config['port']}",
            'config' => $config
        ];
    }

    /**
     * MCPサーバーが起動しているかチェック
     */
    public function isServerRunning(): bool
    {
        $pidFile = $this->getPidFilePath();

        if (!file_exists($pidFile)) {
            return false;
        }

        $pid = trim(file_get_contents($pidFile));
        return $pid && $this->isProcessRunning($pid);
    }

    /**
     * プロセスが実行中かチェック
     */
    public function isProcessRunning(string $pid): bool
    {
        $result = shell_exec("ps -p {$pid} 2>/dev/null");
        return !empty($result) && strpos($result, $pid) !== false;
    }

    /**
     * サーバー設定を取得
     */
    public function getServerConfig(): array
    {
        $configFile = $this->getConfigFilePath();

        $defaultConfig = [
            'host' => '127.0.0.1',
            'port' => '3000'
        ];

        if (file_exists($configFile)) {
            $savedConfig = json_decode(file_get_contents($configFile), true);
            return array_merge($defaultConfig, $savedConfig?: []);
        }

        return $defaultConfig;
    }

    /**
     * サーバー設定を保存
     */
    public function saveServerConfig(array $config): void
    {
        $configFile = $this->getConfigFilePath();
        $configDir = dirname($configFile);

        if (!is_dir($configDir)) {
            mkdir($configDir, 0755, true);
        }

        $jsonConfig = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($configFile, $jsonConfig);
    }

    /**
     * PIDファイルのパスを取得
     */
    private function getPidFilePath(): string
    {
        return TMP . 'bc_mcp_server.pid';
    }

    /**
     * ログファイルのパスを取得
     */
    private function getLogFilePath(): string
    {
        return LOGS . 'bc_mcp_server.log';
    }

    /**
     * 設定ファイルのパスを取得
     */
    private function getConfigFilePath(): string
    {
        return CONFIG . 'bc_mcp_server.json';
    }

}
