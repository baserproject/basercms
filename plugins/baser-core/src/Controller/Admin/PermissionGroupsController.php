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

namespace BaserCore\Controller\Admin;

use BaserCore\Service\Admin\PermissionGroupsAdminService;
use BaserCore\Service\Admin\PermissionGroupsAdminServiceInterface;
use BaserCore\Service\PermissionGroupsServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * PermissionGroupsController
 */
class PermissionGroupsController extends BcAdminAppController
{

    /**
     * アクセスルールグループの一覧を表示する
     *
     * @param PermissionGroupsAdminService $service
     * @param int|null $userGroupId
     */
    public function index(PermissionGroupsAdminServiceInterface $service, ?int $userGroupId = null)
    {
        $this->setViewConditions('Site', ['default' => ['query' => [
            'list_type' => 'Admin'
        ]]]);

        if(is_null($userGroupId)) {
            $userGroupId = $service->getAvailableMinUserGroupId();
            if(!$userGroupId) {
                $this->BcMessage->setWarning(__d('baser_core', 'アクセスルールを設定できるユーザーグループがありません。'));
                return $this->redirect('/baser/admin');
            }
        }

        $request = $this->getRequest();
        $this->setRequest($request->withParsedBody([
            'list_type' => $request->getQuery('list_type'),
            'filter_user_group_id' => $userGroupId
        ]));
        $this->set($service->getViewVarsForIndex($userGroupId, $request));
    }

    /**
     * アクセスグループ新規登録
     *
     * @param PermissionGroupsAdminServiceInterface $service
     * @param int $userGroupId
     * @param int $id
     */
    public function add(PermissionGroupsAdminServiceInterface $service, int $userGroupId, string $prefix)
    {
        if($this->getRequest()->is(['post', 'put'])) {
            try {
                $entity = $service->create($this->getRequest()->getData());
                $this->BcMessage->setSuccess(__d('baser_core', 'ルールグループ「{0}」を登録しました。', $entity->name));
                $this->redirect(['action' => 'edit', $userGroupId, $entity->id]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
            }
        }
        $this->set($service->getViewVarsForForm(
            $userGroupId,
            $entity?? $service->getNew($prefix)
        ));
    }

    /**
     * アクセスグループ編集
     *
     * @param PermissionGroupsAdminServiceInterface $service
     * @param int $userGroupId
     * @param int $id
     */
    public function edit(PermissionGroupsAdminServiceInterface $service, int $userGroupId, int $id)
    {
        $entity = $service->get($id, $userGroupId);
        if($this->getRequest()->is(['post', 'put'])) {
            try {
                $entity = $service->update($entity, $this->getRequest()->getData());
                $this->BcMessage->setSuccess(__d('baser_core', 'ルールグループ「{0}」を更新しました。', $entity->name));
                $this->redirect(['action' => 'edit', $userGroupId, $id]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
            }
        }
        $this->set($service->getViewVarsForForm($userGroupId, $entity));
    }

    /**
     * アクセスグループを削除する
     *
     * @param PermissionGroupsServiceInterface $service
     * @param int $id
     * @return \Cake\Http\Response|null
     */
    public function delete(PermissionGroupsServiceInterface $service, int $userGroupId, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        try {
            $entity = $service->get($id);
            if ($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser_core', 'アクセスグループ「{0}」を削除しました。', $entity->name));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index', $userGroupId]);
    }

    /**
     * ユーザーグループを指定してアクセスルールを再構築する
     *
     * @param PermissionGroupsServiceInterface $service
     * @param int $userGroupId
     * @return \Cake\Http\Response|void|null
     */
    public function rebuild_by_user_group(PermissionGroupsServiceInterface $service, int $userGroupId)
    {
        $this->request->allowMethod(['post', 'put']);
        if($service->rebuildByUserGroup($userGroupId)) {
            $this->BcMessage->setSuccess(__d('baser_core', 'アクセスルールの再構築に成功しました。'));
        } else {
            $this->BcMessage->setError(__d('baser_core', 'アクセスルールの再構築に失敗しました。'));
        }
        return $this->redirect(['action' => 'index', $userGroupId]);
    }

}
