<?php
/* SVN FILE: $Id$ */
/**
 * ブログコメントコントローラー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
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
 * ブログコメントコントローラー
 *
 * @package baser.plugins.blog.controllers
 */
class BlogCommentsController extends BlogAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'BlogComments';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('Blog.BlogCategory', 'Blog.BlogComment', 'Blog.BlogPost');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array();
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure','RequestHandler','EmailEx','Security','Captcha');
/**
 * ぱんくずナビ
 *
 * @var string
 * @access public
 */
	var $navis = array('ブログ管理' => array('controller' => 'blog_contents', 'action' => 'index'));
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {

		parent::beforeFilter();

		$this->AuthEx->allow('add','captcha', 'smartphone_add', 'smartphone_captcha');

		$navis = array();
		if(!empty($this->params['pass'][1])) {

			$dbDatas = $this->BlogPost->read(null,$this->params['pass'][1]);
			
			if(!$dbDatas) {
				$this->notFound();
			}
			
			$this->blogPost['BlogPost'] = $dbDatas['BlogPost'];
			$this->blogContent['BlogContent'] = $dbDatas['BlogContent'];
			$navis[$this->blogContent['BlogContent']['title'].'管理'] = array('controller' => 'blog_posts', 'action' => 'index', $this->blogContent['BlogContent']['id']);
			$navis[$this->blogPost['BlogPost']['name']] = array('controller' => 'blog_posts', 'action' => 'edit', $this->blogContent['BlogContent']['id'], $this->blogPost['BlogPost']['id']);

		}elseif(!empty($this->params['pass'][0])) {

			$dbDatas = $this->BlogPost->BlogContent->read(null,$this->params['pass'][0]);
			$this->blogContent['BlogContent'] = $dbDatas['BlogContent'];
			$navis[$this->blogContent['BlogContent']['title'].'管理'] = array('controller' => 'blog_posts', 'action' => 'index', $this->blogContent['BlogContent']['id']);

		}

		$this->navis = am($this->navis,$navis);
		if(!empty($this->params['prefix']) && $this->params['prefix']=='admin') {
			$this->subMenuElements = array('blog_posts','blog_categories','blog_common');
		}

		$this->Security->enabled = true;
		$this->Security->requireAuth('add');

	}
/**
 * beforeRender
 *
 * @return void
 * @access public
 */
	function beforeRender() {
		
		parent::beforeRender();
		$this->set('blogContent',$this->blogContent);
		
	}
/**
 * [ADMIN] ブログを一覧表示する
 *
 * @return void
 * @access public
 */
	function admin_index($blogContentId,$blogPostId=null) {

		if(!$blogContentId || empty($this->blogContent['BlogContent'])) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller' => 'blog_contents', 'action' => 'index'));
		}
		
		/* 検索条件 */
		if($blogPostId) {
			$conditions['BlogComment.blog_post_id'] = $blogPostId;
			$this->pageTitle = '記事 ['.$this->blogPost['BlogPost']['name'].'] のコメント一覧';
		}else {
			$conditions['BlogComment.blog_content_id'] = $blogContentId;
			$this->pageTitle = 'ブログ ['.$this->blogContent['BlogContent']['title'].'] のコメント一覧';
		}

		/* 画面情報設定 */
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('BlogPost', array('group' => $blogContentId, 'default' => $default));

		// データを取得
		$this->paginate = array('conditions'=>$conditions,
				'fields'=> array(),
				'order'	=> 'BlogComment.id',
				'limit'	=> $this->passedArgs['num']
		);

		$dbDatas = $this->paginate('BlogComment');
		$this->set('dbDatas',$dbDatas);

	}
