<?php
/**
 * [PUBLISH] ページネーション
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
if (empty($this->Paginator)) {
	return;
}
if (!isset($modules)) {
	$modules = 8;
}
if (!isset($options)) {
	$options = array();
}
$pageCount = 0;
if (isset($this->Paginator->params['paging'][$this->Paginator->defaultModel()]['pageCount'])) {
	$pageCount = $this->Paginator->params['paging'][$this->Paginator->defaultModel()]['pageCount'];
}
?>
<div class="pagination clearfix">

	<?php if ($pageCount > 1): ?>
		<div class="page-numbers">
			<?php echo $this->Paginator->prev('< 前へ', array_merge(array('class' => 'prev'), $options), null, array('class' => 'prev disabled')) ?>
			<?php echo $this->Html->tag('span', $this->Paginator->numbers(array_merge(array('separator' => '', 'class' => 'number', 'modulus' => $modules), $options), array('class' => 'page-numbers'))) ?>
			<?php echo $this->Paginator->next('次へ >', array_merge(array('class' => 'next'), $options), null, array('class' => 'next disabled')) ?>
		</div>
	<?php endif ?>
	<div class="page-result">
		<?php echo $this->Paginator->counter(array('format' => '<span class="page-start-num">%start%</span>～<span class="page-end-num">%end%</span> 件 ／ <span class="page-total-num">%count%</span> 件')) ?>
	</div>
</div>
