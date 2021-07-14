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
 * @property int $priority
 * @property FrozenTime|null $modified
 * @property FrozenTime|null $created
 */
class Permission extends Entity
{

    /**
     * accessible
     *
     * @var array
     */
    protected $_accessible = [
        'no' => true,
        'sort' => true,
        'name' => true,
        'user_group_id' => true,
        'url' => true,
        'auth' => true,
        'method' => true,
        'status' => true,
        'modified' => true,
        'created' => true,
    ];
}
?>
