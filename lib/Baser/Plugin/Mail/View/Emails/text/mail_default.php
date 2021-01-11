<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [EMAIL] メール送信
 */
?>

                                           <?php echo $other['date'] ?>
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　◆◇　<?php echo __d('baser', 'お問い合わせを受け付けました')?>　◇◆
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

<?php if ($other['mode'] === 'user'): ?>
　<?php echo __d('baser', 'この度は、お問い合わせいただきありがとうございます。')?>　
　<?php echo __d('baser', '送信内容は下記のようになっております。')?>　
<?php elseif ($other['mode'] === 'admin'): ?>
　<?php echo sprintf(__d('baser', '%s へのお問い合わせを受け付けました。'), $mailConfig['site_name']) ?>　
　<?php echo __d('baser', '受信内容は下記のとおりです。')?>　
<?php endif; ?>

━━━━◇◆━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　◆ <?php echo __d('baser', 'お問い合わせ内容')?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
<?php echo $this->element('../Emails/text/mail_data') ?>



────────────────────────────────────

<?php if ($other['mode'] === 'user'): ?>
　<?php echo __d('baser', 'なお、このメールは自動返信メールとなっております。')?>　
　<?php echo __d('baser', 'メールを確認させて頂き次第、早急にご連絡させていただきます。')?>　
　<?php echo __d('baser', '恐れ入りますがしばらくお待ちください。')?>　
<?php elseif ($other['mode'] === 'admin'): ?>
　<?php echo __d('baser', 'なお、このメールは自動転送システムです。')?>　
　<?php echo __d('baser', '受け付けた旨のメールもユーザーへ送られています。')?>　
<?php endif; ?>

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

　<?php echo $mailConfig['site_name']; ?>　
　<?php echo $mailConfig['site_url'] ?>　<?php echo $mailConfig['site_email']; ?>　
　<?php if ($mailConfig['site_tel']): ?>TEL　<?php echo $mailConfig['site_tel']; ?><?php endif; ?><?php if ($mailConfig['site_fax']): ?>　FAX　<?php echo $mailConfig['site_fax']; ?><?php endif; ?>　

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
