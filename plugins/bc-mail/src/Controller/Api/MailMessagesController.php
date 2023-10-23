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

namespace BcMail\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcMail\Service\Front\MailFrontServiceInterface;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailMessagesService;
use BcMail\Service\MailMessagesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールフィールドコントローラー
 */
class MailMessagesController extends BcApiController
{

    /**
     * Before Filter
     *
     * @param EventInterface $event
     * @return \Cake\Http\Response|void
     * @checked
     * @noTodo
     */
    public function beforeFilter(EventInterface $event)
    {
        $this->FormProtection->setConfig('validate', false);
        return parent::beforeFilter($event);
    }

    /**
     * [API] 受信メール追加
     *
     * @param MailMessagesService $service
     * @param MailContentsServiceInterface $mailContentsService
     * @param int $mailContentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(
        MailFrontServiceInterface $mailFrontService,
        MailMessagesServiceInterface $service,
        MailContentsServiceInterface $mailContentsService,
        int $mailContentId
    )
    {
        $this->request->allowMethod(['post']);
        $mailMessage = null;
        try {
            $service->setup($mailContentId, $this->getRequest()->getData());
            $mailContent = $mailContentsService->get($mailContentId);
            $mailMessage = $service->create($mailContent, $this->request->getData());
            //メールを送信
            // EVENT Mail.beforeSendEmail
            $event = $this->dispatchLayerEvent('beforeSendEmail', [
                'data' => $mailMessage
            ]);
            $sendEmailOptions = [];
            if ($event !== false) {
                $this->request = $this->request->withParsedBody($event->getResult() === true ? $event->getData('data') : $event->getResult());
                if (!empty($event->getData('sendEmailOptions'))) $sendEmailOptions = $event->getData('sendEmailOptions');
            }

            $mailFrontService->sendMail($mailContent, $mailMessage, $sendEmailOptions);

            $message = __d('baser_core',
                '{0} への受信データ NO「{1}」を追加しました。',
                $mailContent->content->title,
                $mailMessage->id
            );
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $mailMessage = $e->getEntity();
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $mailMessage = null;
        }
        $this->set([
            'message' => $message,
            'mailMessage' => $mailMessage,
            'errors' => !!$mailMessage ? $mailMessage->getErrors() : null
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailMessage', 'message', 'errors']);
    }

    /**
     * [API] バリデーション
     *
     * @param MailMessagesServiceInterface $mailMessagesService
     * @param int $mailContentId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validate(MailMessagesServiceInterface $mailMessagesService, int $mailContentId)
    {
        $this->request->allowMethod(['post']);

        $mailMessagesService->setup($mailContentId, $this->getRequest()->getData());
        $messageArray = $mailMessagesService->autoConvert($mailContentId, $this->getRequest()->getData());
        $mailMessage = $mailMessagesService->MailMessages->newEntity($messageArray);

        $this->set([
            'success' => !$mailMessage->getErrors(),
            'errors' => $mailMessage->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', ['success', 'errors']);
    }
}
