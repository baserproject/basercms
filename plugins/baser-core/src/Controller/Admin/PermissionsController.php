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

namespace BaserCore\Controller\Admin;

use Authentication\Controller\Component\AuthenticationComponent;
use BaserCore\Model\Table\PermissionsTable;
use BaserCore\Model\Table\UserGroupsTable;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\UserGroupsServiceInterface;
// use BaserCore\Utility\BcUtil;
use BaserCore\Controller\Component\BcMessageComponent;
// use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
// use Cake\Core\Exception\Exception;
// use Cake\Datasource\Exception\RecordNotFoundException;
// use Cake\Event\Event;
// use Cake\Event\EventInterface;
// use Cake\Event\EventManagerInterface;
// use Cake\Http\ServerRequest;
// use Cake\Routing\Router;
// use Cake\Http\Response;
// use Cake\Http\Exception\ForbiddenException;
// use Cake\Http\Cookie\Cookie;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PermissionsController
 * @package BaserCore\Controller\Admin
 * @property UserGroupsTable $UserGroups
 * @property PermissionsTable $Permissions
 * @property AuthenticationComponent $Authentication
 * @property BcMessageComponent $BcMessage
 */
class PermissionsController extends BcAdminAppController
{
    /**
     * クラス名
     *
     * @var string
     */
    // public $name = 'Permissions';

    /**
     * モデル
     *
     * @var array
     */
    // public $uses = ['Permission'];

	/**
	 * コンポーネント
	 * TODO 未確認
	 * @var array
	 */
	// public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

	/**
	 * ヘルパ
	 * TODO 未確認
	 *
	 * @var array
	 */
	// public $helpers = ['BcTime', 'BcFreeze'];

    /**
     * サブメニューエレメント
     *
     * @var array
     */
    // public $subMenuElements = ['site_configs', 'users', 'permissions'];

	/**
	 * beforeFilter
     * // TODO 未確認
	 *
	 * @return void
	 */
	// public function beforeFilter()
	// {
    //     return;
    //     // TODO 未確認

	// 	// parent::beforeFilter();
	// 	$this->crumbs = [
	// 		['name' => __d('baser', 'ユーザー管理'), 'url' => ['controller' => 'users', 'action' => 'index']],
	// 		['name' => __d('baser', 'ユーザーグループ管理'), 'url' => ['controller' => 'user_groups', 'action' => 'index']]
	// 	];
	// 	if ($this->request->getParam('prefix') === 'admin') {
	// 		$this->set('usePermission', true);
	// 	}
	// 	if (!$this->request->is('ajax')) {
	// 		$this->crumbs[] = ['name' => __d('baser', 'アクセス制限設定管理'), 'url' => ['controller' => 'permissions', 'action' => 'index', $this->request->params['pass'][0]]];
	// 	}
	// }

