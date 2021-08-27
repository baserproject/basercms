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

use BaserCore\Service\PluginService;
use BaserCore\Service\PluginServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcAdminPluginHelper
 * @package BaserCore\View\Helper
 */
class BcAdminPluginHelper extends Helper
{

    /**
     * BcContainerTrait
     */
    use BcContainerTrait;

    /**
     * Plugin Manage Service
     * @var PluginService
     */
    public $PluginService;

    /**
     * initialize
     * @param array $config
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->PluginManage = $this->getService(PluginServiceInterface::class);
    }

    /**
     * プラグインインストールについてのメッセージを取得する
     * @param string $name
     */
    public function getInstallStatusMessage($name): string
    {
        return $this->PluginManage->getInstallStatusMessage($name);
    }

}
