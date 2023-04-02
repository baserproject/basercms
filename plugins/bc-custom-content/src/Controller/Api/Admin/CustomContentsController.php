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
use BcCustomContent\Service\CustomContentsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * CustomContentsController
 */
class CustomContentsController extends BcAdminApiController
{

    /**
     * 一覧取得API
     *
     * @param CustomContentsServiceInterface $service
     *
     * @checked
     * @noTodo
     */
    public function index(CustomContentsServiceInterface $service)
    {
        $this->request->allowMethod(['get']);

        $queryParams = $this->getRequest()->getQueryParams();

        $queryParams = array_merge([
            'contain' => null,
        ], $queryParams);

        $this->set([
            'customContents' => $this->paginate($service->getIndex($queryParams))
        ]);
        $this->viewBuilder()->setOption('serialize', ['customContents']);
    }

    /**
     * 単一データAPI
     *
     * @param CustomContentsServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     */
    public function view(CustomContentsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get']);

        $customContent = $message = null;
        try {
            $customContent = $service->get($id, $this->getRequest()->getQueryParams());
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'customContent' => $customContent,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'customContent']);
    }

    /**
     * カスタムコンテンツの新規追加
     *
     * @param CustomContentsServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(CustomContentsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $entity = $errors = null;
        try {
            $entity = $service->create($this->request->getData());
            $message = __d('baser_core', 'カスタムコンテンツ「{0}」を追加しました。', $entity->content->title);
        } catch (PersistenceFailedException $e) {
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
            $this->setResponse($this->response->withStatus(400));
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'customContent' => $entity,
            'content' => $entity?->content,
            'message' => $message,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', [
            'customContent',
            'content',
            'message',
            'errors'
        ]);
    }

    /**
     * 編集API
     *
     * @param CustomContentsServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(CustomContentsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $customContent = $errors = null;
        try {
            $customContent = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'カスタムコンテンツ「{0}」を更新しました。', $customContent->content->title);
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
            'customContent' => $customContent,
            'content' => $customContent?->content,
            'message' => $message,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', [
            'customContent',
            'content',
            'message',
            'errors'
        ]);
    }

    /**
     * 削除API
     *
     * @param CustomContentsServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(CustomContentsServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $customContent = null;
        try {
            $customContent = $service->get($id);
            $service->delete($id);
            $message = __d('baser_core', 'カスタムコンテンツ「{0}」を削除しました。', $customContent->content->title);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'customContent' => $customContent,
            'content' => $customContent?->content,
        ]);
        $this->viewBuilder()->setOption('serialize', ['customContent', 'content', 'message']);
    }

    /**
     * リストAPI
     *
     * @param CustomContentsServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(CustomContentsServiceInterface $service)
    {
        $this->set([
            'customContents' => $service->getList()
        ]);
        $this->viewBuilder()->setOption('serialize', ['customContents']);
    }
}
