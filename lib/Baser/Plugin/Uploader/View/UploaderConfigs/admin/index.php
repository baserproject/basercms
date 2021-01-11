<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.View
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */
?>
<!-- form -->
<?php echo $this->BcForm->create('UploaderConfig', ['url' => ['action' => 'index']]) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<h2><?php echo __d('baser', '画像サイズ設定') ?></h2>

<div class="section">
	<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
		<tr>
			<th><span class="required">*</span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.large_width', __d('baser', 'PCサイズ（大）')) ?>
			</th>
			<td>
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
			<th><span class="required">*</span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.midium_width', __d('baser', 'PCサイズ（中）')) ?>
			</th>
			<td>
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
			<th><span class="required">*</span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.small_width', __d('baser', 'PCサイズ（小）')) ?>
			</th>
			<td>
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
			<th><span class="required">*</span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.mobile_large_width', __d('baser', '携帯サイズ（大）')) ?>
			</th>
			<td>
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
			<th><span class="required">*</span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.mobile_small_width', __d('baser', '携帯サイズ（小）')) ?>
			</th>
			<td>
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
	<h2 class="btn-slide-form"><a href="javascript:void(0)" id="FormOption"><?php echo __d('baser', 'オプション') ?></a></h2>


	<div id="FormOptionBody" class="slide-body section">
		<table cellpadding="0" cellspacing="0" class="form-table">
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('UploaderConfig.layout_type', __d('baser', 'レイアウトタイプ')) ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->input('UploaderConfig.layout_type', ['type' => 'radio', 'options' => ['panel' => __d('baser', 'パネル'), 'table' => __d('baser', 'テーブル')]]) ?>
					<?php echo $this->BcForm->error('UploaderConfig.layout_type') ?>
				</td>
			</tr>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('UploaderConfig.use_permission', __d('baser', '制限設定')) ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->input('UploaderConfig.use_permission', ['type' => 'checkbox', 'label' => __d('baser', '編集/削除を制限する'), 'between' => '&nbsp;']) ?>
					<?php echo $this->Html->image('admin/icn_help.png', ['class' => 'btn help', 'alt' => __d('baser', 'ヘルプ')]) ?>
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
<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '更　新'), ['div' => false, 'class' => 'btn-orange button', 'id' => 'btnSubmit']) ?>
</div>

<?php echo $this->BcForm->end() ?>
