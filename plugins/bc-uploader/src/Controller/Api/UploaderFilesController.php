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

namespace BcUploader\Controller\Api;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Controller\Api\BcApiController;
use BcUploader\Service\UploaderFilesServiceInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Throwable;

/**
 * アップロードファイルコントローラー
 */
class UploaderFilesController extends BcApiController
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
     * @unitTest
     */
    public function upload(UploaderFilesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        $entity = null;
        try {
            $entity = $service->create($this->request->getData());
            $message = __d('baser', 'アップロードファイル「{0}」を追加しました。', $entity->name);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $entity = $e->getEntity();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'uploaderFile' => $entity,
            'message' => $message,
            'errors' => $entity ?? $entity->getErrors()
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

        $entity = $service->get($id);
        try {
            $entity = $service->update($entity, $this->getRequest()->getData());
            $message = __d('baser', 'アップロードファイル「{0}」を更新しました。', $entity->name);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $entity = $e->getEntity();
            $message = __d('baser', "入力エラーです。内容を修正してください。");
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'uploaderFile' => $entity,
            'message' => $message,
            'errors' => $entity ?? $entity->getErrors()
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
        $this->request->allowMethod(['post', 'put']);
        $entity = $errors = null;
        try {
            $entity = $service->get($id);
            $service->delete($id);
            $message = __d('baser', 'アップロードファイル「{0}」を削除しました。', $entity->name);
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'uploadFile' => $entity,
            'message' => $message,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['uploadFile', 'message', 'errors']);
    }

}
