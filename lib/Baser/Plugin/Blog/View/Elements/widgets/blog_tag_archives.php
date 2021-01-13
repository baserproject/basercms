<?php
/**
 * @var string $name
 * @var bool $use_title
 * @var int $blog_content_id
 * @var bool $view_count
 * @var \BcAppView $this
 */
$id = '';
?>


<div class="widget widget-blog-authors widget-blog-authors-<?php echo $id ?> blog-widget">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php $this->BcBaser->blogTagList((int)$blog_content_id, ['postCount' => $view_count]) ?>
</div>
