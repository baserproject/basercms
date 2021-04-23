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

use BaserCore\Model\Entity\UserGroup;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Model\Table\UserGroupsTable;
use Cake\Controller\ComponentRegistry;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\EventInterface;
use Cake\Event\EventManagerInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UserGroupsController
 * @package BaserCore\Controller\Admin
 * @property UserGroupsTable $UserGroups
 * @property BcMessageComponent $BcMessage
 * @method UserGroup[]|ResultSetInterface paginate($object = null, array $settings = [])
 */
class UserGroupsController extends BcAdminAppController
{
    public $siteConfigs = [];

	/**
	 * UserGroupsController constructor.
	 *
     * @param \Cake\Http\ServerRequest|null $request Request object for this controller. Can be null for testing,
     *   but expect that features that use the request parameters will not work.
     * @param \Cake\Http\Response|null $response Response object for this controller.
     * @param string|null $name Override the name useful in testing when using mocks.
     * @param \Cake\Event\EventManagerInterface|null $eventManager The event manager. Defaults to a new instance.
     * @param \Cake\Controller\ComponentRegistry|null $components The component registry. Defaults to a new instance.
	 */
	public function __construct(
        ?ServerRequest $request = null,
        ?Response $response = null,
        ?string $name = null,
        ?EventManagerInterface $eventManager = null,
        ?ComponentRegistry $components = null
    ) {
		parent::__construct($request, $response, $name, $eventManager, $components);
		$this->crumbs = [
			['name' => __d('baser', 'システム設定'), 'url' => ['controller' => 'site_configs', 'action' => 'form']],
			['name' => __d('baser', 'ユーザーグループ管理'), 'url' => ['controller' => 'user_groups', 'action' => 'index']]
		];
	}

    /**
     * initialize
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        // $this->Authentication->allowUnauthenticated([]);
    }

	/**
	 * beforeFilter
     * @param EventInterface $event
     * @return Response|void|null
     * @checked
	 */
	public function beforeFilter(EventInterface $event)
	{
		parent::beforeFilter($event);

        // TODO 取り急ぎ動作させるためのコード
        // >>>
        $this->siteConfigs['admin_list_num'] = 30;
        return;
        // <<<

		if ($this->request->getParam('prefix') === 'admin') {
			$this->set('usePermission', $this->UserGroup->checkOtherAdmins());
		}

		$authPrefixes = [];
		foreach(Configure::read('BcAuthPrefix') as $key => $authPrefix) {
			$authPrefixes[$key] = $authPrefix['name'];
		}
		if (count($authPrefixes) <= 1) {
			$this->UserGroup->validator()->remove('auth_prefix');
		}
	}

    /**
     * ログインユーザーグループリスト
     *
     * 管理画面にログインすることができるユーザーグループの一覧を表示する
     *
     * - list view
     *  - UserGroup.id
     *    - UserGroup.name
     *  - UserGroup.title
     *  - UserGroup.created && UserGroup.modified
     *
     * - search input
     *    - UserGroup.name
     *
     * - pagination
     * - view num
     *
     * @return Response|null|void Renders view
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index()
    {
        $this->setViewConditions('UserGroup', ['default' => ['query' => [
            'num' => $this->siteConfigs['admin_list_num'],
            'sort' => 'id',
            'direction' => 'asc',
        ]]]);
        $this->paginate = [
            'limit' => $this->request->getQuery('num'),
        ];
        $query = $this->UserGroups->find('all', $this->paginate);
        $this->set([
            'userGroups' => $this->paginate($query),
            '_serialize' => ['userGroups']
        ]);

        $this->setHelp('user_groups_index');
        $this->setTitle(__d('baser', 'ユーザーグループ一覧'));
    }

    /**
     * ユーザーグループ新規追加
     *
     * ユーザーグループの各種情報を新規追加する
     *
     * - input
     *  - UserGroup.name
     *  - UserGroup.title
     *  - UserGroup.use_admin_globalmenu
     *  - UserGroup.use_move_contents
     *  - submit
     *
     * @return Response|null|void Redirects on successful add, renders view otherwise.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add()
    {
        $this->setTitle(__d('baser', '新規ユーザーグループ登録'));
        $this->setHelp('user_groups_form');
        $userGroup = $this->UserGroups->newEmptyEntity();
        $this->set(compact('userGroup'));
        if (!$this->request->is('post')) {
            return;
        }
        $data = $this->request->getData();
        if (empty($data['auth_prefix'])) {
            $data['auth_prefix'] = 'Admin';
        } else {
            $data['auth_prefix'] = implode(',', $data['auth_prefix']);
        }
        $userGroup = $this->UserGroups->patchEntity($userGroup, $data);
        if (!$this->UserGroups->save($userGroup)) {
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            return;
        }
        $this->BcMessage->setSuccess(__d('baser', '新規ユーザーグループ「{0}」を追加しました。', $userGroup->name));
        return $this->redirect(['action' => 'index']);
    }

    /**
     * ユーザーグループ編集
     *
     * ユーザーグループの各種情報を編集する
     *
     * - viewVars
     *  - UserGroup.id
     *  - UserGroup.name
     *  - UserGroup.title
     *  - UserGroup.use_admin_globalmenu
     *  - UserGroup.use_move_contents
     *
     * - input
     *  - UserGroup.name
     *  - UserGroup.title
     *  - UserGroup.use_admin_globalmenu
     *  - UserGroup.use_move_contents
     *  - submit
     *
     * @param string|null $id User Group id.
     * @return Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws RecordNotFoundException When record not found.
     * @checked
     */
    public function edit($id = null)
    {
		if (!$id) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			return $this->redirect(['action' => 'index']);
		}

