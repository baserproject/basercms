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

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\ServerRequest;

/**
 * Interface SearchIndexesFrontServiceInterface
 */
interface SearchIndexesFrontServiceInterface
{

    /**
     * サイト内検索用の view 変数を取得する
     * @param \Cake\ORM\ResultSet|\Cake\Datasource\ResultSetInterface $searchIndexes
     * @param ServerRequest $request
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForSearch(ResultSetInterface $searchIndexes, ServerRequest $request): array;

}
