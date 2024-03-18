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

namespace BaserCore\Service\Admin;

use BaserCore\Service\UserGroupsService;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Service\UsersService;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * UsersAdminService
 */
class UsersAdminService extends UsersService implements UsersAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * UserGroups Service
     * @var UserGroupsServiceInterface|UserGroupsService
     */
    public UserGroupsServiceInterface|UserGroupsService $UserGroupsService;

    /**
     * Pageservice constructor.
     * @checked
     * @unitTest
     * @noTodo
     */
    public function __construct()
    {
        parent::__construct();
        $this->UserGroupsService = $this->getService(UserGroupsServiceInterface::class);
    }

    /**
     * ログインユーザー自身のIDか確認
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isSelf(?int $id): bool
    {
        $loginUser = BcUtil::loginUser();
        return (!empty($id) && !empty($loginUser->id) && $loginUser->id === $id);
    }

    /**
     * 更新ができるかどうか
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isEditable(?int $id): bool
    {
        $user = BcUtil::loginUser();
        if (empty($id) || empty($user)) {
            return false;
        } else {
            return ($this->isSelf($id) || $user->isAdmin());
        }
    }

    /**
     * ユーザーグループが更新可能かどうか
     *
     * - ログインユーザーがシステム管理ユーザーの場合可
     * - 自身に対しての編集でない場合可
     *
     * @param $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isUserGroupEditable(?int $id): bool
    {
        return ($id === null || BcUtil::isAdminUser() || !$this->isSelf($id));
    }

    /**
     * 削除ができるかどうか
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isDeletable(?int $id): bool
    {
        $user = BcUtil::loginUser();
        if (empty($id) || empty($user)) {
            return false;
        }
        return !$this->isSelf($id);
    }

    /**
     * 編集画面に必要なデータを取得する
     * @param int $id
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForEdit(EntityInterface $user): array
    {
        $userGroupList = $this->UserGroupsService->getList();
        $loginUser = BcUtil::loginUser();
        if(!$loginUser->isAddableToAdminGroup()) {
            unset($userGroupList[Configure::read('BcApp.adminGroupId')]);
        }
        return [
            'user' => $user,
            'userGroupList' => $userGroupList,
            'isUserGroupEditable' => $this->isUserGroupEditable($user->id),
            'isDeletable' => $this->isDeletable($user->id)
        ];
    }

    /**
     * 新規登録画面にに必要なデータを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForAdd(EntityInterface $user): array
    {
        $userGroupList = $this->UserGroupsService->getList();
        $loginUser = BcUtil::loginUser();
        if(!$loginUser->isAddableToAdminGroup()) {
            unset($userGroupList[Configure::read('BcApp.adminGroupId')]);
        }
        return [
            'user' => $user,
            'userGroupList' => $userGroupList,
            'isUserGroupEditable' => $this->isUserGroupEditable(null),
        ];
    }

    /**
     * ログイン画面に必要なデータを取得する
     * @param ServerRequest $request
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getViewVarsForLogin(ServerRequest $request): array
    {
        return [
            'savedEnable' => $request->is('https'),
            'isEnableLoginCredit' => (bool) BcSiteConfig::get('login_credit')
        ];
    }

}
