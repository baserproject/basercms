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
 * メールフォーム
 *
 * $this->Mailform->create() にて、valueSources オプションで、context を明示的に指定する。
 * ファイルフィールドの確認画面にて、$_POST からの配列のデータを取得し Warning となってしまうため。
 * BcFileUploader により、エンティティは変換されるが、$_POST は変換されない。
 *
 * @var \BcMail\View\MailFrontAppView $this
 * @var bool $freezed 確認画面かどうか
 * @var array $mailContent メールコンテンツデータ
 * @var \BcMail\Model\Entity\MailMessage $mailMessage
 * @checked
 * @noTodo
 * @unitTest
 */
// ブラウザのヒストリーバック（戻るボタン）対応
$this->Mail->token();
$this->BcBaser->js('BcMail.form-submit', true, ['defer'])
?>


<?php if (!$freezed): ?>
	<?php echo $this->Mailform->create($mailMessage, ['url' => $this->BcBaser->getContentsUrl(null, false, null, false) . 'confirm', 'type' => 'file', 'valueSources' => ['context']]) ?>
<?php else: ?>
	<?php echo $this->Mailform->create($mailMessage, ['url' => $this->BcBaser->getContentsUrl(null, false, null, false)  . 'submit', 'valueSources' => ['context']]) ?>
<?php endif; ?>

<?php $this->Mailform->unlockField('mode') ?>
<?php echo $this->Mailform->hidden('mode', ['id' => 'MailMessageMode']) ?>

<table class="bs-mail-form-body">
	<?php $this->BcBaser->element('mail_input', ['blockStart' => 1]) ?>
</table>

<div class="bs-mail-form-submit">
	<?php if ($freezed): ?>
		<?php echo $this->Mailform->submit('　' . __('書き直す') . '　', ['div' => false, 'class' => 'form-submit', 'id' => 'BtnMessageBack']) ?>
		<?php echo $this->Mailform->submit('　' . __('送信する') . '　', ['div' => false, 'class' => 'form-submit', 'id' => 'BtnMessageSubmit']) ?>
	<?php else: ?>
		<input name="resetdata" value="　取り消す　" type="reset" />
		<?php echo $this->Mailform->submit('　' . __('入力内容を確認する') . '　', ['div' => false, 'class' => 'form-submit', 'id' => 'BtnMessageConfirm']) ?>
	<?php endif; ?>
</div>

<?php echo $this->Mailform->end() ?>
