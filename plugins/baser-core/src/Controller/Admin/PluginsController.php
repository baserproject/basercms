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

use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Utility\Hash;

/**
 * Class PluginsController
 * @package BaserCore\Controller\Admin
 */
class PluginsController extends BcAdminAppController {

   /**
     * プラグインの一覧を表示する
     *
     * @return void
     */
	public function index() {

		// プラグインフォルダーのチェックを行う
		$pluginConfigs = [];
		$paths = App::path('plugins');
		foreach ($paths as $path) {
			$Folder = new Folder($path);
			$files = $Folder->read(true, true, true);
			foreach ($files[0] as $file) {
				$pluginConfigs[basename($file)] = $this->Plugins->getPluginConfig($file);
			}
		}

		$pluginConfigs = array_values($pluginConfigs); // Hash::sortの為、一旦キーを初期化
		$pluginConfigs = array_reverse($pluginConfigs); // Hash::sortの為、逆順に変更

		$availables = $unavailables = [];
		foreach ($pluginConfigs as $pluginInfo) {
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

        foreach($pluginConfigs as $plugin) {
            var_dump($plugin->toArray());
            }
		// 表示設定
		$this->set('datas', $pluginConfigs);
		$this->set('corePlugins', Configure::read('BcApp.corePlugins'));
		$this->set('sortmode', $sortmode);

		$this->setTitle(__d('baser', 'プラグイン一覧'));
		$this->setHelp('plugins_index');
	}

}
