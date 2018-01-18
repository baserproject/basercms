<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('Imageresizer', 'Vendor');

/**
 * テーマファイルコントローラー
 *
 * @package Baser.Controller
 */
class ThemeFilesController extends AppController {

/**
 * クラス名
 * @var string
 */
	public $name = 'ThemeFiles';

/**
 * モデル
 *
 * @var array
 */
	public $uses = ['ThemeFile', 'ThemeFolder'];

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = ['BcForm', 'BcCkeditor'];

/**
 * テーマファイルタイプ
 *
 * @var array
 * @public protected
 */
	protected $_tempalteTypes = [
		'Layouts'	=> 'レイアウトテンプレート',
		'Elements'	=> 'エレメントテンプレート',
		'Emails'	=> 'Eメールテンプレート',
		'etc'		=> 'コンテンツテンプレート',
		'css'		=> 'スタイルシート',
		'js'		=> 'Javascript',
		'img'		=> 'イメージ'
	];

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

/**
 * ぱんくずナビ
 *
 * @var array
 */
	public $crumbs = [
		['name' => 'テーマ管理', 'url' => ['admin' => true, 'controller' => 'themes', 'action' => 'index']]
	];

/**
 * テーマファイル一覧
 *
 * @return void
 */
	public function admin_index() {
		$args = $this->_parseArgs(func_get_args());
		extract($args);

		if (!$theme) {
			$this->notFound();
		}

		// タイトル設定
		$pageTitle = $theme;
		if ($plugin) {
			$pageTitle .= '：' . $plugin;
		}
		$this->pageTitle = '[' . $pageTitle . '] ';
		if (!empty($this->_tempalteTypes[$type])) {
			$this->pageTitle .= $this->_tempalteTypes[$type] . ' 一覧';
		}

		if ($type != 'etc') {

			/* レイアウト／エレメント */
			$folder = new Folder($fullpath);
			$files = $folder->read(true, true);
			$themeFiles = [];
			$folders = [];
			$excludeList = ['_notes'];
			foreach ($files[0] as $file) {
				if (!in_array($file, $excludeList)) {
					if ($file == 'admin' && is_link($fullpath . $file)) {
						continue;
					}
					$folder = [];
					$folder['name'] = $file;
					$folder['type'] = 'folder';
					$folders[] = $folder;
				}
			}
			foreach ($files[1] as $file) {
				$themeFile = [];
				$themeFile['name'] = $file;
				$themeFile['type'] = $this->_getFileType($file);
				$themeFiles[] = $themeFile;
			}
			$themeFiles = am($folders, $themeFiles);
		} else {

			/* その他テンプレート */
			$folder = new Folder($fullpath);
			$files = $folder->read(true, true);
			$themeFiles = [];
			$folders = [];
			$excludeFolderList = [];
			$excludeFileList = ['screenshot.png', 'VERSION.txt', 'config.php', 'AppView.php', 'BcAppView.php'];
			if (!$path) {
				$excludeFolderList = [
					'Layouts', 
					'Elements', 
					'Emails',
					'Pages', 
					'Helper', 
					'Config',
					'Plugin',					
					'img', 
					'css',
					'js',
					'_notes'
				];
			}
			foreach ($files[0] as $file) {
				if (!in_array($file, $excludeFolderList)) {
					$folder = [];
					$folder['name'] = $file;
					$folder['type'] = 'folder';
					$folders[] = $folder;
				}
			}
			foreach ($files[1] as $file) {
				if (in_array($file, $excludeFileList)) {
					continue;
				}
				$themeFile = [];
				$themeFile['name'] = $file;
				$themeFile['type'] = $this->_getFileType($file);
				$themeFiles[] = $themeFile;
			}
			$themeFiles = am($folders, $themeFiles);
		}

		$currentPath = str_replace(ROOT, '', $fullpath);
		$this->subMenuElements = ['theme_files'];
		$this->set('themeFiles', $themeFiles);
		$this->set('currentPath', $currentPath);
		$this->set('fullpath', $fullpath);
		$this->set('path', $path);
		$this->set('theme', $theme);
		$this->set('plugin', $plugin);
		$this->set('type', $type);
		$this->help = 'theme_files_index';
	}

/**
 * ファイルタイプを取得する
 * 
 * @param string $file
 * @return mixed false / type 
 */
	protected function _getFileType($file) {
		if (preg_match('/^(.+?)(\.ctp|\.php|\.css|\.js)$/is', $file)) {
			return 'text';
		} elseif (preg_match('/^(.+?)(\.png|\.gif|\.jpg|\.jpeg)$/is', $file)) {
			return 'image';
		} else {
			return 'file';
		}
		return false;
	}

/**
 * テーマファイル作成
 *
 * @return void
 */
	public function admin_add() {
		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if (!$this->request->data) {

			if ($type == 'css' || $type == 'js') {
				$ext = $type;
			} else {
				$ext = 'php';
			}
			$this->request->data['ThemeFile']['ext'] = $ext;
			$this->request->data['ThemeFile']['parent'] = $fullpath;
		} else {

			$this->ThemeFile->set($this->request->data);
			if ($this->ThemeFile->validates()) {
				$fullpath = $fullpath . $this->request->data['ThemeFile']['name'] . '.' . $this->request->data['ThemeFile']['ext'];
				if (!is_dir(dirname($fullpath))) {
					$folder = new Folder();
					$folder->create(dirname($fullpath), 0777);
				}
				$file = new File($fullpath);
				if ($file->open('w')) {
					$file->append($this->request->data['ThemeFile']['contents']);
					$file->close();
					unset($file);
					$result = true;
				} else {
					$result = false;
				}
			} else {
				$result = false;
			}

			if ($result) {
				clearViewCache();
				$this->setMessage('ファイル ' . basename($fullpath) . ' を作成しました。');
				$this->redirect(array_merge(['action' => 'edit', $theme, $type], explode('/', $path), [$this->request->data['ThemeFile']['name'] . '.' . $this->request->data['ThemeFile']['ext']]));
			} else {
				$this->setMessage('ファイル ' . basename($fullpath) . ' の作成に失敗しました。', true);
			}
		}

		$this->pageTitle = '[' . Inflector::camelize($theme) . '] ' . $this->_tempalteTypes[$type] . ' 作成';
		$this->crumbs[] = ['name' => $this->_tempalteTypes[$type], 'url' => ['controller' => 'theme_files', 'action' => 'index', $theme, $type]];
		$this->subMenuElements = ['theme_files'];
		$this->set('isWritable', is_writable($fullpath));
		$this->set('currentPath', str_replace(ROOT, '', $fullpath));
		$this->set('theme', $theme);
		$this->set('plugin', $plugin);
		$this->set('type', $type);
		$this->set('path', $path);
		$this->help = 'theme_files_form';
		$this->render('form');
	}

/**
 * テーマファイル編集
 *
 * @return void
 */
	public function admin_edit() {
		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		$filename = urldecode(basename($path));

		if (!$this->request->data) {

			$file = new File($fullpath);
			$pathinfo = pathinfo($fullpath);
			$this->request->data['ThemeFile']['name'] = urldecode(basename($file->name, '.' . $pathinfo['extension']));
			$this->request->data['ThemeFile']['type'] = $this->_getFileType(urldecode(basename($file->name)));
			$this->request->data['ThemeFile']['ext'] = $pathinfo['extension'];
			if ($this->request->data['ThemeFile']['type'] == 'text') {
				$this->request->data['ThemeFile']['contents'] = $file->read();
			}
		} else {

			$this->ThemeFile->set($this->request->data);
			if ($this->ThemeFile->validates()) {

				$oldPath = urldecode($fullpath);
				$newPath = dirname($fullpath) . DS . urldecode($this->request->data['ThemeFile']['name']);
				if ($this->request->data['ThemeFile']['ext']) {
					$newPath .= '.' . $this->request->data['ThemeFile']['ext'];
				}
				$this->request->data['ThemeFile']['type'] = $this->_getFileType(basename($newPath));
				if ($this->request->data['ThemeFile']['type'] == 'text') {
					$file = new File($oldPath);
					if ($file->open('w')) {
						$file->append($this->request->data['ThemeFile']['contents']);
						$file->close();
						unset($file);
						$result = true;
					} else {
						$result = false;
					}
				} else {
					$result = true;
				}
				if ($oldPath != $newPath) {
					rename($oldPath, $newPath);
				}
			} else {
				$result = false;
			}

			if ($result) {
				clearViewCache();
				$this->setMessage('ファイル ' . $filename . ' を更新しました。');
				$this->redirect(array_merge([$theme, $plugin, $type], explode('/', dirname($path)), [basename($newPath)]));
			} else {
				$this->setMessage('ファイル ' . $filename . ' の更新に失敗しました。', true);
			}
		}

		$this->pageTitle = '[' . Inflector::camelize($theme) . '] ' . $this->_tempalteTypes[$type] . ' 編集：　' . $filename;
		$this->crumbs[] = ['name' => $this->_tempalteTypes[$type], 'url' => ['controller' => 'theme_files', 'action' => 'index', $theme, $type]];
		$this->subMenuElements = ['theme_files'];
		$this->set('currentPath', str_replace(ROOT, '', dirname($fullpath)) . DS);
		$this->set('isWritable', is_writable($fullpath));
		$this->set('theme', $theme);
		$this->set('plugin', $plugin);
		$this->set('type', $type);
		$this->set('path', $path);
		$this->help = 'theme_files_form';
		$this->render('form');
	}

/**
 * ファイルを削除する
 *
 * @return void
 */
	public function admin_del() {
		$this->_checkSubmitToken();
		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if (is_dir($fullpath)) {
			$folder = new Folder();
			$result = $folder->delete($fullpath);
			$target = 'フォルダ';
		} else {
			$result = @unlink($fullpath);
			$target = 'ファイル';
		}

		if ($result) {
			$this->setMessage($target . ' ' . $path . ' を削除しました。');
		} else {
			$this->setMessage($target . ' ' . $path . ' の削除に失敗しました。', true);
		}

		$this->redirect(array_merge(['action' => 'index', $theme, $type], explode('/', dirname($path))));
	}

/**
 * ファイルを削除する　（ajax）
 *
 * @return void
 */
	public function admin_ajax_del() {
		$this->_checkSubmitToken();
		$args = $this->_parseArgs(func_get_args());

		if (!$args) {
			$this->ajaxError(500, '無効な処理です。');
		}

		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->ajaxError(500, '無効な処理です。');
		}

