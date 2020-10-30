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
 * @return array
 */
	public static function loginUser($prefix = 'admin') {
	    $session = Router::getRequest()->getSession();
	    // TODO 実装要
	    // >>>
		//$sessionKey = BcUtil::authSessionKey($prefix);
//		$user = $session->read('Auth.' . $sessionKey);
//		if (!$user) {
//			if (!empty($_SESSION['Auth'][$sessionKey])) {
//				$user = $_SESSION['Auth'][$sessionKey];
//			}
//		}
		// ---
		$sessionKey = 'AuthAdmin';
		$user = $session->read($sessionKey);
		// <<<
		return $user;
	}

}
