<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright        Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Baser.View
 * @since            baserCMS v 4.4.0
 * @license            https://basercms.net/license/index.html
 */

/**
 * ブログコメント送信メール（コメント送信者）
 * 呼出箇所：ブログコメント（コメント送信時）
 *
 * @var BcAppView $this
 * @var array $Content コンテンツデータ
 * @var array $BlogComment ブログコメントデータ
 * @var array $BlogPost ブログ記事データ
 * @var array $BlogContent ブログコンテンツデータ
 */
?>

                                           <?php echo date('Y-m-d H:i:s') ?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　<?php echo __d('baser', 'コメントが投稿されました')?>　◇◆
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

<?php echo sprintf(__d('baser', '%sさんが、「%s」にコメントしました。'), $BlogComment['name'], $BlogPost['name'])?>　
<?php echo $this->BcBaser->getUri($Content['url'] . 'archives/' . $BlogPost['no'], false) ?>　

<?php echo ($BlogComment['message']) ?>　
　
　
　
　
　

