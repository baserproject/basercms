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

namespace BaserCore\Controller\Admin;

use BaserCore\Error\BcException;
use BaserCore\Service\ThemesAdminServiceInterface;
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use BcZip;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;
use MailMessage;
use Simplezip;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ThemesController
 */
class ThemesController extends BcAdminAppController
{

    /**
     * テーマ一覧
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function index(ThemesServiceInterface $service, ThemesAdminServiceInterface $adminService)
    {
        $this->set($adminService->getViewVarsForIndex($service->getIndex()));
    }

    /**
     * テーマをアップロードして適用する
     */
    public function add()
    {

        if (!$this->getRequest()->is(['post', 'put'])) {
            return;
        }

        if ($this->Theme->isOverPostSize()) {
            $this->BcMessage->setError(
                __d(
                    'baser',
                    '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                    ini_get('post_max_size')
                )
            );
        }
        if (empty($this->getRequest()->getData('Theme.file.tmp_name'))) {
            $message = __d('baser', 'ファイルのアップロードに失敗しました。');
            if (!empty($this->getRequest()->getData('Theme.file.error')) && $this->getRequest()->getData('Theme.file.error') == 1) {
                $message .= __d('baser', 'サーバに設定されているサイズ制限を超えています。');
            }
            $this->BcMessage->setError($message);
            return;
        }

        $name = $this->getRequest()->getData('Theme.file.name');
        move_uploaded_file($this->getRequest()->getData('Theme.file.tmp_name'), TMP . $name);
        $BcZip = new BcZip();
        if (!$BcZip->extract(TMP . $name, BASER_THEMES)) {
            $msg = __d('baser', 'アップロードしたZIPファイルの展開に失敗しました。');
            $msg .= "\n" . $BcZip->error;
            $this->BcMessage->setError($msg);
            return;
        }
        unlink(TMP . $name);
        $this->BcMessage->setInfo('テーマファイル「' . $name . '」を追加しました。');
        $this->redirect(['action' => 'index']);
    }

    /**
     * baserマーケットのテーマデータを取得する
     */
    public function ajax_get_market_themes()
    {

        if (Configure::read('debug')) {
            Cache::delete('baserMarketThemes');
        }
        $baserThemes = Cache::read('baserMarketThemes', '_bc_env_');
        if (!$baserThemes) {
            $Xml = new Xml();
            try {
                $baserThemes = $Xml->build(Configure::read('BcLinks.marketThemeRss'));
            } catch (BcException $e) {
            }
            if ($baserThemes) {
                $baserThemes = $Xml->toArray($baserThemes->channel);
                $baserThemes = $baserThemes['channel']['item'];
                Cache::write('baserMarketThemes', $baserThemes, '_bc_env_');
            } else {
                $baserThemes = [];
            }
        } else {
            $baserThemes = BcUtil::unserialize($baserThemes);
        }

        $this->set('baserThemes', $baserThemes);

    }

    /**
     * 初期データセットを読み込む
     *
     * @return void
     */
    public function load_default_data_pattern()
    {
        if (empty($this->getRequest()->getData('Theme.default_data_pattern'))) {
            $this->BcMessage->setError(__d('baser', '不正な操作です。'));
            $this->redirect('index');
            return;
        }
        $result = $this->_load_default_data_pattern($this->getRequest()->getData('Theme.default_data_pattern'));
        if (!$result) {
            if (!$this->getRequest()->getSession()->check('Message.flash.message')) {
                $this->BcMessage->setError(__d('baser', '初期データの読み込みが完了しましたが、いくつかの処理に失敗しています。ログを確認してください。'));
            }
            $this->redirect('index');
            return;
        }

        $this->BcMessage->setInfo(__d('baser', '初期データの読み込みが完了しました。'));
        $this->redirect('index');
    }

