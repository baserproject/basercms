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

namespace BaserCore\Service;

use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;

/**
 * PagesFrontService
 */
class PagesFrontService extends PagesService implements PagesFrontServiceInterface
{

    /**
     * 固定ページ用のデータを取得する
     * @param EntityInterface $page
     * @param ServerRequest $request
     * @return array
     */
    public function getViewVarsForDisplay(EntityInterface $page, ServerRequest $request): array
    {
        return [
            'page' => $page,
            'editLink' => $this->getEditLink($request)
        ];
    }
}
