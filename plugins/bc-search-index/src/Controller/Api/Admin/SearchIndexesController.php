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

namespace BcSearchIndex\Controller\Api\Admin;

use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * SearchIndicesController
 */
class SearchIndexesController extends BcAdminApiController
{

    /**
     * Before filter
     * @param EventInterface $event
     * @return \Cake\Http\Response|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Security->setConfig('validatePost', false);
    }

    /**
     * [AJAX] 優先順位を変更する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function change_priority(SearchIndexesServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $searchIndex = null;
        try {
            $searchIndex = $service->get($id);
            $searchIndex = $service->changePriority(
                $searchIndex,
                $this->getRequest()->getData('priority')
            );
            $message = __d('baser_core', '検索インデックス「{0}」の優先度を変更しました。', $searchIndex->title);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'searchIndex' => $searchIndex,
        ]);
        $this->viewBuilder()->setOption('serialize', ['searchIndex', 'message']);
    }

    /**
     * バッチ処理
     *
     * @param SearchIndexesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(SearchIndexesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => __d('baser_core', '削除')
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        try {
            $service->batch($method, $this->getRequest()->getData('batch_targets'));
            $this->BcMessage->setSuccess(
                sprintf(__d('baser_core', '検索インデックスより NO.%s を %s しました。'), implode(', ', $this->getRequest()->getData('batch_targets')), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser_core', '一括処理が完了しました。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

    /***
     * [API] 検索インデックスを再構築する
     * @param SearchIndexesServiceInterface $searchIndexesService
     *
     * @noTodo
     * @checked
     * @unitTest
     */
    public function reconstruct(SearchIndexesServiceInterface $searchIndexesService)
    {
        $this->request->allowMethod(['post']);
        $errors = null;
        try {
            if ($searchIndexesService->reconstruct()) {
                $message = __d('baser_core', '検索インデックスの再構築に成功しました。');
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', '検索インデックスの再構築に失敗しました。');
            }
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }


        $this->set(['message' => $message, 'errors' => $errors]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }


    /**
     * [API] 検索インデックス一覧取得
     *
     * 認証なしでアクセスできるが、公開状態のもののみ取得可能。
     *
     * ### URL
     * /baser/api/bc-search-index/search_indexes/index.json
     *
     * ### クエリパラメーター（カッコ内は省略形）
     * - keyword(q): 検索キーワード
     * - site_id(s): サイトID
     * - content_id(c): コンテンツID
     * - content_filter_id(cf): コンテンツフィルダーID
     * - type: コンテンツタイプ
     * - model(m): モデル名（エンティティ名）
     * - priority: 優先度
     * - folder_id(f): フォルダーID
     *
     * ### レスポンス
     * - searchIndexes: 検索インデックスの一覧
     *
     * @param SearchIndexesServiceInterface $searchIndexesService
     * @checked
     * @noTodo
     */
    public function index(SearchIndexesServiceInterface $searchIndexesService)
    {
        $this->request->allowMethod('get');

        $this->set([
            'searchIndexes' => $this->paginate($searchIndexesService->getIndex($this->getRequest()->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['searchIndexes']);
    }

    /**
     * [API] 検索インデックス削除
     * @param SearchIndexesServiceInterface $searchIndexesService
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(SearchIndexesServiceInterface $searchIndexesService, $id)
    {
        $this->request->allowMethod(['post', 'delete']);

        $searchIndex = null;

        try {
            $searchIndex = $searchIndexesService->get($id);
            if ($searchIndexesService->delete($id)) {
                $message = __d('baser_core', '検索インデックス: {0} を削除しました。', $searchIndex->title);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'searchIndex' => $searchIndex
        ]);
        $this->viewBuilder()->setOption('serialize', ['searchIndex', 'message']);
    }

}
