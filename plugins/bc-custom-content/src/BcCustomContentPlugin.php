<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcCustomContent;

use BaserCore\BcPlugin;
use BaserCore\Utility\BcEvent;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcUtil;
use BcCustomContent\ServiceProvider\BcCustomContentServiceProvider;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use \Cake\Core\Plugin as CakePlugin;
use Cake\Core\PluginApplicationInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\ORM\TableRegistry;

/**
 * plugin for BcCustomContent
 */
class BcCustomContentPlugin extends BcPlugin
{

    /**
     * プラグインをインストールする
     *
     * @param array $options
     *  - `plugin` : プラグイン名
     *  - `connection` : コネクション名
     * @checked
     * @noTodo
     */
    public function install($options = []): bool
    {
        $options = array_merge([
            'connection' => 'default'
        ], $options);
        $result = parent::install($options);
        if(Configure::read('BcEnv.isInstalled') && empty($options['db_init'])) {
            $table = TableRegistry::getTableLocator()->get('BcCustomContent.CustomEntries', ['connectionName' => $options['connection']]);
            $table->setUp(1);
            $this->updateDateNow('BcCustomContent.CustomEntries', ['published'], [], $options);
        }
        return $result;
    }

    /**
     * 初期データ読み込み時の更新処理
     * @param array $options
     * @return void
     */
    public function updateDefaultData($options = []) : void
    {
        // エントリーの公開日を更新
        $this->updateDateNow('BcCustomContent.CustomEntries', ['published'], [], $options);
    }

    /**
     * services
     * @param ContainerInterface $container
     * @checked
     * @noTodo
     * @unitTest
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcCustomContentServiceProvider());
    }

    /**
     * Bootstrap
     *
     * @param PluginApplicationInterface $app
     * @checked
     * @noTodo
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);
        $this->loadPlugin();
    }

    /**
     * カスタムコンテンツコアのプラグインをロードする
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function loadPlugin(): void
    {
        // プラグインの配置パスを追加
        $path = CakePlugin::path('BcCustomContent') . 'plugins' . DS;
        Configure::write('App.paths.plugins', array_merge(
            Configure::read('App.paths.plugins'),
            [$path]
        ));

        $Folder = new BcFolder($path);
        $files = $Folder->getFolders();
        if (empty($files)) return;

        foreach($files as $pluginName) {
            // 設定ファイルを読み込む
            if (!BcUtil::includePluginClass($pluginName)) continue;
            $pluginCollection = CakePlugin::getCollection();
            $plugin = $pluginCollection->create($pluginName);
            $pluginCollection->add($plugin);
            BcEvent::registerPluginEvent($pluginName);
        }
    }

}
