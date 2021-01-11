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
 * [PUBLISH] メールフォーム送信完了ページ
 */
if (Configure::read('debug') == 0 && $mailContent['MailContent']['redirect_url']) {
	$this->Html->meta(['http-equiv' => 'Refresh'], null, ['content' => '5;url=' . $mailContent['MailContent']['redirect_url'], 'inline' => false]);
}
?>


<h1 class="contents-head">
	<?php $this->BcBaser->contentsTitle() ?>
</h1>

<h2 class="contents-head"><?php echo __('メール送信完了') ?></h2>

<div class="section">
	<p><?php echo __('お問い合わせ頂きありがとうございました。<br>確認次第、ご連絡させて頂きます。') ?></p>
	<?php if (Configure::read('debug') == 0 && $mailContent['MailContent']['redirect_url']): ?>
		<p>※<?php echo __('%s 秒後にトップページへ自動的に移動します。', 5) ?></p>
		<p>
			<a href="<?php echo $mailContent['MailContent']['redirect_url'] ?>"><?php echo __('移動しない場合はこちらをクリックしてください。') ?>
				≫</a></p>
	<?php endif; ?>
</div>
