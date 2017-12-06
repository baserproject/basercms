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
 * Class ProductFixture
 *
 * @package       Cake.Test.Fixture
 */
class ProductFixture extends CakeTestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'key' => 'primary'],
		'name' => ['type' => 'string', 'length' => 255, 'null' => false],
		'type' => ['type' => 'string', 'length' => 255, 'null' => false],
		'price' => ['type' => 'integer', 'null' => false]
	];

/**
 * records property
 *
 * @var array
 */
	public $records = [
		['name' => 'Park\'s Great Hits', 'type' => 'Music', 'price' => 19],
		['name' => 'Silly Puddy', 'type' => 'Toy', 'price' => 3],
		['name' => 'Playstation', 'type' => 'Toy', 'price' => 89],
		['name' => 'Men\'s T-Shirt', 'type' => 'Clothing', 'price' => 32],
		['name' => 'Blouse', 'type' => 'Clothing', 'price' => 34],
		['name' => 'Electronica 2002', 'type' => 'Music', 'price' => 4],
		['name' => 'Country Tunes', 'type' => 'Music', 'price' => 21],
		['name' => 'Watermelon', 'type' => 'Food', 'price' => 9]
	];
}
