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

declare(strict_types=1);

namespace BaserCore\Model\Entity;

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

/**
 * Content
 *
 * @property string $name
 * @property string $plugin
 * @property string $type
 * @property integer $entity_id
 * @property string $url
 * @property integer $site_id
 * @property integer $alias_id
 * @property integer $main_site_content_id
 * @property integer $parent_id
 * @property integer $lft
 * @property integer $rght
 * @property integer $level
 * @property string $title
 * @property string $description
 * @property string $eyecatch
 * @property integer $author_id
 * @property string $layout_template
 * @property bool $status
 * @property FrozenTime $publish_begin
 * @property FrozenTime $publish_end
 * @property bool $self_status
 * @property FrozenTime $self_publish_begin
 * @property FrozenTime $self_publish_end
 * @property bool $exclude_search
 * @property FrozenTime $created_date
 * @property FrozenTime $modified_date
 * @property bool $site_root
 * @property FrozenTime $deleted_date
 * @property bool $exclude_menu
 * @property bool $blank_link
 * @property FrozenTime $created
 * @property FrozenTime $modified
 * @property Site $site
 */
class Content extends Entity
{

    /**
     * accessible
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];

}
