<?php
/**
 * TestAppSchema file
 *
 * Use for testing the loading of schema files from plugins.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Test.TestApp.Plugin.TestPlugin.Config.Schema
 * @since         CakePHP(tm) v 1.3
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Class TestPluginAppSchema
 *
 * @package       Cake.Test.TestApp.Plugin.TestPlugin.Config.Schema
 */
class TestPluginAppSchema extends CakeSchema {

	public $name = 'TestPluginApp';

	public $test_plugin_acos = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'model' => ['type' => 'string', 'null' => true],
		'foreign_key' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'alias' => ['type' => 'string', 'null' => true],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]]
	];
}
