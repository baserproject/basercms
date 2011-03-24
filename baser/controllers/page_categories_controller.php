<?php
/* SVN FILE: $Id$ */
/**
 * ページカテゴリーコントローラー
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
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ページカテゴリーコントローラー
 *
 * @package       cake
 * @subpackage    cake.baser.controllers
 */
class PageCategoriesController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
	var $name = 'PageCategories';
/**
 * Default helper
 *
 * @var array
 * @access public
 */
	var $helpers = array('TextEx', 'FormEx');
/**
 * This controller does not use a model
 *
 * @var array
 * @access public
 */
	var $uses = array('PageCategory');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array('Auth','Cookie','AuthConfigure');
/**
 * パンくず
 * @var			array
 * @access	public
 */
	var $navis = array('ページ管理'=>'/admin/pages/index');
/**
 * [ADMIN] ページカテゴリーリスト
 *
 * @return	void
 * @access 	public
 */
	function admin_index() {

		if(!Configure::read('Baser.mobile')) {
			$conditions = array('PageCategory.id <>' => $this->PageCategory->getMobileId());
		} else {
			$conditions = array();
		}
		$_dbDatas = $this->PageCategory->generatetreelist($conditions);
		$dbDatas = array();
		foreach($_dbDatas as $key => $dbData) {
			$category = $this->PageCategory->find(array('PageCategory.id'=>$key));
			if(preg_match("/^([_]+)/i",$dbData,$matches)) {
				$prefix = str_replace('_','&nbsp&nbsp&nbsp',$matches[1]);
				$category['PageCategory']['title'] = $prefix.'└'.$category['PageCategory']['title'];
			}
			$dbDatas[] = $category;
		}
		$this->set('dbDatas',$dbDatas);
		/* 表示設定 */
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = 'ページカテゴリー一覧';

	}
/**
 * [ADMIN] ページカテゴリー情報登録
 *
 * @return	void
 * @access 	public
 */
	function admin_add() {

		if(empty($this->data)) {
			$this->data = array('PageCategory' => array('contents_navi' => false));
		} else {

			/* 登録処理 */
			$this->PageCategory->create($this->data);

			if($this->PageCategory->validates()) {
				if($this->PageCategory->save($this->data,false)) {
					$message = 'ページカテゴリー「'.$this->data['PageCategory']['name'].'」を追加しました。';
					if(ini_get('safe_mode')) {
						$message .= '<br />機能制限のセーフモードで動作しているので、手動で次のフォルダ内に追加したカテゴリと同階層のフォルダを作成し、書込権限を与える必要があります。<br />'.
									WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.'pages'.DS;
					}
					$this->Session->setFlash($message);
					$this->PageCategory->saveDbLog('ページカテゴリー「'.$this->data['PageCategory']['name'].'」を追加しました。');
					$this->redirect('/admin/page_categories/index');
				}else {
					$this->Session->setFlash('保存中にエラーが発生しました。');
				}
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = '新規ページカテゴリー登録';
		$this->render('form');

	}
/**
 * [ADMIN] ページカテゴリー情報編集
 *
 * @param	int		page_id
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
			$this->data = $this->PageCategory->read(null, $id);
		}else {

			/* 更新処理 */
			$this->PageCategory->set($this->data);

			if($this->PageCategory->validates()) {
				if($this->PageCategory->save($this->data,false)) {
					$this->Session->setFlash('ページカテゴリー「'.$this->data['PageCategory']['name'].'」を更新しました。');
					$this->PageCategory->saveDbLog('ページカテゴリー「'.$this->data['PageCategory']['name'].'」を更新しました。');
					$this->redirect(array('action'=>'admin_index'));
				}else {
					$this->Session->setFlash('保存中にエラーが発生しました。');
				}
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		$indexPage = array();
		if(isset($this->data['Page'])){
			foreach($this->data['Page'] as $page){
				if($page['name']=='index'){
					$indexPage['url'] = preg_replace('/^\/mobile\//is', '/m/', $page['url']);
					$indexPage['status'] = $page['status'];
					break;
				}
			}
		}
		$this->set('indexPage',$indexPage);

		/* 表示設定 */
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = 'ページカテゴリー情報編集';
		$this->render('form');

	}
/**
 * [ADMIN] ページカテゴリー情報削除
 *
 * @param	int		page_id
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
		$page = $this->PageCategory->read(null, $id);

		/* 削除処理 */
		if($this->PageCategory->del($id)) {
			$this->Session->setFlash('ページカテゴリー: '.$page['PageCategory']['name'].' を削除しました。');
			$this->PageCategory->saveDbLog('ページカテゴリー「'.$page['PageCategory']['name'].'」を削除しました。');
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'admin_index'));

	}

}
?>