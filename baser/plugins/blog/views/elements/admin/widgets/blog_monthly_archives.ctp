<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ブログ月別アーカイブー一覧ウィジェット設定
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
$title = '月別アーカイブ一覧';
$description = 'ブログの月別アーカイブー一覧を表示します。';
?>
<?php echo $formEx->label($key.'.count','表示数') ?>&nbsp;
<?php echo $formEx->text($key.'.count', array('size' => 6, 'value' => 12)) ?>&nbsp;件<br />
<?php echo $formEx->label($key.'.view_count', '件数表示') ?>&nbsp;
<?php echo $formEx->radio($key.'.view_count', $textEx->booleanDoList(''), array('legend' => false, 'value' => false)) ?><br />
<?php echo $formEx->label($key.'.blog_content_id', 'ブログ') ?>&nbsp;
<?php echo $formEx->select($key.'.blog_content_id', $formEx->getControlSource('Blog.BlogContent.id'), null, null, false) ?><br />
<small>ブログページを表示している場合は、上記の設定に関係なく、対象ブログの月別アーカイブ一覧を表示します。</small>