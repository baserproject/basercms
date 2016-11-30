<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [PUBLISH] ページネーション
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


<div class="pagination bca-pagination">

	<?php if ($pageCount > 1): ?>
		<div class="page-numbers bca-page-numbers">
			<?php echo $this->Paginator->prev(' < ', array_merge(array('class' => 'prev'), $options), null, array('class' => 'prev disabled')) ?>
			<?php // ToDo : 我流 アイコンのリンクがHTMLで指定できるように
			//echo $this->Paginator->prev('<span class="bca-icon--arrow-left"><span class="bca-icon-label">前へ</span>', array_merge(array('class' => 'prev'), $options), null, array('class' => 'prev disabled')) ?>
			<?php echo $this->Html->tag('span', $this->Paginator->numbers(array_merge(array('separator' => '', 'class' => 'number', 'modulus' => $modules), $options), array('class' => 'page-numbers'))) ?>
			<?php echo $this->Paginator->next(' > ', array_merge(array('class' => 'next'), $options), null, array('class' => 'next disabled')) ?>
			<?php // ToDo : 我流 アイコンのリンクがHTMLで指定できるように
			//echo $this->Paginator->next('<span class="bca-icon--arrow-right"><span class="bca-icon-label">次へ</span>', array_merge(array('class' => 'next'), $options), null, array('class' => 'next disabled')) ?>
		</div>
	<?php endif ?>
	<div class="page-result bca-page-result">
		<?php echo $this->Paginator->counter(array('format' => '<span class="page-start-num">%start%</span>～<span class="page-end-num">%end%</span> 件 ／ <span class="page-total-num">%count%</span> 件')) ?>
	</div>
</div>
