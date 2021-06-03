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
     * Initialize
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Authentication->allowUnauthenticated(['install',"index"]);
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
     */
    public function install(PluginsServiceInterface $plugins, $name)
    {
        $this->request->allowMethod(['post', 'put']);
        
        $data = $this->request->getData();
        unset($data['name'], $data['title'], $data['status'], $data['version'], $data['permission']);
        // install に $this->request->getData() を引数とするのはユニットテストで connection を test として設定するため
        $plugin = $plugins->install($name, $data);

        if($plugin) {
            $message = __d('baser', 'プラグイン「{0}」をインストールしました。', $name);
        } elseif (is_null($plugin)) {
            $message = __d('baser', 'プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。');
        } else {
            $message = __d('baser', 'プラグインに問題がある為インストールを完了できません。プラグインの開発者に確認してください。');
        }

        $this->set([
            'message' => $message,
            'plugin' => $plugin
        ]);

        $this->viewBuilder()->setOption('serialize', ['plugin', 'message']);
    }
}
