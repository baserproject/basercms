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

namespace BcUploader\Controller\Api\Admin;

use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BcUploader\Service\UploaderFilesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Throwable;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * アップロードファイルコントローラー
 */
class UploaderFilesController extends BcAdminApiController
{

    /**
     * 一覧取得API
     *
     * @param UploaderFilesServiceInterface $service
     * @return void
     *
     * @checked
     * @notodo
     * @unitTest
     */
    public function index(UploaderFilesServiceInterface $service)
    {
        $this->set([
            'uploaderFiles' => $this->paginate($service->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['uploaderFiles']);
    }

    /**
     * [ADMIN] Ajaxファイルアップロード
     *
     * @param UploaderFilesServiceInterface $service
     * @checked
     * @noTodo
     */
    public function upload(UploaderFilesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        $entity = $errors = null;
        try {
            $entity = $service->create($this->request->getData());
            $message = __d('baser_core', 'アップロードファイル「{0}」を追加しました。', $entity->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'uploaderFile' => $entity,
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'uploaderFile', 'errors']);
    }

    /**
     * 編集処理
     *
     * @param UploaderFilesServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UploaderFilesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        $entity = $errors = null;
        try {
            $entity = $service->update($service->get($id), $this->getRequest()->getData());
            $message = __d('baser_core', 'アップロードファイル「{0}」を更新しました。', $entity->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'uploaderFile' => $entity,
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'uploaderFile', 'errors']);
    }

    /**
     * アップロードファイルを削除する
     *
     * @param UploaderFilesServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(UploaderFilesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'delete']);
        $entity = null;
        try {
            $entity = $service->get($id);
            $service->delete($id);
            $message = __d('baser_core', 'アップロードファイル「{0}」を削除しました。', $entity->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'uploaderFile' => $entity,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['uploaderFile', 'message']);
    }

}
