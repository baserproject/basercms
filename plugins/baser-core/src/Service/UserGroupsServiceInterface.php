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
 * Interface UserGroupsServiceInterface
 */
interface UserGroupsServiceInterface extends CrudBaseServiceInterface{

    /**
     * コントロールソースを取得する
     *
     * @param string $field
     * @return array
     */
    public function getControlSource(string $field): array;

}
