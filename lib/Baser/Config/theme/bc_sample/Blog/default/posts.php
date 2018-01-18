<?php
/**
 * パーツ用ブログ記事一覧
 *
 * BcBaserHelper::blogPosts( コンテンツ名, 件数 ) で呼び出す
 * （例）<?php $this->BcBaser->blogPosts('news', 3) ?>
 */
?>


<?php if ($posts): ?>
<ul class="post-list">
	<?php foreach ($posts as $key => $post): ?>
		<?php
		$class = array('clearfix', 'post-' . ($key + 1));
		if ($this->BcArray->first($posts, $key)) {
			$class[] = 'first';
		} elseif ($this->BcArray->last($posts, $key)) {
			$class[] = 'last';
		}
		?>
	<li class="<?php echo implode(' ', $class) ?>">
		<?php $this->Blog->eyeCatch($post, array('width' => 150, 'link' => false)) ?>
		<p><?php $this->Blog->postDate($post, 'Y.m.d') ?><br>
		<?php $this->Blog->postTitle($post) ?><br>
		<?php $this->Blog->postContent($post, false, false, 70) ?></p>
	</li>
	<?php endforeach; ?>
</ul>
<?php else: ?>
<p class="no-data"><?php echo __('記事がありません。'); ?></p>
<?php endif ?>
