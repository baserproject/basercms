<?php
/* SVN FILE: $Id$ */
/**
 * テーマコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class ThemesController extends AppController {
/**
 * コントローラー名
 * @var string
 * @access	public
 */
	var $name = 'Themes';
/**
 * モデル
 * @var array
 * @access public
 */
	var $uses = array('Theme','Page');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcAuth','Cookie','BcAuthConfigure');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_FORM_HELPER);
/**
 * パンくずナビ
 * @var array
 * @access public
 */
	var $crumbs = array(
		array('name' => 'テーマ管理', 'url' => array('controller' => 'themes', 'action' => 'index'))
	);
/**
 * テーマ一覧
 *
 * @return void
 * @access public
 */
	function admin_index(){

		$this->pageTitle = 'テーマ一覧';
		$path = WWW_ROOT.'themed';
		$folder = new Folder($path);
		$files = $folder->read(true,true);
		$datas = array();
		foreach($files[0] as $themename){
			if($themename != 'core' && $themename != '_notes'){
				$datas[] = $this->_loadThemeInfo($themename);
			}
		}
		$datas[] = array(
			'name'				=> 'core',
			'title'				=> 'baserCMSコア',
			'version'			=> $this->getBaserVersion(),
			'description'		=> 'baserCMSのコアファイル。現在のテーマにコピーして利用する事ができます。',
			'author'			=> 'basercms',
			'url'				=> 'http://basercms.net',
			'is_writable_pages'	=> false
		);
		$this->set('datas',$datas);
		$this->subMenuElements = array('themes');
		$this->help = 'themes_index';
		
	}
/**
 * テーマ情報を読み込む
 * 
 * @param string $theme 
 * @return array
 * @access protected
 */
	function _loadThemeInfo($themename) {
		
		$path = WWW_ROOT.'themed';
		$title = $description = $author = $url = $screenshot = '';
		$theme = array();
		if(file_exists($path.DS.$themename.DS.'config.php')){
			include $path.DS.$themename.DS.'config.php';
		}
		if(file_exists($path.DS.$themename.DS.'screenshot.png')){
			$theme['screenshot'] = true;
		}else{
			$theme['screenshot'] = false;
		}
		if(is_writable($path.DS.$themename.DS.'pages'.DS)){
			$theme['is_writable_pages'] = true;
		} else {
			$theme['is_writable_pages'] = false;
		}
		$theme['name'] = $themename;
		$theme['title'] = $title;
		$theme['description'] = $description;
		$theme['author'] = $author;
		$theme['url'] = $url;
		$theme['version'] = $this->getThemeVersion($theme['name']);
		return $theme;
		
	}
/**
 * テーマ名編集
 * 
 * @param string $theme
 * @return void
 * @access public
 */
	function admin_edit($theme){
		
		if(!$theme){
			$this->notFound();
		}
		$themePath = WWW_ROOT.'themed'.DS.$theme.DS;
		$title = $description = $author = $url = '';
		include $themePath.'config.php';
		
		if(!$this->data){
			$this->data['Theme']['name'] = $theme;
			$this->data['Theme']['title'] = $title;
			$this->data['Theme']['description'] = $description;
			$this->data['Theme']['author'] = $author;
			$this->data['Theme']['url'] = $url;
		}else{
			$this->data['Theme']['old_name'] = $theme;
			$this->Theme->set($this->data);
			if($this->Theme->save()){
				$this->Session->setFlash('テーマ「'.$this->data['Theme']['name'].'」を更新しました。');
				$this->redirect(array('action' => 'index'));
			}else{
				$this->Session->setFlash('テーマ情報の変更に失敗しました。入力内容を確認してください。');
			}
		}

		if(is_writable($themePath)){
			$folderDisabled = '';
		}else{
			$folderDisabled = 'disabled';
			$this->data['Theme']['name'] = $theme;
		}

		if(is_writable($themePath.'config.php')){
			$configDisabled = '';
		}else{
			$configDisabled = 'disabled';
			$this->data['Theme']['title'] = $title;
			$this->data['Theme']['description'] = $description;
			$this->data['Theme']['author'] = $author;
			$this->data['Theme']['url'] = $url;
		}

		$this->pageTitle = 'テーマ情報編集';
		$this->subMenuElements = array('themes');
		$this->set('theme',$theme);
		$this->set('configDisabled',$configDisabled);
		$this->set('folderDisabled',$folderDisabled);
		$this->help = 'themes_form';
		$this->render('form');
		
	}
