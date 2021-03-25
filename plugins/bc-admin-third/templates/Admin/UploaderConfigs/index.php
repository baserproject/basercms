<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Uploader.View
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */
?>
<!-- form -->
<?php echo $this->BcForm->create('UploaderConfig', ['url' => ['action' => 'index']]) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<h2><?php echo __d('baser', '画像サイズ設定') ?></h2>

<div class="section bca-section">
	<table cellpadding="0" cellspacing="0" class="list-table bca-form-table" id="ListTable">
		<tr>
			<th class="bca-form-table__label"><span class="bca-label"
													data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.large_width', __d('baser', 'PCサイズ（大）')) ?>
			</th>
			<td class="bca-form-table__input">
				<small>[<?php echo __d('baser', '幅') ?>
					]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.large_width', ['type' => 'text', 'size' => 8, 'maxlength' => 8, 'autofocus' => true]) ?>
				&nbsp;px　×　
				<small>[<?php echo __d('baser', '高さ') ?>
					]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.large_height', ['type' => 'text', 'size' => 8, 'maxlength' => 8]) ?>
				&nbsp;px
				<?php echo $this->BcForm->error('UploaderConfig.large_width') ?>
				<?php echo $this->BcForm->error('UploaderConfig.large_height') ?>
			</td>
		</tr>
		<tr>
			<th class="bca-form-table__label"><span class="bca-label"
													data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.midium_width', __d('baser', 'PCサイズ（中）')) ?>
			</th>
			<td class="bca-form-table__input">
				<small>[<?php echo __d('baser', '幅') ?>
					]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.midium_width', ['type' => 'text', 'size' => 8, 'maxlength' => 8]) ?>
				&nbsp;px　×　
				<small>[<?php echo __d('baser', '高さ') ?>
					]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.midium_height', ['type' => 'text', 'size' => 8, 'maxlength' => 8]) ?>
				&nbsp;px
				<?php echo $this->BcForm->error('UploaderConfig.midium_width') ?>
				<?php echo $this->BcForm->error('UploaderConfig.midium_height') ?>
			</td>
		</tr>
		<tr>
			<th class="bca-form-table__label"><span class="bca-label"
													data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.small_width', __d('baser', 'PCサイズ（小）')) ?>
			</th>
			<td class="bca-form-table__input">
				<small>[<?php echo __d('baser', '幅') ?>
					]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.small_width', ['type' => 'text', 'size' => 8, 'maxlength' => 8]) ?>
				&nbsp;px　×　
				<small>[<?php echo __d('baser', '高さ') ?>
					]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.small_height', ['type' => 'text', 'size' => 8, 'maxlength' => 8]) ?>
				&nbsp;px　
				<?php echo $this->BcForm->input('UploaderConfig.small_thumb', ['type' => 'checkbox', 'label' => __d('baser', '正方形に切り抜く'), 'between' => '&nbsp;']) ?>
				<?php echo $this->BcForm->error('UploaderConfig.small_width') ?>
				<?php echo $this->BcForm->error('UploaderConfig.small_height') ?>
				<?php echo $this->BcForm->error('UploaderConfig.small_thumb') ?>
			</td>
		</tr>
		<tr>
			<th class="bca-form-table__label"><span class="bca-label"
													data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.mobile_large_width', __d('baser', '携帯サイズ（大）')) ?>
			</th>
			<td class="bca-form-table__input">
				<small>[<?php echo __d('baser', '幅') ?>
					]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.mobile_large_width', ['type' => 'text', 'size' => 8, 'maxlength' => 8]) ?>
				&nbsp;px　×　
				<small>[<?php echo __d('baser', '高さ') ?>
					]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.mobile_large_height', ['type' => 'text', 'size' => 8, 'maxlength' => 8]) ?>
				&nbsp;px
				<?php echo $this->BcForm->error('UploaderConfig.mobile_large_width') ?>
				<?php echo $this->BcForm->error('UploaderConfig.mobile_large_height') ?>
			</td>
		</tr>
		<tr>
			<th class="bca-form-table__label"><span class="bca-label"
													data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.mobile_small_width', __d('baser', '携帯サイズ（小）')) ?>
			</th>
			<td class="bca-form-table__input">
				<small>[<?php echo __d('baser', '幅') ?>
					]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.mobile_small_width', ['type' => 'text', 'size' => 8, 'maxlength' => 8]) ?>
				&nbsp;px　×　
				<small>[<?php echo __d('baser', '高さ') ?>
					]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.mobile_small_height', ['type' => 'text', 'size' => 8, 'maxlength' => 8]) ?>
				&nbsp;px　
				<?php echo $this->BcForm->input('UploaderConfig.mobile_small_thumb', ['type' => 'checkbox', 'label' => __d('baser', '正方形に切り抜く'), 'between' => '&nbsp;']) ?>
				<?php echo $this->BcForm->error('UploaderConfig.mobile_small_width') ?>
				<?php echo $this->BcForm->error('UploaderConfig.mobile_small_height') ?>
				<?php echo $this->BcForm->error('UploaderConfig.mobile_small_thumb') ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php if ($user['user_group_id'] == 1): ?>

	<div id="FormOptionBody" class="section">
		<h3><?php echo __d('baser', '詳細設定') ?></h3>
		<table cellpadding="0" cellspacing="0" class="form-table bca-form-table">
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('UploaderConfig.layout_type', __d('baser', 'レイアウトタイプ')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('UploaderConfig.layout_type', ['type' => 'radio', 'options' => ['panel' => __d('baser', 'パネル'), 'table' => __d('baser', 'テーブル')]]) ?>
					<?php echo $this->BcForm->error('UploaderConfig.layout_type') ?>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('UploaderConfig.use_permission', __d('baser', '制限設定')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('UploaderConfig.use_permission', ['type' => 'checkbox', 'label' => __d('baser', '編集/削除を制限する'), 'between' => '&nbsp;']) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('UploaderConfig.use_permission') ?>
					<div id="helptextUsePermission" class="helptext">
						<?php echo __d('baser', '管理者以外のユーザーは、自分がアップロードしたファイル以外、編集・削除をできないようにします。') ?>
					</div>
				</td>
			</tr>
			<?php echo $this->BcForm->dispatchAfterForm('option') ?>
		</table>
	</div>
<?php endif ?>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="submit bca-actions">
	<div class="bca-actions__main">
		<?php echo $this->BcForm->submit(__d('baser', '更新'), ['div' => false, 'class' => 'btn-orange button bca-btn bca-actions__item', 'data-bca-btn-type' => 'add', 'data-bca-btn-width' => 'lg', 'data-bca-btn-size' => 'lg', 'id' => 'btnSubmit']) ?>
	</div>
</div>

<?php echo $this->BcForm->end() ?>
