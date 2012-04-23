<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] スキーマ読み込み フォーム
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


<!-- form -->
<?php echo $bcForm->create('Tool', array('action' => 'load_schema', 'type' => 'file')) ?>

<table cellpadding="0" cellspacing="0" class="form-table">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $bcForm->label('Tool.schema_type', 'スキーマタイプ') ?></th>
		<td class="col-input">
			<?php echo $bcForm->input('Tool.schema_type', array(
					'type' => 'radio',
					'options' => array('create'=>'テーブル作成', 'alter'=>'テーブル構造変更', 'drop' => 'テーブル削除'),
					'legend' => false,
					'separator' => '　')) ?>
			<?php echo $bcForm->error('Tool.schema_type') ?>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $bcForm->label('Tool.schema_file', 'スキーマファイル') ?></th>
		<td class="col-input">
			<?php echo $bcForm->input('Tool.schema_file', array('type' => 'file')) ?>
			<?php echo $bcForm->error('Tool.schema_file') ?>
		</td>
	</tr>
</table>

<div class="submit"><?php echo $bcForm->submit('読み込み', array('div' => false, 'class' => 'btn-red button')) ?></div>

<?php echo $bcForm->end() ?>