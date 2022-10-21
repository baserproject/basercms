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
 * Class BlogCategory
 * @property int $id
 * @property int $blog_content_id
 * @property int $no
 * @property string $name
 * @property string $title
 * @property bool $status
 * @property int $parent_id
 * @property int $lft
 * @property int $rght
 * @property FrozenTime $created
 * @property FrozenTime $modified
 */
class BlogCategory extends Entity
{

    /**
     * 階層
     * BlogCategoriesService::getTreeIndex() を実行した時のみ設定される
     * @var int
     */
    public $depth;

    /**
     * 階層化タイトル
     * BlogCategoriesService::getTreeIndex() を実行した時のみ設定される
     * @var string
     */
    public $layered_title;

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
