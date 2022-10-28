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

namespace BaserCore\Controller\Api;

use BaserCore\Error\BcException;
use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class PluginsController
 *
 * https://localhost/baser/api/baser-core/plugins/action_name.json で呼び出す
 *
 */
class PluginsController extends BcApiController
{

    /**
     * プラグイン情報取得
     * @param PluginsServiceInterface $plugins
     * @param $id
     * @checked
     * @unitTest
     * @noTodo
     */
    public function view(PluginsServiceInterface $plugins, $id)
    {
        $this->set([
            'plugin' => $plugins->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugin']);
    }

    /**
     * プラグイン情報一覧取得
     * @param PluginsServiceInterface $Plugins
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
     * プラグインをインストールする
     * @param PluginsServiceInterface $Plugins
     * @checked
     * @noTodo
     * @unitTest
     */
    public function install(PluginsServiceInterface $plugins, $name)
    {
        $this->request->allowMethod(['post', 'put']);
        $plugin = $plugins->getByName($name);
        try {
            if($plugins->install($name, $this->request->getData('connection'))) {
                $plugins->allow($this->request->getData());
                $message = sprintf(__d('baser', 'プラグイン「%s」をインストールしました。'), $name);
            } else {
                $this->setResponse($this->response->withStatus(400));
                $message = __d('baser', 'プラグインに問題がある為インストールを完了できません。プラグインの開発者に確認してください。');
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'plugin' => $plugin
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugin', 'message']);
    }

    /**
     * プラグインを無効化する
     * @param PluginsServiceInterface $plugins
     * @param $name
     * @checked
     * @noTodo
     * @unitTest
     */
    public function detach(PluginsServiceInterface $plugins, $name)
    {
        $this->request->allowMethod(['post']);
        $plugin = $plugins->getByName($name);
        if ($plugins->detach($name)) {
            $message = sprintf(__d('baser', 'プラグイン「%s」を無効にしました。'), $name);
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'プラグインの無効化に失敗しました。');
        }
        $this->set([
            'message' => $message,
            'plugin' => $plugin
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugin', 'message']);
    }

    /**
     * プラグインを有効化する
     * @param PluginsServiceInterface $plugins
     * @param $name
     * @checked
     * @noTodo
     * @unitTest
     */
    public function attach(PluginsServiceInterface $plugins, $name)
    {
        $this->request->allowMethod(['post']);
        $plugin = $plugins->getByName($name);
        if ($plugins->attach($name)) {
            $message = sprintf(__d('baser', 'プラグイン「%s」を有効にしました。'), $name);
        } else {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', 'プラグインの有効化に失敗しました。');
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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function reset_db(PluginsServiceInterface $plugins, $name)
    {
        $this->request->allowMethod(['put']);
        $plugin = $plugins->getByName($name);
        try {
            $plugins->resetDb($name, $this->request->getData('connection'));
            $message = sprintf(__d('baser', '%s プラグインのデータを初期化しました。'), $plugin->title);
        } catch(\Exception $e) {
            $this->setResponse($this->response->withStatus(400));
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
            $plugins->uninstall($name, $this->request->getData('connection'));
            $message = sprintf(__d('baser', 'プラグイン「%s」を削除しました。'), $name);
        } catch (\Exception $e) {
            $this->setResponse($this->response->withStatus(400));
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
    public function update_sort(PluginsServiceInterface $plugins)
    {
        $this->request->allowMethod(['post']);
        $plugin = $plugins->get($this->request->getData('id'));

        if (!$plugins->changePriority($plugin->id, $this->request->getData('offset'))) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', '一度リロードしてから再実行してみてください。');
        } else {
            $message = sprintf(__d('baser', 'プラグイン「%s」の並び替えを更新しました。'), $plugin->name);
        }
        $this->set([
            'message' => $message,
            'plugin' => $plugin
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugin', 'message']);
    }

    /**
     * baserマーケットのプラグインデータを取得する
     * @param PluginsServiceInterface $pluginService
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get_market_plugins(PluginsServiceInterface $plugins)
    {
        $this->set([
            'plugins' => $plugins->getMarketPlugins()
        ]);
        $this->viewBuilder()->setOption('serialize', ['plugins']);
    }

    /**
     * バッチ処理
     *
     * @param PluginsServiceInterface $service
     * @checked
     * @noTodo
     */
    public function batch(PluginsServiceInterface $service)
    {
        $this->request->allowMethod(['post', 'put']);
        $allowMethod = [
            'detach' => '無効化'
        ];
        $method = $this->getRequest()->getData('batch');
        if (!isset($allowMethod[$method])) {
            $this->setResponse($this->response->withStatus(500));
            $this->viewBuilder()->setOption('serialize', []);
            return;
        }
        $targets = $this->getRequest()->getData('batch_targets');
        try {
            $names = $service->getNamesById($targets);
            $service->batch($method, $targets);
            $this->BcMessage->setSuccess(
                sprintf(__d('baser', 'プラグイン 「%s」 を %s しました。'), implode('」、「', $names), $allowMethod[$method]),
                true,
                false
            );
            $message = __d('baser', '一括処理が完了しました。');
        } catch (BcException $e) {
            $this->setResponse($this->response->withStatus(400));
            $message = __d('baser', $e->getMessage());
        }
        $this->set(['message' => $message]);
        $this->viewBuilder()->setOption('serialize', ['message']);
    }

}
