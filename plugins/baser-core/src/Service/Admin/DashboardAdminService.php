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
namespace BaserCore\Service\Admin;

use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * DashboardAdminService
 */
class DashboardAdminService implements DashboardAdminServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * ダッシュボード画面用のデータを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForIndex(int $logNum): array
    {
        return [
            'panels' => $this->getPanels(),
            'dblogs' => $this->getService(DblogsServiceInterface::class)->getDblogs($logNum),
            'contentsInfo' => $this->getService(ContentsServiceInterface::class)->getContentsInfo()
        ];
    }

    /**
     * パネルを取得する
     * @return array
     */
    public function getPanels()
    {
        $panels = [];
        $plugins = Plugin::loaded();
        $corePlugins = array_merge(Configure::read('BcApp.core'),  Configure::read('BcApp.corePlugins'));

        foreach($corePlugins as $corePlugin) {
            $panels[$corePlugin] = BcUtil::getTemplateList('Admin/element/Dashboard', $corePlugin);
            $key = array_search($corePlugin, $plugins);
            if($key !== false) unset($plugins[$key]);
        }

        if ($plugins) {
            $vendorPanels = [];
            foreach($plugins as $plugin) {
                $templates = BcUtil::getTemplateList('Admin/element/Dashboard', $plugin);
                foreach($templates as $template) {
                    foreach($panels as $corePlugin => $coreTemplates) {
                        if(in_array($template, $coreTemplates)) {
                            unset($panels[$corePlugin][$template]);
                            break;
                        }
                    }
                }
                $vendorPanels[$plugin] = $templates;
            }
        }
        return array_merge($panels, $vendorPanels);
    }

}
