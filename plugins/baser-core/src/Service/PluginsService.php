<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use BaserCore\Model\Table\PluginsTable;
use Cake\Cache\Cache;
use Cake\Http\Client;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Core\Configure;
use BaserCore\Utility\BcUtil;
use Cake\Core\App;
use Cake\Filesystem\Folder;
use BaserCore\Model\Entity\Plugin;
use Cake\Core\Plugin as CakePlugin;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Utility\Xml;
use Exception;

/**
 * Class PluginsService
 * @package BaserCore\Service
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
     * ユーザー一覧を取得
     * @param string $sortMode
     * @return array $plugins
     * @checked
     * @unitTest
     */
    public function getIndex(string $sortMode): array
    {
        if($sortMode) {
            // DBに登録されてる場合
            $registered = $this->Plugins->find()
                ->order(['priority'])
                ->all()
                ->toArray();
            return $registered;
        } else {
            // DBに登録されてないもの含めて、プラグインフォルダから取得
            // TODO: チェック必要
            $paths = App::path('plugins');
            $pluginConfigs = [];
            foreach($paths as $path) {
                $Folder = new Folder($path);
                $files = $Folder->read(true, true, true);
                foreach($files[0] as $file) {
                    $name = Inflector::camelize(Inflector::underscore(basename($file)));
                    if (!in_array(basename($file), Configure::read('BcApp.core'))) {
                        $pluginConfigs[$name] = $this->Plugins->getPluginConfig($name);
                    }
                }
            }
            return array_values($pluginConfigs);
        }
    }

    /**
     * プラグインをインストールする
     * @param string $name プラグイン名
     * @return bool|null
     * @param string $data test connection指定用
     * @checked
     * @noTodo
     * @unitTest
     */
    public function install($name, $data = []): ?bool
    {
        BcUtil::includePluginClass($name);
        $plugins = CakePlugin::getCollection();
        $plugin = $plugins->create($name);

        if (!method_exists($plugin, 'install')) {
            return null;
        } else {
            return $plugin->install($data);
        }
    }

    /**
     * プラグインを無効にする
     * @param string $name
     * @checked
     * @noTodo
     * @unitTest
     */
    public function detach(string $name):bool
    {
        return $this->Plugins->detach($name);
    }

    /**
     * プラグイン名からプラグインエンティティを取得
     * @param string $name
     * @return array|EntityInterface|null
     */
    public function getByName(string $name)
    {
        return $this->Plugins->find()->where(['name' => $name])->first();
    }

    /**
     * データベースをリセットする
     *
     * @param string $name
     * @param array $options
     * @throws Exception
     */
    public function resetDb(string $name, $options = []):void
    {
        $options = array_merge([
            'connection' => 'default'
        ], $options);
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
     * @param array $options
     * @checked
     * @noTodo
     * @unitTest
     */
    public function uninstall(string $name, array $options = []): void
    {
        $options = array_merge([
            'connection' => 'default'
        ], $options);
        $name = urldecode($name);
        BcUtil::includePluginClass($name);
        $plugins = CakePlugin::getCollection();
        $plugin = $plugins->create($name);
        if (!method_exists($plugin, 'uninstall')) {
            throw new Exception(__d('baser', 'プラグインに Plugin クラスが存在しません。手動で削除してください。'));
        }
        if (!$plugin->uninstall($options)) {
            throw new Exception(__d('baser', 'プラグインの削除に失敗しました。'));
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
        BcUtil::clearAllCache();
        return $result;
    }

    /**
     * baserマーケットのプラグイン一覧を取得する
     * @return array|mixed
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
                    'host' => ''
                ]);
                $response = $client->get(Configure::read('BcApp.marketPluginRss'));
                if ($response->getStatusCode() !== 200) {
                    return [];
                }
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

}
