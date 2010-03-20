<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ページカテゴリー フォーム
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

<?php echo $form->create('PageCategory') ?>
<?php echo $form->hidden('PageCategory.id') ?>
<?php echo $form->hidden('PageCategory.no') ?>
<?php echo $form->hidden('PageCategory.theme') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $form->label('PageCategory.no', 'NO') ?></th>
		<td class="col-input">
			<?php echo $freeze->text('PageCategory.no', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('PageCategory.name', 'ページカテゴリー名') ?></th>
		<td class="col-input">
			<?php echo $freeze->text('PageCategory.name', array('size'=>40,'maxlength'=>255)) ?>
			<?php echo $html->image('help.png',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
			<?php echo $form->error('PageCategory.name') ?>
			<div id="helptextName" class="helptext">
				<ul>
                    <li>ページカテゴリ名はURLで利用します</li>
					<li>半角英数字で入力して下さい</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('PageCategory.title', 'ページカテゴリータイトル') ?></th>
		<td class="col-input">
            <?php echo $freeze->text('PageCategory.title', array('size'=>40,'maxlength'=>255)) ?>
			<?php echo $html->image('help.png',array('id'=>'helpTitle','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $form->error('PageCategory.title') ?>
			<div id="helptextTitle" class="helptext">
				<ul>
                    <li>ページカテゴリタイトルはTitleタグに利用します。</li>
				</ul>
			</div>
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('PageCategory.parent_id', '親カテゴリ') ?></th>
		<td class="col-input">
            <?php echo $freeze->select('PageCategory.parent_id', $freeze->getControlSource('parent_id',array('excludeParentId'=>$form->value('PageCategory.id'))),null,array('escape'=>false),'なし') ?>
            <?php echo $html->image('help.png',array('id'=>'helpParentId','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $form->error('PageCategory.parent_id') ?>
			<div id="helptextParentId" class="helptext">
				<ul>
                    <li>カテゴリの下の階層にカテゴリを作成するには親カテゴリを選択します。</li>
				</ul>
			</div>
        </td>
	</tr>
</table>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $form->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $form->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削除する',array('action'=>'delete', $form->value('PageCategory.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $form->value('PageCategory.name')),false); ?>
<?php endif ?>
</div>
