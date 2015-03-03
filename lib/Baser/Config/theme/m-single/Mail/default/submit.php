<?php
/**
 * [PUBLISH] メールフォーム送信完了ページ
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
$this->BcBaser->css('Mail.style', array('inline' => true));
if (Configure::read('debug') == 0 && $mailContent['MailContent']['redirect_url']) {
	$this->Html->meta(array('http-equiv' => 'Refresh'), null, array('content' => '5;url=' . $mailContent['MailContent']['redirect_url'], 'inline' => false));
}
?>

<div class="articleArea bgGray" id="contact">
<article class="mainWidth">
<h2 class="fontawesome-circle-arrow-down"><?php $this->BcBaser->contentsTitle() ?></h2>
<section>
<h3>メール送信完了</h3>
	<p>お問い合わせ頂きありがとうございました。<br />
		確認次第、ご連絡させて頂きます。</p>
<?php if (Configure::read('debug') == 0 && $mailContent['MailContent']['redirect_url']): ?>
	<p>※５秒後にトップページへ自動的に移動します。</p>
	<p><a href="<?php echo $mailContent['MailContent']['redirect_url']; ?>">移動しない場合はコチラをクリックしてください。≫</a></p>
<?php endif; ?>
</section>

</article>
</div><!-- /articleArea -->

