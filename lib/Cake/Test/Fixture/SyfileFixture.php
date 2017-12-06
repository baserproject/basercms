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
 * Class SyfileFixture
 *
 * @package       Cake.Test.Fixture
 */
class SyfileFixture extends CakeTestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'key' => 'primary'],
		'image_id' => ['type' => 'integer', 'null' => true],
		'name' => ['type' => 'string', 'null' => false],
		'item_count' => ['type' => 'integer', 'null' => true]
	];

/**
 * records property
 *
 * @var array
 */
	public $records = [
		['image_id' => 1, 'name' => 'Syfile 1'],
		['image_id' => 2, 'name' => 'Syfile 2'],
		['image_id' => 5, 'name' => 'Syfile 3'],
		['image_id' => 3, 'name' => 'Syfile 4'],
		['image_id' => 4, 'name' => 'Syfile 5'],
		['image_id' => null, 'name' => 'Syfile 6']
	];
}
