<?php
class BcUtil extends Object {
	
/**
 * 管理システムかチェック
 * 
 * 《注意》by ryuring
 * 処理の内容にCakeRequest や、Router::parse() を使おうとしたが、
 * Router::parse() を利用すると、Routing情報が書き換えられてしまうので利用できない。
 * Router::reload() や、Router::setRequestInfo() で調整しようとしたがうまくいかなかった。
 * 
 * @return boolean
 */
	public static function isAdminSystem() {
		
		$url = Configure::read('BcRequest.pureUrl');
		$adminPrefix = Configure::read('Routing.prefixes.0');
		return (boolean) (preg_match('/^' . $adminPrefix. '\//', $url) || preg_match('/^' . $adminPrefix. '$/', $url));
		
	}
	
/**
 * 管理ユーザーかチェック
 * 
 * @return boolean
 */
	public static function isAdminUser() {
		$user = self::loginUser();
		if(empty($user['UserGroup']['name'])) {
			return false;
		}
		return ($user['UserGroup']['name'] == 'admins');
	}

/**
 * ログインユーザーのデータを取得する
 * 
 * @return array
 */
	public static function loginUser() {
		$Session = new CakeSession();
		return $Session->read('Auth.User');
	}
	
/**
 * ログインしているユーザー名を取得
 * 
 * @return string
 */
	public static function loginUserName() {
		$user = self::loginUser();
		if(!empty($user['name'])) {
			return $user['name'];
		} else {
			return '';
		}
	}
	
}