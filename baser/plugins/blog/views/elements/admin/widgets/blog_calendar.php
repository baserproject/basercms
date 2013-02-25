<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログカレンダーウィジェット設定
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
$title = 'ブログカレンダー';
$description = 'ブログのカレンダーを表示します。';
?>
<?php echo $bcForm->label($key.'.blog_content_id', 'ブログ') ?>&nbsp;
<?php echo $bcForm->select($key.'.blog_content_id', $bcForm->getControlSource('Blog.BlogContent.id'), null, null, false) ?><br />
<small>ブログページを表示している場合は、上記の設定に関係なく、対象ブログのブログカレンダーを表示します。</small>