<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use BaserCore\Model\Entity\Permission;
use BaserCore\Model\Table\PermissionsTable;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * Class PermissionService
 * @package BaserCore\Service
 * @property PermissionsTable $Permissions
 */
class PermissionService implements PermissionServiceInterface
{

    /**
     * Permissions Table
     * @var \Cake\ORM\Table
     */
    public $Permissions;

    /**
     * PermissionService constructor.
     */
    public function __construct()
    {
        $this->Permissions = TableRegistry::getTableLocator()->get('BaserCore.Permissions');
    }

    /**
     * パーミッションの新規データ用の初期値を含んだエンティティを取得する
     * @param int $userGroupId
     * @return Permission
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew($userGroupId): EntityInterface
    {
        return $this->Permissions->newEntity(
            $this->autoFillRecord(['user_group_id' => $userGroupId]),
            ['validate' => 'plain']
        );
    }

    /**
     * パーミッションを取得する
     * @param int $id
     * @return EntityInterface
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->Permissions->get($id, [
            'contain' => ['UserGroups'],
        ]);
    }

    /**
     * パーミッション管理の一覧用のデータを取得
     * @param array $queryParams
     * @return Query
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams): Query
    {
        $options = [];
        if (!empty($queryParams['user_group_id'])) {
            $options = ['conditions' => ['Permissions.user_group_id' => $queryParams['user_group_id']]];
        }
        $query = $this->Permissions->find('all', $options)->order('sort', 'ASC');
        return $query;
    }

    /**
     * パーミッション登録
     * @param ServerRequest $request
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData): EntityInterface
    {
        $postData = $this->autoFillRecord($postData);
        $permission = $this->Permissions->newEmptyEntity();
        $permission = $this->Permissions->patchEntity($permission, $postData, ['validate' => 'default']);
        return $this->Permissions->saveOrFail($permission);
    }

    /**
     * パーミッション情報を更新する
     * @param EntityInterface $target
     * @param array $data
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $data): EntityInterface
    {
        $permission = $this->Permissions->patchEntity($target, $data);
        return $this->Permissions->saveOrFail($permission);
    }

    /**
     * 有効状態にする
     *
     * @param int $id
     * @return bool
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish($id): bool
    {
        $permission = $this->get($id);
        $permission->status = true;
        return ($this->Permissions->save($permission)) ? true: false;
    }

    /**
     * 無効状態にする
     *
     * @param int $id
     * @return bool
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish($id): bool
    {
        $permission = $this->get($id);
        $permission->status = false;
        return ($this->Permissions->save($permission)) ? true: false;
    }

    /**
     * 複製する
     *
     * @param int $permissionId
     * @return EntityInterface|false
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(int $permissionId)
    {
        $permission = $this->get($permissionId);
        $permission->id = null;
        $permission->no = null;
        $permission->sort = null;
        $data = $permission->toarray();
        $data = $this->autoFillRecord($data);
        try {
            return $this->create($data);
        } catch (\Exception $e) {
            return false;
        }
    }



    /**
     * パーミッション情報を削除する
     * @param int $id
     * @return bool
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id)
    {
        $Permission = $this->get($id);
        return $this->Permissions->delete($Permission);
    }

    /**
     * 許可・拒否を指定するメソッドのリストを取得
     *
     * @return array
     * @noTodo
     * @unitTest
     * @checked
     */
    public function getMethodList() : array
    {
        return $this->Permissions::METHOD_LIST;
    }

    /**
     * 権限リストを取得
     *
     * @return array
     * @noTodo
     * @unitTest
     * @checked
     */
    public function getAuthList() : array
    {
        return $this->Permissions::AUTH_LIST;
    }

    /**
     *  レコード作成に必要なデータを代入する
     * @param array $data
     * @return array $data
     *
     * @noTodo
     * @unitTest
     * @checked
     */
    protected function autoFillRecord($data = []): array
    {
        if (empty($data['no'])) {
            $data['no'] = $this->Permissions->getMax('no') + 1;
        }
        if (empty($data['sort'])) {
            $data['sort'] = $this->Permissions->getMax('sort') + 1;
        }
        if (!isset($data['auth']) || $data['auth'] === null) {
            $data['auth'] = false;
        }
        if (empty($data['method'])) {
            $data['method'] = '*';
        }
        if (!isset($data['status']) || $data['status'] === null) {
            $data['status'] = true;
        }
        return $data;
    }

