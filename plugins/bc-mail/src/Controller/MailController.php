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
use BaserCore\Service\BcCaptchaServiceInterface;
use BaserCore\Utility\BcSiteConfig;
use BcMail\Model\Entity\MailMessage;
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
use Psr\Http\Message\ResponseInterface;

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
        $this->loadComponent('BaserCore.BcFrontContents');
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
     * @checked
     */
    public function beforeFilter(EventInterface $event)
    {
        $redirect = parent::beforeFilter($event);
        if($redirect) return $redirect;

        if (!$this->request->getParam('entityId')) {
            $this->notFound();
        }
        $mailMessagesService = $this->getService(MailMessagesServiceInterface::class);
        $mailMessagesService->MailMessages->setup($this->request->getParam('entityId'), $this->getRequest()->getData());

        // TODO ucmitz 未確認
        return;
        $this->dbDatas['mailContent'] = $this->MailMessage->mailContent;
        $this->dbDatas['mailFields'] = $this->MailMessage->mailFields;
        $this->dbDatas['mailConfig'] = $this->MailConfig->find();

        // 2013/03/14 ryuring
        // baserCMS２系より必須要件をPHP5以上とした為、SecurityComponent を標準で設定する方針に変更
        if (Configure::read('debug') > 0) {
            $this->FormProtection->setConfig('validate', false);
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
            $this->set('unlockedFields', array_merge($this->FormProtection->getConfig('unlockedFields'), $disabledFields));
        }
    }

    /**
     * beforeRender
     *
     * @return void
     * @checked
     */
    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);
        // TODO ucmitz 未実装
        /*
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
    public function confirm(
        MailFrontServiceInterface $service,
        MailContentsServiceInterface $mailContentsService,
        MailMessagesServiceInterface $mailMessagesService,
        BcCaptchaServiceInterface $bcCaptchaService)
    {
        $mailContent = $mailContentsService->get($this->request->getParam('entityId'));
        if (!$service->isAccepting($mailContent)) {
            return $this->render($service->getUnpublishTemplate($mailContent));
        }

        if (!$this->getRequest()->is(['post', 'put'])) {
            return $this->redirect(['action' => 'index']);
        }

        if (!$this->getRequest()->getSession()->read('BcMail.valid')) {
            $this->BcMessage->setError(__d('baser_core', 'エラーが発生しました。もう一度操作してください。'));
            return $this->redirect(['action' => 'index']);
        }

        try {
            // 画像認証
            if ($mailContent->auth_captcha && !$bcCaptchaService->check(
                $this->getRequest(),
                $this->getRequest()->getData('captcha_id'),
                $this->getRequest()->getData('auth_captcha')
            )) {
                // newEntity() だと配列が消えてしまうため、エンティティクラスで直接変換
                $mailMessage = new MailMessage($this->getRequest()->getData(), ['source' => 'BcMail.MailMessages']);

                // 別途バリデーションを行い、キャプチャのエラーとマージしてセット
                $validateEntity = $mailMessagesService->MailMessages->newEntity($this->getRequest()->getData());
                $errors = $validateEntity->getErrors();
                $errors['auth_captcha'] = __d('baser_core', '画像の文字が間違っています。再度入力してください。');
                $mailMessage->setErrors($errors);

                throw new PersistenceFailedException($mailMessage, __d('baser_core', '入力エラーです。内容を見直してください。'));
            }
            $mailMessage = $service->confirm($mailContent, $this->getRequest()->getData());
        } catch (PersistenceFailedException $e) {
            $mailMessage = $e->getEntity();
            $this->BcMessage->setError($e->getMessage());
            $this->set($service->getViewVarsForIndex($mailContent, $mailMessage));
            return $this->render($service->getIndexTemplate($mailContent));
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
        MailMessagesServiceInterface $mailMessagesService,
        BcCaptchaServiceInterface $bcCaptchaService
    )
    {
        $mailContent = $mailContentsService->get($this->request->getParam('entityId'));
        if (!$service->isAccepting($mailContent)) {
            return $this->render($service->getUnpublishTemplate($mailContent));
        }

        if (!$this->getRequest()->is(['post', 'put'])) {
            return $this->redirect(['action' => 'index']);
        }

        if (!$this->getRequest()->getSession()->read('BcMail.valid')) {
            $this->BcMessage->setError(__d('baser_core', 'エラーが発生しました。もう一度操作してください。'));
            return $this->redirect(['action' => 'index']);
        }

        if($this->getRequest()->getData('mode') === 'Back') {
            // newEntity() だと配列が消えてしまうため、エンティティクラスで直接変換
            $this->set($service->getViewVarsForIndex(
                $mailContent,
                new MailMessage($this->getRequest()->getData(), ['source' => 'BcMail.MailMessages'])
            ));
            return $this->render($service->getIndexTemplate($mailContent));
        }

        // メッセージ保存
        try {
            // 画像認証
            if ($mailContent->auth_captcha && !$bcCaptchaService->check(
                $this->getRequest(),
                $this->getRequest()->getData('captcha_id'),
                $this->getRequest()->getData('auth_captcha')
            )) {
                $mailMessage = $mailMessagesService->MailMessages->newEntity($this->getRequest()->getData());
                $mailMessage->setError('auth_captcha', __d('baser_core', '画像の文字が間違っています。再度入力してください。'));
                throw new PersistenceFailedException($mailMessage, __d('baser_core', '入力エラーです。内容を見直してください。'));
            }

            $mailMessagesService->setup($mailContent->id);
            $entity = $mailMessagesService->create($mailContent, $this->getRequest()->getData());
        } catch (PersistenceFailedException $e) {
            $entity = $e->getEntity();
            $mailMessage->auth_captcha = '';
            $this->BcMessage->setError(__d('baser_core', '入力内容を確認し、再度送信してください。'));
            $this->set($service->getViewVarsForConfirm($mailContent, $entity));
            return $this->render($service->getConfirmTemplate($mailContent));
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser_core', 'エラー : 送信中にエラーが発生しました。しばらくたってから再度送信お願いします。') . $e->getMessage());
            return $this->redirect($this->request->getAttribute('currentContent')->url . '/');
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
            $this->BcMessage->setError(__d('baser_core', 'エラー : 送信中にエラーが発生しました。しばらくたってから再度送信お願いします。') . $e->getMessage());
            return $this->redirect($this->request->getAttribute('currentContent')->url . '/');
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
     * @param MailFrontServiceInterface $service
     * @param MailContentsServiceInterface $mailContentsService
     * @return void|ResponseInterface
     * @checked
     * @noTodo
     */
    public function thanks(MailFrontServiceInterface $service, MailContentsServiceInterface $mailContentsService)
    {
        $mailContent = $mailContentsService->get($this->request->getParam('entityId'));
        if (!$service->isAccepting($mailContent)) {
            return $this->render($service->getUnpublishTemplate($mailContent));
        }

        $mailContent = $this->getRequest()->getSession()->consume('BcMail.MailContent');
        if (!$mailContent) $this->notFound();

        $this->set([
            'mailContent' => $mailContent,
            'currentWidgetAreaId' => $mailContent->widget_area?? BcSiteConfig::get('widget_area')
        ]);
        $this->render($service->getThanksTemplate($mailContent));
    }

    /**
     * 認証用のキャプチャ画像を表示する
     *
     * @param BcCaptchaServiceInterface $service
     * @param string $token
     * @return void
     * @checked
     * @noTodo
     */
    public function captcha(BcCaptchaServiceInterface $service, string $token)
    {
        $this->viewBuilder()->disableAutoLayout();
        $service->render($this->getRequest(), $token);
        exit();
    }

}
