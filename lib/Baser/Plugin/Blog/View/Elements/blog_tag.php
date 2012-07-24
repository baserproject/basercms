<?php
/* SVN FILE: $Id$ */
/**
 * [PUBLISH] タグ
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
?>
<?php if(!empty($this->blog->blogContent['tag_use'])): ?>
	<?php if(!empty($this->blog->post['BlogTag'])) : ?>
<div class="tag">タグ：<?php $this->blog->tag($post) ?></div>
	<?php endif ?>
<?php endif ?>
