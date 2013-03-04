<?php
/* SVN FILE: $Id$ */
/**
 * テーマファイルコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class ThemeFilesController extends AppController {
/**
 * クラス名
 * @var string
 * @access public
 */
	var $name = 'ThemeFiles';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('ThemeFile', 'ThemeFolder');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_FORM_HELPER, BC_CKEDITOR_HELPER);
/**
 * テーマファイルタイプ
 *
 * @var array
 * @public protected
 */
	var $_tempalteTypes = array('layouts'=>'レイアウトテンプレート',
			'elements'=>'エレメントテンプレート',
			'etc'=>'コンテンツテンプレート',
			'css'=>'スタイルシート',
			'js'=>'Javascript',
			'img'=>'イメージ');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcAuth','Cookie','BcAuthConfigure');
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $crumbs = array(
		array('name' => 'テーマ管理', 'url' => array('admin' => true, 'controller' => 'themes', 'action' => 'index'))
	);
/**
 * テーマファイル一覧
 *
 * @return void
 * @access public
 */
	function admin_index() {

		$args = $this->_parseArgs(func_get_args());
		extract($args);

		if(!$theme){
			$this->notFound();
		}
		
		// タイトル設定
		$pageTitle = Inflector::camelize($theme);
		if($plugin) {
			$pageTitle .= '：'.Inflector::camelize($plugin);
		}
		$this->pageTitle = '['.$pageTitle.'] ';
		if(!empty($this->_tempalteTypes[$type])) {
			$this->pageTitle .= $this->_tempalteTypes[$type].' 一覧';
		}

		if($type!='etc') {

			/* レイアウト／エレメント */
			$folder = new Folder($fullpath);
			$files = $folder->read(true,true);
			$themeFiles = array();
			$folders = array();
			$excludeList = array('_notes');
			foreach($files[0] as $file) {
				if(!in_array($file, $excludeList)) {
					$folder = array();
					$folder['name'] = $file;
					$folder['type'] = 'folder';
					$folders[] = $folder;
				}
			}
			foreach($files[1] as $file) {
				$themeFile = array();
				$themeFile['name'] = $file;
				$themeFile['type'] = $this->_getFileType($file);
				$themeFiles[] = $themeFile;
			}
			$themeFiles = am($folders,$themeFiles);

		}else {

			/* その他テンプレート */
			$folder = new Folder($fullpath);
			$files = $folder->read(true,true);
			$themeFiles = array();
			$folders = array();
			if(!$path) {
				$excludeList = array('css','elements','img','layouts','pages','_notes','helpers','js');
			} else {
				$excludeList = array();
			}
			foreach($files[0] as $file) {
				if(!in_array($file, $excludeList)) {
					$folder = array();
					$folder['name'] = $file;
					$folder['type'] = 'folder';
					$folders[] = $folder;
				}
			}
			foreach($files[1] as $file) {
				if($file=='screenshot.png') {
					continue;
				}
				$themeFile = array();
				$themeFile['name'] = $file;
				$themeFile['type'] = $this->_getFileType($file);
				$themeFiles[] = $themeFile;
			}
			$themeFiles = am($folders,$themeFiles);

		}

		$currentPath = str_replace(ROOT, '', $fullpath);
		$this->subMenuElements = array('theme_files');
		$this->set('themeFiles',$themeFiles);
		$this->set('currentPath',$currentPath);
		$this->set('fullpath',$fullpath);
		$this->set('path',$path);
		$this->set('theme',$theme);
		$this->set('plugin',$plugin);
		$this->set('type',$type);
		$this->help = 'theme_files_index';

	}
