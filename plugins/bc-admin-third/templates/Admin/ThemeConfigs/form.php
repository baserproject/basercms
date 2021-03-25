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

<table class="form-table bca-form-table">
	<tr>
		<th class="bca-form-table__label">テーマカラー</th>
		<td class="bca-form-table__input">
			<div class="bca-form-table__group">
				<small>[<?php echo __d('baser', 'メイン') ?>
					]</small>&nbsp;#&nbsp;<?php echo $this->BcForm->input('color_main', ['type' => 'text', 'size' => 6, 'class' => 'color-picker bca-textbox-color__input', 'div' => 'bca-textbox-color']) ?>
				　
				<small>[<?php echo __d('baser', 'サブ') ?>
					]</small>&nbsp;#&nbsp;<?php echo $this->BcForm->input('color_sub', ['type' => 'text', 'size' => 6, 'class' => 'color-picker bca-textbox-color__input', 'div' => 'bca-textbox-color']) ?>
			</div>
			<div class="bca-form-table__group">
				<small>[<?php echo __d('baser', 'テキストリンク') ?>
					]</small>&nbsp;#&nbsp;<?php echo $this->BcForm->input('color_link', ['type' => 'text', 'size' => 6, 'class' => 'color-picker bca-textbox-color__input', 'div' => 'bca-textbox-color']) ?>
				　
				<small>[<?php echo __d('baser', 'テキストホバー') ?>
					]</small>&nbsp;#&nbsp;<?php echo $this->BcForm->input('color_hover', ['type' => 'text', 'size' => 6, 'class' => 'color-picker bca-textbox-color__input', 'div' => 'bca-textbox-color']) ?>
			</div>
		</td>
	</tr>
	<tr>
		<th class="bca-form-table__label">ロゴ</th>
		<td class="bca-form-table__input">
			<p><?php $this->BcBaser->logo(['thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('logo', ['type' => 'file']) ?><?php if ($this->BcForm->value('logo')): ?><?php echo $this->BcForm->input('logo_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?></p>
			<p><?php echo $this->BcForm->input('logo_alt', ['type' => 'text', 'size' => 50]) ?> <small>[説明文]</small></p>
			<p><?php echo $this->BcForm->input('logo_link', ['type' => 'text', 'size' => 50]) ?>
				<small>[リンク先URL]</small></p>
		</td>
	</tr>
	<tr>
		<th class="bca-form-table__label">メインイメージ１</th>
		<td class="bca-form-table__input">
			<p><?php $this->BcBaser->mainImage(['num' => 1, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('main_image_1', ['type' => 'file']) ?><?php if ($this->BcForm->value('main_image_1')): ?><?php echo $this->BcForm->input('main_image_1_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?></p>
			<p><?php echo $this->BcForm->input('main_image_alt_1', ['type' => 'text', 'size' => 50]) ?>
				<small>[説明文]</small></p>
			<p><?php echo $this->BcForm->input('main_image_link_1', ['type' => 'text', 'size' => 50]) ?> <small>[リンク先URL]</small>
			</p>
		</td>
	</tr>
	<tr>
		<th class="bca-form-table__label">メインイメージ２</th>
		<td class="bca-form-table__input">
			<p><?php $this->BcBaser->mainImage(['num' => 2, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('main_image_2', ['type' => 'file']) ?><?php if ($this->BcForm->value('main_image_2')): ?><?php echo $this->BcForm->input('main_image_2_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?></p>
			<p><?php echo $this->BcForm->input('main_image_alt_2', ['type' => 'text', 'size' => 50]) ?>
				<small>[説明文]</small></p>
			<p><?php echo $this->BcForm->input('main_image_link_2', ['type' => 'text', 'size' => 50]) ?> <small>[リンク先URL]</small>
			</p>
		</td>
	</tr>
	<tr>
		<th class="bca-form-table__label">メインイメージ３</th>
		<td class="bca-form-table__input">
			<p><?php $this->BcBaser->mainImage(['num' => 3, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('main_image_3', ['type' => 'file']) ?><?php if ($this->BcForm->value('main_image_3')): ?><?php echo $this->BcForm->input('main_image_3_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?></p>
			<p><?php echo $this->BcForm->input('main_image_alt_3', ['type' => 'text', 'size' => 50]) ?>
				<small>[説明文]</small></p>
			<p><?php echo $this->BcForm->input('main_image_link_3', ['type' => 'text', 'size' => 50]) ?> <small>[リンク先URL]</small>
			</p>
		</td>
	</tr>
	<tr>
		<th class="bca-form-table__label">メインイメージ４</th>
		<td class="bca-form-table__input">
			<p><?php $this->BcBaser->mainImage(['num' => 4, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('main_image_4', ['type' => 'file']) ?><?php if ($this->BcForm->value('main_image_4')): ?><?php echo $this->BcForm->input('main_image_4_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?></p>
			<p><?php echo $this->BcForm->input('main_image_alt_4', ['type' => 'text', 'size' => 50]) ?>
				<small>[説明文]</small></p>
			<p><?php echo $this->BcForm->input('main_image_link_4', ['type' => 'text', 'size' => 50]) ?> <small>[リンク先URL]</small>
			</p>
		</td>
	</tr>
	<tr>
		<th class="bca-form-table__label">メインイメージ５</th>
		<td class="bca-form-table__input">
			<p><?php $this->BcBaser->mainImage(['num' => 5, 'thumb' => true, 'popup' => true, 'class' => 'photo', 'maxWidth' => 320, 'maxHeight' => 320]) ?></p>
			<p><?php echo $this->BcForm->input('main_image_5', ['type' => 'file']) ?><?php if ($this->BcForm->value('main_image_5')): ?><?php echo $this->BcForm->input('main_image_5_delete', ['type' => 'checkbox', 'label' => __d('baser', '削除する'), 'value' => false]) ?><?php endif ?></p>
			<p><?php echo $this->BcForm->input('main_image_alt_5', ['type' => 'text', 'size' => 50]) ?>
				<small>[説明文]</small></p>
			<p><?php echo $this->BcForm->input('main_image_link_5', ['type' => 'text', 'size' => 50]) ?> <small>[リンク先URL]</small>
			</p>
		</td>
	</tr>
	<?php echo $this->BcForm->dispatchAfterForm() ?>
</table>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit bca-actions">
	<?php echo $this->BcForm->button(__d('baser', '保存'), ['id' => 'BtnSave', 'class' => 'button bca-btn', 'data-bca-btn-type' => 'save', 'data-bca-btn-size' => 'lg', 'data-bca-btn-width' => 'lg',]) ?>
</div>

<?php echo $this->BcForm->end() ?>
