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
use BcCustomContent\Service\CustomTablesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomTablesController
 */
class CustomTablesController extends BcAdminApiController
{
    /**
     * 一覧取得API
     *
     * @param CustomTablesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(CustomTablesServiceInterface $service)
    {
        $this->request->allowMethod('get');
        $this->set([
            'customTables' => $this->paginate(
                $service->getIndex($this->request->getQueryParams())
            )
        ]);
        $this->viewBuilder()->setOption('serialize', ['customTables']);
    }

    /**
     * 単一データAPI
     *
     * @param CustomTablesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(CustomTablesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get']);
        $customTable = $message = null;
        try {
            $customTable = $service->get($id, $this->request->getQueryParams());
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'customTable' => $customTable,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customTable']);
    }

    /**
     * 新規追加API
     *
     * @param CustomTablesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(CustomTablesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'delete']);
        $customTable = $errors = null;
        try {
            $customTable = $service->create($this->request->getData());
            $message = __d('baser_core', 'テーブル「{0}」を追加しました。', $customTable->title);
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
            'customTable' => $customTable,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customTable', 'errors']);
    }

    /**
     * 編集API
     *
     * @param CustomTablesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(CustomTablesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $customTable = $errors = null;
        try {
            $customTable = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'テーブル「{0}」を更新しました。', $customTable->title);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'customTable' => $customTable,
            'message' => $message,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['customTable', 'message', 'errors']);
    }

    /**
     * 削除API
     *
     * @param CustomTablesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(CustomTablesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);

        $customTable = null;
        try {
            $customTable = $service->get($id);
            if ($service->delete($id)) {
                $message = __d('baser_core', 'テーブル「{0}」を削除しました。', $customTable->title);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'customTable' => $customTable
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customTable']);
    }

    /**
     * リストAPI
     *
     * @param CustomTablesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(CustomTablesServiceInterface $service)
    {
        $this->request->allowMethod('get');
        $this->set([
            'customTables' => $service->getList($this->request->getQueryParams())
        ]);
        $this->viewBuilder()->setOption('serialize', ['customTables']);
    }
}
