<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログ月別アーカイブ
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
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
if(!isset($count)) {
	$count = true;
}
if(isset($blogContent)){
	$id = $blogContent['BlogContent']['id'];
}else{
	$id = $blog_content_id;
}
if($count) {
	$actionUrl = '/blog/get_blog_dates/'.$id.'/1';
} else {
	$actionUrl = '/blog/get_blog_dates/'.$id;
}
$data = $this->requestAction($actionUrl);
$blogDates = $data['blogDates'];
$blogContent = $data['blogContent'];
?>

<div class="widget widget-blog-monthly-archives widget-blog-monthly-archives-<?php echo $id ?>">
<?php if($name && $use_title): ?>
<h2><?php echo $name ?></h2>
<?php endif ?>
	<?php if(!empty($blogDates)): ?>
	<ul>
		<?php foreach($blogDates as $blogDate): ?>
			<?php if($count): ?>
				<?php $title = $blogDate['year'].'年'.$blogDate['month'].'月'.'('.$blogDate['count'].')' ?>
			<?php else: ?>
				<?php $title = $blogDate['year'].'年'.$blogDate['month'].'月' ?>
			<?php endif ?>
		<li>
			<?php $baser->link($title, array('admin'=>false,'plugin'=>'','controller'=>$blogContent['BlogContent']['name'],'action'=>'archives','date',$blogDate['year'],$blogDate['month']),array('prefix'=>true)) ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>
