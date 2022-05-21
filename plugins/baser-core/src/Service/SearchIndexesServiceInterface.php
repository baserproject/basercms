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

/**
 * Interface SearchIndexesServiceInterface
 */
interface SearchIndexesServiceInterface
{
    /**
     * プラグインを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * 検索インデックス再構築
     *
     * @param int $parentContentId 親となるコンテンツID
     * @return bool
     */
    public function reconstruct($parentContentId = null);

}
