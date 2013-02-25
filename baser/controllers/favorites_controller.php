<?php
/* SVN FILE: $Id$ */
/**
 * よく使う項目　コントローラー
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
/**
 * よく使う項目　コントローラー
 *
 * @package baser.controllers
 */
class FavoritesController extends AppController {
/**
 * クラス名
 * 
 * @var string
 * @access public
 */
	var $name = 'Favorites';
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcAuth','Cookie','BcAuthConfigure');

	function admin_ajax_add () {
		
		if($this->data) {
			$user = $this->BcAuth->user();
			if(!$user) {
				exit();
			}
			$this->data['Favorite']['sort'] = $this->Favorite->getMax('sort')+1;
			$this->data['Favorite']['user_id'] = $user['User']['id'];
			$this->Favorite->create($this->data);
			$data = $this->Favorite->save();
			if($data) {
				$data['Favorite']['id'] = $this->Favorite->id;
				$this->set('favorite', $data);
				$this->render('ajax_form');
				return;
			} else {
				$this->ajaxError(500, $this->Favorite->validationErrors);
			}
		}
		exit();
		
	}
/**
 * [ADMIN] よく使う項目編集
 * 
 * @param int $id
 * @return void
 * @access public
 */
	function admin_ajax_edit ($id) {
		
		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		
		if($this->data) {
			$this->Favorite->set($this->data);
			$data = $this->Favorite->save();
			if($data) {
				$this->set('favorite', $data);
				$this->render('ajax_form');
				return;
			} else {
				$this->ajaxError(500, $this->Favorite->validationErrors);
			}
		}
		
		exit();
		
	}
/**
 * [ADMIN] 削除
 * 
 * @param int $id 
 */
	function admin_ajax_delete () {
		
		if($this->data) {
			$name = $this->Favorite->field('name', array('Favorite.id' => $this->data['Favorite']['id']));
			if($this->Favorite->delete($this->data['Favorite']['id'])) {
				$this->Favorite->saveDbLog('よく使う項目: '.$name.' を削除しました。');
				exit(true);
			}
		} else {
			$this->ajaxError(500, '無効な処理です。');
		}
		exit();
		
	}
/**
 * [ADMIN] 並び替えを更新する
 *
 * @access public
 * @return boolean
 */
	function admin_update_sort () {

		if($this->data){
			if($this->Favorite->changeSort($this->data['Sort']['id'],$this->data['Sort']['offset'])){
				clearDataCache();
				exit(true);
			}
		}
		exit();

	}
	
}