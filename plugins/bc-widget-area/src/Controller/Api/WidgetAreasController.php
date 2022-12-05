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

 namespace BcWidgetArea\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class WidgetAreasController
 *
 * ウィジェットエリアコントローラー
 */
class WidgetAreasController extends BcApiController
{

    /**
     * [AJAX] タイトル更新
     *
     * @return void
     */
    public function update_title()
    {
        if (!$this->request->getData()) {
            $this->notFound();
        }

        $this->WidgetArea->set($this->request->getData());
        if ($this->WidgetArea->save()) {
            echo true;
        }
        exit();
    }

    /**
     * [AJAX] ウィジェット更新
     *
     * @param int $widgetAreaId
     * @return void
     */
    public function update_widget($widgetAreaId)
    {
        if (!$widgetAreaId || !$this->request->getData()) {
            exit();
        }

        $data = $this->request->getData();
        if (isset($data['_Token'])) {
            unset($data['_Token']);
        }
        $dataKey = key($data);
        $widgetArea = $this->WidgetArea->read(null, $widgetAreaId);
        $update = false;
        if ($widgetArea['WidgetArea']['widgets']) {
            $widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
            foreach($widgets as $key => $widget) {
                if (isset($data[$dataKey]['id']) && isset($widget[$dataKey]['id']) && $widget[$dataKey]['id'] == $data[$dataKey]['id']) {
                    $widgets[$key] = $data;
                    $update = true;
                    break;
                }
            }
        } else {
            $widgets = [];
        }
        if (!$update) {
            $widgets[] = $data;
        }

        $widgetArea['WidgetArea']['widgets'] = BcUtil::serialize($widgets);

        $this->WidgetArea->set($widgetArea);
        if ($this->WidgetArea->save()) {
            echo true;
        }
        // 全てのキャッシュを削除しないと画面に反映できない。
        //clearViewCache('element_widget','');
        clearViewCache();

        exit();
    }

    /**
     * 並び順を更新する
     * @param int $widgetAreaId
     * @return void
     */
    public function update_sort($widgetAreaId)
    {
        if (!$widgetAreaId || !$this->request->getData()) {
            exit();
        }
        $ids = explode(',', $this->request->getData('WidgetArea.sorted_ids'));
        $widgetArea = $this->WidgetArea->read(null, $widgetAreaId);
        if ($widgetArea['WidgetArea']['widgets']) {
            $widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
            foreach($widgets as $key => $widget) {
                $widgetKey = key($widget);
                $widgets[$key][$widgetKey]['sort'] = array_search($widget[$widgetKey]['id'], $ids) + 1;
            }
            $widgetArea['WidgetArea']['widgets'] = BcUtil::serialize($widgets);
            $this->WidgetArea->set($widgetArea);
            if ($this->WidgetArea->save()) {
                echo true;
            }
        } else {
            echo true;
        }
        // 全てのキャッシュを削除しないと画面に反映できない。
        //clearViewCache('element_widget','');
        clearViewCache();
        exit();
    }

    /**
     * [AJAX] ウィジェットを削除
     *
     * @param int $widgetAreaId
     * @param int $id
     * @return void
     */
    public function del_widget($widgetAreaId, $id)
    {
        $this->_checkSubmitToken();
        $widgetArea = $this->WidgetArea->read(null, $widgetAreaId);
        if (!$widgetArea['WidgetArea']['widgets']) {
            exit();
        }
        $widgets = BcUtil::unserialize($widgetArea['WidgetArea']['widgets']);
        foreach($widgets as $key => $widget) {
            $type = key($widget);
            if ($id == $widget[$type]['id']) {
                unset($widgets[$key]);
                break;
            }
        }
        if ($widgets) {
            $widgetArea['WidgetArea']['widgets'] = BcUtil::serialize($widgets);
        } else {
            $widgetArea['WidgetArea']['widgets'] = '';
        }
        $this->WidgetArea->set($widgetArea);
        if ($this->WidgetArea->save()) {
            echo true;
        }
        // 全てのキャッシュを削除しないと画面に反映できない。
        //clearViewCache('element_widget','');
        clearViewCache();
        exit();
    }

}

/**
 * ウィジェットの並べ替えを行う
 * usortのコールバックメソッド
 *
 * @param array $a
 * @param array $b
 * @return int
 */
function widgetSort($a, $b)
{
    $aKey = key($a);
    $bKey = key($b);
    if ($a[$aKey]['sort'] == $b[$bKey]['sort']) {
        return 0;
    }
    if ($a[$aKey]['sort'] < $b[$bKey]['sort']) {
        return -1;
    }

    return 1;
}
