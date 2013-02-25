<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] 検索ボックス
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
if(empty($search)) {
	return;
}
?>

<div id="Search" class="clearfix">

	<h2 class="head">
		<?php $bcBaser->img('admin/head_search.png', array('width' => 53, 'height' => 16, 'alt' => '検索')) ?>
	</h2>

	<div class="body">
		<?php $bcBaser->element('searches/'.$search) ?>
	</div>

	<div class="clearfix close">
		<div id="CloseSearch">
			<a><?php $bcBaser->img('admin/btn_close.png', array('width' => 14, 'height' => 14, 'alt' => 'Close', 'class' => 'btn')) ?></a>
		</div>
	</div>

<!-- / #Search clearfix --></div>