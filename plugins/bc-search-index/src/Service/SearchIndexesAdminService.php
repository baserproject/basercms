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

namespace BcSearchIndex\Service;

use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Utility\BcContainerTrait;
use BcSearchIndex\Form\SearchIndexesSearchForm;
use Cake\Http\ServerRequest;

/**
 * SearchIndexesAdminService
 */
class SearchIndexesAdminService extends SearchIndexesService implements SearchIndexesAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 一覧画面に必要なデータを取得する
     * @param \Cake\ORM\ResultSet $searchIndexes
     * @param int $siteId
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForIndex(\Cake\ORM\ResultSet $searchIndexes, ServerRequest $request): array
    {
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $sitesService = $this->getService(SitesServiceInterface::class);
        $searchForm = new SearchIndexesSearchForm();
        $searchForm->setData($request->getQuery());
        return [
            'searchIndexes' => $searchIndexes,
            'folders' => $contentsService->getContentFolderList($request->getQuery('site_id'), ['conditions' => ['Contents.site_root' => false]]),
            'sites' => $sitesService->getList(),
            'searchIndexesSearch' => $searchForm
        ];
    }

}
