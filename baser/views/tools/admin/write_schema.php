<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] スキーマ生成 フォーム
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>


<?php echo $bcForm->create('Tool', array('action' => 'write_schema')) ?>

<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $bcForm->label('Tool.baser', 'コアテーブル名') ?></th>
		<td class="col-input">
			<?php echo $bcForm->input('Tool.baser', array(
				'type'		=> 'select',
				'options'	=> $bcForm->getControlSource('Tool.baser'),
				'multiple'	=> true,
				'style'		=> 'width:400px;height:250px')) ?>
			<?php echo $bcForm->error('Tool.baser') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $bcForm->label('Tool.plugin', 'プラグインテーブル名') ?></th>
		<td class="col-input">
			<?php echo $bcForm->input('Tool.plugin', array(
				'type'		=> 'select',
				'options'	=> $bcForm->getControlSource('Tool.plugin'),
				'multiple'	=> true,
				'style'		=> 'width:400px;height:250px')) ?>
			<?php echo $bcForm->error('Tool.plugin') ?>
		</td>
	</tr>
</table>

<div class="submit"><?php echo $bcForm->submit('生　成', array('div' => false, 'class' => 'btn-red button')) ?></div>

<?php echo $bcForm->end() ?>