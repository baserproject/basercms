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

namespace BaserCore\Utility;

use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Database\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcUtil
 *
 * @package Baser.Lib
 */
class BcUtil
{

    /**
     * 認証領域を指定してログインユーザーのデータを取得する
     * セッションクラスが設定されていない場合にはスーパーグローバル変数を利用する
     *
     * @return mixed Entity|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function loginUser($prefix = 'Admin')
    {
        $session = Router::getRequest()->getSession();
        $sessionKey = Configure::read('BcPrefixAuth.' . $prefix . '.sessionKey');
        $user = $session->read($sessionKey);
        if (!$user) {
            if (!empty($_SESSION[$sessionKey])) {
                $user = $_SESSION[$sessionKey];
            }
        }
        return $user;
    }

    /**
     * 特権ユーザでのログイン状態か判別する
     *
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isSuperUser(): bool
    {
        $loginUser = self::loginUser();
        if (empty($loginUser)) {
            return false;
        }

        if (empty($loginUser->user_groups) || !is_array($loginUser->user_groups)) {
            return false;
        }

        foreach($loginUser->user_groups as $userGroup) {
            if (in_array($userGroup->name, Configure::read('BcApp.adminGroup'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * 代理ログイン状態か判別する
     *
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isAgentUser(): bool
    {
        $session = Router::getRequest()->getSession();
        return $session->check('AuthAgent');
    }

    /**
     * インストールモードか判定する
     * @return bool|string|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isInstallMode()
    {
        return env('INSTALL_MODE');
    }

    /**
     * バージョンを取得する
     *
     * @return bool|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getVersion($plugin = '')
    {
        $plugin = Inflector::dasherize($plugin);
        $corePlugins = Configure::read('BcApp.corePlugins');
        if (!$plugin || in_array($plugin, $corePlugins)) {
            $path = BASER . 'VERSION.txt';
        } else {
            $paths = App::path('plugins');
            $exists = false;
            foreach($paths as $path) {
                $path .= $plugin . DS . 'VERSION.txt';
                if (file_exists($path)) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                return false;
            }
        }
        $versionFile = new File($path);
        $versionData = $versionFile->read();
        $aryVersionData = explode("\n", $versionData);
        if (!empty($aryVersionData[0])) {
            return trim($aryVersionData[0]);
        } else {
            return false;
        }
    }

    /**
     * バージョンを特定する一意の数値を取得する
     * ２つ目以降のバージョン番号は３桁として結合
     * 1.5.9 => 1005009
     * ※ ２つ目以降のバージョン番号は999までとする
     * β版の場合はfalseを返す
     *
     * @param mixed $version Or false
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function verpoint($version)
    {
        $version = str_replace('baserCMS ', '', $version);
        if (preg_match("/([0-9]+)\.([0-9]+)\.([0-9]+)([\sa-z\-]+|\.[0-9]+|)([\sa-z\-]+|\.[0-9]+|)/is", $version, $maches)) {
            if (isset($maches[4]) && preg_match('/^\.[0-9]+$/', $maches[4])) {
                if (isset($maches[5]) && preg_match('/^[\sa-z\-]+$/', $maches[5])) {
                    return false;
                }
                $maches[4] = str_replace('.', '', $maches[4]);
            } elseif (isset($maches[4]) && preg_match('/^[\sa-z\-]+$/', $maches[4])) {
                return false;
            } else {
                $maches[4] = 0;
            }
            return $maches[1] * 1000000000 + $maches[2] * 1000000 + $maches[3] * 1000 + $maches[4];
        } else {
            return 0;
        }
    }

    /**
     * 管理画面用のプレフィックスを取得する
     *
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getAdminPrefix()
    {
        return Configure::read('BcPrefixAuth.Admin.alias');
    }

    /**
     * 利用可能なプラグインのリストを取得する
     *
     * ClassRegistry::removeObject('Plugin'); で一旦 Plugin オブジェクトを削除
     * エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getEnablePlugins()
    {
        $enablePlugins = [];
        if (!Configure::read('debug')) {
            $enablePlugins = Cache::read('enable_plugins', '_cake_env_');
        }
        if (!$enablePlugins) {
            // DBに接続できない場合、CakePHPのエラーメッセージが表示されてしまう為、 try を利用
            try {
                $pluginsTable = TableRegistry::getTableLocator()->get('Plugins');;   // ConnectionManager の前に呼出さないとエラーとなる
            } catch (Exception $ex) {
                return [];
            }
            $sources = ConnectionManager::get('default')->getSchemaCollection()->listTables();
            if (!is_array($sources) || in_array(strtolower('plugins'), array_map('strtolower', $sources))) {
                $plugins = $pluginsTable->find('all', ['conditions' => ['status' => true], 'order' => 'priority']);
                TableRegistry::getTableLocator()->remove('Plugin');
                if ($plugins->count()) {
                    foreach($plugins as $key => $plugin) {
                        foreach(App::path('plugins') as $path) {
                            if (is_dir($path . $plugin->name) || is_dir($path . Inflector::dasherize($plugin->name))) {
                                $enablePlugins[] = $plugin->name;
                                break;
                            }
                        }
                    }
                    if (!Configure::read('debug')) {
                        Cache::write('enable_plugins', $enablePlugins, '_cake_env_');
                    }
                }
            }
        }
        return $enablePlugins;
    }

    /**
     * プラグイン配下の Plugin クラスを読み込む
     *
     * Plugin クラスが読み込めていないとプラグイン自体を読み込めないため
     * プラグインのフォルダ名は camelize と dasherize に対応
     * 例）BcBlog / bc-blog
     *
     * @param string $pluginName
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    static public function includePluginClass($pluginName)
    {
        $pluginNames = [$pluginName, Inflector::dasherize($pluginName)];
        foreach(App::path('plugins') as $path) {
            foreach($pluginNames as $name) {
                $pluginClassPath = $path . $name . DS . 'src' . DS . 'Plugin.php';
                if (file_exists($pluginClassPath)) {
                    $loader = require ROOT . DS . 'vendor/autoload.php';
                    $loader->addPsr4($name . '\\', $path . $name . DS . 'src');
                    require_once $pluginClassPath;
                    return true;
                }
            }
        }
        return false;
    }

}
