<?php
/* SVN FILE: $Id$ */
/**
 * サイト内検索結果
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
?>

<h2 class="contents-head"><?php $bcBaser->contentsTitle() ?></h2>

<div class="section">
<?php if(!empty($paginator)): ?>
	<div class="search-result corner5">		
		<?php echo $paginator->counter(array('format' => '<strong>'.implode(' ', $query).'</strong> で検索した結果 <strong>%start%～%end%</strong>件目 / %count% 件')) ?>
	</div>
<?php endif ?>
	<!-- list-num -->
	<?php $bcBaser->element('admin/list_num') ?>
</div>

<?php if($datas): ?>
	<?php foreach($datas as $data): ?>
<div class="section">
	<h3 class="result-head"><?php $bcBaser->link($bcBaser->mark($query, $data['Content']['title']), $data['Content']['url']) ?></h3>
	<p class="result-body"><?php echo $bcBaser->mark($query, $bcText->mbTruncate($data['Content']['detail'],100)) ?></p>
	<p class="result-link"><small><?php $bcBaser->link(fullUrl($data['Content']['url']), $data['Content']['url']) ?></small></p>
</div>
	<?php endforeach ?>
<?php else: ?>
<div class="section">
	<p class="no-data">該当する結果が存在しませんでした。</p>
</div>
<?php endif ?>

<div class="clearfix section">
	<!-- pagination -->
	<?php $bcBaser->pagination('simple', array(), null, false) ?>
</div>