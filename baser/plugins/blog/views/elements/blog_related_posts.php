<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] 関連投稿一覧
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$relatedPosts = $blog->getRelatedPosts($post);
?>
<?php if($relatedPosts): ?>
<div id="RelatedPosts">
	<h4 class="contents-head">関連記事</h4>
	<ul>
	<?php foreach($relatedPosts as $relatedPost): ?>
		<li><?php $blog->postTitle($relatedPost) ?></li>
	<?php endforeach ?>
	</ul>
</div>
<?php endif ?>