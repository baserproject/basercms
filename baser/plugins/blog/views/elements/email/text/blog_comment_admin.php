<?php
/* SVN FILE: $Id$ */
/**
 * [EMAIL] メール送信
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
?>

                                           <?php echo date('Y-m-d H:i:s') ?> 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　コメントを受付けました　◇◆ 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

　<?php echo $blogContent['title'] ?> へのコメントを受け付けました。
　受信内容は下記のとおりです。

　「<?php echo $blogPost['name'] ?>」
　<?php echo $bcBaser->getUri('/' . $blogContent['name'] . '/archives/' . $blogPost['no'], false) ?>　

━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ コメント内容 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━

送信者名： <?php echo ($blogComment['name']) ?>　
Ｅメール： <?php echo ($blogComment['email']) ?>　
ＵＲＬ　： <?php echo ($blogComment['url']) ?>　

<?php echo ($blogComment['message']) ?>　

コメントの公開状態を変更する場合は次のURLよりご確認ください。
<?php echo $bcBaser->getUri('/admin/blog/blog_comments/index/' . $blogContent['id'], false) ?>　
　
　