		if ($this->_del($args)) {
			exit(true);
		} else {
			exit();
		}
	}

/**
 * 削除
 *
 * @return void
 */
	protected function _del($args) {
		extract($args);
		if (is_dir($fullpath)) {
			$folder = new Folder();
			$result = $folder->delete($fullpath);
			$target = 'フォルダ';
		} else {
			$result = @unlink($fullpath);
			$target = 'ファイル';
		}
		if ($result) {
			$this->ThemeFile->saveDblog($target . ' ' . $path . ' を削除しました。');
			return true;
		} else {
			return false;
		}
	}

/**
 * 一括削除
 *
 * @return void
 */
	protected function _batch_del($ids) {
		if ($ids) {

			$result = true;
			foreach ($ids as $id) {
				$args = $this->request->params['pass'];
				$args[] = $id;
				$args = $this->_parseArgs($args);
				extract($args);
				if (!isset($this->_tempalteTypes[$type])) {
					exit();
				}

				if (is_dir($fullpath)) {
					$folder = new Folder();
					$result = $folder->delete($fullpath);
					$target = 'フォルダ';
				} else {
					$result = @unlink($fullpath);
					$target = 'ファイル';
				}
				if ($result) {
					$this->ThemeFile->saveDblog($target . ' ' . $path . ' を削除しました。');
				} else {
					$result = false;
				}
			}
		}

		return true;
	}

