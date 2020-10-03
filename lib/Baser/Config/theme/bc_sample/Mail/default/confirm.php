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
 * メールフォーム確認ページ
 * 呼出箇所：メールフォーム
 *
 * @var BcAppView $this
 * @var bool $freezed 確認画面かどうか
 */
if ($freezed) {
	$this->Mailform->freeze();
}
?>


<h2 class="bs-mail-title"><?php $this->BcBaser->contentsTitle() ?></h2>

<?php if ($freezed): ?>
<h3 class="bs-mail-title-sub"><?php echo __('入力内容の確認') ?></h3>
<p><?php echo __('入力した内容に間違いがなければ「送信する」ボタンをクリックしてください。') ?></p>
<?php else: ?>
<h3 class="bs-mail-title-sub"><?php echo __('入力フォーム') ?></h3>
<?php endif ?>

<div class="bs-mail-form">
	<?php $this->BcBaser->flash() ?>
	<!-- /Elements/mail_form.php -->
	<?php $this->BcBaser->element('mail_form') ?>
</div>
