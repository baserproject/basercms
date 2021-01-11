<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Feed.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] フィード詳細 フォーム
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

<section class="bca-section" data-bca-section-type='form-group'>

	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table bca-form-table">
		<?php if ($this->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('FeedDetail.id', 'ID') ?>
					&nbsp;<span class="required bca-label"
								data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->value('FeedDetail.id') ?>
					<?php echo $this->BcForm->input('FeedDetail.id', ['type' => 'hidden']) ?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('FeedDetail.name', __d('baser', 'フィード詳細名')) ?>
				&nbsp;<span class="required bca-label"
							data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('FeedDetail.name', ['type' => 'text', 'size' => 40, 'maxlength' => 255, 'autofocus' => true]) ?>
				<?php echo $this->BcForm->error('FeedDetail.name') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('FeedDetail.url', __d('baser', 'フィードURL')) ?>
				&nbsp;<span class="required bca-label"
							data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('FeedDetail.url', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
				<?php echo $this->BcForm->error('FeedDetail.url') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</section>

<section class="bca-section" data-bca-section-type='form-group'>
	<div class="bca-collapse__action">
		<button type="button" class="bca-collapse__btn" data-bca-collapse="collapse" data-bca-target="#formOptionBody"
				aria-expanded="false" aria-controls="formOptionBody"><?php echo __d('baser', '詳細設定') ?>&nbsp;&nbsp;<i
				class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
	</div>
	<div class="bca-collapse" id="formOptionBody" data-bca-state="">
		<table cellpadding="0" cellspacing="0" class="form-table bca-form-table">
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('FeedDetail.cache_time', __d('baser', 'キャッシュ時間')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php
					echo $this->BcForm->input('FeedDetail.cache_time', [
						'type' => 'select',
						'options' => $this->BcForm->getControlSource('cache_time'),
						'empty' => __d('baser', 'なし')])
					?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('FeedDetail.cache_time') ?>
					<div id="helptextCacheTime"
						 class="helptext"><?php echo __d('baser', '負荷を軽減させる為、フィード情報をキャッシュさせる時間を選択してください。') ?></div>
				</td>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('FeedDetail.category_filter', __d('baser', 'カテゴリフィルター')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('FeedDetail.category_filter', ['type' => 'text', 'size' => 40, 'maxlength' => 255]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
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
</section>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="bca-actions">
	<?php if ($this->action == 'admin_edit' || $this->action == 'admin_add'): ?>
		<div class="bca-actions__main">
			<?php echo $this->BcForm->button(__d('baser', '保存'),
				[
					'type' => 'submit',
					'id' => 'BtnSave',
					'div' => false,
					'class' => 'button bca-btn bca-actions__item',
					'data-bca-btn-type' => 'save',
					'data-bca-btn-size' => 'lg',
					'data-bca-btn-width' => 'lg',
				]) ?>
		</div>
	<?php endif ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<div class="bca-actions__sub">
			<?php $this->BcBaser->link(__d('baser', '削除'), ['action' => 'delete', $this->BcForm->value('FeedConfig.id'), $this->BcForm->value('FeedDetail.id')],
				[
					'class' => 'submit-token button bca-btn bca-actions__item',
					'data-bca-btn-type' => 'delete',
					'data-bca-btn-size' => 'sm',
					'data-bca-btn-color' => 'danger'
				],
				sprintf(__d('baser', '%s を本当に削除してもいいですか？'), $this->BcForm->value('FeedDetail.name')
				),
				false
			);
			?>
		</div>
	<?php endif ?>
</div>


<?php echo $this->BcForm->end() ?>
