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

namespace BcBlog\Model\Entity;

use BaserCore\Model\Entity\Content;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BlogContent
 * @property int $id
 * @property string $description
 * @property string $template
 * @property int $list_count
 * @property string $list_direction
 * @property int $feed_count
 * @property bool $tag_use
 * @property bool $comment_use
 * @property bool $comment_approve
 * @property int $widget_area
 * @property string $eye_catch_size
 * @property bool $use_content
 * @property FrozenTime $created
 * @property FrozenTime $modified
 * @property Content $content
 */
class BlogContent extends Entity
{

    /**
     * Accessible
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

}
