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
 * メールフォーム確認ページ
 *
 * 呼出箇所：メールフォーム
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var bool $freezed 確認画面かどうか
 */
if ($freezed) {
	$this->BcBaser->freezeMailForm();
}
$this->BcBaser->setTableToUpload('BcMail.MailMessages');
?>


<h2 class="bs-mail-title"><?php $this->BcBaser->contentsTitle() ?></h2>

<?php if ($freezed): ?>
<h3 class="bs-mail-title-sub"><?php echo __d('baser_core', '入力内容の確認') ?></h3>
<p><?php echo __d('baser_core', '入力した内容に間違いがなければ「送信する」ボタンをクリックしてください。') ?></p>
<?php else: ?>
<h3 class="bs-mail-title-sub"><?php echo __d('baser_core', '入力フォーム') ?></h3>
<?php endif ?>

<div class="bs-mail-form">
	<?php $this->BcBaser->flash() ?>
	<!-- /Elements/mail_form.php -->
	<?php $this->BcBaser->element('mail_form') ?>
</div>
