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
use BcCustomContent\Service\CustomEntriesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\Exception\PersistenceFailedException;
use Throwable;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomEntriesController
 */
class CustomEntriesController extends BcAdminApiController
{

    /**
     * カスタムエントリーの単一データを取得する
     *
     * @param CustomEntriesServiceInterface $service
     * @param int $tableId
     * @param $id
     * @checked
     * @noTodo
     */
    public function view(CustomEntriesServiceInterface $service, $id)
    {
        $this->request->allowMethod('get');
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['use_api'])) {
            throw new ForbiddenException();
        }
        if (empty($queryParams['custom_table_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに custom_table_id を指定してください。'));
        }

        $queryParams = array_merge([
            'use_api' => true
        ], $queryParams);

        $entity = $message = null;
        try {
            $service->setup($queryParams['custom_table_id']);
            $entity = $service->get($id, $queryParams);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', "データが見つかりません。");
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'entry' => $entity,
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['entry', 'message']);
    }

    /**
     * カスタムエントリーの一覧データを取得する
     *
     * @param CustomEntriesServiceInterface $service
     * @param int $tableId
     * @checked
     * @noTodo
     */
    public function index(CustomEntriesServiceInterface $service)
    {
        $this->request->allowMethod('get');
        $queryParams = $this->getRequest()->getQueryParams();
        if (isset($queryParams['use_api'])) {
            throw new ForbiddenException();
        }
        if (empty($queryParams['custom_table_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに custom_table_id を指定してください。'));
        }

        $queryParams = array_merge([
            'use_api' => true
        ], $queryParams);

        $entities = $message = null;
        try {
            $service->setup($queryParams['custom_table_id']);
            $entities = $service->getIndex($queryParams);
        } catch (Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'entries' => $entities,
            'message' => $message,
        ]);
        $this->viewBuilder()->setOption('serialize', ['entries', 'message']);
    }

    /**
     * カスタムエントリー　追加
     * @param CustomEntriesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(CustomEntriesServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        $queryParams = $this->getRequest()->getQueryParams();
        if (empty($queryParams['custom_table_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに custom_table_id を指定してください。'));
        }

        $entry = $errors = null;
        try {
            $service->setup($queryParams['custom_table_id'], $this->getRequest()->getData());
            $entry = $service->create($this->request->getData());
            $message = __d('baser_core', 'フィールド「{0}」を追加しました。', $entry->title);
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
            'entry' => $entry,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'entry', 'errors']);
    }

    /**
     * カスタムエントリー　編集
     * @param CustomEntriesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(CustomEntriesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['patch', 'post', 'put']);
        $queryParams = $this->getRequest()->getQueryParams();
        if (empty($queryParams['custom_table_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに custom_table_id を指定してください。'));
        }
        $entry = $errors = null;
        try {
            $service->setup($queryParams['custom_table_id'], $this->getRequest()->getData());
            $entry = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'フィールド「{0}」を更新しました。', $entry->title);
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
            'entry' => $entry,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['entry', 'message', 'errors']);
    }

    /**
     * カスタムエントリー　削除
     * @param CustomEntriesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(CustomEntriesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $queryParams = $this->getRequest()->getQueryParams();
        if (empty($queryParams['custom_table_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに custom_table_id を指定してください。'));
        }
        $entry = null;
        try {
            $service->setup($queryParams['custom_table_id']);
            $entry = $service->get($id);
            $service->delete($id);
            $message = __d('baser_core', 'フィールド「{0}」を削除しました。', $entry->title);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'entry' => $entry
        ]);
        $this->viewBuilder()->setOption('serialize', ['entry', 'message']);
    }

    /**
     * カスタムエントリー　リスト
     * @param CustomEntriesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(CustomEntriesServiceInterface $service)
    {
        $this->request->allowMethod('get');

        $queryParams = $this->getRequest()->getQueryParams();
        if (empty($queryParams['custom_table_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに custom_table_id を指定してください。'));
        }

        $service->setup($queryParams['custom_table_id']);
        $this->set([
            'entries' => $service->getList()
        ]);
        $this->viewBuilder()->setOption('serialize', ['entries']);
    }
}
