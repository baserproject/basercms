<?php

/**
 * ツールコントローラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
App::uses('Simplezip', 'Vendor');

/**
 * ツールコントローラー
 *
 * @package Baser.Controller
 */
class ToolsController extends AppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Tools';

	public $uses = array('Tool', 'Page');

/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');

/**
 * ヘルパ
 * 
 * @var array
 * @access public
 */
	public $helpers = array('BcForm');

/**
 * サブメニュー
 * 
 * @var type
 * @access public 
 */
	public $subMenuElements = array('tools');

/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	public $crumbs = array(
		array('name' => 'システム設定', 'url' => array('controller' => 'site_configs', 'action' => 'form'))
	);

/**
 * データメンテナンス
 *
 * @param string $mode
 * @return void
 * @access public
 */
	public function admin_maintenance($mode = '') {
		switch ($mode) {
			case 'backup':
				set_time_limit(0);
				$this->_backupDb($this->request->query['backup_encoding']);
				break;
			case 'restore':
				set_time_limit(0);
				if (!$this->request->data) {
					$this->notFound();
				}
				$messages = array();
				if ($this->_restoreDb($this->request->data)) {
					$messages[] = 'データの復元が完了しました。';
					$error = false;
				} else {
					$messages[] = 'データの復元に失敗しました。ログの確認を行なって下さい。';
					$error = true;
				}
				if (!$error && !$this->Page->createAllPageTemplate()) {
					$messages[] = 'ページテンプレートの生成に失敗しました。<br />表示できないページはページ管理より更新処理を行ってください。';
				}
				if ($messages) {
					$this->setMessage(implode('<br />', $messages), $error);
				}
				clearAllCache();
				$this->redirect(array('action' => 'maintenance'));
				break;
		}
		$this->pageTitle = 'データメンテナンス';
		$this->subMenuElements = array('site_configs');
		$this->help = 'tools_maintenance';
	}

/**
 * バックアップファイルを復元する
 *
 * @param array $data
 * @return boolean
 * @access protected
 */
	protected function _restoreDb($data) {
		
		if (empty($data['Tool']['backup']['tmp_name'])) {
			return false;
		}

		$tmpPath = TMP . 'schemas' . DS;
		$targetPath = $tmpPath . $data['Tool']['backup']['name'];

		if (!move_uploaded_file($data['Tool']['backup']['tmp_name'], $targetPath)) {
			return false;
		}

		/* ZIPファイルを解凍する */
		$Simplezip = new Simplezip();
		if (!$Simplezip->unzip($targetPath, $tmpPath)) {
			return false;
		}
		@unlink($targetPath);

		$result = true;
		if (!$this->_loadBackup($tmpPath . 'baser' . DS, 'baser', $data['Tool']['encoding'])) {
			$result = false;
		}
		if (!$this->_loadBackup($tmpPath . 'plugin' . DS, 'plugin', $data['Tool']['encoding'])) {
			$result = false;
		}

		$this->_resetTmpSchemaFolder();
		clearAllCache();
		
		return $result;
	}

/**
 * データベースをレストア
 *
 * @param string $path スキーマファイルのパス
 * @param string $configKeyName DB接続名
 * @return boolean
 * @access protected
 */
	protected function _loadBackup($path, $configKeyName, $encoding) {
		$Folder = new Folder($path);
		$files = $Folder->read(true, true);
		if (!is_array($files[1])) {
			return false;
		}

		$db = ConnectionManager::getDataSource($configKeyName);
		$result = true;
		/* テーブルを削除する */
		foreach ($files[1] as $file) {
			if (preg_match("/\.php$/", $file)) {
				if (!$db->loadSchema(array('type' => 'drop', 'path' => $path, 'file' => $file))) {
					$result = false;
					continue;
				}
			}
		}

		/* テーブルを読み込む */
		foreach ($files[1] as $file) {
			if (preg_match("/\.php$/", $file)) {
				if (!$db->loadSchema(array('type' => 'create', 'path' => $path, 'file' => $file))) {
					$result = false;
					continue;
				}
			}
		}

		/* CSVファイルを読み込む */
		foreach ($files[1] as $file) {
			if (preg_match("/\.csv$/", $file)) {
				if (!$db->loadCsv(array('path' => $path . $file, 'encoding' => $encoding))) {
					$result = false;
					continue;
				}
			}
		}

		return $result;
	}

/**
 * バックアップデータを作成する
 *
 * @return void
 * @access protected
 */
	protected function _backupDb($encoding) {
		$tmpDir = TMP . 'schemas' . DS;
		$version = str_replace(' ', '_', $this->getBaserVersion());
		$this->_resetTmpSchemaFolder();
		clearAllCache();
		$this->_writeBackup('baser', $tmpDir . 'baser' . DS, '', $encoding);
		$Plugin = ClassRegistry::init('Plugin');
		$plugins = $Plugin->find('all');
		if ($plugins) {
			foreach ($plugins as $plugin) {
				$this->_writeBackup('plugin', $tmpDir . 'plugin' . DS, $plugin['Plugin']['name'], $encoding);
			}
		}

		// ZIP圧縮して出力
		$fileName = 'baserbackup_' . $version . '_' . date('Ymd_His');
		$Simplezip = new Simplezip();
		$Simplezip->addFolder($tmpDir);
		$Simplezip->download($fileName);
		$this->_resetTmpSchemaFolder();
		exit();
	}

