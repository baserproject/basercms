<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] ブログカテゴリー一覧
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if(!isset($count)) {
	$count = false;
}
if(isset($blogContent)){
	$id = $blogContent['BlogContent']['id'];
}else{
	$id = $blog_content_id;
}
if($count) {
	$actionUrl = '/blog/get_categories/'.$id.'/1';
} else {
	$actionUrl = '/blog/get_categories/'.$id;
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
<?php echo $blog->getCategoryList($categories, 1, $count) ?>
</div>
