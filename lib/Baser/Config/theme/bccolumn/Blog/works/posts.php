<?php
/**
 * トップページ記事一覧
 */
?>


<?php if ($posts): ?>
	<ul class="post-list clearfix">
		<?php foreach ($posts as $key => $post): ?>
			<?php $class = array('clearfix', 'post-' . ($key + 1)) ?>
			<?php if ($this->BcArray->first($posts, $key)): ?>
				<?php $class[] = 'first' ?>
			<?php elseif ($this->BcArray->last($posts, $key)): ?>
				<?php $class[] = 'last' ?>
			<?php endif ?>
			<?php $uri = $this->BcBaser->getRoot().$post['BlogContent']['name'].'/archives/'.$post['BlogPost']['no']; ?>

			<li class="<?php echo implode(' ', $class) ?>">
				<div class="thumbnail">
					<a href="<?php echo $uri ?>">
						<?php $this->Blog->eyeCatch($post, array('link'=>false, 'width'=>'280px', 'noimage'=>'/theme/bccolumn/img/blog/works/noimage.png')) ?>
					</a>
				</div>
	            <h3><?php $this->Blog->postTitle($post) ?></h3>
	            <p><?php $this->Blog->postContent($post, true, false, 40) ?>...</p>

			</li>
		<?php endforeach; ?>
	</ul>
	<?php else: ?>
	<p class="no-data">記事がありません</p>
<?php endif ?>