        $this->setTitle(__d('baser', 'ユーザーグループ編集'));
        $this->setHelp('user_groups_form');
        $userGroup = $this->UserGroups->get($id, [
            'contain' => ['Users'],
        ]);
        $this->set(compact('userGroup'));

        if (!$this->request->is(['patch', 'post', 'put'])) {
            return;
        }

        $data = $this->request->getData();
        if (empty($data['auth_prefix'])) {
            $data['auth_prefix'] = 'Admin';
        } else {
            $data['auth_prefix'] = implode(',', $data['auth_prefix']);
        }
        $userGroup = $this->UserGroups->patchEntity($userGroup, $data);
        if (!$this->UserGroups->save($userGroup)) {
            $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            return;
        }

        $this->BcMessage->setSuccess(__d('baser', 'ユーザーグループ「{0}」を更新しました。', $userGroup->name));
        // TODO 未実装
        /* >>>
        $this->BcAuth->relogin();
        <<< */
        return $this->redirect(['action' => 'index']);
    }

    /**
     * ユーザーグループ削除
     *
     * ユーザーグループを削除する
     *
     * @param string|null $id User Group id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     * @checked
     */
    public function delete($id = null)
    {
        // TODO 未実装
        /* >>>
        $this->_checkSubmitToken();
        <<< */

		/* 除外処理 */
		if (!$id) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			return $this->redirect(['action' => 'index']);
		}
        $this->request->allowMethod(['post', 'delete']);
        $userGroup = $this->UserGroups->get($id);
        if ($this->UserGroups->delete($userGroup)) {
            $this->BcMessage->setSuccess(__d('baser', 'ユーザーグループ「{0}」を削除しました。', $userGroup->name));
        } else {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * ユーザーグループコピー
     *
     * ユーザーグループをコピーする
     *
     * @param string|null $id User Group id.
     * @return Response|null|void Redirects to index.
     * @throws RecordNotFoundException When record not found.
     * @throws CopyFailedException When copy failed.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy($id = null)
    {
        $this->request->allowMethod(['post']);
        if (!$id || !is_numeric($id)) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        $userGroup = $this->UserGroups->get($id);
        try {
            if ($this->UserGroups->copy($id)) {
                $this->BcMessage->setSuccess(__d('baser', 'ユーザーグループ「{0}」をコピーしました。', $userGroup->name));
            } else {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        } catch (\Exception $e) {
            $message = [$e->getMessage()];
            $errors = $e->getErrors();
            if (!empty($errors)) {
                foreach($errors as $error) $message[] = __d('baser', current($error));
            }
            $this->BcMessage->setError(implode("\n", $message), false);
        }
        return $this->redirect(['action' => 'index']);
    }

	/**
	 * [ADMIN] 削除処理 (ajax)
	 *
	 * @param int ID
	 * @return void
	 */
	public function ajax_delete($id = null)
	{
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		// メッセージ用にデータを取得
		$post = $this->UserGroup->read(null, $id);

		/* 削除処理 */
		if (!$this->UserGroup->delete($id)) {
			exit;
		}

		$message = 'ユーザーグループ「' . $post['UserGroup']['title'] . '」 を削除しました。';
		$this->UserGroup->saveDbLog($message);
		exit(true);
	}

	/**
	 * [ADMIN] データコピー（AJAX）
	 *
	 * @param int $id
	 * @return void
	 */
	public function ajax_copy($id)
	{
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		$result = $this->UserGroup->copy($id);
		if (!$result) {
			$this->ajaxError(500, $this->UserGroup->validationErrors);
		} else {
			$this->set('data', $result);
		}
	}

	/**
	 * ユーザーグループのよく使う項目の初期値を登録する
	 * ユーザー編集画面よりAjaxで呼び出される
	 *
	 * @param $id
	 * @return void
	 * @throws Exception
	 */
	public function set_default_favorites($id)
	{
		if (!$this->request->is(['post', 'put'])) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		$defaultFavorites = null;
		if ($this->request->getData()) {
			$defaultFavorites = BcUtil::serialize($this->request->getData());
		}
		$this->UserGroup->id = $id;
		$this->UserGroup->recursive = -1;
		$data = $this->UserGroup->read();
		$data['UserGroup']['default_favorites'] = $defaultFavorites;
		$this->UserGroup->set($data);
		if ($this->UserGroup->save()) {
			echo true;
		}
		exit();
	}

}
