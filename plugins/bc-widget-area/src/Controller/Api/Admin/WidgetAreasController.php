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

namespace BcWidgetArea\Controller\Api\Admin;

use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BcWidgetArea\Service\WidgetAreasService;
use BcWidgetArea\Service\WidgetAreasServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class WidgetAreasController
 *
 * ウィジェットエリアコントローラー
 */
class WidgetAreasController extends BcAdminApiController
{

    /**
     * 一覧取得
     *
     * @param WidgetAreasServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(WidgetAreasServiceInterface $service)
    {
        $this->set([
            'widgetAreas' => $this->paginate($service->getIndex($this->request->getQueryParams()))
        ]);
        $this->viewBuilder()->setOption('serialize', ['widgetAreas']);
    }

    /**
     * リストデータ取得
     *
     * @param WidgetAreasServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function list(WidgetAreasServiceInterface $service)
    {
        $this->set([
            'widgetAreas' => $service->getList()
        ]);
        $this->viewBuilder()->setOption('serialize', ['widgetAreas']);
    }

    /**
     * 新規追加
     *
     * @param WidgetAreasServiceInterface $service
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(WidgetAreasServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $widgetArea = $errors = null;
        try {
            $widgetArea = $service->create($this->getRequest()->getData());
            $message = __d('baser_core', '新しいウィジェットエリアを保存しました。');
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'widgetArea' => $widgetArea,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'widgetArea', 'errors']);
    }

    /**
     * ウィジェットエリア管理 API 編集
     *
     * @param WidgetAreasService $service
     * @param $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(WidgetAreasServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        $widgetArea = $errors = null;
        try {
            $widgetArea = $service->update($service->get($id), $this->request->getData());
            $message = __d('baser_core', 'ウィジェットエリア「{0}」を更新しました。', $widgetArea->name);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'widgetArea' => $widgetArea,
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['widgetArea', 'message', 'errors']);
    }

    /**
     * 削除
     *
     * @param WidgetAreasServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(WidgetAreasServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'put', 'delete']);

        $widgetArea = null;
        try {
            $widgetArea = $service->get($id);
            $service->delete($id);
            $message = __d('baser_core', 'ウィジェットエリア「{0}」を削除しました。', $widgetArea->name);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }

        $this->set([
            'message' => $message,
            'widgetArea' => $widgetArea,
        ]);
        $this->viewBuilder()->setOption('serialize', ['widgetArea', 'message']);
    }

    /**
     * メールフィールドのバッチ処理
     *
     * 指定したメールフィールドに対して削除、公開、非公開の処理を一括で行う
     *
     * ### エラー
     * 受け取ったPOSTデータのキー名'batch'が'delete'以外の値であれば500エラーを発生させる
     *
     * @param WidgetAreasServiceInterface $service
     * @checked
     * @noTodo
     */
    public function batch(WidgetAreasServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'delete' => __d('baser_core', '削除'),
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
                sprintf(__d('baser_core', 'ウィジェットエリア「%s」を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
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
     * [AJAX] タイトル更新
     *
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update_title(WidgetAreasServiceInterface $service, int $widgetAreaId)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        $entity = $errors = null;
        try {
            $entity = $service->update($service->get($widgetAreaId), $this->getRequest()->getData());
            $message = __d('baser_core', 'ウィジェットエリア「{0}」を更新しました。', $entity->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'widgetArea' => $entity?->toArray(),
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'widgetArea', 'errors']);
    }

    /**
     * [AJAX] ウィジェット更新
     *
     * @param WidgetAreasServiceInterface $service
     * @param int $widgetAreaId
     * @return void
     * @checked
     * @noTodo
     */
    public function update_widget(WidgetAreasServiceInterface $service, int $widgetAreaId)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        $entity = $errors = null;
        try {
            $entity = $service->updateWidget($widgetAreaId, $this->getRequest()->getData());
            $message = __d('baser_core', 'ウィジェットエリア「{0}」を更新しました。', $entity->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (PersistenceFailedException $e) {
            $this->setResponse($this->response->withStatus(400));
            $errors = $e->getEntity()->getErrors();
            $message = __d('baser_core', "入力エラーです。内容を修正してください。");
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'widgetArea' => $entity?->toArray(),
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'widgetArea', 'errors']);
    }

    /**
     * 並び順を更新する
     * @param WidgetAreasServiceInterface $service
     * @param int $widgetAreaId
     * @return void
     * @checked
     * @noTodo
     */
    public function update_sort(WidgetAreasServiceInterface $service, int $widgetAreaId)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        $entity = $errors = null;
        try {
            $entity = $service->updateSort($widgetAreaId, $this->getRequest()->getData());
            $message = __d('baser_core', 'ウィジェットエリア「{0}」の並び順を更新しました。', $entity->name);
            $this->BcMessage->setSuccess($message, true, false);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'widgetArea' => $entity?->toArray(),
            'errors' => $errors
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'widgetArea', 'errors']);
    }

    /**
     * [AJAX] ウィジェットを削除
     *
     * @param WidgetAreasServiceInterface $service
     * @param int $widgetAreaId
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     */
    public function delete_widget(WidgetAreasServiceInterface $service, int $widgetAreaId, int $id)
    {
        $this->getRequest()->allowMethod(['post', 'put']);
        $entity = null;
        try {
            $entity = $service->deleteWidget($widgetAreaId, $id);
            $message = __d('baser_core', 'ウィジェットを削除しました。');
            $this->BcMessage->setSuccess($message, true, false);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'widgetArea' => $entity?->toArray(),
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'widgetArea']);
    }

    /**
     * [API] 単一データ取得
     *
     * @param WidgetAreasServiceInterface $service
     * @param $id
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(WidgetAreasServiceInterface $service, $id)
    {
        $this->getRequest()->allowMethod(['get']);
        $widgetArea = $message = null;
        try {
            $widgetArea = $service->get($id);
        } catch (RecordNotFoundException $e) {
            $this->setResponse($this->response->withStatus(404));
            $message = __d('baser_core', 'データが見つかりません。');
        } catch (\Throwable $e) {
            $this->setResponse($this->response->withStatus(500));
            $message = __d('baser_core', 'データベース処理中にエラーが発生しました。' . $e->getMessage());
        }
        $this->set([
            'widgetArea' => $widgetArea,
            'message' => $message
        ]);
        $this->viewBuilder()->setOption('serialize', ['widgetArea', 'message']);
    }
}
