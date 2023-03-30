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
 * ブログコメント送信メール（コメント送信者）
 * 呼出箇所：ブログコメント（コメント送信時）
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var \BcBlog\Model\Entity\BlogPost $blogPost ブログ記事データ
 * @var \BcBlog\Model\Entity\BlogComment $blogComment
 * @var string $postUrl
 */
?>

                                           <?php echo date('Y-m-d H:i:s') ?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　<?php echo __d('baser_core', 'コメントが投稿されました')?>　◇◆
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

<?php echo __d('baser_core', '{0} さんが、「{1}」にコメントしました。', $blogComment->name, $blogPost->title) ?>　
<?php echo $postUrl ?>　

<?php echo ($blogComment->message) ?>　
　
　
　
　
　

