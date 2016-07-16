<?php
/**
 * ブログ詳細ページ
 */
$this->BcBaser->css('admin/colorbox/colorbox', array('inline' => false));
$this->BcBaser->js('admin/jquery.colorbox-min-1.4.5', false);
$this->BcBaser->setDescription($this->Blog->getTitle() . '｜' . $this->Blog->getPostContent($post, false, false, 50));
?>


<script type="text/javascript">
$(function(){
	if($("a[rel='colorbox']").colorbox) $("a[rel='colorbox']").colorbox({transition:"fade"});
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
<div id="contentsNavi">
	<?php $this->Blog->prevLink($post, "＜ 前の記事") ?>
	&nbsp;  &nbsp;
	<?php $this->BcBaser->link('一覧へ', '/'.$post['BlogContent']['name'].'/index') ?>
	&nbsp;  &nbsp;	<?php $this->Blog->nextLink($post, "後の記事 ＞") ?>
</div>
<?php $this->BcBaser->element('blog_comments') ?>
</div>