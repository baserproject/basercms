<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマファイル管理メニュー
 */
$types = [
	'Layouts'	=> __d('baser', 'レイアウト'),
	'Elements'	=> __d('baser', 'エレメント'),
	'Emails'	=> __d('baser', 'Eメール'),
	'etc'		=> __d('baser', 'コンテンツ'),
	'css'		=> 'CSS',
	'img'		=> __d('baser', 'イメージ'),
	'js'		=> 'Javascript'
];
if ($theme == 'core') {
	$themeFiles = [0 => ['name' => '', 'title' => __d('baser', 'コア')]];
	$Plugin = ClassRegistry::init('Plugin');
	$plugins = $Plugin->find('all', ['fields' => ['name', 'title']]);
	$themeFiles = am($themeFiles, Hash::extract($plugins, '{n}.Plugin'));
} else {
	$themeFiles = [0 => ['name' => '', 'title' => $theme]];
}
?>


<?php foreach ($themeFiles as $themeFile): ?>
	<tr>
		<th>[<?php echo $themeFile['title'] ?>] <?php echo __d('baser', 'テーマ管理メニュー')?></th>
		<td>
			<ul class="cleafix">
				<?php foreach ($types as $key => $type): ?>
					<li><?php $this->BcBaser->link($type . '一覧', ['action' => 'index', $theme, $themeFile['name'], $key]) ?></li>
				<?php endforeach ?>
			</ul>
		</td>
	</tr>
<?php endforeach ?>