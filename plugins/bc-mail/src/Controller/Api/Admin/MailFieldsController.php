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
use BcMail\Service\MailFieldsService;
use BcMail\Service\MailFieldsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールフィールドコントローラー
 */
class MailFieldsController extends BcAdminApiController
{

    /**
     * メールフィールドのバッチ処理
     *
     * 指定したメールフィールドに対して削除、公開、非公開の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名'batch'が'delete','publish','unpublish'以外の値であれば500エラーを発生させる
     *
     * @param MailFieldsService $service
     * @checked
     * @noTodo
     */
    public function batch(MailFieldsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => __d('baser_core', '削除'),
            'publish' => __d('baser_core', '有効化'),
            'unpublish' => __d('baser_core', '無効化')
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            $names = $service->getTitlesById($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser_core', 'メールフィールド「%s」を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser_core', '一括処理が完了しました。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }


    /**
     * 並び替えを更新する [AJAX]
     *
     * @param MailFieldsService $service
     * @param int $mailContentId
     * @return bool|void
     * @checked
     * @noTodo
     */
    public function update_sort(MailFieldsServiceInterface $service, int $mailContentId)
    {
        $this->request->allowMethod(['post']);
        $conditions = [
            'mail_content_id' => $mailContentId,
        ];
        $entity = null;
        try {
            $entity = $service->get($this->request->getData('id'));
            if (!$service->changeSort($this->request->getData('id'), $this->request->getData('offset'), $conditions)) {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', '一度リロードしてから再実行してみてください。');
            } else {
                $message = sprintf(__d('baser_core', 'メールフィールド「%s」の並び替えを更新しました。'), $entity->name);
                $this->BcMessage->setSuccess($message, true, false);
            }
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'mailField' => $entity
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailField', 'message']);
    }

    /**
     * [API] メールフィールド API 一覧取得
     *
     * @param MailFieldsServiceInterface $service
     *
     * @checked
     * @noTodo
     */
    public function index(MailFieldsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();

        if (empty($queryParams['mail_content_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに mail_content_id を指定してください。'));
        }

        $mailFields = $message = null;
        try {
            $queryParams = array_merge([
                'contain' => null,
            ], $queryParams);
            $mailFields = $this->paginate($service->getIndex($queryParams['mail_content_id'], $queryParams));
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'mailFields' => $mailFields,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailFields', 'message']);
    }

    /**
     * [API] メールフィールド API 単一データ取得
     *
     * @param MailFieldsServiceInterface $service
     * @param int $id
     * @return void
     *
     * @checked
     * @noTodo
     */
    public function view(MailFieldsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();

        $queryParams = array_merge([
            'contain' => null
        ], $queryParams);

        $this->set([
            'mailField' => $service->get($id, $queryParams)
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailField']);
    }

    /**
     * [API] メールフィールド API リスト取得
     *
     * @param MailFieldsServiceInterface $service
     * @param int $mailContentId
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(MailFieldsServiceInterface $service, int $mailContentId)
    {
        $mailField = $message = null;
        try {
            $mailField = $service->getList($mailContentId);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'mailField' => $mailField,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailField', 'message']);
    }

    /**
     * [API] メールフィールド API 新規追加
     *
     * @param MailFieldsServiceInterface $service
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(MailFieldsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        try {
            $mailField = $service->create($this->request->getData());
            $message = __d('baser_core', '新規メールフィールド「{0}」を追加しました。', $mailField->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'mailField' => $mailField ?? null,
            'message' => $message,
            'errors' => $errors ?? null,
        ]);
        $this->viewBuilder()->setOption('serialize', [
            'mailField',
            'message',
            'errors'
        ]);
    }


    /**
     * [API] メールフィールド API 編集
     *
     * @param MailFieldsServiceInterface $service
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(MailFieldsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $mailField = $errors = null;
        try {
            $mailField = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'メールフィールド「{0}」を更新しました。', $mailField->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'mailField' => $mailField,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailField', 'message', 'errors']);
    }

    /**
     * [API] メールフィールド API 削除
     *
     * @param MailFieldsServiceInterface $service
     * @param int $id
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(MailFieldsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'delete']);
        $mailField = null;
        try {
            $mailField = $service->get($id);
            if ($service->delete($id)) {
                $message = __d('baser_core', 'メールフィールド「{0}」を削除しました。', $mailField->name);
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'mailField' => $mailField
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailField', 'message']);
    }

    /**
     * [API] メールフィールド API コピー
     *
     * @param MailFieldsServiceInterface $service
     * @param int $mailContentId
     * @param int $id
     * @return void
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(MailFieldsServiceInterface $service, int $mailContentId, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $mailField = null;
        try {
            if ($service->copy($mailContentId, $id)) {
                $mailField = $service->get($id);
                $message = __d('baser_core', 'メールフィールド「{0}」をコピーしました。', $mailField->name);
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', '処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'mailField' => $mailField
        ]);
        $this->viewBuilder()->setOption('serialize', ['mailField', 'message']);
    }
}
