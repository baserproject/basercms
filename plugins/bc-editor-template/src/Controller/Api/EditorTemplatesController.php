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

namespace BcEditorTemplate\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcEditorTemplate\Service\EditorTemplatesServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class EditorTemplatesController
 *
 * エディタテンプレートコントローラー
 *
 * エディタテンプレートのAPI
 */
class EditorTemplatesController extends BcApiController
{

    /**
     * 一覧取得API
     *
     * @param EditorTemplatesServiceInterface $service
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(EditorTemplatesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        $this->set([
            'editorTemplates' => $this->paginate($service->getIndex())
        ]);
        $this->viewBuilder()->setOption('serialize', ['editorTemplates']);
    }

    /**
     * 単一データAPI
     *
     * @param EditorTemplatesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(EditorTemplatesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['get']);
        $editorTemplate = $message = null;
        try {
            $editorTemplate = $service->get($id);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }
        $this->set([
            'editorTemplate' => $editorTemplate,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['editorTemplate', 'message']);
    }

    /**
     * 新規追加API
     *
     * @param EditorTemplatesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(EditorTemplatesServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'post']);
        $editorTemplate = $errors = null;
        try {
            $editorTemplate = $service->create($this->request->getData());
            $message = __d('baser_core', 'エディタテンプレート「{0}」を追加しました。', $editorTemplate->name);
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
            'editorTemplate' => $editorTemplate,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'editorTemplate', 'errors']);
    }

    /**
     * 編集API
     *
     * @param EditorTemplatesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(EditorTemplatesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);
        $editorTemplate = $errors = null;
        try {
            $editorTemplate = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'エディタテンプレート「{0}」を更新しました。', $editorTemplate->name);
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
            'editorTemplate' => $editorTemplate,
            'errors' => $errors,
        ]);
        $this->viewBuilder()->setOption('serialize', ['editorTemplate', 'message', 'errors']);
    }

    /**
     * 削除API
     *
     * @param EditorTemplatesServiceInterface $service
     * @param int $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(EditorTemplatesServiceInterface $service, int $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $editorTemplate = null;
        try {
            $editorTemplate = $service->get($id);
            $service->delete($id);
            $message = __d('baser_core', 'エディタテンプレート「{0}」を削除しました。', $editorTemplate->name);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
            $this->setResponse($this->response->withStatus(500));
        }

        $this->set([
            'message' => $message,
            'editorTemplate' => $editorTemplate,
        ]);
        $this->viewBuilder()->setOption('serialize', ['editorTemplate', 'message']);
    }

    /**
     * リストAPI
     *
     * @param EditorTemplatesServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(EditorTemplatesServiceInterface $service)
    {
        $this->request->allowMethod(['get']);
        $this->set([
            'editorTemplates' => $service->getList()
        ]);
        $this->viewBuilder()->setOption('serialize', ['editorTemplates']);
    }

}
