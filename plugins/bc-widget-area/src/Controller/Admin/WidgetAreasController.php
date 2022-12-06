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

 namespace BcWidgetArea\Controller\Admin;

use BaserCore\Controller\Admin\BcAdminAppController;
use BaserCore\Utility\BcSiteConfig;
use BcWidgetArea\Service\Admin\WidgetAreasAdminService;
use BcWidgetArea\Service\Admin\WidgetAreasAdminServiceInterface;
use BcWidgetArea\Service\WidgetAreasServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\ORM\Exception\PersistenceFailedException;

/**
 * Class WidgetAreasController
 *
 * ウィジェットエリアコントローラー
 */
class WidgetAreasController extends BcAdminAppController
{

    /**
     * 一覧
     * @return void
     * @checked
     * @noTodo
     */
    public function index(WidgetAreasServiceInterface $service)
    {
        $this->setViewConditions('MailMessage', [
            'default' => [
                'query' => [
                    'limit' => BcSiteConfig::get('admin_list_num'),
        ]]]);
        $this->set([
            'widgetAreas' => $this->paginate($service->getIndex($this->getRequest()->getQueryParams()))
        ]);
    }

    /**
     * 新規登録
     *
     * @return void
     */
    public function add(WidgetAreasServiceInterface $service)
    {
        if ($this->getRequest()->is(['post', 'put'])) {
            try {
                $entity = $service->create($this->getRequest()->getData());
                $this->BcMessage->setInfo(__d('baser', '新しいウィジェットエリアを保存しました。'));
                $this->redirect(['action' => 'edit', $entity->id]);
            } catch (PersistenceFailedException $e) {
                $entity = $e->getEntity();
                $this->BcMessage->setError(__d('baser', '新しいウィジェットエリアの保存に失敗しました。'));
            } catch (\Throwable $e) {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
            }
        }
        $this->set(['widgetArea' => $entity?? $service->getNew()]);
    }

    /**
     * 編集
     *
     * @param WidgetAreasAdminService $service
     * @param int $id
     * @return void
     * @checked
     * @noTodo
     */
    public function edit(WidgetAreasAdminServiceInterface $service, int $id)
    {
        $entity = $service->get($id);
        $this->set($service->getViewVarsForEdit($entity));
    }

    /**
     * [ADMIN] 削除処理　(ajax)
     *
     * @param int ID
     * @return void
     * @checked
     * @noTodo
     */
    public function delete(WidgetAreasServiceInterface $service, $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $entity = $service->get($id);
        try {
            if($service->delete($id)) {
                $this->BcMessage->setSuccess(__d('baser', 'ウィジェットエリア「{0}」を削除しました。', $entity->name));
            } else {
                $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
            }
        } catch (\Throwable $e) {
            $this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage());
        }
        return $this->redirect(['action' => 'index']);
    }

}
