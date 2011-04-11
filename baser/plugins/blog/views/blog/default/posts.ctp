<?php if($posts): ?>
<ul class="post-list">
	<?php foreach($posts as $key => $post): ?>
		<?php $class = array('clearfix', 'post-'.($key+1)) ?>
		<?php if($array->first($posts, $key)): ?>
			<?php $class[] = 'first' ?>
		<?php elseif($array->last($posts, $key)): ?>
			<?php $class[] = 'last' ?>
		<?php endif ?>
	<li class="<?php echo implode(' ', $class) ?>">
		<span class="date"><?php $blog->postDate($post, 'Y.m.d') ?></span><br />
		<span class="title"><?php $blog->postTitle($post) ?></span>
	</li>
	<?php endforeach ?>
</ul>
<?php else: ?>
<p class="no-data">記事がありません</p>
<?php endif ?>