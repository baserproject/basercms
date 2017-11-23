<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * よく使う項目　コントローラー
 *
 * @package Baser.Controller
 * @property Favorite $Favorite
 */
class FavoritesController extends AppController {

/**
 * クラス名
 * 
 * @var string
 */
	public $name = 'Favorites';

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

/**
 * beforeFilter
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Favorite->setSession($this->Session);
	}

/**
 * [ADMIN] よく使う項目を追加する（AJAX）
 *  
 * @return void
 */
	public function admin_ajax_add() {
		$this->autoRender = false;
		if ($this->request->data) {
			$user = $this->BcAuth->user();
			if (!$user) {
				exit();
			}
			$this->request->data['Favorite']['sort'] = $this->Favorite->getMax('sort') + 1;
			$this->request->data['Favorite']['user_id'] = $user['id'];

			$this->Favorite->create($this->request->data);
			$data = $this->Favorite->save();
			if ($data) {
				$this->autoLayout = false;
				$data['Favorite']['id'] = $this->Favorite->id;
				$this->set('favorite', $data);
				$this->render('ajax_form');
				return;
			} else {
				$this->ajaxError(500, $this->Favorite->validationErrors);
			}
		}
		return;
	}

/**
 * [ADMIN] よく使う項目編集
 * 
 * @param int $id
 * @return void
 */
	public function admin_ajax_edit($id) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		if ($this->request->data) {
			$this->Favorite->set($this->request->data);
			$data = $this->Favorite->save();
			if ($data) {
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
	public function admin_ajax_delete() {
		if ($this->request->data) {
			$name = $this->Favorite->field('name', ['Favorite.id' => $this->request->data['Favorite']['id']]);
			if ($this->Favorite->delete($this->request->data['Favorite']['id'])) {
				$this->Favorite->saveDbLog('よく使う項目: ' . $name . ' を削除しました。');
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
 * @return bool
 */
	public function admin_update_sort() {
		$user = $this->BcAuth->user();
		if ($this->request->data) {
			if ($this->Favorite->changeSort($this->request->data['Sort']['id'], $this->request->data['Sort']['offset'], ['Favorite.user_id' => $user['id']])) {
				clearDataCache();
				exit(true);
			}
		}
		$this->ajaxError(400, '無効な処理です。');
		exit();
	}

}
