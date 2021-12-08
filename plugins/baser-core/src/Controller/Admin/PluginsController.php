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

use BaserCore\Service\PluginServiceInterface;
use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Error\BcException;
use BaserCore\Model\Table\PluginsTable;
use BaserCore\Service\UserServiceInterface;
use BaserCore\Utility\BcUtil;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Core\Plugin;
use Cake\Event\EventInterface;
use Cake\Http\Client;
use Cake\Http\Response;
use Cake\Utility\Xml;
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
    }

    /**
     * プラグインの一覧を表示する
     * @param PluginServiceInterface $PluginService
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function index(PluginServiceInterface $PluginService)
    {
        $this->set('plugins', $PluginService->getIndex($this->request->getQuery('sortmode') ?? '0'));
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
    public function install(PluginServiceInterface $PluginService, $name)
    {
        $this->set('plugin', $this->Plugins->getPluginConfig($name));
        if ($PluginService->getInstallStatusMessage($name) || !$this->request->is(['put', 'post'])) {
            return;
        } else {
            try {
                if ($PluginService->install($name, $this->request->getData('connection') ?? 'default')) {
                    $PluginService->allow($this->request->getData());
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
     * 無効化
     *
     * @param string $name プラグイン名
     * @checked
     * @noTodo
     * @unitTest
     */
    public function detach(PluginServiceInterface $pluginService, $name)
    {
        if (!$this->request->is('post')) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        if ($pluginService->detach(urldecode($name))) {
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'プラグイン「%s」を無効にしました。'), urldecode($name)));
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
    public function uninstall(PluginServiceInterface $pluginService, $name)
    {
        if (!$this->request->is('post')) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        try {
            $pluginService->uninstall(urldecode($name), $this->request->getData('connection'));
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'プラグイン「%s」を削除しました。'), $name));
        } catch (\Exception $e) {
            $this->BcMessage->setError(__d('baser', 'プラグインの削除に失敗しました。' . $e->getMessage()));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * プラグインファイルを削除する
     *
     * @param string $pluginName プラグイン名
     * @return void
     */
    private function __deletePluginFile($pluginName)
    {
        $paths = App::path('Plugin');
        foreach($paths as $path) {
            $pluginPath = $path . $pluginName;
            if (is_dir($pluginPath)) {
                break;
            }
        }

        $tmpPath = TMP . 'schemas' . DS . 'uninstall' . DS;
        $folder = new Folder();
        $folder->delete($tmpPath);
        $folder->create($tmpPath);

        // インストール用スキーマをdropスキーマとして一時フォルダに移動
        $path = BcUtil::getSchemaPath($pluginName);
        $folder = new Folder($path);
        $files = $folder->read(true, true);
        if (is_array($files[1])) {
            foreach($files[1] as $file) {
                if (preg_match('/\.php$/', $file)) {
                    $from = $path . DS . $file;
                    $to = $tmpPath . 'drop_' . $file;
                    copy($from, $to);
                    chmod($to, 0666);
                }
            }
        }

        // テーブルを削除
        $this->Plugin->loadSchema('default', $tmpPath);

        // プラグインフォルダを削除
        $folder->delete($pluginPath);

        // 一時フォルダを削除
        $folder->delete($tmpPath);
    }

    /**
     * プラグインをアップロードしてインストールする
     *
     * @return void
     */
    public function add()
    {
        $this->setTitle(__d('baser', 'プラグインアップロード'));

        //データなし
        if (empty($this->request->getData())) {
            if ($this->Plugins->isOverPostSize()) {
                $this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
            }
            return;
        }

        //アップロード失敗
        if (empty($this->request->getData('Plugin.file.tmp_name'))) {
            $this->BcMessage->setError(__d('baser', 'ファイルのアップロードに失敗しました。'));
            return;
        }

        $zippedName = $this->request->getData('Plugin.file.name');
        move_uploaded_file($this->request->getData('Plugin.file.tmp_name'), TMP . $zippedName);
        App::uses('BcZip', 'Lib');
        $BcZip = new BcZip();
        if (!$BcZip->extract(TMP . $zippedName, APP . 'Plugin' . DS)) {
            $msg = __d('baser', 'アップロードしたZIPファイルの展開に失敗しました。');
            $msg .= "\n" . $BcZip->error;
            $this->BcMessage->setError($msg);
            $this->redirect(['action' => 'add']);
            return;
        }

        $plugin = $BcZip->topArchiveName;

        // 解凍したプラグインフォルダがキャメルケースでない場合にキャメルケースに変換
        $plugin = preg_replace('/^\s*?(creating|inflating):\s*' . preg_quote(APP . 'Plugin' . DS, '/') . '/', '', $plugin);
        $plugin = explode(DS, $plugin);
        $plugin = $plugin[0];
        $srcPluginPath = APP . 'Plugin' . DS . $plugin;
        $Folder = new Folder();
        $Folder->chmod($srcPluginPath, 0777);
        $tgtPluginPath = APP . 'Plugin' . DS . Inflector::camelize($plugin);
        if ($srcPluginPath != $tgtPluginPath) {
            $Folder->move([
                'to' => $tgtPluginPath,
                'from' => $srcPluginPath,
                'mode' => 0777
            ]);
        }
        unlink(TMP . $zippedName);
        $this->BcMessage->setSuccess(sprintf(__d('baser', '新規プラグイン「%s」を追加しました。'), $plugin));
        $this->redirect(['action' => 'index']);
    }

    /**
     * baserマーケットのプラグインデータを取得する
     * @return void
     * @param PluginServiceInterface $pluginService
     * @uses get_market_plugins
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get_market_plugins(PluginServiceInterface $pluginService)
    {
        $this->viewBuilder()->disableAutoLayout();
        $baserPlugins = $pluginService->getMarketPlugins();
        if ($baserPlugins) {
            $this->set('baserPlugins', $baserPlugins);
        }
    }

    /**
     * 並び替えを更新する
     * @return void|Response
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update_sort(PluginServiceInterface $pluginService)
    {
        $this->disableAutoRender();
        if (!$this->request->getData()) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
            return;
        }

        if (!$pluginService->changePriority($this->request->getData('Sort.id'), $this->request->getData('Sort.offset'))) {
            $this->ajaxError(500, __d('baser', '一度リロードしてから再実行してみてください。'));
            return;
        }

        return $this->response->withStringBody('true');
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
    public function reset_db(PluginServiceInterface $plugins, UserServiceInterface $userService)
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

    /**
     * 一括処理
     *
     * @param array $ids プラグインIDの配列
     * @return void|Response
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch()
    {
        $this->autoRender = false;
        if ($this->request->getData('ListTool.batch') !== 'detach') {
            return;
        }
        foreach($this->request->getData('ListTool.batch_targets') as $id) {
            $plugin = $this->Plugins->get($id);
            if ($this->Plugins->detach($plugin->name)) {
                $this->BcMessage->setSuccess(
                    sprintf(__d('baser', 'プラグイン「%s」 を 無効化しました。'), $plugin->title),
                    true,
                    false
                );
            }
        }
        return $this->response->withStringBody('true');
    }

}
