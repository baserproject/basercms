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

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Http\ServerRequest;

/**
 * Interface SearchIndexesAdminServiceInterface
 */
interface SearchIndexesAdminServiceInterface
{

    /**
     * 一覧画面に必要なデータを取得する
     * @param \Cake\ORM\ResultSet $searchIndexes
     * @param int $siteId
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex(\Cake\ORM\ResultSet $searchIndexes, ServerRequest $request): array;

}
