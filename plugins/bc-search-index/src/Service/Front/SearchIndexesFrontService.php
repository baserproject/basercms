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

namespace BcSearchIndex\Service\Front;

use BaserCore\Service\ContentsServiceInterface;
use BcSearchIndex\Form\SearchIndexesFrontForm;
use BcSearchIndex\Service\SearchIndexesService;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\ServerRequest;

/**
 * SearchIndexesFrontService
 */
class SearchIndexesFrontService extends SearchIndexesService implements SearchIndexesFrontServiceInterface
{

    /**
     * サイト内検索用の view 変数を取得する
     *
     * ### view 変数
     * - searchIndexes: 検索インデックス一覧
     * - query: 検索キーワード
     * - contentFolders: コンテンツフォルダー一覧
     * - searchIndexesFront: サイト内検索フォーム用のコンテキスト
     *
     * @param \Cake\ORM\ResultSet|\Cake\Datasource\ResultSetInterface $searchIndexes
     * @param ServerRequest $request
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForSearch(ResultSetInterface $searchIndexes, ServerRequest $request): array
    {
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $currentSiteId = $request->getAttribute('currentSite')->id;
        $frontForm = new SearchIndexesFrontForm();
        $frontForm->setData($request->getQuery());
        return [
            'searchIndexes' => $searchIndexes,
            'query' => $this->parseQuery($request->getQuery('q')),
            'contentFolders' => $contentsService->getContentFolderList(
                $currentSiteId,
                ['excludeId' => $contentsService->getSiteRoot($currentSiteId)['id']]
            ),
            'searchIndexesFront' => $frontForm
        ];
    }

}
