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

namespace BcCustomContent\Controller\Api\Admin;

use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomFieldsController
 */
class CustomFieldsController extends BcAdminApiController
{
    /**
     * 一覧取得API
     *
     * @param CustomFieldsServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(CustomFieldsServiceInterface $service)
    {
        $this->request->allowMethod('get');
        $this->set([
            'customFields' => $this->paginate(
                $service->getIndex($this->request->getQueryParams())
            )
        ]);
        $this->viewBuilder()->setOption('serialize', ['customFields']);
    }

    /**
     * 単一データAPI
     *
     * @param CustomFieldsServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(CustomFieldsServiceInterface $service, int $id)
    {
        $this->request->allowMethod('get');
        $customField = $message = null;
        try {
            $customField = $service->get($id);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません');
        }

        $this->set([
            'customField' => $customField,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customField']);
    }

    /**
     * 新規追加API
     *
     * @param CustomFieldsServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(CustomFieldsServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        $customField = $errors = null;
        try {
            $customField = $service->create($this->request->getData());
            $message = __d('baser_core', 'フィールド「{0}」を追加しました。', $customField->title);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'customField' => $customField,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customField', 'errors']);
    }

    /**
     * 編集API
     *
     * @param CustomFieldsServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(CustomFieldsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put']);
        $customField = $errors = null;
        try {
            $customField = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'フィールド「{0}」を更新しました。', $customField->title);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'customField' => $customField,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['customField', 'message', 'errors']);
    }

    /**
     * 削除API
     *
     * @param CustomFieldsServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(CustomFieldsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $customField = null;
        try {
            $customField = $service->get($id);
            $service->delete($id);
            $message = __d('baser_core', 'フィールド「{0}」を削除しました。', $customField->title);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'customField' => $customField
        ]);
        $this->viewBuilder()->setOption('serialize', ['customField', 'message']);
    }

    /**
     * リストAPI
     *
     * @param CustomFieldsServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(CustomFieldsServiceInterface $service)
    {
        $this->request->allowMethod('get');
        $this->set([
            'customFields' => $service->getList()
        ]);
        $this->viewBuilder()->setOption('serialize', ['customFields']);
    }
}
