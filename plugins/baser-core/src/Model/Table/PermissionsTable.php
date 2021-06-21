<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
namespace BaserCore\Model\Table;

use BaserCore\Model\AppTable;
use Cake\Core\Configure;
use BaserCore\Utility\BcUtil;
use Cake\Validation\Validator;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class Permission
 * パーミッションモデル
 *
 * @package Baser.Model
 */
class PermissionsTable extends AppTable
{

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('permissions');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('UserGroups', [
            'className' => 'BaserCore.UserGroups',
            'foreignKey' => 'user_group_id',
            'targetForeignKey' => 'id',
            'joinTable' => 'user_groups',
            'joinType' => 'left'
        ]);
    }
    /**
     * permissionsTmp
     * ログインしているユーザーの拒否URLリスト
     * キャッシュ用
     *
     * @var mixed
     */
    public $permissionsTmp = -1;

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator->setProvider('permission', 'BaserCore\Model\Validation\PermissionValidation');

        $validator
            ->scalar('name')
            ->maxLength('name', 255,  __d('baser', '設定名は255文字以内で入力してください。'))
            ->notEmptyString('name', __d('baser', '設定名を入力してください。'));
        $validator
            ->integer('user_group_id')
            ->notEmptyString('user_group_id',  __d('baser', 'ユーザーグループを選択してください。'))
            ->requirePresence('user_group_id', true);
        $validator
            ->scalar('url')
            ->maxLength('url', 255, __d('baser', '設定URLは255文字以内で入力してください。'))
            ->notEmptyString('url', __d('baser', '設定URLを入力してください。'))
            ->add('url', 'checkUrl', [
                'rule' => 'checkUrl',
                'provider' => 'permission',
                'message' => __d('baser', 'アクセス拒否として設定できるのは認証ページだけです。')]);
        
        return $validator;
    }


    /**
     * 認証プレフィックスを取得する
     *
     * @param int $id PermissionのID
     * @return string
     */
    public function getAuthPrefix($id)
    {
        $data = $this->find('first', [
            'conditions' => ['Permission.id' => $id],
            'recursive' => 1
        ]);
        if (isset($data['UserGroup']['auth_prefix'])) {
            return $data['UserGroup']['auth_prefix'];
        } else {
            return '';
        }
    }

    /**
     * 初期値を取得する
     * @return array
     */
    public function getDefaultValue()
    {
        $data['Permission']['auth'] = 0;
        $data['Permission']['status'] = 1;
        return $data;
    }

    /**
     * コントロールソースを取得する
     *
     * @param string フィールド名
     * @return array コントロールソース
     */
    public function getControlSource($field = null)
    {
        $controlSources['user_group_id'] = $this->UserGroup->find('list', ['conditions' => ['UserGroup.id <>' => Configure::read('BcApp.adminGroupId')]]);
        $controlSources['auth'] = ['0' => __d('baser', '不可'), '1' => __d('baser', '可')];
        if (isset($controlSources[$field])) {
            return $controlSources[$field];
        } else {
            return false;
        }
    }

    /**
     * beforeSave
     * urlの先頭に / を付けて絶対パスにする
     *
     * @param object $options
     * @return boolean
     */
    public function beforeSave($options = [])
    {
        $data = $options->getData();
        if (preg_match('/^[^\/]/is', $data["entity"]->get("url"))) {
            $data["entity"]->set("url", '/' . $data["entity"]->get("url"));
        }
        return true;
    }

    /**
     * 権限チェックを行う
     *
     * @param array $url
     * @param string $userGroupId
     * @return boolean
     */
    public function check($url, $userGroupId)
    {
        if ($userGroupId == Configure::read('BcApp.adminGroupId')) {
            return true;
        }

        $this->setCheck($userGroupId);
        $permissions = $this->permissionsTmp;

        if ($url != '/') {
            $url = preg_replace('/^\//is', '', $url);
        }
        $adminPrefix = Configure::read('Routing.prefixes.0');
        $url = preg_replace("/^{$adminPrefix}\//", 'admin/', $url);

        // ダッシュボード、ログインユーザーの編集とログアウトは強制的に許可とする
        $allows = [
            '/^admin$/',
            '/^admin\/$/',
            '/^admin\/dashboard\/.*?/',
            '/^admin\/dblogs\/.*?/',
            '/^admin\/users\/logout$/',
            '/^admin\/user_groups\/set_default_favorites$/'
        ];
        $sessionKey = Configure::read('BcAuthPrefix.admin.sessionKey');
        if (!empty($_SESSION['Auth'][$sessionKey]['id'])) {
            $allows[] = '/^admin\/users\/edit\/' . $_SESSION['Auth'][$sessionKey]['id'] . '$/';
        }

        foreach($allows as $allow) {
            if (preg_match($allow, $url)) {
                return true;
            }
        }

        $ret = true;
        foreach($permissions as $permission) {
            if (!$permission['Permission']['status']) {
                continue;
            }
            if ($permission['Permission']['url'] != '/') {
                $pattern = preg_replace('/^\//is', '', $permission['Permission']['url']);
            } else {
                $pattern = $permission['Permission']['url'];
            }
            $pattern = addslashes($pattern);
            $pattern = str_replace('/', '\/', $pattern);
            $pattern = str_replace('*', '.*?', $pattern);
            $pattern = '/^' . str_replace('\/.*?', '(|\/.*?)', $pattern) . '$/is';
            if (preg_match($pattern, $url)) {
                $ret = $permission['Permission']['auth'];
            }
        }
        return (boolean)$ret;
    }

    /**
     * アクセス制限データをコピーする
     *
     * @param int $id
     * @param array $data
     * @return mixed UserGroup Or false
     */
    public function copy($id, $data = [])
    {
        if ($id) {
            $data = $this->find('first', ['conditions' => ['Permission.id' => $id], 'recursive' => -1]);
        }

        if (!isset($data['Permission']['user_group_id']) || !isset($data['Permission']['name'])) {
            return false;
        }

        if ($this->find('count', ['conditions' => ['Permission.user_group_id' => $data['Permission']['user_group_id'], 'Permission.name' => $data['Permission']['name']]])) {
            $data['Permission']['name'] .= '_copy';
            return $this->copy(null, $data); // 再帰処理
        }

        unset($data['Permission']['id']);
        unset($data['Permission']['modified']);
        unset($data['Permission']['created']);

        $data['Permission']['no'] = $this->getMax('no', ['user_group_id' => $data['Permission']['user_group_id']]) + 1;
        $data['Permission']['sort'] = $this->getMax('sort', ['user_group_id' => $data['Permission']['user_group_id']]) + 1;
        $this->create($data);
        $result = $this->save();
        if ($result) {
            $result['Permission']['id'] = $this->getInsertID();
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 権限チェックの準備をする
     *
     * @param $userGroupId
     */
    public function setCheck($userGroupId)
    {
        if ($this->permissionsTmp === -1) {
            $conditions = ['Permission.user_group_id' => $userGroupId];
            $permissions = $this->find('all', [
                'fields' => ['url', 'auth', 'status'],
                'conditions' => $conditions,
                'order' => 'sort',
                'recursive' => -1
            ]);
            if ($permissions) {
                $this->permissionsTmp = $permissions;
            } else {
                $this->permissionsTmp = [];
            }
        }
    }

    /**
     * 権限チェック対象を追加する
     *
     * @param string $url
     * @param bool $auth
     */
    public function addCheck($url, $auth)
    {
        $this->setCheck(BcUtil::loginUser('admin')['user_group_id']);
        $this->permissionsTmp[] = [
            'Permission' => [
                'url' => $url,
                'auth' => $auth,
                'status' => true
            ]
        ];
    }

}
