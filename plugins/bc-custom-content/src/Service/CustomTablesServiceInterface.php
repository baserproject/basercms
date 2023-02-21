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

namespace BcCustomContent\Service;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomTablesServiceInterface
 */
interface CustomTablesServiceInterface
{

    /**
     * コントロールソースを取得する
     *
     * @param string $field
     * @param array $options
     * @return array
     */
    public function getControlSource(string $field, array $options = []): array;

}
