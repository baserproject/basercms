<?php
/**
 * BcNoPasswordHasher
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller.Component.Auth
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
App::uses('AbstractPasswordHasher', 'Controller/Component/Auth');
class BcNoPasswordHasher extends AbstractPasswordHasher {
/**
 * Generates password hash.
 *
 * @param string $password Plain text password to hash.
 * @return string Password hash
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#hashing-passwords
 */
	public function hash($password) {
		return $password;
	}

/**
 * Check hash. Generate hash for user provided password and check against existing hash.
 *
 * @param string $password Plain text password to hash.
 * @param string Existing hashed password.
 * @return boolean True if hashes match else false.
 */
	public function check($password, $hashedPassword) {
		return $hashedPassword === $this->hash($password);
	}
}