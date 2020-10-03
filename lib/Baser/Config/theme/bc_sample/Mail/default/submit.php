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
 * メールフォーム送信完了ページ
 * 呼出箇所：メールフォーム
 *
 * @var BcAppView $this
 * @var array $mailContent メールコンテンツデータ
 */
if (Configure::read('debug') == 0 && $mailContent['MailContent']['redirect_url']) {
	$this->Html->meta(['http-equiv' => 'Refresh'], null, ['content' => '5;url=' . $mailContent['MailContent']['redirect_url'], 'inline' => false]);
}
?>


<h2 class="bs-mail-title"><?php $this->BcBaser->contentsTitle() ?></h2>

<h3 class="bs-mail-title-sub"><?php echo __('メール送信完了') ?></h3>

<div class="bs-mail-form">
	<p><?php echo __('お問い合わせ頂きありがとうございました。')?>
	<?php echo __('確認次第、ご連絡させて頂きます。') ?></p>
<?php if (Configure::read('debug') == 0 && $mailContent['MailContent']['redirect_url']): ?>
	<p>※<?php echo __('%s 秒後にトップページへ自動的に移動します。', 5) ?></p>
	<p><a href="<?php echo $mailContent['MailContent']['redirect_url']; ?>"><?php echo __('移動しない場合はコチラをクリックしてください。') ?></a></p>
<?php endif; ?>
</div>
