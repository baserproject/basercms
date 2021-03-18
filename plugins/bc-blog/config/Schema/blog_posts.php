<?php

class BlogPostsSchema extends CakeSchema
{

	public $name = 'BlogPosts';

	public $file = 'blog_posts.php';

	public $connection = 'default';

	public function before($event = [])
	{
		return true;
	}

	public function after($event = [])
	{
		$db = ConnectionManager::getDataSource($this->connection);
		if (get_class($db) !== 'BcMysql') {
			return true;
		}

		if (isset($event['create'])) {
			switch($event['create']) {
				case 'blogposts':
					$tableName = $db->config['prefix'] . 'blog_posts';
					$db->query("ALTER TABLE {$tableName} CHANGE content content LONGTEXT");
					$db->query("ALTER TABLE {$tableName} CHANGE content_draft content_draft LONGTEXT");
					$db->query("ALTER TABLE {$tableName} CHANGE detail detail LONGTEXT");
					$db->query("ALTER TABLE {$tableName} CHANGE detail_draft detail_draft LONGTEXT");
					break;
			}
		}
	}

	public $blog_posts = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'],
		'blog_content_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'index'],
		'no' => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => false],
		'name' => ['type' => 'string', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'content' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'detail' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'blog_category_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8, 'unsigned' => false],
		'status' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'posts_date' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'content_draft' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'detail_draft' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'publish_begin' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'publish_end' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'exclude_search' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'eye_catch' => ['type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => [
			'PRIMARY' => ['column' => 'id', 'unique' => 1],
			'blog_content_id_no_index' => ['column' => ['blog_content_id', 'no'], 'unique' => 1]
		],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB']
	];

}
