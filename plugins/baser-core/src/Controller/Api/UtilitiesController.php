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

namespace BaserCore\Controller\Api;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Error\BcException;
use BaserCore\Service\UtilitiesServiceInterface;
use BaserCore\Utility\BcUtil;

/**
 * Class UtilitiesController
 */
class UtilitiesController extends BcApiController
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
            'message' => __d('baser', 'サーバーキャッシュを削除しました。')
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

        if ($service->resetContentsTree()) {
            $message = __d('baser', 'コンテンツのツリー構造をリセットしました。');
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'コンテンツのツリー構造のリセットに失敗しました。');
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

        if ($service->verityContentsTree()) {
            $message = __d('baser', 'コンテンツのツリー構造に問題はありません。');
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'コンテンツのツリー構造に問題があります。ログを確認してください。');
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
                $message = __d('baser', 'バックアップダウンロードが失敗しました。');
            } else {
                $this->autoRender = false;
                $result->download('baserbackup_' . str_replace(' ', '_', BcUtil::getVersion()) . '_' . date('Ymd_His'));
                $service->resetTmpSchemaFolder();
                return;
            }

        } catch (\Exception $exception) {
            $message = __d('baser', 'バックアップダウンロードが失敗しました。' . $exception->getMessage());
            $this->setResponse($this->response->withStatus(400));
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
            $message = __d('baser', 'データの復元が完了しました。');
        } catch (BcException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'データの復元に失敗しました。ログの確認を行なって下さい。') . $e->getMessage();
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
            $message = __d('baser', 'エラーログが存在しません。');
        } catch (\Exception $exception) {
            $message = __d('baser', 'ログファイルダウンロードが失敗しました。' . $exception->getMessage());
            $this->setResponse($this->response->withStatus(400));
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
            $message = __d('baser', 'エラーログを削除しました。');
        } catch (BcException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'エラーログをを削除できません。') . $e->getMessage();
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
