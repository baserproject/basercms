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

use BaserCore\Error\BcException;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use BcMail\Model\Entity\MailContent;
use BcMail\Model\Entity\MailMessage;
use BcMail\Service\MailContentsService;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailFieldsService;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\Service\MailMessagesService;
use BcMail\Service\MailMessagesServiceInterface;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\ServerRequest;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * MailFrontService
 * @property MailContentsService $MailContentsService
 */
class MailFrontService implements MailFrontServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use MailerAwareTrait;


    /**
     * Constructor
     *
     * サービスクラスを初期化する
     *
     * @checked
     * @noTodo
     */
    public function __construct()
    {
        $this->MailContentsService = $this->getService(MailContentsServiceInterface::class);
    }

    /**
     * メールフォーム用の View 変数を取得する
     * @param EntityInterface|MailContent $mailContent
     * @param EntityInterface|MailMessage $mailMessages
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForIndex(EntityInterface $mailContent, EntityInterface $mailMessage): array
    {
        return [
            'freezed' => false,
            'mailContent' => $mailContent,
            'mailFields' => $this->getMailFields($mailContent->id),
            'mailMessage' => $mailMessage,
            'editLink' => BcUtil::loginUser()? $this->getEditLink($mailContent->id) : null,
            'currentWidgetAreaId' => $mailContent->widget_area?? BcSiteConfig::get('widget_area')
        ];
    }

    /**
     * プレビュー用のセットアップをする
     *
     * @param Controller $controller
     * @checked
     * @noTodo
     */
    public function setupPreviewForIndex(Controller $controller): void
    {
        // メールコンテンツ取得
        $mailContent = $this->MailContentsService->get(
            (int)$controller->getRequest()->getAttribute('currentContent')->entity_id
        );
        // メールコンテンツをPOSTデータにより書き換え
        $mailContent = $this->MailContentsService->MailContents->patchEntity(
            $mailContent,
            $controller->getRequest()->getData()
        );
        // ブログコンテンツのアップロードファイルをPOSTデータにより書き換え
        $mailContent->content = $this->MailContentsService->MailContents->Contents->saveTmpFiles(
            $controller->getRequest()->getData('content'),
            mt_rand(0, 99999999)
        );
        // Request のカレンドコンテンツを書き換え
        $controller->setRequest($controller->getRequest()->withAttribute('currentContent', $mailContent->content));
        /** @var MailMessagesService $mailMessagesService */
        $mailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        /* @var MailContent $mailContent */
        $controller->set($this->getViewVarsForIndex(
            $mailContent,
            $mailMessagesService->getNew($mailContent->id, [])
        ));
        $controller->set('title', $mailContent->content->title);
        $controller->viewBuilder()->setTemplate($this->getIndexTemplate($mailContent));
    }

    /**
     * 確認画面用の View 変数を取得する
     * @param EntityInterface $mailContent
     * @param EntityInterface $mailMessage
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForConfirm(EntityInterface $mailContent, EntityInterface $mailMessage): array
    {
        if (!$mailMessage->getErrors()) {
            $freezed = true;
            $error = false;
        } else {
            $freezed = false;
            $error = true;
            $mailMessage->auth_captcha = null;
            $mailMessage->captcha_id = null;
        }

        return [
            'error' => $error,
            'freezed' => $freezed,
            'mailContent' => $mailContent,
            'mailFields' => $this->getMailFields($mailContent->id),
            'mailMessage' => $mailMessage,
            'editLink' => BcUtil::loginUser()? $this->getEditLink($mailContent->id) : null,
            'currentWidgetAreaId' => $mailContent->widget_area?? BcSiteConfig::get('widget_area')
        ];
    }

    /**
     * 送信データの確認を行う
     * @param EntityInterface $mailContent
     * @param array $postData
     * @return EntityInterface
     */
    public function confirm(EntityInterface $mailContent, array $postData): EntityInterface
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__(
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        // fileタイプへの送信データ検証
        if (!$this->_checkDirectoryRraversal($mailContent->id, $postData)) {
            throw new BcException(__d('baser', '不正なファイル送信です。'), 500);
        }

        // 画像認証を行う
        // TODO ucmitz 未実装
        // BcCaptcha を 通常クラス化する
//        if ($mailContent->auth_captcha) {
//            $captchaResult = $this->BcCaptcha->check(
//                Hash::get($this->request->getData(), 'MailMessage.auth_captcha'),
//                Hash::get($this->request->getData(), 'MailMessage.captcha_id')
//            );
//            if (!$captchaResult) {
//                $mailMessagesTable->invalidate('auth_captcha');
//            }
//        }

        /** @var MailMessagesService $mailMessagesService */
        $mailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $messageArray = $mailMessagesService->autoConvert($mailContent->id, $postData);
        $message = $mailMessagesService->MailMessages->newEntity($messageArray);
        if (!$message->getErrors()) {
            $message = $mailMessagesService->MailMessages->saveTmpFiles($messageArray, mt_rand(0, 99999999));
            // 2022/12/18 by ryuring
            // saveTmpFiles() 作成される Entity は無名の Entity のため、MailMessage に変換するため、再度、インスタンス化する
            // テーブルの newEntity() を利用すると、存在しないフィールド（_tmp 付きのフィールド）が消えてしまうため、
            // エンティティをそのまま new するが、テーブルの newEntity() を利用しないと、テーブルとの関連付けができず、
            // フォームの初期化でエラーとなってしまう。そのため、source オプションで明示的にテーブルを指定する
            return new MailMessage($message->toArray(), ['source' => 'BcMail.MailMessages']);
        } else {
            throw new PersistenceFailedException($message, __('エラー : 入力内容を確認して再度送信してください。'));
        }
    }

    /**
     * メールを送信する
     * @param EntityInterface $mailContent
     * @param EntityInterface $mailMessage
     * @param array $sendEmailOptions
     * @throws \Throwable
     * @checked
     * @noTodo
     */
    public function sendMail(EntityInterface $mailContent, EntityInterface $mailMessage, array $sendEmailOptions)
    {
        $mailFields = $this->getMailFields($mailContent->id);
        // メール送信用にハッシュ化前のパスワードをマスクして保持
        $sendEmailPasswords = [];
        foreach($mailFields as $field) {
            if ($field->type !== 'password') continue;
            $sendEmailPasswords[$field->field_name] = preg_replace('/./', '*', $mailMessage->{$field->field_name});
        }
        if (!empty($sendEmailPasswords)) $sendEmailOptions['maskedPasswords'] = $sendEmailPasswords;

        // メール送信
        try {
            $mailConfigsTable = TableRegistry::getTableLocator()->get('BcMail.MailConfigs');
            $mailConfig = $mailConfigsTable->find()->first();
            $adminMail = $this->getAdminMail($mailContent);
            $userMail = $this->getUserMail($mailFields, $mailMessage);
            $attachments = $this->getAttachments($mailFields, $mailMessage);
            $data = $this->createMailData($mailConfig, $mailContent, $mailFields, $mailMessage, $sendEmailOptions);
            $this->getMailer('BcMail.MailMessage')->send('sendFormToAdmin', [
                $mailContent,
                $adminMail,
                $userMail,
                $data,
                $attachments,
                $sendEmailOptions
            ]);
            if ($userMail) {
                $this->getMailer('BcMail.MailMessage')->send('sendFormToUser', [
                    $mailContent,
                    $adminMail,
                    $userMail,
                    $data,
                    $sendEmailOptions
                ]);
            }
            if ($mailContent->save_info) return;
            foreach($mailFields as $field) {
                if ($field->type !== 'file') continue;
                // 削除フラグをセット
                $mailMessage->{$field->field_name . '_delete'} = true;
            }
            // TODO ucmitz 未検証
            $mailMessagesTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
            $mailMessagesTable->getFileUploader()->deleteFiles($mailMessage);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * 管理者メールを取得する
     * @param EntityInterface $mailContent
     * @return string
     * @checked
     * @noTodo
     */
    public function getAdminMail(EntityInterface $mailContent): string
    {
        if ($mailContent->sender_1) {
            $adminMail = $mailContent->sender_1;
        } else {
            $adminMail = BcSiteConfig::get('email');
        }
        return $adminMail;
    }

    /**
     * ユーザーメールを取得する
     * @param EntityInterface $mailFields
     * @param EntityInterface $mailMessage
     * @return string
     * @checked
     * @noTodo
     */
    public function getUserMail(ResultSetInterface $mailFields, EntityInterface $mailMessage): string
    {
        $userMail = '';
        foreach($mailFields as $mailField) {
            if (empty($mailMessage->{$mailField->field_name})) continue;
            $value = $mailMessage->{$mailField->field_name};
            if ($mailField->type === 'email' && $value) $userMail = $value;
        }
        // 前バージョンとの互換性のため type が email じゃない場合にも取得できるようにしておく
        if (!$userMail) {
            if (!empty($mailMessage->email)) {
                $userMail = $mailMessage->email;
            } elseif (!empty($mailMessage->email_1)) {
                $userMail = $mailMessage->email_1;
            }
        }
        return $userMail;
    }

    /**
     * 添付ファイルのパスを取得する
     * @param Query $mailFields
     * @param EntityInterface $mailMessage
     * @return array
     */
    public function getAttachments(ResultSetInterface $mailFields, EntityInterface $mailMessage): array
    {
        $attachments = [];
        /** @var MailMessagesService $mailMessagesService */
        $mailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $settings = $mailMessagesService->MailMessages->getFileUploader()->settings;
        foreach($mailFields as $mailField) {
            if (empty($mailMessage->{$mailField->field_name})) continue;
            $value = $mailMessage->{$mailField->field_name};
            if ($mailField->type === 'file' && $value) {
                $attachments[] = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS . $value;
            }
        }
        return $attachments;
    }

    /**
     * メール送信用のデータを生成する
     *
     * @param EntityInterface $mailConfig
     * @param EntityInterface $mailContent
     * @param Query $mailFields
     * @param EntityInterface $mailMessage
     * @return array
     * @checked
     * @noTodo
     */
    public function createMailData(
        EntityInterface $mailConfig,
        EntityInterface $mailContent,
        ResultSetInterface $mailFields,
        EntityInterface $mailMessage,
        array $options)
    {
        // データを整形
        /** @var MailMessagesService $mailMessagesService */
        $mailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $mailMessage = $mailMessagesService->MailMessages->convertToDb($mailFields, $mailMessage);
        return $mailMessagesService->MailMessages->convertDatasToMail([
            'message' => $mailMessage,
            'content' => $mailContent->content,
            'mailFields' => $mailFields,
            'mailContent' => $mailContent,
            'mailConfig' => $mailConfig,
            'other' => [
                'date' => date('Y/m/d H:i')
            ]
        ], $options);
    }

    /**
     * 編集リンクを取得する
     * @param int $mailContentId
     * @return array
     * @checked
     * @noTodo
     */
    public function getEditLink(int $mailContentId)
    {
        return [
            'prefix' => 'Admin',
            'plugin' => 'BcMail',
            'controller' => 'MailContents',
            'action' => 'edit',
            $mailContentId
        ];
    }

    /**
     * メールコンテンツに関連するメールフィールドを取得する
     * @param int $mailContentId
     * @return \Cake\Datasource\ResultSetInterface
     * @checked
     * @noTodo
     */
    public function getMailFields(int $mailContentId)
    {
        /** @var MailFieldsService $mailFieldsService */
        $mailFieldsService = $this->getService(MailFieldsServiceInterface::class);
        return $mailFieldsService->getIndex($mailContentId, ['use_field' => true]);
    }

    /**
     * ファイルフィールドのデータがアップロードされたファイルパスであることを検証する
     *
     * @param int $mailContentId
     * @param ServerRequest $request
     * @return boolean
     * @checked
     * @noTodo
     */
    private function _checkDirectoryRraversal(int $mailContentId, array $postData)
    {
        $mailFields = $this->getMailFields($mailContentId);
        $mailMessagesTable = TableRegistry::getTableLocator()->get('BcMail.MailMessages');
        if (
            !$mailFields
            || empty($mailMessagesTable->getBehavior('BcUpload')->getSettings())
        ) {
            return false;
        }
        foreach($mailFields as $mailField) {
            if ($mailField->type !== 'file') continue;
            $tmp_name = Hash::get($postData, $mailField->field_name . '.tmp_name');
            if ($tmp_name && !is_uploaded_file($tmp_name)) {
                return false;
            }
        }
        return true;
    }

    /**
     * フォームが公開中かどうかチェックする
     *
     * @param EntityInterface|MailContent $mailContent
     * @return    bool
     * @checked
     * @noTodo
     */
    public function isAccepting(EntityInterface $mailContent): bool
    {
        $publishBegin = $mailContent->publish_begin;
        $publishEnd = $mailContent->publish_end;
        if ($publishBegin && $publishBegin !== '0000-00-00 00:00:00') {
            if ($publishBegin > date('Y-m-d H:i:s')) {
                return false;
            }
        }
        if ($publishEnd && $publishEnd !== '0000-00-00 00:00:00') {
            if ($publishEnd < date('Y-m-d H:i:s')) {
                return false;
            }
        }
        return true;
    }

    /**
     * メールフォームのテンプレート名を取得する
     * @param MailContent|EntityInterface|array $blogContent
     * @return string
     * @checked
     * @noTodo
     */
    public function getIndexTemplate(EntityInterface $mailContent): string
    {
        return 'Mail/' . $mailContent->form_template . DS . 'index';
    }

    /**
     * メールフォーム確認画面のテンプレート名を取得する
     * @param MailContent|EntityInterface|array $blogContent
     * @return string
     * @checked
     * @noTodo
     */
    public function getConfirmTemplate(EntityInterface $mailContent): string
    {
        return 'Mail/' . $mailContent->form_template . DS . 'confirm';
    }

    /**
     * メールフォームの非公開状態用のテンプレート名を取得する
     * @param MailContent|EntityInterface|array $blogContent
     * @return string
     * @checked
     * @noTodo
     */
    public function getUnpublishTemplate(EntityInterface $mailContent): string
    {
        return 'Mail/' . $mailContent->form_template . DS . 'unpublish';
    }

    /**
     * メールフォームの完了画面用のテンプレート名を取得する
     * @param MailContent|EntityInterface|array $blogContent
     * @return string
     * @checked
     * @noTodo
     */
    public function getThanksTemplate(EntityInterface $mailContent): string
    {
        return 'Mail/' . $mailContent->form_template . DS . 'submit';
    }

}
