<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			SinglePage.Controller
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * SinglePageConfigsController
 *
 * @package SinglePage.Controller
 * @property SinglePageConfig $SinglePageConfig
 */
class SinglePageConfigsController extends AppController {

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('Cookie', 'BcAuth', 'BcAuthConfigure', 'BcContents' => array(
		'useForm' 	=> true,
		'type'		=> 'SinglePage.SinglePage'
	));

/**
 * サブメニュー
 *
 * @var array
 */
	public $subMenuElements = array('single_pages');

/**
 * 編集
 */
	public function admin_edit() {
		$this->pageTitle = 'シングルページ編集';
		if(!$this->request->data) {
			$this->request->data = array('SinglePageConfig' => $this->SinglePageConfig->findExpanded()) + $this->Content->findByType('SinglePage.SinglePage');
		} else {
			$this->SinglePageConfig->getDataSource()->begin();
			if($this->SinglePageConfig->saveKeyValue($this->request->data) && $this->Content->createContent($this->request->data, 'SinglePage', 'SinglePage')) {
				$this->SinglePageConfig->getDataSource()->commit();
				$this->setMessage("シングルページ「{$this->request->data['Content']['title']}」を更新しました。", false, true);
				$this->redirect(array());
			} else {
				$this->SinglePageConfig->getDataSource()->rollback();
				$this->setMessage('保存中にエラーが発生しました。入力内容を確認してください。', true, false);
			}
		}
		$this->set('publishLink', $this->request->data['Content']['url']);
	}

/**
 * 削除
 */
	public function admin_delete() {
		$this->SinglePageConfig->getDataSource()->begin();
		if($this->Content->deleteByType('SinglePage.SinglePage') && $this->SinglePageConfig->deleteAll("1=1")) {
			$this->SinglePageConfig->getDataSource()->commit();
			return true;
		}
		$this->SinglePageConfig->getDataSource()->rollback();
		return false;
	}

}