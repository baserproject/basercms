<?php
/**
 * EditorTemplateFixture
 *
 */
class EditorTemplateFixture extends CakeTestFixture {

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
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'image' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'description' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'html' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
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
			'name' => '画像（左）とテキスト',
			'image' => 'template1.gif',
			'description' => '画像を左に配置し、その右にテキストを配置するブロックです。',
			'html' => '<div class="template-image-float-left clearfix">
	<div class="image">ここに画像を挿入します</div>
	<div class="text">
		<h2>見出しを挿入します。</h2>
		<p>1段落目のテキストを挿入します。</p>
		<p>2段落目のテキストを挿入します。</p>
	</div>
</div>
<p>新しいブロックを挿入します。不要な場合はこの段落を削除します</p>',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		),
		array(
			'id' => '2',
			'name' => '画像（右）とテキスト',
			'image' => 'template2.gif',
			'description' => '画像を右に配置し、その左にテキストを配置するブロックです。',
			'html' => '<div class="template-image-float-right clearfix">
	<div class="image">ここに画像を挿入します</div>
	<div class="text">
		<h2>見出しを挿入します。</h2>
		<p>1段落目のテキストを挿入します。</p>
		<p>2段落目のテキストを挿入します。</p>
	</div>
</div>
<p>新しいブロックを挿入します。不要な場合はこの段落を削除します</p>',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		),
		array(
			'id' => '3',
			'name' => 'テキスト２段組',
			'image' => 'template3.gif',
			'description' => 'テキストを左右に２段組するブロックです。',
			'html' => '<div class="template-two-block clearfix">
	<div class="block-left">
		<h2>
			見出しを挿入します。</h2>
		<p>
			1段落目のテキストを挿入します。</p>
		<p>
			2段落目のテキストを挿入します。</p>
	</div>
	<div class="block-right">
		<h2>
			見出しを挿入します。</h2>
		<p>
			1段落目のテキストを挿入します。</p>
		<p>
			2段落目のテキストを挿入します。</p>
	</div>
</div>
<p>
	新しいブロックを挿入します。不要な場合はこの段落を削除します</p>',
			'modified' => null,
			'created' => '2015-01-27 12:56:52'
		),
	);

}
