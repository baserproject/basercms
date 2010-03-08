<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログカテゴリ フォーム
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
 * @package			baser.plugins.blog.views
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

<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if($this->action=='admin_add'): ?>
    <?php echo $form->create('BlogCategory',array('url'=>'/admin/blog/blog_categories/add/'.$blogContent['BlogContent']['id'])) ?>
<?php elseif($this->action=='admin_edit'): ?>
    <?php echo $form->create('BlogCategory',array('url'=>'/admin/blog/blog_categories/edit/'.$blogContent['BlogContent']['id'].'/'.$form->value('BlogCategory.id'))) ?>
<?php endif; ?>
<?php echo $form->hidden('BlogCategory.id') ?>

<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_view' || $this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $form->label('BlogCategory.no', 'NO') ?></th>
		<td class="col-input">
			<?php echo $freeze->text('BlogCategory.no', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $form->label('BlogCategory.name', 'ブログカテゴリ名') ?></th>
		<td class="col-input">
            <?php echo $freeze->text('BlogCategory.name', array('size'=>40,'maxlength'=>255)) ?>
			<?php echo $html->image('help.png',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
			<?php echo $form->error('BlogCategory.name') ?>
			<div id="helptextName" class="helptext">
				<ul>
                    <li>URLに利用されます</li>
					<li>半角英数字で入力して下さい</li>
				</ul>
			</div>
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('BlogCategory.title', 'ブログカテゴリタイトル') ?></th>
		<td class="col-input"><?php echo $freeze->text('BlogCategory.title', array('size'=>40,'maxlength'=>255)) ?><?php echo $form->error('BlogCategory.title') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $form->label('BlogCategory.parent_id', '親カテゴリ') ?></th>
		<td class="col-input"><?php echo $freeze->select('BlogCategory.parent_id', $freeze->getControlSource('BlogCategory.parent_id',array('blogContentId'=>$blogContent['BlogContent']['id'],'excludeParentId'=>$form->value('BlogCategory.id'))),null,array('escape'=>false),'なし') ?><?php echo $form->error('BlogCategory.parent_id') ?>&nbsp;</td>
	</tr>
</table>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $form->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $form->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
<?php else: ?>
	<?php echo $html->link('編集する',array('action'=>'edit', $blogContent['BlogContent']['id'], $form->value('BlogCategory.id')),array('class'=>'btn-orange button'),null,false) ?>　
	<?php echo $html->link('削除する',array('action'=>'delete', $blogContent['BlogContent']['id'], $form->value('BlogCategory.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $form->value('BlogCategory.name')),false); ?>
<?php endif ?>
</div>
