<?php
declare(strict_types=1);

namespace BcMcp\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BcMcp\Mcp\McpServerManger;

/**
 * MCPサーバー管理コントローラー
 * 管理画面からMCPサーバーの起動・停止・設定を行う
 */
class McpServerManagerController extends BcAdminAppController
{

    /**
     * McpServerManger
     * @var McpServerManger
     */
    private McpServerManger $mcpServerManager;

    /**
     * 初期化
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->set('title', 'MCPサーバー管理');
        $this->mcpServerManager = new McpServerManger();
    }

    /**
     * MCPサーバー管理画面
     */
    public function index()
    {
        $status = $this->mcpServerManager->getServerStatus();
        $config = $this->mcpServerManager->getServerConfig();

        $this->set(compact('status', 'config'));
    }

    /**
     * MCPサーバー起動
     */
    public function start()
    {
        $this->request->allowMethod(['post']);

        try {
            if ($this->mcpServerManager->isServerRunning()) {
                $this->BcMessage->setError('MCPサーバーは既に起動しています');
                return $this->redirect(['action' => 'index']);
            }

            $config = $this->mcpServerManager->getServerConfig();
            $result = $this->mcpServerManager->startMcpServer($config);

            if ($result['success']) {
                $this->BcMessage->setSuccess('MCPサーバーを起動しました');
            } else {
                $this->BcMessage->setError('MCPサーバーの起動に失敗しました: ' . $result['message']);
            }

        } catch (\Exception $e) {
            $this->BcMessage->setError('MCPサーバーの起動中にエラーが発生しました: ' . $e->getMessage());
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * MCPサーバー停止
     */
    public function stop()
    {
        $this->request->allowMethod(['post']);

        try {
            $result = $this->mcpServerManager->stopMcpServer();

            if ($result['success']) {
                $this->BcMessage->setSuccess('MCPサーバーを停止しました');
            } else {
                $this->BcMessage->setError('MCPサーバーの停止に失敗しました: ' . $result['message']);
            }

        } catch (\Exception $e) {
            $this->BcMessage->setError('MCPサーバーの停止中にエラーが発生しました: ' . $e->getMessage());
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * MCPサーバー再起動
     */
    public function restart()
    {
        $this->request->allowMethod(['post']);

        try {
            // 停止
            if ($this->mcpServerManager->isServerRunning()) {
                $this->mcpServerManager->stopMcpServer();
                sleep(2); // 少し待機
            }

            // 起動
            $config = $this->mcpServerManager->getServerConfig();
            $result = $this->mcpServerManager->startMcpServer($config);

            if ($result['success']) {
                $this->BcMessage->setSuccess('MCPサーバーを再起動しました');
            } else {
                $this->BcMessage->setError('MCPサーバーの再起動に失敗しました: ' . $result['message']);
            }

        } catch (\Exception $e) {
            $this->BcMessage->setError('MCPサーバーの再起動中にエラーが発生しました: ' . $e->getMessage());
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * 設定画面
     */
    public function configure()
    {
        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            try {
                $this->mcpServerManager->saveServerConfig($data);
                $this->BcMessage->setSuccess('設定を保存しました');
                return $this->redirect(['action' => 'index']);

            } catch (\Exception $e) {
                $this->BcMessage->setError('設定の保存に失敗しました: ' . $e->getMessage());
            }
        }

        $config = $this->mcpServerManager->getServerConfig();
        $this->set(compact('config'));
    }

}
