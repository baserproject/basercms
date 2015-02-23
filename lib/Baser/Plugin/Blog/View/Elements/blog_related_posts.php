<?php
/**
 * [PUBLISH] 関連投稿一覧
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$relatedPosts = $this->Blog->getRelatedPosts($post);
?>
<?php if ($relatedPosts): ?>
	<div id="RelatedPosts">
		<h4 class="contents-head">関連記事</h4>
		<ul>
			<?php foreach ($relatedPosts as $relatedPost): ?>
				<li><?php $this->Blog->postTitle($relatedPost) ?></li>
			<?php endforeach ?>
		</ul>
	</div>
<?php endif ?>