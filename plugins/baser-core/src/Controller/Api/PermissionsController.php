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

namespace BaserCore\Controller\Api;

use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class PermissionsController
 * @uses PermissionsController
 */
class PermissionsController extends BcApiController
{

    /**
     * [API] アクセス制限設定の一覧
     * @param PermissionsServiceInterface $permissionService
     * @param $userGroupId
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(PermissionsServiceInterface $permissionService, $userGroupId)
    {
        $this->request->allowMethod(['get']);

        $this->request = $this->request->withQueryParams(['user_group_id' => $userGroupId]);
        $this->set('permissions', $permissionService->getIndex($this->request->getQueryParams()));
        $this->viewBuilder()->setOption('serialize', ['permissions']);
    }

	/**
	 * 登録処理
     * @checked
     * @noTodo
     * @unitTest
	 */
	public function add(PermissionsServiceInterface $permissionService)
	{
        $this->request->allowMethod(['post', 'delete']);
        try {
            $permission = $permissionService->create($this->request->getData());
            $message = __d('baser', '新規アクセス制限設定「{0}」を追加しました。', $permission->name);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $permission = $e->getEntity();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'permission' => $permission,
            'errors' => $permission->getErrors(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'permission', 'errors']);
    }

}
