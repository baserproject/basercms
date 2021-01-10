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
 * Class ThemeConfigsController
 *
 * テーマ設定コントローラー
 *
 * @package Baser.Controller
 * @property ThemeConfig $ThemeConfig
 */
class ThemeConfigsController extends AppController
{

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
	 * Before Filter
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->crumbs = [
			['name' => __d('baser', 'テーマ管理'), 'url' => ['controller' => 'themes', 'action' => 'index']]
		];
	}

	/**
	 * [ADMIN] 設定編集
	 */
	public function admin_form()
	{
		$this->pageTitle = __d('baser', 'テーマ設定');
		$this->help = 'theme_configs_form';

		if (!$this->request->is(['post', 'put'])) {
			$this->request->data = ['ThemeConfig' => $this->ThemeConfig->findExpanded()];
			return;
		}

		if ($this->ThemeConfig->isOverPostSize()) {
			$this->BcMessage->setError(
				__d(
					'baser',
					'送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
					ini_get('post_max_size')
				)
			);
			$this->redirect(['action' => 'form']);
		}
		$this->ThemeConfig->set($this->request->data);
		if (!$this->ThemeConfig->validates()) {
			$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			return;
		}

		$this->ThemeConfig->updateColorConfig($this->request->data);
		$data = $this->ThemeConfig->saveImage($this->request->data);
		$data = $this->ThemeConfig->deleteImage($data);
		foreach($data['ThemeConfig'] as $key => $value) {
			if (preg_match('/main_image_[0-9]_delete/', $key)) {
				unset($data['ThemeConfig'][$key]);
			}
		}
		if (!$this->ThemeConfig->saveKeyValue($data)) {
			$this->BcMessage->setError(__d('baser', '保存中にエラーが発生しました。'));
			return;
		}

		clearViewCache();
		$this->BcMessage->setInfo(__d('baser', 'システム設定を保存しました。'));
		$this->redirect(['action' => 'form']);
	}

}
