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
        $programVersion = BcUtil::getVersion($entity->name);
        $dbVersion = BcUtil::getDbVersion($entity->name);
        BcUtil::includePluginClass($entity->name);
        $plugin = CakePlugin::getCollection()->create($entity->name);
        $scriptNum = count($plugin->getUpdaters());
        $scriptMessages = $plugin->getUpdateScriptMessages();

        if ($entity->name === 'BaserCore') {
            $availableVersion = $this->getAvailableCoreVersion();
            $corePlugins = Configure::read('BcApp.corePlugins');
            foreach($corePlugins as $corePlugin) {
                $scriptNum += count($plugin->getUpdaters($corePlugin));
                $scriptMessages += $plugin->getUpdateScriptMessages($corePlugin);
            }
        } else {
            $availableVersion = null;
        }

        $programVerPoint = BcUtil::verpoint($programVersion);
        $dbVerPoint = BcUtil::verpoint($dbVersion);

        $isWritableVendor = is_writable(ROOT . DS . 'vendor');
        $isWritableComposerJson = is_writable(ROOT . DS . 'composer.json');
        $isWritableComposerLock = is_writable(ROOT . DS . 'composer.lock');

        return [
            'plugin' => $entity,
            'scriptNum' => $scriptNum,
            'scriptMessages' => $scriptMessages,
            'dbVersion' => $dbVersion,
            'programVersion' => $programVersion,
            'dbVerPoint' => $dbVerPoint,
            'programVerPoint' => $programVerPoint,
            'availableVersion' => $availableVersion,
            'log' => $this->getUpdateLog(),
            'requireUpdate' => $this->isRequireUpdate(
                $programVersion,
                $dbVersion,
                $availableVersion,
                $scriptNum
            ),
            'php' => $this->whichPhp(),
            'isWritableVendor' => $isWritableVendor,
            'isWritableComposerJson' => $isWritableComposerJson,
            'isWritableComposerLock' => $isWritableComposerLock,
            'isWritablePackage' => ($isWritableVendor && $isWritableComposerJson && $isWritableComposerLock)
        ];
    }

    /**
     * CLI版PHPのパスを取得する
     * @return mixed|string
     * @checked
     * @noTodo
     */
    public function whichPhp()
    {
        exec('which php', $out, $code);
        if ($code === 0) return $out[0];
        return '';
    }

    /**
     * アップデートが必要がどうか
     *
     * @param string $programVersion
     * @param string $dbVersion
     * @param string $availableVersion
     * @param int|false $scriptNum
     * @return bool
     * @checked
     * @noTodo
     */
    public function isRequireUpdate(string $programVersion, ?string $dbVersion, ?string $availableVersion, $scriptNum)
    {
        $programVerPoint = BcUtil::verpoint($programVersion);
        $dbVerPoint = BcUtil::verpoint($dbVersion);
        $availableVerPoint = true;
        if ($availableVersion) {
            $availableVerPoint = BcUtil::verpoint($availableVersion);
        }
        if ($programVerPoint === false || $dbVerPoint === false || $availableVerPoint === false) {
            return false;
        }
        if ($availableVerPoint !== true) {
            if ($availableVersion !== $programVersion) return true;
        }
        if ($programVersion !== $dbVersion || $scriptNum) {
            return true;
        } else {
            return false;
        }
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