/**
 * バックアップファイルを書きだす
 *
 * @param string $configKeyName
 * @param string $path
 * @return boolean
 * @access protected
 */
	protected function _writeBackup($configKeyName, $path, $plugin = '', $encoding) {
		$db = ConnectionManager::getDataSource($configKeyName);
		$db->cacheSources = false;
		$tables = $db->listSources();
		$pluginPrefix = Inflector::underscore($plugin) . '_';

		foreach ($tables as $table) {
			if (preg_match("/^" . $db->config['prefix'] . "([^_].+)$/", $table, $matches) &&
				!preg_match("/^" . Configure::read('BcEnv.pluginDbPrefix') . "[^_].+$/", $matches[1])) {
				$table = $matches[1];
				if ($plugin) {
					if (!preg_match('/^' . $pluginPrefix . '([^_].+)$/', $table)) {
						// メールプラグインの場合、先頭に、「mail_」 がなくとも 末尾にmessagesがあれば対象とする
						if ($plugin == 'Mail') {
							if (!preg_match("/messages$/", $table)) {
								continue;
							}
						} else {
							if(Inflector::tableize($plugin) != $table) {
								continue;
							}
						}
					}
				}
				if (!$db->writeSchema(array('path' => $path, 'table' => $table, 'plugin' => $plugin))) {
					return false;
				}
				if (!$db->writeCsv(array('path' => $path . $table . '.csv', 'encoding' => $encoding))) {
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
 * @access public
 */
	public function admin_write_schema() {
		$path = TMP . 'schemas' . DS;

		if (!$this->request->data) {
			$this->request->data['Tool']['connection'] = 'baser';
		} else {
			if (empty($this->request->data['Tool'])) {
				$this->setMessage('テーブルを選択してください。', true);
			} else {
				if (!$this->_resetTmpSchemaFolder()) {
					$this->setMessage('フォルダ：' . $path . ' が存在するか確認し、存在する場合は、削除するか書込権限を与えてください。', true);
					$this->redirect(array('action' => 'write_schema'));
				}
				if ($this->Tool->writeSchema($this->request->data, $path)) {
					$Simplezip = new Simplezip();
					$Simplezip->addFolder($path);
					$Simplezip->download('schemas');
					exit();
				} else {
					$this->setMessage('スキーマファイルの生成に失敗しました。', true);
				}
			}
		}

		/* 表示設定 */
		$this->pageTitle = 'スキーマファイル生成';
		$this->help = 'tools_write_schema';
	}

/**
 * スキーマファイルを読み込みテーブルを生成する
 *
 * @return void
 * @access public
 */
	public function admin_load_schema() {
		if (!$this->request->data) {
			$this->request->data['Tool']['schema_type'] = 'create';
		} else {
			if (is_uploaded_file($this->request->data['Tool']['schema_file']['tmp_name'])) {
				$path = TMP . 'schemas' . DS;
				if (!$this->_resetTmpSchemaFolder()) {
					$this->setMessage('フォルダ：' . $path . ' が存在するか確認し、存在する場合は、削除するか書込権限を与えてください。', true);
					$this->redirect(array('action' => 'load_schema'));
				}
				if ($this->Tool->loadSchemaFile($this->request->data, $path)) {
					$this->setMessage('スキーマファイルの読み込みに成功しました。');
					$this->redirect(array('action' => 'load_schema'));
				} else {
					$this->setMessage('スキーマファイルの読み込みに失敗しました。', true);
				}
			} else {
				$this->setMessage('ファイルアップロードに失敗しました。', true);
			}
		}
		/* 表示設定 */
		$this->pageTitle = 'スキーマファイル読込';
		$this->help = 'tools_load_schema';
	}

/**
 * スキーマ用の一時フォルダをリセットする
 *
 * @return boolean
 * @access protected
 */
	protected function _resetTmpSchemaFolder() {
		$path = TMP . 'schemas' . DS;
		return emptyFolder($path);
	}

/**
 * ログメンテナンス
 *
 * @param string $mode
 * @return void
 * @access public
 */
	public function admin_log($mode = '') {
		$errorLogPath = TMP . 'logs' . DS . 'error.log' ;
		switch ($mode) {
			case 'download':
				set_time_limit(0);
				$this->_downloadErrorLog();
				break;
			case 'delete':
				if( file_exists($errorLogPath) ){
					if( unlink($errorLogPath) ){
						$messages[] = 'エラーログを削除しました。';
						$error = false;
					} else {
						$messages[] = 'エラーログが削除できませんでした。';
						$error = true;
					}
				} else {
					$messages[] = 'エラーログが存在しません。';
					$error = true;
				}

				if ($messages) {
					$this->setMessage(implode('<br />', $messages), $error);
				}
				$this->redirect(array('action' => 'log'));
				break;
		}

		$fileSize = 0 ;
		if( file_exists($errorLogPath) ){
			$fileSize = filesize($errorLogPath);
		}

		$this->pageTitle = 'データメンテナンス';
		$this->subMenuElements = array('site_configs');
		$this->help = 'tools_log';
		$this->set('fileSize', $fileSize);
	}

	/**
	 * ログフォルダを圧縮ダウンロードする
	 *
	 * @return void
	 * @access protected
	 */
	protected function _downloadErrorLog() {
		$tmpDir = TMP . 'logs' . DS;

		// ZIP圧縮して出力
		$fileName = 'basercms_logs_' . date('Ymd_His');
		$Simplezip = new Simplezip();
		$Simplezip->addFolder($tmpDir);
		$Simplezip->download($fileName);
		exit();
	}
}
