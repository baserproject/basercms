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

App::uses('BcZip', 'Lib');

/**
 * Class ToolsController
 *
 * ツールコントローラー
 *
 * @package Baser.Controller
 * @property Tool $Tool
 * @property Page $Page
 * @property BcManagerComponent $BcManager
 */
class ToolsController extends AppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'Tools';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['Tool', 'Page'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcManager'];

	/**
	 * ヘルパ
	 *
	 * @var array
	 */
	public $helpers = ['BcForm'];

	/**
	 * サブメニュー
	 *
	 * @var array
	 * @access public
	 */
	public $subMenuElements = ['site_configs', 'tools'];

	/**
	 * ToolsController constructor.
	 *
	 * @param \CakeRequest $request
	 * @param \CakeRequest $response
	 */
	public function __construct($request = null, $response = null)
	{
		parent::__construct($request, $response);
		$this->crumbs = [
			['name' => __d('baser', 'システム設定'), 'url' => ['controller' => 'site_configs', 'action' => 'form']],
			['name' => __d('baser', 'ユーティリティ'), 'url' => ['controller' => 'tools', 'action' => 'index']]
		];
	}

	/**
	 * ユーティリティ
	 */
	public function admin_index()
	{
		$this->pageTitle = __d('baser', 'ユーティリティトップ');
	}

	/**
	 * データメンテナンス
	 *
	 * @param string $mode
	 * @return void
	 */
	public function admin_maintenance($mode = '')
	{
		$this->_checkReferer();
		$zipEnable = extension_loaded('zip');
		if (!$zipEnable) {
			$this->notFound();
		}
		
		switch($mode) {
			case 'backup':
				$this->_backupDb($this->request->query['backup_encoding']);
				break;
			case 'restore':
				set_time_limit(0);
				$messages = [];
				if (!$this->request->data) {
					if ($this->Tool->isOverPostSize()) {
						$messages[] = __d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size'));
					} else {
						$this->notFound();
					}
				}
				if ($this->_restoreDb($this->request->data)) {
					$messages[] = __d('baser', 'データの復元が完了しました。');
					$error = false;
				} else {
					$messages[] = __d('baser', 'データの復元に失敗しました。ログの確認を行なって下さい。');
					$error = true;
				}
				// Pageモデルがレストア処理でAppModelで初期化されClassRegistryにセットされている為
				ClassRegistry::flush();
				BcSite::flash();
				if (!$error && !$this->Page->createAllPageTemplate()) {
					$messages[] = __d('baser', "ページテンプレートの生成に失敗しました。\n表示できないページはページ管理より更新処理を行ってください。");
				}
				if ($messages) {
					if ($error) {
						$this->BcMessage->setError(implode("\n", $messages));
					} else {
						$this->BcMessage->setInfo(implode("\n", $messages));
					}
				}
				clearAllCache();
				$this->redirect(['action' => 'maintenance']);
				break;
		}
		$this->pageTitle = __d('baser', 'データメンテナンス');
		$this->help = 'tools_maintenance';
	}

	/**
	 * バックアップファイルを復元する
	 *
	 * @param array $data
	 * @return boolean
	 */
	protected function _restoreDb($data)
	{

		if (empty($data['Tool']['backup']['tmp_name'])) {
			return false;
		}

		$tmpPath = TMP . 'schemas' . DS;
		$targetPath = $tmpPath . $data['Tool']['backup']['name'];

		if (!move_uploaded_file($data['Tool']['backup']['tmp_name'], $targetPath)) {
			return false;
		}
		
		/* ZIPファイルを解凍する */
		$bcZip = new BcZip();
		if (!$bcZip->extract($targetPath, $tmpPath)) {
			return false;
		}
		@unlink($targetPath);

		$result = true;
		$db = ConnectionManager::getDataSource('default');
		$db->begin();
		if (!$this->_loadBackup($tmpPath . 'core' . DS, $data['Tool']['encoding'])) {
			$result = false;
		}
		if (!$this->_loadBackup($tmpPath . 'plugin' . DS, $data['Tool']['encoding'])) {
			$result = false;
		}
		if ($result) {
			$db->commit();
		} else {
			$db->rollback();
		}
		$this->_resetTmpSchemaFolder();
		clearAllCache();

		return $result;
	}

	/**
	 * データベースをレストア
	 *
	 * @param string $path スキーマファイルのパス
	 * @param $encoding
	 * @return boolean
	 */
	protected function _loadBackup($path, $encoding)
	{
		$Folder = new Folder($path);
		$files = $Folder->read(true, true);
		if (!is_array($files[1])) {
			return false;
		}

		$db = ConnectionManager::getDataSource('default');
		$result = true;
		/* テーブルを削除する */
		foreach($files[1] as $file) {
			if (preg_match("/\.php$/", $file)) {
				try {
					if (!$db->loadSchema(['type' => 'drop', 'path' => $path, 'file' => $file])) {
						$result = false;
						continue;
					}
				} catch (Exception $e) {
					$result = false;
					$this->log($e->getMessage());
				}
			}
		}

		/* テーブルを読み込む */
		foreach($files[1] as $file) {
			if (preg_match("/\.php$/", $file)) {
				try {
					if (!$db->loadSchema(['type' => 'create', 'path' => $path, 'file' => $file])) {
						$result = false;
						continue;
					}
				} catch (Exception $e) {
					$result = false;
					$this->log($e->getMessage());
				}
			}
		}

		/* CSVファイルを読み込む */
		foreach($files[1] as $file) {
			if (preg_match("/\.csv$/", $file)) {
				try {
					if (!$db->loadCsv(['path' => $path . $file, 'encoding' => $encoding])) {
						$result = false;
						continue;
					}
				} catch (Exception $e) {
					$result = false;
					$this->log($e->getMessage());
				}
			}
		}

		return $result;
	}

	/**
	 * バックアップデータを作成する
	 *
	 * @return void
	 */
	protected function _backupDb($encoding)
	{
		set_time_limit(0);
		
		$version = str_replace(' ', '_', $this->getBaserVersion());
		$tmpDir = TMP . 'schemas' . DS;
		$distPath = TMP . 'baserbackup_' . $version . '_' . date('Ymd_His') . '.zip';
		
		$this->_resetTmpSchemaFolder();
		clearAllCache();
		$this->_writeBackup($tmpDir . 'core' . DS, '', $encoding);
		$Plugin = ClassRegistry::init('Plugin');
		$plugins = $Plugin->find('all');
		if ($plugins) {
			foreach($plugins as $plugin) {
				$this->_writeBackup($tmpDir . 'plugin' . DS, $plugin['Plugin']['name'], $encoding);
			}
		}
		
		// ZIP圧縮して出力
		$bcZip = new BcZip();
		$bcZip->create($tmpDir, $distPath);
		
		header("Cache-Control: no-store");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=" . basename($distPath) . ";");
		header("Content-Length: " . filesize($distPath));
		while (ob_get_level()) { ob_end_clean(); }
		echo readfile($distPath);
		
		unlink($distPath);
		$this->_resetTmpSchemaFolder();
		return true;
	}

	/**
	 * バックアップファイルを書きだす
	 *
	 * @param string $path
	 * @param string $plugin
	 * @param $encoding
	 * @return boolean
	 */
	protected function _writeBackup($path, $plugin = '', $encoding)
	{
		$db = ConnectionManager::getDataSource('default');
		$db->cacheSources = false;
		$tables = $db->listSources();
		$tableList = getTableList();
		foreach($tables as $table) {
			if ((!$plugin && in_array($table, $tableList['core']) || ($plugin && in_array($table, $tableList['plugin'])))) {
				$table = str_replace($db->config['prefix'], '', $table);
				if (!$db->writeSchema(['path' => $path, 'table' => $table, 'plugin' => $plugin])) {
					return false;
				}
				if (!$db->writeCsv(['path' => $path . $table . '.csv', 'encoding' => $encoding])) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * モデル名からスキーマファイルを生成する
	 *
	 * @return void
	 */
	public function admin_write_schema()
	{
		$path = TMP . 'schemas' . DS;
		$distPath = TMP . 'schemas.zip';

		/* 表示設定 */
		$this->pageTitle = __d('baser', 'スキーマファイル生成');
		$this->help = 'tools_write_schema';

		if (!$this->request->data) {
			$this->request->data['Tool']['connection'] = 'core';
			return;
		}

		if (empty($this->request->data['Tool'])) {
			$this->BcMessage->setError(__d('baser', 'テーブルを選択してください。'));
			return;
		}

		if (!$this->_resetTmpSchemaFolder()) {
			$this->BcMessage->setError('フォルダ：' . $path . ' が存在するか確認し、存在する場合は、削除するか書込権限を与えてください。');
			$this->redirect(['action' => 'write_schema']);
		}
		if (!$this->Tool->writeSchema($this->request->data, $path)) {
			$this->BcMessage->setError(__d('baser', 'スキーマファイルの生成に失敗しました。'));
			return;
		}

		// ZIP圧縮して出力
		set_time_limit(0);
		$this->autoRender = false;
		
		// 不要ディレクトリの削除
		rmdir($path . 'core');
		rmdir($path . 'plugin');
		
		$bcZip = new BcZip();
		$bcZip->create($path, $distPath);
		
		header("Cache-Control: no-store");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=" . basename($distPath) . ";");
		header("Content-Length: " . filesize($distPath));
		while (ob_get_level()) { ob_end_clean(); }
		echo readfile($distPath);
		
		unlink($distPath);
		return true;
	}

	/**
	 * スキーマファイルを読み込みテーブルを生成する
	 *
	 * @return void
	 */
	public function admin_load_schema()
	{
		/* 表示設定 */
		$this->pageTitle = __d('baser', 'スキーマファイル読込');
		$this->help = 'tools_load_schema';
		if (!$this->request->is(['post', 'put'])) {
			$this->request->data['Tool']['schema_type'] = 'create';
			return;
		}

		if ($this->Tool->isOverPostSize()) {
			$this->BcMessage->setError(
				__d('baser', '送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。', ini_get('post_max_size'))
			);
			$this->redirect(['action' => 'load_schema']);
		}
		if (!is_uploaded_file($this->request->data['Tool']['schema_file']['tmp_name'])) {
			$this->BcMessage->setError(__d('baser', 'ファイルアップロードに失敗しました。'));
			return;
		}

		$path = TMP . 'schemas' . DS;
		if (!$this->_resetTmpSchemaFolder()) {
			$this->BcMessage->setError('フォルダ：' . $path . ' が存在するか確認し、存在する場合は、削除するか書込権限を与えてください。');
			$this->redirect(['action' => 'load_schema']);
		}
		if (!$this->Tool->loadSchemaFile($this->request->data, $path)) {
			$this->BcMessage->setError(__d('baser', 'スキーマファイルの読み込みに失敗しました。'));
			return;
		}

		$this->BcMessage->setInfo(__d('baser', 'スキーマファイルの読み込みに成功しました。'));
		$this->redirect(['action' => 'load_schema']);
	}

	/**
	 * スキーマ用の一時フォルダをリセットする
	 *
	 * @return boolean
	 */
	protected function _resetTmpSchemaFolder()
	{
		$path = TMP . 'schemas' . DS;
		return emptyFolder($path);
	}

	/**
	 * ログメンテナンス
	 *
	 * @param string $mode
	 * @return void
	 */
	public function admin_log($mode = '')
	{
		$zipEnable = extension_loaded('zip');
		$errorLogPath = TMP . 'logs' . DS . 'error.log';
		switch($mode) {
			case 'download':
				if (!$zipEnable) {
					$this->notFound();
				}
				$this->autoRender = false;
				if ($this->_downloadErrorLog()) {
					return;
				}
				$this->BcMessage->setInfo('エラーログが存在しません。');
				$this->redirect(['action' => 'log']);
				break;
			case 'delete':
				$this->_checkSubmitToken();
				if (file_exists($errorLogPath)) {
					if (unlink($errorLogPath)) {
						$messages[] = __d('baser', 'エラーログを削除しました。');
						$error = false;
					} else {
						$messages[] = __d('baser', 'エラーログが削除できませんでした。');
						$error = true;
					}
				} else {
					$messages[] = __d('baser', 'エラーログが存在しません。');
					$error = false;
				}

				if ($messages) {
					$this->BcMessage->set(implode("\n", $messages), $error);
				}
				$this->redirect(['action' => 'log']);
				break;

		}

		$fileSize = 0;
		if (file_exists($errorLogPath)) {
			$fileSize = filesize($errorLogPath);
		}

		$this->pageTitle = __d('baser', 'データメンテナンス');
		$this->help = 'tools_log';
		$this->set('zipEnable', $zipEnable);
		$this->set('fileSize', $fileSize);
	}

	/**
	 * ログフォルダを圧縮ダウンロードする
	 *
	 * @return bool
	 */
	protected function _downloadErrorLog()
	{
		set_time_limit(0);
		
		$tmpDir = TMP . 'logs' . DS;
		$distPath = TMP . 'basercms_logs_' . date('Ymd_His') . '.zip';
		$Folder = new Folder($tmpDir);
		$files = $Folder->read(true, true, false);
		if (count($files[0]) === 0 && count($files[1]) === 0) {
			return false;
		}
		// ZIP圧縮して出力
		$bcZip = new BcZip();
		$bcZip->create($tmpDir, $distPath);
		
		header("Cache-Control: no-store");
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=" . basename($distPath) . ";");
		header("Content-Length: " . filesize($distPath));
		while (ob_get_level()) { ob_end_clean(); }
		echo readfile($distPath);
		
		unlink($distPath);
		return true;
	}

	/**
	 * 管理システム用アセットファイルを削除する
	 */
	public function admin_delete_admin_assets()
	{
		$this->_checkReferer();
		if (!$this->BcManager->deleteAdminAssets()) {
			$this->BcMessage->setError(__d('baser', '管理システム用のアセットファイルの削除に失敗しました。アセットファイルの書込権限を見直してください。'));
			$this->redirect(['controller' => 'tools', 'action' => 'index']);
			return;
		}

		$this->BcMessage->setSuccess(__d('baser', '管理システム用のアセットファイルを削除しました。'));
		$this->redirect(['controller' => 'tools', 'action' => 'index']);
	}

	/**
	 * 管理システム用アセットファイルを再配置する
	 */
	public function admin_deploy_admin_assets()
	{
		$this->_checkReferer();
		if (!$this->BcManager->deployAdminAssets()) {
			$this->BcMessage->setError(__d('baser', '管理システム用のアセットファイルの再配置に失敗しました。アセットファイルの書込権限を見直してください。'));
		} else {
			$this->BcMessage->setSuccess(__d('baser', '管理システム用のアセットファイルを再配置しました。'));
		}
		$this->redirect(['controller' => 'tools', 'action' => 'index']);
	}

	/**
	 * コンテンツ管理のツリー構造をリセットする
	 */
	public function admin_reset_contents_tree()
	{
		$this->_checkReferer();
		$Content = ClassRegistry::init('Content');
		if ($Content->resetTree()) {
			$this->BcMessage->setSuccess(__d('baser', 'コンテンツのツリー構造をリセットしました。'));
		} else {
			$this->BcMessage->setError(__d('baser', 'コンテンツのツリー構造のリセットに失敗しました。'));
		}
		$this->redirect(['controller' => 'tools', 'action' => 'index']);
	}

	/**
	 * コンテンツ管理のツリー構造のチェックを行う
	 *
	 * 問題がある場合にはログを出力する
	 */
	public function admin_verity_contents_tree()
	{
		$this->_checkReferer();
		$Content = ClassRegistry::init('Content');
		$Content->Behaviors->unload('SoftDelete');
		$result = $Content->verify();
		if ($result !== true) {
			$this->log($result);
			$this->BcMessage->setError(__d('baser', 'コンテンツのツリー構造に問題があります。ログを確認してください。'));
		} else {
			$this->BcMessage->setSuccess(__d('baser', 'コンテンツのツリー構造に問題はありません。'), false);
		}
		$this->redirect(['controller' => 'tools', 'action' => 'index']);
	}
}
