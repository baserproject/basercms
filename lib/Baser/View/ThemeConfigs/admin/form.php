<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] テーマ設定編集
 */
$this->BcBaser->css('admin/colpick', array('inline' => false));
$this->BcBaser->js(['admin/vendors/colpick', 'admin/theme_configs/form'], false);
?>


<script type="text/javascript">
$(function(){
	$(".color-picker").each(function() {
		var color;
		if($(this).val()) {
			$(this).css('border-right','36px solid #'+$(this).val());
			color = $(this).val();
		} else {
			color = 'ffffff';
		}
		$(this).colpick({
			layout:'hex',
			color:color,
			onSubmit:function(hsb,hex,rgb,el) {
				$(el).val(hex).css('border-right','36px solid #'+hex);
				$(el).colpickHide();
			}
		});
	});
});
</script>


<?php echo $this->BcForm->create('ThemeConfig', array('type' => 'file')) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<table class="form-table">
	<tr>
		<th>テーマカラー</th>
		<td>
			<small>[メイン]</small> #<?php echo $this->BcForm->input('color_main', array('type' => 'text', 'size' => 6, 'class' => 'color-picker')) ?>　
			<small>[サブ]</small> #<?php echo $this->BcForm->input('color_sub', array('type' => 'text', 'size' => 6, 'class' => 'color-picker')) ?><br />
			<small>[テキストリンク]</small> #<?php echo $this->BcForm->input('color_link', array('type' => 'text', 'size' => 6, 'class' => 'color-picker')) ?>　
			<small>[テキストホバー]</small> #<?php echo $this->BcForm->input('color_hover', array('type' => 'text', 'size' => 6, 'class' => 'color-picker')) ?>
		</td>
	</tr>
	<tr>
		<th>ロゴ</th>
		<td>
			<p><?php $this->BcBaser->logo(array('thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320)) ?></p>
			<p><?php echo $this->BcForm->file('logo') ?><?php if ($this->BcForm->value('logo')): ?><?php echo $this->BcForm->input('logo_delete', array('type' => 'checkbox', 'label' => '削除する', 'value' => false)) ?><?php endif ?></p>
			<?php echo $this->BcForm->input('logo_alt', array('type' => 'text', 'size' => 50)) ?> <small>[説明文]</small><br />
			<?php echo $this->BcForm->input('logo_link', array('type' => 'text', 'size' => 50)) ?> <small>[リンク先URL]</small>
		</td>
	</tr>
	<tr>
		<th>メインイメージ１</th>
		<td>
			<?php $this->BcBaser->mainImage(array('num' => 1, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320)) ?></p>
			<?php echo $this->BcForm->file('main_image_1') ?><?php if ($this->BcForm->value('main_image_1')): ?><?php echo $this->BcForm->input('main_image_1_delete', array('type' => 'checkbox', 'label' => '削除する', 'value' => false)) ?><?php endif ?></p>
			<?php echo $this->BcForm->input('main_image_alt_1', array('type' => 'text', 'size' => 50)) ?> <small>[説明文]</small><br />
			<?php echo $this->BcForm->input('main_image_link_1', array('type' => 'text', 'size' => 50)) ?> <small>[リンク先URL]</small>
		</td>
	</tr>
	<tr>
		<th>メインイメージ２</th>
		<td>
			<p><?php $this->BcBaser->mainImage(array('num' => 2, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320)) ?></p>
			<p><?php echo $this->BcForm->file('main_image_2') ?><?php if ($this->BcForm->value('main_image_2')): ?><?php echo $this->BcForm->input('main_image_2_delete', array('type' => 'checkbox', 'label' => '削除する', 'value' => false)) ?><?php endif ?></p>
			<?php echo $this->BcForm->input('main_image_alt_2', array('type' => 'text', 'size' => 50)) ?> <small>[説明文]</small><br />
			<?php echo $this->BcForm->input('main_image_link_2', array('type' => 'text', 'size' => 50)) ?> <small>[リンク先URL]</small>
		</td>
	</tr>
	<tr>
		<th>メインイメージ３</th>
		<td>
			<p><?php $this->BcBaser->mainImage(array('num' => 3, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320)) ?></p>
			<p><?php echo $this->BcForm->file('main_image_3') ?><?php if ($this->BcForm->value('main_image_3')): ?><?php echo $this->BcForm->input('main_image_3_delete', array('type' => 'checkbox', 'label' => '削除する', 'value' => false)) ?><?php endif ?></p>
			<?php echo $this->BcForm->input('main_image_alt_3', array('type' => 'text', 'size' => 50)) ?> <small>[説明文]</small><br />
			<?php echo $this->BcForm->input('main_image_link_3', array('type' => 'text', 'size' => 50)) ?> <small>[リンク先URL]</small>
		</td>
	</tr>
	<tr>
		<th>メインイメージ４</th>
		<td>
			<p><?php $this->BcBaser->mainImage(array('num' => 4, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320)) ?></p>
			<p><?php echo $this->BcForm->file('main_image_4') ?><?php if ($this->BcForm->value('main_image_4')): ?><?php echo $this->BcForm->input('main_image_4_delete', array('type' => 'checkbox', 'label' => '削除する', 'value' => false)) ?><?php endif ?></p>
			<?php echo $this->BcForm->input('main_image_alt_4', array('type' => 'text', 'size' => 50)) ?> <small>[説明文]</small><br />
			<?php echo $this->BcForm->input('main_image_link_4', array('type' => 'text', 'size' => 50)) ?> <small>[リンク先URL]</small>
		</td>
	</tr>
	<tr>
		<th>メインイメージ５</th>
		<td>
			<p><?php $this->BcBaser->mainImage(array('num' => 5, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320)) ?></p>
			<p><?php echo $this->BcForm->file('main_image_5') ?><?php if ($this->BcForm->value('main_image_5')): ?><?php echo $this->BcForm->input('main_image_5_delete', array('type' => 'checkbox', 'label' => '削除する', 'value' => false)) ?><?php endif ?></p>
			<?php echo $this->BcForm->input('main_image_alt_5', array('type' => 'text', 'size' => 50)) ?> <small>[説明文]</small><br />
			<?php echo $this->BcForm->input('main_image_link_5', array('type' => 'text', 'size' => 50)) ?> <small>[リンク先URL]</small>
		</td>
	</tr>
	<?php echo $this->BcForm->dispatchAfterForm() ?>
</table>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('class' => 'button', 'id' => 'BtnSave')) ?>
</div>

<?php echo $this->BcForm->end() ?>
