<?php
/**
 * サイト内検索結果（スマホ用）
 * 呼出箇所：サイト内検索結果ページ
 */
$this->request->params['action'] = str_replace('smartphone_', '', $this->request->params['action']);
?>


<h2><?php $this->BcBaser->contentsTitle() ?></h2>

<?php if (!empty($this->Paginator)): ?>
	<div class="search-result">
		<?php echo $this->Paginator->counter(array('format' => '<strong>' . implode(' ', $query) . '</strong> '.__('で検索した結果').' <strong>%start%〜%end%</strong>'.__('件目').' / %count% '.__('件'))) ?>
	</div>
<?php endif ?>
<?php $this->BcBaser->element('list_num') ?>

<?php if ($datas): ?>
<div id="SearchResultList">
	<?php foreach ($datas as $data): ?>
	<article>
		<h3 class="result-head"><?php $this->BcBaser->link($this->BcBaser->mark($query, $data['Content']['title']), $data['Content']['url']) ?></h3>
		<p class="result-body"><?php echo $this->BcBaser->mark($query, $this->Text->truncate($data['Content']['detail'], 80)) ?></p>
		<p class="result-link"><small><?php $this->BcBaser->link(fullUrl($data['Content']['url']), $data['Content']['url']) ?></small></p>
	</article>
	<?php endforeach ?>
</div>
<?php else: ?>
	<div>
		<p class="no-data"><?php echo __('該当する結果が存在しませんでした。'); ?></p>
	</div>
<?php endif ?>

<div class="clearfix">
	<!-- /Elements/paginations/simple.php -->
	<?php $this->BcBaser->pagination('simple', array(), array('subDir' => false)) ?>
</div>

<?php
$this->request->params['action'] = 'smartphone_' . $this->request->params['action'];