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

namespace BaserCore\View;

use Cake\Utility\Hash;
use Cake\Core\Configure;
use Cake\Utility\Inflector;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\View\Helper\BcAuthHelper;
use BaserCore\View\Helper\BcFormHelper;
use BaserCore\View\Helper\BcHtmlHelper;
use BaserCore\View\Helper\BcTextHelper;
use BaserCore\View\Helper\BcTimeHelper;
use BaserCore\View\Helper\BcAdminHelper;
use BaserCore\View\Helper\BcBaserHelper;
use BaserCore\View\Helper\BcContentsHelper;
use BaserCore\View\Helper\BcAdminFormHelper;
use BaserCore\View\Helper\BcFormTableHelper;
use BaserCore\View\Helper\BcListTableHelper;
use BaserCore\View\Helper\BcSiteConfigHelper;

/**
 * Class BcAdminAppView
 * @property BcBaserHelper $BcBaser
 * @property BcFormHelper $BcForm
 * @property BcAdminFormHelper $BcAdminForm
 * @property BcTimeHelper $BcTime
 * @property BcFormTableHelper $BcFormTable
 * @property BcAdminHelper $BcAdmin
 * @property BcTextHelper $BcText
 * @property BcHtmlHelper $BcHtml
 * @property BcListTableHelper $BcListTable
 * @property BcAuthHelper $BcAuth
 * @property BcContentsHelper $BcContents
 * @property BcSiteConfigHelper $BcSiteConfig
 */
class BcAdminAppView extends AppView
{

    /**
     * initialize
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadHelper('BaserCore.BcAdminForm', ['templates' => 'BaserCore.bc_form']);
        $this->loadHelper('BaserCore.BcAuth');
        $this->loadHelper('BaserCore.BcText');
        $this->loadHelper('BaserCore.BcTime');
        $this->loadHelper('BaserCore.BcContents');
        $this->loadHelper('BaserCore.BcListTable');
        $this->loadHelper('BaserCore.BcUpload');
        $this->loadHelper('BaserCore.BcHtml');
        $this->loadHelper('BaserCore.BcSiteConfig');
        $this->loadHelper('BaserCore.BcSearchBox');
        // TODO ucmitz 未移行のため暫定措置
        // >>>
//        $this->loadHelper('BaserCore.BcFormTable');
//        $this->loadHelper('BaserCore.BcLayout');
        // <<<
        if (!$this->get('title')) {
            $this->set('title', 'Undefined');
        }
    }

    /**
     * _paths
     *
     * 管理画面のファイルを別のテーマのテンプレートで上書きするためのパスを追加する
     * 別のテーマは、 setting.php で、 BcApp.customAdminTheme として定義する
     *
     * @param string|null $plugin
     * @param bool $cached
     * @return array
     * @checked
     * @unitTest
     * @noTodo
     */
    protected function _paths(?string $plugin = null, bool $cached = true): array
    {
        $paths = parent::_paths($plugin, $cached);
        $customAdminTheme = Configure::read('BcApp.customAdminTheme');
        $plugins = Hash::extract(BcUtil::getEnablePlugins(), '{n}.name');
        if (!$customAdminTheme || !in_array($customAdminTheme, $plugins)) {
            return $paths;
        }
        $themes = [$customAdminTheme, Inflector::dasherize($customAdminTheme)];
        foreach($themes as $theme) {
            array_unshift($paths,
                ROOT . DS
                . 'plugins' . DS
                . $theme . DS
                . 'templates' . DS
            );
        }
        return $paths;
    }

}
