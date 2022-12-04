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

namespace BaserCore\Utility;

use BaserCore\Middleware\BcAdminMiddleware;
use BaserCore\Middleware\BcFrontMiddleware;
use BaserCore\Middleware\BcRequestFilterMiddleware;
use BaserCore\Service\PluginsServiceInterface;
use Cake\Core\App;
use Cake\Cache\Cache;
use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManagerInterface;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Database\Exception;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Model\Entity\User;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Http\ServerRequestFactory;
use Cake\Datasource\ConnectionManager;
use Authentication\Authenticator\Result;
use BaserCore\Service\SiteConfigsServiceInterface;
use ReflectionClass;

/**
 * Class BcUtil
 *
 * @package Baser.Lib
 */
class BcUtil
{

    /**
     * detectors
     *
     * BcUtil::createRequest() にて
     * ServerRequest::_detectors を初期化する際に利用
     * @var array
     */
    protected static $_detectors = [
        'get' => ['env' => 'REQUEST_METHOD', 'value' => 'GET'],
        'post' => ['env' => 'REQUEST_METHOD', 'value' => 'POST'],
        'put' => ['env' => 'REQUEST_METHOD', 'value' => 'PUT'],
        'patch' => ['env' => 'REQUEST_METHOD', 'value' => 'PATCH'],
        'delete' => ['env' => 'REQUEST_METHOD', 'value' => 'DELETE'],
        'head' => ['env' => 'REQUEST_METHOD', 'value' => 'HEAD'],
        'options' => ['env' => 'REQUEST_METHOD', 'value' => 'OPTIONS'],
        'ssl' => ['env' => 'HTTPS', 'options' => [1, 'on']],
        'ajax' => ['env' => 'HTTP_X_REQUESTED_WITH', 'value' => 'XMLHttpRequest'],
        'json' => ['accept' => ['application/json'], 'param' => '_ext', 'value' => 'json'],
        'xml' => ['accept' => ['application/xml', 'text/xml'], 'param' => '_ext', 'value' => 'xml'],
    ];

    /**
     * contentsMaping
     * @var string[]
     */
    public static $contentsMaping = [
        "image/gif" => "gif",
        "image/jpeg" => "jpg",
        "image/pjpeg" => "jpg",
        "image/x-png" => "png",
        "image/jpg" => "jpg",
        "image/png" => "png",
        /* "application/pdf" => "pdf", */ // TODO windows で ai ファイルをアップロードをした場合、headerがpdfとして出力されるのでコメントアウト
        "application/pgp-signature" => "sig",
        "application/futuresplash" => "spl",
        "application/msword" => "doc",
        "application/postscript" => "ai",
        "application/x-bittorrent" => "torrent",
        "application/x-dvi" => "dvi",
        "application/x-gzip" => "gz",
        "application/x-ns-proxy-autoconfig" => "pac",
        "application/x-shockwave-flash" => "swf",
        "application/x-tgz" => "tar.gz",
        "application/x-tar" => "tar",
        "application/zip" => "zip",
        "audio/mpeg" => "mp3",
        "audio/x-mpegurl" => "m3u",
        "audio/x-ms-wma" => "wma",
        "audio/x-ms-wax" => "wax",
        "audio/x-wav" => "wav",
        "image/x-xbitmap" => "xbm",
        "image/x-xpixmap" => "xpm",
        "image/x-xwindowdump" => "xwd",
        "text/css" => "css",
        "text/html" => "html",
        "text/javascript" => "js",
        "text/plain" => "txt",
        "text/xml" => "xml",
        "video/mpeg" => "mpeg",
        "video/quicktime" => "mov",
        "video/x-msvideo" => "avi",
        "video/x-ms-asf" => "asf",
        "video/x-ms-wmv" => "wmv"
    ];