    /**
     * 権限チェックを行う
     *
     * @param string $url
     * @param string $userGroupId
     * @return boolean
     * @checked
     * @unitTest
     */
    public function check($url, $userGroupId): bool
    {
        if ($userGroupId == Configure::read('BcApp.adminGroupId')) {
            return true;
        }
        $this->setCheck($userGroupId);
        $permissions = $this->Permissions->getCurrentPermissions();
        if ($url != '/') {
            $url = preg_replace('/^\//is', '', $url);
        }
        $adminPrefix = BcUtil::getPrefix(true);
        // TODO ucmitz 管理画面のURLを変更した場合に対応する必要がある
        $url = preg_replace("/^{$adminPrefix}\//", 'baser/admin/', $url);
        // ダッシュボード、ログインユーザーの編集とログアウトは強制的に許可とする
        $allows = [
            '/^baser\/admin$/',
            '/^baser\/admin\/$/',
            '/^baser\/admin\/dashboard\/.*?/',
            '/^baser\/admin\/dblogs\/.*?/',
            '/^baser\/admin\/users\/logout$/',
        ];
        $sessionKey = Configure::read('BcAuthPrefix.admin.sessionKey');
        if (!empty($_SESSION['Auth'][$sessionKey]['id'])) {
            $allows[] = '/^baser\/admin\/users\/edit\/' . $_SESSION['Auth'][$sessionKey]['id'] . '$/';
        }
        foreach($allows as $allow) {
            if (preg_match($allow, $url)) {
                return true;
            }
        }
        $ret = true;
        foreach($permissions as $permission) {
            if (!$permission->status) {
                continue;
            }
            if ($permission->url != '/') {
                $pattern = preg_replace('/^\//is', '', $permission->url);
            } else {
                $pattern = $permission->url;
            }
            $pattern = addslashes($pattern);
            $pattern = str_replace('/', '\/', $pattern);
            $pattern = str_replace('*', '.*?', $pattern);
            $pattern = '/^' . str_replace('\/.*?', '(|\/.*?)', $pattern) . '$/is';
            if (preg_match($pattern, $url)) {
                $ret = $permission->auth;
            }
        }
        return (boolean)$ret;
    }

    /**
     * 権限チェックの準備をする
     *
     * @param int $userGroupId
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setCheck($userGroupId): void
    {
        $currentPermissions = $this->Permissions->getCurrentPermissions();
        if (empty($currentPermissions)) {
            $permissions = $this->Permissions->find('all')
                ->select(['url', 'auth', 'status'])
                ->where(['Permissions.user_group_id' => $userGroupId])
                ->order('sort');
            $this->Permissions->setCurrentPermissions($permissions->toArray());
        }
    }

    /**
     * 権限チェック対象を追加する
     *
     * @param string $url
     * @param bool $auth
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function addCheck($url, $auth)
    {
        $userGroups = BcUtil::loginUser('Admin')->user_groups;
        $this->setCheck($userGroups[0]->id);
        $permission = new Permission([
            'url' => $url,
            'auth' => $auth,
            'status' => true
        ]);
        $permissions = array_merge($this->Permissions->getCurrentPermissions(), [$permission]);
        $this->Permissions->setCurrentPermissions($permissions);
    }

    /**
     * 優先度を変更する
     *
     * @param int $id
     * @param int $offset
     * @param array $conditions
     * @return bool
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function changeSort(int $id, int $offset, array $conditions = []): bool
    {
        $offset = intval($offset);
        if ($offset === 0) {
            return true;
        }

        $current = $this->get($id);

        // currentを含め変更するデータを取得
        if ($offset > 0) { // DOWN
            $order = ["sort"];
            $conditions["sort >="] = $current->sort;
        } else { // UP
            $order = ["sort DESC"];
            $conditions["sort <="] = $current->sort;
        }

        $result = $this->Permissions->find()
            ->where($conditions)
            ->order($order)
            ->limit(abs($offset) + 1)
            ->all();

        $count = $result->count();
        if (!$count) {
            return false;
        }
        $permissions = $result->toList();

        //データをローテーション
        $currentNewValue = $permissions[$count - 1]->sort;
        for($i = $count - 1; $i > 0; $i--) {
            $permissions[$i]->sort = $permissions[$i - 1]->sort;
        }
        $permissions[0]->sort = $currentNewValue;
        if (!$this->Permissions->saveMany($permissions)) {
            return false;
        }

        return true;
    }

}
