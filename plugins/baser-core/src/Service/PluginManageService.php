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
use BaserCore\Model\Table\PluginsTable;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Utility\Xml;
use Exception;

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
     * @param string $name プラグイン名
     * @return bool|null
     * @param string $data test connection指定用
     * @checked
     * @unitTest
     * @noTodo
     */
    public function install($name, $data = []): ?bool
    {
        return parent::install($name, $data);
    }

    /**
     * インストール可能かチェックする
     *
     * @param $pluginName
     * @return array [string message, bool status]
     * @checked
     * @unitTest
     * @noTodo
     */
    public function installStatus($pluginName): array
    {
        $installedPlugin = $this->Plugins->find()->where([
            'name' => $pluginName,
            'status' => true,
        ])->first();

        // 既にプラグインがインストール済み
        if ($installedPlugin) {
            return ['message' => '既にインストール済のプラグインです。', 'status' => false];
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
            return ['message' => 'インストールしようとしているプラグインのフォルダが存在しません。', 'status' => false];
        }

        // インストールしようとしているプラグイン名と、設定ファイル内のプラグイン名が違う
        if (!empty($config['name']) && $pluginName !== $config['name']) {
            return ['message' => 'このプラグイン名のフォルダ名を' . $config['name'] . 'にしてください。', 'status' => false];
        }
        return ['message' => '', 'status' => true];
    }   

    /**
     * プラグインを無効にする
     * @param string $name
     * @checked
     * @noTodo
     * @unitTest
     */
    public function detach(string $name): bool
    {
        return parent::detach(urldecode($name));
    }

    /**
     * データベースをリセットする
     *
     * @param string $name
     * @param array $options
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetDb(string $name, $options = []):void
    {
        if(isset($options['connection'])) {
            $options = ['connection' => $options['connection']];
        } else {
            $options = [];
        }
        parent::resetDb($name, $options);
    }

    /**
     * プラグインを削除する
     * @param string $name
     * @param array $options
     * @checked
     * @noTodo
     * @unitTest
     */
    public function uninstall(string $name, array $options = []): void
    {
        if(isset($options['connection'])) {
            $options = ['connection' => $options['connection']];
        } else {
            $options = [];
        }
        parent::uninstall(urldecode($name), $options);
    }

    /**
     * baserマーケットのプラグイン一覧を取得する
     * @return array|mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMarketPlugins(): array
    {
        return parent::getMarketPlugins();
    }
}
