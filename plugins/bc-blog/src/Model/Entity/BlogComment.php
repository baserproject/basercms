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
 * Class BlogComment
 * @property int $id
 * @property int $blog_content_id
 * @property int $blog_post_id
 * @property int $no
 * @property bool $status
 * @property string $name
 * @property string $email
 * @property string $url
 * @property string $message
 * @property FrozenTime $created
 * @property FrozenTime $modified
 */
class BlogComment extends Entity
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