    /**
     * コアの初期データを読み込む
     *
     * @return void
     */
    public function reset_data()
    {
        $this->_checkSubmitToken();
        $result = $this->_load_default_data_pattern('core.default', BcSiteConfig::get('theme'));
        if (!$result) {
            $this->BcMessage->setError(__d('baser', '初期データの読み込みが完了しましたが、いくつかの処理に失敗しています。ログを確認してください。'));
            $this->redirect('/admin');
            return;
        }

        $this->BcMessage->setInfo(__d('baser', '初期データの読み込みが完了しました。'));
        $this->redirect('/admin');
    }

    /**
     * 初期データを読み込む
     *
     * @param string $dbDataPattern 初期データのパターン
     * @param string $currentTheme テーマ名
     * @return bool
     */
    protected function _load_default_data_pattern($dbDataPattern, $currentTheme = '')
    {
        [$theme, $pattern] = explode('.', $dbDataPattern);
        if (!$this->BcManager->checkDefaultDataPattern($pattern, $theme)) {
            $this->BcMessage->setError(__d('baser', '初期データのバージョンが違うか、初期データの構造が壊れています。'));
            return false;
        }
        $adminTheme = Configure::read('BcSite.admin_theme');
        $excludes = ['plugins', 'dblogs', 'users'];
        /* データを削除する */
        $this->BcManager->resetAllTables(null, $excludes);
        $result = true;
        /* コアデータ */
        if (!$this->BcManager->loadDefaultDataPattern('default', null, $pattern, $theme, 'core', $excludes)) {
            $result = false;
            $this->log(sprintf(__d('baser', '%s の初期データのロードに失敗しました。'), $dbDataPattern));
        }

        /* プラグインデータ */
        $corePlugins = Configure::read('BcApp.corePlugins');
        $plugins = array_merge($corePlugins, BcUtil::getCurrentThemesPlugins());

        foreach($plugins as $plugin) {
            $this->BcManager->loadDefaultDataPattern('default', null, $pattern, $theme, $plugin, $excludes);
        }
        if (!$result) {
            /* 指定したデータセットでの読み込みに失敗した場合、コアのデータ読み込みを試みる */
            if (!$this->BcManager->loadDefaultDataPattern('default', null, 'default', 'core', 'core', $excludes)) {
                $this->log(__d('baser', 'コアの初期データのロードに失敗しました。'));
                $result = false;
            }
            foreach($corePlugins as $corePlugin) {
                if (!$this->BcManager->loadDefaultDataPattern('default', null, 'default', 'core', $corePlugin, $excludes)) {
                    $this->log(__d('baser', 'コアのプラグインの初期データのロードに失敗しました。'));
                    $result = false;
                }
            }
            if ($result) {
                $this->BcMessage->setError(__d('baser', '初期データの読み込みに失敗しましたので baserCMSコアの初期データを読み込みました。'));
            } else {
                $this->BcMessage->setError(__d('baser', '初期データの読み込みに失敗しました。データが不完全な状態です。正常に動作しない可能性があります。'));
            }
        }

        BcUtil::clearAllCache();

        // メール受信テーブルの作成
        $MailMessage = new MailMessage();
        if (!$MailMessage->reconstructionAll()) {
            $this->log(__d('baser', 'メールプラグインのメール受信用テーブルの生成に失敗しました。'));
            $result = false;
        }
        BcUtil::clearAllCache();
        $this->getTableLocator()->clear();

        if ($currentTheme) {
            $siteConfigs = ['SiteConfig' => ['theme' => $currentTheme]];
            $this->SiteConfig->saveKeyValue($siteConfigs);
        }

        if (!$this->Page->createAllPageTemplate()) {
            $result = false;
            $this->log(
                __d('baser', '初期データの読み込み中にページテンプレートの生成に失敗しました。') .
                __d('baser', '「Pages」フォルダに書き込み権限が付与されていない可能性があります。') .
                __d('baser', '権限設定後、テーマの適用をやり直すか、表示できないページについて固定ページ管理より更新処理を行ってください。')
            );
        }
        // システムデータの初期化
        // TODO $this->BcManager->initSystemData() は、$this->Page->createAllPageTemplate() の
        // 後に呼出さないと $this->Page の実体が何故か AppModel にすりかわってしまい、
        // createAllPageTemplate メソッドが呼び出せないので注意
        if (!$this->BcManager->initSystemData(null, ['excludeUsers' => true, 'adminTheme' => $adminTheme])) {
            $result = false;
            $this->log(__d('baser', 'システムデータの初期化に失敗しました。'));
        }
        // ユーザーデータの初期化
        $User = ClassRegistry::init('User');
        $UserGroup = ClassRegistry::init('UserGroup');
        $adminGroupId = $UserGroup->field('id', ['UserGroup.name' => 'admins']);
        $users = $User->find('all', ['recursive' => -1]);
        foreach($users as $userData) {
            $userData['User']['user_group_id'] = $adminGroupId;
            unset($userData['User']['password']);
            if (!$User->save($userData)) {
                $result = false;
                $this->log(__d('baser', 'ユーザーデータの初期化に失敗しました。手動で各ユーザーのユーザーグループの設定を行なってください。'));
            }
        }
        $Db = ConnectionManager::getDataSource('default');
        if ($Db->config['datasource'] === 'Database/BcPostgres') {
            $Db->updateSequence();
        }
        // システム基本設定の更新
        $siteConfigs = ['SiteConfig' => [
            'email' => BcSiteConfig::get('email'),
            'google_analytics_id' => BcSiteConfig::get('google_analytics_id'),
            'first_access' => null,
            'version' => BcSiteConfig::get('version')
        ]];
        $this->SiteConfig->saveKeyValue($siteConfigs);


        return $result;

    }

