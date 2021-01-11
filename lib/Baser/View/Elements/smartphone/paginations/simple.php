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
 */
if (empty($this->Paginator)) {
	return;
}
if (!isset($modules)) {
	$modules = 8;
}
$this->request->params['action'] = str_replace('smartphone_', '', $this->request->params['action']);
?>


<?php if ((int)$this->Paginator->counter(['format' => '%pages%']) > 1): ?>
	<div class="pagination clearfix">
		<?php echo $this->Paginator->prev(__d('baser', '< 前へ'), ['class' => 'prev'], null, ['class' => 'disabled']) ?>
		<?php echo $this->Html->tag('span', $this->Paginator->numbers(['separator' => '', 'class' => 'number', 'modulus' => $modules], ['class' => 'page-numbers'])) ?>
		<?php echo $this->Paginator->next(__d('baser', '次へ >'), ['class' => 'next'], null, ['class' => 'disabled']) ?>
	</div>
<?php endif; ?>
<?php $this->request->params['action'] = 'smartphone_' . $this->request->params['action'] ?>
