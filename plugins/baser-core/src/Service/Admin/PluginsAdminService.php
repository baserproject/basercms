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

use BaserCore\Service\PluginsService;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin as CakePlugin;
use Cake\Datasource\EntityInterface;
use Cake\Filesystem\File;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * PluginsAdminService
 */
class PluginsAdminService extends PluginsService implements PluginsAdminServiceInterface
{

    /**
     * インストール画面用のデータを取得
     * @param EntityInterface $plugin
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForInstall(EntityInterface $plugin): array
    {
        return [
            'plugin' => $plugin,
            'installStatusMessage' => $this->getInstallStatusMessage($plugin->name)
        ];
    }

    /**
     * アップデート画面用のデータを取得
     * @param EntityInterface $entity
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getViewVarsForUpdate(EntityInterface $entity): array
    {
        $targetVersion = BcUtil::getVersion($entity->name);
        $dbVersion = BcUtil::getDbVersion($entity->name);
        BcUtil::includePluginClass($entity->name);
        $plugin = CakePlugin::getCollection()->create($entity->name);
        $scriptNum = count($plugin->getUpdaters());
        $scriptMessages = $plugin->getUpdateScriptMessages();

        if($entity->name === 'BaserCore') {
            $corePlugins = Configure::read('BcApp.corePlugins');
            foreach($corePlugins as $corePlugin) {
                $scriptNum += count($plugin->getUpdaters($corePlugin));
                $scriptMessages += $plugin->getUpdateScriptMessages($corePlugin);
            }
        }

        return [
            'plugin' => $entity,
            'scriptNum' => $scriptNum,
            'scriptMessages' => $scriptMessages,
            'siteVer' => $dbVersion,
            'baserVer' => $targetVersion,
            'siteVerPoint' => BcUtil::verpoint($dbVersion),
            'baserVerPoint' => BcUtil::verpoint($targetVersion),
            'log' => $this->getUpdateLog()
        ];
    }

    /**
     * アップデートログを取得する
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUpdateLog()
    {
        $updateLogFile = LOGS . 'update.log';
        $updateLog = '';
        if (file_exists($updateLogFile)) {
            $File = new File($updateLogFile);
            $updateLog = $File->read();
        }
        return $updateLog;
    }

}
