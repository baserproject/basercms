<?php
/**
 * Short description for file.
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.Test.Fixture
 * @since         CakePHP(tm) v 1.2.0.4667
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Short description for class.
 *
 * @package       Cake.Test.Fixture
 */
class ContentAccountFixture extends CakeTestFixture {

	public $table = 'ContentAccounts';

/**
 * fields property
 *
 * @var array
 */
	public $fields = [
		'iContentAccountsId' => ['type' => 'integer', 'key' => 'primary'],
		'iContentId' => ['type' => 'integer'],
		'iAccountId' => ['type' => 'integer']
	];

/**
 * records property
 *
 * @var array
 */
	public $records = [
		['iContentId' => 1, 'iAccountId' => 1],
		['iContentId' => 2, 'iAccountId' => 2],
		['iContentId' => 3, 'iAccountId' => 3],
		['iContentId' => 4, 'iAccountId' => 4],
		['iContentId' => 1, 'iAccountId' => 2],
		['iContentId' => 2, 'iAccountId' => 3],
	];
}