/**
 * テーマファイル表示
 *
 * @return	void
 * @access	public
 */
	public function admin_view() {
		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		$pathinfo = pathinfo($fullpath);
		$file = new File($fullpath);
		$this->request->data['ThemeFile']['name'] = basename($file->name, '.' . $pathinfo['extension']);
		$this->request->data['ThemeFile']['ext'] = $pathinfo['extension'];
		$this->request->data['ThemeFile']['contents'] = $file->read();
		$this->request->data['ThemeFile']['type'] = $this->_getFileType($file->name);

		$pageTitle = $theme;
		if ($plugin) {
			$pageTitle .= '：' . $plugin;
		}
		$this->pageTitle = '[' . $pageTitle . '] ' . $this->_tempalteTypes[$type] . ' 表示：　' . basename($path);
		$this->crumbs[] = ['name' => $this->_tempalteTypes[$type], 'url' => ['controller' => 'theme_files', 'action' => 'index', $theme, $type]];
		$this->subMenuElements = ['theme_files'];
		$this->set('currentPath', str_replace(ROOT, '', dirname($fullpath)) . '/');
		$this->set('theme', $theme);
		$this->set('plugin', $plugin);
		$this->set('type', $type);
		$this->set('path', $path);
		$this->render('form');
	}

/**
 * テーマファイルをコピーする
 *
 * @return void
 */
	public function admin_ajax_copy() {
		$args = $this->_parseArgs(func_get_args());

		if (!$args) {
			$this->ajaxError(500, '無効な処理です。');
		}

		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$themeFile = [];
		if (is_dir($fullpath)) {
			$newPath = preg_replace('/\/$/is', '', $fullpath) . '_copy';
			while (true) {
				if (!is_dir($newPath)) {
					break;
				}
				$newPath .= '_copy';
			}
			$folder = new Folder();
			$result = $folder->copy(['from' => $fullpath, 'to' => $newPath, 'chmod' => 0777, 'skip' => ['_notes']]);
			$folder = null;
			$target = 'フォルダ';
			$themeFile['name'] = basename(urldecode($newPath));
			$themeFile['type'] = 'folder';
		} else {
			$pathinfo = pathinfo($fullpath);
			$newPath = $pathinfo['dirname'] . DS . urldecode(basename($fullpath, '.' . $pathinfo['extension'])) . '_copy';
			while (true) {
				if (!file_exists($newPath . '.' . $pathinfo['extension'])) {
					$newPath .= '.' . $pathinfo['extension'];
					break;
				}
				$newPath .= '_copy';
			}
			$result = @copy(urldecode($fullpath), $newPath);
			if ($result) {
				chmod($newPath, 0666);
			}
			$target = 'ファイル';
			$themeFile['name'] = basename(urldecode($newPath));
			$themeFile['type'] = $this->_getFileType($themeFile['name']);
		}

		if ($result) {
			$this->ThemeFile->saveDblog($target . ' ' . urldecode($path) . ' をコピーしました。');
			$this->set('fullpath', $fullpath);
			$this->set('path', dirname($path));
			$this->set('theme', $theme);
			$this->set('plugin', $plugin);
			$this->set('type', $type);
			$this->set('data', $themeFile);
		} else {
			$this->ThemeFile->saveDblog($target . ' ' . urldecode($path) . ' のコピーに失敗しました。');
			$this->ajaxError(500, '上位フォルダのアクセス権限を見直してください。');
		}
	}

