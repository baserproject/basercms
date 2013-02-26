<?php
/* SVN FILE: $Id$ */
/**
 * ブログタグコントローラー
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
 * ブログタグコントローラー
 *
 * @package baser.plugins.blog.controllers
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
	var $uses = array('Blog.BlogCategory', 'Blog.BlogTag');
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
				'limit'	=> $this->passedArgs['num'],
				'recursive' => 0
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
				$this->setMessage('タグ「'.$this->data['BlogTag']['name'].'」を追加しました。', false, true);
				$this->redirect(array('action' => 'index'));
			}else {
				$this->setMessage('エラーが発生しました。内容を確認してください。', true);
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
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('action' => 'index'));
		}

		if(empty($this->data)) {
			$this->data = $this->BlogTag->read(null, $id);
		} else {

			$this->BlogTag->set($this->data);
			if($this->BlogTag->save()) {
				$this->setMessage('タグ「'.$this->data['BlogTag']['name'].'」を更新しました。', false, true);
				$this->redirect(array('action' => 'index'));
			}else {
				$this->setMessage('エラーが発生しました。内容を確認してください。', true);
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
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('action' => 'index'));
		}

		$data = $this->BlogTag->read(null, $id);

		if($this->BlogTag->del($id)) {
			$this->setMessage('タグ「' . $this->BlogTag->data['BlogTag']['name'] . '」を削除しました。', false, true);
		}else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));

	}
/**
 * [ADMIN] 削除処理　(ajax)
 *
 * @param int $id
 * @return void
 * @access public
 */
	function admin_ajax_delete($id = null) {

		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$data = $this->BlogTag->read(null, $id);
		if($this->BlogTag->del($id)) {
			$message = 'タグ「' . $this->BlogTag->data['BlogTag']['name'] . '」を削除しました。';
			$this->BlogTag->saveDbLog($message);
			exit(true);
		}
		exit();

	}
/**
 * [ADMIN] 一括削除
 *
 * @param int $id
 * @return void
 * @access public
 */
	function _batch_del($ids) {
		if($ids) {
			foreach($ids as $id) {
				$data = $this->BlogTag->read(null, $id);
				if($this->BlogTag->del($id)) {
					$message = 'タグ「' . $this->BlogTag->data['BlogTag']['name'] . '」を削除しました。';
					$this->BlogTag->saveDbLog($message);
				}
			}
		}
		return true;
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
			} else {
				$this->ajaxError(500, $this->BlogTag->validationErrors);
			}
		} else {
			$this->ajaxError(500, '無効な処理です。');
		}

	}
	
}
