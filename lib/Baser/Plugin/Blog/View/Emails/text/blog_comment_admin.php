<?php
/**
 * [EMAIL] メール送信
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Blog.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>

                                           <?php echo date('Y-m-d H:i:s') ?> 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　コメントを受付けました　◇◆ 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

　<?php echo $BlogContent['title'] ?> へのコメントを受け付けました。
　受信内容は下記のとおりです。

　「<?php echo $BlogPost['name'] ?>」
　<?php echo $this->BcBaser->getUri('/' . $BlogContent['name'] . '/archives/' . $BlogPost['no'], false) ?>　

━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ コメント内容 
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━

送信者名： <?php echo ($BlogComment['name']) ?>　
Ｅメール： <?php echo ($BlogComment['email']) ?>　
ＵＲＬ　： <?php echo ($BlogComment['url']) ?>　

<?php echo ($BlogComment['message']) ?>　

コメントの公開状態を変更する場合は次のURLよりご確認ください。
<?php echo $this->BcBaser->getUri('/admin/blog/blog_comments/index/' . $BlogContent['id'], false) ?>　
　
　
