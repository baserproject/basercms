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
App::import('Component', 'Auth');
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
	var $serial = '';
/**
 * Identifies a user based on specific criteria.
 *
 * @param mixed $user Optional. The identity of the user to be validated.
 *              Uses the current user session if none specified.
 * @param array $conditions Optional. Additional conditions to a find.
 * @return array User record data, or null, if the user could not be identified.
 * @access public
 */
	function identify($user = null, $conditions = null) {
		
		if ($conditions === false) {
			$conditions = null;
		} elseif (is_array($conditions)) {
			$conditions = array_merge((array)$this->userScope, $conditions);
		} else {
			$conditions = $this->userScope;
		}
		if (empty($user)) {
			$user = $this->user();
			if (empty($user)) {
				return null;
			}
		} elseif (is_object($user) && is_a($user, 'Model')) {
			if (!$user->exists()) {
				return null;
			}
			$user = $user->read();
			$user = $user[$this->userModel];
		} elseif (is_array($user) && isset($user[$this->userModel])) {
			$user = $user[$this->userModel];
		}

		// >>> ADD
		$model =& $this->getModel();
		$alias = $model->alias;
		// <<<
		
		if (is_array($user) && (isset($user[$this->fields['username']]) || isset($user[$this->userModel . '.' . $this->fields['username']]))) {

			// >>> MODIFY
			/*if (isset($user[$this->fields['username']]) && !empty($user[$this->fields['username']])  && !empty($user[$this->fields['password']])) {
				if (trim($user[$this->fields['username']]) == '=' || trim($user[$this->fields['password']]) == '=') {
					return false;
				}
				$find = array(
					$this->userModel.'.'.$this->fields['username'] => $user[$this->fields['username']],
					$this->userModel.'.'.$this->fields['password'] => $user[$this->fields['password']]
				);
			} elseif (isset($user[$this->userModel . '.' . $this->fields['username']]) && !empty($user[$this->userModel . '.' . $this->fields['username']])) {
				if (trim($user[$this->userModel . '.' . $this->fields['username']]) == '=' || trim($user[$this->userModel . '.' . $this->fields['password']]) == '=') {
					return false;
				}
				$find = array(
					$this->userModel.'.'.$this->fields['username'] => $user[$this->userModel . '.' . $this->fields['username']],
					$this->userModel.'.'.$this->fields['password'] => $user[$this->userModel . '.' . $this->fields['password']]
				);
			} else {
				return false;
			}
			$model =& $this->getModel();
			$data = $model->find(array_merge($find, $conditions), null, null, 0);
			if (empty($data) || empty($data[$this->userModel])) {
				return null;
			}*/
			// ---
			if (isset($user[$this->fields['username']]) && !empty($user[$this->fields['username']])  && !empty($user[$this->fields['password']])) {
				if (trim($user[$this->fields['username']]) == '=' || trim($user[$this->fields['password']]) == '=') {
					return false;
				}
				$find = array(
					$alias.'.'.$this->fields['username'] => $user[$this->fields['username']],
					$alias.'.'.$this->fields['password'] => $user[$this->fields['password']]
				);
			} elseif (isset($user[$this->userModel . '.' . $this->fields['username']]) && !empty($user[$this->userModel . '.' . $this->fields['username']])) {
				if (trim($user[$this->userModel . '.' . $this->fields['username']]) == '=' || trim($user[$this->userModel . '.' . $this->fields['password']]) == '=') {
					return false;
				}
				$find = array(
					$alias.'.'.$this->fields['username'] => $user[$this->userModel . '.' . $this->fields['username']],
					$alias.'.'.$this->fields['password'] => $user[$this->userModel . '.' . $this->fields['password']]
				);
			} else {
				return false;
			}
			$data = $model->find(array_merge($find, $conditions), null, null, 0);
			if (empty($data) || empty($data[$alias])) {
				return null;
			}
			// <<<

		} elseif (!empty($user) && is_string($user)) {
			$model =& $this->getModel();
			$data = $model->find(array_merge(array($model->escapeField() => $user), $conditions));

			// >>> MODIFY
			/*if (empty($data) || empty($data[$this->userModel])) {
				return null;
			}*/
			// ---
			if (empty($data) || empty($data[$alias])) {
				return null;
			}
			// <<<
		}

		if (!empty($data)) {
			// >>> MODIFY
			/*if (!empty($data[$alias][$this->fields['password']])) {
				unset($data[$alias][$this->fields['password']]);
			}
			return $data[$this->userModel];*/
			// ---
			if (!empty($data[$alias][$this->fields['password']])) {
				unset($data[$alias][$this->fields['password']]);
			}
			return $data[$alias];
			// <<<
		}
		return null;
		
	}
