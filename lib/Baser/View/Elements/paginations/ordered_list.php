<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.1.3
 * @license			http://basercms.net/license/index.html
 */

/**
 * ページネーション（OrderedList）
 *
 * $this->BcBaser->pagination() で呼び出す
 */
if (empty($this->Paginator)) {
	return;
}
if (!isset($modules)) {
	$modules = 8;
}
?>
<?php if ((int) $this->Paginator->counter(array('format' => '%pages%')) > 1): ?>
<nav class="c-pagination" aria-label="<?php __('ページ送り') ?>">
	<div class="c-pagination__prev">
	<?php if ($this->Paginator->hasPrev()): ?>
		<?php echo $this->Paginator->prev('prev', ['tag' => false, 'rel' => 'prev'], 'prev', ['class' => 'disabled']) ?>
	<?php else: ?>
		<a data-disabled="true">prev</a>
	<?php endif ?>
	</div>
	<div class="c-pagination__next">
	<?php if ($this->Paginator->hasNext()): ?>
		<?php echo $this->Paginator->next('next', ['tag' => false, 'rel' => 'next'], 'next', ['class' => 'disabled']) ?>
	<?php else: ?>
		<a data-disabled="true">next</a>
	<?php endif ?>
	</div>
	<ol class="c-pagination__numbers">
		<?php echo $this->Paginator->numbers(
			['tag' => 'li', 'separator' => '', 'class' => 'c-pagination__number','currentTag' => 'a aria-current="page"']
		);?>
	</ol>
</nav>
<?php endif; ?>
