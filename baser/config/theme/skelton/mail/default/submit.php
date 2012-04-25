<?php
/**
 * メールフォーム送信完了ページ
 */
if(Configure::read('debug')==0){
	/* プラグインの為か、inlineが動作しない */
	//$html->meta(array('http-equiv'=>'Refresh'),null,array('content'=>'5;url='.$mailContent['MailContent']['redirect_url']),false);
	$this->addScript($html->meta(array('http-equiv'=>'Refresh'),null,array('content'=>'5;url='.$mailContent['MailContent']['redirect_url'])));
}
?>

<h2 class="contents-head">
	<?php $bcBaser->contentsTitle() ?>
</h2>
<h3 class="contents-head">メール送信完了</h3>
<div class="section">
	<p>お問い合わせ頂きありがとうございました。<br />
		確認次第、ご連絡させて頂きます。</p>
	<?php if($mailContent['MailContent']['redirect_url']): ?>
	<p>※５秒後にトップページへ自動的に移動します。</p>
	<p> <a href="<?php echo $mailContent['MailContent']['redirect_url'] ?>">移動しない場合はコチラをクリックしてください。≫</a> </p>
	<?php endif; ?>
</div>
