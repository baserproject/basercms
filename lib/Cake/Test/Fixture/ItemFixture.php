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
 * Class ItemFixture
 *
 * @package       Cake.Test.Fixture
 */
class ItemFixture extends CakeTestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'key' => 'primary'],
		'syfile_id' => ['type' => 'integer', 'null' => false],
		'published' => ['type' => 'boolean', 'null' => false],
		'name' => ['type' => 'string', 'null' => false]
	];

/**
 * records property
 *
 * @var array
 */
	public $records = [
		['syfile_id' => 1, 'published' => 0, 'name' => 'Item 1'],
		['syfile_id' => 2, 'published' => 0, 'name' => 'Item 2'],
		['syfile_id' => 3, 'published' => 0, 'name' => 'Item 3'],
		['syfile_id' => 4, 'published' => 0, 'name' => 'Item 4'],
		['syfile_id' => 5, 'published' => 0, 'name' => 'Item 5'],
		['syfile_id' => 6, 'published' => 0, 'name' => 'Item 6']
	];
}
