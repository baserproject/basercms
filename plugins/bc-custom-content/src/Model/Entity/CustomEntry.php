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

use Cake\I18n\FrozenDate;
use Cake\ORM\Entity;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomEntry
 *
 * @property int $custom_table_id
 * @property int $parent_id
 * @property int $lft
 * @property int $rght
 * @property int $level
 * @property string $title
 * @property string $name
 * @property bool $status
 * @property int creator_id
 * @property FrozenDate $published
 * @property FrozenDate $publish_begin
 * @property FrozenDate $publish_end
 * @property CustomTable $custom_table
 */
class CustomEntry extends Entity
{

}
