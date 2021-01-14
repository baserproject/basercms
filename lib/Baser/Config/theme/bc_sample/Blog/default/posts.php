<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * パーツ用ブログ記事一覧
 * 呼出箇所：トップページ
 *
 * BcBaserHelper::blogPosts( コンテンツ名, 件数 ) で呼び出す
 * （例）<?php $this->BcBaser->blogPosts('news', 3) ?>
 *
 * @var BcAppView $this
 * @var array $posts ブログ記事リスト
 */
?>


<?php if ($posts): ?>
<ul class="bs-top-post">
	<?php foreach ($posts as $key => $post): ?>
		<?php
		$class = ['bs-top-post__item', 'post-' . ($key + 1)];
		if ($this->BcArray->first($posts, $key)) {
			$class[] = 'first';
		} elseif ($this->BcArray->last($posts, $key)) {
			$class[] = 'last';
		}
		?>
	<li class="<?php echo implode(' ', $class) ?>">
		<?php if(!empty($post['BlogPost']['eye_catch'])): ?>
		<a href="<?php echo $this->Blog->getPostLinkUrl($post) ?>" class="bs-top-post__item-eye-catch">
			<?php $this->Blog->eyeCatch($post, ['width' => 150, 'link' => false]) ?>
		</a>
		<?php endif ?>
		<span class="bs-top-post__item-date"><?php $this->Blog->postDate($post, 'Y.m.d') ?></span>
		<?php $this->Blog->category($post, ['class' => 'bs-top-post__item-category']) ?>
		<a href="<?php echo $this->Blog->getPostLinkUrl($post) ?>" class="bs-top-post__item-title"><?php $this->Blog->postTitle($post, false) ?></a>
		<?php if(strip_tags($post['BlogPost']['content'] . $post['BlogPost']['detail'])): ?>
		<div class="bs-top-post__item-detail"><?php $this->Blog->postContent($post, true, false, 46) ?>...</div>
		<?php endif ?>
	</li>
	<?php endforeach ?>
</ul>
<div class="bs-top-post-to-list"><?php $this->BcBaser->link('VIEW ALL', $this->Blog->getContentsUrl($posts[0]['BlogPost']['blog_content_id'], false)) ?></div>
<?php else: ?>
<p class="bs-top-post-no-data"><?php echo __('記事がありません。'); ?></p>
<?php endif ?>
