<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */
// TODO: 一部の関数使うため一時的にコメントアウト
// App::uses('EmailComponent', 'Controller/Component');
// App::uses('BcEmailComponent', 'Controller/Component');
// App::uses('CakeText', 'Utility');
use Cake\Cache\Cache;
use Cake\Utility\Text;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;
use BaserCore\Utility\BcUtil;

/**
 * baserCMS共通関数
 *
 * baser/config/bootstrapより呼び出される
 *
 * @package         Baser
 */

/**
 * リビジョンを取得する
 * @param string    baserCMS形式のバージョン表記　（例）baserCMS 1.5.3.1600 beta
 * @return string   リビジョン番号
 */
function revision($version)
{
    return preg_replace("/baserCMS [0-9]+?\.[0-9]+?\.[0-9]+?\.([0-9]*)[\sa-z]*/is", "$1", $version);
}

/**
 * 環境変数よりURLパラメータを取得する
 *
 * ＊ プレフィックスは除外する
 * ＊ GETパラメーターは除外する
 *
 * 《注意》
 * bootstrap 実行後でのみ利用可
 */
function getUrlParamFromEnv()
{
    $url = getUrlFromEnv();
    $url = preg_replace('/^\//', '', $url);
    if (strpos($url, '?') !== false) {
        [$url] = explode('?', $url);
    }
    return $url;
}

/**
 * 環境変数よりURLを取得する
 *
 * スマートURLオフ＆bootstrapのタイミングでは、$_GET['url']が取得できてない為、それをカバーする為に利用する
 * ＊ 先頭のスラッシュは除外する
 * ＊ baseUrlは除外する
 *
 */
function getUrlFromEnv()
{

    if (!empty($_GET['url'])) {
        return preg_replace('/^\//', '', $_GET['url']);
    }

    if (!isset($_SERVER['REQUEST_URI'])) {
        return;
    } else {
        $requestUri = $_SERVER['REQUEST_URI'];
    }

    $appBaseUrl = Configure::read('App.baseUrl');
    $parameter = '';

    if ($appBaseUrl) {

        $base = dirname($appBaseUrl);
        if (strpos($requestUri, $appBaseUrl) !== false) {
            $parameter = str_replace($appBaseUrl, '', $requestUri);
        } else {
            // トップページ
            $parameter = str_replace($base . '/', '', $requestUri);
        }
    } else {

        if (strpos($requestUri, '?')) {
            $aryRequestUri = explode('?', $requestUri);
            $requestUri = $aryRequestUri[0];
        }
        if (preg_match('/^' . str_replace('/', '\/', BcUtil::baseUrl()) . '/is', $requestUri)) {
            $parameter = preg_replace('/^' . str_replace('/', '\/', BcUtil::baseUrl()) . '/is', '', $requestUri);
        } else {
            $parameter = $requestUri;
        }
    }

    return preg_replace('/^\//', '', $parameter);
}

/**
 * モバイルプレフィックスは除外したURLを取得する
 *
 * MEMO: BcRequest.(agent).aliasは廃止
 *
 * @param CakeRequest $Request
 * @return type
 */
function getPureUrl($Request)
{
    if (!$Request) {
        $Request = new CakeRequest();
    }
    $url = $Request->url;
    if ($url === false) {
        $url = '';
    }
    if (strpos($url, '?') !== false) {
        [$url] = explode('?', $url);
    }
    return $url;
}

/**
 * Viewキャッシュを削除する
 * URLを指定しない場合は全てのViewキャッシュを削除する
 * 全て削除する場合、標準の関数clearCacheだとemptyファイルまで削除されてしまい、
 * 開発時に不便なのでFolderクラスで削除
 *
 * @param    $url
 * @return    void
 * @access    public
 */
function clearViewCache($url = null, $ext = '.php')
{

    $url = preg_replace('/^\/mobile\//is', '/m/', $url);
    if ($url == '/' || $url == '/index' || $url == '/index.html' || $url == '/m/' || $url == '/m/index' || $url == '/m/index.html') {
        $homes = ['index', 'index_html'];
        foreach($homes as $home) {
            if (preg_match('/^\/m/is', $url)) {
                if ($home) {
                    $home = 'm_' . $home;
                } else {
                    $home = 'm';
                }
            } elseif (preg_match('/^\/s/is', $url)) {
                if ($home) {
                    $home = 's_' . $home;
                } else {
                    $home = 's';
                }
            }
            $baseUrl = BcUtil::baseUrl();
            if ($baseUrl) {
                $baseUrl = str_replace(['/', '.'], '_', $baseUrl);
                $baseUrl = preg_replace('/^_/', '', $baseUrl);
                $baseUrl = preg_replace('/_$/', '', $baseUrl);
                if ($home) {
                    $home = $baseUrl . $home;
                } else {
                    $home = $baseUrl;
                }
            } elseif (!$home) {
                $home = 'home';
            }
            clearCache($home);
        }
    } elseif ($url) {
        if (preg_match('/\/index$/', $url)) {
            clearCache(strtolower(Text::slug($url)), 'views', $ext);
            $url = preg_replace('/\/index$/', '', $url);
            clearCache(strtolower(Text::slug($url)), 'views', $ext);
        } else {
            clearCache(strtolower(Text::slug($url)), 'views', $ext);
        }
    } else {
        $folder = new Folder(CACHE . 'views' . DS);
        $files = $folder->read(true, true);
        foreach($files[1] as $file) {
            if ($file != 'empty') {
                @unlink(CACHE . 'views' . DS . $file);
            }
        }
    }
}

