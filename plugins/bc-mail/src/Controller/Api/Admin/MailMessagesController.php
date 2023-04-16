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

namespace BcMail\Controller\Api\Admin;

use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BaserCore\View\Helper\BcCsvHelper;
use BcMail\Service\Admin\MailMessagesAdminServiceInterface;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailMessagesService;
use BcMail\Service\MailMessagesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\View\View;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールフィールドコントローラー
 */
class MailMessagesController extends BcAdminApiController
{

    /**
     * [API] 受信メール一覧
     *
     * @param MailMessagesService $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(MailMessagesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();

        if (empty($queryParams['mail_content_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに mail_content_id を指定してください。'));
        }

        $mailMessages = $message = null;
        try {
            $service->setup($queryParams['mail_content_id']);
            $mailMessages = $this->paginate($service->getIndex($this->request->getQueryParams()));
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'mailMessages' => $mailMessages,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailMessages', 'message' => $message]);
    }

    /**
     * [API] 受信メール詳細
     *
     * @param MailMessagesService $service
     * @param int $messageId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(MailMessagesServiceInterface $service, int $messageId)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();

        if (empty($queryParams['mail_content_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに mail_content_id を指定してください。'));
        }

        $mailMessage = $message = null;
        try {
            $service->setup($queryParams['mail_content_id']);
            $mailMessage = $service->get($messageId);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'mailMessage' => $mailMessage,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailMessage', 'message' => $message]);
    }

    /**
     * [API] 受信メール追加
     *
     * @param MailMessagesService $service
     * @param MailContentsServiceInterface $mailContentsService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(
        MailMessagesServiceInterface $service,
        MailContentsServiceInterface $mailContentsService
    )
    {
        $this->request->allowMethod(['post']);

        $queryParams = $this->getRequest()->getQueryParams();

        if (empty($queryParams['mail_content_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに mail_content_id を指定してください。'));
        }

        $mailMessage = null;
        try {
            $service->setup($queryParams['mail_content_id']);
            $mailContent = $mailContentsService->get($queryParams['mail_content_id']);
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

    /**
     * [API] 受信メール編集
     *
     * @param MailMessagesServiceInterface $service
     * @param MailContentsServiceInterface $mailContentsService
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(
        MailMessagesServiceInterface $service,
        MailContentsServiceInterface $mailContentsService,
        int $id
    )
    {
        $this->request->allowMethod(['post', 'patch']);

        $queryParams = $this->getRequest()->getQueryParams();

        if (empty($queryParams['mail_content_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに mail_content_id を指定してください。'));
        }

        $mailMessage = $errors = null;
        try {
            $service->setup($queryParams['mail_content_id']);
            $entity = $service->get($id);
            $mailMessage = $service->update($entity, $this->request->getData());
            $mailContent = $mailContentsService->get($queryParams['mail_content_id']);
            $message = __d('baser_core',
                '{0} への受信データ NO「{1}」を更新しました。',
                $mailContent->content->title,
                $mailMessage->id
            );
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $mailMessage = $e->getEntity();
            $errors = $mailMessage->getErrors();
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'mailMessage' => $mailMessage,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailMessage', 'message', 'errors']);
    }

    /**
     * [API] 受信メール削除
     *
     * @param MailMessagesService $service
     * @param MailContentsServiceInterface $mailContentsService,
     * @param int $messageId
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(
        MailMessagesServiceInterface $service,
        MailContentsServiceInterface $mailContentsService,
        int $messageId
    )
    {
        $this->request->allowMethod(['post', 'put', 'delete']);

        $queryParams = $this->getRequest()->getQueryParams();

        if (empty($queryParams['mail_content_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに mail_content_id を指定してください。'));
        }

        $mailMessage = $errors = null;
        try {
            $service->setup($queryParams['mail_content_id']);
            $mailContent = $mailContentsService->get($queryParams['mail_content_id']);
            $mailMessage = $service->delete($messageId);
            $message = __d('baser_core',
                '{0} への受信データ NO「{1}」を削除しました。',
                $mailContent->content->title,
                $messageId
            );
            $this->BcMessage->setSuccess($message, true, false);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'mailMessage' => $mailMessage,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailMessage', 'message', 'errors']);
    }

    /**
     * メールメッセージのバッチ処理
     *
     * 指定したメールフィールドに対して削除、公開、非公開の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名'batch'が'delete'以外の値であれば500エラーを発生させる
     *
     * @param MailMessagesService $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch(MailMessagesServiceInterface $service, $mailContentId)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => __d('baser_core', '削除'),
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        $errors = null;
        try {
            $service->setup($mailContentId);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser_core', 'メールメッセージ No %s を %s しました。'), implode(', ', $targets), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser_core', '一括処理が完了しました。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set(['message' => $message, 'errors' => $errors]);
        $this->viewBuilder()->setOption('serialize', ['message', 'errors']);
    }

    /**
     * [API] CSVダウンロード
     *
     * @checked
     * @noTodo
     */
    public function download(MailMessagesAdminServiceInterface $service, int $mailContentId)
    {
        $this->request->allowMethod(['get']);
        $result = $service->getViewVarsForDownloadCsv($mailContentId, $this->getRequest());
        $bcCsvHelper = new BcCsvHelper(new View());
        $bcCsvHelper->encoding = $result['encoding'];
        $bcCsvHelper->addModelDatas('MailMessage', $result['messages']);
        $bcCsvHelper->download($result['contentName']);
    }

}
