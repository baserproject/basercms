<?php

/* BlogPosts schema generated on: 2011-08-20 02:08:54 : 1313774094 */

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
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
		'blog_content_id' => ['type' => 'integer', 'null' => true, 'length' => 8],
		'no' => ['type' => 'integer', 'null' => true],
		'name' => ['type' => 'string', 'null' => true, 'default' => null],
		'content' => ['type' => 'text', 'null' => true, 'default' => null],
		'detail' => ['type' => 'text', 'null' => true, 'default' => null],
		'blog_category_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'user_id' => ['type' => 'integer', 'null' => true, 'default' => null, 'length' => 8],
		'status' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'posts_date' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'content_draft' => ['type' => 'text', 'null' => true, 'default' => null],
		'detail_draft' => ['type' => 'text', 'null' => true, 'default' => null],
		'publish_begin' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'publish_end' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'exclude_search' => ['type' => 'boolean', 'null' => true, 'default' => null],
		'eye_catch' => ['type' => 'text', 'null' => true, 'default' => null],
		'created' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'modified' => ['type' => 'datetime', 'null' => true, 'default' => null],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
		'tableParameters' => ['charset' => 'utf8', 'collate' => 'utf8_general_ci']
	];

}
