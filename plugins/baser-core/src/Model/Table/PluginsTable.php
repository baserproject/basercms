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

namespace BaserCore\Model\Table;

use BaserCore\Error\BcException;
use BaserCore\Utility\BcUtil;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\ORM\Table;
use Cake\Utility\Inflector;

/**
 * Class PluginsTable
 * @package BaserCore\Model\Table
 */
class PluginsTable extends Table
{

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('Timestamp');
    }

    /**
     * 利用可能なプラグインの一覧を取得
     *
     * @return array
     */
    public function getAvailable()
    {
        // プラグインフォルダーのチェックを行う
        $pluginConfigs = [];
        $paths = App::path('plugins');
        foreach($paths as $path) {
            $Folder = new Folder($path);
            $files = $Folder->read(true, true, true);
            foreach($files[0] as $file) {
                if (!in_array(basename($file), Configure::read('BcApp.core'))) {
                    $pluginConfigs[basename($file)] = $this->getPluginConfig($file);
                }
            }
        }
        return array_reverse(array_values($pluginConfigs));
    }

    /**
     * プラグイン情報を取得する
     *
     * @param array $datas プラグインのデータ配列
     * @param string $file プラグインファイルのパス
     * @return \BaserCore\Model\Entity\Plugin|\Cake\Datasource\EntityInterface
     */
    public function getPluginConfig($file)
    {

        $pluginName = Inflector::camelize(basename($file), '-');

        // プラグインのバージョンを取得
        $corePlugins = Configure::read('BcApp.corePlugins');
        if (in_array($pluginName, $corePlugins)) {
            $core = true;
            $version = BcUtil::getVersion();
        } else {
            $core = false;
            $version = BcUtil::getVersion($pluginName);
        }

        $result = $this->find()
            ->order(['priority'])
            ->where(['name' => $pluginName])
            ->first();

        if ($result) {
            $pluginRecord = $result;
            $this->patchEntity($pluginRecord, [
                'update' => false,
                'core' => $core,
                'registered' => true
            ]);
            if (BcUtil::verpoint($pluginRecord->version) < BcUtil::verpoint($version) &&
                !in_array($pluginRecord->name, Configure::read('BcApp.corePlugins'))
            ) {
                $pluginRecord->update = true;
            }
        } else {
            $pluginRecord = $this->newEntity([
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
        $appConfigPath = $file . DS . 'config.php';
        if (file_exists($appConfigPath)) {
            $this->patchEntity($pluginRecord, include $appConfigPath);
        }
        return $pluginRecord;
    }

    /**
     * インストール可能かチェックする
     *
     * @param $pluginName
     * @return bool
     */
    public function isInstallable($pluginName)
    {
        $installedPlugin = $this->find()->where([
            'name' => $pluginName,
            'status' => true,
        ])->first();

        // 既にプラグインがインストール済み
        if ($installedPlugin) {
            throw new BcException('既にインストール済のプラグインです。');
        }

        $paths = App::path('plugins');
        $existsPluginFolder = false;
        $folder = $pluginName;
        foreach($paths as $path) {
            if (!is_dir($path . $folder)) {
                $dasherize = Inflector::dasherize($folder);
                if(!is_dir($path . $dasherize)) {
                    continue;
                }
                $folder = $dasherize;
            }
            $existsPluginFolder = true;
            $configPath = $path . $folder . DS . 'config.php';
            if (file_exists($configPath)) {
                $config = include $configPath;
            }
            break;
        }

        // プラグインのフォルダが存在しない
        if (!$existsPluginFolder) {
            throw new BcException('インストールしようとしているプラグインのフォルダが存在しません。');
        }

        // インストールしようとしているプラグイン名と、設定ファイル内のプラグイン名が違う
        if (!empty($config['name']) && $pluginName !== $config['name']) {
            throw new BcException('このプラグイン名のフォルダ名を' . $config['name'] . 'にしてください。');
        }

        return true;
    }

    /**
     * プラグインをインストールする
     *
     * @param $name
     * @return bool
     */
    public function install($name): bool
    {
        $recordExists = $this->find()->where(['name' => $name])->count();
        $plugin = $this->getPluginConfig($name);
        if (!$recordExists) {
            $query = $this->find();
            $priority = $query->select(['max' => $query->func()->max('priority')])->first();
            $plugin->priority = $priority->max + 1;
            $plugin->status = true;
        }
        if ($this->save($plugin)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * プラグインをアンインストールする
     *
     * @param $name
     * @return bool
     */
    public function uninstall($name): bool
    {
        $targetPlugin = $this->find()->where(['name' => $name])->first();
        return $this->delete($targetPlugin);
    }


    /**
     * プラグインを無効化する
     *
     * @param $name
     * @return bool
     */
    public function detouch($name): bool
    {
        $targetPlugin = $this->find()->where(['name' => $name])->first();
        if ($targetPlugin === null) {
            return false;
        }
        $targetPlugin->status = 0;
        $result = $this->save($targetPlugin);
        return $result !== false;
    }



}
