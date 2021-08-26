<?php

/* Pages schema generated on: 2013-03-23 04:03:08 : 1363981208 */

class PagesSchema extends CakeSchema {

	public $name = 'Pages';

	public $file = 'pages.php';

	public $connection = 'default';

	public function before($event = []) {
		return true;
	}

	public function after($event = [])
	{
		$db = ConnectionManager::getDataSource($this->connection);
		if (get_class($db) !== 'BcMysql') {
			return true;
		}

		if (isset($event['create'])) {
			switch ($event['create']) {
				case 'pages':
					$tableName = $db->config['prefix'] . 'pages';
					$db->query("ALTER TABLE {$tableName} CHANGE contents contents LONGTEXT");
					$db->query("ALTER TABLE {$tableName} CHANGE draft draft LONGTEXT");
					break;
			}
		}
	}

	public $pages = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'],
		'contents' => ['type' => 'text', 'null' => true, 'default' => null],
		'draft' => ['type' => 'text', 'null' => true, 'default' => null],
		'page_template' => ['type' => 'string', 'null' => true, 'default' => null],
		'code' => ['type' => 'text', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

}
