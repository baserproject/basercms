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
use BaserCore\Utility\BcUtil;
use BcCustomContent\ServiceProvider\BcCustomContentServiceProvider;
use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use \Cake\Core\Plugin as CakePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Filesystem\Folder;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * plugin for BcCustomContent
 */
class Plugin extends BcPlugin
{

    /**
     * プラグインをインストールする
     *
     * @param array $options
     *  - `plugin` : プラグイン名
     *  - `connection` : コネクション名
     */
    public function install($options = []) : bool
    {
        // ここに必要なインストール処理を記述
        return parent::install($options);
    }

    /**
     * プラグインをアンインストールする
     *  - `plugin` : プラグイン名
     *  - `connection` : コネクション名
     *  - `target` : ロールバック対象バージョン
     */
    public function uninstall($options = []): bool
    {
        // ここに必要なアンインストール処理を記述
        return parent::uninstall();
    }

    /**
     * services
     * @param ContainerInterface $container
     * @noTodo
     * @checked
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcCustomContentServiceProvider());
    }

    /**
     * Bootstrap
     *
     * @param PluginApplicationInterface $application
     */
    public function bootstrap(PluginApplicationInterface $application): void
    {
        parent::bootstrap($application);
        $this->loadPlugin();
    }

    /**
     * カスタムコンテンツコアのプラグインをロードする
     *
     * @checked
     */
    public function loadPlugin(): void
    {
        // プラグインの配置パスを追加
        $path = CakePlugin::path('BcCustomContent') . 'plugins' . DS;
        Configure::write('App.paths.plugins', array_merge(
            Configure::read('App.paths.plugins'),
            [$path]
        ));

		$Folder = new Folder($path);
		$files = $Folder->read(true, true, false);
		if(empty($files[0])) return;

		if(Configure::read('BcRequest.asset')) {
		    // TODO ucmitz 検証要
			foreach($files[0] as $pluginName) {
			    BcUtil::includePluginClass($pluginName);
			}
		} else {
			foreach($files[0] as $pluginName) {
                // 設定ファイルを読み込む
                if(!BcUtil::includePluginClass($pluginName)) continue;
                $pluginCollection = CakePlugin::getCollection();
                $plugin = $pluginCollection->create($pluginName);
                $pluginCollection->add($plugin);
                BcEvent::registerPluginEvent($pluginName);
			}
		}
    }

}
