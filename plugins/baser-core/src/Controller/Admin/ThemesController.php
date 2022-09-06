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
use BaserCore\Vendor\Simplezip;
use BcZip;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;
use MailMessage;
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
     * @unitTest
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
    public function get_market_themes(ThemesServiceInterface $service)
    {
        $this->viewBuilder()->disableAutoLayout();
        $this->set('baserThemes', $service->getMarketThemes());
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
     * @checked
     * @noTodo
     */
    public function copy(ThemesServiceInterface $service, $theme)
    {
        $this->request->allowMethod(['post']);
        if (!$theme) $this->notFound();
        try {
            $service->copy($theme);
            $this->BcMessage->setInfo(__d('baser', 'テーマ「{0}」をコピーしました。', $theme));
        } catch (BcException $e) {
            $this->BcMessage->setError(__d('baser', 'テーマフォルダのアクセス権限を見直してください。' . $e->getMessage()));
        }
        $this->redirect(['action' => 'index']);
    }

    /**
     * テーマを削除する
     *
     * @param string $theme
     * @checked
     * @noTodo
     */
    public function delete(ThemesServiceInterface $service, $theme)
    {
        $this->request->allowMethod(['post']);
        if (!$theme) $this->notFound();
        try {
            $service->delete($theme);
            $this->BcMessage->setInfo(__d('baser', 'テーマ「{0}」を削除しました。', $theme));
        } catch (BcException $e) {
            $this->BcMessage->setError(__d('baser', 'テーマフォルダのアクセス権限を見直してください。' . $e->getMessage()));
        }
        $this->redirect(['action' => 'index']);
    }

    /**
     * テーマを適用する
     *
     * @param string $theme
     * @return void
     * @checked
     * @noTodo
     */
    public function apply(ThemesServiceInterface $service, string $theme)
    {
        $this->request->allowMethod(['post']);
        if (!$theme) $this->notFound();
        try {
            $info = $service->apply($this->getRequest()->getAttribute('currentSite'), $theme);
            $message = [__d('baser', 'テーマ「{0}」を適用しました。', $theme)];
            if ($info) $message = array_merge($message, [''], $info);
            $this->BcMessage->setInfo(implode("\n", $message));
        } catch(BcException $e) {
            $this->BcMessage->setError(__d('baser', 'テーマの適用に失敗しました。', $e->getMessage()));
        }
        $this->redirect(['action' => 'index']);
    }

    /**
     * 初期データセットをダウンロードする
     */
    public function download_default_data_pattern()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);

        // コアのCSVを生成
        $tmpDir = TMP . 'csv' . DS;
        $Folder = new Folder();
        $Folder->create($tmpDir);
        emptyFolder($tmpDir);
        BcUtil::clearAllCache();
        $excludes = ['plugins', 'dblogs', 'users'];
        $this->_writeCsv('core', $tmpDir, $excludes);
        // プラグインのCSVを生成
        $plugins = CakePlugin::loaded();
        foreach($plugins as $plugin) {
            $Folder->create($tmpDir . $plugin);
            emptyFolder($tmpDir . $plugin);
            $this->_writeCsv($plugin, $tmpDir . $plugin . DS);
        }
        // site_configsの編集 (email / google_analytics_id / version)
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
     * @checked
     * @noTodo
     */
    public function download(ThemesServiceInterface $service): void
    {
        $this->autoRender = false;
        $theme = BcUtil::getCurrentTheme();
        $tmpDir = $service->createDownloadToTmp($theme);
        $simplezip = new Simplezip();
        $simplezip->addFolder($tmpDir);
        $simplezip->download($theme);
        $folder = new Folder();
        $folder->delete($tmpDir);
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