/**
 * テーマをコピーする
 *
 * @param string $theme
 * @return void
 * @access public
 */
	function admin_ajax_copy($theme){

		if(!$theme){
			$this->ajaxError(500, '無効な処理です。');
		}
		$result = $this->_copy($theme);
		if($result) {
			$this->set('data', $result);
		} else {
			$this->ajaxError(500, 'テーマフォルダのアクセス権限を見直してください。');
		}

	}
/**
 * テーマをコピーする
 *
 * @param string $theme
 * @return boolean
 * @access public
 */
	function _copy($theme) {
		
		$basePath = WWW_ROOT.'themed'.DS;
		$newTheme = $theme.'_copy';
		while(true){
			if(!is_dir($basePath.$newTheme)){
				break;
			}
			$newTheme .= '_copy';
		}
		$folder = new Folder();
		if($folder->copy(array('from'=>$basePath.$theme,'to'=>$basePath.$newTheme,'mode'=>0777,'skip'=>array('_notes')))) {
			$this->Theme->saveDblog('テーマ「'.$theme.'」をコピーしました。');
			return $this->_loadThemeInfo($newTheme);
		} else {
			return false;
		}
		
	}
/**
 * テーマを削除する　(ajax)
 *
 * @param string $theme
 * @return void
 * @access public
 */
	function admin_ajax_delete($theme){

		if(!$theme){
			$this->ajaxError(500, '無効な処理です。');
		}
		if($this->_del($theme)) {
			clearViewCache();
			exit(true);
		} else {
			$this->ajaxError(500, 'テーマフォルダを手動で削除してください。');
		}
		exit();
		
	}
/**
 * 一括削除
 * 
 * @param array $ids
 * @return boolean
 * @access protected
 */
	function _batch_del($ids) {
		
		if($ids) {
			foreach($ids as $id) {
				$this->_del($id);
			}
		}
		clearViewCache();
		return true;
		
	}
/**
 * データを削除する
 * 
 * @param int $id
 * @return boolean 
 * @access protected
 */
	function _del($theme) {
		
		$path = WWW_ROOT.'themed'.DS.$theme;
		$folder = new Folder();
		if($folder->delete($path)) {
			$siteConfig = array('SiteConfig'=>$this->siteConfigs);
			if($theme == $siteConfig['SiteConfig']['theme']){
				$siteConfig['SiteConfig']['theme'] = '';
				$SiteConfig = ClassRegistry::getObject('SiteConfig');
				$SiteConfig->saveKeyValue($siteConfig);
			}
			return true;
		} else {
			return false;
		}
		
	}
/**
 * テーマを削除する
 *
 * @param string $theme
 * @return void
 * @access public
 */
	function admin_del($theme){

		if(!$theme){
			$this->notFound();
		}
		$siteConfig = array('SiteConfig'=>$this->siteConfigs);
		$path = WWW_ROOT.'themed'.DS.$theme;
		$folder = new Folder();
		$folder->delete($path);
		if($theme == $siteConfig['SiteConfig']['theme']){
			$siteConfig['SiteConfig']['theme'] = '';
			$SiteConfig = ClassRegistry::getObject('SiteConfig');
			$SiteConfig->saveKeyValue($siteConfig);
		}
		clearViewCache();
		$this->Session->setFlash('テーマ「'.$theme.'」を削除しました。');
		$this->redirect(array('action' => 'index'));

	}
/**
 * テーマを適用する
 *
 * @param string $theme
 * @return void
 * @access public
 */
	function admin_apply($theme){
		
		if(!$theme){
			$this->notFound();
		}
		$siteConfig['SiteConfig']['theme'] = $theme;
		$SiteConfig = ClassRegistry::getObject('SiteConfig');
		$SiteConfig->saveKeyValue($siteConfig);
		clearViewCache();
		if(!$this->Page->createAllPageTemplate()){
				$this->Session->setFlash('テーマ変更中にページテンプレートの生成に失敗しました。<br />「pages」フォルダに書き込み権限が付与されていない可能性があります。<br />テーマの適用をやり直すか、表示できないページについてページ管理より更新処理を行ってください。');
		} else {
			$this->Session->setFlash('テーマ「'.$theme.'」を適用しました。');
		}
		$this->redirect(array('action' => 'index'));
		
	}

}
