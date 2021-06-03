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

namespace BaserCore\Controller\Api;

use BaserCore\Service\PluginsServiceInterface;
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PluginsController
 *
 * https://localhost/baser/api/baser-core/plugins/action_name.json で呼び出す
 *
 * @package BaserCore\Controller\Api
 */
class PluginsController extends BcApiController
{
    /**
     * プラグイン情報一覧取得
     * @param PluginsServiceInterface $plugins
     * @checked
     * @unitTest
     * @noTodo
     */
    public function index(PluginsServiceInterface $plugins)
    {
        $this->set([
            'plugins' => $plugins->getIndex($this->request->getQuery('sortmode') ?? '0')
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugins']);
    }

    /**
     * プラグインを無効化する
     * @param PluginsServiceInterface $plugins
     * @param $name
     */
    public function detach(PluginsServiceInterface $plugins, $name)
    {
        $this->request->allowMethod(['post']);
        $plugin = $plugins->getByName($name);
        if ($plugins->detach($name)) {
            $message = sprintf(__d('baser', 'プラグイン「%s」を無効にしました。'), $name);
        } else {
            $message = __d('baser', 'プラグインの無効化に失敗しました。');
        }
        $this->set([
            'message' => $message,
            'plugin' => $plugin
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugin', 'message']);
    }

    /**
     * プラグインのデータベースを初期化する
     * @param PluginsServiceInterface $plugins
     * @param $name
     */
    public function reset_db(PluginsServiceInterface $plugins, $name)
    {
        $this->request->allowMethod(['put']);
        $plugin = $plugins->getByName($name);
        try {
            $plugins->resetDb($name, $this->request->getData());
            $message = sprintf(__d('baser', '%s プラグインのデータを初期化しました。'), $plugin->title);
        } catch(\Exception $e) {
            $message = __d('baser', 'リセット処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'plugin' => $plugin
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugin', 'message']);
    }

    public function uninstall(PluginsServiceInterface $plugins, $name)
    {
        BcUtil::includePluginClass($name);
        $plugins = Plugin::getCollection();
        $plugin = $plugins->create($name);
        if (!method_exists($plugin, 'uninstall')) {
            $this->BcMessage->setError(__d('baser', 'プラグインに Plugin クラスが存在しません。手動で削除してください。'));
            return;
        }

        try {
            $pluginManage->uninstall($name, $this->request->getData());
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'プラグイン「%s」を削除しました。'), $name));
        } catch (\Exception $e) {
            $this->BcMessage->setError(__d('baser', 'プラグインの削除に失敗しました。' . $e->getMessage()));
        }
        return $this->redirect(['action' => 'index']);
    }

}
