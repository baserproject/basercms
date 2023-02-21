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

use Cake\I18n\FrozenDate;
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
 * @property FrozenDate $publish_begin
 * @property FrozenDate $publish_end
 * @property bool $self_status
 * @property FrozenDate $self_publish_begin
 * @property FrozenDate $self_publish_end
 * @property bool $exclude_search
 * @property FrozenDate $created_date
 * @property FrozenDate $modified_date
 * @property bool $site_root
 * @property FrozenDate $deleted_date
 * @property bool $exclude_menu
 * @property bool $blank_link
 * @property FrozenDate $created
 * @property FrozenDate $modified
 */
class Content extends Entity
{

    /**
     * accessible
     *
     * @var array
     */
    protected $_accessible = [
        'id' => true,
        'name' => true,
        'plugin' => true,
        'type' => true,
        'entity_id' => true,
        'url' => true,
        'site_id' => true,
        'alias_id' => true,
        'main_site_content_id' => true,
        'parent_id' => true,
        'lft' => true,
        'rght' => true,
        'level' => true,
        'title' => true,
        'description' => true,
        'eyecatch' => true,
        'author_id' => true,
        'layout_template' => true,
        'status' => true,
        'publish_begin' => true,
        'publish_end' => true,
        'self_status' => true,
        'self_publish_begin' => true,
        'self_publish_end' => true,
        'exclude_search' => true,
        'created_date' => true,
        'modified_date' => true,
        'site_root' => true,
        'deleted_date' => true,
        'exclude_menu' => true,
        'blank_link' => true,
        'created' => true,
        'modified' => true,
    ];
}
