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
 * [ADMIN] スキーマ生成 フォーム
 */
?>


<?php echo $this->BcForm->create('Tool', ['url' => ['action' => 'write_schema']]) ?>

<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th class="col-head"><span
				class="required">*</span>&nbsp;<?php echo $this->BcForm->label('Tool.baser', __d('baser', 'コアテーブル名')) ?>
		</th>
		<td class="col-input">
			<?php echo $this->BcForm->input('Tool.core', [
				'type' => 'select',
				'options' => $this->BcForm->getControlSource('Tool.core'),
				'multiple' => true,
				'style' => 'width:400px;height:250px']); ?>
			<?php echo $this->BcForm->error('Tool.core') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span
				class="required">*</span>&nbsp;<?php echo $this->BcForm->label('Tool.plugin', __d('baser', 'プラグインテーブル名')) ?>
		</th>
		<td class="col-input">
			<?php echo $this->BcForm->input('Tool.plugin', [
				'type' => 'select',
				'options' => $this->BcForm->getControlSource('Tool.plugin'),
				'multiple' => true,
				'style' => 'width:400px;height:250px']); ?>
			<?php echo $this->BcForm->error('Tool.plugin') ?>
		</td>
	</tr>
</table>
<p>テーブルを選択して「生成」ボタンを押してください。</p>
<div
	class="submit"><?php echo $this->BcForm->submit(__d('baser', '生　成'), ['div' => false, 'class' => 'btn-red button']) ?></div>

<?php echo $this->BcForm->end() ?>
