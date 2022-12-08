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

namespace BcWidgetArea\View\Helper;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\View\Helper\BcBaserHelper;
use BcWidgetArea\Service\WidgetAreasService;
use BcWidgetArea\Service\WidgetAreasServiceInterface;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * ウィジェットエリアヘルパー
 *
 * @property BcBaserHelper $BcBaser
 */
class BcWidgetAreaHelper extends Helper
{
    /**
     * Trait
     */
    use BcEventDispatcherTrait;
    use BcContainerTrait;

    /**
     * Helper
     * @var string[]
     */
    public $helpers = ['BaserCore.BcBaser'];

    /**
     * ウィジェットエリアを出力する
     *
     * @param int $no ウィジェットエリアNO（初期値 : null）※ 省略した場合は、コンテンツごとに管理システムにて設定されているウィジェットエリアを出力する
     * @param array $options オプション（初期値 : array()）
     * @return void
     */
    public function widgetArea($no = null, $options = [])
    {
        echo $this->getWidgetArea($no, $options);
    }

    /**
     * ウィジェットエリアを取得する
     *
     * @param int $no ウィジェットエリアNO（初期値 : null）※ 省略した場合は、コンテンツごとに管理システムにて設定されているウィジェットエリアを出力する
     * @param array $options オプション（初期値 : array()）
     * @return string
     */
    public function getWidgetArea($no = null, $options = [])
    {
        if (!$no && !empty($this->_View->get('currentWidgetAreaId'))) {
            $no = $this->_View->get('currentWidgetAreaId');
        }
        if ($no) return $this->BcBaser->getElement('widget_area', ['no' => $no], $options);
        return '';
    }

    /**
     * ウィジェットエリアを表示する
     *
     * @param int $no ウィジェットエリアNO
     * @param array $options オプション
     *  `subDir` (boolean) エレメントのパスについてプレフィックスによるサブディレクトリを追加するかどうか
     *  ※ その他のパラメータについては、View::element() を参照
     */
    public function show($no, $options = [])
    {
        $options = array_merge([
            'subDir' => true,
        ], $options);

        /** @var WidgetAreasService $widgetAreasService */
        $widgetAreasService = $this->getService(WidgetAreasServiceInterface::class);
        try {
            $widgetArea = $widgetAreasService->get($no);
        } catch (\Throwable $e) {
            return;
        }

        if (!$widgetArea->widgets) return;

        if ($this->BcBaser->isAdminUser() && Configure::read('BcWidget.editLinkAtFront')) {
            $editLink = $this->BcBaser->getUrl(['prefix' => 'Admin', 'plugin' => 'BcWidgetArea', 'controller' => 'WidgetAreas', 'action' => 'edit', $no]);
            $this->BcBaser->element('admin/widget_link', ['editLink' => $editLink]);
        }

        $widgets = $widgetArea->widgets_array;
        foreach($widgets as $widget) {
            $key = key($widget);
            if ($widget[$key]['status']) {
                $plugin = '';
                $params = ['widget' => true];
                $params = array_merge($params, $widget[$key]);
                if (!empty($params['plugin'])) {
                    $plugin = Inflector::camelize($params['plugin']) . '.';
                    unset($params['plugin']);
                }
                $this->_View->BcBaser->element($plugin . 'widgets/' . $widget[$key]['element'], $params, $options);
            }
        }
    }

}
