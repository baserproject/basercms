<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
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
	$options = [];
}
$pageCount = 0;
if (isset($this->Paginator->params['paging'][$this->Paginator->defaultModel()]['pageCount'])) {
	$pageCount = $this->Paginator->params['paging'][$this->Paginator->defaultModel()]['pageCount'];
}
?>


<div class="pagination clearfix">

	<?php if ($pageCount > 1): ?>
		<div class="page-numbers">
			<?php echo $this->Paginator->prev(__d('baser', '< 前へ'), array_merge(['class' => 'prev'], $options), null, ['class' => 'prev disabled']) ?>
			<?php echo $this->Html->tag('span', $this->Paginator->numbers(array_merge(['separator' => '', 'class' => 'number', 'modulus' => $modules], $options), ['class' => 'page-numbers'])) ?>
			<?php echo $this->Paginator->next(__d('baser', '次へ >'), array_merge(['class' => 'next'], $options), null, ['class' => 'next disabled']) ?>
		</div>
	<?php endif ?>
	<div class="page-result">
		<?php echo $this->Paginator->counter(['format' => sprintf(__d('baser', '%s～%s 件'), '<span class="page-start-num">%start%</span>', '<span class="page-end-num">%end%</span>') . ' ／ ' . sprintf(__d('baser', '%s 件'), '<span class="page-total-num">%count%</span>')]) ?>
	</div>
</div>
