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
use BaserCore\Model\Entity\Plugin;
use BaserCore\Model\Table\PluginsTable;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcSiteConfig;
use BaserCore\Utility\BcUpdateLog;
use BaserCore\Utility\BcZip;
use Cake\Cache\Cache;
use Cake\Core\Exception\MissingPluginException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Filesystem\File;
use Cake\Http\Client;
use Cake\Http\Client\Exception\NetworkException;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\Core\Configure;
use BaserCore\Utility\BcUtil;
use Cake\Core\App;
use Cake\Filesystem\Folder;
use Cake\Core\Plugin as CakePlugin;
use Cake\Datasource\EntityInterface;
use Cake\Utility\Xml;
use Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;
use InvalidArgumentException;

/**
 * Class PluginsService
 * @property PluginsTable $Plugins
 */
class PluginsService implements PluginsServiceInterface
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Plugins Table
     * @var PluginsTable|\Cake\ORM\Table
     */
    public PluginsTable|Table $Plugins;

    /**
     * PluginsService constructor.
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function __construct()
    {
        $this->Plugins = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
    }

    /**
     * プラグインを取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->Plugins->get($id);
    }

    /**
     * プラグイン一覧を取得
     *
     * @param string $sortMode
     * @return array $plugins
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getIndex(string $sortMode): array
    {
        $plugins = $this->Plugins->find()
            ->order(['priority'])
            ->all()
            ->toArray();
        if ($sortMode) {
            return $plugins;
        } else {
            $registeredName = Hash::extract($plugins, '{n}.name');
            // DBに登録されてないもの含めて、プラグインフォルダから取得
            if (!$plugins) {
                $plugins = [];
            }
            $paths = App::path('plugins');
            foreach($paths as $path) {
                $Folder = new Folder($path);
                $files = $Folder->read(true, true, true);
                foreach($files[0] as $file) {
                    $name = Inflector::camelize(Inflector::underscore(basename($file)));
                    if (in_array(Inflector::camelize(basename($file), '-'), Configure::read('BcApp.core'))) continue;
                    if (in_array($name, $registeredName)) {
                        $plugins[array_search($name, $registeredName)] = $this->Plugins->getPluginConfig($name);
                    } else {
                        $plugin = $this->Plugins->getPluginConfig($name);
                        if ($plugin->isPlugin()) {
                            $plugins[] = $plugin;
                        }
                    }
                }
            }
            return $plugins;
        }
    }

    /**
     * プラグインをインストールする
     *
     * @param string $name プラグイン名
     * @param bool $permission アクセスルールを作るか作らないか
     * @param string $connection test connection指定用
     * @return bool|null
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function install($name, bool $permission = true, $connection = 'default'): ?bool
    {
        $dbInit = false;
        $config = $this->Plugins->getPluginConfig($name);
        if ($config) {
            $dbInit = $config->db_init;
        }
        $options = [
            'permission' => $permission,
            'connection' => $connection,
            'db_init' => $dbInit
        ];
        BcUtil::includePluginClass($name);
        $plugins = CakePlugin::getCollection();
        $plugin = $plugins->create($name);
        if (!method_exists($plugin, 'install')) {
            throw new Exception(__d('baser_core', 'プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。'));
        } else {
            return $plugin->install($options);
        }
    }

    /**
     * プラグインをアップデートする
     *
     * @param string $pluginName プラグイン名
     * @param string $connection コネクション名
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(string $name, string $connection = 'default'): ?bool
    {
        $options = ['connection' => $connection];
        BcUtil::clearAllCache();
        BcUtil::includePluginClass($name);

        if (function_exists('ini_set')) {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');
        }
        if (file_exists(LOGS . 'update.log')) {
            unlink(LOGS . 'update.log');
        }

        $ids = [];
        if ($name === 'BaserCore') {
            $pluginNames = array_merge(['BaserCore'], Configure::read('BcApp.corePlugins'));
            $ids = $this->detachAll();
        } else {
            $pluginNames = [$name];
        }

        TableRegistry::getTableLocator()->clear();
        BcUtil::clearAllCache();
        $pluginCollection = CakePlugin::getCollection();
        $plugins = [];

        // マイグレーション実行
        foreach($pluginNames as $pluginName) {
            if ($pluginName !== 'BaserCore') {
                $entity = $this->Plugins->getPluginConfig($pluginName);
                if (!$entity->registered) continue;
            }
            $targetVersion = BcUtil::getVersion($pluginName);
            BcUpdateLog::set(__d('baser_core', '{0} プラグイン {1} へのアップデートを開始します。', $pluginName, $targetVersion));
            $plugin = $pluginCollection->create($pluginName);
            $migrate = false;
            if (method_exists($plugin, 'migrate')) {
                try {
                    $plugin->migrate($options);
                } catch (\Throwable $e) {
                    if ($ids) $this->attachAllFromIds($ids);
                    BcUpdateLog::set(__d('baser_core', 'アップデート処理が途中で失敗しました。'));
                    BcUpdateLog::set($e->getMessage());
                    BcUtil::clearAllCache();
                    BcUpdateLog::save();
                    return false;
                }
                $migrate = true;
            }
            $plugins[$pluginName] = [
                'instance' => $plugin,
                'migrate' => $migrate,
                'version' => $targetVersion
            ];
        }

        if (!$plugins) {
            BcUpdateLog::set(__d('baser_core', '登録済のプラグインが見つかりませんでした。先にインストールを実行してください。'));
            BcUpdateLog::save();
            return false;
        }

        // アップデートスクリプト実行
        try {
            foreach($plugins as $plugin) {
                if (method_exists($plugin['instance'], 'execUpdater')) {
                    $plugin['instance']->execUpdater();
                }
            }
        } catch (\Throwable $e) {
            foreach($plugins as $plugin) {
                if ($plugin['migrate']) {
                    $plugin['instance']->migrations->rollback($options);
                }
            }
            if ($ids) $this->attachAllFromIds($ids);
            BcUpdateLog::set(__d('baser_core', 'アップデート処理が途中で失敗しました。'));
            BcUpdateLog::set($e->getMessage());
            BcUtil::clearAllCache();
            BcUpdateLog::save();
            return false;
        }

        // バージョン番号更新
        try {
            $pluginsTable = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
            foreach($plugins as $pluginName => $plugin) {
                $pluginsTable->update($pluginName, $plugin['version']);
                BcUpdateLog::set(__d('baser_core', '{0} プラグイン {1} へのアップデートが完了しました。', $pluginName, $plugin['version']));
            }
        } catch (\Throwable $e) {
            foreach($plugins as $plugin) {
                if ($plugin['migrate']) {
                    $plugin['instance']->migrations->rollback($options);
                }
            }
            if ($ids) $this->attachAllFromIds($ids);
            BcUpdateLog::set(__d('baser_core', 'アップデート処理が途中で失敗しました。'));
            BcUpdateLog::set($e->getMessage());
            BcUtil::clearAllCache();
            BcUpdateLog::save();
            return false;
        }

        $plugin['instance']->createAssetsSymlink();

        BcUtil::clearAllCache();
        BcUpdateLog::save();
        if ($ids) $this->attachAllFromIds($ids);

        return true;
    }

    /**
     * コアファイルをロールバックする
     *
     * @param string $currentVersion
     * @param string $php
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function rollbackCore(string $currentVersion, string $php): void
    {
        // 元のバージョンに戻す
        $command = $php . ' ' . ROOT . DS . 'bin' . DS . 'cake.php composer ' . $currentVersion;
        exec($command, $out, $code);
        if ($code !== 0) {
            throw new BcException(__d('baser_core', 'コアファイルを元に戻そうとしましたが失敗しました。ログを確認してください。'));
        }
    }

    /**
     * BaserCoreをアップデートする
     *
     * @param string $currentVersion
     * @param string $targetVersion
     * @param string $connection
     * @checked
     * @noTodo
     */
    public function updateCore($php, $connection = 'default')
    {
        $this->updateCoreFiles();

        // マイグレーション、アップデートスクリプト実行、バージョン番号更新
        // マイグレーションファイルがプログラムに反映されないと実行できないため、別プロセスとして実行する
        $command = $php . ' ' . ROOT . DS . 'bin' . DS . 'cake.php update --connection ' . $connection;
        $out = $code = null;
        exec($command, $out, $code);
        if ($code !== 0) {
            throw new BcException(__d('baser_core', 'マイグレーション処理が失敗しました。'));
        }
    }

    /**
     * コアファイルを更新
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function updateCoreFiles()
    {
        if (!is_dir(TMP . 'update' . DS . 'vendor')) {
            throw new BcException(__d('baser_core', 'ダウンロードした最新版が見つかりませんでした。'));
        }

        // バックアップ作成
        $zip = new BcZip();
        $zip->create(ROOT . DS . 'vendor', TMP . 'update' . DS . 'vendor.zip');

        // コアファイルを削除
        (new Folder())->delete(ROOT . DS . 'vendor');

        // 最新版に更新
        if (!(new Folder(TMP . 'update' . DS . 'vendor'))->copy(ROOT . DS . 'vendor')) {
            $zip->extract(TMP . 'update' . DS . 'vendor.zip', ROOT . DS . 'vendor');
            throw new BcException(__d('baser_core', '最新版のファイルをコピーできませんでした。'));
        }

        // composer.json, composer.lock を更新
        copy(TMP . 'update' . DS . 'composer.json', ROOT . DS . 'composer.json');
        copy(TMP . 'update' . DS . 'composer.lock', ROOT . DS . 'composer.lock');
    }

    /**
     * プラグインを全て無効化する
     *
     * @return array 無効化したIDのリスト
     * @checked
     * @noTodo
     * @unitTest
     */
    public function detachAll()
    {
        $plugins = $this->Plugins->find()->where(['status' => true])->all();
        $ids = [];
        if ($plugins) {
            foreach($plugins as $plugin) {
                $ids[] = $plugin->id;
                $plugin->status = false;
                $this->Plugins->save($plugin);
            }
        }
        return $ids;
    }

    /**
     * 複数のIDからプラグインを有効化する
     *
     * @param $ids
     * @checked
     * @noTodo
     * @unitTest
     */
    public function attachAllFromIds($ids)
    {
        if (!$ids) {
            return;
        }
        foreach($ids as $id) {
            $this->Plugins->save(new Plugin(['id' => $id, 'status' => true]));
        }
    }

    /**
     * バージョンを取得する
     *
     * @param $name
     * @return mixed|string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getVersion($name)
    {
        $plugin = $this->Plugins->find()->where(['name' => $name])->first();
        if ($plugin) {
            return $plugin->version;
        } else {
            return '';
        }
    }

    /**
     * プラグインを無効にする
     *
     * @param string $name
     * @checked
     * @noTodo
     * @unitTest
     */
    public function detach(string $name): bool
    {
        return $this->Plugins->detach($name);
    }

    /**
     * プラグインを有効にする
     *
     * @param string $name
     * @checked
     * @noTodo
     * @unitTest PluginsTable::attach() のテストに委ねる
     */
    public function attach(string $name): bool
    {
        return $this->Plugins->attach($name);
    }

    /**
     * プラグイン名からプラグインエンティティを取得
     *
     * @param string $name
     * @return array|EntityInterface|null
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getByName(string $name)
    {
        return $this->Plugins->find()->where(['name' => $name])->first();
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
    public function resetDb(string $name, $connection = 'default'): void
    {
        $options = ['connection' => $connection];
        unset($options['name']);
        $plugin = $this->Plugins->find()
            ->where(['name' => $name])
            ->first();

        BcUtil::includePluginClass($plugin->name);
        $plugins = CakePlugin::getCollection();
        $pluginClass = $plugins->create($plugin->name);
        if (!method_exists($pluginClass, 'rollbackDb')) {
            throw new Exception(__d('baser_core', 'プラグインに Plugin クラスが存在しません。手動で削除してください。'));
        }

        $plugin->db_init = false;
        if (!$pluginClass->rollbackDb($options) || !$this->Plugins->save($plugin)) {
            throw new Exception(__d('baser_core', '処理中にエラーが発生しました。プラグインの開発者に確認してください。'));
        }

        // アクセスルールを削除する
        /** @var PermissionGroupsService $permissionGroupsService */
        $permissionGroupsService = $this->getService(PermissionGroupsServiceInterface::class);
        $permissionGroupsService->deleteByPlugin($plugin->name);

        BcUtil::clearAllCache();
    }

    /**
     * プラグインを削除する
     *
     * @param string $name
     * @param array $connection
     * @checked
     * @noTodo
     * @unitTest
     */
    public function uninstall(string $name, $connection = 'default'): void
    {
        $options = ['connection' => $connection];
        $name = rawurldecode($name);
        BcUtil::includePluginClass($name);
        $plugins = CakePlugin::getCollection();
        try {
            $plugin = $plugins->create($name);
            if (!$plugin->uninstall($options)) {
                throw new Exception(__d('baser_core', 'プラグインの削除に失敗しました。'));
            }
            if (!method_exists($plugin, 'uninstall')) {
                throw new Exception(__d('baser_core', 'プラグインに Plugin クラスが存在しません。手動で削除してください。'));
            }
        } catch (MissingPluginException) {
            throw new Exception(__d('baser_core', 'プラグインに Plugin クラスが存在しません。手動で削除してください。'));
        }
    }

    /**
     * 優先度を変更する
     *
     * @param int $id
     * @param int $offset
     * @param array $conditions
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function changePriority(int $id, int $offset, array $conditions = []): bool
    {
        $result = $this->Plugins->changeSort($id, $offset, [
            'conditions' => $conditions,
            'sortFieldName' => 'priority',
        ]);
        return $result;
    }

    /**
     * baserマーケットのプラグイン一覧を取得する
     *
     * @return array|mixed
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getMarketPlugins(): array
    {
        $bcOfficialApiService = $this->getService(BcOfficialApiServiceInterface::class);
        return $bcOfficialApiService->getRss('marketPluginRss');
    }

    /**
     * インストールに関するメッセージを取得する
     *
     * @param $pluginName
     * @return string
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getInstallStatusMessage($pluginName): string
    {
        $pluginName = rawurldecode($pluginName);
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
     * 一括処理
     *
     * @param array $ids
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function batch(string $method, array $ids): bool
    {
        if (!$ids) return true;
        $db = $this->Plugins->getConnection();
        $db->begin();
        foreach($ids as $id) {
            try {
                $plugin = $this->Plugins->get($id);
            } catch (RecordNotFoundException $e) {
                continue;
            }
            if (!$this->$method($plugin->name)) {
                $db->rollback();
                throw new BcException(__d('baser_core', 'データベース処理中にエラーが発生しました。'));
            }
        }
        $db->commit();
        return true;
    }

    /**
     * IDを指定して名前リストを取得する
     *
     * @param $ids
     * @return array
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getNamesById($ids): array
    {
        return $this->Plugins->find('list')->where(['id IN' => $ids])->toArray();
    }

    /**
     * プラグインをアップロードする
     *
     * POSTデータにて キー`file` で Zipファイルをアップロードとすると、
     * /plugins/ 内に、Zipファイルを展開して配置する。
     *
     * ### エラー
     * post_max_size　を超えた場合、サーバーに設定されているサイズ制限を超えた場合、
     * Zipファイルの展開に失敗した場合は、Exception を発生。
     *
     * ### リネーム処理
     * 展開後のフォルダー名はアッパーキャメルケースにリネームする。
     * 既に /plugins/ 内に同名のプラグインが存在する場合には、数字付きのディレクトリ名（PluginName2）にリネームする。
     * 数字付きのディレクトリ名にリネームする際、プラグイン内の Plugin クラスの namespace もリネームする。
     *
     * @param array $postData
     * @return string Zip を展開したフォルダ名
     * @checked
     * @noTodo
     * @unitTest
     * @throws BcException
     */
    public function add(array $postData)
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
        $zip = new BcZip();
        if (!$zip->extract(TMP . $name, TMP)) {
            throw new BcException(__d('baser_core', 'アップロードしたZIPファイルの展開に失敗しました。'));
        }
        $srcDirName = $zip->topArchiveName;
        $dstName = $srcName = Inflector::camelize($srcDirName);
        if (preg_match('/^(.+?)([0-9]+)$/', $srcName, $matches)) {
            $baseName = $matches[1];
            $num = $matches[2];
        } else {
            $baseName = $srcName;
            $num = null;
        }
        while(is_dir(BASER_PLUGINS . $dstName) || is_dir(BASER_THEMES . Inflector::dasherize($dstName))) {
            if (is_null($num)) $num = 1;
            $num++;
            $dstName = $baseName . $num;
        }
        $folder = new Folder(TMP . $srcDirName);
        $folder->move(BASER_PLUGINS . $dstName, ['mode' => 0777]);
        unlink(TMP . $name);
        BcUtil::changePluginClassName($srcName, $dstName);
        BcUtil::changePluginNameSpace($dstName);
        return $dstName;
    }

    /**
     * 取得可能なコアのバージョン情報を取得
     *
     * `BcApp.coreReleaseUrl` で、Packagist よりリリース情報を取得し、
     * キャッシュ `_bc_update_` の `coreReleaseInfo` として保存する。
     *
     * アップデート対象バージョンの取得条件
     * - 同じメジャーバージョンであること
     * - 現在のパッケージののバージョンが開発版でないこと
     * - アップデートバージョンが現在のパッケージのバージョンより大きいこと
     *
     * @return array
     *  - `latest`: 最新バージョン
     *  - `versions`: 取得可能なコアのバージョンリスト
     * @checked
     * @noTodo
     */
    public function getAvailableCoreVersionInfo()
    {
        if (!BcSiteConfig::get('use_update_notice')) return [];

        $coreReleaseInfo = Cache::read('coreReleaseInfo', '_bc_update_');
        if (!$coreReleaseInfo) {
            $releaseUrl = Configure::read('BcApp.coreReleaseUrl');
            $http = new Client();
            try {
                $response = $http->get($releaseUrl);
                $body = $response->getStringBody();
            } catch (InvalidArgumentException $e) {
                // ユニットテストの場合にhttpでアクセスできないので、ファイルから直接読み込む
                $file = new File($releaseUrl);
                $body = $file->read();
            } catch (NetworkException $e) {
                return [];
            }
            $xml = Xml::build($body);
            $latest = null;
            $versions = [];
            $currentVersion = BcUtil::getVersion();
            if (isset($xml->channel->item)) {
                $major = preg_replace('/^([0-9]+\.).+?$/', "$1", $currentVersion);
                foreach($xml->channel->item as $item) {
                    if (!isset($item->guid)) continue;
                    if (preg_match('/baserproject\/baser-core ([0-9.]+)$/', $item->guid, $matches)) {
                        $version = $matches[1];
                        if (!$latest) {
                            $latest = $version;
                        }
                        // 同じメジャーバージョンでない場合は無視
                        if (!preg_match('/^' . preg_quote($major) . '/', $version)) continue;

                        $currentVerPoint = BcUtil::verpoint($currentVersion);
                        $latestVerPoint = BcUtil::verpoint($latest);
                        // 現在のパッケージが開発版の場合は無視
                        if ($currentVerPoint === false) break;
                        // アップデートバージョンが開発版の場合は無視
                        if ($latestVerPoint === false) continue;
                        // アップデートバージョンが現在のパッケージのバージョンより小さい場合は無視
                        if ($currentVerPoint > $latestVerPoint) break;

                        if ($currentVersion === $version) break;
                        $versions[] = $version;
                    }
                }
            }
            arsort($versions);
            $coreReleaseInfo = [
                'latest' => $latest,
                'versions' => $versions,
            ];
            Cache::write('coreReleaseInfo', $coreReleaseInfo, '_bc_update_');
            return $coreReleaseInfo;
        } else {
            return $coreReleaseInfo;
        }
    }

    /**
     * コアの最新版を取得する
     * tmp/update に最新版をダウンロードする
     * @param string $targetVersion
     * @param string $php
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getCoreUpdate(string $targetVersion, string $php, ?bool $force = false)
    {
        if (function_exists('ini_set')) {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');
        }
        if (file_exists(LOGS . 'update.log')) {
            unlink(LOGS . 'update.log');
        }

        if (!is_dir(TMP . 'update')) {
            mkdir(TMP . 'update', 0777);
        }
        if (!is_dir(TMP . 'update' . DS . 'vendor')) {
            $folder = new Folder(ROOT . DS . 'vendor');
            $folder->copy(TMP . 'update' . DS . 'vendor');
        }
        copy(ROOT . DS . 'composer.json', TMP . 'update' . DS . 'composer.json');
        copy(ROOT . DS . 'composer.lock', TMP . 'update' . DS . 'composer.lock');

        // Composer 実行
        $command = $php . ' ' . ROOT . DS . 'bin' . DS . 'cake.php composer ' . $targetVersion . ' --php ' . $php . ' --dir ' . TMP . 'update';
        if ($force) {
            $command .= ' --force true';
        }

        exec($command, $out, $code);
        if ($code !== 0) throw new BcException(__d('baser_core', '最新版のダウンロードに失敗しました。ログを確認してください。'));

        Cache::write('coreDownloaded', true, '_bc_update_');
    }

    /**
     * 利用可能なコアの最新のバーションを取得
     *
     * @return bool|mixed|string
     * @checked
     * @noTodo
     */
    public function getAvailableCoreVersion()
    {
        $info = $this->getAvailableCoreVersionInfo();
        return isset($info['latest'])? $info['latest'] : BcUtil::getVersion();
    }

    /**
     * 利用可能なコアのバージョン群を取得
     *
     * @return array|mixed
     * @checked
     * @noTodo
     */
    public function isAvailableCoreUpdates()
    {
        $info = $this->getAvailableCoreVersionInfo();
        return isset($info['versions'])? $info['versions'] : [];
    }

}