    /**
     * テーマをコピーする
     *
     * @param string $theme
     * @return void
     */
    public function ajax_copy($theme)
    {
        $this->_checkSubmitToken();
        if (!$theme) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        $result = $this->_copy($theme);
        if (!$result) {
            $this->ajaxError(500, __d('baser', 'テーマフォルダのアクセス権限を見直してください。'));
            return;
        }

        exit(true);
    }

    /**
     * テーマをコピーする
     *
     * @param string $theme
     * @return array|bool
     */
    protected function _copy($theme)
    {
        $basePath = WWW_ROOT . 'theme' . DS;
        $newTheme = $theme . '_copy';
        while(true) {
            if (!is_dir($basePath . $newTheme)) {
                break;
            }
            $newTheme .= '_copy';
        }
        $folder = new Folder();
        $result = $folder->copy([
            'from' => $basePath . $theme,
            'to' => $basePath . $newTheme,
            'mode' => 0777,
            'skip' => ['_notes']
        ]);
        if (!$result) {
            return false;
        }

        $this->Theme->saveDblog('テーマ「' . $theme . '」をコピーしました。');
        return $this->_loadThemeInfo($newTheme);
    }

    /**
     * テーマを削除する　(ajax)
     *
     * @param string $theme
     * @return void
     */
    public function ajax_delete($theme)
    {
        $this->_checkSubmitToken();
        if (!$theme) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
        }
        if (!$this->_del($theme)) {
            $this->ajaxError(500, __d('baser', 'テーマフォルダを手動で削除してください。'));
            exit;
        }
        clearViewCache();
        exit(true);
    }

    /**
     * データを削除する
     *
     * @param string $theme テーマ名
     * @return bool
     */
    protected function _del($theme)
    {
        $path = WWW_ROOT . 'theme' . DS . $theme;
        $folder = new Folder();
        if (!$folder->delete($path)) {
            return false;
        }
        $siteConfig = ['SiteConfig' => $this->siteConfigs];
        if ($theme == $siteConfig['SiteConfig']['theme']) {
            $siteConfig['SiteConfig']['theme'] = '';
            $this->SiteConfig->saveKeyValue($siteConfig);
        }
        return true;
    }

    /**
     * テーマを削除する
     *
     * @param string $theme
     * @return void
     */
    public function del($theme)
    {
        $this->_checkSubmitToken();
        if (!$theme) {
            $this->notFound();
        }
        $siteConfig = ['SiteConfig' => $this->siteConfigs];
        $path = WWW_ROOT . 'theme' . DS . $theme;
        $folder = new Folder();
        $folder->delete($path);
        if ($theme == $siteConfig['SiteConfig']['theme']) {
            $siteConfig['SiteConfig']['theme'] = '';
            $this->SiteConfig->saveKeyValue($siteConfig);
        }
        clearViewCache();
        $this->BcMessage->setInfo('テーマ「' . $theme . '」を削除しました。');
        $this->redirect(['action' => 'index']);
    }

    /**
     * テーマを適用する
     *
     * @param string $theme
     * @return void
     */
    public function apply($theme)
    {
        $this->_checkSubmitToken();
        if (!$theme) {
            $this->notFound();
        }

        $this->_applyTheme($theme);
        $this->redirect(['action' => 'index']);

    }

    protected function _applyTheme($theme)
    {

        $plugins = BcUtil::getCurrentThemesPlugins();
        // テーマ梱包のプラグインをアンインストール
        foreach($plugins as $plugin) {
            // TODO PluginsTable::detach() に移行する
            $this->BcManager->uninstallPlugin($plugin);
        }

        $siteConfig['SiteConfig']['theme'] = $theme;
        $this->SiteConfig->saveKeyValue($siteConfig);
        clearViewCache();

        $info = [];
        $themePath = BASER_THEMES . $theme . DS;

        $Folder = new Folder($themePath . 'Plugin');
        $files = $Folder->read(true, true, false);
        if (!empty($files[0])) {
            $info = array_merge($info, [
                __d('baser', 'このテーマは下記のプラグインを同梱しています。')
            ]);
            foreach($files[0] as $file) {
                $info[] = '	・' . $file;
            }
        }

        Configure::write('BcSite.theme', $theme);
        $plugins = BcUtil::getCurrentThemesPlugins();

        App::build(['Plugin' => array_merge([BASER_THEMES . $theme . DS . 'Plugin' . DS], App::path('Plugin'))]);
        // テーマ梱包のプラグインをインストール
        foreach($plugins as $plugin) {
            $this->BcManager->installPlugin($plugin);
        }

        $path = BcUtil::getDefaultDataPath('BaserCore', $theme);
        if (strpos($path, '/theme/' . $theme . '/') !== false) {
            if ($info) {
                $info = array_merge($info, ['']);
            }
            $info = array_merge($info, [
                __d('baser', 'このテーマは初期データを保有しています。'),
                __d('baser', 'Webサイトにテーマに合ったデータを適用するには、初期データ読込を実行してください。'),
            ]);
        }

        if (!$this->Page->createAllPageTemplate()) {
            $message = [
                __d('baser', 'テーマ変更中にページテンプレートの生成に失敗しました。'),
                __d('baser', '「Pages」フォルダに書き込み権限が付与されていない可能性があります。'),
                __d('baser', '権限設定後、テーマの適用をやり直すか、表示できないページについて固定ページ管理より更新処理を行ってください。')
            ];
            if ($info) {
                $message = array_merge($message, [''], $info);
            }
            $this->BcMessage->setError(implode("\n", $message));
            return true;
        }

        $message = ['テーマ「' . $theme . '」を適用しました。'];
        if ($info) {
            $message = array_merge($message, [''], $info);
        }
        $this->BcMessage->setInfo(implode("\n", $message));
        return true;

    }

    /**
     * 初期データセットをダウンロードする
     */
    public function download_default_data_pattern()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);

        /* コアのCSVを生成 */
        $tmpDir = TMP . 'csv' . DS;
        $Folder = new Folder();
        $Folder->create($tmpDir);
        emptyFolder($tmpDir);
        BcUtil::clearAllCache();
        $excludes = ['plugins', 'dblogs', 'users'];
        $this->_writeCsv('core', $tmpDir, $excludes);
        /* プラグインのCSVを生成 */
        $plugins = CakePlugin::loaded();
        foreach($plugins as $plugin) {
            $Folder->create($tmpDir . $plugin);
            emptyFolder($tmpDir . $plugin);
            $this->_writeCsv($plugin, $tmpDir . $plugin . DS);
        }
        /* site_configsの編集 (email / google_analytics_id / version) */
        $targets = ['email', 'google_analytics_id', 'version'];
        $path = $tmpDir . 'site_configs.csv';
        $fp = fopen($path, 'a+');
        $records = [];
        while(($record = fgetcsvReg($fp, 10240)) !== false) {
            if (in_array($record[1], $targets)) {
                $record[2] = '';
            }
            $records[] = '"' . implode('","', $record) . '"';
        }
        ftruncate($fp, 0);
        fwrite($fp, implode("\n", $records));
        /* ZIPに固めてダウンロード */
        $fileName = 'default';
        $Simplezip = new Simplezip();
        $Simplezip->addFolder($tmpDir);
        $Simplezip->download($fileName);
        emptyFolder($tmpDir);
        exit();
    }

    /**
     * CSVファイルを書きだす
     *
     * @param string $configKeyName
     * @param string $path
     * @return boolean
     */
    function _writeCsv($plugin, $path, $exclude = [])
    {

        $pluginTables = [];
        if ($plugin !== 'core') {
            $pluginPath = BcUtil::getSchemaPath($plugin);
            $Folder = new Folder($pluginPath);
            $files = $Folder->read(true, true, false);
            $pluginTables = $files[1];
            foreach($pluginTables as $key => $pluginTable) {
                if (preg_match('/^(.+)\.php$/', $pluginTable, $matches)) {
                    $pluginTables[$key] = $matches[1];
                } else {
                    unset($pluginTables[$key]);
                }
            }
        }

        $pluginKey = Inflector::underscore($plugin);
        $db = ConnectionManager::getDataSource('default');
        $db->cacheSources = false;
        $tables = $db->listSources();
        $tableList = getTableList();
        $result = true;
        foreach($tables as $table) {
            if (($plugin === 'core' && in_array($table, $tableList['core'])) || ($plugin !== 'core' && in_array($table, $tableList['plugin']))) {
                $table = str_replace($db->config['prefix'], '', $table);
                if (in_array($table, $exclude)) {
                    continue;
                }
                if ($pluginKey !== 'core' && !in_array($table, $pluginTables)) {
                    continue;
                }
                if (!$db->writeCsv([
                    'path' => $path . $table . '.csv',
                    'encoding' => 'UTF-8',
                    'init' => false,
                    'plugin' => ($plugin === 'core')? null : $plugin
                ])) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * ダウンロード
     */
    public function download()
    {
        $this->autoRender = false;
        $tmpDir = TMP . 'theme' . DS;
        $Folder = new Folder();
        $Folder->create($tmpDir);
        $theme = BcSiteConfig::get('theme');
        $path = BASER_THEMES . $theme . DS;
        $Folder->copy([
            'from' => $path,
            'to' => $tmpDir . $theme,
            'chmod' => 0777
        ]);
        $Simplezip = new Simplezip();
        $Simplezip->addFolder($tmpDir);
        $Simplezip->download($theme);
        $Folder->delete($tmpDir);
    }

    /**
     * スクリーンショットを表示
     * @param $theme
     * @return false|string
     * @checked
     * @noTodo
     */
    public function screenshot($theme)
    {
        $this->autoRender = false;
        $pluginPath = BcUtil::getPluginPath($theme);
        if(!file_exists($pluginPath . 'screenshot.png')) {
            $this->notFound();
        }
        return $this->getResponse()->withFile($pluginPath . 'screenshot.png');
    }

}
