<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] プラグイン　フォーム
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
<?php if($this->action == 'admin_view'): ?>
<?php $freeze->freeze(); ?>
<?php endif; ?>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $form->create('Plugin',array('url'=>array($this->data['Plugin']['name']))) ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_view' || $this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $form->label('Plugin.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $freeze->text('Plugin.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('Plugin.name', 'プラグイン名') ?></th>
		<td class="col-input"><?php echo $freeze->text('Plugin.name', array('size'=>40,'maxlength'=>255,'readonly'=>'readonly')) ?><?php echo $form->error('Plugin.name') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('Plugin.title', 'タイトル') ?></th>
		<td class="col-input"><?php echo $freeze->text('Plugin.title', array('size'=>40,'maxlength'=>255)) ?><?php echo $form->error('Plugin.title') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('Plugin.admin_link', '管理URL') ?></th>
		<td class="col-input"><?php echo $freeze->text('Plugin.admin_link', array('size'=>40,'maxlength'=>255)) ?><?php echo $form->error('Plugin.admin_link') ?>
            <?php echo $html->image('help.png',array('id'=>'helpAdminLink','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextAdminLink" class="helptext">
                先頭にスラッシュをつけたルートパスで入力して下さい。<br />(例) /admin/plugins/index
            </div>
            &nbsp;
        </td>
	</tr>
</table>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $form->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $form->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
<?php else: ?>
	<?php $baser->link('編集する',array('action'=>'edit',$form->value('Plugin.id')),array('class'=>'btn-orange button'),null,false) ?>　
	<?php $baser->link('削除する',array('action'=>'delete', $form->value('Plugin.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $form->value('Plugin.name')),false); ?>
<?php endif ?>
</div>
