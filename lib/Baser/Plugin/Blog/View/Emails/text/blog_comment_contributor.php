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
?>

                                           <?php echo date('Y-m-d H:i:s') ?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　<?php echo __d('baser', 'コメントが投稿されました')?>　◇◆
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

<?php echo sprintf(__d('baser', '%sさんが、「%s」にコメントしました。'), $BlogComment['name'], $BlogPost['name'])?>　
<?php echo $this->BcBaser->getUri($Content['url'] . 'archives/' . $BlogPost['no'], false) ?>　

<?php echo ($BlogComment['message']) ?>　
　
　

