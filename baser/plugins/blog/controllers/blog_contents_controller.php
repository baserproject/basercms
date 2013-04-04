<?php
/* SVN FILE: $Id$ */
/**
 * ブログコンテンツコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * ブログコンテンツコントローラー
 *
 * @package baser.plugins.blog.controllers
 */
class BlogContentsController extends BlogAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'BlogContents';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('SiteConfig', 'Blog.BlogCategory', 'Blog.BlogContent');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_HTML_HELPER, BC_TIME_HELPER, BC_FORM_HELPER, 'Blog.Blog');
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
 * @var string
 * @access public
 */
	var $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')),
		array('name' => 'ブログ管理', 'url' => array('controller' => 'blog_contents', 'action' => 'index'))
	);
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * before_filter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {
		
		parent::beforeFilter();
		if(isset($this->params['prefix']) && $this->params['prefix']=='admin') {
			$this->subMenuElements = array('blog_common');
		}
		
	}
/**
 * [ADMIN] ブログコンテンツ一覧
 *
 * @return void
 * @access public
 */
	function admin_index() {

		$datas = $this->BlogContent->find('all',array('order'=>array('BlogContent.id')));
		$this->set('datas', $datas);
		
		if($this->RequestHandler->isAjax() || !empty($this->params['url']['ajax'])) {
			$this->render('ajax_index');
			return;
		}
		
		$this->pageTitle = 'ブログ一覧';
		$this->help = 'blog_contents_index';

	}
/**
 * [ADMIN] ブログコンテンツ追加
 *
 * @return void
 * @access public
 */
	function admin_add() {

		$this->pageTitle = '新規ブログ登録';

		if(!$this->data) {
			
			$this->data = $this->BlogContent->getDefaultValue();
			
		}else {

			$this->data = $this->BlogContent->deconstructEyeCatchSize($this->data);
			$this->BlogContent->create($this->data);
			
			if($this->BlogContent->save()) {

				$id = $this->BlogContent->getLastInsertId();
				$this->setMessage('新規ブログ「'.$this->data['BlogContent']['title'].'」を追加しました。', false, true);
				$this->redirect(array('action' => 'edit', $id));

			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}

			$this->data = $this->BlogContent->constructEyeCatchSize($this->data);
			
		}

		// テーマの一覧を取得
		$this->set('themes',$this->SiteConfig->getThemes());
		$this->render('form');

	}
/**
 * [ADMIN] 編集処理
 *
 * @param int $id
 * @return void
 * @access public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		if(empty($this->data)) {
			
			$this->data = $this->BlogContent->read(null, $id);
			$this->data = $this->BlogContent->constructEyeCatchSize($this->data);
			
		}else {
			
			$this->data = $this->BlogContent->deconstructEyeCatchSize($this->data);
			$this->BlogContent->set($this->data);
			
			if($this->BlogContent->save()) {

				$this->setMessage('ブログ「'.$this->data['BlogContent']['title'].'」を更新しました。', false, true);

				if($this->data['BlogContent']['edit_layout_template']){
					$this->redirectEditLayout($this->data['BlogContent']['layout']);
				}elseif ($this->data['BlogContent']['edit_blog_template']) {
					$this->redirectEditBlog($this->data['BlogContent']['template']);
				}else{
					$this->redirect(array('action' => 'edit', $id));
				}
				
			}else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
			
			$this->data = $this->BlogContent->constructEyeCatchSize($this->data);

		}

		$this->set('publishLink', '/'.$this->data['BlogContent']['name'].'/index');
		
		/* 表示設定 */
		$this->set('blogContent',$this->data);
		$this->subMenuElements = array('blog_posts','blog_categories','blog_common');
		$this->set('themes',$this->SiteConfig->getThemes());
		$this->pageTitle = 'ブログ設定編集：'.$this->data['BlogContent']['title'];
		$this->help = 'blog_contents_form';
		$this->render('form');

	}
/**
 * レイアウト編集画面にリダイレクトする
 * 
 * @param string $template
 * @return void
 * @access public
 */
	function redirectEditLayout($template){
		
		$target = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.'layouts'.DS.$template.$this->ext;
		$sorces = array(BASER_PLUGINS.'blog'.DS.'views'.DS.'layouts'.DS.$template.$this->ext,
						BASER_VIEWS.'layouts'.DS.$template.$this->ext);
		if($this->siteConfigs['theme']){
			if(!file_exists($target)){
				foreach($sorces as $source){
					if(file_exists($source)){
						copy($source,$target);
						chmod($target,0666);
						break;
					}
				}
			}
			$this->redirect(array('plugin' => null, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], 'layouts', $template.$this->ext));
		}else{
			$this->setMessage('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。', true);
			$this->redirect(array('action' => 'index'));
		}
		
	}
/**
 * ブログテンプレート編集画面にリダイレクトする
 * 
 * @param string $template
 * @return void
 * @access public
 */
	function redirectEditBlog($template){
		$path = 'blog'.DS.$template;
		$target = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$path;
		$sorces = array(BASER_PLUGINS.'blog'.DS.'views'.DS.$path);
		if($this->siteConfigs['theme']){
			if(!file_exists($target.DS.'index'.$this->ext)){
				foreach($sorces as $source){
					if(is_dir($source)){
						$folder = new Folder();
						$folder->create(dirname($target), 0777);
						$folder->copy(array('from'=>$source,'to'=>$target,'chmod'=>0777,'skip'=>array('_notes')));
						break;
					}
				}
			}
			$path = str_replace(DS, '/', $path);
			$this->redirect(array('plugin' => null, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], 'etc', $path.'/index'.$this->ext));
		}else{
			$this->setMessage('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。', true);
			$this->redirect(array('action' => 'index'));
		}
	}
/**
 * [ADMIN] 削除処理
 *
 * @param int $id
 * @return void
 * @access public
 * @deprecated
 */
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$post = $this->BlogContent->read(null, $id);

		/* 削除処理 */
		if($this->BlogContent->del($id)) {
			$this->setMessage('ブログ「'.$post['BlogContent']['title'].'」 を削除しました。', false, true);
		}else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));

	}
/**
 * [ADMIN] Ajax 削除処理
 *
 * @param int $id
 * @return void
 * @access public
 */
	function admin_ajax_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		// メッセージ用にデータを取得
		$post = $this->BlogContent->read(null, $id);

		/* 削除処理 */
		if($this->BlogContent->del($id)) {
			$this->BlogContent->saveDbLog('ブログ「'.$post['BlogContent']['title'].'」 を削除しました。');
			echo true;
		}

		exit();

	}
/**
 * [ADMIN] データコピー（AJAX）
 * 
 * @param int $id 
 * @return void
 * @access public
 */
	function admin_ajax_copy($id) {
		
		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$result = $this->BlogContent->copy($id);
		if($result) {
			$this->set('data', $result);
		} else {
			$this->ajaxError(500, $this->BlogContent->validationErrors);
		}
		
	}
	
}
