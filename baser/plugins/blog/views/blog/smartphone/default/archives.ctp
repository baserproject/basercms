<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログアーカイブ一覧
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$baser->setDescription($blog->getTitle().'｜'.$baser->getContentsTitle().'のアーカイブ一覧です。');
?>

<!-- title -->
<h2 class="contents-head">
	<?php $blog->title() ?>
</h2>

<!-- archives title -->
<h3 class="contents-head">
	<?php $baser->contentsTitle() ?>
</h3>

<section class="box news">
<!-- list -->
<?php if(!empty($posts)): ?>
<ul>
	<?php foreach($posts as $post): ?>
<li><?php $blog->postLink($post, '<span class="date">'.$blog->getPostDate($post).'</span><br />'.$blog->getPostTitle($post)) ?></li>
	<?php endforeach; ?>
</ul>
<?php else: ?>
<p class="no-data">記事がありません。</p>
<?php endif; ?>
</section>

<!-- pagination -->
<?php $baser->pagination('simple'); ?>