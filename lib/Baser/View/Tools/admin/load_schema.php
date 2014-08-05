<?php
/**
 * [ADMIN] スキーマ読み込み フォーム
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<!-- form -->
<?php echo $this->BcForm->create('Tool', array('action' => 'load_schema', 'type' => 'file')) ?>

<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $this->BcForm->label('Tool.schema_type', 'スキーマタイプ') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('Tool.schema_type', array(
				'type' => 'radio',
				'options' => array('create' => 'テーブル作成', 'alter' => 'テーブル構造変更', 'drop' => 'テーブル削除'),
				'legend' => false,
				'separator' => '　')); ?>
			<?php echo $this->BcForm->error('Tool.schema_type') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $this->BcForm->label('Tool.schema_file', 'スキーマファイル') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('Tool.schema_file', array('type' => 'file')) ?>
			<?php echo $this->BcForm->error('Tool.schema_file') ?>
		</td>
	</tr>
</table>

<div class="submit"><?php echo $this->BcForm->submit('読み込み', array('div' => false, 'class' => 'btn-red button')) ?></div>

<?php echo $this->BcForm->end(); ?>
