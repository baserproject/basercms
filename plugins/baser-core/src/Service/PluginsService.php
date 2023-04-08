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
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Client;
use Cake\Http\Client\Exception\NetworkException;
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
     * @var \Cake\ORM\Table
     */
    public $Plugins;

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
        $options = ['permission' => $permission];
        if ($connection) {
            $options['connection'] = $connection;
        }
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
    public function update($pluginName, $connection = 'default'): ?bool
    {
        $options = ['connection' => $connection];
        BcUtil::includePluginClass($pluginName);

        if (function_exists('ini_set')) {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');
        }
        if (file_exists(LOGS . 'update.log')) {
            unlink(LOGS . 'update.log');
        }

        if ($pluginName === 'BaserCore') {
            $names = array_merge(['BaserCore'], Configure::read('BcApp.corePlugins'));
            $ids = $this->detachAll();
        } else {
            $names = [$pluginName];
        }

        TableRegistry::getTableLocator()->clear();
        BcUtil::clearAllCache();
        $pluginCollection = CakePlugin::getCollection();
        $plugins = [];

        // マイグレーション実行
        foreach($names as $name) {
            if ($name !== 'BaserCore') {
                $entity = $this->Plugins->getPluginConfig($name);
                if (!$entity->registered) continue;
            }
            $targetVersion = BcUtil::getVersion($name);
            BcUpdateLog::set(__d('baser_core', '{0} プラグイン {1} へのアップデートを開始します。', $name, $targetVersion));
            $plugin = $pluginCollection->create($name);
            $migrate = false;
            if (method_exists($plugin, 'migrate')) {
                $plugin->migrate($options);
                $migrate = true;
            }
            $plugins[$name] = [
                'instance' => $plugin,
                'migrate' => $migrate,
                'version' => $targetVersion
            ];
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
            BcUpdateLog::set(__d('baser_core', 'アップデート処理が途中で失敗しました。'));
            BcUpdateLog::set($e->getMessage());
            BcUtil::clearAllCache();
            BcUpdateLog::save();
            return false;
        }

        // バージョン番号更新
        try {
            $pluginsTable = TableRegistry::getTableLocator()->get('BaserCore.Plugins');
            foreach($plugins as $name => $plugin) {
                $pluginsTable->update($name, $plugin['version']);
                BcUpdateLog::set(__d('baser_core', '{0} プラグイン {1} へのアップデートが完了しました。', $name, $plugin['version']));
            }
        } catch (\Throwable $e) {
            foreach($plugins as $plugin) {
                if ($plugin['migrate']) {
                    $plugin['instance']->migrations->rollback($options);
                }
            }
            BcUpdateLog::set(__d('baser_core', 'アップデート処理が途中で失敗しました。'));
            BcUpdateLog::set($e->getMessage());
            BcUtil::clearAllCache();
            BcUpdateLog::save();
            return false;
        }

        $plugin['instance']->createAssetsSymlink();

        BcUtil::clearAllCache();
        BcUpdateLog::save();

        if ($pluginName === 'BaserCore') {
            $this->attachAllFromIds($ids);
        }

        return true;
    }

    /**
     * BaserCoreをアップデートする
     *
     * @param string $currentVersion
     * @param string $targetVersion
     * @param string $connection
     */
    public function updateCore(string $currentVersion, string $targetVersion, string $php, $connection = 'default')
    {
        // Composer 実行
        $command = ROOT . DS . 'bin' . DS . 'cake composer ' . $targetVersion . ' --php ' . $php;
        exec($command, $out, $code);
        if ($code !== 0) throw new BcException(__d('baser_core', 'プログラムファイルのアップデートに失敗しました。ログを確認してください。'));

        // マイグレーション、アップデートスクリプト実行、バージョン番号更新
        // マイグレーションファイルがプログラムに反映されないと実行できないため、別プロセスとして実行する
        $command = ROOT . DS . 'bin' . DS . 'cake update --connection ' . $connection;
        $out = $code = null;
        exec($command, $out, $code);
        if ($code !== 0) {
            // 失敗した場合は元のバージョンに戻す
            $command = ROOT . DS . 'bin' . DS . 'cake composer ' . $currentVersion;
            exec($command, $out, $code);
            if ($code !== 0) {
                throw new BcException(__d('baser_core', 'アップデートスクリプトの処理が失敗したので、プログラムファイルを元に戻そうとしましたが失敗しました。。ログを確認してください。'));
            } else {
                throw new BcException(__d('baser_core', 'アップデートスクリプトの処理が失敗したので、プログラムファイルを元に戻しました。。ログを確認してください。'));
            }
        }
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
        $plugin = $plugins->create($name);
        if (!$plugin->uninstall($options)) {
            throw new Exception(__d('baser_core', 'プラグインの削除に失敗しました。'));
        }
        if (!method_exists($plugin, 'uninstall')) {
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
        $srcName = basename($name, '.zip');
        $zip = new BcZip();
        if (!$zip->extract(TMP . $name, TMP)) {
            throw new BcException(__d('baser_core', 'アップロードしたZIPファイルの展開に失敗しました。'));
        }

        $dstName = Inflector::camelize($srcName);
        if (preg_match('/^(.+?)([0-9]+)$/', $dstName, $matches)) {
            $baseName = $matches[1];
            $num = $matches[2];
        } else {
            $baseName = $dstName;
            $num = null;
        }
        while(is_dir(BASER_PLUGINS . $dstName) || is_dir(BASER_THEMES . Inflector::dasherize($dstName))) {
            if (is_null($num)) {
                $num = 1;
            }
            $num++;
            $dstName = Inflector::camelize($baseName) . $num;
        }
        $folder = new Folder(TMP . $srcName);
        $folder->move(BASER_PLUGINS . $dstName, ['mode' => 0777]);
        unlink(TMP . $name);
        BcUtil::changePluginNameSpace($dstName);
        return $dstName;
    }

    /**
     * 取得可能なコアのバージョン情報を取得
     *
     * @return array
     */
    public function getAvailableCoreVersionInfo()
    {
        if(!BcSiteConfig::get('use_update_notice')) return [];

        $coreReleaseInfo = Cache::read('coreReleaseInfo', '_bc_update_');
        if (!$coreReleaseInfo) {
            $releaseUrl = Configure::read('BcApp.coreReleaseUrl');
            $http = new Client();
            try {
                $response = $http->get($releaseUrl);
            } catch (NetworkException $e) {
                return [];
            }
            $xml = Xml::build($response->getStringBody());
            if (isset($xml->channel->item)) {
                $latest = null;
                $versions = [];
                $currentVersion = BcUtil::getVersion();
                $major = preg_replace('/^([0-9]+\.).+?$/', "$1", $currentVersion);
                foreach($xml->channel->item as $item) {
                    if (!isset($item->guid)) continue;
                    if (preg_match('/baserproject\/baser-core ([0-9.]+)$/', $item->guid, $matches)) {
                        $version = $matches[1];
                        // 同じメジャーバージョンでない場合は無視
                        if (!preg_match('/^' . preg_quote($major) . '/', $version)) continue;
                        if (!$latest) {
                            $latest = $version;
                            $currentVerPoint = BcUtil::verpoint($currentVersion);
                            $latestVerPoint = BcUtil::verpoint($latest);
                            if($currentVerPoint > $latestVerPoint) break;
                        }
                        if ($currentVersion === $version) break;
                        $versions[] = $version;
                    }
                }
                $coreReleaseInfo = [
                    'latest' => $latest,
                    'versions' => $versions,
                ];
                Cache::write('coreReleaseInfo', $coreReleaseInfo, '_bc_update_');
                return $coreReleaseInfo;
            }
        } else {
            return $coreReleaseInfo;
        }
        return [];
    }

    /**
     * 利用可能なコアの最新のバーションを取得
     *
     * @return bool|mixed|string
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
     */
    public function isAvailableCoreUpdates()
    {
        $info = $this->getAvailableCoreVersionInfo();
        return isset($info['versions'])? $info['versions'] : [];
    }

}
