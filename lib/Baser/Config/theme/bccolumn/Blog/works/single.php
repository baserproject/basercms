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


<div class="blog works works-single">
	<div class="category"><?php $this->Blog->category($post) ?></div>

	<h2><?php $this->BcBaser->contentsTitle() ?></h2>

	<?php $this->BcBaser->element('blog_tag', array('post' => $post)) ?>

	<div class="eye-catch">
	<?php $this->Blog->eyeCatch($post, array('noimage'=>'/theme/bccolumn/img/blog/works/noimage.png')) ?>
	</div>

	<div class="post">
	<?php $this->Blog->postContent($post) ?>
	</div>
	<div id="contentsNavi">
		<?php $this->Blog->prevLink($post, "＜ 前の記事") ?>
		&nbsp;  &nbsp;
		<?php $this->BcBaser->link('一覧へ', '/'.$post['BlogContent']['name'].'/index') ?>
		&nbsp;  &nbsp;
		<?php $this->Blog->nextLink($post, "後の記事 ＞") ?>
	</div>
	<?php $this->BcBaser->element('blog_comments') ?>
</div>