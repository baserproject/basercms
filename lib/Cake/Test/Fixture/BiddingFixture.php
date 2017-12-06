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
 * @since         CakePHP(tm) v 1.3.14
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Short description for class.
 *
 * @package       Cake.Test.Fixture
 */
class BiddingFixture extends CakeTestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'key' => 'primary'],
		'bid' => ['type' => 'string', 'null' => false],
		'name' => ['type' => 'string', 'null' => false]
	];

/**
 * records property
 *
 * @var array
 */
	public $records = [
		['bid' => 'One', 'name' => 'Bid 1'],
		['bid' => 'Two', 'name' => 'Bid 2'],
		['bid' => 'Three', 'name' => 'Bid 3'],
		['bid' => 'Five', 'name' => 'Bid 5']
	];
}
