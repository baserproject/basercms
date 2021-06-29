<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

/**
 * Interface DblogsServiceInterface
 * @package BaserCore\Service
 */
interface DblogsServiceInterface
{

    /**
     * 新規登録する
     * @param array $data
     * @return EntityInterface
     */
    public function create(array $data);

}