/**
 * データキャッシュを削除する
 */
function clearDataCache()
{

    App::import('Core', 'Folder');
    $folder = new Folder(CACHE . 'datas' . DS);

    $files = $folder->read(true, true, true);
    foreach($files[1] as $file) {
        @unlink($file);
    }
    $Folder = new Folder();
    foreach($files[0] as $folder) {
        $Folder->delete($folder);
    }
}

/**
 * キャッシュファイルを全て削除する
 */
function clearAllCache()
{

    Cache::clear(false, '_cake_core_');
    Cache::clear(false, '_cake_model_');
    Cache::clear(false, '_bc_env_');
    // viewキャッシュ削除
    clearCache();
    // dataキャッシュ削除
    clearDataCache();
}

/**
 * DBセッティングが存在するかチェックする
 *
 * @param string $name
 * @return mixed DatabaseConfig Or false
 */
function getDbConfig($name = 'default')
{
    if (file_exists(APP . 'Config' . DS . 'database.php')) {
        require_once APP . 'Config' . DS . 'database.php';
        $dbConfig = new DATABASE_CONFIG();
        if (!empty($dbConfig->{$name}['datasource'])) {
            return $dbConfig->{$name};
        }
    }
    return false;
}

/**
 * 配列を再帰的に上書きする
 * 二つまで
 * @param array $a
 * @param array $b
 * @return    array
 */
function amr($a, $b)
{

    foreach($b as $k => $v) {
        if (is_array($v)) {
            if (isset($a[$k])) {
                $a[$k] = amr($a[$k], $v);
                continue;
            }
        }
        if (!is_array($a)) {
            $a = [$a];
        }
        $a[$k] = $v;
    }
    return $a;
}

/**
 * 利用可能なプラグインのリストを取得する
 *
 * ClassRegistry::removeObject('Plugin'); で一旦 Plugin オブジェクトを削除
 * エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
 *
 * @return array
 */
function getEnablePlugins()
{

    $enablePlugins = [];
    if (!Configure::read('Cache.disable') && Configure::read('debug') == 0) {
        $enablePlugins = Cache::read('enable_plugins', '_bc_env_');
    }
    if (!$enablePlugins) {
        // DBに接続できない場合、CakePHPのエラーメッセージが表示されてしまう為、 try を利用
        try {
            $Plugin = ClassRegistry::init('Plugin');   // ConnectionManager の前に呼出さないとエラーとなる
        } catch (Exception $ex) {
            return [];
        }
        $db = ConnectionManager::getDataSource('default');
        $sources = $db->listSources();
        $pluginTable = $db->config['prefix'] . 'plugins';
        $enablePlugins = [];
        if (!is_array($sources) || in_array(strtolower($pluginTable), array_map('strtolower', $sources))) {
            $enablePlugins = $Plugin->find('all', ['conditions' => ['Plugin.status' => true], 'order' => 'Plugin.priority']);
            ClassRegistry::removeObject('Plugin');
            if ($enablePlugins) {
                foreach($enablePlugins as $key => $enablePlugin) {
                    $pluginExists = false;
                    foreach(App::path('plugins') as $path) {
                        if (is_dir($path . $enablePlugin['Plugin']['name'])) {
                            $pluginExists = true;
                        }
                        $underscored = Inflector::underscore($enablePlugin['Plugin']['name']);
                        if (is_dir($path . $underscored)) {
                            $pluginExists = true;
                        }
                    }
                    if (!$pluginExists) {
                        unset($enablePlugins[$key]);
                    }
                }
                if (!Configure::read('Cache.disable')) {
                    Cache::write('enable_plugins', $enablePlugins, '_bc_env_');
                }
            }
        }
    }
    return $enablePlugins;

}

/**
 * アップデートのURLを記載したメールを送信する
 */
