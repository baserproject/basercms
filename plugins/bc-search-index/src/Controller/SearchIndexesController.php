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

/**
 * SearchIndexesController
 */
class SearchIndexesController extends BcFrontAppController
{

    /**
     * コンテンツ検索
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
