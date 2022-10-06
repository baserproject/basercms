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
 * SiteConfigsAdminServiceInterface
 */
interface SiteConfigsAdminServiceInterface
{

    /**
     * サイト基本設定画面用のデータを取得
     * @param EntityInterface $siteConfig
     * @return array
     */
    public function getViewVarsForIndex(EntityInterface $siteConfig): array;

}
