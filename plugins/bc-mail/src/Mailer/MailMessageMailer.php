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

namespace BcMail\Mailer;

use BaserCore\Mailer\BcMailer;
use Cake\Datasource\EntityInterface;
use Cake\Utility\Inflector;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * MailMessageMailer
 */
class MailMessageMailer extends BcMailer
{

    /**
     * 管理者宛にフォームの内容を送信する
     *
     * @param EntityInterface $mailContent
     * @param string $adminMail
     * @param string $userMail
     * @param array $data
     * @param array $attachments
     * @param array $options
     * @checked
     * @noTodo
     */
    public function sendFormToAdmin(
        EntityInterface $mailContent,
        string $adminMail,
        string $userMail,
        array $data,
        array $attachments,
        array $options = []
    )
    {
        $options = array_merge([
            'toAdmin' => [],
        ], $options);
        if (strpos($adminMail, ',') !== false) {
            [$fromAdmin] = explode(',', $adminMail);
        } else {
            $fromAdmin = $adminMail;
        }
        $data['other']['mode'] = 'admin';
        $this->setTo($adminMail)
            ->setFrom($fromAdmin, $mailContent->sender_name)
            ->setSubject($mailContent->subject_admin)
            ->setAttachments($attachments)
            ->viewBuilder()
            ->setClassName('BcMail.MailFrontEmail')
            ->setTemplate('BcMail.' . $mailContent->mail_template)
            ->setVars($data);
        if($mailContent->sender_2) {
            $this->setBcc($mailContent->sender_2);
        }
        if($userMail) {
            // カンマ区切りで複数設定されていた場合先頭のアドレスをreplayToに利用
            $this->setReplyTo(strpos($userMail, ',') === false? $userMail : strstr($userMail, ',', true));
        }
        if (empty($options['toAdmin'])) return;
        foreach($options['toAdmin'] as $key => $value) {
            $method = 'set' . Inflector::camelize($key);
            if (method_exists($this, $method)) continue;
            $this->{$method}($value);
        }
    }

    /**
     * ユーザー宛にフォームの内容を送信する（サンクスメール）
     *
     * @param EntityInterface $mailContent
     * @param string $adminMail
     * @param string $userMail
     * @param array $data
     * @param array $options
     * @checked
     * @noTodo
     */
    public function sendFormToUser(
        EntityInterface $mailContent,
        string $adminMail,
        string $userMail,
        array $data,
        array $options = []
    )
    {
        $options = array_merge([
            'toUser' => [],
        ], $options);
        if (strpos($adminMail, ',') !== false) {
            [$fromAdmin] = explode(',', $adminMail);
        } else {
            $fromAdmin = $adminMail;
        }
        $data['other']['mode'] = 'user';
        $this->setTo($userMail)
            ->setFrom($fromAdmin, $mailContent->sender_name)
            ->setReplyTo($fromAdmin)
            ->setSubject($mailContent->subject_user)
            ->viewBuilder()
            ->setClassName('BcMail.MailFrontEmail')
            ->setTemplate('BcMail.' . $mailContent->mail_template)
            ->setVars($data);
        if (empty($options['toUser'])) return;
        foreach($options['toUser'] as $key => $value) {
            $method = 'set' . Inflector::camelize($key);
            if (!method_exists($this, $method)) continue;
            $this->{$method}($value);
        }
    }

}
