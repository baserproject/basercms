<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] テーマ一覧　テーブル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<script type="text/javascript">
$(function(){
	$(".theme-popup").colorbox({inline:true, width:"60%"});
});
</script>

<ul class="list-panel clearfix">
	<?php if (!empty($baserThemes)): ?>
		<?php $key = 0 ?>
		<?php foreach ($baserThemes as $data): ?>
			<?php $this->BcBaser->element('themes/index_row_market', array('data' => $data, 'key' => $key++)) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<li class="no-data">変更できるテーマがありません。<br /><a href="http://basercms.net/themes/index" target="_blank">baserCMSの公式サイト</a>では無償のテーマが公開されています。</li>
		<?php endif; ?>
</ul>