/**
 * ファイルをアップロードする
 *
 * @return void
 */
	public function admin_upload() {
		if (!$this->request->data) {
			$this->notFound();
		}
		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}
		$filePath = $fullpath . DS . $this->request->data['ThemeFile']['file']['name'];
		$Folder = new Folder();
		$Folder->create(dirname($filePath), 0777);

		if (@move_uploaded_file($this->request->data['ThemeFile']['file']['tmp_name'], $filePath)) {
			$this->setMessage('アップロードに成功しました。');
		} else {
			$this->setMessage('アップロードに失敗しました。', true);
		}
		$this->redirect(array_merge(['action' => 'index', $theme, $type], explode('/', $path)));
	}

/**
 * フォルダ追加
 *
 * @return void
 */
	public function admin_add_folder() {
		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if (!$this->request->data) {
			$this->request->data['ThemeFolder']['parent'] = dirname($fullpath);
		} else {
			$folder = new Folder();
			$this->ThemeFolder->set($this->request->data);
			if ($this->ThemeFolder->validates() && $folder->create($fullpath . $this->request->data['ThemeFolder']['name'], 0777)) {
				$this->setMessage('フォルダ ' . $this->request->data['ThemeFolder']['name'] . ' を作成しました。');
				$this->redirect(array_merge(['action' => 'index', $theme, $type], explode('/', $path)));
			} else {
				$this->setMessage('フォルダの作成に失敗しました。', true);
			}
		}

		$this->crumbs[] = ['name' => $this->_tempalteTypes[$type], 'url' => ['controller' => 'theme_files', 'action' => 'index', $theme, $type]];
		$this->pageTitle = '[' . $theme . '] フォルダ作成：　' . $path;
		$this->subMenuElements = ['theme_files'];
		$this->set('currentPath', str_replace(ROOT, '', $fullpath));
		$this->set('isWritable', is_writable($fullpath));
		$this->set('theme', $theme);
		$this->set('plugin', $plugin);
		$this->set('type', $type);
		$this->set('path', $path);
		$this->help = 'theme_files_form_folder';
		$this->render('form_folder');
	}

