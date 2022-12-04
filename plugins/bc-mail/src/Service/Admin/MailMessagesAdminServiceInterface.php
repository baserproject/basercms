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

namespace BcMail\Service\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\ORM\ResultSet;

/**
 * MailMessagesAdminServiceInterface
 */
interface MailMessagesAdminServiceInterface
{

    /**
     * メールメッセージ一覧用の View 変数を取得する
     *
     * @param int $mailContentId
     * @param ResultSet $mailMessages
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForIndex(int $mailContentId, ResultSet $mailMessages): array;

}
