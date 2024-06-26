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
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity as EntityAlias;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Utility\Hash;

/**
 * Class User
 * @property int $id
 * @property string $name
 * @property string $password
 * @property string $real_name_1
 * @property string $real_name_2
 * @property string $email
 * @property string $nickname
 * @property bool $status
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 */
class User extends EntityAlias
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
     * Hidden
     *
     * @var array
     */
    protected array $_hidden = [
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
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
     */
    public function isAddableToAdminGroup(): bool
    {
        return $this->isSuper();
    }

    /**
     * 対象ユーザーに対して代理ログイン可能かどうか判定する
     *
     * 利用可能条件
     * - 対象ユーザーのステータスが有効であること
     * - 自身がスーパーユーザーで対象がスーパーユーザーでない場合
     * - 自身がシステム管理ユーザーで対象がシステム管理ユーザーでない場合
     * @param EntityInterface|User $targetUser
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isEnableLoginAgent(EntityInterface $targetUser): bool
    {
        if (!$targetUser->status) return false;
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
     * @checked
     * @noTodo
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
     * - 自身がスーパーユーザーの場合無条件に可
     * - 自身に対しての変更は可
     * - 相手がシステム管理ユーザーでない場合は可（アクセスルールで制御）
     *
     * @param EntityInterface|User $targetUser
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isEditableUser(EntityInterface $targetUser): bool
    {
        if ($this->isSuper()) return true;
        if ($this->id === $targetUser->id) return true;
        return !$targetUser->isAdmin();
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
        if (count($userName) > 1) {
            return implode(' ', $userName);
        } elseif (count($userName) === 1) {
            return $userName[0];
        } else {
            return 'undefined';
        }
    }

    /**
     * 認証領域のプレフィックスを配列で取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAuthPrefixes(): array
    {
        if(!$this->user_groups) return [];
        $prefixes = [];
        foreach($this->user_groups as $userGroup) {
            $prefixes += explode(',', $userGroup->auth_prefix);
        }
        return $prefixes;
    }

}
