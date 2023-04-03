<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Controller\Api\Admin;

use BaserCore\Service\UtilitiesServiceInterface;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UtilitiesController
 */
class UtilitiesController extends BcAdminApiController
{

    /**
     * [API] サーバーキャッシュを削除する
     *
     * @checked
     * @unitTest
     * @noTodo
     */
    public function clear_cache()
    {
        BcUtil::clearAllCache();

        $this->set([
            'message' => __d('baser_core', 'サーバーキャッシュを削除しました。')
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }


    /**
     * [API] ユーティリティ：ツリー構造リセット
     * @param UtilitiesServiceInterface $service
     * @checked
     * @noTodo
     */
    public function reset_contents_tree(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        try {
            if ($service->resetContentsTree()) {
                $message = __d('baser_core', 'コンテンツのツリー構造をリセットしました。');
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'コンテンツのツリー構造のリセットに失敗しました。');
            }
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * [API] ユーティリティ：ツリー構造チェック
     *
     * @param UtilitiesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function verity_contents_tree(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        try {
            if ($service->verityContentsTree()) {
                $message = __d('baser_core', 'コンテンツのツリー構造に問題はありません。');
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'コンテンツのツリー構造に問題があります。ログを確認してください。');
            }
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * [API] ユーティリティ：バックアップダウンロード
     *
     * @param UtilitiesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function download_backup(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        try {
            $result = $service->backupDb($this->request->getQuery('backup_encoding'));
            if (!$result) {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'バックアップダウンロードが失敗しました。');
            } else {
                $this->autoRender = false;
                $result->download('baserbackup_' . str_replace(' ', '_', BcUtil::getVersion()) . '_' . date('Ymd_His'));
                $service->resetTmpSchemaFolder();
                return;
            }
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }


    /**
     * [API] ユーティリティ：バックアップよりレストア
     *
     * @param UtilitiesServiceInterface $service
     * @checked
     * @noTodo
     */
    public function restore_db(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        try {
            $service->restoreDb($this->getRequest()->getData(), $this->getRequest()->getUploadedFiles());
            $message = __d('baser_core', 'データの復元が完了しました。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * [API] ユーティリティ：ログファイルダウンロード
     * @param UtilitiesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function download_log(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        try {
            $this->autoRender = false;
            $result = $service->createLogZip();

            if ($result) {
                $result->download('basercms_logs_' . date('Ymd_His'));
                return;
            }

            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser_core', 'エラーログが存在しません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * [API] ユーティリティ：ログファイルを削除
     *
     * @param UtilitiesServiceInterface $service
     * @checked
     * @noTodo
     */
    public function delete_log(UtilitiesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        try {
            $service->deleteLog();
            $message = __d('baser_core', 'エラーログを削除しました。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /**
     * 検索ボックスの表示状態を保存する
     *
     * @param string $key キー
     * @param mixed $open 1 Or ''
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function save_search_opened($key, $open = '')
    {
        $this->request->allowMethod(['post']);
        $this->request->getSession()->write('BcApp.adminSearchOpened.' . $key, $open);
        $this->set([
            'result' => true
        ]);
        $this->viewBuilder()->setOption('serialize', ['result']);
    }

}
