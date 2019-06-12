<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.Controller
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */

/**
 * ファイルアップローダーコントローラー
 *
 * @package			Uploader.Controller
 */
class UploaderConfigsController extends AppController {
/**
 * クラス名
 *
 * @var		string
 * @access	public
 */
	public $name = 'UploaderConfigs';
/**
 * モデル
 *
 * @var		array
 * @access	public
 */
	public $uses = array('Plugin', 'Uploader.UploaderConfig');
/**
 * コンポーネント
 *
 * @var		array
 * @access	public
 */
	public $components = array('BcAuth','Cookie','BcAuthConfigure');
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	public $subMenuElements = array('uploader');
/**
 * [ADMIN] アップローダー設定
 *
 * @return	void
 * @access	public
 */
	public function admin_index() {
		
		$this->pageTitle = __d('baser', 'アップローダープラグイン設定');
		if(!$this->request->data) {
			$this->request->data['UploaderConfig'] = $this->UploaderConfig->findExpanded();
		} else {
			$this->UploaderConfig->set($this->request->data);
			if($this->UploaderConfig->validates()) {
				$this->UploaderConfig->saveKeyValue($this->request->data);
				$this->setMessage(__d('baser', 'アップローダー設定を保存しました。'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage(__d('baser', '入力エラーです。内容を修正してください。'), true);
			}
		}
		
	}

}