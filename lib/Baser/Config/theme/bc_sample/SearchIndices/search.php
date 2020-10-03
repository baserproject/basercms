<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.View
 * @since            baserCMS v 4.4.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * サイト内検索結果
 * @var BcAppView $this
 * @var array $query 検索キーワード
 * @var array $datas 検索結果リスト
 */
?>


<h2 class="bs-search-title"><?php $this->BcBaser->contentsTitle() ?></h2>

<section class="bs-search-header">
	<?php if (!empty($this->Paginator)): ?>
		<div class="bs-search__result-text">
			<?php echo $this->Paginator->counter(['format' => sprintf(__d('baser', '<strong>%s</strong> で検索した結果 <strong>%%start%%〜%%end%%</strong>件目 / %%count%% 件'), implode(' ', $query))]) ?>
		</div>
	<?php endif ?>
	<!-- list-num -->
	<?php $this->BcBaser->element('list_num') ?>
</section>

<section class="bs-search-result">
<?php if ($datas): ?>
	<?php foreach ($datas as $data): ?>
	<div class="bs-search-result__item">
		<h3 class="bs-search-result__item-head"><?php $this->BcBaser->link($this->BcBaser->mark($query, $data['SearchIndex']['title']), $data['SearchIndex']['url']) ?></h3>
		<p class="bs-search-result__item-body"><?php echo $this->BcBaser->mark($query, $this->Text->truncate($data['SearchIndex']['detail'], 100)) ?></p>
		<p class="bs-search-result__item-link"><small><?php $this->BcBaser->link(fullUrl(urldecode($data['SearchIndex']['url'])), $data['SearchIndex']['url']) ?></small></p>
	</div>
	<?php endforeach ?>
<?php elseif (!isset($this->request->query['q'][0])): ?>
	<p class="bs-search-result__no-data"><?php echo __d('baser', '検索キーワードを入力してください。')?></p>
<?php else: ?>
	<p class="bs-search-result__no-data"><?php echo __d('baser', '該当する結果が存在しませんでした。')?></p>
<?php endif ?>
</section>

<div class="bs-search-pagination">
	<!-- /Elements/paginations/simple.php -->
	<?php $this->BcBaser->pagination('simple', [], ['subDir' => false]) ?>
</div>
