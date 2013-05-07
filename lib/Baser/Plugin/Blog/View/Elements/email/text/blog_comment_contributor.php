<?php
/* SVN FILE: $Id$ */
/**
 * [EMAIL] メール送信
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

                                           <?php echo date('Y-m-d H:i:s') ?> 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　コメントが投稿されました　◇◆ 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

<?php echo $this->BlogComment['name'] ?>さんが、
「<?php echo $this->BlogPost['name'] ?>」にコメントしました。
<?php echo $this->BcBaser->getUri('/' . $blogContent['name'] . '/archives/' . $this->BlogPost['no'], false) ?>　
 
<?php echo ($this->BlogComment['message']) ?>　
　
　

