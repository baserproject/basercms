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

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Controller\Api\BcApiController;
use BcMail\Service\MailContentsService;
use BcMail\Service\MailContentsServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * メールコンテンツコントローラー
 */
class MailContentsController extends BcApiController
{

    /**
     * メールフォーム登録
     *
     * @checked
     * @noTodo
     */
    public function add(MailContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $entity = $service->create($this->request->getData());
            $message = __d('baser', 'メールフォーム「{0}」を追加しました。', $entity->content->title);
        } catch (\Cake\ORM\Exception\PersistenceFailedException $e) {
            $entity = $e->getEntity();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        }
        $this->set([
            'mailContent' => $entity,
            'content' => $entity->content,
            'message' => $message,
            'errors' => $entity->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'blogContent', 'content', 'errors']);
    }

    /**
     * コピー
     *
     * @param MailContentsService $service
     * @checked
     * @noTodo
     */
    public function copy(MailContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $errors = null;
        try {
            $entity = $service->copy($this->request->getData());
            if (!$entity) {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'コピーに失敗しました。データが不整合となっている可能性があります。');
            } else {
                $message = __d('baser', 'メールフォームのコピー「{0}」を追加しました。', $entity->content->title);
            }

        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(500));
            $errors = $e->getEntity();
            $message = __d('baser', 'コピーに失敗しました。データが不整合となっている可能性があります。');
        }
        $this->set([
            'message' => $message,
            'mailContent' => $entity,
            'content' => $entity->content,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailContent', 'content', 'message', 'errors']);
    }

}
