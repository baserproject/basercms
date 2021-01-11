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
 * [ADMIN] テーマ設定編集
 */
$this->BcBaser->css('admin/colpick', ['inline' => false]);
$this->BcBaser->js(['admin/vendors/colpick', 'admin/theme_configs/form'], false);
?>


<script type="text/javascript">
	$(function () {
		$(".color-picker").each(function () {
			var color;
			if ($(this).val()) {
				$(this).css('border-right', '36px solid #' + $(this).val());
				color = $(this).val();
			} else {
				color = 'ffffff';
			}
			$(this).colpick({
				layout: 'hex',
				color: color,
				onSubmit: function (hsb, hex, rgb, el) {
					$(el).val(hex).css('border-right', '36px solid #' + hex);
					$(el).colpickHide();
				}
			});
		});
	});
</script>


<?php echo $this->BcForm->create('ThemeConfig', ['type' => 'file']) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<table class="form-table">
	<tr>
		<th><?php echo __d('baser', 'テーマカラー') ?></th>
		<td>
			<small>[<?php echo __d('baser', 'メイン') ?>]</small>
			#<?php echo $this->BcForm->input('color_main', ['type' => 'text', 'size' => 6, 'class' => 'color-picker']) ?>
			　
			<small>[<?php echo __d('baser', 'サブ') ?>]</small>
			#<?php echo $this->BcForm->input('color_sub', ['type' => 'text', 'size' => 6, 'class' => 'color-picker']) ?>
			<br/>
			<small>[<?php echo __d('baser', 'テキストリンク') ?>]</small>
			#<?php echo $this->BcForm->input('color_link', ['type' => 'text', 'size' => 6, 'class' => 'color-picker']) ?>
			　
			<small>[<?php echo __d('baser', 'テキストホバー') ?>]</small>
			#<?php echo $this->BcForm->input('color_hover', ['type' => 'text', 'size' => 6, 'class' => 'color-picker']) ?>
		</td>
	</tr>
	<tr>
		<th><?php echo __d('baser', 'ロゴ') ?></th>
		<td>
			<p><?php $this->BcBaser->logo(['thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('logo', ['type' => 'file']) ?><?php if ($this->BcForm->value('logo')): ?><?php echo $this->BcForm->input('logo_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?><?php echo $this->BcForm->error('logo') ?></p>
			<?php echo $this->BcForm->input('logo_alt', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', '説明文') ?>]</small><br/>
			<?php echo $this->BcForm->input('logo_link', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
		</td>
	</tr>
	<tr>
		<th><?php echo __d('baser', 'メインイメージ１') ?></th>
		<td>
			<p><?php $this->BcBaser->mainImage(['num' => 1, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('main_image_1', ['type' => 'file']) ?><?php if ($this->BcForm->value('main_image_1')): ?><?php echo $this->BcForm->input('main_image_1_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?><?php echo $this->BcForm->error('main_image_1') ?></p>
			<?php echo $this->BcForm->input('main_image_alt_1', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', '説明文') ?>]</small><br/>
			<?php echo $this->BcForm->input('main_image_link_1', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
		</td>
	</tr>
	<tr>
		<th><?php echo __d('baser', 'メインイメージ２') ?></th>
		<td>
			<p><?php $this->BcBaser->mainImage(['num' => 2, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('main_image_2', ['type' => 'file']) ?><?php if ($this->BcForm->value('main_image_2')): ?><?php echo $this->BcForm->input('main_image_2_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?><?php echo $this->BcForm->error('main_image_2') ?></p>
			<?php echo $this->BcForm->input('main_image_alt_2', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', '説明文') ?>]</small><br/>
			<?php echo $this->BcForm->input('main_image_link_2', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
		</td>
	</tr>
	<tr>
		<th><?php echo __d('baser', 'メインイメージ３') ?></th>
		<td>
			<p><?php $this->BcBaser->mainImage(['num' => 3, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('main_image_3', ['type' => 'file']) ?><?php if ($this->BcForm->value('main_image_3')): ?><?php echo $this->BcForm->input('main_image_3_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?><?php echo $this->BcForm->error('main_image_3') ?></p>
			<?php echo $this->BcForm->input('main_image_alt_3', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', '説明文') ?>]</small><br/>
			<?php echo $this->BcForm->input('main_image_link_3', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
		</td>
	</tr>
	<tr>
		<th><?php echo __d('baser', 'メインイメージ４') ?></th>
		<td>
			<p><?php $this->BcBaser->mainImage(['num' => 4, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('main_image_4', ['type' => 'file']) ?><?php if ($this->BcForm->value('main_image_4')): ?><?php echo $this->BcForm->input('main_image_4_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?><?php echo $this->BcForm->error('main_image_4') ?></p>
			<?php echo $this->BcForm->input('main_image_alt_4', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', '説明文') ?>]</small><br/>
			<?php echo $this->BcForm->input('main_image_link_4', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
		</td>
	</tr>
	<tr>
		<th><?php echo __d('baser', 'メインイメージ５') ?></th>
		<td>
			<p><?php $this->BcBaser->mainImage(['num' => 5, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('main_image_5', ['type' => 'file']) ?><?php if ($this->BcForm->value('main_image_5')): ?><?php echo $this->BcForm->input('main_image_5_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?><?php echo $this->BcForm->error('main_image_5') ?></p>
			<?php echo $this->BcForm->input('main_image_alt_5', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', '説明文') ?>]</small><br/>
			<?php echo $this->BcForm->input('main_image_link_5', ['type' => 'text', 'size' => 50]) ?>
			<small>[<?php echo __d('baser', 'リンク先URL') ?>]</small>
		</td>
	</tr>
	<?php echo $this->BcForm->dispatchAfterForm() ?>
</table>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['class' => 'button', 'id' => 'BtnSave']) ?>
</div>

<?php echo $this->BcForm->end() ?>
