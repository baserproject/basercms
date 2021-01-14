<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * @var array $folderTemplateList フォルダテンプレートリスト
 * @var array $pageTemplateList ページテンプレートリスト
 */
?>


<?php echo $this->BcForm->create() ?>
<?php echo $this->BcFormTable->dispatchBefore() ?>
<?php echo $this->BcForm->hidden('ContentFolder.id') ?>

<table class="form-table">
	<tr>
		<th><?php echo $this->BcForm->label('ContentFolder.folder_template', __d('baser', 'フォルダーテンプレート')) ?></th>
		<td>
			<?php echo $this->BcForm->input('ContentFolder.folder_template', ['type' => 'select', 'options' => $folderTemplateList]) ?>
		</td>
	</tr>
	<tr>
		<th><?php echo $this->BcForm->label('ContentFolder.page_template', __d('baser', '固定ページテンプレート')) ?></th>
		<td>
			<?php echo $this->BcForm->input('ContentFolder.page_template', ['type' => 'select', 'options' => $pageTemplateList]) ?>
		</td>
	</tr>
	<?php echo $this->BcForm->dispatchAfterForm() ?>
</table>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit">
	<?php echo $this->BcForm->submit(__d('baser', '保存'), ['class' => 'button', 'div' => false]) ?>
</div>
<?php echo $this->BcForm->end() ?>