/**
 * [ADMIN] 削除処理
 *
 * @param int $blogContentId
 * @param int $blogPostId
 * @param int $id
 * @return void
 * @access public
 */
	function admin_delete($blogContentId,$blogPostId,$id = null) {

		/* 除外処理 */
		if(!$blogContentId || !$id) {
			$this->notFound();
		}

		/* 削除処理 */
		if($this->BlogComment->del($id)) {
			if(isset($this->blogPost['BlogPost']['name'])) {
				$message = '記事「'.$this->blogPost['BlogPost']['name'].'」へのコメントを削除しました。';
			}else {
				$message = '記事「'.$this->blogContent['BlogContent']['title'].'」へのコメントを削除しました。';
			}
			$this->Session->setFlash($message);
			$this->BlogComment->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		if($blogPostId) {
			$this->redirect(array('action' => 'index', $blogContentId, $blogPostId));
		}else {
			$this->redirect(array('action' => 'index', $blogContentId));
		}

	}
/**
 * [ADMIN] コメントを公開状態に設定する
 * 
 * @param string $blogContentId
 * @param string $blogPostId
 * @param string $blogCommentId
 * @return void
 * @access public
 */
	function admin_publish($blogContentId,$blogPostId,$blogCommentId) {

		if(!$blogContentId || !$blogCommentId) {
			$this->notFound();
		}
		$blogComment['id'] = $blogCommentId;
		$blogComment['status'] = true;
		$this->BlogComment->set($blogComment);
		if($this->BlogComment->save()) {
			if(isset($this->blogPost['BlogPost']['name'])) {
				$message = '記事「'.$this->blogPost['BlogPost']['name'].'」へのコメントを公開状態に設定しました。';
			}else {
				$message = '記事「'.$this->blogContent['BlogContent']['title'].'」へのコメントを公開状態に設定しました。';
			}
			$this->Session->setFlash($message);
			$this->BlogComment->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		if($blogPostId) {
			$this->redirect(array('action' => 'index', $blogContentId, $blogPostId));
		}else {
			$this->redirect(array('action' => 'index', $blogContentId));
		}

	}
/**
 * [ADMIN] コメントを非公開状態に設定する
 * 
 * @param string $blogContentId
 * @param string $blogPostId
 * @param string $blogCommentId
 * @return void
 * @access public
 */
	function admin_unpublish($blogContentId,$blogPostId,$blogCommentId) {
		if(!$blogContentId || !$blogCommentId) {
			$this->notFound();
		}
		$blogComment['id'] = $blogCommentId;
		$blogComment['status'] = false;
		$this->BlogComment->set($blogComment);
		if($this->BlogComment->save()) {
			if(isset($this->blogPost['BlogPost']['name'])) {
				$message = '記事「'.$this->blogPost['BlogPost']['name'].'」へのコメントを非公開状態に設定しました。';
			}else {
				$message = '記事「'.$this->blogContent['BlogContent']['title'].'」へのコメントを非公開状態に設定しました。';
			}

			$this->Session->setFlash($message);
			$this->BlogComment->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		if($blogPostId) {
			$this->redirect(array('action' => 'index', $blogContentId, $blogPostId));
		}else {
			$this->redirect(array('action' => 'index', $blogContentId));
		}

	}
/**
 * [AJAX] ブログコメントを登録する
 * 
 * @param string $blogContentId
 * @param string $blogPostId
 * @return boolean
 * @access public
 */
	function add($blogContentId,$blogPostId) {
		if(!$this->data || !$blogContentId || !$blogPostId || empty($this->blogContent) || !$this->blogContent['BlogContent']['comment_use']) {
			$this->notFound();
		}else {

			// 画像認証を行う
			$captchaResult = true;
			if($this->blogContent['BlogContent']['auth_captcha']){
				$captchaResult = $this->Captcha->check($this->data['BlogComment']['auth_captcha']);
				if(!$captchaResult){
					$this->set('dbData',false);
					return false;
				} else {
					unset($this->data['BlogComment']['auth_captcha']);
				}
			}
			
			$result = $this->BlogComment->add($this->data,$blogContentId,$blogPostId,$this->blogContent['BlogContent']['comment_approve']);
			if($result && $captchaResult) {
				$this->_sendComment();
				$this->set('dbData',$result['BlogComment']);
			}else{
				$this->set('dbData',false);
			}
		}

	}
/**
 * [AJAX] ブログコメントを登録する
 * 
 * @param string $blogContentId
 * @param string $blogPostId
 * @return boolean
 * @access public
 */
	function smartphone_add($blogContentId,$blogPostId) {
		
		$this->setAction('add', $blogContentId, $blogPostId);
		
	}
/**
 * 認証用のキャプチャ画像を表示する
 * 
 * @return void
 * @access public
 */
    function captcha()
    {
		
        $this->Captcha->render();
		
    } 
/**
 * [SMARTPHONE] 認証用のキャプチャ画像を表示する
 * 
 * @return void
 * @access public
 */
    function smartphone_captcha()
    {
		
        $this->Captcha->render();
		
    } 
}
?>