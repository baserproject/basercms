<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
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


<?php if ((int) $this->Paginator->counter(array('format' => '%pages%')) > 1): ?>
	<div class="pagination clearfix">
		<?php echo $this->Paginator->prev('< 前へ', array('class' => 'prev'), null, array('class' => 'disabled')) ?>
		<?php echo $this->Html->tag('span', $this->Paginator->numbers(array('separator' => '', 'class' => 'number', 'modulus' => $modules), array('class' => 'page-numbers'))) ?>
		<?php echo $this->Paginator->next('次へ >', array('class' => 'next'), null, array('class' => 'disabled')) ?>
	</div>
<?php endif; ?>
<?php $this->request->params['action'] = 'smartphone_' . $this->request->params['action'] ?>