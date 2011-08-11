<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログ年別アーカイブ
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
if(!isset($view_count)) {
	$view_count = false;
}
if(isset($blogContent)){
	$id = $blogContent['BlogContent']['id'];
}else{
	$id = $blog_content_id;
}
$actionUrl = '/blog/get_posted_years/'.$id;
if($view_count) {
	$actionUrl .= '/1';
}
$data = $this->requestAction($actionUrl);
$postedDates = $data['postedDates'];
$blogContent = $data['blogContent'];
$baseCurrentUrl = $blogContent['BlogContent']['name'].'/archives/date/';
?>
<div class="widget widget-blog-yearly-archives widget-blog-yearly-archives-<?php echo $id ?>">
<?php if($name && $use_title): ?>
<h2><?php echo $name ?></h2>
<?php endif ?>
	<?php if(!empty($postedDates)): ?>
	<ul>
		<?php foreach($postedDates as $postedDate): ?>
			<?php if(isset($this->params['named']['year']) && $this->params['named']['year'] == $postedDate['year']): ?>
				<?php $class = ' class="selected"' ?>
			<?php elseif($this->params['url']['url'] == $baseCurrentUrl.$postedDate['year']): ?>
				<?php $class = ' class="current"' ?>
			<?php else: ?>
				<?php $class = '' ?>
			<?php endif ?>
			<?php if($view_count): ?>
				<?php $title = $postedDate['year'].'年'.'('.$postedDate['count'].')' ?>
			<?php else: ?>
				<?php $title = $postedDate['year'].'年' ?>
			<?php endif ?>
		<li<?php echo $class ?>>
			<?php $baser->link($title, array(
				'admin'			=> false, 
				'plugin'		=> '', 
				'controller'	=> $blogContent['BlogContent']['name'], 
				'action'		=> 'archives', 
				'date', $postedDate['year']
			), array('prefix' => true)) ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>