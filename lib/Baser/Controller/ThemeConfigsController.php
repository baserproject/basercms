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
 * テーマ設定コントローラー
 *
 * @package Baser.Controller
 * @property ThemeConfig $ThemeConfig
 */
class ThemeConfigsController extends AppController {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'ThemeConfigs';

/**
 * モデル
 *
 * @var array
 */
	public $uses = ['ThemeConfig'];

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

/**
 * サブメニューエレメント
 *
 * @var array
 */
	public $subMenuElements = ['themes'];

/**
 * ぱんくずナビ
 *
 * @var array
 */
	public $crumbs = [
		['name' => 'テーマ管理', 'url' => ['controller' => 'themes', 'action' => 'index']]
	];

/**
 * [ADMIN] 設定編集
 */
	public function admin_form() {
		$this->pageTitle = 'テーマ設定';
		$this->help = 'theme_configs_form';

		if (empty($this->request->data)) {
			$this->request->data = ['ThemeConfig' => $this->ThemeConfig->findExpanded()];
		} else {

			$this->ThemeConfig->set($this->request->data);
			if (!$this->ThemeConfig->validates()) {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			} else {
				$this->ThemeConfig->updateColorConfig($this->request->data);
				$data = $this->ThemeConfig->saveImage($this->request->data);
				$data = $this->ThemeConfig->deleteImage($data);
				foreach($data['ThemeConfig'] as $key => $value) {
					if(preg_match('/main_image_[0-9]_delete/', $key)) {
						unset($data['ThemeConfig'][$key]);
					}
				}
				if ($this->ThemeConfig->saveKeyValue($data)) {
					clearViewCache();
					$this->setMessage('システム設定を保存しました。');
					$this->redirect(['action' => 'form']);
				} else {
					$this->setMessage('保存中にエラーが発生しました。', true);
				}
			}
		}
	}

}
