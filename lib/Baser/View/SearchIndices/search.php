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
 * サイト内検索結果
 */
?>

<h2 class="contents-head"><?php $this->BcBaser->contentsTitle() ?></h2>

<div class="section">
	<?php if (!empty($this->Paginator)): ?>
		<div class="search-result corner5">
			<?php echo $this->Paginator->counter(['format' => sprintf(__d('baser', '<strong>%s</strong> で検索した結果 <strong>%%start%%〜%%end%%</strong>件目 / %%count%% 件'), implode(' ', $query))]) ?>
		</div>
	<?php endif ?>
	<!-- list-num -->
	<?php $this->BcBaser->element('list_num') ?>
</div>

<?php if ($datas): ?>
	<?php foreach($datas as $data): ?>
		<div class="section">
			<h3 class="result-head"><?php $this->BcBaser->link($this->BcBaser->mark($query, $data['SearchIndex']['title']), $data['SearchIndex']['url']) ?></h3>
			<p class="result-body"><?php echo $this->BcBaser->mark($query, $this->Text->truncate($data['SearchIndex']['detail'], 100)) ?></p>
			<p class="result-link">
				<small><?php $this->BcBaser->link(fullUrl(urldecode($data['SearchIndex']['url'])), $data['SearchIndex']['url']) ?></small>
			</p>
		</div>
	<?php endforeach ?>
<?php elseif (!isset($this->request->query['q'][0])): ?>
	<div class="section">
		<p class="no-data"><?php echo __d('baser', '検索キーワードを入力してください。') ?></p>
	</div>
<?php else: ?>
	<div class="section">
		<p class="no-data"><?php echo __d('baser', '該当する結果が存在しませんでした。') ?></p>
	</div>
<?php endif ?>

<div class="clearfix section">
	<!-- pagination -->
	<?php $this->BcBaser->pagination('simple', [], ['subDir' => false]) ?>
</div>
