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
use BcCustomContent\Service\CustomLinksServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomLinksController
 */
class CustomLinksController extends BcAdminApiController
{

    /**
     * 一覧取得API
     *
     * @param CustomLinksServiceInterface $service
     *
     * @checked
     * @noTodo
     */
    public function index(CustomLinksServiceInterface $service)
    {
        $this->request->allowMethod('get');

        $queryParams = $this->getRequest()->getQueryParams();

        if (empty($queryParams['custom_table_id'])) {
            throw new BadRequestException(__d('baser_core', 'パラメーターに custom_table_id を指定してください。'));
        }

        $queryParams = array_merge([
            'contain' => null,
        ], $queryParams);

        $this->set([
            'customLinks' => $this->paginate(
                $service->getIndex($queryParams['custom_table_id'], $queryParams)
            )
        ]);
        $this->viewBuilder()->setOption('serialize', ['customLinks']);
    }

    /**
     * 単一データAPI
     *
     * @param CustomLinksServiceInterface $service
     * @param int $id
     * @checked
     * @noTodo
     */
    public function view(CustomLinksServiceInterface $service, int $id)
    {
        $this->request->allowMethod('get');

        $customLink = $message = null;
        try {
            $customLink = $service->get($id, $this->getRequest()->getQueryParams());
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません');
        }

        $this->set([
            'customLink' => $customLink,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customLink']);
    }

    /**
     * 新規追加API
     *
     * @param CustomLinksServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(CustomLinksServiceInterface $service)
    {
        $this->request->allowMethod(['post']);
        $customLink = $errors = null;
        try {
            $customLink = $service->create($this->request->getData());
            $message = sprintf(__d('baser_core', 'カスタムリンク「%s」を追加しました。'), $customLink->title);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'customLink' => $customLink,
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['customLink', 'message', 'errors']);
    }

    /**
     * カスタムリンク編集API
     *
     * @param CustomLinksServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     */
    public function edit(CustomLinksServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $entity = null;
        try {
            $entity = $service->update($service->get($id, ['contain' => null]), $this->request->getData());
            $message = __d('baser_core', 'カスタムリンク「{0}」を更新しました。', $entity->title);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $entity = $e->getEntity();
            $message = __d('baser_core', '入力エラーです。内容を修正してください。');
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $entity = $e->getEntity();
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'customLink' => $entity,
            'errors' => $entity?->getErrors(),
        ]);

        $this->viewBuilder()->setOption('serialize', ['customLink', 'message', 'errors']);
    }

    /**
     * 削除API
     *
     * @param CustomLinksServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(CustomLinksServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $customLink = null;
        try {
            $customLink = $service->get($id);
            if ($service->delete($id)) {
                $message = __d('baser_core', 'カスタムリンク「{0}」を削除しました。', $customLink->title);
                $this->BcMessage->setSuccess($message, true, false);
            } else {
                $this->setResponse($this->response->withStatus(500));
                $message = __d('baser_core', 'データベース処理中にエラーが発生しました。');
            }
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'message' => $message,
            'customLink' => $customLink
        ]);
        $this->viewBuilder()->setOption('serialize', ['customLink', 'message']);
    }

    /**
     * リストAPI
     *
     * @param CustomLinksServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(CustomLinksServiceInterface $service, int $id)
    {
        $this->request->allowMethod('get');
        $this->set([
            'customLinks' => $service->getList($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['customLinks']);
    }

    /**
     * カスタムリンクの親のリストを取得する
     *
     * @param CustomLinksServiceInterface $service
     * @param int $tableId
     */
    public function get_parent_list(CustomLinksServiceInterface $service, int $tableId)
    {
        $parentList = $service->getControlSource('parent_id', ['tableId' => $tableId]);
        $this->set(['parentList' => $parentList]);
        $this->viewBuilder()->setOption('serialize', ['parentList']);
    }

}
