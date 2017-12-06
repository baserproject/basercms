<?php
/**
 * This is Acl Schema file
 *
 * Use it to configure database for ACL
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config.Schema
 * @since         CakePHP(tm) v 0.2.9
 */

/**
 * Using the Schema command line utility
 * cake schema run create DbAcl
 */
class DbAclSchema extends CakeSchema {

/**
 * Before event.
 *
 * @param array $event The event data.
 * @return bool success
 */
	public function before($event = []) {
		return true;
	}

/**
 * After event.
 *
 * @param array $event The event data.
 * @return void
 */
	public function after($event = []) {
	}

/**
 * ACO - Access Control Object - Something that is wanted
 */
	public $acos = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'model' => ['type' => 'string', 'null' => true],
		'foreign_key' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'alias' => ['type' => 'string', 'null' => true],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]]
	];

/**
 * ARO - Access Request Object - Something that wants something
 */
	public $aros = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
		'parent_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'model' => ['type' => 'string', 'null' => true],
		'foreign_key' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'alias' => ['type' => 'string', 'null' => true],
		'lft' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'rght' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 10],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]]
	];

/**
 * Used by the Cake::Model:Permission class.
 * Checks if the given $aro has access to action $action in $aco.
 */
	public $aros_acos = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'primary'],
		'aro_id' => ['type' => 'integer', 'null' => false, 'length' => 10, 'key' => 'index'],
		'aco_id' => ['type' => 'integer', 'null' => false, 'length' => 10],
		'_create' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2],
		'_read' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2],
		'_update' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2],
		'_delete' => ['type' => 'string', 'null' => false, 'default' => '0', 'length' => 2],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1], 'ARO_ACO_KEY' => ['column' => ['aro_id', 'aco_id'], 'unique' => 1]]
	];

}
