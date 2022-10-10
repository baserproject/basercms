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

namespace BaserCore\Service\Admin;

use Cake\Datasource\EntityInterface;

/**
 * PagesAdminServiceInterface
 */
interface PagesAdminServiceInterface{

    /**
     * 編集画面用の view 変数を取得する
     *
     * @param EntityInterface $page
     * @return array
     */
    public function getViewVarsForEdit(EntityInterface $page): array;

}
