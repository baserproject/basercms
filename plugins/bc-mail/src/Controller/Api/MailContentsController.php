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
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * メールコンテンツコントローラー
 */
class MailContentsController extends BcApiController
{

    /**
     * メールコンテンツAPI 一覧取得
     * @return void
     */
    public function index()
    {
        //todo メールコンテンツAPI 一覧取得
    }

    /**
     * メールコンテンツAPI 単一データ取得
     * @param MailContentsServiceInterface $service
     * @param int $id
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(MailContentsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get']);
        $mailContent = $message = null;
        try {
            $mailContent = $service->get($id);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'mailContent' => $mailContent,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailContent', 'message']);
    }

    /**
     * メールコンテンツAPI リスト取得
     * @param MailContentsServiceInterface $service
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(MailContentsServiceInterface $service)
    {
        $this->set([
            'mailContents' => $service->getList()
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailContents']);
    }

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
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'mailContent' => $entity,
            'content' => $entity->content,
            'message' => $message,
            'errors' => $entity->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'mailContent', 'content', 'errors']);
    }

    /**
     * メールコンテンツAPI 編集
     * @param MailContentsServiceInterface $service
     * @param int $id
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(MailContentsServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        try {
            $entity = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser', 'メールフォーム「{0}」を更新しました。', $entity->content->title);
        } catch (PersistenceFailedException $e) {
            $entity = $e->getEntity();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '処理中にエラーが発生しました。');
        }

        $this->set([
            'mailContent' => $entity,
            'content' => $entity->content,
            'message' => $message,
            'errors' => $entity->getErrors()
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'mailContent', 'content', 'errors']);
    }

    /**
     * メールコンテンツAPI 削除
     * @param MailContentsServiceInterface $service
     * @param int$id
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(MailContentsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $mailContent = null;
        try {
            $mailContent = $service->get($id);
            if ($service->delete($id)) {
                $message = __d('baser', 'メールフォーム「{0}」を削除しました。', $mailContent->content->title);
            } else {
                $message = __d('baser', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser', 'データが見つかりません');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'mailContent' => $mailContent,
            'content' => $mailContent ?? $mailContent->content,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailContent', 'content', 'message']);
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
        $entity = null;
        try {
            $entity = $service->copy($this->request->getData());
            if (!$entity) {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'コピーに失敗しました。データが不整合となっている可能性があります。');
            } else {
                $message = __d('baser', 'メールフォームのコピー「{0}」を追加しました。', $entity->content->title);
            }

        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'mailContent' => $entity,
            'content' => $entity?->content
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailContent', 'content', 'message']);
    }

}
