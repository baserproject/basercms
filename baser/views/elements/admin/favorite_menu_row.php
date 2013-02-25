<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] よく使う項目　行
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if(!isset($count)) {
	$count = 0;
}
?>
<li id="FavoriteRow<?php echo $count ?>">
	<?php $bcBaser->link($favorite['Favorite']['name'], $favorite['Favorite']['url'], array('title' => Router::url($favorite['Favorite']['url'], true))) ?>
	<?php echo $bcForm->input('Favorite.id.'.$favorite['Favorite']['id'], array('type' => 'hidden', 'value' => $favorite['Favorite']['id'], 'class' => 'favorite-id')) ?>
	<?php echo $bcForm->input('Favorite.name.'.$favorite['Favorite']['id'], array('type' => 'hidden', 'value' => $favorite['Favorite']['name'], 'class' => 'favorite-name')) ?>
	<?php echo $bcForm->input('Favorite.url.'.$favorite['Favorite']['id'], array('type' => 'hidden', 'value' => $favorite['Favorite']['url'], 'class' => 'favorite-url')) ?>
</li>