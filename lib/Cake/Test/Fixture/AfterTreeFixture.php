<?php
/**
 * Short description for after_tree_fixture.php
 *
 * Long description for after_tree_fixture.php
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://www.cakephp.org
 * @package       Cake.Test.Fixture
 * @since         1.2
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * AfterTreeFixture class
 *
 * @package       Cake.Test.Fixture
 */
class AfterTreeFixture extends CakeTestFixture {

/**
 * fields property
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'key' => 'primary'],
		'parent_id' => ['type' => 'integer'],
		'lft' => ['type' => 'integer'],
		'rght' => ['type' => 'integer'],
		'name' => ['type' => 'string', 'length' => 255, 'null' => false]
	];

/**
 * records property
 *
 * @var array
 */
	public $records = [
		['parent_id' => null, 'lft' => 1, 'rght' => 2, 'name' => 'One'],
		['parent_id' => null, 'lft' => 3, 'rght' => 4, 'name' => 'Two'],
		['parent_id' => null, 'lft' => 5, 'rght' => 6, 'name' => 'Three'],
		['parent_id' => null, 'lft' => 7, 'rght' => 12, 'name' => 'Four'],
		['parent_id' => null, 'lft' => 8, 'rght' => 9, 'name' => 'Five'],
		['parent_id' => null, 'lft' => 10, 'rght' => 11, 'name' => 'Six'],
		['parent_id' => null, 'lft' => 13, 'rght' => 14, 'name' => 'Seven']
	];
}
