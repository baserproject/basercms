<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Permission Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Permission Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use BaserCore\Error\BcException;
use BaserCore\Model\Entity\Permission;
use BaserCore\Model\Entity\UserGroup;
use BaserCore\Model\Table\PermissionsTable;
use BaserCore\Utility\BcContainerTrait;
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
use Cake\Routing\Router;
use Cake\Utility\Inflector;

/**
 * Class PermissionsService
 * @property PermissionsTable $Permissions
 */
class PermissionsService implements PermissionsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Permissions Table
     * @var \Cake\ORM\Table
     */
    public $Permissions;

    /**
     * @var string
     */
    public $adminUrlPrefix;

    /**
     * @var array
     */
    private $defaultAllows = [];

    /**
     * @var array
     */
    private $defaultDenies = [];

    /**
     * PermissionsService constructor.
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->Permissions = TableRegistry::getTableLocator()->get('BaserCore.Permissions');
        $this->adminUrlPrefix = BcUtil::getPrefix();
        $this->setDefaultAllow();
    }

    /**
     * パーミッションの新規データ用の初期値を含んだエンティティを取得する
     *
     * @param int $userGroupId
     * @return Permission
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(int $userGroupId = null, int $permissionGroupId = null): EntityInterface
    {
        return $this->Permissions->newEntity(
            $this->autoFillRecord([
                'user_group_id' => $userGroupId,
                'permission_group_id' => $permissionGroupId,
                'permission_group_type' => ($permissionGroupId)? null : 'Admin'
            ]),
            ['validate' => 'plain']
        );
    }

    /**
     * リストデータを取得
     * 対応しない
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array
    {
        return [];
    }

    /**
     * パーミッションを取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->Permissions->get($id, [
            'contain' => ['UserGroups', 'PermissionGroups'],
        ]);
    }

    /**
     * パーミッション管理の一覧用のデータを取得
     *
     * @param array $queryParams
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams = []): Query
    {
        $queryParams = array_merge([
            'contain' => ['PermissionGroups']
        ], $queryParams);

        $conditions = [];
        if (!empty($queryParams['user_group_id'])) {
            $conditions['Permissions.user_group_id'] = $queryParams['user_group_id'];
        }
        if (!empty($queryParams['permission_group_id'])) {
            $conditions['Permissions.permission_group_id'] = $queryParams['permission_group_id'];
        }
        if (!empty($queryParams['permission_group_type'])) {
            $conditions['PermissionGroups.type'] = $queryParams['permission_group_type'];
        }
        $query = $this->Permissions->find()
            ->contain($queryParams['contain'])
            ->where($conditions)
            ->order('sort', 'ASC');
        return $query;
    }

    /**
     * パーミッション登録
     *
     * @param ServerRequest $request
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
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
     *
     * @param EntityInterface $target
     * @param array $data
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
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
        $data = $permission->toArray();
        $data = $this->autoFillRecord($data);
        try {
            return $this->create($data);
        } catch (\Exception $e) {
            return false;
        }
    }



    /**
     * パーミッション情報を削除する
     *
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool
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
        return (array) $this->Permissions::METHOD_LIST;
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
        return (array) $this->Permissions::AUTH_LIST;
    }

    /**
     * レコード作成に必要なデータを代入する
     *
     * @param array $data
     * @return array $data
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
     * @param array $userGroupId
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function check(string $url, array $userGroupId): bool
    {
        if (in_array(Configure::read('BcApp.adminGroupId'), $userGroupId)) return true;
        if ($this->checkDefaultDeny($url)) return false;
        if ($this->checkDefaultAllow($url)) return true;

        $userGroupsService = $this->getService(UserGroupsServiceInterface::class);

        $permissionGroupList = $this->Permissions->getTargetPermissions($userGroupId);
        if($permissionGroupList) {
            foreach($permissionGroupList as $userGroupId => $permissionGroup) {
                $userGroup = null;
                if($userGroupId) {
                    $userGroup = $userGroupsService->get($userGroupId);
                }
                if ($this->checkGroup($url, $permissionGroup, $userGroup)) {
                    return true;
                }
            }
        } else {
            if ($this->checkGroup($url, [], null)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 標準アクセス許可リクエストを設定
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    private function setDefaultAllow(): void
    {
        // ダッシュボード、ログインユーザーの編集とログアウトは強制的に許可とする
        $allowUrls = Configure::read('BcPermission.defaultAllows');
        foreach($allowUrls as $url) {
            $this->addCheck($url, true);
        }
    }

    /**
     * 標準アクセス許可リストからURLを検証
     *
     * @param string $url
     * @return boolean
     * @checked
     * @unitTest
     * @noTodo
     */
    private function checkDefaultAllow(string $url): bool
    {
        foreach($this->defaultAllows as $allow) {
            if (preg_match($allow, $url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 標準アクセス拒否リストからURLを検証
     *
     * @param string $url
     * @return boolean
     * @checked
     * @unitTest
     * @noTodo
     */
    private function checkDefaultDeny(string $url): bool
    {
        foreach($this->defaultDenies as $deny) {
            if (preg_match($deny, $url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * パーミッションリストを検証する
     *
     * @param string $url
     * @param array $groupPermission
     * @param UserGroup|EntityInterface|null $userGroup
     * @return boolean
     * @checked
     * @unitTest
     * @noTodo
     */
    private function checkGroup(string $url, array $groupPermission, $userGroup): bool
    {
        // ドメイン部分を除外
        if(preg_match('/^(http(s|):\/\/[^\/]+?\/)(.*?)$/', $url, $matches)) {
            if(in_array($matches[1], [Configure::read('BcEnv.siteUrl'), Configure::read('BcEnv.sslUrl')])) {
                $url = str_replace([Configure::read('BcEnv.siteUrl'), Configure::read('BcEnv.sslUrl')], '', $url);
                if(!$url) $url = '/';
            } else {
                return true;
            }
        }

        // プレフィックスを取得するためリバースルーティングで解析
        try {
            $urlArray = Router::parseRequest(new ServerRequest(['url' => $url]));
        } catch(\Throwable $e) {
            return true;
        }

        // プレフィックスがない場合はフロントとみなす
        if(empty($urlArray['prefix'])) {
            $prefix = 'Front';
        } else {
            $prefix = $urlArray['prefix'];
        }

        $prefixAuthSetting = array_merge([
            'disabled' => false,
            'permissionType' => 1
        ], Configure::read("BcPrefixAuth.{$prefix}"));

        // 設定が無効の場合は無条件に true
        if($prefixAuthSetting['disabled']) return true;

        // フルアクセスの場合は true
        if($userGroup) {
            $type = (int)$userGroup->getAuthPrefixSetting($prefix, 'type');
            if ($type === 1) return true;
        }

        if($prefix === 'Api') {
            // 管理画面からAPIのURLを参照した場合は無条件に true
            if (BcUtil::isAdminSystem()) return true;
            // 管理画面から呼び出された API は無条件に true
            if (BcUtil::isSameReferrerAsCurrent()) return true;
        }

        // URLのプレフィックスを標準の文字列に戻す
        foreach(Configure::read('BcPrefixAuth') as $key => $value) {
            $prefixAreas = Configure::read('BcApp.' . Inflector::variable($key) . 'Prefix');
            if(!$prefixAreas) continue;
            $regex = '/^' . preg_quote('/' . Configure::read('BcApp.baserCorePrefix') . '/' . $prefixAreas . '/', '/') . '/';
            $url = preg_replace($regex, '/baser/' . Inflector::underscore($key) . '/', $url);
        }

        // permissionType
        // 1: ホワイトリスト（全部拒否して一部許可を設定）
        // 2: ブラックリスト（全部許可して一部拒否を設定）
        $ret = ((int) $prefixAuthSetting['permissionType'] === 2);
        foreach($groupPermission as $permission) {
            $pattern = $this->convertRegexUrl($permission->url);
            if (preg_match($pattern, $url)) {
                $ret = $permission->auth;
            }
        }
        return (boolean)$ret;
    }

    /**
     * URLを正規表現用の文字列に変換する
     * @param string $url
     * @return string
     */
    public function convertRegexUrl(string $url)
    {
        if(strpos($url, '{loginUserId}') !== false) {
            $user = BcUtil::loginUser();
            $url = str_replace('{loginUserId}', $user->id, $url);
        }
        $pattern = preg_quote($url, '/');
        $pattern = str_replace('\*', '.*?', $pattern);
        return '/^' . str_replace('\/.*?', '(|\/.*?)', $pattern) . '$/is';
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
    public function addCheck(string $url, bool $auth)
    {
        $pattern = $this->convertRegexUrl($url);
        if ($auth) {
            $this->defaultAllows[] = $pattern;
        } else {
            $this->defaultDenies[] = $pattern;
        }
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

    /**
     * 一括処理
     *
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch($method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->Permissions->getConnection();
        $db->begin();
        foreach($ids as $id) {
            if (!$this->{$method}($id)) {
                $db->rollback();
                throw new BcException(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

    /**
     * IDを指定して名前リストを取得する
     *
     * @param $ids
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNamesById($ids): array
    {
        return $this->Permissions->find('list')->where(['id IN' => $ids])->toArray();
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field
     * @param array $options
     * @return array
     * @checked
     * @noTodo
     */
    public function getControlSource(string $field, array $options = [])
    {
        if($field === 'permission_group_id') {
            $permissionGroupsService = $this->getService(PermissionGroupsServiceInterface::class);
            return $permissionGroupsService->getList($options);
        } elseif($field === 'permission_group_type') {
            return BcUtil::getAuthPrefixList();
        } elseif($field === 'user_group_id') {
            $userGroups = TableRegistry::getTableLocator()->get('BaserCore.UserGroups');
            $groupList = $userGroups->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where([
                'UserGroups.id !=' => Configure::read('BcApp.adminGroupId')
            ]);
            return $groupList->toArray();
        }
        return [];
    }

}
