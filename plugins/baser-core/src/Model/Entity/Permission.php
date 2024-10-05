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

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class Permission
 * @property int $id
 * @property int $no
 * @property int $sort
 * @property string $name
 * @property int $permission_group_id
 * @property int $user_group_id
 * @property string $url
 * @property bool $auth
 * @property string $method
 * @property bool $status
 * @property \Cake\I18n\DateTime|null $modified
 * @property \Cake\I18n\DateTime|null $created
 */
class Permission extends Entity
{

    /**
     * accessible
     *
     * @var array
     */
    protected array $_accessible = [
        'no' => true,
        'sort' => true,
        'name' => true,
        'permission_group_id' => true,
        'user_group_id' => true,
        'url' => true,
        'auth' => true,
        'method' => true,
        'status' => true,
        'modified' => true,
        'created' => true,
        'permission_group_type' => true
    ];

    /**
     * アクセスルールグループタイプを取得
     *
     * @return string|null
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _getPermissionGroupType()
    {
        if ($this->permission_group_id) {
            $permissionGroupsTable = TableRegistry::getTableLocator()->get('BaserCore.PermissionGroups');
            $entity = $permissionGroupsTable->find()->where(['id' => $this->permission_group_id])->first();
            return $entity->type;
        }
        return isset($this->_fields['permission_group_type'])? $this->_fields['permission_group_type'] : null;
    }

}
