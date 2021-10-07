<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\View;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\View\Helper\BcAdminContentFolderHelper;
use BaserCore\View\Helper\BcAdminContentHelper;
use BaserCore\View\Helper\BcAdminPermissionHelper;
use BaserCore\View\Helper\BcAdminPluginHelper;
use BaserCore\View\Helper\BcAdminSiteConfigHelper;
use BaserCore\View\Helper\BcAdminSiteHelper;
use BaserCore\View\Helper\BcAdminUserHelper;
use BaserCore\View\Helper\BcAdminFormHelper;
use BaserCore\View\Helper\BcAdminHelper;
use BaserCore\View\Helper\BcAuthHelper;
use BaserCore\View\Helper\BcBaserHelper;
use BaserCore\View\Helper\BcFormHelper;
use BaserCore\View\Helper\BcFormTableHelper;
use BaserCore\View\Helper\BcHtmlHelper;
use BaserCore\View\Helper\BcListTableHelper;
use BaserCore\View\Helper\BcTextHelper;
use BaserCore\View\Helper\BcTimeHelper;
use BaserCore\View\Helper\BcContentsHelper;

/**
 * Class BcAdminAppView
 * @package BaserCore\View
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
 * @property BcAdminUserHelper $BcAdminUser
 * @property BcAdminPluginHelper $BcAdminPlugin
 * @property BcAdminSiteHelper $BcAdminSite
 * @property BcAdminPermissionHelper $BcAdminPermission
 * @property BcAdminSiteConfigHelper $BcAdminSiteConfig
 * @property BcContentsHelper $BcContents
 * @property BcAdminContentHelper $BcAdminContent
 * @property BcAdminContentFolderHelper $BcAdminContentFolder
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
        $this->loadHelper('BaserCore.BcBaser');
        $this->loadHelper('BaserCore.BcAuth');
        $this->loadHelper('BaserCore.BcText');
        $this->loadHelper('BaserCore.BcTime');
        $this->loadHelper('BaserCore.BcAdmin');
        $this->loadHelper('BaserCore.BcAdminUser');
        $this->loadHelper('BaserCore.BcAdminSiteConfig');
        $this->loadHelper('BaserCore.BcAdminSite');
        $this->loadHelper('BaserCore.BcContents');
        $this->loadHelper('BaserCore.BcListTable');
        $this->loadHelper('BaserCore.BcAdminContent');
        if (!$this->get('title')) {
            $this->set('title', 'Undefined');
        }
    }
}
