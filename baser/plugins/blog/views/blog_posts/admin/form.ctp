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
<script type="text/javascript">
$(function(){
	$("#BtnPreview").click(function(){
		var action = $("#BlogPostForm").attr('action');
		$("#BlogPostForm").attr('action','<?php echo $this->base ?>/admin/blog/preview/<?php echo $blogContent['BlogContent']['id'] ?>');
		$("#BlogPostForm").attr('target','_blank');
		$("#BlogPostForm").submit();
		$("#BlogPostForm").attr('action',action);
		$("#BlogPostForm").attr('target','_self');
		return false;
	});
});
</script>

<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ブログ記事の登録を行います。</p>
	<ul>
		<li>ワード感覚でブログ記事の作成を行う事ができます。</li>
		<li>記事を作成してもすぐに公開しない場合は、公開状態を「公開しない」にしておきます。</li>
		<li>「公開しない」にした記事を確認するには、画面下の「確認」ボタン、または、一覧の「確認」ボタンをクリックします。</li>
	</ul>
</div>

<?php if($this->action == 'admin_edit'): ?>
	<?php if($formEx->value('BlogPost.status')): ?>
	<p><strong>この記事のURL：<?php $baser->link($baser->getUri('/'.$blogContent['BlogContent']['name'].'/archives/'.$formEx->value('BlogPost.no')),'/'.$blogContent['BlogContent']['name'].'/archives/'.$formEx->value('BlogPost.no'),array('target'=>'_blank')) ?></strong></p>
	<?php else: ?>
	<p><strong>この記事のURL：<?php echo $baser->getUri('/'.$blogContent['BlogContent']['name'].'/archives/'.$formEx->value('BlogPost.no')) ?></strong></p>
	<?php endif ?>
<?php endif ?>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php /* BlogContent.idを第一引数にしたいが為にURL直書き */ ?>
<?php if($this->action=='admin_add'): ?>
<?php echo $formEx->create('BlogPost',array('url'=>'/admin/blog/blog_posts/add/'.$blogContent['BlogContent']['id'],'id'=>'BlogPostForm')) ?>
<?php elseif($this->action=='admin_edit'): ?>
<?php echo $formEx->create('BlogPost',array('url'=>'/admin/blog/blog_posts/edit/'.$blogContent['BlogContent']['id'].'/'.$formEx->value('BlogPost.id'),'id'=>'BlogPostForm')) ?>
<?php endif; ?>
<?php echo $formEx->hidden('BlogPost.id') ?> <?php echo $formEx->hidden('BlogPost.blog_content_id',array('value'=>$blogContent['BlogContent']['id'])) ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogPost.no', 'NO') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogPost.no', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp; </td>
	</tr>
	<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogPost.name', 'タイトル') ?></th>
		<td class="col-input"><?php echo $formEx->text('BlogPost.name', array('size'=>40,'maxlength'=>255)) ?><?php echo $formEx->error('BlogPost.name') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogPost.content', '本文') ?></th>
		<td class="col-input"><?php echo $ckeditor->textarea('BlogPost.content',array('cols'=>60, 'rows'=>20)) ?> <?php echo $formEx->error('BlogPost.content') ?>&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogPost.detail', '詳細') ?></th>
		<td class="col-input"><?php echo $ckeditor->textarea('BlogPost.detail', array('cols'=>60,'rows'=>20)) ?><?php echo $formEx->error('BlogPost.detail') ?>&nbsp;</td>
	</tr>
	<?php $categories = $formEx->getControlSource('BlogPost.blog_category_id',array('blogContentId'=>$blogContent['BlogContent']['id'])) ?>
	<?php if($categories): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('BlogPost.blog_category_id', 'カテゴリ') ?></th>
		<td class="col-input"><?php echo $formEx->select('BlogPost.blog_category_id',$categories,null,array('escape'=>false),'なし') ?><?php echo $formEx->error('BlogPost.blog_category_id') ?>&nbsp;</td>
	</tr>
	<?php endif ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogPost.user_id', '投稿者') ?></th>
		<td class="col-input"><?php if($this->action=='admin_edit' && count($users) && isset($user) && $user['user_group_id']!=1): ?>
			<?php echo $users[$formEx->value('User.id')] ?>
			<?php else: ?>
			<?php echo $formEx->select('BlogPost.user_id',$users,null,null,false) ?><?php echo $formEx->error('BlogPost.user_id') ?>
			<?php endif ?></td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogPost.status', '公開状態') ?></th>
		<td class="col-input"><?php echo $formEx->radio('BlogPost.status', $textEx->booleanDoList("公開"),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?> <?php echo $formEx->error('BlogPost.status') ?> &nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('BlogPost.posts_date', '投稿日') ?></th>
		<td class="col-input"><?php echo $formEx->dateTimePicker('BlogPost.posts_date',array('size'=>12,'maxlength'=>10),true) ?> <?php echo $formEx->error('BlogPost.posts_date') ?> &nbsp; </td>
	</tr>
</table>
<div class="submit">
	<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
	<?php echo $formEx->end(array('label'=>'保存前確認','div'=>false,'class'=>'btn-green button','id'=>'BtnPreview')) ?>
	<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php echo $formEx->end(array('label'=>'保存前確認','div'=>false,'class'=>'btn-green button','id'=>'BtnPreview')) ?>
	<?php $baser->link('削　除',array('action'=>'delete', $blogContent['BlogContent']['id'], $formEx->value('BlogPost.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $formEx->value('BlogPost.name')),false); ?>
	<?php endif ?>
</div>
