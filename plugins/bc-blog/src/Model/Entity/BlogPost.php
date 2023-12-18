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
use BcBlog\View\Helper\BlogHelper;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\View\View;

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
 * @property \Cake\I18n\DateTime $posted
 * @property string $content_draft
 * @property string $detail_draft
 * @property \Cake\I18n\DateTime $publish_begin
 * @property \Cake\I18n\DateTime $publish_end
 * @property bool $exclude_search
 * @property string $eye_catch
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property BlogContent $blog_content
 */
class BlogPost extends Entity
{

    /**
     * Accessible
     *
     * @var array
     */
    protected array $_accessible = [
        '*' => true,
        'id' => false
    ];

    /**
     * アイキャッチのフルパス
     */
    protected array $_virtual = ['_eyecatch'];

    /**
     * アイキャッチのフルパスを取得
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _get_eyecatch(){
        try {
            $BlogHelper = new BlogHelper(new View());
            return $BlogHelper->getEyeCatch($this, ['output' => 'url']);
        } catch (\Throwable) {
            return '';
        }
    }

}