/**
 * Manually log-in a user with the given parameter data.  The $data provided can be any data
 * structure used to identify a user in AuthComponent::identify().  If $data is empty or not
 * specified, POST data from Controller::$data will be used automatically.
 *
 * After (if) login is successful, the user record is written to the session key specified in
 * AuthComponent::$sessionKey.
 *
 * @param mixed $data User object
 * @return boolean True on login success, false on failure
 * @access public
 */
	function login($data = null) {
		
		// CUSTOMIZE ADD 2011/09/25 ryuring
		// 簡単ログイン
		// >>>
		if(!empty($this->fields['serial']) && !$data) {
			$serial = $this->getSerial();
			$Model = $model =& $this->getModel();
			if($serial) {
				$data = $Model->find('first', array('conditions' => array($Model->alias.'.'.$this->fields['serial'] => $serial), 'recursive' => -1));
			}
		}
		// <<<
		
		// CUSTOMIZE ADD 2011/09/25 ryuring
		// ログイン時点でもモデルを保存しておく Session::user() のキーとして利用する
		// >>>
		$result = parent::login($data);
		if($result) {
			$this->Session->write('Auth.userModel', $this->userModel);
		}
		return $result;
		// <<<
		
	}
/**
 * Logs a user out, and returns the login action to redirect to.
 *
 * @param mixed $url Optional URL to redirect the user to after logout
 * @return string AuthComponent::$loginAction
 * @see AuthComponent::$loginAction
 * @access public
 */
	function logout() {
		if(!empty($this->fields['serial'])) {
			$this->deleteSerial();
		}
		return parent::logout();
	}
/**
 * 個体識別IDを保存する
 * @return boolean 
 */
	function saveSerial() {
		$user = $this->user();
		if(!empty($this->fields['serial']) && $user) {
			$serial = $this->getSerial();
			$Model = $model =& $this->getModel();
			if($serial) {
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
	function deleteSerial() {
		$user = $this->user();
		if(!empty($this->fields['serial']) && $user) {
			$Model = $model =& $this->getModel();
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
	function getSerial() {
		
		if(!empty($_SERVER['HTTP_X_DCMGUID'])) {
			return $_SERVER['HTTP_X_DCMGUID'];
		} elseif(!empty($_SERVER['HTTP_X_UP_SUBNO'])) {
			return $_SERVER['HTTP_X_UP_SUBNO'];
		} elseif(!empty($_SERVER['HTTP_X_JPHONE_UID'])) {
			return $_SERVER['HTTP_X_JPHONE_UID'];
		}
		return '';
		
	}
/**
 * Get the current user from the session.
 *
 * @param string $key field to retrive.  Leave null to get entire User record
 * @return mixed User record. or null if no user is logged in.
 * @access public
 */
	function user($key = null) {
		$this->__setDefaults();
		if (!$this->Session->check($this->sessionKey)) {
			return null;
		}

		if ($key == null) {
			// CUSTOMIZE MODIFY 2013/02/27 ryuring
			// ユーザーモデルを複数扱う場合、認証設定をしたタイミングでのモデルがキーとして強制的に入る
			// 仕様となっている為、User固定となる仕様とした
			// そのモデルをキーとして入れる仕様に変更
			// >>>
			//return array($this->userModel => $this->Session->read($this->sessionKey));
			// ---
			return array('User' => $this->Session->read($this->sessionKey));
			// <<<
		} else {
			$user = $this->Session->read($this->sessionKey);
			if (isset($user[$key])) {
				return $user[$key];
			}
			return null;
		}
	}
	
}
