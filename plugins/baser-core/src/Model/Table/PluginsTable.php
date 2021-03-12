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

use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\Utility\Inflector;

class PluginsTable extends Table
{
    /**
     * プラグイン情報を取得する
     *
     * @param array $datas プラグインのデータ配列
     * @param string $file プラグインファイルのパス
     * @return array
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
            if (BcUtil::verpoint($pluginRecord->version) < BcUtil::verpoint($version) && !in_array($pluginRecord->name, Configure::read('BcApp.corePlugins'))) {
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
}
