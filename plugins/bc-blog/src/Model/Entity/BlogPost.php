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

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

/**
 * Class BlogPost
 * @property int $id
 * @property int $blog_content_id
 * @property int $no
 * @property string $name
 * @property string $title
 * @property string $content
 * @property string $detail
 * @property int $blog_category_id
 * @property int $user_id
 * @property bool $status
 * @property FrozenTime $posted
 * @property string $content_draft
 * @property string $detail_draft
 * @property FrozenTime $publish_begin
 * @property FrozenTime $publish_end
 * @property bool $exclude_search
 * @property string $eye_catch
 * @property FrozenTime $created
 * @property FrozenTime $modified
 * @property BlogContent $blog_content
 */
class BlogPost extends Entity
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
