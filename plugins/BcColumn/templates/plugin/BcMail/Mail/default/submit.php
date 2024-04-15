<?php
/**
 * メールフォーム送信完了ページ
 */
if (\Cake\Core\Configure::read('debug') == 0 && $mailContent->redirect_url) {
	$this->Html->meta(array('http-equiv' => 'Refresh'), null, array('content' => '5;url=' . $mailContent->redirect_url, 'inline' => false));
}
?>


<div class="contact-form">

	<h2><?php $this->BcBaser->contentsTitle() ?></h2>
	<h3 ><?php echo __d('baser_core', 'メール送信完了') ?></h3>
	<div class="section">
		<p><?php echo __d('baser_core', 'お問い合わせ頂きありがとうございました。')?>
		<?php echo __d('baser_core', '確認次第、ご連絡させて頂きます。') ?></p>
		<?php if ($mailContent->redirect_url): ?>
			<p>※<?php echo __d('baser_core', '{0} 秒後にトップページへ自動的に移動します。', 5) ?></p>
			<p> <a href="<?php echo $mailContent->redirect_url ?>"><?php echo __d('baser_core', '移動しない場合はコチラをクリックしてください。') ?>≫</a> </p>
		<?php endif; ?>
	</div>

</div>
