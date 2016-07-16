<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			SingleBlog.View
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<h1><?php echo h($this->content['title']) ?></h1>
<h2><?php echo h($data['SingleBlogPost']['title']) ?></h2>
<p><?php echo h($data['SingleBlogPost']['content']) ?></p>