<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマファイル管理メニュー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$types = array(
	'layouts'	=> 'レイアウト',
	'elements'	=> 'エレメント',
	'etc'		=> 'コンテンツ',
	'css'		=> 'CSS',
	'img'		=> 'イメージ',
	'js'		=> 'Javascript'
);
if($theme == 'core'){
	$themeFiles = array(0=>array('name'=>'','title'=>'コア'));
	$Plugin = ClassRegistry::init('Plugin');
	$plugins = $Plugin->find('all',array('fields'=>array('name','title')));
	$themeFiles = am($themeFiles,Set::extract('/Plugin/.',$plugins));
}else{
	$themeFiles = array(0 => array('name' => '', 'title' => Inflector::camelize($theme)));
}
?>

<?php foreach($themeFiles as $themeFile): ?>
<tr>
	<th>[<?php echo $themeFile['title'] ?>] テーマ管理メニュー</th>
	<td>
		<ul class="cleafix">
<?php foreach($types as $key => $type): ?>
			<li><?php $bcBaser->link($type.'一覧', array('action' => 'index', $theme, $themeFile['name'], $key)) ?></li>
<?php endforeach ?>
		</ul>
	</td>
</tr>
<?php endforeach ?>