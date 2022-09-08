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

/**
 * UtilitiesAdminServiceInterface
 */
interface UtilitiesAdminServiceInterface
{

    /**
     * info 画面用の view 変数を生成
     * @return array
     */
    public function getViewVarsForInfo(): array;

}
