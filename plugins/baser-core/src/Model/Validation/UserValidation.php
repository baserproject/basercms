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

namespace BaserCore\Model\Validation;

use BaserCore\Model\Entity\User;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Validation\Validation;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UserValidation
 */
class UserValidation extends Validation
{

    /**
     * 自身の属するユーザーグループを更新しようとしているかどうか確認
     *
     * - 連続してスラッシュは入力できない
     * - 先頭と末尾にスラッシュは入力できない
     * @param string $alias
     * @return bool
     * @unitTest
     * @noTodo
     * @checked
     */
    public static function willChangeSelfGroup($userGroup, $context)
    {
        if (empty($context['data']['login_user_id'])) {
            return false;
        } else {
            $loginUserId = (string) $context['data']['login_user_id'];
        }
        $users = TableRegistry::getTableLocator()->get('BaserCore.Users');
        /* @var User $loginUser */
        $loginUser = $users->find()->contain('UserGroups')->where(['Users.id' => $loginUserId])->first();

        // 自身の変更ではない
        if($context['data']['id'] !== $loginUserId) {
            return true;
        }

        // スーパーユーザーはシステム管理グループが含まれていれば自身のユーザーグループも変更可能
        if($loginUser->isSuper()) {
            if(in_array(Configure::read('BcApp.adminGroupId'), $userGroup['_ids'])) {
                return true;
            }
            return false;
        }

        // 自身のユーザーグループを変更しているかどうか
        $loginGroupId = Hash::extract($loginUser->user_groups, '{n}.id');
        $postGroupId = array_map('intval', $userGroup['_ids']);
        return ($loginGroupId === $postGroupId);
    }

}
