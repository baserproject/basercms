<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class FavoritesController
 *
 * よく使う項目　コントローラー
 *
 * @package Baser.Controller
 * @property Favorite $Favorite
 */
class FavoritesController extends AppController
{

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
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Favorite->setSession($this->Session);
	}

	/**
	 * [ADMIN] よく使う項目を追加する（AJAX）
	 *
	 * @return void
	 */
	public function admin_ajax_add()
	{
		$this->autoRender = false;
		if (!$this->request->data) {
			return;
		}

		$user = $this->BcAuth->user();
		if (!$user) {
			exit();
		}
		$this->request->data['Favorite']['sort'] = $this->Favorite->getMax('sort') + 1;
		$this->request->data['Favorite']['user_id'] = $user['id'];

		$this->Favorite->create($this->request->data);
		$data = $this->Favorite->save();
		if (!$data) {
			$this->ajaxError(500, $this->Favorite->validationErrors);
			return;
		}
		$this->autoLayout = false;
		$data['Favorite']['id'] = $this->Favorite->id;
		$this->set('favorite', $data);
		$this->render('ajax_form');
	}

	/**
	 * [ADMIN] よく使う項目編集
	 *
	 * @param int $id
	 * @return void
	 */
	public function admin_ajax_edit($id)
	{
		$this->autoRender = false;
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		if (!$this->request->data) {
			return;
		}

		$this->Favorite->set($this->request->data);
		$data = $this->Favorite->save();
		if (!$data) {
			$this->ajaxError(500, $this->Favorite->validationErrors);
			return;
		}
		$this->autoLayout = false;
		$this->set('favorite', $data);
		$this->render('ajax_form');
	}

	/**
	 * [ADMIN] 削除
	 *
	 * @param int $id
	 */
	public function admin_ajax_delete()
	{
		if (!$this->request->data) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
			exit();
		}

		$name = $this->Favorite->field('name', ['Favorite.id' => $this->request->data['Favorite']['id']]);
		if ($this->Favorite->delete($this->request->data['Favorite']['id'])) {
			$this->Favorite->saveDbLog(sprintf(__d('baser', 'よく使う項目: %s を削除しました。'), $name));
			exit(true);
		}

		$this->ajaxError(500, __d('baser', '無効な処理です。'));
		exit();
	}

	/**
	 * [ADMIN] 並び替えを更新する
	 *
	 * @return void
	 */
	public function admin_update_sort()
	{
		$user = $this->BcAuth->user();
		if ($this->request->data) {
			if ($this->Favorite->changeSort($this->request->data['Sort']['id'], $this->request->data['Sort']['offset'], ['Favorite.user_id' => $user['id']])) {
				clearDataCache();
				exit(true);
			}
		}
		$this->ajaxError(400, __d('baser', '無効な処理です。'));
		exit();
	}

}
