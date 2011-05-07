<?php
/* SVN FILE: $Id$ */
/**
 * ブログタグコントローラー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
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
 * ブログタグコントローラー
 *
 * @package			baser.plugins.blog.controllers
 */
class BlogTagsController extends BlogAppController {
/**
 * クラス名
 *
 * @var array
 * @access public
 */
	var $name = 'BlogTags';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('Blog.BlogTag');
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
	var $navis = array('ブログ管理'=>'/admin/blog/blog_contents/index');
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array('blog_common');
/**
 * [ADMIN] タグ一覧
 *
 * @return void
 * @access public
 */
	function admin_index () {

		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('BlogTag', array('default' => $default));

		$this->paginate = array(
				'order'	=> 'BlogTag.id',
				'limit'	=> $this->passedArgs['num']
		);
		$this->set('datas', $this->paginate('BlogTag'));

		$this->pageTitle = 'ブログタグ一覧';
		
	}
/**
 * [ADMIN] タグ登録
 *
 * @return void
 * @access public
 */
	function admin_add () {

		if(!empty($this->data)) {

			$this->BlogTag->create($this->data);
			if($this->BlogTag->save()) {
				$message = '記事「'.$this->data['BlogTag']['name'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->BlogTag->saveDbLog($message);
				$this->redirect(array('action' => 'index'));
			}else {
				$this->Session->setFlash('エラーが発生しました。内容を確認してください。');
			}

		}

		$this->pageTitle = '新規ブログタグ登録';
		$this->render('form');
		
	}
/**
 * [ADMIN] タグ編集
 *
 * @param int $id タグID
 * @return void
 * @access public
 */
	function admin_edit ($id) {

		if(!$id) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('action' => 'index'));
		}

		if(empty($this->data)) {
			$this->data = $this->BlogTag->read(null, $id);
		} else {

			$this->BlogTag->set($this->data);
			if($this->BlogTag->save()) {
				$message = '記事「'.$this->data['BlogTag']['name'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->BlogTag->saveDbLog($message);
				$this->redirect(array('action' => 'index'));
			}else {
				$this->Session->setFlash('エラーが発生しました。内容を確認してください。');
			}

		}

		$this->pageTitle = 'ブログタグ編集： '.$this->data['BlogTag']['name'];
		$this->render('form');

	}
/**
 * [ADMIN] 削除処理
 *
 * @param int $id
 * @return void
 * @access public
 */
	function admin_delete($id = null) {

		if(!$id) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('action' => 'index'));
		}

		$data = $this->BlogTag->read(null, $id);

		if($this->BlogTag->del($id)) {
			$message = $this->BlogTag->data['BlogTag']['name'].' を削除しました。';
			$this->Session->setFlash($message);
			$this->BlogTag->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action' => 'index'));

	}
/**
 * [ADMIN] AJAXタグ登録
 *
 * @return void
 * @access public
 */
	function admin_ajax_add () {

		if(!empty($this->data)) {
			$this->BlogTag->create($this->data);
			if($data = $this->BlogTag->save()) {
				$result = array($this->BlogTag->id => $data['BlogTag']['name']);
				$this->set('result', $result);
			}
		}

	}
	
}
?>