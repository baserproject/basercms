<h2 class="contents-head"><?php $baser->contentsTitle() ?></h2>

<div class="section">
<?php if(!empty($paginator)): ?>
	<div class="search-result corner5">		
		<?php echo $paginator->counter(array('format' => '<strong>'.implode(' ', $query).'</strong> で検索した結果 <strong>%start%～%end%</strong>件目 / %count% 件')) ?>
	</div>
<?php endif ?>
	<!-- list-num -->
	<?php $baser->element('admin/list_num') ?>
</div>

<?php if($datas): ?>
	<?php foreach($datas as $data): ?>
<div class="section">
	<h3 class="result-head"><?php $baser->link($baser->mark($query, $data['Content']['title']), $data['Content']['url']) ?></h3>
	<p class="result-body"><?php echo $baser->mark($query, $textEx->mbTruncate($data['Content']['detail'],100)) ?></p>
	<p class="result-link"><small><?php $baser->link(fullUrl($data['Content']['url']), $data['Content']['url']) ?></small></p>
</div>
	<?php endforeach ?>
<?php else: ?>
<div class="section">
	<p class="no-data">該当する結果が存在しませんでした。</p>
</div>
<?php endif ?>

<div class="clearfix section">
	<!-- pagination -->
	<?php $baser->pagination('simple', array(), null, false) ?>
</div>