function sendUpdateMail()
{

    $bcSite = Configure::read('BcSite');
    $bcSite['update_id'] = CakeText::uuid();
    $SiteConfig = ClassRegistry::init('SiteConfig');
    $SiteConfig->saveKeyValue(['SiteConfig' => $bcSite]);
    ClassRegistry::removeObject('SiteConfig');

    $BcEmail = new BcEmailComponent();
    if (!empty($bcSite['mail_encode'])) {
        $encode = $bcSite['mail_encode'];
    } else {
        $encode = 'ISO-2022-JP';
    }
    $BcEmail->charset = $encode;
    $BcEmail->sendAs = 'text';
    $BcEmail->lineLength = 105;
    if (!empty($bcSite['smtp_host'])) {
        $BcEmail->delivery = 'smtp';
        $BcEmail->smtpOptions = ['host' => $bcSite['smtp_host'],
            'port' => 25,
            'timeout' => 30,
            'username' => ($bcSite['smtp_user'])? $bcSite['smtp_user'] : null,
            'password' => ($bcSite['smtp_password'])? $bcSite['smtp_password'] : null];
    } else {
        $BcEmail->delivery = "mail";
    }
    $BcEmail->to = $bcSite['email'];
    $BcEmail->subject = __d('baser_core', 'baserCMSアップデート');
    $BcEmail->from = $bcSite['name'] . ' <' . $bcSite['email'] . '>';
    $message = [];
    $message[] = __d('baser_core', '下記のURLよりbaserCMSのアップデートを完了してください。');
    $message[] = \BaserCore\Utility\BcUtil::topLevelUrl(false) . BcUtil::baseUrl() . 'updaters/index/' . $bcSite['update_id'];
    $BcEmail->send($message);
}

/**
 * 展開出力
 *
 * デバッグレベルが 0 の時でも強制的に出力する
 *
 * @param mixed $var
 * @return void
 */
function p($var)
{
    $debug = Configure::read('debug');
    if ($debug < 1) {
        Configure::write('debug', 1);
    }
    $calledFrom = debug_backtrace();
    echo '<strong style="font-size:10px">' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
    echo '<span style="font-size:10px"> (line <strong>' . $calledFrom[0]['line'] . '</strong>)</span>';
    debug($var, true, false);
    if ($debug < 1) {
        Configure::write('debug', $debug);
    }
}

/**
 * データベースのドライバー名を取得する
 *
 * @param string $dbConfigKeyName
 * @return string
 */
function getDbDriver($dbConfigKeyName = 'default')
{

    $db = ConnectionManager::getDataSource($dbConfigKeyName);
    return $db->config['datasource'];
}


/**
 * Constructs associative array from pairs of arguments.
 *
 * Example:
 *
 * `aa('a','b')`
 *
 * Would return:
 *
 * `array('a'=>'b')`
 *
 * @return array Associative array
 * @link http://book.cakephp.org/view/695/aa
 */
function aa()
{
    $args = func_get_args();
    $argc = count($args);
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
 * プラグインを読み込む
 *
 * @param string $plugin
 * @return bool
 */
function loadPlugin($plugin, $priority)
{
    if (CakePlugin::loaded($plugin)) {
        return true;
    }
    try {
        CakePlugin::load($plugin);
    } catch (Exception $e) {
        return false;
    }
    $pluginPath = CakePlugin::path($plugin);
    $config = [
        'bootstrap' => file_exists($pluginPath . 'Config' . DS . 'bootstrap.php'),
        'routes' => file_exists($pluginPath . 'Config' . DS . 'routes.php')
    ];
    CakePlugin::load($plugin, $config);
    if (file_exists($pluginPath . 'Config' . DS . 'setting.php')) {
        // DBに接続できない場合、CakePHPのエラーメッセージが表示されてしまう為、 try を利用
        // ※ プラグインの setting.php で、DBへの接続処理が書かれている可能性がある為
        try {
            Configure::load($plugin . '.setting');
        } catch (Exception $ex) {
        }
    }
    return true;
}

/**
 * 後方互換のための非推奨メッセージを生成する
 *
 * @param string $target 非推奨の対象
 * @param string $since 非推奨となったバージョン
 * @param string $remove 削除予定のバージョン
 * @param string $note その他特記事項
 * @return string 非推奨メッセージ
 */
function deprecatedMessage($target, $since, $remove = null, $note = null)
{

    if (Configure::read('debug') == 0) {
        return;
    }
    $message = sprintf(__d('baser_core', '%s は、バージョン %s より非推奨となりました。'), $target, $since);
    if ($remove) {
        $message .= sprintf(__d('baser_core', 'バージョン %s で削除される予定です。'), $remove);
    }
    if ($note) {
        $message .= $note;
    }
    return $message;

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
 */
function base64UrlsafeEncode($val)
{
    $val = base64_encode($val);
    return str_replace(['+', '/', '='], ['_', '-', '.'], $val);
}

/**
 * パーセントエンコーディングされないURLセーフなbase64デコード
 *
 * @param string $val 対象文字列
 * @return string
 */
function base64UrlsafeDecode($val)
{
    $val = str_replace(['_', '-', '.'], ['+', '/', '='], $val);
    return base64_decode($val);
}

/**
 * 時刻の有効性チェックを行う
 *
 * @param $hour
 * @param $min
 * @param $sec
 * @return bool
 */
function checktime($hour, $min, $sec = null)
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
