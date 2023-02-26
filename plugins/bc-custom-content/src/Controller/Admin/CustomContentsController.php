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

namespace BcCustomContent\Controller\Admin;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcCustomContent\Service\Admin\CustomContentsAdminServiceInterface;
use BcCustomContent\Service\CustomContentsServiceInterface;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * CustomContentsController
 */
class CustomContentsController extends CustomContentAdminAppController
{

    /**
     * initialize
     * @checked
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('BaserCore.BcAdminContents', [
            'entityVarName' => 'entity',
            'useForm' => true
        ]);
    }

    /**
     * カスタムコンテンツ編集
     *
     * @param CustomContentsAdminServiceInterface $service
     * @param int $id
     * @return \Cake\Http\Response|void|null
     */
    public function edit(CustomContentsAdminServiceInterface $service, int $id)
    {
        $entity = $service->get($id);

        if ($this->request->is(['patch', 'post', 'put'])) {

            // EVENT CustomContents.beforeEdit
            $event = $this->dispatchLayerEvent('beforeEdit', [
                'request' => $this->request,
            ]);
            if ($event !== false) {
                $this->request = ($event->getResult() === null || $event->getResult() === true)? $event->getData('request') : $event->getResult();
            }

            try {
                $entity = $service->update($entity, $this->request->getData());

                // EVENT CustomContents.afterEdit
                $this->dispatchLayerEvent('afterEdit', [
                    'request' => $this->request,
                ]);

                $this->BcMessage->setSuccess(__d('baser', "カスタムコンテンツ「{0}」を更新しました。", $entity->content->title));
                return $this->redirect(['action' => 'edit', $id]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。' . $e->getMessage()));
            }
        }

        $this->set($service->getViewVarsForEdit($entity));
    }

    /**
     * カスタムエントリーの一覧にリダイレクトする
     *
     * @param CustomContentsServiceInterface $service
     * @param int $id
     */
    public function index(CustomContentsServiceInterface $service, int $id)
    {
        $this->redirect([
            'controller' => 'CustomEntries',
            'action' => 'index',
            $service->get($id)->custom_table_id
        ]);
    }

}
