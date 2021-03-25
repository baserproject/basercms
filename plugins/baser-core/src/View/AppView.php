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
use Cake\View\View;

/**
 * Class AppView
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
 */
class AppView extends View
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadHelper('BaserCore.BcAdminForm', ['templates' => 'BaserCore.bc_form']);
    }
}
