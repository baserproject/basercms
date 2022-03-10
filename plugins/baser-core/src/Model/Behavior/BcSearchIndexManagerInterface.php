<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Model\Behavior;

use Cake\ORM\Entity;

interface BcSearchIndexManagerInterface
{
    /**
     * BcSearchIndexManagerBehavior用の検索インデクスデータを作成する
     *
     * @param Entity $entity
     * @return array|false
     */
    public function createSearchIndex($entity);
}
?>
