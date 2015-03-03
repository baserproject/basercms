<?php
/**
 * UserGroupFixture
 *
 */
class UserGroupFixture extends CakeTestFixture {

/**
 * Import
 *
 * @var array
 */
	public $import = array('connection' => 'baser');

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'unsigned' => false, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'title' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'auth_prefix' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 20, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'use_admin_globalmenu' => array('type' => 'boolean', 'null' => false, 'default' => '1'),
		'default_favorites' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '1',
			'name' => 'admins',
			'title' => 'システム管理',
			'auth_prefix' => 'admin',
			'use_admin_globalmenu' => 1,
			'default_favorites' => 'YTo2OntpOjA7YToyOntzOjQ6Im5hbWUiO3M6MjE6IuWbuuWumuODmuODvOOCuOeuoeeQhiI7czozOiJ1cmwiO3M6MTg6Ii9hZG1pbi9wYWdlcy9pbmRleCI7fWk6MTthOjI6e3M6NDoibmFtZSI7czoxODoi5paw552A5oOF5aCx566h55CGIjtzOjM6InVybCI7czozMDoiL2FkbWluL2Jsb2cvYmxvZ19wb3N0cy9pbmRleC8xIjt9aToyO2E6Mjp7czo0OiJuYW1lIjtzOjI0OiLjgYrllY/jgYTlkIjjgo/jgZvnrqHnkIYiO3M6MzoidXJsIjtzOjMxOiIvYWRtaW4vbWFpbC9tYWlsX2ZpZWxkcy9pbmRleC8xIjt9aTozO2E6Mjp7czo0OiJuYW1lIjtzOjIxOiLlj5fkv6Hjg6Hjg7zjg6vkuIDopqciO3M6MzoidXJsIjtzOjMzOiIvYWRtaW4vbWFpbC9tYWlsX21lc3NhZ2VzL2luZGV4LzEiO31pOjQ7YToyOntzOjQ6Im5hbWUiO3M6MTg6IuOCs+ODoeODs+ODiOS4gOimpyI7czozOiJ1cmwiO3M6MzM6Ii9hZG1pbi9ibG9nL2Jsb2dfY29tbWVudHMvaW5kZXgvMSI7fWk6NTthOjI6e3M6NDoibmFtZSI7czoxNToi44Kv44Os44K444OD44OIIjtzOjM6InVybCI7czoyMDoiamF2YXNjcmlwdDpjcmVkaXQoKTsiO319',
			'modified' => null,
			'created' => '2015-01-27 12:56:53'
		),
		array(
			'id' => '2',
			'name' => 'operators',
			'title' => 'サイト運営',
			'auth_prefix' => 'admin',
			'use_admin_globalmenu' => 0,
			'default_favorites' => 'YTo1OntpOjA7YToyOntzOjQ6Im5hbWUiO3M6MjE6IuWbuuWumuODmuODvOOCuOeuoeeQhiI7czozOiJ1cmwiO3M6MTg6Ii9hZG1pbi9wYWdlcy9pbmRleCI7fWk6MTthOjI6e3M6NDoibmFtZSI7czoxODoi5paw552A5oOF5aCx566h55CGIjtzOjM6InVybCI7czozMDoiL2FkbWluL2Jsb2cvYmxvZ19wb3N0cy9pbmRleC8xIjt9aToyO2E6Mjp7czo0OiJuYW1lIjtzOjI0OiLjgYrllY/jgYTlkIjjgo/jgZvnrqHnkIYiO3M6MzoidXJsIjtzOjMxOiIvYWRtaW4vbWFpbC9tYWlsX2ZpZWxkcy9pbmRleC8xIjt9aTozO2E6Mjp7czo0OiJuYW1lIjtzOjIxOiLlj5fkv6Hjg6Hjg7zjg6vkuIDopqciO3M6MzoidXJsIjtzOjMzOiIvYWRtaW4vbWFpbC9tYWlsX21lc3NhZ2VzL2luZGV4LzEiO31pOjQ7YToyOntzOjQ6Im5hbWUiO3M6MTg6IuOCs+ODoeODs+ODiOS4gOimpyI7czozOiJ1cmwiO3M6MzM6Ii9hZG1pbi9ibG9nL2Jsb2dfY29tbWVudHMvaW5kZXgvMSI7fX0=',
			'modified' => null,
			'created' => '2015-01-27 12:56:53'
		),
	);

}
