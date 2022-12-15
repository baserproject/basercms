<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * メールフォーム送信メール本体
 * 呼出箇所：メールフォーム（メール送信時）
 *
 * @var \BcMail\View\MailFrontEmailView $this
 * @var array $other その他データ
 * @var array $mailConfig メール設定データ
 */
?>

                                           <?php echo $other['date'] ?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　　　　　　　　　　◆◇　<?php echo __('受信完了メール') ?>　◇◆　
━━━━━━━━━━━━━━━━━━━━━━━━━━━

<?php if ($other['mode'] === 'user'): ?>
  <?php echo __('この度は、ご連絡ありがとうございます。') ?>　
  <?php echo __('送信内容は下記のようになっております。') ?>　
<?php elseif ($other['mode'] === 'admin'): ?>
　<?php echo $mailConfig->site_name ?> <?php echo __('へ連絡を受け付けました。') ?>　
　<?php echo __('受信内容は下記のとおりです。') ?>　
<?php endif; ?>
　
━━━━◇◆━━━━━━━━━━━━━━━━━━━━━
　◆ <?php echo __('送信内容') ?>　
━━━━━━━━━━━━━━━━━━━━━◆◇━━━━
<?php echo $this->element('../email/text/mail_data') ?>



───────────────────────────

<?php if ($other['mode'] === 'user'): ?>
  <?php echo __('なお、このメールは自動返信メールとなっております。') ?>　
  　<?php echo __('メールを確認させて頂き次第、早急にご連絡させていただきます。') ?>　
  　<?php echo __('恐れ入りますがしばらくお待ちください。') ?>　
<?php elseif ($other['mode'] === 'admin'): ?>
  <?php echo __('なお、このメールは自動転送システムです。') ?>　
  　<?php echo __('受け付けた旨のメールもユーザーへ送られています。') ?>　
<?php endif; ?>

━━━━━━━━━━━━━━━━━━━━━━━━━━━

　<?php echo $mailConfig->site_name; ?>　
　<?php echo $mailConfig->site_url ?>　<?php echo $mailConfig->site_email; ?>　
　<?php if ($mailConfig->site_tel): ?>TEL　<?php echo $mailConfig->site_tel; ?><?php endif; ?><?php if ($mailConfig->site_fax): ?>　FAX　<?php echo $mailConfig->site_fax; ?><?php endif; ?>　

━━━━━━━━━━━━━━━━━━━━━━━━━━━
