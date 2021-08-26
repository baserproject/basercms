<?php

/* WidgetAreas schema generated on: 2010-11-04 18:11:11 : 1288863011 */

class WidgetAreasSchema extends CakeSchema {

	public $name = 'WidgetAreas';

	public $file = 'widget_areas.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = []) {
	}

	public $widget_areas = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 4, 'key' => 'primary'],
		'name' => ['type' => 'string', 'null' => true, 'default' => null],
		'widgets' => ['type' => 'text', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
