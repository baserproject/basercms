<?php

/* SVN FILE: $Id: auth.php 2 2011-07-06 16:11:32Z ryuring $ */

/**
 * Authentication component
 *
 * Manages user logins and permissions.
 *
 * PHP versions 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller.components
 * @since         CakePHP(tm) v 0.10.0.1076
 * @version       $Revision: 2 $
 * @modifiedby    $LastChangedBy: ryuring $
 * @lastmodified  $Date: 2011-07-07 01:11:32 +0900 (木, 07 7 2011) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
App::uses('AuthComponent', 'Controller/Component');

/**
 * Authentication control component class
 *
 * Binds access control with user authentication and session management.
 *
 * @package cake
 * @subpackage cake.cake.libs.controller.components
 */
class BcAuthComponent extends AuthComponent {

/**
 * 個体識別ID
 * @var string 
 * CUSTOMIZE ADD 2011/09/25 ryuring
 */
	public $serial = '';

/**
 * Log a user in. If a $user is provided that data will be stored as the logged in user.  If `$user` is empty or not
 * specified, the request will be used to identify a user. If the identification was successful,
 * the user record is written to the session key specified in AuthComponent::$sessionKey. Logging in
 * will also change the session id in order to help mitigate session replays.
 *
 * @param array $user Either an array of user data, or null to identify a user using the current request.
 * @return boolean True on login success, false on failure
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#identifying-users-and-logging-them-in
 */
	public function login($user = null) {
		// CUSTOMIZE ADD 2011/09/25 ryuring
		// 簡単ログイン
		// >>>
		if (!empty($this->fields['serial']) && !$user) {
			$serial = $this->getSerial();
			$Model = $model = $this->getModel();
			if ($serial) {
				$user = $Model->find('first', array('conditions' => array($Model->alias . '.' . $this->fields['serial'] => $serial), 'recursive' => -1));
			}
		}
		// <<<
		// CUSTOMIZE ADD 2011/09/25 ryuring
		// ログイン時点でもモデルを保存しておく Session::user() のキーとして利用する
		// >>>
		$result = parent::login($user);
		if ($result) {
			$this->setSessionAuthAddition();
		}
		return $result;
		// <<<
	}

/**
 * Logs a user out, and returns the login action to redirect to.
 * Triggers the logout() method of all the authenticate objects, so they can perform
 * custom logout logic.  AuthComponent will remove the session data, so
 * there is no need to do that in an authentication object.  Logging out
 * will also renew the session id.  This helps mitigate issues with session replays.
 *
 * @return string AuthComponent::$logoutRedirect
 * @see AuthComponent::$logoutRedirect
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#logging-users-out
 */
	public function logout() {
		if (!empty($this->fields['serial'])) {
			$this->deleteSerial();
		}
		return parent::logout();
	}

/**
 * 個体識別IDを保存する
 * 
 * @return boolean 
 */
	public function saveSerial() {
		$user = $this->user();
		if (!empty($this->fields['serial']) && $user) {
			$serial = $this->getSerial();
			$Model = $model = $this->getModel();
			if ($serial) {
				$user[$this->userModel][$this->fields['serial']] = $serial;
				$Model->set($user);
				return $Model->save();
			}
		}
	}

/**
 * 個体識別IDを削除する
 * 
 * @return boolean
 */
	public function deleteSerial() {
		$user = $this->user();
		if (!empty($this->fields['serial']) && $user) {
			$Model = $model = $this->getModel();
			$user[$this->userModel][$this->fields['serial']] = '';
			$Model->set($user);
			return $Model->save();
		}
	}

/**
 * 個体識別IDを取得
 * 
 * @return string
 */
	public function getSerial() {
		if (!empty($_SERVER['HTTP_X_DCMGUID'])) {
			return $_SERVER['HTTP_X_DCMGUID'];
		} elseif (!empty($_SERVER['HTTP_X_UP_SUBNO'])) {
			return $_SERVER['HTTP_X_UP_SUBNO'];
		} elseif (!empty($_SERVER['HTTP_X_JPHONE_UID'])) {
			return $_SERVER['HTTP_X_JPHONE_UID'];
		}
		return '';
	}

/**
 * セッションキーをセットする
 * 
 * @param string $sessionKey
 */
	public function setSessionKey($sessionKey) {
		self::$sessionKey = $sessionKey;
	}

/**
 * 認証に関する付加情報を保存する
 * authPrefix
 * userModel
 */
	public function setSessionAuthAddition() {
		$authPrefix = $this->Session->read(BcAuthComponent::$sessionKey . '.authPrefix');
		$userModel = '';
		if (!$authPrefix) {
			$userModel = $this->authenticate['Form']['userModel'];
			$User = ClassRegistry::init($userModel);
			$authPrefix = $User->getAuthPrefix($this->user('name'));
			if (empty($authPrefix)) {
				$authPrefix = 'front';
			}
		}
		$this->Session->write(BcAuthComponent::$sessionKey . '.authPrefix', $authPrefix);
		$this->Session->write(BcAuthComponent::$sessionKey . '.userModel', $userModel);
	}

	public function authenticatedUserModel() {
		$this->Session->read(BcAuthComponent::$sessionKey . '.userModel');
	}

}
