<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

declare(strict_types=1);

namespace BaserCore\Model\Entity;

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

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
        'deleted' => true,
        'exclude_menu' => true,
        'blank_link' => true,
        'created' => true,
        'modified' => true,
    ];
}
?>
