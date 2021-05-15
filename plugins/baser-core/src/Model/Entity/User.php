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

namespace BaserCore\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\I18n\Time as TimeAlias;
use Cake\ORM\Entity as EntityAlias;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class User
 * @package BaserCore\Model\Entity
 * @property int $id
 * @property string $name
 * @property string $password
 * @property string $real_name_1
 * @property string $real_name_2
 * @property string $email
 * @property string $nickname
 * @property TimeAlias $created
 * @property TimeAlias $modified
 */
class User extends EntityAlias
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

    /**
     * Hidden
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];

    /**
     * Set Password
     *
     * @param $value
     * @return bool|string
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _setPassword($value)
    {
        if ($value) {
            $hasher = new DefaultPasswordHasher();
            return $hasher->hash($value);
        } else {
            return false;
        }
    }

    /**
     * 管理ユーザーかどうか判定する
     * @param EntityInterface|User $user
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAdmin()
    {
        if ($this->user_groups) {
            foreach($this->user_groups as $group) {
                if($group->id === Configure::read('BcApp.adminGroupId')) {
                    return true;
                }
            }
        }
        return false;
    }

}
