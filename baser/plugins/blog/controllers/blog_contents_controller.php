<?php
/* SVN FILE: $Id$ */
/**
 * ブログコンテンツコントローラー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.controllers
 * @since			Baser v 0.1.0
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
 * @package			baser.plugins.blog.controllers
 */
class BlogContentsController extends BlogAppController {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'BlogContents';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('SiteConfig',"Blog.BlogContent");
/**
 * ヘルパー
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('Html','TimeEx','FormEx','Blog.Blog');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array('Auth','Cookie','AuthConfigure');
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array('システム設定'=>'/admin/site_configs/form',
			'プラグイン設定'=>'/admin/plugins/index',
			'ブログ管理'=>'/admin/blog/blog_contents/index');
/**
 * サブメニューエレメント
 *
 * @var		string
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * before_filter
 *
 * @return	void
 * @access 	public
 */
	function beforeFilter() {
		parent::beforeFilter();
		if($this->params['prefix']=='admin') {
			$this->subMenuElements = array('blog_common');
		}
	}
/**
 * [ADMIN] ブログコンテンツ一覧
 *
 * @return  void
 * @access  public
 */
	function admin_index() {

		$listDatas = $this->BlogContent->find('all',array('order'=>array('BlogContent.id')));
		$this->set('listDatas',$listDatas);
		$this->pageTitle = 'ブログ一覧';

	}
/**
 * [ADMIN] ブログコンテンツ追加
 *
 * @return  void
 * @access  public
 */
	function admin_add() {

		$this->pageTitle = '新規ブログ登録';

		if(!$this->data) {
			$this->data = $this->_getDefaultValue();
		}else {

			/* 登録処理 */
			$this->BlogContent->create($this->data);

			// データを保存
			if($this->BlogContent->save()) {
				$id = $this->BlogContent->getLastInsertId();
				$message = '新規ブログ「'.$this->data['BlogContent']['title'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->BlogContent->saveDbLog($message);
				$this->redirect(array('controller'=>'blog_posts','action'=>'index',$id));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		// テーマの一覧を取得
		$this->set('themes',$this->SiteConfig->getThemes());
		$this->render('form');

	}
/**
 * [ADMIN] 編集処理
 *
 @ @param	int		blog_category_no
 * @return	void
 * @access 	public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		if(empty($this->data)) {
			$this->data = $this->BlogContent->read(null, $id);
			$this->set('blogContent',$this->data);
		}else {

			/* 更新処理 */
			if($this->BlogContent->save($this->data)) {
				$message = 'ブログ「'.$this->data['BlogContent']['title'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->BlogContent->saveDbLog($message);

				if($this->data['BlogContent']['edit_layout_template']){
					$this->redirectEditLayout($this->data['BlogContent']['layout']);
				}elseif ($this->data['BlogContent']['edit_blog_template']) {
					$this->redirectEditBlog($this->data['BlogContent']['template']);
				}else{
					$this->redirect(array('action'=>'index',$id));
				}
				
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('blog_posts','blog_categories','blog_common');
		$this->set('themes',$this->SiteConfig->getThemes());
		$this->pageTitle = 'ブログ設定編集：'.$this->data['BlogContent']['title'];
		$this->render('form');

	}
/**
 * レイアウト編集画面にリダイレクトする
 * @param	string	$template
 * @return	void
 * @access	public
 */
	function redirectEditLayout($template){
		$target = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.'layouts'.DS.$template.'.ctp';
		$sorces = array(BASER_PLUGINS.'blog'.DS.'views'.DS.'layouts'.DS.$template.'.ctp',
						BASER_VIEWS.'layouts'.DS.$template.'.ctp');
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
			$this->redirect(array('plugin'=>null,'controller'=>'theme_files','action'=>'edit',$this->siteConfigs['theme'],'layouts',$template.'.ctp'));
		}else{
			$this->Session->setFlash('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。');
			$this->redirect(array('action'=>'index'));
		}
	}
/**
 * ブログテンプレート編集画面にリダイレクトする
 * @param	string	$template
 * @return	void
 * @access	public
 */
	function redirectEditBlog($template){
		$path = 'blog'.DS.$template;
		$target = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$path;
		$sorces = array(BASER_PLUGINS.'blog'.DS.'views'.DS.$path);
		if($this->siteConfigs['theme']){
			if(!file_exists($target.DS.'index.ctp')){
				foreach($sorces as $source){
					if(is_dir($source)){
						$folder = new Folder();
						$folder->create(dirname($target), 0777);
						$folder->copy(array('from'=>$source,'to'=>$target,'chmod'=>0777,'skip'=>array('_notes')));
						break;
					}
				}
			}
			$this->redirect(array('plugin'=>null,'controller'=>'theme_files','action'=>'edit',$this->siteConfigs['theme'],'etc',$path.DS.'index.ctp'));
		}else{
			$this->Session->setFlash('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。');
			$this->redirect(array('action'=>'index'));
		}
	}
/**
 * [ADMIN] 削除処理
 *
 @ @param	int		blog_content_id
 * @return	void
 * @access 	public
 */
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		// メッセージ用にデータを取得
		$post = $this->BlogContent->read(null, $id);

		/* 削除処理 */
		if($this->BlogContent->del($id)) {
			$message = 'ブログ「'.$post['BlogContent']['title'].'」 を削除しました。';
			$this->Session->setFlash($message);
			$this->BlogContent->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'index'));

	}
/**
 * フォームの初期値を取得する
 *
 * @return  void
 * @access  protected
 */
	function _getDefaultValue() {

		$data['BlogContent']['comment_use'] = true;
		$data['BlogContent']['comment_approve'] = false;
		$data['BlogContent']['layout'] = 'default';
		$data['BlogContent']['template'] = 'default';
		$data['BlogContent']['list_count'] = 10;
		$data['BlogContent']['feed_count'] = 10;
		return $data;

	}
}
?>