    /**
     * 認証領域を指定してログインユーザーのデータを取得する
     * セッションクラスが設定されていない場合にはスーパーグローバル変数を利用する
     *
     * @return User|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function loginUser($prefix = 'Admin')
    {
        $request = Router::getRequest();
        if (!$request) return false;

        $authenticator = $request->getAttribute('authentication');
        if (!$authenticator) return BcUtil::loginUserFromSession($prefix);

        /** @var Result $result */
        $result = $authenticator->getResult();
        if (isset($result) && $result->isValid()) {
            /* @var User $user */
            $user = $result->getData();
            if (is_null($user->user_groups)) {
                $userTable = TableRegistry::getTableLocator()->get('BaserCore.Users');
                $user = $userTable->get($user->id, ['contain' => ['UserGroups']]);
            }
            return $user;
        } else {
            return BcUtil::loginUserFromSession($prefix);
        }
    }

    /**
     * セッションからログイン情報を取得する
     * @param string $prefix
     * @return array|false|mixed|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function loginUserFromSession($prefix = 'Admin')
    {
        $request = Router::getRequest();
        $sessionKey = BcUtil::authSessionKey($prefix);
        if ($request->getSession()->check($sessionKey)) {
            return $request->getSession()->read($sessionKey);
        } else {
            return false;
        }
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
        return filter_var(env('INSTALL_MODE', true), FILTER_VALIDATE_BOOLEAN);
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
        if (!$plugin) $plugin = 'BaserCore';
        $corePlugins = Configure::read('BcApp.corePlugins');
        if (in_array($plugin, $corePlugins)) {
            $path = BASER . 'VERSION.txt';
        } else {
            $paths = App::path('plugins');
            $exists = false;
            foreach($paths as $path) {
                $path .= self::getPluginDir($plugin) . DS . 'VERSION.txt';
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
     * DBのバージョンを取得する
     *
     * @param string $plugin プラグイン名
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getDbVersion($plugin = '')
    {
        if (!$plugin || $plugin === 'BaserCore') {
            $service = BcContainer::get()->get(SiteConfigsServiceInterface::class);
        } else {
            $service = BcContainer::get()->get(PluginsServiceInterface::class);
        }
        return $service->getVersion($plugin);
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
        return Configure::read('BcApp.adminPrefix');
    }

    /**
     * baserCore用のプレフィックスを取得する
     *
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getBaserCorePrefix()
    {
        return Configure::read('BcApp.baserCorePrefix');
    }

    /**
     * プレフィックス全体を取得する
     * @param bool $regex 正規表現時にエスケープするかどうか
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getPrefix($regex = false)
    {
        $prefix = '/' . self::getBaserCorePrefix() . '/' . self::getAdminPrefix();
        return $regex? str_replace('/', '\/', substr($prefix, 1)) : $prefix;
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
        if(!BcUtil::isInstalled()) return [];
        $enablePlugins = [];
        if (!Configure::read('debug')) {
            $enablePlugins = Cache::read('enable_plugins', '_bc_env_');
        }
        if (!$enablePlugins) {
            // DBに接続できない場合、CakePHPのエラーメッセージが表示されてしまう為、 try を利用
            try {
                $pluginsTable = TableRegistry::getTableLocator()->get('BaserCore.Plugins');;   // ConnectionManager の前に呼出さないとエラーとなる
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
                                $enablePlugins[] = $plugin;
                                break;
                            }
                        }
                    }
                    if (!Configure::read('debug')) {
                        Cache::write('enable_plugins', $enablePlugins, '_bc_env_');
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
     * @param string|array $pluginName
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    static public function includePluginClass($pluginName)
    {
        if (!is_array($pluginName)) {
            $pluginName = [$pluginName];
        }
        $result = true;
        foreach($pluginName as $name) {
            $pluginPath = self::getPluginPath($name);
            if (!$pluginPath) {
                return false;
            }
            $pluginClassPath = $pluginPath . 'src' . DS . 'Plugin.php';
            if (file_exists($pluginClassPath)) {
                $loader = require ROOT . DS . 'vendor/autoload.php';
                $loader->addPsr4($name . '\\', $pluginPath . 'src');
                $loader->addPsr4($name . '\\Test\\', $pluginPath . 'tests');
                require_once $pluginClassPath;
            }
        }
        return true;
    }

    /**
     * キャッシュファイルを全て削除する
     * @return void
     * @checked
     * @unitTest
     * @note(value="viewキャッシュ／dataキャッシュ実装時に対応")
     */
    public static function clearAllCache(): void
    {
        Cache::clear('_cake_core_');
        self::clearModelCache();
        Cache::clear('_bc_env_');
        //TODO ucmitz : viewキャッシュ削除
        // clearCache();
        //TODO ucmitz : dataキャッシュ削除
        // clearDataCache();
    }

    /**
     * モデルキャッシュを削除する
     * @checked
     * @noTodo
     */
    public static function clearModelCache(): void
    {
        Cache::clear('_cake_model_');
    }

    /**
     * 管理システムかチェック
     *
     * 《注意》by ryuring
     * 処理の内容にCakeRequest や、Router::parse() を使おうとしたが、
     * Router::parse() を利用すると、Routing情報が書き換えられてしまうので利用できない。
     * Router::reload() や、Router::setRequestInfo() で調整しようとしたがうまくいかなかった。
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isAdminSystem($url = null)
    {
        if (!$url) {
            if (!$request = Router::getRequest()) {
                $request = ServerRequestFactory::fromGlobals();
            }
            if ($request) {
                $url = $request->getPath();
            } else {
                return false;
            }
        }
        $adminPrefix = BcUtil::getPrefix(true);
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
        /** @var User $User */
        $loginUser = $user ?? self::loginUser();
        return ($loginUser)? $loginUser->isAdmin() : false;
    }

    /**
     * 現在ログインしているユーザーのユーザーグループ情報を取得する
     *
     * @param string $prefix ログイン認証プレフィックス
     * @return bool|mixed ユーザーグループ情報
     * @checked
     * @notodo
     * @unitTest
     */
    public static function loginUserGroup()
    {
        $loginUser = self::loginUser();

        if (!empty($loginUser->user_groups)) {
            return $loginUser->user_groups;
        } else {
            return false;
        }
    }

    /**
     * 認証用のキーを取得
     *
     * @param string $prefix
     * @return mixed
     * @checked
     * @notodo
     * @unitTest
     */
    public static function authSessionKey($prefix = 'Admin')
    {
        return Configure::read('BcPrefixAuth.' . $prefix . '.sessionKey');
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getCurrentThemesPlugins()
    {
        return BcUtil::getThemesPlugins(BcUtil::getCurrentTheme());
    }

    /**
     * テーマ梱包プラグインのリストを取得する
     *
     * @param string $theme テーマ名
     * @return array プラグインリスト
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getThemesPlugins($theme)
    {
        $path = BcUtil::getPluginPath($theme) . 'Plugin';
        if (!file_exists($path)) return [];
        $Folder = new Folder($path);
        $files = $Folder->read(true, true, false);
        if (!empty($files[0])) {
            return $files[0];
        }
        return [];
    }

    /**
     * 初期データのパスを取得する
     *
     * @param string $plugin プラグイン名
     * @param string $theme テーマ名
     * @param string $pattern 初期データの類型
     * @return string Or false
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getDefaultDataPath($theme = null, $pattern = null)
    {
        if (!$theme) $theme = Configure::read('BcApp.defaultFrontTheme');
        if (!$pattern) $pattern = 'default';
        $paths = [
            BASER_THEMES . $theme . DS . 'config' . DS . 'data' . DS . $pattern,
            BASER_THEMES . $theme . DS . 'config' . DS . 'data' . DS . 'default',
            BASER_THEMES . Inflector::dasherize($theme) . DS . 'config' . DS . 'data' . DS . $pattern,
            BASER_THEMES . Inflector::dasherize($theme) . DS . 'config' . DS . 'data' . DS . 'default',
        ];
        foreach($paths as $path) {
            if (is_dir($path)) return $path;
        }
        return false;

    }

    /**
     * シリアライズ
     *
     * @param mixed $value 対象文字列
     * @return string
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
     * @unitTest
	 */
	public static function urlencode($value)
	{
		$value = str_replace([
			' ', '　', '	', '\\', '\'', '|', '`', '^', '"', ')', '(', '}', '{', ']', '[', ';',
			'/', '?', ':', '@', '&', '=', '+', '$', ',', '%', '<', '>', '#', '!'
		], '_', $value);
		$value = preg_replace('/_{2,}/', '_', $value);
		$value = preg_replace('/(^_|_$)/', '', $value);
		return urlencode($value);
	}

    /**
     * コンソールから実行されているかチェックする
     * $_ENV は、bootstrap にて設定
     * ユニットテストで状態を変更できる仕様とする
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isConsole()
    {
        return (bool)$_ENV['IS_CONSOLE'];
    }

    /**
     * レイアウトテンプレートのリストを取得する
     *
     * @param string $path
     * @param array|string $plugin
     * @return array
     * @checked
     * @notodo
     * @unitTest
     */
    public static function getTemplateList($path, $plugins)
    {
        if (!$plugins) return [];
        if (!is_array($plugins)) $plugins = [$plugins];

        $_templates = [];
        foreach($plugins as $plugin) {
            if (is_null($plugin)) continue;
            $templatePath = self::getTemplatePath($plugin);
            $folder = new Folder($templatePath . $path . DS);
            $files = $folder->read(true, true)[1];
            if ($files) {
                $_templates = array_merge($_templates, $files);
            }
        }
        $templates = [];
        foreach($_templates as $template) {
            if ($template != 'installations.php') {
                $template = basename($template, '.php');
                $templates[$template] = $template;
            }
        }
        return $templates;
    }

    /**
     * テンプレートのpathを返す
     *
     * @param string $plugin
     * @return string|false $templatePath
     * @checked
     * @notodo
     * @unitTest
     */
    public static function getTemplatePath(string $plugin): string
    {
        if (Plugin::isLoaded($plugin)) {
            return Plugin::path($plugin) . 'templates/';
        } else {
            return false;
        }
    }

    /**
     * 全てのテーマを取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getAllThemeList()
    {
        $themeTypes = ['Theme', 'AdminTheme'];
        $paths = [ROOT . DS . 'plugins'];
        $themes = [];
        foreach($paths as $path) {
            $folder = new Folder($path);
            $files = $folder->read(true);
            if (!$files[0]) {
                continue;
            }
            foreach($files[0] as $name) {
                $appConfigPath = BcUtil::getPluginPath($name) . 'config.php';
                if ($name === '_notes' || !file_exists($appConfigPath)) {
                    continue;
                }
                $config = include $appConfigPath;
                if (!empty($config['type']) && in_array($config['type'], $themeTypes)) {
                    $name = Inflector::camelize(Inflector::underscore($name));
                    $themes[$name] = $name;
                }
            }
        }
        return $themes;
    }

    /**
     * テーマリストを取得する
     *
     * @return array
     * @checked
     * @notodo
     * @unitTest
     */
    public static function getThemeList()
    {
        $themes = self::getAllThemeList();
        foreach($themes as $key => $theme) {
            if(!file_exists(BcUtil::getPluginPath($theme) . 'config.php')) continue;
            $config = include BcUtil::getPluginPath($theme) . 'config.php';
            if($config === false) continue;
            if ($config['type'] !== 'Theme') unset($themes[$key]);
        }
        return $themes;
    }

    /**
     * テーマリストを取得する
     *
     * @return array
     * @checked
     * @notodo
     * @unitTest
     */
    public static function getAdminThemeList()
    {
        $themes = self::getAllThemeList();
        foreach($themes as $key => $theme) {
            $config = include BcUtil::getPluginPath($theme) . 'config.php';
            if ($config['type'] !== 'AdminTheme') unset($themes[$key]);
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
        $currentDomain = self::getCurrentDomain();
        if (empty($currentDomain) && empty($host)) {
            return '';
        }
        if (empty($host)) {
            $host = $currentDomain;
        }
        if (strpos($host, '.') === false) {
            return '';
        }
        $mainHost = BcUtil::getMainDomain();
        if ($host == $mainHost) {
            return '';
        }
        if (!empty($mainHost) && strpos($host, $mainHost) === false) {
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
     * @checked
     * @notodo
     * @unitTest
     */
    public static function getDomain($url)
    {
        $mainUrlInfo = parse_url($url);
        $host = $mainUrlInfo['host'] ?? '';
        if (!empty($mainUrlInfo['port'])) {
            $host .= ':' . $mainUrlInfo['port'];
        }
        return $host;
    }

    /**
     * メインとなるドメインを取得する
     *
     * @return string
     * @checked
     * @notodo
     * @unitTest
     */
    public static function getMainDomain()
    {
        $mainDomain = Configure::read('BcEnv.mainDomain');
        return !empty($mainDomain)? $mainDomain : self::getDomain(Configure::read('BcEnv.siteUrl'));
    }

    /**
     * 現在のドメインを取得する
     *
     * @return string
     * @checked
     * @notodo
     * @unitTest
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
        $pluginDir = self::getPluginDir($pluginName);
        if ($pluginDir) {
            $paths = App::path('plugins');
            foreach($paths as $path) {
                if (is_dir($path . $pluginDir)) {
                    return $path . $pluginDir . DS;
                }
            }
        }
        return false;
    }

    /**
     * プラグインのディレクトリ名を取得する
     * @param $pluginName
     * @return false|mixed
     */
    public static function getPluginDir($pluginName)
    {
        if (!$pluginName) $pluginName = 'BaserCore';
        $pluginNames = [$pluginName, Inflector::dasherize($pluginName)];
        foreach(App::path('plugins') as $path) {
            foreach($pluginNames as $name) {
                if (is_dir($path . $name)) {
                    return $name;
                }
            }
        }
        return false;
    }

    /**
     * getContentsItem
     * コンテンツ一覧用にアイテムを整形して返す
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getContentsItem(): array
    {
        $items = Configure::read('BcContents.items');
        $createdItems = [];
        foreach($items as $name => $items) {
            foreach($items as $type => $item) {
                $item['plugin'] = $name;
                $item['type'] = $type;
                $createdItems[$type] = $item;
            }
        }
        return $createdItems;
    }


    /**
     * baserCMSのインストールが完了しているかチェックする
     * @return    boolean
     * @checked
     */
    public static function isInstalled()
    {
        return (bool) Configure::read('BcRequest.isInstalled');
    }

    /**
     * サイズの単位を変換する
     *
     * @param string $size 変換前のサイズ
     * @param string $outExt 変換後の単位
     * @param string $inExt 変換元の単位
     * @return int 変換後のサイズ
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function convertSize($size, $outExt = 'B', $inExt = null)
    {
        if (!$size) return 0;
        preg_match('/\A\d+(\.\d+)?/', $size, $num);
        $sizeNum = (isset($num[0]))? $num[0] : 0;

        $extArray = ['B', 'K', 'M', 'G', 'T'];
        $extRegex = implode('|', $extArray);
        if (empty($inExt)) {
            $inExt = (preg_match("/($extRegex)B?\z/i", $size, $ext))? strtoupper($ext[1]) : 'B';
        }
        $inExt = (preg_match("/\A($extRegex)B?\z/i", $inExt, $ext))? strtoupper($ext[1]) : 'B';
        $outExt = (preg_match("/\A($extRegex)B?\z/i", $outExt, $ext))? strtoupper($ext[1]) : 'B';

        $index = array_search($inExt, $extArray) - array_search($outExt, $extArray);

        $outSize = pow(1024, $index) * $sizeNum;
        return $outSize;
    }

    /**
     * 送信されたPOSTがpost_max_sizeを超えているかチェックする
     *
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isOverPostSize()
    {
        if (empty($_POST) &&
            env('REQUEST_METHOD') === 'POST' &&
            env('CONTENT_LENGTH') > self::convertSize(ini_get('post_max_size'))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * サイトのトップレベルのURLを取得する
     *
     * @param boolean $lastSlash
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function topLevelUrl($lastSlash = true)
    {
        if (self::isConsole() && !Configure::check('BcEnv.host')) {
            return Configure::read('App.fullBaseUrl');
        }
        $request = Router::getRequest();
        $protocol = 'http://';
        if (!empty($request) && $request->is('ssl')) {
            $protocol = 'https://';
        }
        $host = Configure::read('BcEnv.host');
        $url = $protocol . $host;
        if ($lastSlash) {
            $url .= '/';
        }
        return $url;
    }

    /**
     * 現在のビューディレクトリのパスを取得する
     *
     * @return string
     */
    public static function getViewPath()
    {
        if (BcUtil::isAdminSystem()) {
            $theme = BcUtil::getCurrentAdminTheme();
        } else {
            $theme = BcUtil::getCurrentTheme();
        }
        $pluginPath = ROOT . DS . 'plugins' . DS;
        if (is_dir($pluginPath . $theme)) {
            return $pluginPath . $theme . DS;
        } elseif (is_dir($pluginPath . Inflector::dasherize($theme))) {
            return $pluginPath . Inflector::dasherize($theme) . DS;
        }
        return false;
    }

    /**
     * 現在のテーマ名を取得する
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getCurrentTheme()
    {
        $theme = Inflector::camelize(Inflector::underscore(Configure::read('BcApp.defaultFrontTheme')));
        if(!BcUtil::isInstalled()) return $theme;
        $request = Router::getRequest();
        if (BcUtil::isAdminSystem()) {
            $site = $request->getAttribute('currentSite');
        } else {
            $site = $request->getAttribute('currentSite');
        }
        if (!$site) {
            return self::getRootTheme();
        } elseif($site->theme) {
            return $site->theme;
        } else {
            return $theme;
        }
    }

    /**
     * ルートとなるサイトのテーマを取得する
     * @return mixed
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getRootTheme()
    {
        $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->getRootMain();
        return (isset($site->theme))? $site->theme : null;
    }

    /**
     * 現在の管理画面のテーマ名を取得する
     * キャメルケースが前提
     * @return mixed|string
     */
    public static function getCurrentAdminTheme()
    {
        $adminTheme = Inflector::camelize(Inflector::underscore(Configure::read('BcApp.defaultAdminTheme')));
        if (BcUtil::isInstalled() && !empty(BcSiteConfig::get('admin_theme'))) {
            $adminTheme = BcSiteConfig::get('admin_theme');
        }
        return $adminTheme;
    }

    /**
     * 日本語ファイル名対応版basename
     *
     * @param string $str
     * @param string $suffix
     * @return type
     */
    public static function mb_basename($str, $suffix = null)
    {
        $tmp = preg_split('/[\/\\\\]/', $str);
        $res = end($tmp);
        if ($suffix && strlen($suffix)) {
            $suffix = preg_quote($suffix);
            $res = preg_replace("/({$suffix})$/u", "", $res);
        }
        return $res;
    }

    /**
     * コンテンツタイプから拡張子を取得する
     * @param string mimeタイプ
     * @return string 拡張子
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function decodeContent($content, $fileName = null)
    {
        if (isset(self::$contentsMaping[$content])) {
            return self::$contentsMaping[$content];
        } elseif ($fileName) {
            return self::getExtension($fileName);
        } else {
            return false;
        }
    }

    /**
     * ファイル名よりContent-Type を取得する
     * @param string $fileName
     * @return false|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getContentType($fileName)
    {
        $extension = self::getExtension($fileName);
        if(!$extension) return false;
        return array_search($extension, self::$contentsMaping);
    }

    /**
     * ファイル名より拡張子を取得する
     * @param $fileName
     * @return false|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getExtension($fileName)
    {
        $info = pathinfo($fileName);
        if (empty($info['extension'])) return false;
        return $info['extension'];
    }

    /**
     * サイトの設置URLを取得する
     *
     * index.phpは含まない
     *
     * @return    string
     */
    public static function siteUrl()
    {
        $baseUrl = preg_replace('/' . preg_quote(basename($_SERVER['SCRIPT_FILENAME']), '/') . '\/$/', '', BcUtil::baseUrl());
        $topLevelUrl = BcUtil::topLevelUrl(false);
        if ($topLevelUrl) {
            return $topLevelUrl . $baseUrl;
        } else {
            return '';
        }
    }

    /**
     * WebサイトのベースとなるURLを取得する
     *
     * コントローラーが初期化される前など {$this->base} が利用できない場合に利用する
     * / | /index.php/ | /subdir/ | /subdir/index.php/
     *
     * ※ プログラムフォルダ内の画像やCSSの読み込み時もbootstrap.php で呼び出されるのでサーバーキャッシュは利用しない
     *
     * @return string ベースURL
     */
    public static function baseUrl()
    {
        $baseUrl = Configure::read('App.baseUrl');
        if ($baseUrl) {
            if (!preg_match('/\/$/', $baseUrl)) {
                $baseUrl .= '/';
            }
        } else {
            $script = $_SERVER['SCRIPT_FILENAME'];
            $script = str_replace(['\\', '/'], DS, $script);
            $docroot = BcUtil::docRoot();
            $script = str_replace($docroot, '', $script);
            $baseUrl = preg_replace('/' . preg_quote('webroot' . DS . 'index.php', '/') . '/', '', $script);
            $baseUrl = preg_replace('/' . preg_quote('webroot' . DS . 'test.php', '/') . '/', '', $baseUrl);
            // ↓ Windows Azure 対策 SCRIPT_FILENAMEに期待した値が入ってこない為
            $baseUrl = preg_replace('/index\.php/', '', $baseUrl);
            $baseUrl = preg_replace("/index$/", '', $baseUrl);
        }

        $baseUrl = str_replace(DS, '/', $baseUrl);
        if (!$baseUrl) {
            $baseUrl = '/';
        }
        return $baseUrl;
    }

    /**
     * ドキュメントルートを取得する
     *
     * サブドメインの場合など、$_SERVER['DOCUMENT_ROOT'] が正常に取得できない場合に利用する
     * UserDir に対応
     *
     * @return string   ドキュメントルートの絶対パス
     */
    public static function docRoot()
    {
        if (empty($_SERVER['SCRIPT_NAME'])) {
            return '';
        }
        if (strpos($_SERVER['SCRIPT_NAME'], '.php') === false) {
            // さくらの場合、/index を呼びだすと、拡張子が付加されない
            $scriptName = $_SERVER['SCRIPT_NAME'] . '.php';
        } else {
            $scriptName = $_SERVER['SCRIPT_NAME'];
        }
        $path = explode('/', $scriptName);
        krsort($path);
        // WINDOWS環境の場合、SCRIPT_NAMEのDIRECTORY_SEPARATORがスラッシュの場合があるので
        // スラッシュに一旦置換してスラッシュベースで解析
        $docRoot = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
        foreach($path as $value) {
            $reg = "/\/" . $value . "$/";
            $docRoot = preg_replace($reg, '', $docRoot);
        }
        return str_replace('/', DS, $docRoot);
    }

    /**
     * 実行環境のOSがWindowsであるかどうかを返す
     *
     * @return bool
     */
    public static function isWindows()
    {
        return DIRECTORY_SEPARATOR == '\\';
    }

    /**
     * URLにセッションIDを付加する
     * 既に付加されている場合は重複しない
     *
     * @param mixed $url
     * @return mixed
     */
    public static function addSessionId($url, $force = false)
    {
        if (BcUtil::isAdminSystem()) {
            return $url;
        }
        $sessionId = session_id();
        if (!$sessionId) {
            return $url;
        }

        $site = null;
        if (!Configure::read('BcRequest.isUpdater')) {
            $currentUrl = \Cake\Routing\Router::getRequest()->getPath();
            $sites = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
            $site = $sites->findByUrl($currentUrl);
        }
        // use_trans_sid が有効になっている場合、２重で付加されてしまう
        if ($site && $site->device == 'mobile' && Configure::read('BcAgent.mobile.sessionId') && (!ini_get('session.use_trans_sid') || $force)) {
            if (is_array($url)) {
                $url["?"][session_name()] = $sessionId;
            } else {
                if (strpos($url, '?') !== false) {
                    $args = [];
                    $_url = explode('?', $url);
                    if (!empty($_url[1])) {
                        if (strpos($_url[1], '&') !== false) {
                            $aryUrl = explode('&', $_url[1]);
                            foreach($aryUrl as $pass) {
                                if (strpos($pass, '=') !== false) {
                                    [$key, $value] = explode('=', $pass);
                                    $args[$key] = $value;
                                }
                            }
                        } else {
                            if (strpos($_url[1], '=') !== false) {
                                [$key, $value] = explode('=', $_url[1]);
                                $args[$key] = $value;
                            }
                        }
                    }
                    $args[session_name()] = $sessionId;
                    $pass = '';
                    foreach($args as $key => $value) {
                        if ($pass) {
                            $pass .= '&';
                        }
                        $pass .= $key . '=' . $value;
                    }
                    $url = $_url[0] . '?' . $pass;
                } else {
                    $url .= '?' . session_name() . '=' . $sessionId;
                }
            }
        }
        return $url;
    }

    /**
     * フォルダの中をフォルダを残して空にする(ファイルのみを削除する)
     *
     * @param string $path
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function emptyFolder($path)
    {
        $result = true;
        $Folder = new Folder($path);
        $files = $Folder->read(true, true, true);
        if (is_array($files[1])) {
            foreach($files[1] as $file) {
                if ($file != 'empty') {
                    if (!@unlink($file)) {
                        $result = false;
                    }
                }
            }
        }
        if (is_array($files[0])) {
            foreach($files[0] as $file) {
                if (!BcUtil::emptyFolder($file)) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * ファイルポインタから行を取得し、CSVフィールドを処理する
     *
     * @param stream    handle
     * @param int        length
     * @param string    delimiter
     * @param string    enclosure
     * @return    mixed    ファイルの終端に達した場合を含み、エラー時にFALSEを返します。
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function fgetcsvReg(&$handle, $length = null, $d = ',', $e = '"')
    {
        $d = preg_quote($d);
        $e = preg_quote($e);
        $_line = "";
        $eof = false;
        while(($eof != true) and (!feof($handle))) {
            $_line .= (empty($length)? fgets($handle) : fgets($handle, $length));
            $itemcnt = preg_match_all('/' . $e . '/', $_line, $dummy);
            if ($itemcnt % 2 == 0)
                $eof = true;
        }
        $_csv_line = preg_replace('/(?:\r\n|[\r\n])?$/', $d, trim($_line));
        $_csv_pattern = '/(' . $e . '[^' . $e . ']*(?:' . $e . $e . '[^' . $e . ']*)*' . $e . '|[^' . $d . ']*)' . $d . '/';
        preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
        $_csv_data = $_csv_matches[1];
        for($_csv_i = 0; $_csv_i < count($_csv_data); $_csv_i++) {
            $_csv_data[$_csv_i] = preg_replace('/^' . $e . '(.*)' . $e . '$/s', '$1', $_csv_data[$_csv_i]);
            $_csv_data[$_csv_i] = str_replace($e . $e, $e, $_csv_data[$_csv_i]);
        }
        return empty($_line)? false : $_csv_data;
    }

    /**
     * オベントをオフにする
     * @param EventManagerInterface $eventManager
     * @param string $eventKey
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function offEvent(EventManagerInterface $eventManager, string $eventKey)
    {
        $eventListeners = $eventManager->listeners($eventKey);
        if ($eventListeners) {
            foreach($eventListeners as $eventListener) {
                $eventManager->off($eventKey, $eventListener['callable']);
            }
        }
        return $eventListeners;
    }

    /**
     * イベントをオンにする
     * @param EventManagerInterface $eventManager
     * @param string $eventKey
     * @param EventListenerInterface[] $eventListeners
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function onEvent(EventManagerInterface $eventManager, string $eventKey, array $eventListeners)
    {
        if ($eventListeners) {
            foreach($eventListeners as $eventListener) {
                $eventManager->on($eventKey, $eventListener['callable']);
            }
        }
    }


    /**
     * Request を取得する
     *
     * @param string $url
     * @return ServerRequest
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function createRequest($url = '/', $data = [], $method = 'GET', $config = [])
    {
        $config = array_merge([
            'ajax' => false,
            'webroot' => '/',
            'method' => 'GET'
        ], $config);

        $isAjax = (!empty($config['ajax']))? true : false;
        unset($config['ajax']);
        if (preg_match('/^http/', $url)) {
            $parseUrl = parse_url($url);
            Configure::write('BcEnv.host', $parseUrl['host']);
            $query = strpos($url, '?') !== false? explode('?', $url)[1] : '';
            $queryParameters = [];
            if($query) parse_str($query, $queryParameters);
            $defaultConfig = [
                'uri' => ServerRequestFactory::createUri([
                    'HTTP_HOST' => $parseUrl['host'],
                    'REQUEST_URI' => $url,
                    'HTTPS' => (preg_match('/^https/', $url))? 'on' : '',
                    'QUERY_STRING' => $query
                ]),
                'query' => $queryParameters,
                'environment' => [
                    'REQUEST_METHOD' => $method
                ]];
        } else {
            $defaultConfig = [
                'url' => $url,
                'environment' => [
                    'REQUEST_METHOD' => $method
                ]];
        }
        $defaultConfig = array_merge($defaultConfig, $config);
        $request = new ServerRequest($defaultConfig);

        try {
            Router::setRequest($request);
            $params = Router::parseRequest($request);
        } catch (\Exception $e) {
            return $request;
        }

        if(!empty($params['?'])) {
            $request = $request->withQueryParams($params['?']);
            unset($params['?']);
        }
        $request = $request->withAttribute('params', $params);
        if ($request->getParam('prefix') === 'Admin') {
            $bcAdmin = new BcAdminMiddleware();
            $request = $bcAdmin->setCurrentSite($request);
        } else {
            $bcAdmin = new BcFrontMiddleware();
            $request = $bcAdmin->setCurrent($request);
        }
        if ($data) {
            $request = $request->withParsedBody($data);
        }
        $request = $request->withEnv('HTTPS', (preg_match('/^https/', $url))? 'on' : '');
        if ($isAjax) {
            $request = $request->withEnv('HTTP_X_REQUESTED_WITH', 'XMLHttpRequest');
        }
        // ServerRequest::_detectors を初期化
        // static プロパティで値が残ってしまうため
        $ref = new ReflectionClass($request);
        $detectors = $ref->getProperty('_detectors');
        $detectors->setAccessible(true);
        $detectors->setValue(self::$_detectors);
        $bcRequestFilter = new BcRequestFilterMiddleware();
        $request = $bcRequestFilter->addDetectors($request);
        return $request;
    }

    /**
     * 必要な一時フォルダが存在するかチェックし、
     * なければ生成する
     */
    public static function checkTmpFolders()
    {
        if (!is_writable(TMP)) {
            return;
        }
        $folder = new Folder();
        $folder->create(TMP . 'sessions', 0777);
        $folder->create(CACHE, 0777);
        $folder->create(CACHE . 'models', 0777);
        $folder->create(CACHE . 'persistent', 0777);
        $folder->create(CACHE . 'environment', 0777);
    }

    /**
     * プラグインの namespace を書き換える
     * @param $newPlugin
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function changePluginNameSpace($newPlugin)
    {
        $pluginPath = BcUtil::getPluginPath($newPlugin);
        if(!$pluginPath) return false;
        $file = new File($pluginPath . 'src' . DS . 'Plugin.php');
        $data = $file->read();
        $file->write(preg_replace('/namespace .+?;/', 'namespace ' . $newPlugin . ';', $data));
        $file->close();
        return true;
    }


    /**
     * httpからのフルURLを取得する
     *
     * @param mixed $url
     * @return    string
     */
    public static function fullUrl($url)
    {
        $url = Router::url($url);
        return self::topLevelUrl(false) . $url;
    }

    /**
     * 現在の処理がCakePHPのマイグレーションコマンドかどうか
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isMigrations()
    {
        if(self::isConsole() && isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] === 'migrations') {
            return true;
        }
        return false;
    }

}
