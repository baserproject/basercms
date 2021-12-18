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

namespace BaserCore\View\Helper;

use BaserCore\Service\UserGroupServiceInterface;
use BaserCore\Service\UserServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcAdminUserHelper
 * @package BaserCore\View\Helper
 */
class BcAdminUserHelper extends Helper
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * ログインユーザー自身の更新かどうか
     * @param int $id
     * @return false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isSelfUpdate(?int $id)
    {
        return $this->getService(UserServiceInterface::class)->isSelf($id);
    }

    /**
     * 更新ができるかどうか
     * @param int $id
     * @return false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isEditable(?int $id)
    {
        $user = BcUtil::loginUser();
        if (empty($id) || empty($user)) {
            return false;
        } else {
            return ($this->isSelfUpdate($id) || $user->isAdmin());
        }
    }

    /**
     * 削除ができるかどうか
     * @param int $id
     * @return false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isDeletable(?int $id)
    {
        $user = BcUtil::loginUser();
        if (empty($id) || empty($user)) {
            return false;
        }
        return !$this->isSelfUpdate($id);
    }

    /**
     * ユーザーグループ選択用のリスト
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUserGroupList()
    {
        return $this->getService(UserGroupServiceInterface::class)->getList();
    }

}
