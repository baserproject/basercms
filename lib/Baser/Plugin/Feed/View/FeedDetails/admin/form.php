<?php
/**
 * [ADMIN] フィード詳細 フォーム
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<script type="text/javascript">
$(window).load(function() {
	$("#FeedDetailName").focus();
});
</script>

<?php if ($this->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('FeedDetail', array('url' => array('controller' => 'feed_details', 'action' => 'add', $this->BcForm->value('FeedDetail.feed_config_id')))) ?>
	<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $this->BcForm->create('FeedDetail', array('url' => array('controller' => 'feed_details', 'action' => 'edit', $this->BcForm->value('FeedDetail.feed_config_id'), $this->BcForm->value('FeedDetail.id'), 'id' => false))) ?>
<?php endif; ?>

<?php echo $this->BcForm->input('FeedDetail.feed_config_id', array('type' => 'hidden')) ?>

<div class="section">
	<h2>基本項目</h2>

	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('FeedDetail.id', 'ID') ?>&nbsp;<span class="required">*</span></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('FeedDetail.id') ?>
					<?php echo $this->BcForm->input('FeedDetail.id', array('type' => 'hidden')) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedDetail.name', 'フィード詳細名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedDetail.name', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $this->BcForm->error('FeedDetail.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedDetail.url', 'フィードURL') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedDetail.url', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $this->BcForm->error('FeedDetail.url') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<div class="section">
	<h2 class="btn-slide-form"><a href="javascript:void(0)" id="FormOption">オプション</a></h2>
	<table cellpadding="0" cellspacing="0" class="form-table slide-body" id="FormOptionBody">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedDetail.cache_time', 'キャッシュ時間') ?></th>
			<td class="col-input">
				<?php
				echo $this->BcForm->input('FeedDetail.cache_time', array(
					'type' => 'select',
					'options' => $this->BcForm->getControlSource('cache_time'),
					'empty' => 'なし'))
				?>
<?php echo $this->BcHtml->image('admin/icn_help.png', array('id' => 'helpCacheTime', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('FeedDetail.cache_time') ?>
				<div id="helptextCacheTime" class="helptext"> 負荷を軽減させる為、フィード情報をキャッシュさせる時間を選択してください。</div>
			</td>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedDetail.category_filter', 'カテゴリフィルター') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedDetail.category_filter', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
<?php echo $this->BcHtml->image('admin/icn_help.png', array('id' => 'helpCategoryFilter', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('FeedDetail.category_filter') ?>
				<div id="helptextCategoryFilter" class="helptext">
					<ul>
						<li>特定のカテゴリのみ絞込みたい場合は、カテゴリ名を入力してください。</li>
						<li>複数のカテゴリを指定する場合は、カテゴリ名を|（半角縦棒）で区切ります。</li>
					</ul>
				</div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm('option') ?>
	</table>
</div>

<!-- button -->
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<?php $this->BcBaser->link('削除', array('action' => 'delete', $this->BcForm->value('FeedConfig.id'), $this->BcForm->value('FeedDetail.id')), array('class' => 'submit-token button'), sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('FeedConfig.name')), false); ?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>