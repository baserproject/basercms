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

use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

/**
 * Class UserGroup
 * @package BaserCore\Model\Entity
 * @property int $id
 * @property string $name
 * @property string|null $title
 * @property string|null $auth_prefix
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
        'use_move_contents' => true,
        'modified' => true,
        'created' => true,
        'users' => true,
    ];

    /**
     * 管理グループかどうか判定
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return (Configure::read('BcApp.adminGroupId') === $this->id);
    }

}