	/**
	 * アクセス制限設定の一覧を表示する
	 *
	 * @return void
	 */
	public function index(PermissionsServiceInterface $permissions, UserGroupsServiceInterface $userGroups, $userGroupId = null)
	{
		/* セッション処理 */
        // TODO
		// if (!$userGroupId) {
		// 	$this->BcMessage->setError(__d('baser', '無効な処理です。'));
		// 	$this->redirect(['controller' => 'user_groups', 'action' => 'index']);
		// }

        // TODO 未実装、取り急ぎ動作させるためのコード
        $this->request->withQueryParams(['user_group_id' => $userGroupId]);
        $userGroup = $userGroups->get($userGroupId);


        $this->siteConfigs['admin_list_num'] = 30;
        $this->setViewConditions('Permission', ['default' => ['query' => [
            'num' => $this->siteConfigs['admin_list_num'],
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);

        $this->set('userGroupId', $userGroupId);
        $this->set('permissions', $this->paginate($permissions->getIndex($this->request->getQueryParams())));



        // TODO
		// if ($datas) {
		// 	foreach($datas as $key => $data) {
		// 		$datas[$key]['Permission']['url'] = preg_replace('/^\/admin\//', '/' . Configure::read('Routing.prefixes.0') . '/', $data['Permission']['url']);
		// 	}
		// }
		$this->_setAdminIndexViewData();


        // TODO
		// if ($this->RequestHandler->isAjax() || !empty($this->request->query['ajax'])) {
		// 	$this->render('ajax_index');
		// 	return;
		// }



		$this->setTitle(sprintf(__d('baser', '%s｜アクセス制限設定一覧'), $userGroup->title));
		$this->setHelp('permissions_index');
	}

	/**
	 * 一覧の表示用データをセットする
	 *
	 * @return void
	 */
	protected function _setAdminIndexViewData()
	{
		$this->set('sortmode', $this->request->getParam('sortmode'));
	}

	/**
	 * [ADMIN] 登録処理
	 *
	 * @return void
	 */
	public function add(PermissionsServiceInterface $permissions, UserGroupsServiceInterface $userGroups, $userGroupId)
	{
		$userGroup = $userGroups->get($userGroupId);

        if ($this->request->is('post')) {
            $permission = $permissions->set($this->request->withData('user_group_id', $userGroup->id)->getData());
            if (empty($permission->getErrors()) === true) {
                $permissions->create($permission);
                $this->BcMessage->setSuccess(sprintf(__d('baser', '新規アクセス制限設定「%s」を追加しました。'), $permission->name));
                $this->redirect(['action' => 'index', $userGroupId]);
            }
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
        } else {
            $permission = $permissions->getNew($userGroupId);
        }

        $this->set('permission', $permission);
        $this->set('userGroup', $userGroup);
        $this->set('methodList', $permissions->getMethodList());
        $this->setHelp('permissions_form');
        $this->setTitle(sprintf(__d('baser', '%s｜新規アクセス制限設定登録'), $userGroup['UserGroup']['title']));

        // TODO 現在 admin 固定、今後、mypage 等にも対応する
        // $permissionAuthPrefix = 'admin';
        // $this->request = $this->request->withData('Permission.url', preg_replace('/^(\/' . $permissionAuthPrefix . '\/|\/)/', '', $this->request->getData('Permission.url')));
        // $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
	}

	/**
	 * [ADMIN] 登録処理
	 *
	 * @return void
	 */
	public function admin_ajax_add()
	{
		if (!$this->request->data) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
			exit;
		}

// TODO 現在 admin 固定、今後、mypage 等にも対応する
        $authPrefix = 'admin';
        $this->request = $this->request->withData('Permission.url', '/' . $authPrefix . '/' . $this->request->getData('Permission.url'));
        $this->request = $this->request->withData('Permission.no', $this->Permission->getMax('no', ['user_group_id' => $this->request->getData('Permission.user_group_id')]) + 1);
        $this->request = $this->request->withData('Permission.sort', $this->Permission->getMax('sort', ['user_group_id' => $this->request->getData('Permission.user_group_id')]) + 1);
        $this->request = $this->request->withData('Permission.status', true);
        $this->Permission->create($this->request->data);
        if (!$this->Permission->save()) {
            $this->ajaxError(500, $this->Page->validationErrors);
            exit;
        }

        $this->Permission->saveDbLog(
            sprintf(
                __d('baser', '新規アクセス制限設定「%s」を追加しました。'), $this->request->getData('Permission.name')
            )
        );
        exit(true);
    }

    /**
     * [ADMIN] 編集処理
     *
     * @param int $id
     * @return void
     */
    public function admin_edit($userGroupId, $id)
    {
        /* 除外処理 */
        if (!$userGroupId || !$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }

        $userGroup = $this->Permission->UserGroup->find('first', ['conditions' => ['UserGroup.id' => $userGroupId],
            'fields' => ['id', 'title'],
            'order' => 'UserGroup.id ASC', 'recursive' => -1]);

        // TODO 現在 admin 固定、今後、mypage 等にも対応する
        $authPrefix = 'admin';
        if (empty($this->request->data)) {

            $this->request->data = $this->Permission->read(null, $id);
            $this->request = $this->request->withData('Permission.url', preg_replace('/^(\/' . $authPrefix . '\/|\/)/', '', $this->request->getData('Permission.url')));
        } else {

            /* 更新処理 */
            $this->request = $this->request->withData('Permission.url', '/' . $authPrefix . '/' . $this->request->getData('Permission.url'));

            if ($this->Permission->save($this->request->data)) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', 'アクセス制限設定「%s」を更新しました。'), $this->request->getData('Permission.name')));
                $this->redirect(['action' => 'index', $userGroupId]);
            } else {
                $this->request = $this->request->withData('Permission.url', preg_replace('/^(\/' . $authPrefix . '\/|\/)/', '', $this->request->getData('Permission.url')));
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            }
        }

        /* 表示設定 */
        $this->setTitle(sprintf(__d('baser', '%s｜アクセス制限設定編集'), $userGroup['UserGroup']['title']));
        $this->set('permissionAuthPrefix', Configure::read('Routing.prefixes.0'));
        $this->setHelp('permissions_form');
        $this->render('form');
    }

    /**
     * [ADMIN] 削除処理　(ajax)
     *
     * @param $ids
     * @return boolean
     */
    protected function _batch_del($ids)
    {
        if ($ids) {
            foreach($ids as $id) {
                // メッセージ用にデータを取得
                $post = $this->Permission->read(null, $id);
                /* 削除処理 */
                if ($this->Permission->delete($id)) {
                    $message = sprintf(__d('baser', 'アクセス制限設定「%s」 を削除しました。'), $post['Permission']['name']);
                }
            }
        }
        return true;
    }

    /**
     * [ADMIN] 削除処理　(ajax)
     *
     * @param int $id
     * @return void
     */
    public function admin_ajax_delete($id = null)
    {
        $this->_checkSubmitToken();
        /* 除外処理 */
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        // メッセージ用にデータを取得
        $post = $this->Permission->read(null, $id);

        /* 削除処理 */
        if ($this->Permission->delete($id)) {
            $message = sprintf(__d('baser', 'アクセス制限設定「%s」 を削除しました。'), $post['Permission']['name']);
            exit(true);
        }
        exit();
    }

    /**
     * [ADMIN] 削除処理
     *
     * @param int $id
     * @return void
     */
    public function admin_delete($userGroupId, $id = null)
    {
        $this->_checkSubmitToken();
        /* 除外処理 */
        if (!$id) {
            $this->BcMessage->setError(__d('baser', '無効なIDです。'));
            $this->redirect(['action' => 'index']);
        }

        // メッセージ用にデータを取得
        $post = $this->Permission->read(null, $id);

        /* 削除処理 */
        if ($this->Permission->delete($id)) {
            $this->BcMessage->setSuccess(
                sprintf(
                    __d('baser', 'アクセス制限設定「%s」 を削除しました。'), $post['Permission']['name']
                )
            );
        } else {
            $this->BcMessage->setError('データベース処理中にエラーが発生しました。');
        }

        $this->redirect(['action' => 'index', $userGroupId]);
    }

    /**
     * 並び替えを更新する [AJAX]
     *
     * @access    public
     * @param $userGroupId
     * @return void
     */
    public function admin_ajax_update_sort($userGroupId)
    {
        $this->autoRender = false;
        if (!$this->request->data) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
            exit;
        }

        $conditions = $this->_createAdminIndexConditions($userGroupId);
        if (!$this->Permission->changeSort($this->request->getData('Sort.id'), $this->request->getData('Sort.offset'), $conditions)) {
            $this->ajaxError(500, $this->Permission->validationErrors);
            exit;
        }
        echo true;
    }

