<?php
/* SVN FILE: $Id$ */
/**
 * テーマコントローラー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
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
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array('FormEx');
/**
 * パンくずナビ
 * @var array
 * @access public
 */
	var $navis = array(
		'システム設定'	=> array('controller' => 'site_configs', 'action' => 'form'), 
		'テーマ管理'	=> array('controller' => 'themes', 'action' => 'index')
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
		foreach($files[0] as $themename){
			if($themename != 'core'){
				$themeName = $title = $description = $author = $url = $screenshot = '';
				if(file_exists($path.DS.$themename.DS.'config.php')){
					include $path.DS.$themename.DS.'config.php';
				}
				if(file_exists($path.DS.$themename.DS.'screenshot.png')){
					$theme['screenshot'] = true;
				}else{
					$theme['screenshot'] = false;
				}
				$theme['name'] = $themename;
				$theme['title'] = $title;
				$theme['description'] = $description;
				$theme['author'] = $author;
				$theme['url'] = $url;
				$theme['version'] = $this->getThemeVersion($theme['name']);
				$themes[] = $theme;
			}
		}
		$themes[] = array(
			'name'=>'core',
			'title'=>'baserCMSコア',
			'version'=>$this->getBaserVersion(),
			'description'=>'baserCMSのコアファイル。現在のテーマにコピーして利用する事ができます。',
			'author'=>'basercms',
			'url'=>'http://basercms.net'
		);
		$this->set('themes',$themes);
		$this->subMenuElements = array('site_configs', 'themes');

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
		$this->render('form');
		
	}
/**
 * テーマをコピーする
 *
 * @param string $theme
 * @return void
 * @access public
 */
	function admin_copy($theme){

		if(!$theme){
			$this->notFound();
		}
		$path = WWW_ROOT.'themed'.DS.$theme;
		$newPath = $path.'_copy';
		while(true){
			if(!is_dir($newPath)){
				break;
			}
			$newPath .= '_copy';
		}
		$folder = new Folder();
		$folder->copy(array('from'=>$path,'to'=>$newPath,'mode'=>0777,'skip'=>array('_notes')));
		$this->Session->setFlash('テーマ「'.$theme.'」をコピーしました。');
		$this->redirect(array('action' => 'index'));

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
				$this->Session->setFlash('テーマ変更中にページテンプレートの生成に失敗しました。<br />表示できないページはページ管理より更新処理を行ってください。');
		} else {
			$this->Session->setFlash('テーマ「'.$theme.'」を適用しました。');
		}
		$this->redirect(array('action' => 'index'));
		
	}

}
?>