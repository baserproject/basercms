<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] スキーマ生成 フォーム
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<h2>
	<?php $baser->contentsTitle() ?>
</h2>
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $form->create('Schema',array('action'=>'write')) ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('SiteConfig.mode', 'モデル名') ?></th>
		<td class="col-input"><?php echo $form->text('Schema.model') ?> <?php echo $form->error('Schema.model') ?></td>
	</tr>
</table>
<div class="align-center"> <?php echo $form->end(array('label'=>'生　成','div'=>false,'class'=>'btn-red button')) ?> </div>
