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

    /**
     * アンインストール
     * @param PluginsServiceInterface $plugins
     * @param $name
     * @return \Cake\Http\Response|void|null
     * @checked
     * @noTodo
     * @unitTest
     */
    public function uninstall(PluginsServiceInterface $plugins, $name)
    {
        $this->request->allowMethod(['post']);
        $plugin = $plugins->getByName($name);
        try {
            $plugins->uninstall($name, $this->request->getData());
            $message = sprintf(__d('baser', 'プラグイン「%s」を削除しました。'), $name);
        } catch (\Exception $e) {
            $message = __d('baser', 'プラグインの削除に失敗しました。' . $e->getMessage());
        }
        $this->set([
            'message' => $message,
            'plugin' => $plugin
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugin', 'message']);
    }

    /**
     * 並び替えを更新する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update_sort(PluginsServiceInterface $plugins, $name)
    {
        $this->request->allowMethod(['post']);
        $plugin = $plugins->getByName($name);
        if (!$plugins->changePriority($plugin->id, $this->request->getQuery('offset'))) {
            $message = __d('baser', '一度リロードしてから再実行してみてください。');
        } else {
            $message = sprintf(__d('baser', 'プラグイン「%s」の並び替えを更新しました。'), $name);
        }
        $this->set([
            'message' => $message,
            'plugin' => $plugin
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugin', 'message']);
    }

}
