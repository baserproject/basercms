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

namespace BcSearchIndex\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * SearchIndicesController
 */
class SearchIndexesController extends BcApiController
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
     * @unitTest
     */
    public function index(SearchIndexesServiceInterface $searchIndexesService)
    {
        $this->request->allowMethod('get');
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['status'])) {
            throw new ForbiddenException();
        }
        $queryParams = array_merge($queryParams, [
            'status' => 'publish'
        ]);
        $this->set([
            'searchIndexes' => $this->paginate($searchIndexesService->getIndex($queryParams))
        ]);
        $this->viewBuilder()->setOption('serialize', ['searchIndexes']);
    }

}
