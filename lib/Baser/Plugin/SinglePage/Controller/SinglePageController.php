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
 * SinglePageController
 *
 * @package SinglePage.Controller
 * @property SinglePageConfig $SinglePageConfig
 * @property Content $Content
 */
class SinglePageController extends AppController {

/**
 * モデル
 *
 * @var array
 */
	public $uses = array('SinglePage.SinglePageConfig');

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('BcContents');

/**
 * 表示
 */
	public function view() {
		$data = $this->SinglePageConfig->findExpanded();
		if($this->BcContents->preview == 'default' && $this->request->data) {
			$data = $this->request->data['SinglePageConfig'];
        }
		$this->set('data', $data);
		$this->set('editLink', array('plugin' => 'single_page', 'admin' => true, 'controller' => 'single_page_configs', 'action' => 'edit'));
	}

}