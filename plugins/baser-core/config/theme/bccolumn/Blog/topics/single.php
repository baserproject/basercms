<?php
/**
 * ブログ詳細ページ
 */
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->Blog->getPostContent($post, false, false, 50));
?>


<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade", maxWidth:"80%"});
	});
</script>


<div class="blog topics topics-single">

<h2><?php $this->BcBaser->contentsTitle() ?></h2>

<div class="eye-catch">
<?php echo $this->Blog->eyeCatch($post) ?>
</div>

<div class="post">
<?php $this->Blog->postContent($post) ?>
	<div class="meta"> 
		<span class="date">
<?php $this->Blog->postDate($post) ?>
		</span>
		<span class="category">
			<?php $this->Blog->category($post) ?>
			&nbsp;
<?php $this->Blog->author($post) ?>
		</span>
    </div>
<?php $this->BcBaser->element('blog_tag', array('post' => $post)) ?>
</div>
<div class="post-navi">
	<?php $this->Blog->prevLink($post, "＜ ". __('前の記事')) ?>
	&nbsp;  &nbsp;
	<?php $this->BcBaser->link(__('一覧へ'), '/'.$this->request->params['Content']['name'].'/index') ?>
	&nbsp;  &nbsp;	<?php $this->Blog->nextLink($post, __('後の記事'). " ＞") ?>
</div>
<?php $this->BcBaser->element('blog_comments') ?>
</div>