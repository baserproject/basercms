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
 * Class UserGroup
 * @package BaserCore\Model\Entity
 * @property int $id
 * @property string $name
 * @property string|null $title
 * @property string|null $auth_prefix
 * @property bool $use_admin_globalmenu
 * @property string|null $default_favorites
 * @property bool|null $use_move_contents
 * @property FrozenTime|null $modified
 * @property FrozenTime|null $created
 * @property User[] $users
 */
class UserGroup extends Entity
{

    /**
     * accessible
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'title' => true,
        'auth_prefix' => true,
        'use_admin_globalmenu' => true,
        'default_favorites' => true,
        'use_move_contents' => true,
        'modified' => true,
        'created' => true,
        'users' => true,
    ];

}
