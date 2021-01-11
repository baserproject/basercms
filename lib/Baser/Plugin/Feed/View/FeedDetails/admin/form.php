<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Feed.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] フィード詳細 フォーム
 * @var BcAppView $this
 */
$this->BcBaser->js('Feed.admin/feed_details/form', false);
?>

<?php if ($this->action == 'admin_add'): ?>
	<?php echo $this->BcForm->create('FeedDetail', ['url' => ['controller' => 'feed_details', 'action' => 'add', $this->BcForm->value('FeedDetail.feed_config_id')]]) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $this->BcForm->create('FeedDetail', ['url' => ['controller' => 'feed_details', 'action' => 'edit', $this->BcForm->value('FeedDetail.feed_config_id'), $this->BcForm->value('FeedDetail.id'), 'id' => false]]) ?>
<?php endif; ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcForm->input('FeedDetail.feed_config_id', ['type' => 'hidden']) ?>

<div class="section">
	<h2><?php echo __d('baser', '基本項目') ?></h2>

	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('FeedDetail.id', 'ID') ?>&nbsp;<span
						class="required">*</span></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('FeedDetail.id') ?>
					<?php echo $this->BcForm->input('FeedDetail.id', ['type' => 'hidden']) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedDetail.name', __d('baser', 'フィード詳細名')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedDetail.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
				<?php echo $this->BcForm->error('FeedDetail.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedDetail.url', __d('baser', 'フィードURL')) ?>
				&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedDetail.url', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
				<?php echo $this->BcForm->error('FeedDetail.url') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<div class="section">
	<h2 class="btn-slide-form"><a href="javascript:void(0)" id="FormOption"><?php echo __d('baser', 'オプション') ?></a></h2>
	<table cellpadding="0" cellspacing="0" class="form-table slide-body" id="FormOptionBody">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedDetail.cache_time', __d('baser', 'キャッシュ時間')) ?></th>
			<td class="col-input">
				<?php
				echo $this->BcForm->input('FeedDetail.cache_time', [
					'type' => 'select',
					'options' => $this->BcForm->getControlSource('cache_time'),
					'empty' => __d('baser', 'なし')])
				?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpCacheTime', 'class' => 'btn help', 'alt' => 'ヘルプ']) ?>
				<?php echo $this->BcForm->error('FeedDetail.cache_time') ?>
				<div id="helptextCacheTime"
					 class="helptext"> <?php echo __d('baser', '負荷を軽減させる為、フィード情報をキャッシュさせる時間を選択してください。') ?></div>
			</td>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('FeedDetail.category_filter', __d('baser', 'カテゴリフィルター')) ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('FeedDetail.category_filter', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpCategoryFilter', 'class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
				<?php echo $this->BcForm->error('FeedDetail.category_filter') ?>
				<div id="helptextCategoryFilter" class="helptext">
					<ul>
						<li><?php echo __d('baser', '特定のカテゴリのみ絞込みたい場合は、カテゴリ名を入力してください。') ?></li>
						<li><?php echo __d('baser', '複数のカテゴリを指定する場合は、カテゴリ名を|（半角縦棒）で区切ります。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm('option') ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $this->BcForm->value('FeedConfig.id'), $this->BcForm->value('FeedDetail.id')], ['class' => 'submit-token button'], sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('FeedConfig.name')), false); ?>
	<?php endif ?>
</div>

<?php echo $this->BcForm->end() ?>
