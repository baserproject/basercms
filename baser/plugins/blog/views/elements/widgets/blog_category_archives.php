<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログカテゴリー一覧
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
if(empty($view_count)) {
	$view_count = '0';
}
if(empty($limit)) {
	$limit = '0';
}
if(!isset($by_year)) {
	$by_year = null;
}
if(isset($blogContent)){
	$id = $blogContent['BlogContent']['id'];
}else{
	$id = $blog_content_id;
}
if(empty($depth)) {
	$depth = 1;
}
$actionUrl = '/blog/blog/get_categories/'.$id.'/'.$limit.'/'.$view_count.'/'.$depth;
if($by_year) {
	$actionUrl .= '/year';
}
$data = $this->requestAction($actionUrl);
$categories = $data['categories'];
$this->viewVars['blogContent'] = $data['blogContent'];
App::import('Helper','Blog.Blog');
$blog = new BlogHelper();
?>


<div class="widget widget-blog-categories-archives widget-blog-categories-archives-<?php echo $id ?> blog-widget">
<?php if($name && $use_title): ?>
<h2><?php echo $name ?></h2>
<?php endif ?>
<?php if($by_year): ?>
	<ul>
	<?php foreach($categories as $key => $category): ?>
		<li class="category-year"><span><?php $bcBaser->link($key.'年', array('plugin' => null, 'controller' => $blogContent['BlogContent']['name'], 'action' => 'archives', 'date', $key)) ?></span>
		<?php echo $blog->getCategoryList($category, $depth, $view_count, array('named' => array('year' => $key))) ?>
		</li>
	<?php endforeach ?>
	</ul>
<?php else: ?>
	<?php echo $blog->getCategoryList($categories, $depth, $view_count) ?>
<?php endif ?>
</div>
