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
use BcWidgetArea\Service\WidgetAreasServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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
    public function add()
    {
        $this->setTitle(__d('baser', '新規ウィジェットエリア登録'));

        if ($this->request->getData()) {
            $this->WidgetArea->set($this->request->getData());
            if (!$this->WidgetArea->save()) {
                $this->BcMessage->setError(__d('baser', '新しいウィジェットエリアの保存に失敗しました。'));
            } else {
                $this->BcMessage->setInfo(__d('baser', '新しいウィジェットエリアを保存しました。'));
                $this->redirect(['action' => 'edit', $this->WidgetArea->getInsertID()]);
            }
        }
        $this->setHelp('widget_areas_form');
        $this->render('form');
    }

    /**
     * 編集
     *
     * @return void
     */
    public function edit($id)
    {
        $this->setTitle(__d('baser', 'ウィジェットエリア編集'));

        $widgetArea = $this->WidgetArea->read(null, $id);
        if ($widgetArea['WidgetArea']['widgets']) {
            $widgetArea['WidgetArea']['widgets'] = $widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
            usort($widgetArea['WidgetArea']['widgets'], 'widgetSort');
            foreach($widgets as $widget) {
                $key = key($widget);
                $widgetArea[$key] = $widget[$key];
            }
        }
        $this->request = $this->request->withParsedBody($widgetArea);

        $widgetInfos = [0 => ['title' => __d('baser', 'コアウィジェット'), 'plugin' => '', 'paths' => [BASER_VIEWS . 'Elements' . DS . 'admin' . DS . 'widgets']]];
        if (is_dir(APP . 'View' . DS . 'Elements' . DS . 'admin' . DS . 'widgets')) {
            $widgetInfos[0]['paths'][] = APP . 'View' . DS . 'Elements' . DS . 'admin' . DS . 'widgets';
        }

        $plugins = $this->Plugin->find('all', ['conditions' => ['status' => true]]);

        if ($plugins) {
            $pluginWidgets = [];
            $paths = App::path('Plugin');
            foreach($plugins as $plugin) {

                $pluginWidget['paths'] = [];
                foreach($paths as $path) {
                    $path .= $plugin['Plugin']['name'] . DS . 'View' . DS . 'Elements' . DS . 'admin' . DS . 'widgets';
                    if (is_dir($path)) {
                        $pluginWidget['paths'][] = $path;
                    }
                }

                if (!$pluginWidget['paths']) {
                    continue;
                }

                $pluginWidget['title'] = $plugin['Plugin']['title'] . 'ウィジェット';
                $pluginWidget['plugin'] = $plugin['Plugin']['name'];
                $pluginWidgets[] = $pluginWidget;
            }
            if ($pluginWidgets) {
                $widgetInfos = am($widgetInfos, $pluginWidgets);
            }
        }

        $this->set('widgetInfos', $widgetInfos);
        $this->setHelp('widget_areas_form');
        $this->render('form');
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
