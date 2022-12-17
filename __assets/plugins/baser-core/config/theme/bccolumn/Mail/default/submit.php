<?php
/**
 * メールフォーム送信完了ページ
 */
if (Configure::read('debug') == 0 && $mailContent['MailContent']['redirect_url']) {
	$this->Html->meta(array('http-equiv' => 'Refresh'), null, array('content' => '5;url=' . $mailContent['MailContent']['redirect_url'], 'inline' => false));
}
?>


<div class="contact-form">

	<h2><?php $this->BcBaser->contentsTitle() ?></h2>
	<h3 ><?php echo __('メール送信完了') ?></h3>
	<div class="section">
		<p><?php echo __('お問い合わせ頂きありがとうございました。')?> 
		<?php echo __('確認次第、ご連絡させて頂きます。') ?></p>
		<?php if ($mailContent['MailContent']['redirect_url']): ?>
			<p>※<?php echo __('%s 秒後にトップページへ自動的に移動します。', 5) ?></p>
			<p> <a href="<?php echo $mailContent['MailContent']['redirect_url'] ?>"><?php echo __('移動しない場合はコチラをクリックしてください。') ?>≫</a> </p>
		<?php endif; ?>
	</div>
	
</div>