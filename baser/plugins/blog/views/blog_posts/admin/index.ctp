<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ブログ記事 一覧
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
$blogCategories = $formEx->getControlSource('blog_category_id',array('blogContentId'=>$blogContent['BlogContent']['id']));
?>
<script type="text/javascript">
$(document).ready(function(){
	<?php if($form->value('BlogPost.open')): ?>
	$("#BlogPostFilterBody").show();
	<?php endif ?>
});
</script>

<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ブログ記事の管理が行えます。</p>
	<ul>
		<li>新しい記事を登録するには、画面下の「新規登録」ボタンをクリックします。</li>
		<li>ブログの表示を確認するには、サイドメニューの「公開ページ確認」をクリックします。</li>
		<li>各記事の表示を確認するには、対象記事の「確認」ボタンをクリックします。</li>
		<li>ブログのコメントを確認するには、サイドメニューの「コメント一覧」をクリックするか、各記事のコメント欄の数字をクリックします。</li>
		<li>ブログのカテゴリを登録するには、サイドメニューの「新規カテゴリを登録」をクリックします。</li>
	</ul>
</div>
<h3><a href="javascript:void(0);" class="slide-trigger" id="BlogPostFilter">検索</a></h3>
<div class="function-box corner10" id="BlogPostFilterBody" style="display:none"> <?php echo $formEx->create('BlogPost',array('url'=>array('action'=>'index',$blogContent['BlogContent']['id']))) ?>
	<p>
		<?php if($blogCategories): ?>
		<small>カテゴリ</small> <?php echo $formEx->select('BlogPost.blog_category_id', $blogCategories, null, array('escape'=>false)) ?>　
		<?php endif ?>
		<small>公開設定</small> <?php echo $formEx->select('BlogPost.status', $textEx->booleanMarkList()) ?>　
	</p>
	<?php echo $formEx->hidden('BlogPost.open',array('value'=>true)) ?>
	<div class="align-center"> <?php echo $formEx->submit('検　索',array('div'=>false,'class'=>'btn-orange button')) ?> </div>
</div>

<!-- list-num -->
<?php $baser->element('list_num') ?>

<!-- pagination -->
<?php $baser->pagination('default',array(),null,false) ?>
<?php
// TODO PHP4環境にてCakePHPでエレメント内で設定したプロパティを引き継げない問題があるので
// ページネーションエレメント内にも記述しているがここにも記述
// http://project.e-catchup.jp/issues/show/850
$paginator->options = array('url' => $this->passedArgs);
?>
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableBlogPosts">
	<tr>
		<th>操作</th>
		<th><?php echo $paginator->sort(array('asc'=>'NO ▼','desc'=>'NO ▲'),'no'); ?></th>
		<th style="width:122px"><?php echo $paginator->sort(array('asc'=>'タイトル ▼','desc'=>'タイトル ▲'),'name'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'カテゴリ ▼','desc'=>'カテゴリ ▲'),'BlogCategory.name'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'投稿者 ▼','desc'=>'投稿者 ▲'),'User.real_name_1'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'公開状態 ▼','desc'=>'公開状態 ▲'),'status'); ?></th>
		<?php if($blogContent['BlogContent']['comment_use']): ?>
		<th>コメント</th>
		<?php endif ?>
		<th><?php echo $paginator->sort(array('asc'=>'投稿日 ▼','desc'=>'投稿日 ▲'),'posts_date'); ?></th>
		<th><?php echo $paginator->sort(array('asc'=>'更新日 ▼','desc'=>'更新日 ▲'),'modified'); ?></th>
	</tr>
	<?php if(!empty($posts)): ?>
		<?php $count=0; ?>
		<?php foreach($posts as $post): ?>
			<?php if (!$post['BlogPost']['status']): ?>
				<?php $class=' class="disablerow"'; ?>
			<?php elseif ($count%2 === 0): ?>
				<?php $class=' class="altrow"'; ?>
			<?php else: ?>
				<?php $class=''; ?>
			<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button"><?php $baser->link('確認','/'.$blogContent['BlogContent']['name'].'/archives/'.$post['BlogPost']['no'],array('target'=>'_blank','class'=>'btn-green-s button-s')) ?>
			<?php $baser->link('編集',array('action'=>'edit', $blogContent['BlogContent']['id'], $post['BlogPost']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php $baser->link('削除', array('action'=>'delete', $blogContent['BlogContent']['id'], $post['BlogPost']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $post['BlogPost']['name']),false); ?></td>
		<td><?php echo $post['BlogPost']['no']; ?></td>
		<td><?php $baser->link($post['BlogPost']['name'],array('action'=>'edit', $blogContent['BlogContent']['id'], $post['BlogPost']['id'])) ?></td>
		<td><?php if(!empty($post['BlogCategory']['title'])): ?>
			<?php echo $post['BlogCategory']['title']; ?>
			<?php endif; ?></td>
		<td>
			<?php if(!empty($post['User'])): ?>
			<?php echo $post['User']['real_name_1']." ".$post['User']['real_name_2']; ?>
			<?php endif ?>
		</td>
		<td style="text-align:center"><?php echo $textEx->booleanMark($post['BlogPost']['status']); ?></td>
		<?php if($blogContent['BlogContent']['comment_use']): ?>
		<td><?php $comment = count($post['BlogComment']) ?>
			<?php if($comment): ?>
			<?php $baser->link($comment,array('controller'=>'blog_comments','action'=>'index',$blogContent['BlogContent']['id'],$post['BlogPost']['id'])) ?>
			<?php else: ?>
			<?php echo $comment ?>
			<?php endif ?></td>
		<?php endif ?>
		<td><?php echo $timeEx->format('y-m-d',$post['BlogPost']['posts_date']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$post['BlogPost']['modified']); ?></td>
	</tr>
			<?php $count++; ?>
		<?php endforeach; ?>
	<?php else: ?>
	<tr>
		<td colspan="9"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
	<?php endif; ?>
</table>
<?php $baser->pagination('default',array(),null,false) ?>
<div class="align-center">
	<?php $baser->link('新規登録',array('action'=>'add', $blogContent['BlogContent']['id']),array('class'=>'btn-red button')) ?>
</div>
