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
use Cake\Cache\Cache;
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
        /** @var \BaserCore\Model\Entity\Plugin $plugin */
        $installStatusMessage = $this->getInstallStatusMessage($plugin->name);
        return [
            'plugin' => $plugin,
            'installStatusMessage' => $installStatusMessage,
            'installMessage' => (!$installStatusMessage)? $plugin->installMessage : '',
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
        $scriptNum = count($plugin->getUpdaters('', true));
        $scriptMessages = $plugin->getUpdateScriptMessages('', true);
        $coreDownloaded = Cache::read('coreDownloaded', '_bc_update_');

        if ($entity->name === 'BaserCore') {
            $availableVersion = null;
            if($coreDownloaded) {
                $availableVersion = BcUtil::getVersion('BaserCore', true);
            }
            if(!$availableVersion) {
                $availableVersion = $this->getAvailableCoreVersion();
            }
            $corePlugins = Configure::read('BcApp.corePlugins');
            foreach($corePlugins as $corePlugin) {
                $scriptNum += count($plugin->getUpdaters($corePlugin, true));
                $scriptMessages += $plugin->getUpdateScriptMessages($corePlugin, true);
            }
        } else {
            $availableVersion = null;
        }

        $programVerPoint = BcUtil::verpoint($programVersion);
        $dbVerPoint = BcUtil::verpoint($dbVersion);

        $isWritableVendor = is_writable(ROOT . DS . 'vendor');
        $isWritableComposerJson = is_writable(ROOT . DS . 'composer.json');
        $isWritableComposerLock = is_writable(ROOT . DS . 'composer.lock');
        $requireUpdate = $this->isRequireUpdate(
            $programVersion,
            $dbVersion,
            $availableVersion
        );
        if($entity->name === 'BaserCore') {
            $isUpdatable = ($requireUpdate && $isWritableVendor && $isWritableComposerJson && $isWritableComposerLock);
        } else {
            $isUpdatable = $requireUpdate;
        }
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
            'coreDownloaded' => $coreDownloaded,
            'php' => $this->whichPhp(),
            'isCore' => $entity->name === 'BaserCore',
            'isWritableVendor' => $isWritableVendor,
            'isWritableComposerJson' => $isWritableComposerJson,
            'isWritableComposerLock' => $isWritableComposerLock,
            'isUpdatable' => $isUpdatable
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
     * DBのバージョンと利用可能なバージョンが違う場合に必要とする
     * @param string $programVersion
     * @param string $dbVersion
     * @param string $availableVersion
     * @param int|false $scriptNum
     * @return bool
     * @checked
     * @noTodo
     */
    public function isRequireUpdate(string $programVersion, ?string $dbVersion, ?string $availableVersion)
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

        if(is_null($availableVersion)) {
            // プラグインの場合 プログラムのバージョンを利用可能なバージョンとする
            $availableVerPoint = $programVerPoint;
        } else {
            // コアの場合は、プログラムのバージョンとDBのバージョンが違う場合はアップデート不可
            if ($programVerPoint !== $dbVerPoint) return false;
        }
        if ($availableVerPoint > $dbVerPoint) return true;
        return false;
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

    /**
     * プラグインアップロード画面用のデータを取得
     * @return array
     * @checked
     * @noTodo
     */
    public function getViewVarsForAdd()
    {
        return [
            'isPluginsDirWritable' => $this->isPluginsDirWritable()
        ];
    }

    /**
     * プラグインディレクトリが書き込み可能かどうか
     * @return bool
     * @checked
     * @noTodo
     */
    public function isPluginsDirWritable()
    {
        return is_writable(BASER_PLUGINS);
    }

}
