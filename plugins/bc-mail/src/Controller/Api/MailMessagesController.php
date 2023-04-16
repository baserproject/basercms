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

    public function beforeFilter(EventInterface $event)
    {
        $this->Security->setConfig('validatePost', false);
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
        MailMessagesServiceInterface $service,
        MailContentsServiceInterface $mailContentsService,
        int $mailContentId
    )
    {
        $this->request->allowMethod(['post']);
        $mailMessage = null;
        try {
            $service->setup($mailContentId);
            $mailContent = $mailContentsService->get($mailContentId);
            $mailMessage = $service->create($mailContent, $this->request->getData());
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

}
