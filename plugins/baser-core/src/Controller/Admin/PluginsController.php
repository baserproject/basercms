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

use BaserCore\Error\BcException;
use BaserCore\Model\Table\PluginsTable;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Utility\Hash;

/**
 * Class PluginsController
 * @package BaserCore\Controller\Admin
 * @property PluginsTable $Plugins
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

        $plugins = $this->Plugins->getAvailable();
        $available = $unavailable = [];
        foreach($plugins as $pluginInfo) {
            if (isset($pluginInfo['Plugin']['priority'])) {
                $available[] = $pluginInfo;
            } else {
                $unavailable[] = $pluginInfo;
            }
        }

        //並び替えモードの場合はDBにデータが登録されていないプラグインを表示しない
        // TODO 未実装
//		if (!empty($this->passedArgs['sortmode'])) {
//			$sortmode = true;
//			$pluginConfigs = Hash::sort($availables, '{n}.Plugin.priority', 'asc', 'numeric');
//		} else {
        $sortmode = false;

        $plugins = array_merge(Hash::sort(
            $available,
            '{n}.Plugin.priority',
            'asc',
            'numeric'), $unavailable);
//		}

        $this->set('plugins', $plugins);
        $this->set('corePlugins', Configure::read('BcApp.corePlugins'));
        $this->set('sortmode', $sortmode);
        $this->setTitle(__d('baser', 'プラグイン一覧'));
        $this->setHelp('plugins_index');
    }

    /**
     * インストール
     *
     * @param string $name プラグイン名
     * @return void
     */
    public function install($name)
    {
        $name = urldecode($name);
        $installMessage = '';

        try {
            if ($this->Plugins->isInstallable($name)) {
                $isInstallable = true;
            }
        } catch (BcException $e) {
            $isInstallable = false;
            $installMessage = $e->getMessage();
        }

        if ($isInstallable && $this->request->is('post')) {
            // プラグインをインストール
            BcUtil::includePluginClass($name);
            $plugins = Plugin::getCollection();
            $plugin = $plugins->create($name);
            if ($plugin->install()) {
                $this->BcMessage->setSuccess(sprintf(__d('baser', '新規プラグイン「%s」を baserCMS に登録しました。'), $name));
                // TODO: アクセス権限を追加する
                // $this->_addPermission($this->request->data);
                $this->redirect(['action' => 'index']);
            } else {
                $this->BcMessage->setError(__d('baser', 'プラグインに問題がある為インストールを完了できません。プラグインの開発者に確認してください。'));
            }
        }

        $pluginEntity = $this->Plugins->getPluginConfig($name);
        $this->set('installMessage', $installMessage);
        $this->set('isInstallable', $isInstallable);
        $this->set('dbInited', $pluginEntity->db_inited);
        $this->set('plugin', $pluginEntity);
        $this->setTitle(__d('baser', '新規プラグイン登録'));
        $this->setHelp('plugins_install');
    }


    /**
     * アンインストール
     *
     * @param string $name プラグイン名
     * @return void
     */
    public function uninstall($name)
    {
        $name = urldecode($name);
        if (!$this->request->is('post')) {
            $this->notfound();
        }

        $plugins = Plugin::getCollection();
        $plugin = $plugins->get($name);
        $plugin->uninstall();

        if ($plugin->uninstall()) {
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'プラグイン「%s」を削除しました。'), $name));
        } else {
            $this->BcMessage->setError(__d('baser', 'プラグインの削除に失敗しました。'));
        }
    }

    /**
     * 無効化
     *
     * @param string $name プラグイン名
     * @return void
     */
    public function detouch($name) {
        $name = urldecode($name);
        if (!$this->request->is('post')) {
            $this->notfound();
        }

        if ($this->Plugins->detouch($name)) {
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'プラグイン「%s」を無効にしました。'), $name));
        } else {
            $this->BcMessage->setError(__d('baser', 'プラグインの無効化に失敗しました。'));
        }
        $this->redirect(['action' => 'index']);
    }





}
