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

namespace BaserCore\Service;

use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Core\Configure;
use BaserCore\Utility\BcUtil;
use Cake\Core\App;
use Cake\Filesystem\Folder;
use BaserCore\Model\Entity\Plugin;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
/**
 * Class PluginsService
 * @package BaserCore\Service
 * @property PluginsTable $Plugins
 */
class PluginsService implements PluginsServiceInterface
{

    /**
     * Plugins Table
     * @var \Cake\ORM\Table
     */
    public $Plugins;

    /**
     * PluginsService constructor.
     */
    public function __construct()
    {
        $this->Plugins = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
    }

    /**
     * ユーザー一覧を取得
     * @param string $sortMode
     * @return array $plugins
     * @checked
     * @unitTest
     */
    public function getIndex(string $sortMode): array
    {
        if($sortMode) {
            // DBに登録されてる場合
            $registered = $this->Plugins->find()
                ->order(['priority'])
                ->all()
                ->toArray();
            return $registered;
        } else {
            // DBに登録されてないもの含めて、プラグインフォルダから取得
            // TODO: チェック必要
            $paths = App::path('plugins');
            $pluginConfigs = [];
            foreach($paths as $path) {
                $Folder = new Folder($path);
                $files = $Folder->read(true, true, true);
                foreach($files[0] as $file) {
                    $name = Inflector::camelize(Inflector::underscore(basename($file)));
                    if (!in_array(basename($file), Configure::read('BcApp.core'))) {
                        $pluginConfigs[$name] = $this->getPluginConfig($name);
                    }
                }
            }
            return array_values($pluginConfigs);
        }
    }

    /**
     * プラグイン情報を取得する
     *
     * @param string $name プラグイン名
     * @return Plugin|EntityInterface
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getPluginConfig($name)
    {

        $pluginName = Inflector::camelize($name, '-');

        // プラグインのバージョンを取得
        $corePlugins = Configure::read('BcApp.corePlugins');
        if (in_array($pluginName, $corePlugins)) {
            $core = true;
            $version = BcUtil::getVersion();
        } else {
            $core = false;
            $version = BcUtil::getVersion($pluginName);
        }

        $result = $this->Plugins->find()
            ->order(['priority'])
            ->where(['name' => $pluginName])
            ->first();

        if ($result) {
            $pluginRecord = $result;
            $this->Plugins->patchEntity($pluginRecord, [
                'update' => false,
                'core' => $core,
                'permission' => 1,
                'registered' => true
            ]);
            if (BcUtil::verpoint($pluginRecord->version) < BcUtil::verpoint($version) &&
                !in_array($pluginRecord->name, Configure::read('BcApp.corePlugins'))
            ) {
                $pluginRecord->update = true;
            }
        } else {
            $pluginRecord = $this->Plugins->newEntity([
                'id' => '',
                'name' => $pluginName,
                'created' => '',
                'version' => $version,
                'status' => false,
                'update' => false,
                'core' => $core,
                'permission' => 1,
                'registered' => false,
            ]);
        }

        // 設定ファイル読み込み
        $appConfigPath = BcUtil::getPluginPath($name) . 'config.php';
        if (file_exists($appConfigPath)) {
            $this->Plugins->patchEntity($pluginRecord, include $appConfigPath);
        }
        return $pluginRecord;
    }
}
