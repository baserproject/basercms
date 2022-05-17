<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class PluginsController
 *
 * Plugin 拡張クラス
 * プラグインのコントローラーより継承して利用する
 *
 * @package Baser.Controller
 * @property Plugin $Plugin
 * @property BcManagerComponent $BcManager
 * @property BcAuthComponent $BcAuth
 */
class PluginsController extends AppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'Plugins';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['Plugin'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

	/**
	 * ヘルパ
	 *
	 * @var array
	 */
	public $helpers = ['BcTime', 'BcForm'];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = [];

	/**
	 * Before Filter
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->crumbs = [
			[
				'name' => __d('baser', 'プラグイン管理'),
				'url' => [
					'plugin' => '',
					'controller' => 'plugins',
					'action' => 'index'
				]
			]
		];
	}

	/**
	 * プラグインをアップロードしてインストールする
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->pageTitle = __d('baser', 'プラグインアップロード');
		$this->subMenuElements = ['plugins'];

		//データなし
		if (empty($this->request->data)) {
			if ($this->Plugin->isOverPostSize()) {
				$this->BcMessage->setError(__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size')));
			}
			return;
		}

		//アップロード失敗
		if (empty($this->request->data['Plugin']['file']['tmp_name'])) {
			$this->BcMessage->setError(__d('baser', 'ファイルのアップロードに失敗しました。'));
			return;
		}

		$zippedName = $this->request->data['Plugin']['file']['name'];
		move_uploaded_file($this->request->data['Plugin']['file']['tmp_name'], TMP . $zippedName);
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
		$Folder = new Folder($srcPluginPath);
		// .htacessファイルが含まれる場合はアップロード不可
		$htaccessFiles = $Folder->findRecursive('.*\.htaccess');
		if ($htaccessFiles) {
			$msg = __d('baser', '.htaccessファイルが含まれるプラグインはアップロードできません。');
			$Folder->delete();
			$this->BcMessage->setError($msg);
			$this->redirect(['action' => 'add']);
			return;
		}

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
	 * プラグインの一覧を表示する
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$this->Plugin->cacheQueries = false;
		$datas = $this->Plugin->find('all', ['order' => 'Plugin.priority']);
		if (!$datas) {
			$datas = [];
		}

		// プラグインフォルダーのチェックを行う。
		$pluginInfos = [];
		$paths = App::path('Plugin');
		foreach($paths as $path) {
			$Folder = new Folder($path);
			$files = $Folder->read(true, true, true);
			foreach($files[0] as $file) {
				$pluginInfos[basename($file)] = $this->Plugin->getPluginInfo($datas, $file);
			}
		}

		$pluginInfos = array_values($pluginInfos); // Hash::sortの為、一旦キーを初期化
		$pluginInfos = array_reverse($pluginInfos); // Hash::sortの為、逆順に変更

		$availables = $unavailables = [];
		foreach($pluginInfos as $pluginInfo) {
			if (isset($pluginInfo['Plugin']['priority'])) {
				$availables[] = $pluginInfo;
			} else {
				$unavailables[] = $pluginInfo;
			}
		}

		//並び替えモードの場合はDBにデータが登録されていないプラグインを表示しない
		if (!empty($this->passedArgs['sortmode'])) {
			$sortmode = true;
			$pluginInfos = Hash::sort($availables, '{n}.Plugin.priority', 'asc', 'numeric');
		} else {
			$sortmode = false;
			$pluginInfos = array_merge(Hash::sort($availables, '{n}.Plugin.priority', 'asc', 'numeric'), $unavailables);
		}

		// 表示設定
		$this->set('datas', $pluginInfos);
		$this->set('corePlugins', Configure::read('BcApp.corePlugins'));
		$this->set('sortmode', $sortmode);

		if ($this->request->is('ajax')) {
			$this->render('ajax_index');
		}

		$this->subMenuElements = ['plugins'];
		$this->pageTitle = __d('baser', 'プラグイン一覧');
		$this->help = 'plugins_index';
	}

	/**
	 * baserマーケットのプラグインデータを取得する
	 *
	 * @return void
	 */
	public function admin_ajax_get_market_plugins()
	{
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
			$context = stream_context_create(array('ssl'=>array(
				'allow_self_signed'=> true,
				'verify_peer' => false,
			)));
			libxml_set_streams_context($context);
			$baserPlugins = simplexml_load_file(Configure::read('BcApp.marketPluginRss'));
		} catch (Exception $ex) {

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

	/**
	 * 並び替えを更新する [AJAX]
	 *
	 * @return bool
	 */
	public function admin_ajax_update_sort()
	{
		$this->autoRender = false;
		if (!$this->request->data) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
			return false;
		}

		if (!$this->Plugin->changePriority($this->request->data['Sort']['id'], $this->request->data['Sort']['offset'])) {
			$this->ajaxError(500, __d('baser', '一度リロードしてから再実行してみてください。'));
			return false;
		}

		clearViewCache();
		clearDataCache();
		Configure::write('debug', 0);
		return true;
	}

	/**
	 * [ADMIN] ファイル削除
	 *
	 * @param string $pluginName プラグイン名
	 * @return void
	 */
	public function admin_ajax_delete_file($pluginName)
	{
		$this->_checkSubmitToken();
		if (!$pluginName) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		$pluginName = urldecode($pluginName);
		$this->__deletePluginFile($pluginName);
		$this->Plugin->saveDbLog(sprintf(__d('baser', 'プラグイン「%s」 を完全に削除しました。'), $pluginName));
		exit(true);
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
	 * [ADMIN] 登録処理
	 *
	 * @param string $name プラグイン名
	 * @return void
	 */
	public function admin_install($name)
	{
		$name = urldecode($name);
		$dbInited = false;
		$installMessage = '';

		try {
			if ($this->canInstall($name)) {
				$isInstallable = true;
			}
		} catch (BcException $e) {
			$isInstallable = false;
			$installMessage = $e->getMessage();
		}

		if ($isInstallable) {
			if (!$this->request->data) {
				$paths = App::path('Plugin');
				foreach($paths as $path) {
					$path .= $name . DS . 'config.php';
					if (file_exists($path)) {
						include $path;
						break;
					}
				}

				if (!isset($title)) {
					$title = $name;
				}
				$corePlugins = Configure::read('BcApp.corePlugins');
				if (in_array($name, $corePlugins)) {
					$version = $this->getBaserVersion();
				} else {
					$version = $this->getBaserVersion($name);
				}

				$this->request->data = ['Plugin' => [
					'name' => $name,
					'title' => $title,
					'status' => true,
					'version' => $version,
					'permission' => 1
				]];

				$data = $this->Plugin->find('first', ['conditions' => ['name' => $this->request->data['Plugin']['name']]]);
				if ($data) {
					$dbInited = $data['Plugin']['db_inited'];
				}
			} else {
				// プラグインをインストール
				if ($this->BcManager->installPlugin($this->request->data['Plugin']['name'])) {
					$this->BcMessage->setSuccess(sprintf(__d('baser', '新規プラグイン「%s」を baserCMS に登録しました。'), $name));

					$this->Plugin->addFavoriteAdminLink($name, $this->BcAuth->user());
					$this->_addPermission($this->request->data);

					$this->redirect(['action' => 'index']);
				} else {
					$this->BcMessage->setError(__d('baser', 'プラグインに問題がある為インストールを完了できません。プラグインの開発者に確認してください。'));
				}
			}
		}

		/* 表示設定 */
		$this->set('installMessage', $installMessage);
		$this->set('isInstallable', $isInstallable);
		$this->set('dbInited', $dbInited);
		$this->subMenuElements = ['plugins'];
		$this->pageTitle = __d('baser', '新規プラグイン登録');
		$this->help = 'plugins_form';
		$this->render('form');
	}

	/**
	 * プラグインがインストール可能か判定する
	 *
	 * @param string $pluginName プラグイン名
	 * @return boolean
	 */
	private function canInstall($pluginName)
	{
		$installedPlugin = $this->Plugin->find('first', [
			'conditions' => [
				'name' => $pluginName,
				'status' => 1,
			],
		]);
		// 既にプラグインがインストール済み
		if ($installedPlugin) {
			throw new BcException('既にインストール済のプラグインです。');
		}

		$paths = App::path('Plugin');
		$existsPluginFolder = false;
		foreach($paths as $path) {
			if (!is_dir($path . $pluginName)) {
				continue;
			}
			$existsPluginFolder = true;
			$configPath = $path . $pluginName . DS . 'config.php';
			if (file_exists($configPath)) {
				include $configPath;
			}
			break;
		}

		// プラグインのフォルダが存在しない
		if (!$existsPluginFolder) {
			throw new BcException('インストールしようとしているプラグインのフォルダが存在しません。');
		}

		// インストールしようとしているプラグイン名と、設定ファイル内のプラグイン名が違う
		if (!empty($name) && $pluginName !== $name) {
			throw new BcException('このプラグイン名のフォルダ名を' . $name . 'にしてください。');
		}

		return true;
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
	 */
	public function admin_reset_db()
	{
		if (!$this->request->data) {
			$this->BcMessage->setError(__d('baser', '無効な処理です。'));
			return;
		}
		$data = $this->Plugin->find('first', ['conditions' => ['name' => $this->request->data['Plugin']['name']]]);
		$this->Plugin->resetDb($this->request->data['Plugin']['name']);
		$data['Plugin']['db_inited'] = false;
		$this->Plugin->set($data);

		// データを保存
		if (!$this->Plugin->save()) {
			$this->BcMessage->setError(__d('baser', '処理中にエラーが発生しました。プラグインの開発者に確認してください。'));
			return;
		}
		clearAllCache();
		$this->BcAuth->relogin();
		$this->BcMessage->setSuccess(
			sprintf(__d('baser', '%s プラグインのデータを初期化しました。'), $data['Plugin']['title'])
		);
		$this->redirect(['action' => 'install', $data['Plugin']['name']]);
	}

	/**
	 * [ADMIN] 削除処理　(ajax)
	 *
	 * @param string $name プラグイン名
	 * @return void
	 */
	public function admin_ajax_delete($name = null)
	{
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$name) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		if ($this->BcManager->uninstallPlugin($name)) {
			clearAllCache();
			$this->Plugin->saveDbLog(sprintf(__d('baser', 'プラグイン「%s」 を 無効化しました。'), $name));
			exit(true);
		}

		exit();
	}

	/**
	 * 一括無効
	 *
	 * @param array $ids プラグインIDの配列
	 * @return bool
	 */
	protected function _batch_del($ids)
	{
		if (!$ids) {
			return true;
		}
		foreach($ids as $id) {
			$data = $this->Plugin->read(null, $id);
			if ($this->BcManager->uninstallPlugin($data['Plugin']['name'])) {
				$this->Plugin->saveDbLog(
					sprintf(__d('baser', 'プラグイン「%s」 を 無効化しました。'), $data['Plugin']['title'])
				);
			}
		}
		clearAllCache();
		return true;
	}

}
