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

namespace BaserCore\Controller\Admin;

use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Error\BcException;
use BaserCore\Model\Table\PluginsTable;
use BaserCore\Service\Admin\PluginsAdminServiceInterface;
use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PluginsController
 * @package BaserCore\Controller\Admin
 * @property PluginsTable $Plugins
 * @property BcMessageComponent $BcMessage
 */
class PluginsController extends BcAdminAppController
{

    /**
     * モデル
     *
     * @var array
     */
    public $uses = ['BaserCore.Plugin'];

    /**
     * initialize
     * @throws \Exception
     * @checked
     * @unitTest
     * @noTodo
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Before Filter
     * @param \Cake\Event\EventInterface $event An Event instance
     * @checked
     * @unitTest
     * @noTodo
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Security->setConfig('unlockedActions', ['reset_db', 'update_sort', 'batch']);
        if(Configure::read('BcRequest.isUpdater')) $this->Authentication->allowUnauthenticated(['update']);
    }

    /**
     * プラグインの一覧を表示する
     * @param PluginsServiceInterface $PluginsService
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function index(PluginsServiceInterface $PluginsService)
    {
        $this->set('plugins', $PluginsService->getIndex($this->request->getQuery('sortmode') ?? '0'));
    }

    /**
     * インストール
     *
     * @param string $name プラグイン名
     * @return Response|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function install(PluginsAdminServiceInterface $PluginsService, $name)
    {
        $this->set($PluginsService->getViewVarsForInstall($this->Plugins->getPluginConfig($name)));
        if ($PluginsService->getInstallStatusMessage($name) || !$this->request->is(['put', 'post'])) {
            return;
        } else {
            try {
                if ($PluginsService->install($name, $this->request->getData('connection') ?? 'default')) {
                    $PluginsService->allow($this->request->getData());
                    $this->BcMessage->setSuccess(sprintf(__d('baser', '新規プラグイン「%s」を baserCMS に登録しました。'), $name));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->BcMessage->setError(__d('baser', 'プラグインに問題がある為インストールを完了できません。プラグインの開発者に確認してください。'));
                }
            } catch (\Exception $e) {
                $this->BcMessage->setError($e->getMessage());
            }
        }
    }

	/**
	 * アップデート実行
     * @param PluginsServiceInterface $pluginsService
     * @param string $name
     * @checked
     * @noTodo
     * @unitTest
	 */
	public function update(PluginsAdminServiceInterface $pluginsService, $name = ''): void
	{
        BcUtil::clearAllCache();
        $plugin = $this->Plugins->getPluginConfig($name);
        $this->set($pluginsService->getViewVarsForUpdate($plugin));
        if (!$this->request->is(['put', 'post'])) return;
        try {
            $pluginsService->update($plugin->name, $this->request->getData('connection') ?? 'default');
            if($plugin->name === 'BaserCore') {
                $this->BcMessage->setInfo(__d('baser', '全てのアップデート処理が完了しました。 {0} にログを出力しています。', LOGS . 'update.log'));
                $this->redirect('/');
            } else {
                $this->BcMessage->setInfo(__d('baser', 'アップデート処理が完了しました。画面下部のアップデートログを確認してください。'));
                $this->redirect(['action' => 'update', $name]);
            }
        } catch (\Exception $e) {
            $this->BcMessage->setError($e->getMessage());
        }
	}

    /**
     * 無効化
     *
     * @param string $name プラグイン名
     * @checked
     * @noTodo
     * @unitTest
     */
    public function detach(PluginsServiceInterface $pluginService, $name)
    {
        if (!$this->request->is('post')) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        if ($pluginService->detach(rawurldecode($name))) {
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'プラグイン「%s」を無効にしました。'), rawurldecode($name)));
        } else {
            $this->BcMessage->setError(__d('baser', 'プラグインの無効化に失敗しました。'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * アンインストール
     *
     * - プラグインのテーブルを削除
     * - プラグインのディレクトリを削除
     *
     * @param string $name プラグイン名
     * @return Response|void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function uninstall(PluginsServiceInterface $pluginService, $name)
    {
        if (!$this->request->is('post')) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        try {
            $pluginService->uninstall(rawurldecode($name), $this->request->getData('connection') ?? 'default');
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'プラグイン「%s」を削除しました。'), $name));
        } catch (\Exception $e) {
            $this->BcMessage->setError(__d('baser', 'プラグインの削除に失敗しました。' . $e->getMessage()));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * プラグインをアップロードする
     * @checked
     * @noTodo
     */
    public function add(PluginsServiceInterface $service)
    {
        if ($this->request->is('post')) {
            try {
                $name = $service->add($this->getRequest()->getUploadedFiles());
                $this->BcMessage->setInfo(sprintf(__d('baser', '新規プラグイン「%s」を追加しました。'), $name));
                $this->redirect(['action' => 'index']);
            } catch (BcException $e) {
                $this->BcMessage->setError(__d('baser', 'ファイルのアップロードに失敗しました。') . $e->getMessage());
            }
        }
    }

    /**
     * baserマーケットのプラグインデータを取得する
     * @return void
     * @param PluginsServiceInterface $pluginService
     * @uses get_market_plugins
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get_market_plugins(PluginsServiceInterface $pluginService)
    {
        $this->viewBuilder()->disableAutoLayout();
        $baserPlugins = $pluginService->getMarketPlugins();
        if ($baserPlugins) {
            $this->set('baserPlugins', $baserPlugins);
        }
    }

    /**
     * データベースをリセットする
     *
     * @return void
     * @uses reset_db
     * @checked
     * @noTodo
     * @unitTest
     */
    public function reset_db(PluginsServiceInterface $plugins, UsersServiceInterface $userService)
    {
        if (!$this->request->is('put')) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return;
        }
        $plugin = $plugins->getByName($this->request->getData('name'));
        try {
            $plugins->resetDb($this->request->getData('name'), $this->request->getData('connection'));
            $userService->reLogin($this->request, $this->response);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser', '%s プラグインのデータを初期化しました。'), $plugin->title)
            );
        } catch(\Exception $e) {
            $this->BcMessage->setError(__d('baser', 'リセット処理中にエラーが発生しました。') . $e->getMessage());
        }
        $this->redirect(['action' => 'install', $plugin->name]);
    }

}
