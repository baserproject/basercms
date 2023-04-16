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

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;

/**
 * Class UserGroup
 * @property int $id
 * @property string $name
 * @property string|null $title
 * @property string|null $auth_prefix
 * @property bool|null $use_move_contents
 * @property string $auth_prefix_settings
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
        '*' => true,
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

    /**
     * 利用可能なプレフィックスかどうか判定
     *
     * @param string $prefix
     * @return bool
     * @checked
     * @noTodo
     */
    public function isAuthPrefixAvailabled(string $prefix): bool
    {
        return in_array($prefix, $this->getAuthPrefixArray());
    }

    /**
     * 認証プレフィックスを配列で取得
     *
     * @return string[]
     * @checked
     * @noTodo
     */
    public function getAuthPrefixArray(): array
    {
        if($this->auth_prefix) {
            return explode(',', $this->auth_prefix);
        } else {
            return [];
        }
    }

    /**
     * １つ以上の認証プレフィックス設定を配列で取得
     *
     * @return array
     * @checked
     * @noTodo
     */
    public function getAuthPrefixSettingsArray(): array
    {
        if($this->auth_prefix_settings) {
            return json_decode($this->auth_prefix_settings, true);
        } else {
            return [];
        }
    }

    /**
     * １つの認証プレフィックス設定を取得
     *
     * @param string $prefix
     * @return array
     * @checked
     * @noTodo
     */
    public function getAuthPrefixSettings(string $prefix): array
    {
        $settings = $this->getAuthPrefixSettingsArray();
        if(isset($settings[$prefix])) {
            return $settings[$prefix];
        } else {
            return [];
        }
    }

    /**
     * 指定した認証プレフィックスの設定値を取得
     *
     * @param string $prefix
     * @param string $name
     * @return string
     * @checked
     * @noTodo
     */
    public function getAuthPrefixSetting(string $prefix, string $name): string
    {
        $settings = $this->getAuthPrefixSettings($prefix);
        if(isset($settings[$name])) {
            return $settings[$name];
        } else {
            return '';
        }
    }

}
