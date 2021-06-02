<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Plugin Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Plugin Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\View\Helper;

use BaserCore\Service\PluginsService;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Core\Configure;

/**
 * Class BcPluginManageHelper
 * @package BaserCore\View\Helper
 */
class BcPluginManageHelper extends Helper
{

    /**
     * Plugin Manage Service
     * @var PluginManageService
     */
    public $PluginManage;
}