/**
 * フォルダ編集
 *
 * @return void
 */
	public function admin_edit_folder() {
		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if (!$this->request->data) {
			$this->request->data['ThemeFolder']['name'] = basename($path);
			$this->request->data['ThemeFolder']['parent'] = dirname($fullpath);
			$this->request->data['ThemeFolder']['pastname'] = basename($path);
		} else {
			$newPath = dirname($fullpath) . DS . $this->request->data['ThemeFolder']['name'] . DS;
			$folder = new Folder();
			$this->ThemeFolder->set($this->request->data);
			if ($this->ThemeFolder->validates()) {
				if ($fullpath != $newPath) {
					if ($folder->move(['from' => $fullpath, 'to' => $newPath, 'chmod' => 0777, 'skip' => ['_notes']])) {
						$this->setMessage('フォルダ名を ' . $this->request->data['ThemeFolder']['name'] . ' に変更しました。');
						$this->redirect(array_merge(['action' => 'index', $theme, $type], explode('/', dirname($path))));
					} else {
						$this->setMessage('フォルダ名の変更に失敗しました。', true);
					}
				} else {
					$this->setMessage('フォルダ名に変更はありませんでした。', true);
					$this->redirect(array_merge(['action' => 'index', $theme, $type], explode('/', dirname($path))));
				}
			} else {
				$this->setMessage('フォルダ名の変更に失敗しました。', true);
			}
		}

		$pageTitle = $theme;
		$this->pageTitle = '[' . $pageTitle . '] フォルダ表示：　' . basename($path);
		$this->crumbs[] = ['name' => $this->_tempalteTypes[$type], 'url' => ['controller' => 'theme_files', 'action' => 'index', $theme, $type]];
		$this->subMenuElements = ['theme_files'];
		$this->set('currentPath', str_replace(ROOT, '', dirname($fullpath)) . '/');
		$this->set('isWritable', is_writable($fullpath));
		$this->set('theme', $theme);
		$this->set('plugin', $plugin);
		$this->set('type', $type);
		$this->set('path', $path);
		$this->help = 'theme_files_form_folder';
		$this->render('form_folder');
	}

