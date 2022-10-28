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

namespace BcSearchIndex\Controller;

use BaserCore\Controller\BcFrontAppController;
use BcSearchIndex\Service\Front\SearchIndexesFrontService;
use BcSearchIndex\Service\Front\SearchIndexesFrontServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * SearchIndexesController
 */
class SearchIndexesController extends BcFrontAppController
{

    /**
     * サイト内検索
     *
     * 検索インデックスに保存された公開状態のデータをページネーションによって
     * デフォルトで10件表示する。
     *
     * ### 並び順
     * - priority: 降順
     * - modified: 降順
     * - id: 昇順
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
     * @checked
     * @noTodo
     */
    public function search(SearchIndexesFrontServiceInterface $service)
    {
        $this->setViewConditions([], [
            'default' => ['query' => ['limit' => 10]]
        ]);
        $params = array_merge($this->request->getQueryParams(), ['status' => 'publish']);
        /* @var SearchIndexesFrontService $service */
        $this->set($service->getViewVarsForSearch(
            $this->paginate($service->getIndex($params)),
            $this->getRequest()
        ));
    }

}
