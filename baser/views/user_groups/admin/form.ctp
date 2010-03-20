<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ユーザーグループ登録/編集フォーム
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
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $form->create('UserGroup') ?>
<?php echo $form->hidden('UserGroup.id') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_view' || $this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $form->label('UserGroup.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $form->text('UserGroup.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
<tr>
	<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('UserGroup.name', '識別名') ?></th>
	<td class="col-input">
		<?php if($form->value('UserGroup.name')=='admins'): ?>
			<?php echo $form->text('UserGroup.name', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>
		<?php else: ?>
			<?php echo $form->text('UserGroup.name', array('size'=>20,'maxlength'=>255)) ?>
		<?php endif ?>
		<?php echo $html->image('help.png',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
		<?php echo $form->error('UserGroup.name') ?>
        <div id="helptextName" class="helptext">
            <ul>
                <li>半角英数字で入力して下さい。</li>
				<li>識別名 admin は変更できません。</li>
            </ul>
        </div>
	</td>
</tr>
<tr>
	<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('UserGroup.title', 'ユーザーグループ名') ?></th>
	<td class="col-input">
		<?php echo $form->text('UserGroup.title', array('size'=>20,'maxlength'=>255)) ?>
		<?php echo $form->error('UserGroup.title') ?>
	</td>
</tr>
</table>

<div class="align-center">
<?php if ($this->action == 'admin_edit'): ?>
	<?php echo $form->submit('更　新',array('div'=>false,'class'=>'btn-orange button')) ?>
	<?php echo $html->link('削除する', array('action'=>'delete', $form->value('UserGroup.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $form->value('UserGroup.name')),false); ?>
	</form>
<?php else: ?>
	<?php echo $form->end(array('label'=>'登　録', 'div'=>false,'class'=>'btn-red button')) ?>
<?php endif ?>
</div>