/**
 * フォルダ表示
 *
 * @return void
 */
	public function admin_view_folder() {
		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		$this->request->data['ThemeFolder']['name'] = basename($path);
		$this->request->data['ThemeFolder']['parent'] = dirname($fullpath);
		$this->request->data['ThemeFolder']['pastname'] = basename($path);

		$pageTitle = $theme;
		if ($plugin) {
			$pageTitle .= '：' . $plugin;
		}
		$this->pageTitle = '[' . $pageTitle . '] フォルダ表示：　' . basename($path);
		$this->crumbs[] = ['name' => $this->_tempalteTypes[$type], 'url' => ['controller' => 'theme_files', 'action' => 'index', $theme, $type]];
		$this->subMenuElements = ['theme_files'];
		$this->set('currentPath', str_replace(ROOT, '', dirname($fullpath)) . '/');
		$this->set('theme', $theme);
		$this->set('plugin', $plugin);
		$this->set('type', $type);
		$this->set('path', $path);
		$this->render('form_folder');
	}

/**
 * 引き数を解析する
 *
 * @param array $args
 * @return array
 */
	protected function _parseArgs($args) {
		$data = ['plugin' => '', 'theme' => '', 'type' => '', 'path' => '', 'fullpath' => '', 'assets' => false];
		$assets = ['css', 'js', 'img'];

		if (!empty($args[1]) && !isset($this->_tempalteTypes[$args[1]])) {
			$folder = new Folder(BASER_PLUGINS);
			$files = $folder->read(true, true);
			foreach ($files[0] as $file) {
				if ($args[1] == $file) {
					$data['plugin'] = $args[1];
					unset($args[1]);
					break;
				}
			}
		}

		if ($data['plugin']) {

			if (!empty($args[0])) {
				$data['theme'] = $args[0];
				unset($args[0]);
			}
			if (!empty($args[2])) {
				$data['type'] = $args[2];
				unset($args[2]);
			}
		} else {

			if (!empty($args[0])) {
				$data['theme'] = $args[0];
				unset($args[0]);
			}
			if (!empty($args[1])) {
				$data['type'] = $args[1];
				unset($args[1]);
			}
		}

		if (empty($data['type'])) {
			$data['type'] = 'Layouts';
		}

		if (!empty($args)) {
			$data['path'] = implode(DS, $args);
			$data['path'] = urldecode($data['path']);
		}

		if ($data['plugin']) {
			if (in_array($data['type'], $assets)) {
				$data['assets'] = true;
				$viewPath = BASER_PLUGINS . $data['plugin'] . DS . 'webroot' . DS;
			} else {
				$viewPath = BASER_PLUGINS . $data['plugin'] . DS . 'View' . DS;
			}
		} elseif ($data['theme'] == 'core') {
			if (in_array($data['type'], $assets)) {
				$data['assets'] = true;
				$viewPath = BASER_WEBROOT;
			} else {
				$viewPath = BASER_VIEWS;
			}
		} else {
			$viewPath = WWW_ROOT . 'theme' . DS . $data['theme'] . DS;
		}

		if ($data['type'] != 'etc') {
			$data['fullpath'] = $viewPath . $data['type'] . DS . $data['path'];
		} else {
			$data['fullpath'] = $viewPath . $data['path'];
		}

		if ($data['path'] && is_dir($data['fullpath']) && !preg_match('/\/$/', $data['fullpath'])) {
			$data['fullpath'] .= DS;
		}

		return $data;
	}

/**
 * コアファイルを現在のテーマにコピーする
 *
 * @return void
 */
	public function admin_copy_to_theme() {
		$this->_checkSubmitToken();
		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if ($type != 'etc') {
			if ($plugin && $assets) {
				$themePath = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $plugin . DS . $type . DS . $path;
			} else {
				$themePath = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $type . DS . $path;
			}
		} else {
			$themePath = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $path;
		}
		$folder = new Folder();
		$folder->create(dirname($themePath), 0777);
		if (copy($fullpath, $themePath)) {
			chmod($themePath, 0666);
			$_themePath = str_replace(ROOT, '', $themePath);
			$this->setMessage('コアファイル ' . basename($path) . ' を テーマ ' . Inflector::camelize($this->siteConfigs['theme']) . ' の次のパスとしてコピーしました。<br />' . $_themePath);
			// 現在のテーマにリダイレクトする場合、混乱するおそれがあるのでとりあえずそのまま
			//$this->redirect(array_merge(array('action' => 'edit', $this->siteConfigs['theme'], $type), explode('/', $path)));
		} else {
			$this->setMessage('コアファイル ' . basename($path) . ' のコピーに失敗しました。', true);
		}
		$this->redirect(array_merge(['action' => 'view', $theme, $plugin, $type], explode('/', $path)));
	}

