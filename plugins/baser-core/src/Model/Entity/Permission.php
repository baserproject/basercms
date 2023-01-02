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

use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Class Permission
 * @package BaserCore\Model\Entity
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
     */
    protected function _getPermissionGroupType()
    {
        if($this->permission_group_id) {
            $permissionGroupsTable = TableRegistry::getTableLocator()->get('BaserCore.PermissionGroups');
            $entity = $permissionGroupsTable->find()->where(['id' => $this->permission_group_id])->first();
            return $entity->type;
        }
        return isset($this->_fields['permission_group_type'])? $this->_fields['permission_group_type'] : null;
    }
}
?>
