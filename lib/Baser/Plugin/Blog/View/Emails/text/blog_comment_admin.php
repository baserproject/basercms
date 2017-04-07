<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [EMAIL] メール送信
 */
$adminPrefix = BcUtil::getAdminPrefix();
?>

                                           <?php echo date('Y-m-d H:i:s') ?> 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　コメントを受付けました　◇◆ 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

　<?php echo $Content['title'] ?> へのコメントを受け付けました。
　受信内容は下記のとおりです。

　「<?php echo $BlogPost['name'] ?>」
　<?php echo $this->BcBaser->getUri($Content['url'] . '/archives/' . $BlogPost['no'], false) ?>　

━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ コメント内容 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━

送信者名： <?php echo ($BlogComment['name']) ?>　
Ｅメール： <?php echo ($BlogComment['email']) ?>　
ＵＲＬ　： <?php echo ($BlogComment['url']) ?>　

<?php echo ($BlogComment['message']) ?>　

コメントの公開状態を変更する場合は次のURLよりご確認ください。
<?php echo $this->BcBaser->getUri('/' . $adminPrefix . '/blog/blog_comments/index/' . $BlogContent['id'], false) ?>　
　
　
