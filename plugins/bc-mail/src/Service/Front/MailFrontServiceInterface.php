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

namespace BcMail\Service\Front;

use BcMail\Model\Entity\MailContent;
use BcMail\Model\Entity\MailMessage;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;

/**
 * MailFrontServiceInterface
 */
interface MailFrontServiceInterface
{

    /**
     * メールフォーム用の View 変数を取得する
     * @param EntityInterface|MailContent $mailContent
     * @param EntityInterface|MailMessage $mailMessages
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex(EntityInterface $mailContent, EntityInterface $mailMessage): array;

    /**
     * プレビュー用のセットアップをする
     *
     * @param Controller $controller
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setupPreviewForIndex(Controller $controller): void;

    /**
     * 確認画面用の View 変数を取得する
     * @param EntityInterface $mailContent
     * @param EntityInterface $mailMessage
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForConfirm(EntityInterface $mailContent, EntityInterface $mailMessage): array;

    /**
     * 送信データの確認を行う
     * @param EntityInterface $mailContent
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function confirm(EntityInterface $mailContent, array $postData): EntityInterface;

    /**
     * メールを送信する
     * @param EntityInterface $mailContent
     * @param EntityInterface $mailMessage
     * @param array $sendEmailOptions
     * @throws \Throwable
     * @checked
     * @noTodo
     * @unitTest
     */
    public function sendMail(EntityInterface $mailContent, EntityInterface $mailMessage, array $sendEmailOptions);

    /**
     * 管理者メールを取得する
     * @param EntityInterface $mailContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAdminMail(EntityInterface $mailContent): string;

    /**
     * ユーザーメールを取得する
     * @param EntityInterface $mailFields
     * @param EntityInterface $mailMessage
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUserMail(ResultSetInterface $mailFields, EntityInterface $mailMessage): string;

    /**
     * 添付ファイルのパスを取得する
     * @param ResultSetInterface $mailFields
     * @param EntityInterface $mailMessage
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAttachments(ResultSetInterface $mailFields, EntityInterface $mailMessage): array;

    /**
     * メール送信用のデータを生成する
     *
     * @param EntityInterface $mailConfig
     * @param EntityInterface $mailContent
     * @param ResultSetInterface $mailFields
     * @param EntityInterface $mailMessage
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createMailData(
        EntityInterface $mailConfig,
        EntityInterface $mailContent,
        ResultSetInterface $mailFields,
        EntityInterface $mailMessage,
        array $options);

    /**
     * 編集リンクを取得する
     * @param int $mailContentId
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEditLink(int $mailContentId);

    /**
     * メールコンテンツに関連するメールフィールドを取得する
     * @param int $mailContentId
     * @return \Cake\Datasource\ResultSetInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMailFields(int $mailContentId);

    /**
     * フォームが公開中かどうかチェックする
     *
     * @param EntityInterface|MailContent $mailContent
     * @return    bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAccepting(EntityInterface $mailContent): bool;

    /**
     * メールフォームのテンプレート名を取得する
     * @param MailContent|EntityInterface|array $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndexTemplate(EntityInterface $mailContent): string;

    /**
     * メールフォーム確認画面のテンプレート名を取得する
     * @param MailContent|EntityInterface|array $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getConfirmTemplate(EntityInterface $mailContent): string;

    /**
     * メールフォームの非公開状態用のテンプレート名を取得する
     * @param MailContent|EntityInterface|array $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUnpublishTemplate(EntityInterface $mailContent): string;

    /**
     * メールフォームの完了画面用のテンプレート名を取得する
     * @param MailContent|EntityInterface|array $blogContent
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getThanksTemplate(EntityInterface $mailContent): string;

}
