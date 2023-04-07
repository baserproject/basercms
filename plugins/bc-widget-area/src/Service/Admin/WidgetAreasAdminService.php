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

namespace BcWidgetArea\Service\Admin;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BcWidgetArea\Model\Entity\WidgetArea;
use BcWidgetArea\Service\WidgetAreasService;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;

/**
 * WidgetAreasAdminService
 */
class WidgetAreasAdminService extends WidgetAreasService implements WidgetAreasAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * 編集画面用の View 変数を取得する
     *
     * @param EntityInterface|WidgetArea $widgetArea
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForEdit(EntityInterface $widgetArea)
    {
        return [
            'widgetArea' => $widgetArea,
            'widgetInfos' => $this->getWidgetInfos()
        ];
    }

    /**
     * ウィジェット情報を取得する
     *
     * プラグインのウィジェットも取得する
     *
     * ### 各ウィジェット情報の内容
     * - title: タイトル
     * - plugin: プラグイン名
     * - paths: 各プラグインの View/Admin/element/widgets/
     *
     * @return array|array[]
     * @checked
     * @noTodo
     */
    protected function getWidgetInfos()
    {
        $widgetInfos = [0 => [
            'title' => __d('baser_core', 'コアウィジェット'),
            'plugin' => 'BaserCore',
            'paths' => [Plugin::templatePath(Configure::read('BcApp.coreAdminTheme')) . 'Admin' . DS . 'element' . DS . 'widget']
        ]];
        $plugins = BcUtil::getEnablePlugins();
        if (!$plugins) return $widgetInfos;
        $pluginWidgets = [];
        foreach($plugins as $plugin) {
            $pluginWidget['paths'] = [];
            $path = BcUtil::getExistsTemplateDir('', $plugin->name, 'Admin' . DS . 'element' . DS . 'widget');
            if (!is_dir($path)) continue;
            $pluginWidgets[] = [
                'title' => $plugin->title . __d('baser_core', 'ウィジェット'),
                'plugin' => $plugin->name,
                'paths' => [$path]
            ];
        }
        if ($pluginWidgets) $widgetInfos = array_merge_recursive($widgetInfos, $pluginWidgets);
        return $widgetInfos;
    }

}