/**
 * コアファイルのフォルダを現在のテーマにコピーする
 *
 * @return void
 */
	public function admin_copy_folder_to_theme() {
		$this->_checkSubmitToken();
		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if (!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if ($type != 'etc') {
			if ($plugin && $assets) {
				$themePath = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $plugin . DS . $type . DS;
			} else {
				$themePath = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $type . DS;
			}
			if ($path) {
				$themePath .= $path . DS;
			}
		} else {
			$themePath = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $path . DS;
		}
		$folder = new Folder();
		$folder->create(dirname($themePath), 0777);
		if ($folder->copy(['from' => $fullpath, 'to' => $themePath, 'chmod' => 0777, 'skip' => ['_notes']])) {
			$_themePath = str_replace(ROOT, '', $themePath);
			$this->setMessage('コアフォルダ ' . basename($path) . ' を テーマ ' . Inflector::camelize($this->siteConfigs['theme']) . ' の次のパスとしてコピーしました。<br />' . $_themePath);
			// 現在のテーマにリダイレクトする場合、混乱するおそれがあるのでとりあえずそのまま
			//$this->redirect(array('action' => 'edit', $this->siteConfigs['theme'], $type, $path));
		} else {
			$this->setMessage('コアフォルダ ' . basename($path) . ' のコピーに失敗しました。', true);
		}
		$this->redirect(array_merge(['action' => 'view_folder', $theme, $plugin, $type], explode('/', $path)));
	}

/**
 * 画像を表示する
 * コアの画像等も表示可
 * 
 * @param array パス情報
 * @return void
 */
	public function admin_img() {
		$args = $this->_parseArgs(func_get_args());
		$contents = ['jpg' => 'jpeg', 'gif' => 'gif', 'png' => 'png'];
		extract($args);
		$pathinfo = pathinfo($fullpath);

		if (!isset($this->_tempalteTypes[$type]) || !isset($contents[$pathinfo['extension']]) || !file_exists($fullpath)) {
			$this->notFound();
		}

		$file = new File($fullpath);
		if ($file->open('r')) {
			header("Content-Length: " . $file->size());
			header("Content-type: image/" . $contents[$pathinfo['extension']]);
			echo $file->read();
			exit();
		} else {
			$this->notFound();
		}
	}

/**
 * 画像を表示する
 * コアの画像等も表示可
 * 
 * @param int $width
 * @param int $height
 * @param array パス情報
 * @return void
 */
	public function admin_img_thumb() {
		$args = func_get_args();
		$width = $args[0];
		$height = $args[1];
		unset($args[0]);
		unset($args[1]);
		$args = array_values($args);

		if ($width == 0) {
			$width = 100;
		}
		if ($height == 0) {
			$height = 100;
		}

		$args = $this->_parseArgs($args);
		$contents = ['jpeg' => 'jpeg', 'jpg' => 'jpeg', 'gif' => 'gif', 'png' => 'png'];
		extract($args);
		$pathinfo = pathinfo($fullpath);

		if (!isset($this->_tempalteTypes[$type]) || !isset($contents[$pathinfo['extension']]) || !file_exists($fullpath)) {
			$this->notFound();
		}

		header("Content-type: image/" . $contents[$pathinfo['extension']]);
		$Imageresizer = new Imageresizer();
		$Imageresizer->resize($fullpath, null, $width, $height);
		exit();
	}

}
