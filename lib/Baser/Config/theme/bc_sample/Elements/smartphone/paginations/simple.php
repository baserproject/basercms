<?php
/**
 * ページネーション（スマホ用）
 * 呼出箇所：サイト内検索結果一覧、ブログトップ、カテゴリ別ブログ記事一覧、タグ別ブログ記事一覧、年別ブログ記事一覧、月別ブログ記事一覧、日別ブログ記事一覧
 *
 * BcBaserHelper::pagination() で呼び出す
 * （例）<?php $this->BcBaser->pagination() ?>
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
		<?php echo $this->Paginator->prev('< '. __('前へ'), array('class' => 'prev'), null, array('class' => 'disabled')) ?>
		<?php echo $this->Html->tag('span', $this->Paginator->numbers(array('separator' => '', 'class' => 'number', 'modulus' => $modules), array('class' => 'page-numbers'))) ?>
		<?php echo $this->Paginator->next(__('次へ'). ' >', array('class' => 'next'), null, array('class' => 'disabled')) ?>
	</div>
<?php endif; ?>
<?php $this->request->params['action'] = 'smartphone_' . $this->request->params['action'] ?>