/**
 * ファイルタイプを取得する
 * 
 * @param string $file
 * @return mixed false / type 
 */
	function _getFileType($file) {
		
		if(preg_match('/^(.+?)(\.ctp|\.php|\.css|\.js)$/is',$file)) {
			return 'text';
		} elseif(preg_match('/^(.+?)(\.png|\.gif|\.jpg)$/is',$file)) {
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
 * @access public
 */
	function admin_add() {

		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if (!$this->data) {

			if($type=='css' || $type == 'js') {
				$ext = $type;
			}else {
				$ext = 'php';
			}
			$this->data['ThemeFile']['ext'] = $ext;
			$this->data['ThemeFile']['parent'] = $fullpath;

		} else {

			$this->ThemeFile->set($this->data);
			if($this->ThemeFile->validates()) {
				$fullpath = $fullpath.$this->data['ThemeFile']['name'].'.'.$this->data['ThemeFile']['ext'];
				if(!is_dir(dirname($fullpath))) {
					$folder = new Folder();
					$folder->create(dirname($fullpath),0777);
				}
				$file = new File($fullpath);
				if($file->open('w')) {
					$file->append($this->data['ThemeFile']['contents']);
					$file->close();
					unset($file);
					$result = true;
				}else {
					$result = false;
				}
			} else {
				$result = false;
			}

			if ($result) {
				clearViewCache();
				$this->setMessage('ファイル ' .basename($fullpath). ' を作成しました。');
				$this->redirect(array('action' => 'edit', $theme, $type, $path, $this->data['ThemeFile']['name'].'.'.$this->data['ThemeFile']['ext']));
			} else {
				$this->setMessage('ファイル ' .basename($fullpath). ' の作成に失敗しました。', true);
			}

		}

		$this->pageTitle = '['.Inflector::camelize($theme).'] '.$this->_tempalteTypes[$type].' 作成';
		$this->crumbs[] = array('name' => $this->_tempalteTypes[$type], 'url' => array('controller' => 'theme_files', 'action' => 'index', $theme, $type));
		$this->subMenuElements = array('theme_files');
		$this->set('currentPath', str_replace(ROOT, '', $fullpath));
		$this->set('theme',$theme);
		$this->set('plugin',$plugin);
		$this->set('type', $type);
		$this->set('path', $path);
		$this->help = 'theme_files_form';
		$this->render('form');

	}
/**
 * テーマファイル編集
 *
 * @return void
 * @access public
 */
	function admin_edit() {

		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		$filename = urldecode(basename($path));

		if (!$this->data) {

			$file = new File($fullpath);
			$pathinfo = pathinfo($fullpath);
			$this->data['ThemeFile']['name'] = urldecode(basename($file->name,'.'.$pathinfo['extension']));
			$this->data['ThemeFile']['type'] = $this->_getFileType(urldecode(basename($file->name)));
			$this->data['ThemeFile']['ext'] = $pathinfo['extension'];
			if($this->data['ThemeFile']['type'] == 'text') {
				$this->data['ThemeFile']['contents'] = $file->read();
			}

		} else {

			$this->ThemeFile->set($this->data);
			if($this->ThemeFile->validates()) {

				$oldPath = urldecode($fullpath);
				$newPath = dirname($fullpath).DS.urldecode($this->data['ThemeFile']['name']);
				if($this->data['ThemeFile']['ext']) {
					$newPath .= '.'.$this->data['ThemeFile']['ext'];
				}
				$this->data['ThemeFile']['type'] = $this->_getFileType(basename($newPath));
				if($this->data['ThemeFile']['type'] == 'text') {
					$file = new File($oldPath);
					if($file->open('w')) {
						$file->append($this->data['ThemeFile']['contents']);
						$file->close();
						unset($file);
						$result = true;
					}else {
						$result = false;
					}
				}else {
					$result = true;
				}
				if($oldPath != $newPath) {
					rename($oldPath, $newPath);
				}

			} else {
				$result = false;
			}

			if ($result) {
				clearViewCache();
				$this->setMessage('ファイル ' .$filename. ' を更新しました。');
				$this->redirect(array($theme, $plugin, $type, dirname($path), basename($newPath)));
			} else {
				$this->setMessage('ファイル ' .$filename. ' の更新に失敗しました。', true);
			}

		}

		$this->pageTitle = '['.Inflector::camelize($theme).'] '.$this->_tempalteTypes[$type].' 編集：　'.$filename;
		$this->crumbs[] = array('name' => $this->_tempalteTypes[$type], 'url' => array('controller' => 'theme_files', 'action' => 'index', $theme, $type));
		$this->subMenuElements = array('theme_files');
		$this->set('currentPath', str_replace(ROOT, '', dirname($fullpath)).DS);
		$this->set('theme',$theme);
		$this->set('plugin',$plugin);
		$this->set('type', $type);
		$this->set('path', $path);
		$this->help = 'theme_files_form';
		$this->render('form');

	}
/**
 * ファイルを削除する
 *
 * @return void
 * @access public
 */
	function admin_del () {

		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if(is_dir($fullpath)) {
			$folder = new Folder();
			$result = $folder->delete($fullpath);
			$target = 'フォルダ';
		}else {
			$result = @unlink($fullpath);
			$target = 'ファイル';
		}

		if ($result) {
			$this->setMessage($target .' '.$path .' を削除しました。');
		} else {
			$this->setMessage($target .' '.$path .' の削除に失敗しました。', true);
		}

		$this->redirect(array('action' => 'index', $theme, $type, dirname($path)));

	}
/**
 * ファイルを削除する　（ajax）
 *
 * @return void
 * @access public
 */
	function admin_ajax_del () {

		$args = $this->_parseArgs(func_get_args());
		
		if(!$args) {
			$this->ajaxError(500, '無効な処理です。');
		}
		
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->ajaxError(500, '無効な処理です。');
		}
		
		if($this->_del($args)){
			exit(true);
		}else{
			exit();
		}

	}
/**
 * 削除
 *
 * @return void
 * @access public
 */
	function _del($args) {
		
		extract($args);
		if(is_dir($fullpath)) {
			$folder = new Folder();
			$result = $folder->delete($fullpath);
			$target = 'フォルダ';
		}else {
			$result = @unlink($fullpath);
			$target = 'ファイル';
		}
		if ($result) {
			$this->ThemeFile->saveDblog($target .' '.$path .' を削除しました。');
			return true;
		} else {
			return false;
		}
		
	}
/**
 * 一括削除
 *
 * @return void
 * @access public
 */
	function _batch_del($ids) {
		
		if($ids) {
			
			$result = true;
			foreach($ids as $id) {
				$args = $this->params['pass'];
				$args[] = $id;
				$args = $this->_parseArgs($args);
				extract($args);
				if(!isset($this->_tempalteTypes[$type])) {
					exit();
				}

				if(is_dir($fullpath)) {
					$folder = new Folder();
					$result = $folder->delete($fullpath);
					$target = 'フォルダ';
				}else {
					$result = @unlink($fullpath);
					$target = 'ファイル';
				}
				if ($result) {
					$this->ThemeFile->saveDblog($target .' '.$path .' を削除しました。');
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
	function admin_view() {

		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		$pathinfo = pathinfo($fullpath);
		$file = new File($fullpath);
		$this->data['ThemeFile']['name'] = basename($file->name,'.'.$pathinfo['extension']);
		$this->data['ThemeFile']['ext'] = $pathinfo['extension'];
		$this->data['ThemeFile']['contents'] = $file->read();
		$this->data['ThemeFile']['type'] = $this->_getFileType($file->name);
		
		$pageTitle = Inflector::camelize($theme);
		if($plugin) {
			$pageTitle .= '：'.Inflector::camelize($plugin);
		}
		$this->pageTitle = '['.$pageTitle.'] '.$this->_tempalteTypes[$type].' 表示：　'.basename($path);
		$this->crumbs[] = array('name' => $this->_tempalteTypes[$type], 'url' => array('controller' => 'theme_files', 'action' => 'index', $theme, $type));
		$this->subMenuElements = array('theme_files');
		$this->set('currentPath', str_replace(ROOT, '', dirname($fullpath)).'/');
		$this->set('theme',$theme);
		$this->set('plugin',$plugin);
		$this->set('type', $type);
		$this->set('path', $path);
		$this->render('form');

	}
/**
 * テーマファイルをコピーする
 *
 * @return void
 * @access public
 */
	function admin_ajax_copy() {

		$args = $this->_parseArgs(func_get_args());
		
		if(!$args) {
			$this->ajaxError(500, '無効な処理です。');
		}
		
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$themeFile = array();
		if(is_dir($fullpath)) {
			$newPath = preg_replace('/\/$/is', '', $fullpath).'_copy';
			while(true) {
				if(!is_dir($newPath)) {
					break;
				}
				$newPath .= '_copy';
			}
			$folder = new Folder();
			$result = $folder->copy(array('from'=>$fullpath,'to'=>$newPath,'chmod'=>0777,'skip'=>array('_notes')));
			$folder = null;
			$target = 'フォルダ';
			$themeFile['name'] = basename(urldecode($newPath));
			$themeFile['type'] = 'folder';
		} else {
			$pathinfo = pathinfo($fullpath);
			$newPath = $pathinfo['dirname'].DS.urldecode(basename($fullpath,'.'.$pathinfo['extension'])).'_copy';
			while(true) {
				if(!file_exists($newPath.'.'.$pathinfo['extension'])) {
					$newPath .= '.'.$pathinfo['extension'];
					break;
				}
				$newPath .= '_copy';
			}
			$result = @copy(urldecode($fullpath),$newPath);
			if($result) {
				chmod($newPath, 0666);
			}
			$target = 'ファイル';
			$themeFile['name'] = basename(urldecode($newPath));
			$themeFile['type'] = $this->_getFileType($themeFile['name'] );
		}

		if($result) {
			$this->ThemeFile->saveDblog($target.' '.urldecode($path) .' をコピーしました。');
			$this->set('fullpath',$fullpath);
			$this->set('path',dirname($path));
			$this->set('theme',$theme);
			$this->set('plugin',$plugin);
			$this->set('type',$type);
			$this->set('data', $themeFile);
		}else {
			$this->ThemeFile->saveDblog($target.' '.urldecode($path) .' のコピーに失敗しました。');
			$this->ajaxError(500, '上位フォルダのアクセス権限を見直してください。');
		}

	}
/**
 * ファイルをアップロードする
 *
 * @return void
 * @access public
 */
	function admin_upload() {

		if(!$this->data) {
			$this->notFound();
		}
		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}
		$pathinfo = pathinfo($this->data['ThemeFile']['file']['name']);
		$ext = $pathinfo['extension'];
		//if(in_array($ext, array('ctp', 'css', 'js', 'png', 'gif', 'jpg'))) {
			$filePath = $fullpath .DS. $this->data['ThemeFile']['file']['name'];
			$Folder = new Folder();
			$Folder->create(dirname($filePath), 0777);

			if(@move_uploaded_file($this->data['ThemeFile']['file']['tmp_name'], $filePath)) {
				$this->setMessage('アップロードに成功しました。');
			}else {
				$this->setMessage('アップロードに失敗しました。', true);
			}
		//} else {
			//$this->setMessage('アップロードに失敗しました。<br />アップロードできるファイルは、拡張子が、ctp / css / js / png / gif / jpg のファイルのみです。', true);
		//}
		$this->redirect(array('action' => 'index', $theme, $type, $path));

	}
/**
 * フォルダ追加
 *
 * @return void
 * @access public
 */
	function admin_add_folder() {

		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if (!$this->data) {
			$this->data['ThemeFolder']['parent'] = dirname($fullpath);
		} else {
			$folder = new Folder();
			$this->ThemeFolder->set($this->data);
			if ($this->ThemeFolder->validates() && $folder->create($fullpath.$this->data['ThemeFolder']['name'], 0777)) {
				$this->setMessage('フォルダ '.$this->data['ThemeFolder']['name'].' を作成しました。');
				$this->redirect(array('action' => 'index', $theme, $type, $path));
			} else {
				$this->setMessage('フォルダの作成に失敗しました。', true);
			}
		}

		$this->crumbs[] = array('name' => $this->_tempalteTypes[$type], 'url' => array('controller' => 'theme_files', 'action' => 'index', $theme, $type));
		$this->pageTitle = '['.$theme.'] フォルダ作成：　'.$path;
		$this->subMenuElements = array('theme_files');
		$this->set('currentPath', str_replace(ROOT, '', $fullpath));
		$this->set('theme',$theme);
		$this->set('plugin',$plugin);
		$this->set('type',$type);
		$this->set('path',$path);
		$this->help = 'theme_files_form_folder';
		$this->render('form_folder');

	}
/**
 * フォルダ編集
 *
 * @return void
 * @access public
 */
	function admin_edit_folder() {

		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if (!$this->data) {
			$this->data['ThemeFolder']['name'] = basename($path);
			$this->data['ThemeFolder']['parent'] = dirname($fullpath);
			$this->data['ThemeFolder']['pastname'] = basename($path);
		} else {
			$newPath = dirname($fullpath).DS.$this->data['ThemeFolder']['name'].DS;
			$folder = new Folder();
			$this->ThemeFolder->set($this->data);
			if ($this->ThemeFolder->validates()) {
				if($fullpath != $newPath) {
					if($folder->move(array('from'=>$fullpath,'to'=>$newPath,'chmod'=>0777,'skip'=>array('_notes')))) {
						$this->setMessage('フォルダ名を '.$this->data['ThemeFolder']['name'].' に変更しました。');
						$this->redirect(array('action' => 'index', $theme, $type, dirname($path)));
					}else {
						$this->setMessage('フォルダ名の変更に失敗しました。', true);
					}
				}else {
					$this->setMessage('フォルダ名に変更はありませんでした。', true);
					$this->redirect(array('action' => 'index', $theme, $type, dirname($path)));
				}
			} else {
				$this->setMessage('フォルダ名の変更に失敗しました。', true);
			}
		}

		$pageTitle = Inflector::camelize($theme);
		$this->pageTitle = '['.$pageTitle.'] フォルダ表示：　'.basename($path);
		$this->crumbs[] = array('name' => $this->_tempalteTypes[$type], 'url' => array('controller' => 'theme_files', 'action' => 'index', $theme, $type));
		$this->subMenuElements = array('theme_files');
		$this->set('currentPath', str_replace(ROOT, '', dirname($fullpath)).'/');
		$this->set('theme',$theme);
		$this->set('plugin',$plugin);
		$this->set('type',$type);
		$this->set('path',$path);
		$this->help = 'theme_files_form_folder';
		$this->render('form_folder');

	}
/**
 * フォルダ表示
 *
 * @return void
 * @access public
 */
	function admin_view_folder() {

		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		$this->data['ThemeFolder']['name'] = basename($path);
		$this->data['ThemeFolder']['parent'] = dirname($fullpath);
		$this->data['ThemeFolder']['pastname'] = basename($path);

		$pageTitle = Inflector::camelize($theme);
		if($plugin) {
			$pageTitle .= '：'.Inflector::camelize($plugin);
		}
		$this->pageTitle = '['.$pageTitle.'] フォルダ表示：　'.basename($path);
		$this->crumbs[] = array('name' => $this->_tempalteTypes[$type], 'url' => array('controller' => 'theme_files', 'action' => 'index', $theme, $type));
		$this->subMenuElements = array('theme_files');
		$this->set('currentPath', str_replace(ROOT, '', dirname($fullpath)).'/');
		$this->set('theme',$theme);
		$this->set('plugin',$plugin);
		$this->set('type',$type);
		$this->set('path',$path);
		$this->render('form_folder');

	}
/**
 * 引き数を解析する
 *
 * @param array $args
 * @return array
 * @access protected
 */
	function _parseArgs($args) {

		$data = array('plugin' => '', 'theme' => '', 'type' => '', 'path' => '', 'fullpath' => '', 'assets' => false);
		$assets = array('css', 'js', 'img');

		if(!empty($args[1]) && !isset($this->_tempalteTypes[$args[1]])) {
			$folder = new Folder(BASER_PLUGINS);
			$files = $folder->read(true,true);
			foreach($files[0] as $file) {
				if($args[1]==$file) {
					$data['plugin'] = $args[1];
					unset($args[1]);
					break;
				}
			}
		}

		if($data['plugin']) {

			if(!empty($args[0])) {
				$data['theme'] = $args[0];
				unset($args[0]);
			}
			if(!empty($args[2])) {
				$data['type'] = $args[2];
				unset($args[2]);
			}

		} else {

			if(!empty($args[0])) {
				$data['theme'] = $args[0];
				unset($args[0]);
			}
			if(!empty($args[1])) {
				$data['type'] = $args[1];
				unset($args[1]);
			}

		}

		if(empty($data['type'])) {
			$data['type'] = 'layouts';
		}

		if(!empty($args)) {
			$data['path'] = implode('/', $args);
			$data['path'] = urldecode($data['path']);
		}

		if($data['plugin']) {
			if(in_array($data['type'],$assets)) {
				$data['assets'] = true;
				$viewPath = BASER_PLUGINS.$data['plugin'].DS.'vendors'.DS;
			}else {
				$viewPath = BASER_PLUGINS.$data['plugin'].DS.'views'.DS;
			}
		}elseif($data['theme'] == 'core') {
			if(in_array($data['type'],$assets)) {
				$data['assets'] = true;
				$viewPath = BASER_VENDORS;
			}else {
				$viewPath = BASER_VIEWS;
			}
		}else {
			$viewPath = WWW_ROOT.'themed'.DS.$data['theme'].DS;
		}

		if($data['type']!='etc') {
			$data['fullpath'] = $viewPath.$data['type'].DS.$data['path'];
		}else {
			$data['fullpath'] = $viewPath.$data['path'];
		}

		if($data['path'] && is_dir($data['fullpath']) && !preg_match('/\/$/', $data['fullpath'])) {
			$data['fullpath'] .= '/';
		}
		
		return $data;

	}
/**
 * コアファイルを現在のテーマにコピーする
 *
 * @return void
 * @access public
 */
	function admin_copy_to_theme() {

		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if($type!='etc') {
			if($plugin && $assets) {
				$themePath = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$plugin.DS.$type.DS.$path;
			} else {
				$themePath = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$type.DS.$path;
			}
		}else {
			$themePath = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$path;
		}
		$folder = new Folder();
		$folder->create(dirname($themePath),0777);
		if(copy($fullpath,$themePath)) {
			chmod($themePath,0666);
			$_themePath = str_replace(ROOT,'',$themePath);
			$this->setMessage('コアファイル '.basename($path).' を テーマ '.Inflector::camelize($this->siteConfigs['theme']).' の次のパスとしてコピーしました。<br />'.$_themePath);
			// 現在のテーマにリダイレクトする場合、混乱するおそれがあるのでとりあえずそのまま
			//$this->redirect(array('action' => 'edit', $this->siteConfigs['theme'], $type, $path));
		}else {
			$this->setMessage('コアファイル '.basename($path).' のコピーに失敗しました。', true);
		}
		$this->redirect(array('action' => 'view', $theme, $plugin, $type, $path));

	}
/**
 * コアファイルのフォルダを現在のテーマにコピーする
 *
 * @return void
 * @access public
 */
	function admin_copy_folder_to_theme() {

		$args = $this->_parseArgs(func_get_args());
		extract($args);
		if(!isset($this->_tempalteTypes[$type])) {
			$this->notFound();
		}

		if($type!='etc') {
			if($plugin && $assets) {
				$themePath = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$plugin.DS.$type.DS;
			} else {
				$themePath = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$type.DS;
			}
			if($path) {
				$themePath .= $path.DS;
			}
		}else {
			$themePath = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$path.DS;
		}
		$folder = new Folder();
		$folder->create(dirname($themePath),0777);
		if($folder->copy(array('from'=>$fullpath,'to'=>$themePath,'chmod'=>0777,'skip'=>array('_notes')))) {
			$_themePath = str_replace(ROOT,'',$themePath);
			$this->setMessage('コアフォルダ '.basename($path).' を テーマ '.Inflector::camelize($this->siteConfigs['theme']).' の次のパスとしてコピーしました。<br />'.$_themePath);
			// 現在のテーマにリダイレクトする場合、混乱するおそれがあるのでとりあえずそのまま
			//$this->redirect(array('action' => 'edit', $this->siteConfigs['theme'], $type, $path));
		}else {
			$this->setMessage('コアフォルダ '.basename($path).' のコピーに失敗しました。', true);
		}
		$this->redirect(array('action' => 'view_folder', $theme, $plugin, $type, $path));

	}
/**
 * 画像を表示する
 * コアの画像等も表示可
 * 
 * @param array パス情報
 * @return void
 * @access public
 */
	function admin_img() {

		$args = $this->_parseArgs(func_get_args());
		$contents = array('jpg'=>'jpeg','gif'=>'gif','png'=>'png');
		extract($args);
		$pathinfo = pathinfo($fullpath);

		if(!isset($this->_tempalteTypes[$type]) || !isset($contents[$pathinfo['extension']]) || !file_exists($fullpath)) {
			$this->notFound();
		}

		$file = new File($fullpath);
		if($file->open('r')) {
			header("Content-Length: ".$file->size());
			header("Content-type: image/".$contents[$pathinfo['extension']]);
			echo $file->read();
			exit();
		}else {
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
 * @access public
 */
	function admin_img_thumb() {

		$args = func_get_args();
		$width = $args[0];
		$height = $args[1];
		unset($args[0]);
		unset($args[1]);
		$args = array_values($args);

		if($width == 0) {
			$width = 100;
		}
		if($height == 0) {
			$height = 100;
		}

		$args = $this->_parseArgs($args);
		$contents = array('jpg'=>'jpeg','gif'=>'gif','png'=>'png');
		extract($args);
		$pathinfo = pathinfo($fullpath);

		if(!isset($this->_tempalteTypes[$type]) || !isset($contents[$pathinfo['extension']]) || !file_exists($fullpath)) {
			$this->notFound();
		}

		header("Content-type: image/".$contents[$pathinfo['extension']]);
		App::import('Vendor','Imageresizer');
		$Imageresizer = new Imageresizer();
		$Imageresizer->resize($fullpath,'',$width,$height);
		exit();

	}
	
}
