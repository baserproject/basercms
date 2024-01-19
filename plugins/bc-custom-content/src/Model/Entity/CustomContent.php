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

use BaserCore\Model\Entity\Content;
use Cake\I18n\FrozenDate;
use Cake\ORM\Entity;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomContent
 *
 * @property Content $content
 * @property integer $custom_table_id
 * @property string $description
 * @property string $template
 * @property integer $widget_area
 * @property integer $list_count
 * @property string $list_order
 * @property string $list_direction
 * @property \Cake\I18n\Date $created
 * @property \Cake\I18n\Date $modified
 */
class CustomContent extends Entity
{

}
