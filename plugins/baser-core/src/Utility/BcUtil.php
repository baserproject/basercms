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

namespace BaserCore\Utility;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\Routing\Router;

/**
 * Class BcUtil
 *
 * @package Baser.Lib
 */
class BcUtil {

/**
 * ログインユーザーのデータを取得する
 *
 * @return Entity
 */
	public static function loginUser($prefix = 'Admin') {
	    $session = Router::getRequest()->getSession();
	    $sessionKey = Configure::read('BcPrefixAuth.' . $prefix . '.sessionKey');
		$user = $session->read($sessionKey);
		if (!$user) {
			if (!empty($_SESSION[$sessionKey])) {
				$user = $_SESSION[$sessionKey];
			}
		}
		return $user;
	}

}
