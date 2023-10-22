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
 * CustomLink
 *
 * @property int $custom_table_id
 * @property int $custom_field_id
 * @property int $no
 * @property int $parent_id
 * @property int $level
 * @property int $lft
 * @property int $rght
 * @property string $name
 * @property string $title
 * @property string $before_head
 * @property string $after_head
 * @property string $description
 * @property string $attention
 * @property string $options
 * @property string $class
 * @property bool $required
 * @property bool $group_valid
 * @property bool $use_loop
 * @property bool $display_admin_list
 * @property bool $search_target_front
 * @property bool $use_api
 * @property bool $before_linefeed
 * @property bool $after_linefeed
 * @property bool $display_front
 * @property bool $search_target_admin
 * @property CustomField $custom_field
 * @property array $children
 */
class CustomLink extends Entity
{

    /**
     * グループが選択可能かどうか反映
     *
     * 自身が group の場合は、選択できない
     *
     * @return bool
     * @checked
     * @noTodo
     */
    public function isGroupSelectable(): bool
    {
        return !($this->custom_field && $this->custom_field->type === 'group');
    }

}
