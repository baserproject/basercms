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


<?php if ((int) $this->Paginator->counter(array('format' => '%pages%')) > 1): ?>
	<div class="pagination">
		<?php echo $this->Paginator->prev('< 前へ', array('class' => 'prev'), null, array('class' => 'disabled')) ?>
		<?php echo $this->Html->tag('span', $this->Paginator->numbers(array('separator' => '', 'class' => 'number', 'modulus' => $modulus), array('class' => 'page-numbers'))) ?>
		<?php echo $this->Paginator->next('次へ >', array('class' => 'next'), null, array('class' => 'disabled')) ?>
	</div>
<?php endif; ?>