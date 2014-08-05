<?php
/**
 * [ADMIN] スキーマ生成 フォーム
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


<?php echo $this->BcForm->create('Tool', array('action' => 'write_schema')) ?>

<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $this->BcForm->label('Tool.baser', 'コアテーブル名') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('Tool.baser', array(
				'type' => 'select',
				'options' => $this->BcForm->getControlSource('Tool.baser'),
				'multiple' => true,
				'style' => 'width:400px;height:250px')); ?>
			<?php echo $this->BcForm->error('Tool.baser') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $this->BcForm->label('Tool.plugin', 'プラグインテーブル名') ?></th>
		<td class="col-input">
			<?php echo $this->BcForm->input('Tool.plugin', array(
				'type' => 'select',
				'options' => $this->BcForm->getControlSource('Tool.plugin'),
				'multiple' => true,
				'style' => 'width:400px;height:250px')); ?>
			<?php echo $this->BcForm->error('Tool.plugin') ?>
		</td>
	</tr>
</table>

<div class="submit"><?php echo $this->BcForm->submit('生　成', array('div' => false, 'class' => 'btn-red button')) ?></div>

<?php echo $this->BcForm->end() ?>