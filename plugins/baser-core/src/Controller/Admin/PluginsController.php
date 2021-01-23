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

namespace BaserCore\Controller\Admin;

use BaserCore\Utility\BcUtil;
use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Utility\Hash;
use Cake\Utility\Xml;

/**
 * Class PluginsController
 * @package BaserCore\Controller\Admin
 */
class PluginsController extends BcAdminAppController
{

    /**
     * プラグインの一覧を表示する
     *
     * @return void
     */
    public function index()
    {

        // プラグインフォルダーのチェックを行う
        $pluginConfigs = [];
        $paths = App::path('plugins');
        foreach($paths as $path) {
            $Folder = new Folder($path);
            $files = $Folder->read(true, true, true);
            foreach($files[0] as $file) {
                if(!in_array(basename($file), Configure::read('BcApp.core'))) {
                    $pluginConfigs[basename($file)] = $this->Plugins->getPluginConfig($file);
                }
            }
        }

        $pluginConfigs = array_values($pluginConfigs); // Hash::sortの為、一旦キーを初期化
        $pluginConfigs = array_reverse($pluginConfigs); // Hash::sortの為、逆順に変更

        $availables = $unavailables = [];
        foreach($pluginConfigs as $pluginInfo) {
            if (isset($pluginInfo['Plugin']['priority'])) {
                $availables[] = $pluginInfo;
            } else {
                $unavailables[] = $pluginInfo;
            }
        }

        //並び替えモードの場合はDBにデータが登録されていないプラグインを表示しない
        // TODO 未実装
//		if (!empty($this->passedArgs['sortmode'])) {
//			$sortmode = true;
//			$pluginConfigs = Hash::sort($availables, '{n}.Plugin.priority', 'asc', 'numeric');
//		} else {
        $sortmode = false;
        $pluginConfigs = array_merge(Hash::sort(
            $availables,
            '{n}.Plugin.priority',
            'asc',
            'numeric'), $unavailables);
//		}

        // 表示設定
        $this->set('plugins', $pluginConfigs);
        $this->set('corePlugins', Configure::read('BcApp.corePlugins'));
        $this->set('sortmode', $sortmode);

        $this->setTitle(__d('baser', 'プラグイン一覧'));
        $this->setHelp('plugins_index');
    }


    /**
     * baserマーケットのプラグインデータを取得する
     *
     * @return void
     */
    public function admin_ajax_get_market_plugins()
    {
        return false;
        // TODO 実装要
        $cachePath = 'views' . DS . 'baser_market_plugins.rss';
        if (Configure::read('debug') > 0) {
            clearCache('baser_market_plugins', 'views', '.rss');
        }
        $baserPlugins = cache($cachePath);
        if ($baserPlugins) {
            $baserPlugins = BcUtil::unserialize($baserPlugins);
            $this->set('baserPlugins', $baserPlugins);
            return;
        }

        $Xml = new Xml();
        try {
            $baserPlugins = $Xml->build(Configure::read('BcApp.marketPluginRss'));
        } catch (BcException $ex) {

        }
        if ($baserPlugins) {
            $baserPlugins = $Xml->toArray($baserPlugins->channel);
            $baserPlugins = $baserPlugins['channel']['item'];
            cache($cachePath, BcUtil::serialize($baserPlugins));
            chmod(CACHE . $cachePath, 0666);
        } else {
            $baserPlugins = [];
        }
        $this->set('baserPlugins', $baserPlugins);
    }

}
