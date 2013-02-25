<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログ最近の投稿
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if(!isset($count)) {
	$count = 5;
}
if(isset($blogContent)){
	$id = $blogContent['BlogContent']['id'];
}else{
	$id = $blog_content_id;
}
$data = $this->requestAction('/blog/blog/get_recent_entries/'.$id.'/'.$count);
$recentEntries = $data['recentEntries'];
$blogContent = $data['blogContent'];
$baseCurrentUrl = $blogContent['BlogContent']['name'].'/archives/';
?>
<div class="widget widget-blog-recent-entries widget-blog-recent-entries-<?php echo $id ?> blog-widget">
<?php if($name && $use_title): ?>
<h2><?php echo $name ?></h2>
<?php endif ?>
	<?php if($recentEntries): ?>
	<ul>
		<?php foreach($recentEntries as $recentEntry): ?>
			<?php if($this->params['url']['url'] == $baseCurrentUrl.$recentEntry['BlogPost']['no']): ?>
				<?php $class = ' class="current"' ?>
			<?php else: ?>
				<?php $class = '' ?>
			<?php endif ?>
		<li<?php echo $class ?>>
			<?php $bcBaser->link($recentEntry['BlogPost']['name'],array('admin'=>false,'plugin'=>'','controller'=>$blogContent['BlogContent']['name'],'action'=>'archives',$recentEntry['BlogPost']['no'])) ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>
