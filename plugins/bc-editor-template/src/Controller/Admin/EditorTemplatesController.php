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

namespace BcEditorTemplate\Controller\Admin;

use BaserCore\Error\BcException;
use BaserCore\Utility\BcSiteConfig;
use BcEditorTemplate\Service\EditorTemplatesService;
use BcEditorTemplate\Service\EditorTemplatesServiceInterface;
use BaserCore\Controller\Admin\BcAdminAppController;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class EditorTemplatesController
 *
 * エディタテンプレートコントローラー
 *
 * エディタテンプレートの管理を行う
 */
class EditorTemplatesController extends BcAdminAppController
{

    /**
     * エディタテンプレートの一覧を表示する
     *
     * @param EditorTemplatesService $service
     * @checked
     * @noTodo
     */
    public function index(EditorTemplatesServiceInterface $service)
    {
        $this->set([
            'editorTemplates' => $service->getIndex()
        ]);
    }

    /**
     * エディタテンプレートを新しく登録する
     *
     * @param EditorTemplatesService $service
     * @checked
     * @noTodo
     */
    public function add(EditorTemplatesServiceInterface $service)
    {
        if ($this->request->is(['post', 'put'])) {
            try {
                $entity = $service->create($this->getRequest()->getData());

                // EVENT EditorTemplates.afterAdd
                $this->dispatchLayerEvent('afterAdd', [
                    'data' => $entity
                ]);

                $this->BcMessage->setSuccess(__d('baser_core', 'テンプレート「{0}」を追加しました', $entity->name));
                $this->redirect(['action' => 'index']);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $this->set([
            'editorTemplate' => $entity?? $service->getNew()
        ]);
        $this->viewBuilder()->addHelper(BcSiteConfig::get('editor'));
    }

    /**
     * [ADMIN] 編集
     *
     * @param EditorTemplatesService $service
     * @param int $id
     * @checked
     * @noTodo
     */
    public function edit(EditorTemplatesServiceInterface $service, int $id)
    {
        $entity = $service->get($id);
        if ($this->request->is(['post', 'put'])) {
            try {
                $entity = $service->update($entity, $this->getRequest()->getData());

                // EVENT EditorTemplates.afterEdit
                $this->dispatchLayerEvent('afterEdit', [
                    'data' => $entity
                ]);

                $this->BcMessage->setSuccess(__d('baser_core', 'テンプレート「{0}」を更新しました', $entity->name));
                $this->redirect(['action' => 'index']);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser_core', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $this->set([
            'editorTemplate' => $entity
        ]);
        $this->viewBuilder()->addHelper(BcSiteConfig::get('editor'));
    }

    /**
     * [ADMIN] 削除
     *
     * @param EditorTemplatesService $service
     * @param int $id
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     */
    public function delete(EditorTemplatesServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            $entity = $service->get($id);
            if ($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser_core', 'テンプレート「{0}」を削除しました。', $entity->name));
            }
        } catch (BcException $e) {
            $this->BcMessage->setError(__d('baser_core', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * [ADMIN] CKEditor用テンプレート用のjavascriptを出力する
     *
     * @param EditorTemplatesService $service
     * @checked
     * @noTodo
     */
    public function js(EditorTemplatesServiceInterface $service)
    {
        header('Content-Type: text/javascript; name="editor_templates.js"');
        $this->viewBuilder()->disableAutoLayout();
        $this->set([
            'templates' => $service->getIndex()
        ]);
        $this->viewBuilder()->addHelper('BaserCore.BcArray');
    }

}
