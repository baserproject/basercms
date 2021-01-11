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
 * [ADMIN] スキーマ読み込み フォーム
 */
?>


<!-- form -->
<?php echo $this->BcForm->create('Tool', ['url' => ['action' => 'load_schema'], 'type' => 'file']) ?>

<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th class="col-head"><span
				class="required">*</span>&nbsp;<?php echo $this->BcForm->label('Tool.schema_type', __d('baser', 'スキーマタイプ')) ?>
		</th>
		<td class="col-input">
			<?php echo $this->BcForm->input('Tool.schema_type', [
				'type' => 'radio',
				'options' => ['create' => __d('baser', 'テーブル作成'), 'alter' => __d('baser', 'テーブル構造変更'), 'drop' => __d('baser', 'テーブル削除')],
				'legend' => false,
				'separator' => '　']); ?>
			<?php echo $this->BcForm->error('Tool.schema_type') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span
				class="required">*</span>&nbsp;<?php echo $this->BcForm->label('Tool.schema_file', __d('baser', 'スキーマファイル')) ?>
		</th>
		<td class="col-input">
			<?php echo $this->BcForm->input('Tool.schema_file', ['type' => 'file']) ?>
			<?php echo $this->BcForm->error('Tool.schema_file') ?>
		</td>
	</tr>
</table>

<div
	class="submit"><?php echo $this->BcForm->submit(__d('baser', '読み込み'), ['div' => false, 'class' => 'btn-red button']) ?></div>

<?php echo $this->BcForm->end(); ?>
