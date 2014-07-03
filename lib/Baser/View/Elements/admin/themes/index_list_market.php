<?php
/**
 * [ADMIN] テーマ一覧　テーブル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
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
		<?php if(strtotime('2014-03-31 17:00:00') >= time()): ?>
		<li class="no-data">baserマーケットは、2014年3月31日 17時に公開です。お楽しみに！</li>
		<?php else: ?>
		<li class="no-data">baserマーケットのテーマを読み込めませんでした。</li>
		<?php endif ?>
	<?php endif; ?>
</ul>