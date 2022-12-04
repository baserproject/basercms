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

namespace BcMail\Controller;

use BaserCore\Error\BcException;
use BcMail\Service\Front\MailFrontService;
use BcMail\Service\Front\MailFrontServiceInterface;
use BcMail\Service\MailContentsService;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailMessagesService;
use BcMail\Service\MailMessagesServiceInterface;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * お問い合わせメールフォーム用コントローラー
 */
class MailController extends MailFrontAppController
{

    /**
     * initialize
     *
     * コンポーネントをロードする
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcFrontContents', ['isContentsPage' => false]);
        // TODO ucmitz 未実装
//        $this->loadComponent('BaserCore.BcCaptcha');
    }

    /**
     * CSS
     *
     * @var array
     */
    public $css = ['mail/form'];

    /**
     * データベースデータ
     *
     * @var array
     */
    public $dbDatas = null;

    /**
     * beforeFilter.
     *
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        if (!$this->request->getParam('entityId')) {
            $this->notFound();
        }
        $mailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        // TODO ucmitz 未実装
        return;
        $mailMessagesService->MailMessages->setup($this->request->getParam('entityId'));
        $this->dbDatas['mailContent'] = $this->MailMessage->mailContent;
        $this->dbDatas['mailFields'] = $this->MailMessage->mailFields;
        $this->dbDatas['mailConfig'] = $this->MailConfig->find();

        // ページタイトルをセット
        $this->setTitle($this->request->getParam('Content.title'));

        if (empty($this->contentId)) {
            // 配列のインデックスが無いためエラーとなるため修正
            $this->contentId = $this->request->getParam('entityId');
        }

        $this->subMenuElements = ['default'];

        // 2013/03/14 ryuring
        // baserCMS２系より必須要件をPHP5以上とした為、SecurityComponent を標準で設定する方針に変更
        if (Configure::read('debug') > 0) {
            $this->Security->validatePost = false;
            $this->Security->csrfCheck = false;
        } else {
            // PHP4でセキュリティコンポーネントがうまくいかなかったので利用停止
            // 詳細はコンポーネント設定のコメントを参照
            $disabledFields = ['MailMessage.mode'];
            // type="file" を除外
            foreach($this->MailMessage->mailFields as $field) {
                if (Hash::get($field, 'MailField.type') === 'file') {
                    $disabledFields[] = $field['MailField']['field_name'];
                }
            }
            $this->Security->requireAuth('confirm', 'submit');
            $this->set('unlockedFields', array_merge($this->Security->unlockedFields, $disabledFields));

            // SSL設定
            if ($this->dbDatas['mailContent']['MailContent']['ssl_on']) {
                $this->Security->blackHoleCallback = 'sslFail';
                $this->Security->requireSecure = am($this->Security->requireSecure, ['index', 'confirm', 'submit']);
            }
        }
    }

    /**
     * beforeRender
     *
     * @return void
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        // TODO ucmitz 未実装
        /*if ($this->dbDatas['mailContent']['MailContent']['widget_area']) {
            $this->set('widgetArea', $this->dbDatas['mailContent']['MailContent']['widget_area']);
        }

        // キャッシュ対策
        if (!isConsole() && !$this->request->getParam('requested')) {
            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: " . date(DATE_RFC1123, strtotime("-1 day")));
        }*/
    }

    /**
     * [PUBIC] フォームを表示する
     *
     * @param MailFrontService $service
     * @param MailContentsService $mailContentsService
     * @param MailMessagesService $mailMessagesService
     * @return void
     * @checked
     * @noTodo
     */
    public function index(
        MailFrontServiceInterface $service,
        MailContentsServiceInterface $mailContentsService,
        MailMessagesServiceInterface $mailMessagesService
    )
    {
        $mailContent = $mailContentsService->get($this->request->getParam('entityId'));
        if (!$service->isAccepting($mailContent)) {
            $this->render($service->getUnpublishTemplate($mailContent));
            return;
        }

        if (!$this->getRequest()->is(['post', 'put'])) {
            $mailMessage = $mailMessagesService->getNew($mailContent->id, $this->getRequest()->getQueryParams());
        } else {
            $mailMessage = $mailMessagesService->getNew($mailContent->id, $this->getRequest()->getData());
        }

        $this->getRequest()->getSession()->write('BcMail.valid', true);
        $this->set($service->getViewVarsForIndex($mailContent, $mailMessage));
        $this->render($service->getIndexTemplate($mailContent));
    }

    /**
     * [PUBIC] データの確認画面を表示
     *
     * @param MailFrontService $service
     * @param MailContentsService $mailContentsService
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     */
    public function confirm(MailFrontServiceInterface $service, MailContentsServiceInterface $mailContentsService)
    {
        $mailContent = $mailContentsService->get($this->request->getParam('entityId'));
        if (!$service->isAccepting($mailContent)) {
            $this->render($service->getUnpublishTemplate($mailContent));
            return;
        }

        if (!$this->getRequest()->getSession()->read('BcMail.valid') || !$this->getRequest()->is(['post', 'put'])) {
            $this->BcMessage->setError('エラーが発生しました。もう一度操作してください。');
            $this->redirect($this->request->getParam('Content.url') . '/index');
        }

        try {
            $mailMessage = $service->confirm($mailContent, $this->getRequest()->getData());
        } catch (PersistenceFailedException $e) {
            $mailMessage = $e->getEntity();
            $this->BcMessage->setError($e->getMessage());
        } catch (BcException $e) {
            $this->BcMessage->setError($e->getMessage());
            if ($e->getCode() === 500) {
                return $this->redirect($this->request->getAttribute('currentContent')->url . '/index');
            }
        }
        $this->set($service->getViewVarsForConfirm($mailContent, $mailMessage));
        $this->render($service->getConfirmTemplate($mailContent));
    }

    /**
     * [PUBIC] データ送信
     *
     * @param mixed mail_content_id
     * @return void|Response
     * @checked
     * @noTodo
     */
    public function submit(
        MailFrontServiceInterface $service,
        MailContentsServiceInterface $mailContentsService,
        MailMessagesServiceInterface $mailMessagesService
    )
    {
        $mailContent = $mailContentsService->get($this->request->getParam('entityId'));
        if (!$service->isAccepting($mailContent)) {
            $this->render($service->getUnpublishTemplate($mailContent));
            return;
        }
        if (!$this->getRequest()->getSession()->read('BcMail.valid') || !$this->getRequest()->is(['post', 'put'])) {
            $this->BcMessage->setError('エラーが発生しました。もう一度操作してください。');
            $this->redirect($this->request->getParam('Content.url') . '/index');
        }

        // 戻る
        if ($this->getRequest()->getData('mode') === 'Back') {
            $this->set($service->getViewVarsForConfirm(
                $mailContent,
                $mailMessagesService->MailMessages->newEntity($this->getRequest()->getData())
            ));
            $this->render($service->getConfirmTemplate($mailContent));
            return;
        }

        // データ確認
        try {
            $entity = $service->confirm($mailContent, $this->getRequest()->getData());
        } catch (PersistenceFailedException $e) {
            $entity = $e->getEntity();
            $this->BcMessage->setError($e->getMessage());
        } catch (BcException $e) {
            $this->BcMessage->setError($e->getMessage());
            if ($e->getCode() === 500) {
                return $this->redirect($this->request->getAttribute('currentContent')->url . '/index');
            }
        }

        // メッセージ保存
        try {
            $mailMessagesService->setup($mailContent->id);
            $entity = $mailMessagesService->create($mailContent, $entity);
        } catch (PersistenceFailedException $e) {
            $entity = $e->getEntity();
            $this->BcMessage->setError(__('入力内容を確認し、再度送信してください。'));
            $this->set($service->getViewVarsForConfirm($mailContent, $entity));
            $this->render($service->getConfirmTemplate($mailContent));
            return;
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__('エラー : 送信中にエラーが発生しました。しばらくたってから再度送信お願いします。') . $e->getMessage());
            return $this->redirect($this->request->getAttribute('currentContent')->url . '/index');
        }

        // EVENT Mail.beforeSendEmail
        $event = $this->dispatchLayerEvent('beforeSendEmail', [
            'data' => $entity
        ]);
        $sendEmailOptions = [];
        if ($event !== false) {
            $this->request = $this->request->withParsedBody($event->getResult() === true? $event->getData('data') : $event->getResult());
            if (!empty($event->getData('sendEmailOptions'))) $sendEmailOptions = $event->getData('sendEmailOptions');
        }

        // メール送信
        try {
            $service->sendMail($mailContent, $entity, $sendEmailOptions);
            $this->getRequest()->getSession()->delete('BcMail.valid');
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__('エラー : 送信中にエラーが発生しました。しばらくたってから再度送信お願いします。') . $e->getMessage());
            return $this->redirect($this->request->getAttribute('currentContent')->url . '/index');
        }

        // EVENT Mail.afterSendEmail
        $this->dispatchLayerEvent('afterSendEmail', [
            'data' => $this->request->getData()
        ]);

        $this->getRequest()->getSession()->write('BcMail.MailContent', $mailContent);
        return $this->redirect($this->request->getAttribute('currentContent')->url . '/thanks');
    }

    /**
     * [PUBIC] メール送信完了
     *
     * @return void
     */
    public function thanks(MailFrontServiceInterface $service, MailContentsServiceInterface $mailContentsService)
    {
        $mailContent = $mailContentsService->get($this->request->getParam('entityId'));
        if (!$service->isAccepting($mailContent)) {
            $this->render($service->getUnpublishTemplate($mailContent));
            return;
        }

        $mailContent = $this->getRequest()->getSession()->consume('BcMail.MailContent');
        if (!$mailContent) $this->notFound();

        $this->set('mailContent', $mailContent);
        $this->render($service->getThanksTemplate($mailContent));
    }

    /**
     * 認証用のキャプチャ画像を表示する
     *
     * @return void
     */
    public function captcha($token = null)
    {
        $this->BcCaptcha->render($token);
        exit();
    }

}
