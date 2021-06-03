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

use Cake\Core\App;
use BaserCore\Error\BcException;
use Cake\Utility\Inflector;
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
    /**
     * プラグイン一覧を取得
     * @param string $sortMode
     * @return array $plugins
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getIndex(string $sortMode): array
    {
        return parent::getIndex($sortMode);
    }

    /**
     * プラグインをインストールする
     */
    public function install($name, $data = [])
    {
        return parent::install($name, $data);
    }

    /**
     * プラグイン情報を取得する
     *
     * @param string $name プラグイン名
     * @return EntityInterface
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getPluginConfig($name)
    {
        return parent::getPluginConfig($name);
    }

    /**
     * インストール可能かチェックする
     *
     * @param $pluginName
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function isInstallable($pluginName)
    {
        $installedPlugin = $this->Plugins->find()->where([
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
                if (!is_dir($path . $dasherize)) {
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
}
