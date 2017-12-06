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


<?php if ((int) $this->Paginator->counter(['format' => '%pages%']) > 1): ?>
	<div class="pagination clearfix">
		<?php echo $this->Paginator->prev('< 前へ', ['class' => 'prev'], null, ['class' => 'disabled']) ?>
		<?php echo $this->Html->tag('span', $this->Paginator->numbers(['separator' => '', 'class' => 'number', 'modulus' => $modules], ['class' => 'page-numbers'])) ?>
		<?php echo $this->Paginator->next('次へ >', ['class' => 'next'], null, ['class' => 'disabled']) ?>
	</div>
<?php endif; ?>
<?php $this->request->params['action'] = 'smartphone_' . $this->request->params['action'] ?>