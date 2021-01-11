<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [EMAIL] メール送信
 */
$adminPrefix = BcUtil::getAdminPrefix();
?>

                                           <?php echo date('Y-m-d H:i:s') ?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　<?php echo __d('baser', 'コメントを受付けました')?>　◇◆
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

　<?php echo sprintf(__d('baser', '%sへのコメントを受け付けました。'), $Content['title'])?>　
　<?php echo __d('baser', '受信内容は下記のとおりです。')?>　

　「<?php echo $BlogPost['name'] ?>」　
　<?php echo $this->BcBaser->getUri($Content['url'] . '/archives/' . $BlogPost['no'], false) ?>　

━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ <?php echo __d('baser', 'コメント内容 ')?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━

<?php echo __d('baser', '送信者名')?>： <?php echo ($BlogComment['name']) ?>　
<?php echo __d('baser', 'Ｅメール')?>： <?php echo ($BlogComment['email']) ?>　
ＵＲＬ　： <?php echo ($BlogComment['url']) ?>　

<?php echo ($BlogComment['message']) ?>　

<?php echo __d('baser', 'コメントの公開状態を変更する場合は次のURLよりご確認ください。')?>　
<?php echo $this->BcBaser->getUri('/' . $adminPrefix . '/blog/blog_comments/index/' . $BlogContent['id'], false) ?>　
　
　
