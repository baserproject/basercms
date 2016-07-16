<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Uploader.View
 * @since			baserCMS v 3.0.10
 * @license			http://basercms.net/license/index.html
 */
?>


<script type="text/javascript">
$(window).load(function() {
	$("#UploaderConfigLargeWidth").focus();
});
</script>

<!-- form -->
<?php echo $this->BcForm->create('UploaderConfig', array('action' => 'index')) ?>

<h2>画像サイズ設定</h2>

<div class="section">
	<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
		<tr>
			<th><span class="required">*</span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.large_width', 'PCサイズ（大）') ?>
			</th>
			<td>
				<small>[幅]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.large_width', array('type' => 'text', 'size' => 8,'maxlength' => 8)) ?>&nbsp;px　×　
				<small>[高さ]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.large_height', array('type' => 'text', 'size' => 8,'maxlength' => 8)) ?>&nbsp;px
				<?php echo $this->BcForm->error('UploaderConfig.large_width') ?>
				<?php echo $this->BcForm->error('UploaderConfig.large_height') ?>
			</td>
		</tr>
		<tr>
			<th><span class="required">*</span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.midium_width', 'PCサイズ（中）') ?>
			</th>
			<td>
				<small>[幅]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.midium_width', array('type' => 'text', 'size' => 8,'maxlength' => 8)) ?>&nbsp;px　×　
				<small>[高さ]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.midium_height', array('type' => 'text', 'size' => 8,'maxlength' => 8)) ?>&nbsp;px
				<?php echo $this->BcForm->error('UploaderConfig.midium_width') ?>
				<?php echo $this->BcForm->error('UploaderConfig.midium_height') ?>
			</td>
		</tr>
		<tr>
			<th><span class="required">*</span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.small_width', 'PCサイズ（小）') ?>
			</th>
			<td>
				<small>[幅]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.small_width', array('type' => 'text', 'size' => 8,'maxlength' => 8)) ?>&nbsp;px　×　
				<small>[高さ]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.small_height', array('type' => 'text', 'size' => 8,'maxlength' => 8)) ?>&nbsp;px　
				<?php echo $this->BcForm->input('UploaderConfig.small_thumb', array('type' => 'checkbox', 'label' => '正方形に切り抜く', 'between' => '&nbsp;')) ?>
				<?php echo $this->BcForm->error('UploaderConfig.small_width') ?>
				<?php echo $this->BcForm->error('UploaderConfig.small_height') ?>
				<?php echo $this->BcForm->error('UploaderConfig.small_thumb') ?>
			</td>
		</tr>
		<tr>
			<th><span class="required">*</span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.mobile_large_width', '携帯サイズ（大）') ?>
			</th>
			<td>
				<small>[幅]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.mobile_large_width', array('type' => 'text', 'size' => 8,'maxlength' => 8)) ?>&nbsp;px　×　
				<small>[高さ]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.mobile_large_height', array('type' => 'text', 'size' => 8,'maxlength' => 8)) ?>&nbsp;px
				<?php echo $this->BcForm->error('UploaderConfig.mobile_large_width') ?>
				<?php echo $this->BcForm->error('UploaderConfig.mobile_large_height') ?>
			</td>
		</tr>
		<tr>
			<th><span class="required">*</span>&nbsp;
				<?php echo $this->BcForm->label('UploaderConfig.mobile_small_width', '携帯サイズ（小）') ?>
			</th>
			<td>
				<small>[幅]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.mobile_small_width', array('type' => 'text', 'size' => 8,'maxlength' => 8)) ?>&nbsp;px　×　
				<small>[高さ]</small>&nbsp;<?php echo $this->BcForm->input('UploaderConfig.mobile_small_height', array('type' => 'text', 'size' => 8,'maxlength' => 8)) ?>&nbsp;px　
				<?php echo $this->BcForm->input('UploaderConfig.mobile_small_thumb', array('type' => 'checkbox', 'label' => '正方形に切り抜く', 'between' => '&nbsp;')) ?>
				<?php echo $this->BcForm->error('UploaderConfig.mobile_small_width') ?>
				<?php echo $this->BcForm->error('UploaderConfig.mobile_small_height') ?>
				<?php echo $this->BcForm->error('UploaderConfig.mobile_small_thumb') ?>
			</td>
		</tr>
	</table>
</div>

<?php if($user['user_group_id'] == 1): ?>
<h2 class="btn-slide-form"><a href="javascript:void(0)" id="FormOption">オプション</a></h2>


<div id ="FormOptionBody" class="slide-body section">
	<table cellpadding="0" cellspacing="0" class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('UploaderConfig.layout_type', 'レイアウトタイプ') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('UploaderConfig.layout_type', array('type' => 'radio', 'options' => array('panel' => 'パネル', 'table' => 'テーブル'))) ?>
				<?php echo $this->BcForm->error('UploaderConfig.layout_type') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('UploaderConfig.use_permission', '制限設定') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('UploaderConfig.use_permission', array('type' => 'checkbox', 'label' => '編集/削除を制限する', 'between' => '&nbsp;')) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('UploaderConfig.use_permission') ?>
				<div id="helptextUsePermission" class="helptext">
					管理者以外のユーザーは、自分がアップロードしたファイル以外、編集・削除をできないようにします。
				</div>
			</td>
		</tr>
	</table>
</div>
<?php endif ?>

<!-- button -->
<div class="submit">
	<?php echo $this->BcForm->submit('更　新', array('div' => false, 'class' => 'btn-orange button', 'id' => 'btnSubmit')) ?>
</div>

<?php echo $this->BcForm->end() ?>
