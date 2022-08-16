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

use BaserCore\Model\Entity\Plugin;
use BaserCore\Model\Table\PluginsTable;
use Cake\Cache\Cache;
use Cake\Http\Client;
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
     * Plugins Table
     * @var \Cake\ORM\Table
     */
    public $Plugins;

    /**
     * PluginsService constructor.
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
                    if (in_array(basename($file), Configure::read('BcApp.core'))) continue;
                    if(in_array($name, $registeredName)) {
                        $plugins[array_search($name, $registeredName)] = $this->Plugins->getPluginConfig($name);
                    } else {
                        $plugins[] = $this->Plugins->getPluginConfig($name);
                    }
                }
            }
            return $plugins;
        }
    }

    /**
     * プラグインをインストールする
     * @param string $name プラグイン名
     * @param string $connection test connection指定用
     * @return bool|null
     * @throws Exception
     * @checked
     * @noTodo
     * @unitTest
     */
    public function install($name, $connection = 'default'): ?bool
    {
        $options = ['connection' => $connection];
        BcUtil::includePluginClass($name);
        $plugins = CakePlugin::getCollection();
        $plugin = $plugins->create($name);
        if (!method_exists($plugin, 'install')) {
            throw new Exception(__d('baser', 'プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。'));
        } else {
            return $plugin->install($options);
        }
    }

    /**
     * プラグインをアップデートする
     * @param string $name プラグイン名
     * @param string $connection コネクション名
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update($name, $connection = 'default'): ?bool
    {
        $options = ['connection' => $connection];
        BcUtil::includePluginClass($name);

        if (function_exists('ini_set')) {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');
        }
        if(file_exists(LOGS . 'update.log')) {
            unlink(LOGS . 'update.log');
        }

        if ($name === 'BaserCore') {
            $names = array_merge(['BaserCore'], Configure::read('BcApp.corePlugins'));
            $ids = $this->detachAll();
        } else {
            $names = [$name];
        }

        $result = true;
        $pluginCollection = CakePlugin::getCollection();
        foreach($names as $name) {
            if($name !== 'BaserCore') {
                $entity = $this->Plugins->getPluginConfig($name);
                if(!$entity->registered) continue;
            }
            $plugin = $pluginCollection->create($name);
            if (!method_exists($plugin, 'update')) {
                throw new Exception(__d('baser', 'プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。'));
            } else {
                if(!$plugin->update($options)) {
                    $result = false;
                }
            }
        }

        if ($name === 'BaserCore') {
            $this->attachAllFromIds($ids);
        }

        return $result;
    }

    /**
     * プラグインを全て無効化する
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
     * プラグイン名からプラグインエンティティを取得
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
            throw new Exception(__d('baser', 'プラグインに Plugin クラスが存在しません。手動で削除してください。'));
        }

        $plugin->db_init = false;
        if (!$pluginClass->rollbackDb($options) || !$this->Plugins->save($plugin)) {
            throw new Exception(__d('baser', '処理中にエラーが発生しました。プラグインの開発者に確認してください。'));
        }
        BcUtil::clearAllCache();
    }

    /**
     * プラグインを削除する
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
            throw new Exception(__d('baser', 'プラグインの削除に失敗しました。'));
        }
        if (!method_exists($plugin, 'uninstall')) {
            throw new Exception(__d('baser', 'プラグインに Plugin クラスが存在しません。手動で削除してください。'));
        }
    }

    /**
     * 優先度を変更する
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
        $result = $this->Plugins->changePriority($id, $offset, $conditions);
        return $result;
    }

    /**
     * baserマーケットのプラグイン一覧を取得する
     * @return array|mixed
     * @checked
     * @unitTest
     * @noTodo
     */
    public function getMarketPlugins(): array
    {
        if (Configure::read('debug') > 0) {
            Cache::delete('baserMarketPlugins');
        }
        if (!($baserPlugins = Cache::read('baserMarketPlugins', '_bc_env_'))) {
            $Xml = new Xml();
            try {
                $client = new Client([
                    'host' => '',
                    'redirect' => true,
                ]);
                $response = $client->get(Configure::read('BcLinks.marketPluginRss'));
                $baserPlugins = $Xml->build($response->getBody()->getContents());
                $baserPlugins = $Xml->toArray($baserPlugins->channel);
                $baserPlugins = $baserPlugins['channel']['item'];
            } catch (Exception $e) {
                return [];
            }
            Cache::write('baserMarketPlugins', $baserPlugins, '_bc_env_');
        }
        if ($baserPlugins) {
            return $baserPlugins;
        }
        return [];
    }

    /**
     * ユーザーグループにアクセス許可設定を追加する
     *
     * @param array $data リクエストデータ
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function allow($data): void
    {
        $permissions = TableRegistry::getTableLocator()->get('BaserCore.Permissions');
        $userGroups = $permissions->UserGroups->find('all')->where(['UserGroups.id <>' => Configure::read('BcApp.adminGroupId')]);
        if (!$userGroups) {
            return;
        }

        foreach($userGroups as $userGroup) {

            $permissionAuthPrefix = $permissions->UserGroups->getAuthPrefix($userGroup->id);
            $url = '/baser/' . $permissionAuthPrefix . '/' . Inflector::underscore($data['name']) . '/*';

            $prePermissions = $permissions->find()->where(['url' => $url])->first();
            switch($data['permission']) {
                case 1:
                    if (!$prePermissions) {
                        $permission = $permissions->newEmptyEntity();
                        $permission->name = $data['title'] . ' ' . __d('baser', '管理');
                        $permission->user_group_id = $userGroup->id;
                        $permission->auth = 1;
                        $permission->status = 1;
                        $permission->url = $url;
                        $permission->no = $permissions->getMax('no', ['user_group_id' => $userGroup->id]) + 1;
                        $permission->sort = $permissions->getMax('sort', ['user_group_id' => $userGroup->id]) + 1;
                        $permissions->save($permission);
                    }
                    break;
                case 2:
                    if ($prePermissions) {
                        $permissions->delete($prePermissions->id);
                    }
                    break;
            }
        }
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

}
