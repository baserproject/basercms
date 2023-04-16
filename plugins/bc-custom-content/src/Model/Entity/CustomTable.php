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

namespace BcCustomContent\Model\Entity;

use Cake\ORM\Entity;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomTable
 *
 * @property string $name
 * @property string $title
 * @property string $display_field
 * @property array $custom_links
 * @property bool $has_child
 * @property CustomContent $custom_content
 */
class CustomTable extends Entity
{

    /**
     * コンテンツ用のテーブルか判定する
     *
     * @param CustomTable $table
     * @return bool
     */
    public function isContentTable(): bool
    {
        return ($this->type === '1' && $this->custom_content);
    }

}
