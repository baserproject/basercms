<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since            baserCMS v 4.4.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * ブログコメント送信メール（管理者）
 * 呼出箇所：ブログコメント（コメント送信時）
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogPost $blogPost ブログ記事データ
 * @var \BcBlog\Model\Entity\BlogComment $blogComment
 * @var string $contentTitle
 * @var string $postUrl
 * @var string $adminUrl
 */
?>

                                           <?php echo date('Y-m-d H:i:s') ?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　<?php echo __d('baser_core', 'コメントを受付けました')?>　◇◆
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

　<?php echo __d('baser_core', '{0} へのコメントを受け付けました。', $contentTitle)?>　
　<?php echo __d('baser_core', '受信内容は下記のとおりです。')?>　

　「<?php echo $blogPost->title ?>」　
　<?php echo $postUrl ?>　

━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ <?php echo __d('baser_core', 'コメント内容 ')?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━

<?php echo __d('baser_core', '送信者名')?>： <?php echo ($blogComment->name) ?>　
<?php echo __d('baser_core', 'Ｅメール')?>： <?php echo ($blogComment->email) ?>　
ＵＲＬ　： <?php echo ($blogComment->url) ?>　

<?php echo ($blogComment->message) ?>　

<?php echo __d('baser_core', 'コメントの公開状態を変更する場合は次のURLよりご確認ください。')?>　
<?php echo $adminUrl ?>　
　
　
　
