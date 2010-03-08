<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログ記事 フォーム
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
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if($this->action=='admin_add'): ?>
    <?php echo $freeze->create('BlogPost',array('url'=>'/admin/blog/blog_posts/add/'.$blogContent['BlogContent']['id'])) ?>
<?php elseif($this->action=='admin_edit'): ?>
    <?php echo $freeze->create('BlogPost',array('url'=>'/admin/blog/blog_posts/edit/'.$blogContent['BlogContent']['id'].'/'.$freeze->value('BlogPost.id'))) ?>
<?php endif; ?>
<?php echo $freeze->hidden('BlogPost.id') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $freeze->label('BlogPost.no', 'NO') ?></th>
		<td class="col-input">
			<?php echo $freeze->text('BlogPost.no', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $freeze->label('BlogPost.name', 'タイトル') ?></th>
		<td class="col-input"><?php echo $freeze->text('BlogPost.name', array('size'=>40,'maxlength'=>255)) ?><?php echo $freeze->error('BlogPost.name') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $freeze->label('BlogPost.content', '本文') ?></th>
		<!--<td class="col-input"><?php //echo $freeze->nicEdit('BlogPost.content', array('cols'=>60,'rows'=>16),true) ?><?php //echo $freeze->error('BlogPost.content') ?>&nbsp;</td>-->
        <td class="col-input">
            <?php echo $ckeditor->textarea('BlogPost.content',array('cols'=>60, 'rows'=>20)) ?>
            <?php echo $freeze->error('BlogPost.content') ?>&nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $freeze->label('BlogPost.detail', '詳細') ?></th>
		<td class="col-input"><?php echo $ckeditor->textarea('BlogPost.detail', array('cols'=>60,'rows'=>20)) ?><?php echo $freeze->error('BlogPost.detail') ?>&nbsp;</td>
	</tr>
    <?php $categories = $freeze->getControlSource('BlogCategory.parent_id',array('blogContentId'=>$blogContent['BlogContent']['id'])) ?>
    <?php if($categories): ?>
    <tr>
		<th class="col-head"><?php echo $freeze->label('BlogPost.blog_category_id', 'カテゴリ') ?></th>
		<td class="col-input"><?php echo $freeze->select('BlogPost.blog_category_id',$categories,null,array('escape'=>false),'なし') ?><?php echo $freeze->error('BlogPost.blog_category_id') ?>&nbsp;</td>
	</tr>
    <?php endif ?>
	<tr>
		<th class="col-head"><?php echo $freeze->label('BlogPost.user_id', '投稿者') ?></th>
		<td class="col-input"><?php echo $freeze->select('BlogPost.user_id',$users) ?><?php echo $freeze->error('BlogPost.user_id') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $freeze->label('BlogPost.status', '公開状態') ?></th>
		<td class="col-input">
            <?php echo $freeze->radio('BlogPost.status', $textEx->booleanDoList("公開"),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?>
            <?php echo $freeze->error('BlogPost.status') ?>
            &nbsp;
		</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $freeze->label('BlogPost.posts_date', '投稿日') ?></th>
		<td class="col-input">
            <?php echo $freeze->dateTimePicker('BlogPost.posts_date',array('size'=>12,'maxlength'=>10),true) ?>
            <?php echo $freeze->error('BlogPost.posts_date') ?>
            &nbsp;
		</td>
	</tr>
</table>
<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $freeze->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php elseif ($this->action == 'admin_edit'): ?>
	<?php $baser->link('確　認',array('controller'=>'blog','action'=>'preview', $blogContent['BlogContent']['id'], $formEx->value('BlogPost.id')), array('class'=>'btn-green button','target'=>'_blank')) ?>
	<?php echo $freeze->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php echo $html->link('削除する',array('action'=>'delete', $blogContent['BlogContent']['id'], $freeze->value('BlogPost.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $freeze->value('BlogPost.name')),false); ?>
<?php endif ?>
</div>
