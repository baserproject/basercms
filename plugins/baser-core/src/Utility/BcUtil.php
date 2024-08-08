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
use BaserCore\Model\Entity\Site;
use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Service\SitesService;
use BaserCore\Service\SitesServiceInterface;
use Cake\Core\App;
use Cake\Cache\Cache;
use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Database\Exception\MissingConnectionException;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManagerInterface;
use Cake\Http\ServerRequest;
use Cake\Http\UriFactory;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Database\Exception;
use BaserCore\Annotation\Note;
use BaserCore\Annotation\NoTodo;
use BaserCore\Model\Entity\User;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\Http\ServerRequestFactory;
use Authentication\Authenticator\Result;
use BaserCore\Service\SiteConfigsServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

/**
 * Class BcUtil
 *
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
        'https' => ['env' => 'HTTPS', 'options' => [1, 'on']],
        'ajax' => ['env' => 'HTTP_X_REQUESTED_WITH', 'value' => 'XMLHttpRequest'],
        'json' => ['accept' => ['application/json'], 'param' => '_ext', 'value' => 'json'],
        'xml' => [
            'accept' => ['application/xml', 'text/xml'],
            'exclude' => ['text/html'],
            'param' => '_ext',
            'value' => 'xml',
        ],
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
     *
     * - 第一優先：authenticationから取得
     *  - モデルが BaserCore.Users の場合、ユーザーグループがなかったら取得する
     * - 第二優先：現在のリクエストに紐づくセッションから取得
     * - 第三優先：上記で取得できない場合、プレフィックスが Front だった場合に、
     *      他の領域のログインセッションより取得する。
     *      複数のログインセッションにログインしている場合は定義順の降順で最初のログイン情報を取得
     *
     * $prefix を指定したとしても authentication より取得できた場合はそちらを優先する
     *
     * @return User|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function loginUser()
    {
        $request = Router::getRequest();
        if (!$request) return false;

        $prefix = BcUtil::getRequestPrefix($request);

        $authenticator = $request->getAttribute('authentication');
        if ($authenticator) {
            /** @var Result $result */
            $result = $authenticator->getResult();
            if (!empty($result) && $result->isValid()) {
                /* @var User $user */
                $user = $result->getData();
                if (is_null($user->user_groups)) {
                    $userModel = Configure::read("BcPrefixAuth.{$prefix}.userModel");
                    if ($userModel === 'BaserCore.Users') {
                        $userTable = TableRegistry::getTableLocator()->get('BaserCore.Users');
                        $user = $userTable->get($user->id, contain: ['UserGroups']);
                    }
                }
                return $user;
            }
        }

        $user = false;
        if ($prefix === 'Front') {
            $user = BcUtil::loginUserFromSession($prefix);
            if (!$user) {
                $users = self::getLoggedInUsers(false);
                if (!empty($users[0])) $user = $users[0];
            }
        }
        return $user;
    }

    /**
     * ログイン済のユーザー情報をログイン領域ごとに取得する
     *
     * @param bool $assoc ログイン領域のプレフィックスをキーとして連想配列で取得するかどうか
     *                      false の場合は、通常の配列として取得する
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getLoggedInUsers($assoc = true)
    {
        $users = [];
        $prefixSettings = array_reverse(Configure::read('BcPrefixAuth'));
        foreach($prefixSettings as $key => $prefixSetting) {
            if (!empty($prefixSetting['disabled'])) continue;
            $user = BcUtil::loginUserFromSession($key);
            if ($user) {
                if ($assoc) {
                    $users[$key] = $user;
                } else {
                    $users[] = $user;
                }
            }
        }
        return $users;
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
        if ($sessionKey && $request->getSession()->check($sessionKey)) {
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
    public static function isSuperUser($user = null): bool
    {
        /** @var User $User */
        $loginUser = $user ?? self::loginUser();
        return ($loginUser)? $loginUser->isSuper() : false;
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
     * @param string $plugin プラグイン名
     * @param bool $isUpdateTmp アップデート時の一時ファイルが対象かどうか
     * @return false|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getVersion(string $plugin = '', bool $isUpdateTmp = false): string|false
    {
        if (!$plugin) $plugin = 'BaserCore';
        $corePlugins = Configure::read('BcApp.corePlugins');
        $updateTmpDir = TMP . 'update';
        $pluginTmpDir = $updateTmpDir . DS . 'vendor' . DS . 'baserproject';

        if (in_array($plugin, $corePlugins)) {
            $path = BASER . 'VERSION.txt';
            if($isUpdateTmp) {
                if (preg_match('/^' . preg_quote(ROOT . DS . 'plugins' . DS, '/') . '/', $path)) {
                    $path = str_replace(ROOT . DS . 'plugins', $pluginTmpDir, $path);
                } else {
                    $path = str_replace(ROOT, $updateTmpDir, $path);
                }
            }
            if (!file_exists($path)) {
                return false;
            }
        } else {
            if($isUpdateTmp) {
                $paths = [$pluginTmpDir . DS];
            } else {
                $paths = App::path('plugins');
            }
            $exists = false;
            foreach($paths as $path) {
                $path .= self::getPluginDir($plugin, $isUpdateTmp) . DS . 'VERSION.txt';
                if (file_exists($path)) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                return false;
            }
        }
        $versionFile = new BcFile($path);
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
     * @param int|false $version
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
    public static function getEnablePlugins($force = false)
    {
        if (!BcUtil::isInstalled() && !$force) return [];
        $enablePlugins = [];
        if (!Configure::read('debug') && !$force) {
            $enablePlugins = Cache::read('enable_plugins', '_bc_env_');
        }
        if (!$enablePlugins) {
            $enablePlugins = [];
            $pluginsTable = TableRegistry::getTableLocator()->get('BaserCore.Plugins');;   // ConnectionManager の前に呼出さないとエラーとなる
            $prefix = self::getCurrentDbConfig()['prefix'];
            // DBに接続できない場合、CakePHPのエラーメッセージが表示されてしまう為、 try を利用
            try {
                $sources = self::getCurrentDb()->getSchemaCollection()->listTables();
            } catch (MissingConnectionException) {
                return [];
            }
            if (!is_array($sources) || in_array($prefix . strtolower('plugins'), array_map('strtolower', $sources))) {
                $plugins = $pluginsTable->find('all', conditions: ['status' => true], order: 'priority');
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
     * 現在のDB接続の設定を取得する
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getCurrentDbConfig()
    {
        return self::getCurrentDb()->config();
    }

    /**
     * 現在のDB接続を取得する
     *
     * @return \Cake\Database\Connection
     * @checked
     * @noTodo
     */
    public static function getCurrentDb()
    {
        return TableRegistry::getTableLocator()->get('BaserCore.App')->getConnection();
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
            $name = Inflector::camelize($name, '-');
            if(file_exists($pluginPath . 'src' . DS . 'Plugin.php')) {
                $pluginClassPath = $pluginPath . 'src' . DS . 'Plugin.php';
            } elseif(file_exists($pluginPath . 'src' . DS . $name . 'Plugin.php')) {
                $pluginClassPath = $pluginPath . 'src' . DS . $name . 'Plugin.php';
            } else {
                return false;
            }
            $loader = require ROOT . DS . 'vendor/autoload.php';
            $loader->addPsr4($name . '\\', $pluginPath . 'src');
            $loader->addPsr4($name . '\\Test\\', $pluginPath . 'tests');
            require_once $pluginClassPath;
        }
        return true;
    }

    /**
     * キャッシュファイルを全て削除する
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public static function clearAllCache(): void
    {
        Cache::clear('_cake_core_');
        self::clearModelCache();
        Cache::clear('_bc_env_');
        Cache::clear('_bc_update_');
        Cache::clear('_bc_gmaps_');
    }

    /**
     * モデルキャッシュを削除する
     *
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
     * @noTodo
     * @checked
     * @unitTest
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
        $path = BcUtil::getPluginPath($theme) . 'plugins';
        if (!file_exists($path)) return [];
        $Folder = new BcFolder($path);
        $files = $Folder->getFolders();
        if (!empty($files)) {
            return $files;
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
        if (!$theme) $theme = Configure::read('BcApp.coreFrontTheme');
        if (!$pattern) $pattern = 'default';
        $base = Plugin::path($theme);
        $paths = [
            $base . 'config' . DS . 'data' . DS . $pattern,
            $base . $theme . DS . 'config' . DS . 'data' . DS . 'default',
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function unserialize($value)
    {
        $_value = $value;
        $value = @unserialize(base64_decode($value));
        // 下位互換のため、しばらくの間、失敗した場合の再変換を行う v.3.0.2
        if ($value === false) {
            $value = @unserialize($_value);
            if ($value === false) {
                return '';
            }
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
     * ユニットテストかどうか
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isTest()
    {
        return (!empty($_SERVER['argv'][0]) &&
            preg_match('/vendor\/bin\/phpunit$/', $_SERVER['argv'][0]));
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

        $templates = [];
        foreach($plugins as $plugin) {
            if (is_null($plugin)) continue;
            $templatePaths = [
                self::getTemplatePath($plugin),
                self::getTemplatePath(Inflector::camelize(Configure::read('BcApp.coreAdminTheme'), '-')) . 'plugin' . DS . $plugin . DS
            ];
            foreach($templatePaths as $templatePath) {
                $folder = new BcFolder($templatePath . $path . DS);
                $files = $folder->getFiles();
                if ($files) {
                    $templates = array_merge($templates, $files);
                }
            }
        }
        foreach($templates as $key => $template) {
            if ($template === 'installations.php') continue;
            $template = basename($template, '.php');
            unset($templates[$key]);
            $templates[$template] = $template;
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
            return Plugin::templatePath($plugin);
        } else {
            return false;
        }
    }

    /**
     * フロントのテンプレートのパス一覧を取得する
     *
     * @param $siteId
     * @return []|array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getFrontTemplatePaths($siteId, $plugin)
    {
        /* @var SitesService $sitesService */
        $sitesTable = TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sitesTable->get($siteId);

        $themes = $site->theme? [$site->theme] : [];
        $rootTheme = BcUtil::getRootTheme();
        if (!$themes || $rootTheme !== $themes[0]) {
            $themes[] = $rootTheme;
        }
        $defaultTheme = Configure::read('BcApp.coreFrontTheme');
        if (!in_array($defaultTheme, $themes)) $themes[] = $defaultTheme;

        $templatesPaths = [];
        foreach($themes as $theme) {
            $themeTemplatesPaths = App::path('templates', $theme);
            $templatesPaths = array_merge($templatesPaths, [
                $themeTemplatesPaths[0],
                $themeTemplatesPaths[0] . 'plugin' . DS . $plugin . DS,
            ]);
        }
        $templatesPaths[] = App::path('templates', $plugin)[0];
        return $templatesPaths;
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
            $Folder = new BcFolder($path);
            $folders = $Folder->getFolders();
            if (!$folders) {
                continue;
            }
            foreach($folders as $name) {
                $appConfigPath = BcUtil::getPluginPath($name) . 'config.php';
                if ($name === '_notes' || !file_exists($appConfigPath)) {
                    continue;
                }
                $config = include $appConfigPath;
                if (!empty($config['type'])) {
                    if (!is_array($config['type'])) $config['type'] = [$config['type']];
                    $isTheme = false;
                    foreach($config['type'] as $type) {
                        if (in_array($type, $themeTypes)) $isTheme = true;
                    }
                    if (!$isTheme) continue;
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
            if (!file_exists(BcUtil::getPluginPath($theme) . 'config.php')) continue;
            $config = include BcUtil::getPluginPath($theme) . 'config.php';
            if ($config === false) continue;
            if (!is_array($config['type'])) $config['type'] = [$config['type']];
            if (!in_array('Theme', $config['type'])) unset($themes[$key]);
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
            if (!is_array($config['type'])) $config['type'] = [$config['type']];
            if (!in_array('AdminTheme', $config['type'])) unset($themes[$key]);
        }
        return $themes;
    }

    /**
     * サブドメインを取得する
     *
     * @return string
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
     */
    public static function getPluginPath(string $pluginName, bool $isUpdateTmp = false): string|false
    {
        $pluginDir = self::getPluginDir($pluginName, $isUpdateTmp);
        if($isUpdateTmp) {
            return TMP . 'update' . DS . 'vendor' . DS . 'baserproject' . DS . $pluginDir . DS;
        }
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
     * @param string $pluginName
     * @param bool $isUpdateTmp
     * @return false|string
     * @checked
     * @noTodo
     */
    public static function getPluginDir(string $pluginName, bool $isUpdateTmp = false): string|false
    {
        if (!$pluginName) $pluginName = 'BaserCore';
        $pluginNames = [$pluginName, Inflector::dasherize($pluginName)];
        if($isUpdateTmp) {
            $paths = [TMP . 'update' . DS . 'vendor' . DS . 'baserproject' . DS];
        } else {
            $paths = App::path('plugins');
        }
        foreach($paths as $path) {
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
     * @noTodo
     * @unitTest
     */
    public static function isInstalled()
    {
        return (bool)Configure::read('BcEnv.isInstalled');
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
        if (!empty($request) && $request->is('https')) {
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
     * @checked
     * @noTodo
     * @unitTest
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
        $theme = Inflector::camelize(Inflector::underscore(Configure::read('BcApp.coreFrontTheme')));
        if (!BcUtil::isInstalled()) return $theme;
        $request = Router::getRequest();

        if (is_null($request))
            $site = null;
        else {
            /** @var Site $site */
            $site = $request->getAttribute('currentSite');
        }

        if ($site) {
            if ($site->theme) {
                return $site->theme;
            } else {
                $sitesService = BcContainer::get()->get(SitesServiceInterface::class);
                try {
                    $site = $sitesService->get($site->main_site_id);
                    return $site->theme;
                } catch (MissingConnectionException) {
                    return $theme;
                }
            }
        } elseif (self::getRootTheme()) {
            return self::getRootTheme();
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
        try {
            $site = $sites->getRootMain();
            return (isset($site->theme))? $site->theme : null;
        } catch (MissingConnectionException) {
            return null;
        }
    }

    /**
     * 現在の管理画面のテーマ名を取得する
     * キャメルケースが前提
     * @return mixed|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getCurrentAdminTheme()
    {
        $adminTheme = Inflector::camelize(Inflector::underscore(Configure::read('BcApp.coreAdminTheme')));
        if (BcUtil::isInstalled()) {
            try {
                $siteConfigAdminTheme = BcSiteConfig::get('admin_theme');
                if($siteConfigAdminTheme) return $siteConfigAdminTheme;
            } catch (MissingConnectionException) {
                return $adminTheme;
            }
        }
        return $adminTheme;
    }

    /**
     * 日本語ファイル名対応版basename
     *
     * @param string $str
     * @param string $suffix
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function mbBasename($str, $suffix = null)
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
        if (!$extension) return false;
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
     * @return string
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
     * @unitTest
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
     * @return string ドキュメントルートの絶対パス
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
     * @unitTest
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
     * @checked
     * @noTodo
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

        $currentUrl = \Cake\Routing\Router::getRequest()->getPath();
        $sites = \Cake\ORM\TableRegistry::getTableLocator()->get('BaserCore.Sites');
        $site = $sites->findByUrl($currentUrl);

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
        $Folder = new BcFolder($path);
        $files = $Folder->getFiles(['full'=>true]);
        if (is_array($files)) {
            foreach($files as $file) {
                if ($file != 'empty') {
                    if (!@unlink($file)) {
                        $result = false;
                    }
                }
            }
        }
        $folders = $Folder->getFolders(['full'=>true]);
        if (is_array($folders)) {
            foreach($folders as $folder) {
                if (!BcUtil::emptyFolder($folder)) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * ファイルポインタから行を取得し、CSVフィールドを処理する
     *
     * @param resource $handle
     * @param int $length
     * @param string $d delimiter
     * @param string $e enclosure
     * @return mixed ファイルの終端に達した場合を含み、エラー時にFALSEを返します。
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
     *
     * グローバルイベントマネージャーからも削除する
     * @param EventManagerInterface $eventManager
     * @param string $eventKey
     * @return array
     * @checked
     * @noTodo
     */
    public static function offEvent(EventManagerInterface $eventManager, string $eventKey)
    {
        $eventListeners = $eventManager->listeners($eventKey);
        $globalEventManager = $eventManager->instance();
        if ($eventListeners) {
            foreach($eventListeners as $eventListener) {
                $eventManager->off($eventKey, $eventListener['callable']);
                $globalEventManager->off($eventKey, $eventListener['callable']);
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
     * @return ServerRequestInterface
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
            if ($query) parse_str($query, $queryParameters);
            $defaultConfig = [
                'uri' => UriFactory::marshalUriAndBaseFromSapi([
                    'HTTP_HOST' => $parseUrl['host'],
                    'REQUEST_URI' => $url,
                    'HTTPS' => (preg_match('/^https/', $url))? 'on' : '',
                    'QUERY_STRING' => $query
                ])['uri'],
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

        $params = [];
        try {
            Router::setRequest($request);
            $params = Router::parseRequest($request);
        } catch (MissingRouteException) {
        } catch (\Throwable $e) {
            throw $e;
        }

        if (!empty($params['?'])) {
            $request = $request->withQueryParams($params['?']);
            unset($params['?']);
        }
        $request = $request->withAttribute('params', $params);
        if ($request->getParam('prefix') === 'Admin') {
            $bcAdmin = new BcAdminMiddleware();
            $request = $bcAdmin->setCurrentSite($request);
        } elseif($params) {
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function checkTmpFolders()
    {
        if (!is_writable(TMP)) {
            return;
        }
        (new BcFolder(TMP . 'sessions'))->create();
        (new BcFolder(CACHE))->create();
        (new BcFolder(CACHE . 'models'))->create();
        (new BcFolder(CACHE . 'persistent'))->create();
        (new BcFolder(CACHE . 'environment'))->create();
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
        if (!$pluginPath) return false;
        if(file_exists($pluginPath . 'src' . DS . 'Plugin.php')) {
            $pluginClassPath = $pluginPath . 'src' . DS . 'Plugin.php';
        } elseif(file_exists($pluginPath . 'src' . DS . $newPlugin . 'Plugin.php')) {
            $pluginClassPath = $pluginPath . 'src' . DS . $newPlugin . 'Plugin.php';
        } else {
            return false;
        }
        $file = new BcFile($pluginClassPath);
        $data = $file->read();
        $file->write(preg_replace('/namespace .+?;/', 'namespace ' . $newPlugin . ';', $data));
        return true;
    }

    /**
     * Plugin クラスのクラス名を変更する
     *
     * 古い形式の場合は新しい形式に変更する
     * `Plugin` -> `{PluginName}Plugin`
     * @param string $oldPlugin
     * @param string $newPlugin
     * @return bool
     */
    public static function changePluginClassName(string $oldPlugin, string $newPlugin)
    {
        $pluginPath = BcUtil::getPluginPath($newPlugin);
        if (!$pluginPath) return false;
        $oldTypePath = $pluginPath . 'src' . DS . 'Plugin.php';
        $oldPath = $pluginPath . 'src' . DS . $oldPlugin . 'Plugin.php';
        $newPath = $pluginPath . 'src' . DS . $newPlugin . 'Plugin.php';
        if(!file_exists($newPath)) {
            if(file_exists($oldTypePath)) {
                rename($oldTypePath, $newPath);
            } elseif(file_exists($oldPath)) {
                rename($oldPath, $newPath);
            } else {
                return false;
            }
        }
        $file = new BcFile($newPath);
        $data = $file->read();
        $file->write(preg_replace('/class\s+.*?Plugin/', 'class ' . $newPlugin . 'Plugin', $data));
        return true;
    }

    /**
     * httpからのフルURLを取得する
     *
     * @param mixed $url
     * @return string
     * @checked
     * @noTodo
     * @unitTest
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
        if (self::isConsole() && isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] === 'migrations') {
            return true;
        }
        return false;
    }

    /**
     * 既に存在するテンプレートのディレクトリを取得する
     *
     * 存在しない場合は false を返す
     *
     * @param string $plugin
     * @param string $path
     * @param string $type
     * @return false|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getExistsTemplateDir(string $theme, string $plugin, string $path, string $type = '')
    {
        if (!$theme) {
            $frontTheme = BcUtil::getCurrentTheme();
            $adminTheme = BcUtil::getCurrentAdminTheme();
        } else {
            $frontTheme = $adminTheme = $theme;
        }
        if ($plugin === 'BaserCore') {
            if ($type === 'front') {
                $templatePaths = [Plugin::templatePath($frontTheme) . $path];
            } elseif ($type === 'admin') {
                $templatePaths = [Plugin::templatePath($adminTheme) . $path];
            } else {
                $templatePaths = [
                    Plugin::templatePath($frontTheme) . $path,
                    Plugin::templatePath($adminTheme) . $path,
                ];
            }
        } else {
            if ($type === 'front') {
                $templatePaths = [
                    Plugin::templatePath($frontTheme) . 'plugin' . DS . $plugin . DS . $path,
                    Plugin::templatePath($plugin) . $path
                ];
            } elseif ($type === 'admin') {
                $templatePaths = [
                    Plugin::templatePath($adminTheme) . 'plugin' . DS . $plugin . DS . $path,
                    Plugin::templatePath($plugin) . $path
                ];
            } else {
                $templatePaths = [
                    Plugin::templatePath($frontTheme) . 'plugin' . DS . $plugin . DS . $path,
                    Plugin::templatePath($adminTheme) . 'plugin' . DS . $plugin . DS . $path,
                    Plugin::templatePath($plugin) . $path
                ];
            }
        }
        foreach($templatePaths as $templatePath) {
            if (is_dir($templatePath)) return $templatePath;
        }
        return false;
    }

    /**
     * 既に存在する webroot ディレクトリを取得する
     *
     * 存在しない場合は false を返す
     *
     * @param string $plugin
     * @param string $path
     * @param string $type
     * @return false|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getExistsWebrootDir(string $theme, string $plugin, string $path, string $type = '')
    {
        if (!$theme) {
            $frontTheme = BcUtil::getCurrentTheme();
            $adminTheme = BcUtil::getCurrentAdminTheme();
        } else {
            $frontTheme = $adminTheme = $theme;
        }
        if ($plugin === 'BaserCore') {
            if ($type === 'front') {
                $templatePaths = [Plugin::path($frontTheme) . 'webroot' . DS . $path];
            } elseif ($type === 'admin') {
                $templatePaths = [Plugin::path($adminTheme) . 'webroot' . DS . $path];
            } else {
                $templatePaths = [
                    Plugin::path($frontTheme) . 'webroot' . DS . $path,
                    Plugin::path($adminTheme) . 'webroot' . DS . $path,
                ];
            }
        } else {
            if ($type === 'front') {
                $templatePaths = [
                    Plugin::path($frontTheme) . 'webroot' . DS . Inflector::underscore($plugin) . DS . $path,
                ];
            } elseif ($type === 'admin') {
                $templatePaths = [
                    Plugin::path($adminTheme) . 'webroot' . DS . Inflector::underscore($plugin) . DS . $path,
                ];
            } else {
                $templatePaths = [
                    Plugin::path($frontTheme) . 'webroot' . DS . Inflector::underscore($plugin) . DS . $path,
                    Plugin::path($adminTheme) . 'webroot' . DS . Inflector::underscore($plugin) . DS . $path,
                ];
            }
            if (!$theme) {
                $templatePaths[] = Plugin::path($plugin) . 'webroot' . DS . $path;
            }
        }
        foreach($templatePaths as $templatePath) {
            if (is_dir($templatePath)) return $templatePath;
        }
        return false;
    }

    /**
     * 引数のペアから連想配列を構築する
     *
     * Example:
     * `aa('a','b')`
     *
     * Would return:
     * `array('a'=>'b')`
     *
     * @return array Associative array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function pairToAssoc()
    {
        $args = func_get_args();
        $argc = count($args);
        if ($argc === 1) {
            if (!$args[0]) return [];
            $args = preg_split('/(?<!\\\)\|/', $args[0]);
            $argc = count($args);
        }
        for($i = 0; $i < $argc; $i++) {
            if ($i + 1 < $argc) {
                $a[$args[$i]] = $args[$i + 1];
            } else {
                $a[$args[$i]] = null;
            }
            $i++;
        }
        return $a;
    }

    /**
     * 処理を実行し、例外が発生した場合は指定した回数だけリトライする
     * @param int $times リトライ回数
     * @param callable $callback 実行する処理
     * @param int $interval 試行の間隔（ミリ秒）
     * @return mixed
     * @throws Exception
     * @checked
     * @noTodo
     */
    public static function retry($times, callable $callback, $interval = 0)
    {
        if ($times <= 0) {
            throw new \InvalidArgumentException(__d('baser_core', 'リトライ回数は正の整数値で指定してください。'));
        }
        $times--;

        while(true) {
            try {
                return $callback();
            } catch (\Exception $e) {
                if ($times <= 0) throw $e;
                $times--;
                if ($interval > 0) usleep($interval * 1000);
            }
        }
    }

    /**
     * リファラが現在のサイトと同じかどうか判定
     *
     * @return bool
     * @checked
     * @noTodo
     */
    public static function isSameReferrerAsCurrent()
    {
        $siteDomain = BcUtil::getCurrentDomain();
        if (empty($_SERVER['HTTP_REFERER'])) {
            return false;
        }
        $refererDomain = BcUtil::getDomain($_SERVER['HTTP_REFERER']);
        if (!preg_match('/^' . preg_quote($siteDomain, '/') . '/', $refererDomain)) {
            return false;
        }
        return true;
    }

    /**
     * 認証プレフィックスのリストを取得
     *
     * 設定 `BcPrefixAuth` で定義されているものより取得する
     *
     * - フロント用のAPIでないこと
     * - disabled = true でないこと
     *
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getAuthPrefixList()
    {
        $authPrefixes = [];
        foreach(Configure::read('BcPrefixAuth') as $key => $authPrefix) {
            if ($key === 'Api') continue;
            if (!empty($authPrefix['disabled'])) continue;
            $authPrefixes[$key] = $authPrefix['name'];
        }
        return $authPrefixes;
    }

    /**
     * 指定したリクエストのプレフィックスを取得する
     *
     * @param ServerRequest $request
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getRequestPrefix(ServerRequestInterface $request)
    {
        $prefix = $request->getParam('prefix');
        if (!$prefix) $prefix = 'Front';
        return $prefix;
    }

    /**
     * デバッグモードかどうか
     *
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isDebug(): bool
    {
        return Configure::read('debug');
    }

    /**
     * 時刻の有効性チェックを行う
     *
     * @param $hour
     * @param $min
     * @param $sec
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function checkTime($hour, $min, $sec = null): bool
    {
        $hour = (int)$hour;
        if ($hour < 0 || $hour > 23) {
            return false;
        }
        $min = (int)$min;
        if ($min < 0 || $min > 59) {
            return false;
        }
        if ($sec) {
            $sec = (int)$sec;
            if ($sec < 0 || $sec > 59) {
                return false;
            }
        }
        return true;
    }

    /**
     * パーセントエンコーディングされないURLセーフなbase64デコード
     *
     * @param string $val 対象文字列
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function base64UrlSafeDecode($val): string
    {
        $val = str_replace(['_', '-', '.'], ['+', '/', '='], $val);
        return base64_decode($val);
    }

    /**
     * パーセントエンコーディングされないURLセーフなbase64エンコード
     *
     * base64エンコード時でに出てくる記号 +(プラス) , /(スラッシュ) , =(イコール)
     * このbase64エンコードした値をさらにURLのパラメータで使うためにURLエンコードすると
     * パーセントエンコーディングされてしまいます。
     * そのため、このメソッドではパーセントエンコーディングされないURLセーフな
     * base64エンコードを行います。
     *
     * @param string $val 対象文字列
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function base64UrlSafeEncode($val): string
    {
        $val = base64_encode($val);
        return str_replace(['+', '/', '='], ['_', '-', '.'], $val);
    }

    /**
     * 指定したプラグインがコアプラグインかどうかを判定する
     *
     * @param string $plugin
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function isCorePlugin(string $plugin)
    {
        $corePlugins = array_merge(Configure::read('BcApp.core'), Configure::read('BcApp.corePlugins'));
        return in_array(Inflector::camelize($plugin, '-'), $corePlugins);
    }

    /**
     * 非推奨エラーを発生させる
     *
     * デバッグモードの場合のみ発生する
     *
     * @param string $target 非推奨のターゲット
     * @param string $since 非推奨となったバージョン
     * @param string $remove 削除予定のバージョン
     * @param string $note 備考
     * @return void
     * @checked
     * @noTodo
     * @unitTest trigger_error のテストができないのでテストはスキップ
     */
    public static function triggerDeprecatedError(
        string $target,
        string $since,
        string $remove = null,
        string $note = null
    ): void
    {
        if (!Configure::read('debug')) return;
        trigger_error(self::getDeprecatedMessage($target, $since, $remove, $note), E_USER_DEPRECATED);
    }

    /**
     * 非推奨エラーメッセージを取得する
     *
     * @param string $target 非推奨のターゲット
     * @param string $since 非推奨となったバージョン
     * @param string $remove 削除予定のバージョン
     * @param string $note 備考
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getDeprecatedMessage(
        string $target,
        string $since,
        string $remove = null,
        string $note = null
    ): string
    {
        $message = sprintf(__d('baser_core', '%s は、バージョン %s より非推奨となりました。'), $target, $since);
        if ($remove) $message .= sprintf(__d('baser_core', 'バージョン %s で削除される予定です。'), $remove);
        if ($note) $message .= $note;
        return $message;
    }

    /**
     * baserCMS のバージョンが 5.1 かどうか判定
     * 5.1系へのバージョンアップ時のみ利用
     *
     * @return bool
     * @deprecated remove 5.1.0 このメソッドは非推奨です。
     */
    public static function is51()
    {
        if(file_exists(ROOT . DS . 'plugins' . DS . 'baser-core' . DS . 'VERSION.txt')) {
            $versionData = file_get_contents(ROOT . DS . 'plugins' . DS . 'baser-core' . DS . 'VERSION.txt');
        } elseif(ROOT . DS . 'vendor' . DS . 'baserproject' . DS . 'baser-core' . DS . 'VERSION.txt') {
            $versionData = file_get_contents(ROOT . DS . 'vendor' . DS . 'baserproject' . DS . 'baser-core' . DS . 'VERSION.txt');
        } else {
            trigger_error('baserCMSのバージョンが特定できません。');
        }
        $aryVersionData = explode("\n", $versionData);
        if (!empty($aryVersionData[0])) {
            $version = $aryVersionData[0];
            if(preg_match('/^5\.0/', $version) || $version === '5.1.0-dev') {
                return false;
            } else {
                return true;
            }
        } else {
            trigger_error('baserCMSのバージョンが特定できません。');
        }
        return false;
    }

}
