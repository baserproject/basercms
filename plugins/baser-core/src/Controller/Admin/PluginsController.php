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

use BaserCore\Controller\Component\BcMessageComponent;
use BaserCore\Error\BcException;
use BaserCore\Model\Table\PluginsTable;
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
     * @noTodo
     */
    public function initialize():void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Before Filter
     * @param \Cake\Event\EventInterface $event An Event instance
     * @checked
     * @noTodo
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Security->setConfig('unlockedActions', ['reset_db', 'update_sort', 'batch']);
    }

    /**
     * プラグインの一覧を表示する
     *
     * @return void
     * @checked
     * @unitTest
     * @noTodo
     */
    public function index()
    {
        $available = $this->Plugins->getAvailable();
        $registered = $unregistered = [];
        foreach($available as $pluginInfo) {
            if (isset($pluginInfo->priority)) {
                $registered[] = $pluginInfo;
            } else {
                $unregistered[] = $pluginInfo;
            }
        }

		if (!empty($this->request->getQuery('sortmode'))) {
		    //並び替えモードの場合はDBにデータが登録されていないプラグインを表示しない
			$sortmode = true;
			$plugins = $registered;
		} else {
            $sortmode = false;
            $plugins = array_merge($registered, $unregistered);
		}

        $this->set('plugins', $plugins);

        if($this->RequestHandler->prefers('json')) {
            $this->viewBuilder()->setOption('serialize', ['plugins']);
            return;
        }

        $this->set('corePlugins', Configure::read('BcApp.corePlugins'));
        $this->set('sortmode', $sortmode);
        $this->setTitle(__d('baser', 'プラグイン一覧'));
        $this->setHelp('plugins_index');
    }

    /**
     * インストール
     *
     * @param string $name プラグイン名
     * @return Response|void
     * @checked
     * @unitTest
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

        $pluginEntity = $this->Plugins->getPluginConfig($name);
        $this->set('installMessage', $installMessage);
        $this->set('isInstallable', $isInstallable);
        $this->set('dbInit', $pluginEntity->db_init);
        $this->set('plugin', $pluginEntity);
        $this->setTitle(__d('baser', '新規プラグイン登録'));
        $this->setHelp('plugins_install');

        if (!$isInstallable || !$this->request->is('put')) {
            return;
        }

        // プラグインをインストール
        BcUtil::includePluginClass($name);
        $plugins = Plugin::getCollection();
        $plugin = $plugins->create($name);
        if(!method_exists($plugin, 'install')) {
            $this->BcMessage->setError(__d('baser', 'プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。'));
            return;
        }

        $data = $this->request->getData();
        unset($data['name'], $data['title'], $data['status'], $data['version'], $data['permission']);
        // install に $this->request->getData() を引数とするのはユニットテストで connection を test として設定するため
        if ($plugin->install($data)) {
            $this->BcMessage->setSuccess(sprintf(__d('baser', '新規プラグイン「%s」を baserCMS に登録しました。'), $name));
            // TODO: アクセス権限を追加する
            // $this->_addPermission($this->request->data);
            return $this->redirect(['action' => 'index']);
        } else {
            $this->BcMessage->setError(__d('baser', 'プラグインに問題がある為インストールを完了できません。プラグインの開発者に確認してください。'));
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
    public function detach($name)
    {
        $name = urldecode($name);
        if (!$this->request->is('post')) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }
        if ($this->Plugins->detach($name)) {
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'プラグイン「%s」を無効にしました。'), $name));
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
    public function uninstall($name)
    {
        $name = urldecode($name);
        if (!$this->request->is('post')) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return $this->redirect(['action' => 'index']);
        }

        BcUtil::includePluginClass($name);
        $plugins = Plugin::getCollection();
        $plugin = $plugins->create($name);
        if(!method_exists($plugin, 'uninstall')) {
            $this->BcMessage->setError(__d('baser', 'プラグインに Plugin クラスが存在しません。手動で削除してください。'));
            return;
        }

        if ($plugin->uninstall($this->request->getData())) {
            $this->BcMessage->setSuccess(sprintf(__d('baser', 'プラグイン「%s」を削除しました。'), $name));
        } else {
            $this->BcMessage->setError(__d('baser', 'プラグインの削除に失敗しました。'));
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
        $this->subMenuElements = ['plugins'];

        //データなし
        if (empty($this->request->getData())) {
            if ($this->Plugin->isOverPostSize()) {
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
     *
     * @return void
     * @checked
     * @noTodo
     */
    public function get_market_plugins()
    {
        $this->viewBuilder()->disableAutoLayout();
        if (Configure::read('debug') > 0) {
            Cache::delete('baserMarketPlugins');
        }
        if (!($baserPlugins = Cache::read('baserMarketPlugins', '_bc_env_'))) {
            $Xml = new Xml();
            try {
				$client = new Client([
				    'host' => ''
                ]);
				$response = $client->get(Configure::read('BcApp.marketPluginRss'));
				if ($response->getStatusCode() !== 200) {
                    return;
				}
                $baserPlugins = $Xml->build($response->getBody()->getContents());
                $baserPlugins = $Xml->toArray($baserPlugins->channel);
                $baserPlugins = $baserPlugins['channel']['item'];
            } catch (Exception $e) {

            }
            Cache::write('baserMarketPlugins', $baserPlugins, '_bc_env_');
        }
        if ($baserPlugins) {
            $this->set('baserPlugins', $baserPlugins);
        }
    }

    /**
     * 並び替えを更新する
     * @return void|Response
     * @checked
     * @noTodo
     */
    public function update_sort()
    {
        $this->disableAutoRender();
        if (!$this->request->getData()) {
            $this->ajaxError(500, __d('baser', '無効な処理です。'));
            return;
        }

        if (!$this->Plugins->changePriority($this->request->getData('Sort.id'), $this->request->getData('Sort.offset'))) {
            $this->ajaxError(500, __d('baser', '一度リロードしてから再実行してみてください。'));
            return;
        }

        BcUtil::clearAllCache();
        return $this->response->withStringBody(true);
    }

    /**
     * アクセス制限設定を追加する
     *
     * @param array $data リクエストデータ
     * @return void
     */
    public function _addPermission($data)
    {
        if (ClassRegistry::isKeySet('Permission')) {
            $Permission = ClassRegistry::getObject('Permission');
        } else {
            $Permission = ClassRegistry::init('Permission');
        }

        $userGroups = $Permission->UserGroup->find('all', ['conditions' => ['UserGroup.id <>' => Configure::read('BcApp.adminGroupId')], 'recursive' => -1]);
        if (!$userGroups) {
            return;
        }

        foreach($userGroups as $userGroup) {
            //$permissionAuthPrefix = $Permission->UserGroup->getAuthPrefix($userGroup['UserGroup']['id']);
            // TODO 現在 admin 固定、今後、mypage 等にも対応する
            $permissionAuthPrefix = 'admin';
            $url = '/' . $permissionAuthPrefix . '/' . Inflector::underscore($data['Plugin']['name']) . '/*';
            $permission = $Permission->find(
                'first',
                [
                    'conditions' => ['Permission.url' => $url],
                    'recursive' => -1
                ]
            );
            switch($data['Plugin']['permission']) {
                case 1:
                    if (!$permission) {
                        $Permission->create([
                            'name' => $data['Plugin']['title'] . ' ' . __d('baser', '管理'),
                            'user_group_id' => $userGroup['UserGroup']['id'],
                            'auth' => true,
                            'status' => true,
                            'url' => $url,
                            'no' => $Permission->getMax('no', ['user_group_id' => $userGroup['UserGroup']['id']]) + 1,
                            'sort' => $Permission->getMax('sort', ['user_group_id' => $userGroup['UserGroup']['id']]) + 1
                        ]);
                        $Permission->save();
                    }
                    break;
                case 2:
                    if ($permission) {
                        $Permission->delete($permission['Permission']['id']);
                    }
                    break;
            }
        }
    }

    /**
     * データベースをリセットする
     *
     * @return void
     * @checked
     * @unitTest
     */
    public function reset_db()
    {
        if (!$this->request->is('put')) {
            $this->BcMessage->setError(__d('baser', '無効な処理です。'));
            return;
        }
        $plugin = $this->Plugins->find()
            ->where(['name' => $this->request->getData('name')])
            ->first();

        BcUtil::includePluginClass($plugin->name);
        $plugins = Plugin::getCollection();
        $pluginClass = $plugins->create($plugin->name);
        if(!method_exists($pluginClass, 'rollbackDb')) {
            $this->BcMessage->setError(__d('baser', 'プラグインに Plugin クラスが存在しません。手動で削除してください。'));
            return;
        }

        $plugin->db_init = false;
        $data = $this->request->getData();
        unset($data['name'], $data['title'], $data['status'], $data['version'], $data['permission']);
        if (!$pluginClass->rollbackDb($data) || !$this->Plugins->save($plugin)) {
            $this->BcMessage->setError(__d('baser', '処理中にエラーが発生しました。プラグインの開発者に確認してください。'));
            return;
        }

        // TODO
        /*
        clearAllCache();
        $this->BcAuth->relogin();
        */

        $this->BcMessage->setSuccess(
            sprintf(__d('baser', '%s プラグインのデータを初期化しました。'), $plugin->title)
        );
        $this->redirect(['action' => 'install', $plugin->name]);
    }

    /**
     * 一括処理
     *
     * @param array $ids プラグインIDの配列
     * @return void|Response
     * @checked
     * @noTodo
     */
    public function batch()
    {
        $this->autoRender = false;
        if($this->request->getData('ListTool.batch') !== 'detach') {
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
        return $this->response->withStringBody(true);
    }

}
