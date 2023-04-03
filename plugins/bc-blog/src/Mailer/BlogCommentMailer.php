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

namespace BcBlog\Mailer;

use BaserCore\Mailer\BcMailer;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * BlogCommentMailer
 */
class BlogCommentMailer extends BcMailer
{

    /**
     * 管理者宛にフォームの内容を送信する
     *
     * @param array $data
     * @checked
     * @noTodo
     */
    public function sendCommentToAdmin(string $senderName, array $data)
    {
        $adminMail = BcSiteConfig::get('email');
        if (strpos($adminMail, ',') !== false) {
            [$fromAdmin] = explode(',', $adminMail);
        } else {
            $fromAdmin = $adminMail;
        }

        $this->setTo($adminMail)
            ->setFrom($fromAdmin, $senderName)
            ->setReplyTo($fromAdmin)
            ->setSubject(__d('baser_core', '【{0}】コメントを受け付けました', $senderName))
            ->viewBuilder()
            ->setTemplate('BcBlog.blog_comment_admin')
            ->setVars($data);
    }

    /**
     * ユーザー宛にフォームの内容を送信する（サンクスメール）
     *
     * @param string $userMail
     * @param array $data
     * @checked
     * @noTodo
     */
    public function sendCommentToUser(string $senderName, string $userMail, array $data)
    {
        $adminMail = BcSiteConfig::get('email');
        if (strpos($adminMail, ',') !== false) {
            [$fromAdmin] = explode(',', $adminMail);
        } else {
            $fromAdmin = $adminMail;
        }
        // タイトル
        $subject = __d('baser_core', '【{0}】コメントが投稿されました', $senderName);

        $this->setTo($userMail)
            ->setFrom($fromAdmin, $senderName)
            ->setReplyTo($fromAdmin)
            ->setSubject($subject)
            ->viewBuilder()
            ->setTemplate('BcBlog.blog_comment_contributor')
            ->setVars($data);
    }

}
