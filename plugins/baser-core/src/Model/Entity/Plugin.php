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

/**
 * Class Plugin
 * @package BaserCore\Model\Entity
 * @property int $id
 * @property string $name
 * @property string|null $title
 * @property string|null $version
 * @property bool $status
 * @property bool $db_init
 * @property FrozenTime|null $modified
 * @property FrozenTime|null $created
 */
class Plugin extends Entity
{

    /**
     * accessible
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'title' => true,
        'version' => true,
        'status' => true,
        'db_init' => true,
        'priority' => true,
        'modified' => true,
        'created' => true,
        'update' => true,
        'core' => true,
        'registered' => true,
        'description' => true,
        'author' => true,
        'url' => true,
        'installMessage' => true,
        'adminLink' => true
    ];

}
