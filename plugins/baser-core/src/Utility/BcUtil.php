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
use Cake\Filesystem\Folder;
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
        $user = isset($_SESSION[$sessionKey]) ? $session->read($sessionKey) : null;
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

    /**
     * キャッシュファイルを全て削除する
     * @return void
     * @checked
     * @unitTest
     */
    public static function clearAllCache() : void
    {
        Cache::clear('_cake_core_');
        Cache::clear('_cake_model_');
        Cache::clear('_cake_env_');
        //TODO: viewキャッシュ削除
        // clearCache();
        //TODO: dataキャッシュ削除
        // clearDataCache();
    }

    /**
     * 管理システムかチェック
     *
     * 《注意》by ryuring
     * 処理の内容にCakeRequest や、Router::parse() を使おうとしたが、
     * Router::parse() を利用すると、Routing情報が書き換えられてしまうので利用できない。
     * Router::reload() や、Router::setRequestInfo() で調整しようとしたがうまくいかなかった。
     *
     * @return boolean
     */
    public static function isAdminSystem($url = null)
    {
        if (!$url) {
            $request = Router::getRequest(true);
            if ($request) {
                $url = $request->url;
            } else {
                return false;
            }
        }
        $adminPrefix = Configure::read('Routing.prefixes.0');
        return (boolean)(preg_match('/^(|\/)' . $adminPrefix . '\//', $url) || preg_match('/^(|\/)' . $adminPrefix . '$/', $url));
    }

    /**
     * 管理ユーザーかチェック
     * @param array|null
     * @return bool
     * @checked
     * @notodo
     * @unitTest
     */
	public static function isAdminUser($user = null): bool
	{
        $User = $user ? $user : self::loginUser('Admin');
        $group_id = array_column($User->user_groups, 'id');
        return in_array(Configure::read('BcApp.adminGroupId'), $group_id);
	}

    /**
     * 現在ログインしているユーザーのユーザーグループ情報を取得する
     *
     * @param string $prefix ログイン認証プレフィックス
     * @return bool|mixed ユーザーグループ情報
     */
    public static function loginUserGroup($prefix = 'admin')
    {
        $loginUser = self::loginUser($prefix);
        if (!empty($loginUser['UserGroup'])) {
            return $loginUser['UserGroup'];
        } else {
            return false;
        }
    }

    /**
     * 認証用のキーを取得
     *
     * @param string $prefix
     * @return mixed
     */
    public static function authSessionKey($prefix = 'admin')
    {
        return Configure::read('BcAuthPrefix.' . $prefix . '.sessionKey');
    }

    /**
     * ログインしているユーザーのセッションキーを取得
     *
     * @return string
     */
    public static function getLoginUserSessionKey()
    {
        [, $sessionKey] = explode('.', BcAuthComponent::$sessionKey);
        return $sessionKey;
    }

    /**
     * ログインしているユーザー名を取得
     *
     * @return string
     */
    public static function loginUserName()
    {
        $user = self::loginUser();
        if (!empty($user['name'])) {
            return $user['name'];
        } else {
            return '';
        }
    }

    /**
     * 現在適用しているテーマ梱包プラグインのリストを取得する
     *
     * @return array プラグインリスト
     */
    public static function getCurrentThemesPlugins()
    {
        return BcUtil::getThemesPlugins(Configure::read('BcSite.theme'));
    }

    /**
     * テーマ梱包プラグインのリストを取得する
     *
     * @param string $theme テーマ名
     * @return array プラグインリスト
     */
    public static function getThemesPlugins($theme)
    {
        $path = BASER_THEMES . $theme . DS . 'Plugin';
        if (is_dir($path)) {
            $Folder = new Folder($path);
            $files = $Folder->read(true, true, false);
            if (!empty($files[0])) {
                return $files[0];
            }
        }
        return [];
    }

    /**
     * スキーマ情報のパスを取得する
     *
     * @param string $plugin プラグイン名
     * @return string Or false
     */
    public static function getSchemaPath($plugin = null)
    {

        if (!$plugin) {
            $plugin = 'Core';
        } else {
            $plugin = Inflector::camelize($plugin);
        }

        if ($plugin == 'Core') {
            return BASER_CONFIGS . 'Schema';
        }

        $paths = App::path('Plugin');
        foreach($paths as $path) {
            $_path = $path . $plugin . DS . 'Config' . DS . 'Schema';
            if (is_dir($_path)) {
                return $_path;
            }
        }

        return false;

    }

    /**
     * 初期データのパスを取得する
     *
     * 初期データのフォルダは アンダースコア区切り推奨
     *
     * @param string $plugin プラグイン名
     * @param string $theme テーマ名
     * @param string $pattern 初期データの類型
     * @return string Or false
     */
    public static function getDefaultDataPath($plugin = null, $theme = null, $pattern = null)
    {

        if (!$plugin) {
            $plugin = 'Core';
        } else {
            $plugin = Inflector::camelize($plugin);
        }

        if (!$theme) {
            $theme = 'core';
        }

        if (!$pattern) {
            $pattern = 'default';
        }

        if ($plugin == 'Core') {
            $paths = [BASER_CONFIGS . 'data' . DS . $pattern];
            if ($theme != 'core') {
                $paths = array_merge([
                    BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern,
                    BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . $pattern,
                    BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern),
                    BASER_CONFIGS . 'theme' . DS . $theme . DS . 'Config' . DS . 'data' . DS . $pattern,
                    BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default',
                    BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . 'default',
                ], $paths);
            }
        } else {
            $pluginPaths = App::path('Plugin');
            foreach($pluginPaths as $pluginPath) {
                $pluginPath .= $plugin;
                if (is_dir($pluginPath)) {
                    break;
                }
                $pluginPath = null;
            }
            if (!$pluginPath) {
                return false;
            }
            $paths = [
                $pluginPath . DS . 'Config' . DS . 'data' . DS . $pattern,
                $pluginPath . DS . 'Config' . DS . 'Data' . DS . $pattern,
                $pluginPath . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern),
                $pluginPath . DS . 'sql',
                $pluginPath . DS . 'Config' . DS . 'data' . DS . 'default',
                $pluginPath . DS . 'Config' . DS . 'Data' . DS . 'default',
            ];
            if ($theme != 'core') {
                $paths = array_merge([
                    BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern . DS . $plugin,
                    BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . $pattern . DS . $plugin,
                    BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . Inflector::camelize($pattern) . DS . $plugin,
                    BASER_CONFIGS . 'theme' . DS . $theme . DS . 'Config' . DS . 'data' . DS . $pattern . DS . $plugin,
                    BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default' . DS . $plugin,
                    BASER_THEMES . $theme . DS . 'Config' . DS . 'Data' . DS . 'default' . DS . $plugin,
                ], $paths);
            }
        }

        foreach($paths as $path) {
            if (is_dir($path)) {
                return $path;
            }
        }
        return false;

    }

    /**
     * シリアライズ
     *
     * @param mixed $value 対象文字列
     * @return string
     */
    public static function serialize($value)
    {
        return base64_encode(serialize($value));
    }

    /**
     * アンシリアライズ
     * base64_decode が前提
     *
     * @param mixed $value 対象文字列
     * @return mixed
     */
    public static function unserialize($value)
    {
        $_value = $value;
        $value = @unserialize(base64_decode($value));
        // 下位互換のため、しばらくの間、失敗した場合の再変換を行う v.3.0.2
        if ($value === false) {
            $value = unserialize($_value);
        }
        return $value;
    }

    /**
     * URL用に文字列を変換する
     *
     * できるだけ可読性を高める為、不要な記号は除外する
     *
     * @param $value
     * @return string
     */
    public static function urlencode($value)
    {
        $value = str_replace([
            ' ', '　', '	', '\\', '\'', '|', '`', '^', '"', ')', '(', '}', '{', ']', '[', ';',
            '/', '?', ':', '@', '&', '=', '+', '$', ',', '%', '<', '>', '#', '!'
        ], '_', $value);
        $value = preg_replace('/\_{2,}/', '_', $value);
        $value = preg_replace('/(^_|_$)/', '', $value);
        return urlencode($value);
    }

    /**
     * レイアウトテンプレートのリストを取得する
     *
     * @param string $path
     * @param string $plugin
     * @param string $theme
     * @return array
     */
    public static function getTemplateList($path, $plugin, $theme)
    {

        if ($plugin) {
            $templatesPathes = App::path('View', $plugin);
        } else {
            $templatesPathes = App::path('View');
            if ($theme) {
                array_unshift($templatesPathes, WWW_ROOT . 'theme' . DS . $theme . DS);
            }
        }
        $_templates = [];
        foreach($templatesPathes as $templatesPath) {
            $templatesPath .= $path . DS;
            $folder = new Folder($templatesPath);
            $files = $folder->read(true, true);
            $foler = null;
            if ($files[1]) {
                if ($_templates) {
                    $_templates = array_merge($_templates, $files[1]);
                } else {
                    $_templates = $files[1];
                }
            }
        }
        $templates = [];
        foreach($_templates as $template) {
            $ext = Configure::read('BcApp.templateExt');
            if ($template != 'installations' . $ext) {
                $template = basename($template, $ext);
                $templates[$template] = $template;
            }
        }
        return $templates;
    }

    /**
     * 全てのテーマを取得する
     * @return array
     */
    public static function getAllThemeList()
    {
        $paths = [WWW_ROOT . 'theme', BASER_VIEWS . 'Themed'];
        $themes = [];
        foreach($paths as $path) {
            $folder = new Folder($path);
            $files = $folder->read(true, true);
            if ($files[0]) {
                foreach($files[0] as $theme) {
                    if ($theme !== 'core' && $theme !== '_notes') {
                        $themes[$theme] = $theme;
                    }
                }
            }
        }
        return $themes;
    }

    /**
     * テーマリストを取得する
     *
     * @return array
     */
    public static function getThemeList()
    {
        $themes = self::getAllThemeList();
        foreach($themes as $key => $theme) {
            if (preg_match('/^admin\-/', $theme)) {
                unset($themes[$key]);
            }
        }
        return $themes;
    }

    /**
     * テーマリストを取得する
     *
     * @return array
     */
    public static function getAdminThemeList()
    {
        $themes = self::getAllThemeList();
        foreach($themes as $key => $theme) {
            if (!preg_match('/^admin\-/', $theme)) {
                unset($themes[$key]);
            }
        }
        return $themes;
    }

    /**
     * サブドメインを取得する
     *
     * @return string
     */
    public static function getSubDomain($host = null)
    {
        $currentDomain = BcUtil::getCurrentDomain();
        if (!$currentDomain && !$host) {
            return '';
        }
        if (!$host) {
            $host = $currentDomain;
        }
        if (strpos($host, '.') === false) {
            return '';
        }
        $mainHost = BcUtil::getMainDomain();
        if ($host == $mainHost) {
            return '';
        }
        if (strpos($host, $mainHost) === false) {
            return '';
        }
        $subDomain = str_replace($mainHost, '', $host);
        if ($subDomain) {
            return preg_replace('/\.$/', '', $subDomain);
        }
        return '';
    }

    /**
     * 指定したURLのドメインを取得する
     *
     * @param $url URL
     * @return string
     */
    public static function getDomain($url)
    {
        $mainUrlInfo = parse_url($url);
        $host = $mainUrlInfo['host'];
        if (!empty($mainUrlInfo['port'])) {
            $host .= ':' . $mainUrlInfo['port'];
        }
        return $host;
    }

    /**
     * メインとなるドメインを取得する
     *
     * @return string
     */
    public static function getMainDomain()
    {
        $mainDomain = Configure::read('BcEnv.mainDomain');
        if ($mainDomain) {
            return $mainDomain;
        } else {
            return BcUtil::getDomain(Configure::read('BcEnv.siteUrl'));
        }
    }

    /**
     * 現在のドメインを取得する
     *
     * @return string
     */
    public static function getCurrentDomain()
    {
        return Configure::read('BcEnv.host');
    }

    /**
     * プラグインのパスを取得する
     *
     * @param $pluginName
     * @return string|false
     */
    public static function getPluginPath($pluginName): string
    {
        $paths = App::path('plugins');
        foreach($paths as $path) {
            foreach([$pluginName, Inflector::dasherize($pluginName)] as $name) {
                if(is_dir($path . $name)) {
                    return $path . $name . DS;
                }
            }
        }
        return false;
    }
}
