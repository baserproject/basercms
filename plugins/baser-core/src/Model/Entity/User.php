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

namespace BaserCore\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\I18n\Time as TimeAlias;
use Cake\ORM\Entity as EntityAlias;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Utility\Hash;

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
 * @property bool $status
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
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAdmin()
    {
        if (empty($this->user_groups)) {
            return false;
        }
        $userGroupId = Hash::extract($this->user_groups, '{n}.id');
        return in_array(Configure::read('BcApp.adminGroupId'), $userGroupId);
    }

    /**
     * スーパーユーザーかどうか判定する
     *
     * @return bool
     */
    public function isSuper(): bool
    {
        return (Configure::read('BcApp.superUserId') === $this->id);
    }

    /**
     * システム管理グループにユーザーを追加可能かどうか判定する
     *
     * 利用可能条件
     * - 自身がスーパーユーザーで対象がスーパーユーザーでない場合
     * - 自身がシステム管理ユーザーで対象がシステム管理ユーザーでない場合
     */
    public function isAddableToAdminGroup(): bool
    {
        return $this->isSuper();
    }

    /**
     * 対象ユーザーに対して代理ログイン可能かどうか判定する
     *
     * 利用可能条件
     * - 自身がスーパーユーザーで対象がスーパーユーザーでない場合
     * - 自身がシステム管理ユーザーで対象がシステム管理ユーザーでない場合
     * @param EntityInterface|User $targetUser
     * @return bool
     */
    public function isEnableLoginAgent(EntityInterface $targetUser): bool
    {
        return (($this->isSuper() && !$targetUser->isSuper()) ||
            ($this->isAdmin() && !$targetUser->isAdmin()));
    }

    /**
     * 対象ユーザーに対して削除可能かどうか判定する
     *
     * 利用可能条件
     * - 自身がスーパーユーザーで対象がスーパーユーザーでない場合
     * - 自身がシステム管理ユーザーで対象がシステム管理ユーザーでない場合
     * @param EntityInterface|User $targetUser
     * @return bool
     */
    public function isDeletableUser(EntityInterface $targetUser): bool
    {
        return (($this->isSuper() && !$targetUser->isSuper()) ||
            ($this->isAdmin() && !$targetUser->isAdmin()));
    }

    /**
     * 対象ユーザーに対して編集可能かどうか判定する
     *
     * 利用可能条件
     * - 自身がスーパーユーザーで対象がスーパーユーザーでない場合
     * - 自身がシステム管理ユーザーで対象がシステム管理ユーザーでない場合
     * @param EntityInterface|User $targetUser
     * @return bool
     */
    public function isEditableUser(EntityInterface $targetUser): bool
    {
        return ($this->isSuper() ||
            ($this->id === $targetUser->id) ||
            ($this->isAdmin() && !$targetUser->isAdmin()));
    }

    /**
     * 整形されたユーザー名を取得する
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDisplayName()
    {
        if (!empty($this->nickname)) {
            return $this->nickname;
        }
        $userName = [];
        if (!empty($this->real_name_1)) {
            $userName[] = $this->real_name_1;
        }
        if (!empty($this->real_name_2)) {
            $userName[] = $this->real_name_2;
        }
        if(count($userName) > 1) {
            return implode(' ', $userName);
        } elseif(count($userName) === 1) {
            return $userName[0];
        } else {
            return 'undefined';
        }
    }

}
