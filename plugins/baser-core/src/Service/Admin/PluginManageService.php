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

namespace BaserCore\Service\Admin;

use BaserCore\Service\PluginsService;
use Cake\Core\App;
use Cake\Utility\Inflector;
use BaserCore\Model\Table\PluginsTable;
use Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PluginManageService
 * @package BaserCore\Service
 * @property PluginsTable $Plugins
 */

class PluginManageService extends PluginsService implements PluginManageServiceInterface
{
}
