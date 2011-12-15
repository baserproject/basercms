<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ最近の投稿ウィジェット設定
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.blog.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$title = '最近の投稿';
$description = 'ブログの最近の投稿を表示します。';
?>
<?php echo $formEx->label($key.'.count','表示数') ?>&nbsp;
<?php echo $formEx->text($key.'.count', array('size' => 6, 'value' => 5)) ?>&nbsp;件<br />
<?php echo $formEx->label($key.'.blog_content_id','ブログ') ?>&nbsp;
<?php echo $formEx->select($key.'.blog_content_id',$formEx->getControlSource('Blog.BlogContent.id'), null, null, false) ?><br />
<small>ブログページを表示している場合は、上記の設定に関係なく、対象ブログの最近の投稿を表示します。</small>