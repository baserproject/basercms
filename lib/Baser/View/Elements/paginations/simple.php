<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [PUBLISH] ページネーションシンプル
 *
 * $this->BcBaser->pagination() で呼び出す
 */

if (empty($this->Paginator)) {
	return;
}
if (!isset($modulus)) {
	$modulus = 8;
}
?>


<?php if ((int)$this->Paginator->counter(['format' => '%pages%']) > 1): ?>
	<div class="pagination">
		<?php echo $this->Paginator->prev(__d('baser', '< 前へ'), ['class' => 'prev'], null, ['class' => 'disabled']) ?>
		<?php echo $this->Html->tag('span', $this->Paginator->numbers(['separator' => '', 'class' => 'number', 'modulus' => $modulus], ['class' => 'page-numbers'])) ?>
		<?php echo $this->Paginator->next(__d('baser', '次へ >'), ['class' => 'next'], null, ['class' => 'disabled']) ?>
	</div>
<?php endif; ?>
