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

namespace BaserCore\Controller\Component;

use BaserCore\Utility\BcUtil;
use Cake\Controller\Component;

/**
 * Class BcManagerComponent
 *
 * baser Manager コンポーネント
 *
 * @package Baser.Controller.Component
 */
class BcManagerComponent extends Component
{
    /**
     * Controller
     *
     * @var Controller
     */
    public $Controller = null;

    /**
     * Startup
     *
     * @param Controller $controller
     */
    public function startup(Controller $controller)
    {
        parent::startup($controller);
        $this->Controller = $controller;
    }

    /**
     * baserCMSのインストール
     *
     * @param type $dbConfig
     * @param type $adminUser
     * @param type $adminPassword
     * @param type $adminEmail
     * @return boolean
     */
    public function install($siteUrl, $dbConfig, $adminUser = [], $baseUrl = '', $dbDataPattern = '')
    {
        if (!$dbDataPattern) {
            $dbDataPattern = Configure::read('BcApp.defaultTheme') . '.default';
        }

        $result = true;

        // キャッシュ削除
        BcUtil::clearAllCache();

        // 一時フォルダ作成
        BcUtil::checkTmpFolders();

        if ($dbConfig['datasource'] == 'sqlite' || $dbConfig['datasource'] == 'csv') {
            switch($dbConfig['datasource']) {
                case 'sqlite':
                    $dbFolderPath = APP . 'db' . DS . 'sqlite';
                    break;
                case 'csv':
                    $dbFolderPath = APP . 'db' . DS . 'csv';
                    break;
            }
            $Folder = new Folder();
            if (!is_writable($dbFolderPath) && !$Folder->create($dbFolderPath, 0777)) {
                $this->log(__d('baser', 'データベースの保存フォルダの作成に失敗しました。db フォルダの書き込み権限を見なおしてください。'));
                $result = false;
            }
        }

        // SecritySaltの設定
        $securitySalt = $this->setSecuritySalt();
        $securityCipherSeed = $this->setSecurityCipherSeed();

        // インストールファイル作成
        if (!$this->createInstallFile($securitySalt, $securityCipherSeed, $siteUrl)) {
            $this->log(__d('baser', 'インストールファイル生成に失敗しました。設定フォルダの書き込み権限を見なおしてください。'));
            $result = false;
        }

        // データベース設定ファイル生成
        if (!$this->createDatabaseConfig($dbConfig)) {
            $this->log(__d('baser', 'データベースの設定ファイル生成に失敗しました。設定フォルダの書き込み権限を見なおしてください。'));
            $result = false;
        }

        // データベース初期化
        if (!$this->constructionDb($dbConfig, $dbDataPattern, Configure::read('BcApp.defaultAdminTheme'))) {
            $this->log(__d('baser', 'データベースの初期化に失敗しました。データベースの設定を見なおしてください。'));
            $result = false;
        }

        if ($adminUser) {
            // サイト基本設定登録
            if (!$this->setAdminEmail($adminUser['email'])) {
                $this->log(__d('baser', 'サイト基本設定への管理者メールアドレスの設定処理が失敗しました。データベースの設定を見なおしてください。'));
            }
            // ユーザー登録
            $adminUser['password_1'] = $adminUser['password'];
            $adminUser['password_2'] = $adminUser['password'];
            if (!$this->addDefaultUser($adminUser)) {
                $this->log(__d('baser', '初期ユーザーの作成に失敗しました。データベースの設定を見なおしてください。'));
                $result = false;
            }
        }

        // データベースの初期更新
        if (!$this->executeDefaultUpdates($dbConfig)) {
            $this->log(__d('baser', 'データベースのデータ更新に失敗しました。データベースの設定を見なおしてください。'));
            $result = false;
        }

        // コアプラグインのインストール
        if (!$this->installCorePlugin($dbConfig, $dbDataPattern)) {
            $this->log(__d('baser', 'コアプラグインのインストールに失敗しました。'));
            $result = false;
        }

        // テーマを配置
        if (!$this->deployTheme()) {
            $this->log(__d('baser', 'テーマの配置に失敗しました。テーマフォルダの書き込み権限を確認してください。'));
            $result = false;
        }

        // テーマに管理画面のアセットへのシンボリックリンクを作成する
        if (!$this->deployAdminAssets()) {
            $this->log(__d('baser', '管理システムのアセットファイルの配置に失敗しました。テーマフォルダの書き込み権限を確認してください。'));
        }

        // アップロード用初期フォルダを作成する
        if (!$this->createDefaultFiles()) {
            $this->log(__d('baser', 'アップロード用初期フォルダの作成に失敗しました。files フォルダの書き込み権限を確認してください。'));
            $result = false;
        }

        // エディタテンプレート用の画像を配置
        if (!$this->deployEditorTemplateImage()) {
            $this->log(__d('baser', 'エディタテンプレートイメージの配置に失敗しました。files フォルダの書き込み権限を確認してください。'));
            $result = false;
        }

        //SiteConfigを再設定
        loadSiteConfig();

        // ページファイルを生成
        $this->createPageTemplates();

        return $result;
    }

