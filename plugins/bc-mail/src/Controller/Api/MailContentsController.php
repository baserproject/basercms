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
use BcMail\Service\MailContentsServiceInterface;

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
     * @return bool
     */
    public function copy()
    {
        $this->autoRender = false;
        if (!$this->request->getData()) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        $user = $this->BcAuth->user();
        $data = $this->MailContent->copy(
            $this->request->getData('entityId'),
            $this->request->getData('parentId'),
            $this->request->getData('title'),
            $user['id'],
            $this->request->getData('siteId')
        );
        if (!$data) {
            $this->ajaxError(500, $this->MailContent->validationErrors);
            return false;
        }
        $message = sprintf(
            __d('baser', 'メールフォームのコピー「%s」を追加しました。'),
            $this->request->getData('title')
        );
        $this->BcMessage->setSuccess($message, true, false);
        return json_encode($data['Content']);
    }

}
