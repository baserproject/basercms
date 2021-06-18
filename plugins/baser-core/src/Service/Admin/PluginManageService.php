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
     * @param string $connection test connection指定用
     * @return bool|null
     * @throws Exception
     * @checked
     * @unitTest
     * @noTodo
     */
    public function install($name, $connection = 'default'): ?bool
    {
        return parent::install($name, $connection);
    }

    /**
     * インストール可能かチェックする
     *
     * @param $pluginName
     * @return string
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getInstallStatusMessage($pluginName): string
    {
        $pluginName = urldecode($pluginName);
        $installedPlugin = $this->Plugins->find()->where([
            'name' => $pluginName,
            'status' => true,
        ])->first();

        // 既にプラグインがインストール済み
        if ($installedPlugin) {
            return '既にインストール済のプラグインです。';
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
            return 'インストールしようとしているプラグインのフォルダが存在しません。';
        }

        // インストールしようとしているプラグイン名と、設定ファイル内のプラグイン名が違う
        if (!empty($config['name']) && $pluginName !== $config['name']) {
            return 'このプラグイン名のフォルダ名を' . $config['name'] . 'にしてください。';
        }
        return '';
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
     * @param string $connection
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function resetDb(string $name, $connection = 'default'):void
    {
        parent::resetDb($name, $connection);
    }

    /**
     * プラグインを削除する
     * @param string $name
     * @param string $connection
     * @checked
     * @noTodo
     * @unitTest
     */
    public function uninstall(string $name, $connection = 'default'): void
    {
        parent::uninstall(urldecode($name), $connection);
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
    /**
     * ユーザーグループにアクセス許可設定を追加する
     *
     * @param array $data リクエストデータ
     * @return void
     * @checked
     * @unitTest
     */
    public function allow($data): void
    {
        parent::allow($data);
    }
}
