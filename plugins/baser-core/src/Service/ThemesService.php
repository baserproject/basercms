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

namespace BaserCore\Service;

use BaserCore\Error\BcException;
use BaserCore\Model\Entity\Site;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUtil;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Utility\BcZip;
use BcMail\Service\MailMessagesServiceInterface;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\EntityInterface;
use Cake\Log\LogTrait;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Laminas\Diactoros\UploadedFile;

/**
 * ThemesService
 */
class ThemesService implements ThemesServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use LogTrait;

    /**
     * 単一データ取得
     *
     * @param string $theme
     * @return EntityInterface
     * @checked
     * @unitTest
     * @noTodo
     */
    public function get(string $theme)
    {
        $pluginsTable = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
        return $pluginsTable->getPluginConfig($theme);
    }

    /**
     * 一覧データ取得
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(): array
    {
        $themeNames = BcUtil::getThemeList();
        $themes = [];
        foreach($themeNames as $value) {
            $themes[] = $this->get($value);
        }
        return $themes;
    }

    /**
     * 初期データのセットを取得する
     *
     * @param string $theme
     * @param array $options
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDefaultDataPatterns($theme = '', $options = [])
    {
        if (!$theme) $theme = Inflector::camelize(Configure::read('BcApp.coreFrontTheme'), '-');
        $options = array_merge(['useTitle' => true], $options);
        $dataPath = dirname(BcUtil::getDefaultDataPath($theme));
        if(!$dataPath) return [];
        if ($theme !== Inflector::camelize(Configure::read('BcApp.coreFrontTheme'), '-') &&
            $dataPath === dirname(BcUtil::getDefaultDataPath())) {
            return [];
        }

        $patterns = [];
        $Folder = new BcFolder($dataPath);
        $files = $Folder->getFolders();
        if ($files) {
            foreach($files as $pattern) {
                if ($options['useTitle']) {
                    if(BcUtil::isInstalled()) {
                        $pluginsTable = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
                        $themeRecord = $pluginsTable->getPluginConfig($theme);
                        if ($themeRecord && $themeRecord->title) {
                            $title = $themeRecord->title;
                        } else {
                            $title = $theme;
                        }
                    } else {
                        $title = $theme;
                    }
                    $patternName = $title . ' ( ' . $pattern . ' )';
                } else {
                    $patternName = $pattern;
                }
                $patterns[$theme . '.' . $pattern] = $patternName;
            }
        }
        return $patterns;
    }

    /**
     * 新しいテーマをアップロードする
     *
     * @param UploadedFile[] $postData
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(array $postData): string
    {
        if (BcUtil::isOverPostSize()) {
            throw new BcException(__d('baser_core',
                '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
                ini_get('post_max_size')
            ));
        }
        if (empty($_FILES['file']['tmp_name'])) {
            $message = '';
            if (isset($postData['file']) && $postData['file']->getError() === 1) {
                $message = __d('baser_core', 'サーバに設定されているサイズ制限を超えています。');
            }
            throw new BcException($message);
        }
        $name = $postData['file']->getClientFileName();
        $postData['file']->moveTo(TMP . $name);
        $srcDirName = basename($name, '.zip');
        $zip = new BcZip();
        if (!$zip->extract(TMP . $name, TMP)) {
            throw new BcException(__d('baser_core', 'アップロードしたZIPファイルの展開に失敗しました。'));
        }

        $dstName = $srcName = Inflector::camelize($srcDirName);
        if (preg_match('/^(.+?)([0-9]+)$/', $srcName, $matches)) {
            $baseName = $matches[1];
            $num = $matches[2];
        } else {
            $baseName = $srcName;
            $num = null;
        }
        while(is_dir(BASER_THEMES . $dstName) || is_dir(BASER_THEMES . Inflector::dasherize($dstName))) {
            if (is_null($num)) $num = 1;
            $num++;
            $dstName = $baseName . $num;
        }
        $folder = new BcFolder(TMP . $srcDirName);
        $folder->move(BASER_THEMES . $dstName);
        unlink(TMP . $name);
        BcUtil::changePluginClassName($srcName, $dstName);
        BcUtil::changePluginNameSpace($dstName);
        return $dstName;
    }

    /**
     * テーマを適用する
     *
     * @param string $theme
     * @return array 適用完了後に表示するメッセージ
     * @checked
     * @noTodo
     * @unitTest
     */
    public function apply(Site $site, string $theme): array
    {
        // テーマを適用
        BcUtil::includePluginClass($theme);

        $plugin = Plugin::getCollection()->get($theme);
        if(method_exists($plugin, 'applyAsTheme')) {
            $plugin->applyAsTheme($site, $theme);
        } else {
            throw new BcException(__d('baser_core', 'src フォルダに Plugin クラスが配置されていません。'));
        }

        // テーマ梱包のプラグインを無効化
        $this->detachCurrentThemesPlugins();

        // テーマが梱包するプラグイン情報を取得
        $info = $this->getThemesPluginsInfo($theme);

        // テーマが梱包するプラグインをインストール
        $this->installThemesPlugins($theme);

        // テーマが初期データを保有している場合の情報を取得
        return $this->getThemesDefaultDataInfo($theme, $info);
    }

    /**
     * 現在のテーマのプラグインを無効化する
     *
     * @checked
     * @unitTest
     * @noTodo
     */
    private function detachCurrentThemesPlugins()
    {
        $plugins = BcUtil::getCurrentThemesPlugins();
        foreach($plugins as $plugin) {
            /* @var PluginsService $pluginsService */
            $pluginsService = $this->getService(PluginsServiceInterface::class);
            $pluginsService->detach($plugin);
        }
    }

    /**
     * 指定したテーマが梱包するプラグイン情報を取得
     *
     * @param string $theme
     * @return array|string[]
     * @checked
     * @noTodo
     * @unitTest
     */
    private function getThemesPluginsInfo(string $theme)
    {
        $info = [];
        $themePath = BcUtil::getPluginPath($theme);
        $Folder = new BcFolder($themePath . 'plugins');
        $files = $Folder->getFolders();
        if (!empty($files)) {
            $info = array_merge($info, [
                __d('baser_core', 'このテーマは下記のプラグインを同梱しています。')
            ]);
            foreach($files as $file) {
                $info[] = '	・' . $file;
            }
        }
        return $info;
    }

    /**
     * テーマが初期データを保有している場合の情報を取得
     *
     * @param string $theme
     * @param array $info
     * @return array|mixed|string[]
     * @noTodo
     * @checked
     * @unitTest
     */
    private function getThemesDefaultDataInfo(string $theme, array $info = [])
    {
        $path = BcUtil::getDefaultDataPath($theme);
        if (preg_match('/\/(' . $theme . '|' . Inflector::dasherize($theme) . ')\//', $path)) {
            if ($info) $info = array_merge($info, ['']);
            $info = array_merge($info, [
                __d('baser_core', 'このテーマは初期データを保有しています。'),
                __d('baser_core', 'Webサイトにテーマに合ったデータを適用するには、初期データ読込を実行してください。'),
            ]);
        }
        return $info;
    }

    /**
     * テーマが梱包するプラグインをインストールする
     *
     * @param string $theme
     * @throws \Exception
     * @checked
     * @unitTest
     * @noTodo
     */
    public function installThemesPlugins(string $theme)
    {
        /* @var PluginsService $pluginsService */
        $pluginsService = $this->getService(PluginsServiceInterface::class);
        $plugins = BcUtil::getThemesPlugins($theme);
        // テーマ梱包のプラグインをインストール
        foreach($plugins as $plugin) {
            $pluginsService->install($plugin);
        }
    }

    /**
     * 初期データを読み込む
     *
     * @param string $currentTheme
     * @param string $dbDataPattern
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function loadDefaultDataPattern(string $currentTheme, string $dbDataPattern): bool
    {
        /* @var BcDatabaseService $dbService */
        $dbService = $this->getService(BcDatabaseServiceInterface::class);

        // データパターンのチェック
        [$theme, $pattern] = explode('.', $dbDataPattern);
        if (!$this->checkDefaultDataPattern($theme, $pattern)) {
            throw new BcException(__d('baser_core', '初期データのバージョンが違うか、初期データの構造が壊れています。'));
        }

        // 初期データ読み込み
        $result = true;
        try {
            if (!$dbService->loadDefaultDataPattern($theme, $pattern)) $result = false;
        } catch (\Throwable $e) {
            throw $e;
        }

        /** @var MailMessagesServiceInterface $mailMessagesService */
        $mailMessagesService = $this->getService(MailMessagesServiceInterface::class);
		if (!$mailMessagesService->reconstructionAll()) {
			$this->log(__d('baser_core', 'メールプラグインのメール受信用テーブルの生成に失敗しました。'));
			$result = false;
		}

        // システムデータの初期化
        if (!$dbService->initSystemData([
            'excludeUsers' => true,
            'email' => BcSiteConfig::get('email'),
            'google_analytics_id' => BcSiteConfig::get('google_analytics_id'),
            'first_access' => null,
            'version' => BcSiteConfig::get('version'),
            'theme' => $currentTheme,
            'adminTheme' => BcSiteConfig::get('admin_theme')
        ])) {
            $this->log(__d('baser_core', 'システムデータの初期化に失敗しました。'));
            $result = false;
        }

        // 管理画面のカレントサイトの設定状態を削除
        $request = Router::getRequest();
        $request->getSession()->delete('BcApp.Admin.currentSite');

        // DBシーケンスの更新
        $dbService->updateSequence();

        return $result;
    }

    /**
     * コピーする
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(string $theme): bool
    {
        if (!is_writable(BASER_THEMES)) throw new BcException(BASER_THEMES . ' に書込み権限がありません。');

        if (in_array($theme, Configure::read('BcApp.core'))) $theme = Inflector::dasherize($theme);
        $oldTheme = Inflector::camelize($theme, '-');
        $newTheme = $oldTheme . 'Copy';
        while(true) {
            if (!is_dir(BASER_THEMES . $newTheme)) break;
            $newTheme .= 'Copy';
        }
        $folder = new BcFolder(BASER_THEMES . $theme);
        if (!$folder->copy(BASER_THEMES . $newTheme)) {
            return false;
        }
        if(!file_exists(BASER_THEMES . $newTheme . DS . 'src' . DS . $newTheme . 'Plugin.php')) {
            if (file_exists(BASER_THEMES . $newTheme . DS . 'src' . DS . 'Plugin.php')) {
                rename(
                    BASER_THEMES . $newTheme . DS . 'src' . DS . 'Plugin.php',
                    BASER_THEMES . $newTheme . DS . 'src' . DS . $newTheme . 'Plugin.php'
                );
            } elseif (file_exists(BASER_THEMES . $newTheme . DS . 'src' . DS . $oldTheme . 'Plugin.php')) {
                rename(
                    BASER_THEMES . $newTheme . DS . 'src' . DS . $oldTheme . 'Plugin.php',
                    BASER_THEMES . $newTheme . DS . 'src' . DS . $newTheme . 'Plugin.php'
                );
            }
        }
        BcUtil::changePluginClassName($oldTheme, $newTheme);
        BcUtil::changePluginNameSpace($newTheme);
        return true;
    }

    /**
     * 削除する
     *
     * @param string $theme
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(string $theme): bool
    {
        $path = BcUtil::getPluginPath($theme);
        if (!is_writable($path)) throw new BcException($path . ' に書込み権限がありません。');
        $folder = new BcFolder($path);
        if (!$folder->delete()) {
            return false;
        }
        return true;
    }

    /**
     * baserマーケットのテーマ一覧を取得する
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMarketThemes(): array
    {
        $bcOfficialApiService = $this->getService(BcOfficialApiServiceInterface::class);
        return $bcOfficialApiService->getRss('marketThemeRss');
    }

    /**
     * 指定したテーマをダウンロード用のテーマとして一時フォルダに作成する
     *
     * @param string $theme
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createDownloadToTmp(string $theme): string
    {
        $tmpDir = TMP . 'theme' . DS;
        if (!is_dir($tmpDir)) {
            $folder = new BcFolder($tmpDir);
            $folder->create();
        }
        $folder = new BcFolder(BcUtil::getPluginPath($theme));
        $folder->copy($tmpDir . $theme);
        return $tmpDir;
    }

    /**
     * 現在のDB内のデータをダウンロード用のCSVとして一時フォルダに作成する
     *
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createDownloadDefaultDataPatternToTmp(): string
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        // コアのCSVを生成
        $tmpDir = TMP . 'csv' . DS;
        $folder = new BcFolder($tmpDir);
        $folder->create();
        BcUtil::emptyFolder($tmpDir);
        BcUtil::clearAllCache();
        $excludes = ['plugins', 'dblogs', 'users'];
        // プラグインのCSVを生成
        $plugins = Plugin::loaded();
        foreach($plugins as $plugin) {
            (new BcFolder($tmpDir . $plugin))->create();
            BcUtil::emptyFolder($tmpDir . $plugin);
            $this->_writeCsv($plugin, $tmpDir . $plugin . DS, $excludes);
            $folder = new BcFolder($tmpDir . $plugin);
            $files = $folder->getFiles();
            $folders = $folder->getFolders();
            if (!$files && !$folders) {
                $folder->delete();
            } else {
                $pluginClass = Plugin::getCollection()->get($plugin);
                if(method_exists($pluginClass, 'modifyDownloadDefaultData')) {
                    $pluginClass->modifyDownloadDefaultData($tmpDir . $plugin . DS);
                }
            }
        }
        // site_configs 調整
        $this->_modifySiteConfigsCsv($tmpDir . 'BaserCore' . DS . 'site_configs.csv');
        return $tmpDir;
    }

    /**
     * site_configs テーブルにて、 CSVに出力しないフィールドを空にする
     *
     * @param string $path
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _modifySiteConfigsCsv(string $path)
    {
        $targets = ['email', 'google_analytics_id', 'version'];
        $fp = fopen($path, 'a+');
        $records = [];
        while(($record = BcUtil::fgetcsvReg($fp, 10240)) !== false) {
            if (in_array($record[1], $targets)) {
                $record[2] = '';
            }
            $records[] = '"' . implode('","', $record) . '"';
        }
        ftruncate($fp, 0);
        fwrite($fp, implode("\n", $records));
        return true;
    }

    /**
     * CSVファイルを書きだす
     *
     * @param string $configKeyName
     * @param string $path
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    protected function _writeCsv($plugin, $path, $exclude = [])
    {
        /** @var BcDatabaseService $dbService */
        $dbService = $this->getService(BcDatabaseServiceInterface::class);
        /* @var AppTable $appTable */
        $appTable = TableRegistry::getTableLocator()->get('BaserCore.App');
        /* @var \Cake\Database\Connection $db */
        $db = $appTable->getConnection();
        $tables = $db->getSchemaCollection()->listTables();
        $tableList = $dbService->getAppTableList();
        if (!isset($tableList[$plugin])) return true;
        $result = true;
        foreach($tables as $table) {
            if (in_array($table, $tableList[$plugin])) {
                if (in_array($table, $exclude)) continue;
                if (!$dbService->writeCsv($table, [
                    'path' => $path . $table . '.csv',
                    'encoding' => 'UTF-8',
                    'init' => false,
                    'plugin' => $plugin
                ])) {
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * 初期データチェックする
     *
     * @param string $theme
     * @param string $pattern
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function checkDefaultDataPattern($theme, $pattern = 'default')
    {
        $path = BcUtil::getDefaultDataPath($theme, $pattern);
        if (!$path) return false;
        $corePath = BcUtil::getDefaultDataPath(Configure::read('BcApp.coreFrontTheme'), 'default');

        $Folder = new BcFolder($corePath . DS . 'BaserCore');
        $files = $Folder->getFiles();
        $coreTables = $files;
        $Folder = new BcFolder($path . DS . 'BaserCore');
        $files = $Folder->getFiles();
        if (empty($files)) return false;
        $targetTables = $files;
        foreach($coreTables as $coreTable) {
            if (!in_array($coreTable, $targetTables)) {
                return false;
            }
        }
        return true;
    }

}