    /**
     * 設定ファイルをリセットする
     *
     * @return boolean
     */
    public function resetSetting()
    {
        $result = true;
        if (file_exists(APP . 'Config' . DS . 'database.php')) {
            if (!unlink(APP . 'Config' . DS . 'database.php')) {
                $result = false;
            }
        }
        if (file_exists(APP . 'Config' . DS . 'install.php')) {
            if (!unlink(APP . 'Config' . DS . 'install.php')) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * files フォルダを初期化する
     *
     * @return boolean
     */
    public function resetFiles()
    {
        return $this->resetEmptyFolder(WWW_ROOT . 'files');
    }

    /**
     * 管理画面用のアセットフォルダ（img / js / css）を初期化する
     *
     * @return boolean
     */
    public function resetAdminAssets()
    {
        $paths = [
            WWW_ROOT . 'img' . DS . 'admin',
            WWW_ROOT . 'css' . DS . 'admin',
            WWW_ROOT . 'js' . DS . 'admin'
        ];
        $result = true;
        foreach($paths as $path) {
            if (is_dir($path)) {
                $Folder = new Folder($path);
                if (!$Folder->delete()) {
                    $result = false;
                }
                $Folder = null;
            }
        }
        return $result;
    }

    /**
     * empty ファイルを梱包したフォルダをリセットする
     *
     * empty ファイルを残して内包するファイルとフォルダを全て削除する
     *
     * @param string $path
     * @return boolean
     */
    public function resetEmptyFolder($path)
    {
        $result = true;
        $Folder = new Folder($path);
        $files = $Folder->read(true, true, true);
        $Folder = null;
        if (!empty($files[0])) {
            foreach($files[0] as $file) {
                $Folder = new Folder();
                if (!$Folder->delete($file)) {
                    $result = false;
                }
                $Folder = null;
            }
        }
        if (!empty($files[1])) {
            foreach($files[1] as $file) {
                if (basename($file) != 'empty') {
                    $Folder = new Folder();
                    if (!$Folder->delete($file)) {
                        $result = false;
                    }
                    $Folder = null;
                }
            }
        }
        return $result;
    }

    /**
     * baserCMSをリセットする
     *
     * @param array $dbConfig
     */
    public function reset($dbConfig)
    {
        $result = true;

        if (BcUtil::isInstalled()) {
            // 設定ファイルを初期化
            if (!$this->resetSetting()) {
                $result = false;
                $this->log(__d('baser', '設定ファイルを正常に初期化できませんでした。'));
            }
            // テーブルを全て削除
            if (!$this->deleteTables('default', $dbConfig)) {
                $result = false;
                $this->log(__d('baser', 'データベースを正常に初期化できませんでした。'));
            }
        }

        // テーマのテンプレートを初期化
        if (!$this->resetTheme()) {
            $result = false;
            $this->log(__d('baser', 'テーマフォルダを初期化できませんでした。'));
        }

        // 固定ページテンプレートを初期化
        if (!$this->resetPages()) {
            $result = false;
            $this->log(__d('baser', '固定ページテンプレートを初期化できませんでした。'));
        }

        // files フォルダの初期化
        if (!$this->resetFiles()) {
            $result = false;
            $this->log(__d('baser', 'files フォルダを初期化できませんでした。'));
        }

        // files フォルダの初期化
        if (!$this->resetAdminAssets()) {
            $result = false;
            $this->log(__d('baser', 'img / css / js フォルダを初期化できませんでした。'));
        }

        ClassRegistry::flush();
        BcUtil::clearAllCache();

        return $result;
    }

    /**
     * テーマリセットする
     *
     * @return bool
     */
    public function resetTheme()
    {
        $Folder = new Folder(BASER_CONFIGS . 'theme');
        $sources = $Folder->read()[0];
        $result = true;
        foreach($sources as $theme) {
            $targetPath = WWW_ROOT . 'theme' . DS . $theme;
            if (is_dir($targetPath)) {
                if (!$Folder->delete($targetPath)) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * 固定ページテンプレートをリセットする
     *
     * @return bool
     */
    public function resetPages()
    {
        $Folder = new Folder(APP . 'View' . DS . 'Pages');
        $files = $Folder->read(true, true, true);
        $result = true;
        foreach($files[0] as $file) {
            if (!$Folder->delete($file)) {
                $result = false;
            }
        }
        foreach($files[1] as $file) {
            if (basename($file) != 'empty') {
                if (!@unlink($file)) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * インストール設定を書き換える
     *
     * @param string $key
     * @param string $value
     * @return    boolean
     * @access    public
     */
    public function setInstallSetting($key, $value)
    {
        /* install.php の編集 */
        $setting = "Configure::write('" . $key . "', " . $value . ");\n";
        $key = str_replace('.', '\.', $key);
        $pattern = '/Configure\:\:write[\s]*\([\s]*\'' . $key . '\'[\s]*,[\s]*([^\s]*)[\s]*\);(\n|)/is';
        $file = new File(APP . 'Config' . DS . 'install.php');
        if (file_exists(APP . 'Config' . DS . 'install.php')) {
            $data = $file->read();
        } else {
            $data = "<?php\n";
        }
        if (preg_match($pattern, $data)) {
            $data = preg_replace($pattern, $setting, $data);
        } else {
            $data = $data . "\n" . $setting;
        }
        $return = $file->write($data);
        $file->close();
        return $return;
    }

    /**
     * 環境チェック
     *
     * @return array
     */
    public function checkEnv()
    {
        if (function_exists('apache_get_modules')) {
            $rewriteInstalled = in_array('mod_rewrite', apache_get_modules());
        } else {
            $rewriteInstalled = -1;
        }

        $status = [
            'encoding' => mb_internal_encoding(),
            'phpVersion' => phpversion(),
            'phpMemory' => intval(ini_get('memory_limit')),
            'safeModeOff' => !ini_get('safe_mode'),
            'configDirWritable' => is_writable(APP . 'Config' . DS),
            'pluginDirWritable' => is_writable(APP . 'Plugin' . DS),
            'themeDirWritable' => is_writable(WWW_ROOT . 'theme'),
            'filesDirWritable' => is_writable(WWW_ROOT . 'files'),
            'imgDirWritable' => is_writable(WWW_ROOT . 'img'),
            'jsDirWritable' => is_writable(WWW_ROOT . 'js'),
            'cssDirWritable' => is_writable(WWW_ROOT . 'css'),
            'imgAdminDirExists' => is_dir(WWW_ROOT . 'img' . DS . 'admin'),
            'jsAdminDirExists' => is_dir(WWW_ROOT . 'js' . DS . 'admin'),
            'cssAdminDirExists' => is_dir(WWW_ROOT . 'css' . DS . 'admin'),
            'tmpDirWritable' => is_writable(TMP),
            'pagesDirWritable' => is_writable(APP . 'View' . DS . 'Pages'),
            'dbDirWritable' => is_writable(APP . 'db'),
            'phpActualVersion' => preg_replace('/[a-z-]/', '', phpversion()),
            'phpGd' => extension_loaded('gd'),
            'phpPdo' => extension_loaded('pdo'),
            'phpXml' => extension_loaded('xml'),
            'apacheRewrite' => $rewriteInstalled,
        ];
        $check = [
            'encodingOk' => (preg_match('/UTF-8/i', $status['encoding'])? true : false),
            'gdOk' => $status['phpGd'],
            'pdoOk' => $status['phpPdo'],
            'xmlOk' => $status['phpXml'],
            'phpVersionOk' => version_compare(preg_replace('/[a-z-]/', '', $status['phpVersion']), Configure::read('BcRequire.phpVersion'), '>='),
            'phpMemoryOk' => ((($status['phpMemory'] >= Configure::read('BcRequire.phpMemory')) || $status['phpMemory'] == -1) === true)
        ];

        if (!$status['configDirWritable']) {
            @chmod(APP . 'Config' . DS, 0777);
            $status['configDirWritable'] = is_writable(APP . 'Config' . DS);
        }
        if (!$status['pluginDirWritable']) {
            @chmod(APP . 'Plugin' . DS, 0777);
            $status['pluginDirWritable'] = is_writable(APP . 'Plugin' . DS);
        }
        if (!$status['themeDirWritable']) {
            @chmod(WWW_ROOT . 'theme', 0777);
            $status['themeDirWritable'] = is_writable(WWW_ROOT . 'theme');
        }
        if (!$status['filesDirWritable']) {
            @chmod(WWW_ROOT . 'files', 0777);
            $status['filesDirWritable'] = is_writable(WWW_ROOT . 'files');
        }
        if (!$status['imgDirWritable']) {
            @chmod(WWW_ROOT . 'img', 0777);
            $status['imgDirWritable'] = is_writable(WWW_ROOT . 'img');
        }
        if (!$status['cssDirWritable']) {
            @chmod(WWW_ROOT . 'css', 0777);
            $status['cssDirWritable'] = is_writable(WWW_ROOT . 'css');
        }
        if (!$status['jsDirWritable']) {
            @chmod(WWW_ROOT . 'js', 0777);
            $status['jsDirWritable'] = is_writable(WWW_ROOT . 'js');
        }
        if (!$status['tmpDirWritable']) {
            @chmod(TMP, 0777);
            $status['tmpDirWritable'] = is_writable(TMP);
        }
        if (!$status['dbDirWritable']) {
            @chmod(APP . 'db', 0777);
            $status['dbDirWritable'] = is_writable(APP . 'db');
        }

        return $status + $check;
    }

    /**
     * サイトルートの管理システム用アセットを削除する
     *
     * @return bool
     */
    public function deleteAdminAssets()
    {
        $viewPath = WWW_ROOT;
        $css = $viewPath . 'css' . DS . 'admin';
        $js = $viewPath . 'js' . DS . 'admin';
        $img = $viewPath . 'img' . DS . 'admin';
        $fonts = $viewPath . 'fonts' . DS . 'admin';
        $result = true;
        $Folder = new Folder();
        if (!$Folder->delete($css)) {
            $result = false;
        }
        if (!$Folder->delete($js)) {
            $result = false;
        }
        if (!$Folder->delete($img)) {
            $result = false;
        }
        if (!$Folder->delete($fonts)) {
            $result = false;
        }
        return $result;
    }

    /**
     * テーマに梱包されているプラグインをインストールする
     *
     * @param string $theme テーマ名
     * @return bool
     */
    public function installThemesPlugins($theme)
    {
        $plugins = BcUtil::getThemesPlugins($theme);
        $result = true;
        if ($plugins) {
            App::build(['Plugin' => array_merge([BASER_THEMES . $theme . DS . 'Plugin' . DS], App::path('Plugin'))]);
            foreach($plugins as $plugin) {
                if (!$this->installPlugin($plugin)) {
                    $result = false;
                }
            }
        }
        return $result;
    }

}