    /**
     * 管理画面ページ一覧の検索条件を取得する
     *
     * @param $userGroupId
     * @return array
     */
    protected function _createAdminIndexConditions($userGroupId)
    {
        /* 条件を生成 */
        $conditions = [];
        if ($userGroupId) {
            $conditions['Permission.user_group_id'] = $userGroupId;
        }

        return $conditions;
    }

    /**
     * [ADMIN] データコピー（AJAX）
     *
     * @param int $id
     * @return void
     */
    public function admin_ajax_copy($userGroupId, $id)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }

        $result = $this->Permission->copy($id);
        if ($result) {
            $this->setViewConditions('Permission', ['action' => 'admin_index']);
            $result['Permission']['url'] = preg_replace('/^\/admin\//', '/' . Configure::read('Routing.prefixes.0') . '/', $result['Permission']['url']);
            $sortmode = false;
            if (isset($this->passedArgs['sortmode'])) {
                $sortmode = $this->passedArgs['sortmode'];
            }
            $this->set('sortmode', $sortmode);
            $this->set('data', $result);
        } else {
            $this->ajaxError(500, $this->Permission->validationErrors);
        }
    }

    /**
     * [ADMIN] 無効状態にする（AJAX）
     *
     * @param $id
     * @return void
     */
    public function admin_ajax_unpublish($id)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        if (!$this->_changeStatus($id, false)) {
            $this->ajaxError(500, $this->Permission->validationErrors);
            exit;
        }
        exit(true);
    }

    /**
     * [ADMIN] 有効状態にする（AJAX）
     *
     * @param $id
     * @return void
     */
    public function admin_ajax_publish($id)
    {
        $this->_checkSubmitToken();
        if (!$id) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        if (!$this->_changeStatus($id, true)) {
            $this->ajaxError(500, $this->Permission->validationErrors);
            exit;
        }

        exit(true);
    }

    /**
     * 一括公開
     *
     * @param array $ids
     * @return boolean
     */
    protected function _batch_publish($ids)
    {
        if ($ids) {
            foreach($ids as $id) {
                $this->_changeStatus($id, true);
            }
        }
        return true;
    }

    /**
     * 一括非公開
     *
     * @param array $ids
     * @return boolean
     */
    protected function _batch_unpublish($ids)
    {
        if ($ids) {
            foreach($ids as $id) {
                $this->_changeStatus($id, false);
            }
        }
        return true;
    }

    /**
     * ステータスを変更する
     *
     * @param int $id
     * @param boolean $status
     * @return boolean
     */
    protected function _changeStatus($id, $status)
    {
        $statusTexts = [0 => __d('baser', '無効'), 1 => __d('baser', '有効')];
        $data = $this->Permission->find('first', ['conditions' => ['Permission.id' => $id], 'recursive' => -1]);
        $data['Permission']['status'] = $status;
        $this->Permission->set($data);

        if (!$this->Permission->save()) {
            return false;
        }

        $statusText = $statusTexts[$status];
        $this->Permission->saveDbLog(
            sprintf(
                'アクセス制限設定「%s」 を %s に設定しました。',
                $data['Permission']['name'],
                $statusText
            )
        );
        return true;
    }
}
