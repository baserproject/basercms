<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\View\Helper;
use Cake\View\Helper;

/**
 * Class BcAdminHelper
 * @package BaserCore\View\Helper
 * @uses BcAdminHelper
 */
class BcAdminHelper extends Helper {
    public function isAdminGlobalmenuUsed() {
        return true;
    }

/**
 * 管理画面の画面タイトルの横に配置するボタンをを追加する
 *
 * @param array $links ['url' => string or array, 'confirm' => 'confirm message', 'something attributes' => 'attr value']
 */
	public function addAdminMainBodyHeaderLinks($links) {
		$mainBodyHeaderLinks = $this->_View->get('mainBodyHeaderLinks');
		if($mainBodyHeaderLinks === null) {
			$mainBodyHeaderLinks = [];
		}
		$mainBodyHeaderLinks[] = $links;
		$this->_View->set('mainBodyHeaderLinks', $mainBodyHeaderLinks);
	}

}
