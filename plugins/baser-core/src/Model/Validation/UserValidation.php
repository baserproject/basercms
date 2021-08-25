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

namespace BaserCore\Model\Validation;

use BaserCore\Utility\BcUtil;
use Cake\Utility\Hash;
use Cake\Validation\Validation;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class SiteValidation
 * @package BaserCore\Model\Validation
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
    public static function willChangeSelfGroup($userGroup)
    {
        if(BcUtil::isAdminUser()) {
            return true;
        }
        $loginUser = BcUtil::loginUser();
        if (empty($loginUser->user_groups)) {
            return false;
        }
        $loginGroupId = Hash::extract($loginUser->user_groups, '{n}.id');
        $postGroupId = array_map('intval', $userGroup['_ids']);
        return ($loginGroupId === $postGroupId);
    }

}
