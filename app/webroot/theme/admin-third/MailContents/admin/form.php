<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package            Mail.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] メールコンテンツ フォーム
 */
$this->BcBaser->i18nScript([
	'confirmMessage1' => __d('baser', 'メールフォーム設定を保存して、レイアウトテンプレート %s の編集画面に移動します。よろしいですか？'),
	'confirmMessage2' => __d('baser', 'メールフォーム設定を保存して、メールフォームテンプレート %s の編集画面に移動します。よろしいですか？'),
	'confirmMessage3' => __d('baser', 'メールフォーム設定を保存して、送信メールテンプレート %s の編集画面に移動します。よろしいですか？')
]);
$this->BcBaser->js('Mail.admin/mail_contents/edit', false);
?>


<?php echo $this->BcForm->create('MailContent', ['novalidate' => true]) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcForm->input('MailContent.id', ['type' => 'hidden']) ?>

<section class="bca-section bca-section__mail-description">
	<label for="MailContentDescriptionTmp"
		   class="bca-form-table__label -label"><?php echo $this->BcForm->label('MailContent.description', __d('baser', 'メールフォーム説明文')) ?></label>
	<span class="bca-form-table__input-wrap">
		<?php
		echo $this->BcForm->ckeditor('MailContent.description', [
			'editorWidth' => 'auto',
			'editorHeight' => '120px',
			'editorToolType' => 'simple',
			'editorEnterBr' => @$siteConfig['editor_enter_br']
		])
		?>
		<?php echo $this->BcForm->error('MailContent.description') ?>
   </span>
</section>

<div class="section">
	<table class="form-table bca-form-table" data-bca-table-type="type2">
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.sender_1', __d('baser', '送信先メールアドレス')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php
				echo $this->BcForm->input('MailContent.sender_1_', [
					'type' => 'radio',
					'options' => ['0' => __d('baser', '管理者用メールアドレスに送信する'), '1' => __d('baser', '別のメールアドレスに送信する')],
					'legend' => false,
					'separator' => '<br>'])
				?><br/>
				<?php echo $this->BcForm->input('MailContent.sender_1', ['type' => 'text', 'size' => 40, 'class' => 'bca-textbox__input bca-mailContentSender1', 'placeholder' => __d('baser', 'メールアドレスを入力してください')]) ?>
				<?php echo $this->BcForm->error('MailContent.sender_1') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.sender_name', __d('baser', '送信先名')) ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailContent.sender_name', ['type' => 'text', 'size' => 80, 'maxlength' => 255, 'placeholder' => '送信先名を入力してください。']) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailContent.sender_name') ?>
				<div id="helptextSenderName" class="helptext"><?php echo __d('baser', '自動返信メールの送信者に表示します。入力がない場合、サイト名が設定されます。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.subject_user', __d('baser', '自動返信メール件名<br />[ユーザー宛]')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailContent.subject_user', ['type' => 'text', 'size' => 80, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailContent.subject_user') ?>
				<div id="helptextSubjectUser"
					 class="helptext"><?php
					 echo __d('baser', 'ユーザー宛の自動返信メールの件名に表示します。');
					 echo '<br>';
					 echo __d('baser', '{$フィールド名}といれると、フォームの入力内容を取り入れることができます');
					 ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.subject_admin', __d('baser', '自動送信メール件名<br />[管理者宛]')) ?>
				&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
			</th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailContent.subject_admin', ['type' => 'text', 'size' => 80, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailContent.subject_admin') ?>
				<div id="helptextSubjectAdmin"
					 class="helptext"><?php
					 echo __d('baser', '管理者宛の自動送信メールの件名に表示します。');
					 echo '<br>';
					 echo __d('baser', '{$フィールド名}といれると、フォームの入力内容を取り入れることができます');
					 ?> ?></div>
			</td>
		</tr>
		<tr>
			<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.redirect_url', __d('baser', 'リダイレクトURL')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailContent.redirect_url', ['type' => 'text', 'size' => 80, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailContent.redirect_url') ?>
				<div id="helptextRedirectUrl" class="helptext">
					<ul>
						<li><?php echo __d('baser', 'メール送信後、別のURLにリダイレクトする場合、ここにURLを指定します。') ?></li>
						<li><?php echo __d('baser', 'httpからの完全なURLを指定してください。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>


<div class="bca-section" data-bca-section-type='form-group'>
	<div class="bca-collapse__action">
		<button type="button" class="bca-collapse__btn" data-bca-collapse="collapse" data-bca-target="#formOptionBody"
				aria-expanded="false" aria-controls="formOptionBody"><?php echo __d('baser', '詳細設定') ?>&nbsp;&nbsp;<i
				class="bca-icon--chevron-down bca-collapse__btn-icon"></i></button>
	</div>
	<div class="bca-collapse" id="formOptionBody" data-bca-state="">
		<table class="form-table bca-form-table" data-bca-table-type="type2">
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.publish_begin', __d('baser', 'フォーム受付期間')) ?></th>
				<td class="col-input bca-form-table__input">
					&nbsp;&nbsp;
					<?php echo $this->BcForm->input('MailContent.publish_begin', [
						'type' => 'dateTimePicker',
						'size' => 12,
						'maxlength' => 10,
						'dateLabel' => ['text' => __d('baser', '開始日付')],
						'timeLabel' => ['text' => __d('baser', '開始時間')]
					]) ?>
					&nbsp;〜&nbsp;
					<?php echo $this->BcForm->input('MailContent.publish_end', [
						'type' => 'dateTimePicker',
						'size' => 12,
						'maxlength' => 10,
						'dateLabel' => ['text' => __d('baser', '終了日付')],
						'timeLabel' => ['text' => __d('baser', '終了時間')]
					]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<div class="helptext">
						<p><?php echo __d('baser', '公開期間とは別にフォームの受付期間を設定する事ができます。受付期間外にはエラーではなく受付期間外のページを表示します。') ?></p>
					</div>
					<?php echo $this->BcForm->error('MailContent.publish_begin') ?>
					<?php echo $this->BcForm->error('MailContent.publish_end') ?>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.save_info', __d('baser', 'データベース保存')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailContent.save_info', ['type' => 'radio', 'options' => [1 => __d('baser', '送信情報をデータベースに保存する'), 0 => __d('baser', '送信情報をデータベースに保存しない')]]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('MailContent.save_info') ?>
					<div id="saveInfo" class="helptext">
						<ul>
							<li><?php echo __d('baser', 'メールフォームから送信された情報をデータベースに保存するかどうかを指定できます。') ?></li>
							<li><?php echo __d('baser', 'メールフォームから送信された情報をデータベースに保存したくない場合は、保存しないを指定してください。') ?></li>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.auth_capthca', __d('baser', 'イメージ認証')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailContent.auth_captcha', ['type' => 'checkbox', 'label' => __d('baser', '利用する')]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('MailContent.auth_captcha') ?>
					<div id="helptextAuthCaptcha" class="helptext">
						<ul>
							<li><?php echo __d('baser', 'メールフォーム送信の際、表示された画像の文字入力させる事で認証を行ないます。') ?></li>
							<li><?php echo __d('baser', 'スパムなどいたずら送信が多いが多い場合に設定すると便利です。') ?></li>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.ssl_on', __d('baser', 'SSL通信')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailContent.ssl_on', ['type' => 'checkbox', 'label' => __d('baser', '利用する')]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<div id="helptextSslOn"
						 class="helptext"><?php echo __d('baser', '管理者ページでSSLを利用する場合は、事前にSSLの申込、設定が必要です。また、SSL通信で利用するURLをシステム設定で指定している必要があります。') ?></div>
					<?php echo $this->BcForm->error('MailContent.ssl_on', sprintf(__d('baser', 'SSL通信を利用するには、%s で、事前にSSL通信用のWebサイトURLを指定してください。'), $this->BcBaser->getLink(__d('baser', 'システム設定'), ['controller' => 'site_configs', 'action' => 'form', 'plugin' => null], ['target' => '_blank'])), ['escape' => false]) ?>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.sender_2', __d('baser', 'BCC用送信先メールアドレス')) ?></th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailContent.sender_2', ['type' => 'text', 'size' => 80, 'maxlength' => 255]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('MailContent.sender_2') ?>
					<div id="helptextSender2" class="helptext">
						<ul>
							<li><?php echo __d('baser', 'BCC（ブラインドカーボンコピー）用のメールアドレスを指定します。') ?></li>
							<li><?php echo __d('baser', '複数の送信先を指定するには、カンマで区切って入力します。') ?></li>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.widget_area', __d('baser', 'ウィジェットエリア')) ?>
					&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
				</th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailContent.widget_area', ['type' => 'select', 'options' => $this->BcForm->getControlsource('WidgetArea.id'), 'empty' => __d('baser', 'サイト基本設定に従う')]) ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('MailContent.widget_area') ?>
					<div id="helptextWidgetArea" class="helptext">
						<?php echo sprintf(__d('baser', 'メールコンテンツで利用するウィジェットエリアを指定します。<br>ウィジェットエリアは「%s」より追加できます。'), $this->BcBaser->getLink(__d('baser', 'ウィジェットエリア管理'), ['plugin' => null, 'controller' => 'widget_areas', 'action' => 'index'])) ?>
					</div>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.form_template', __d('baser', 'メールフォームテンプレート名')) ?>
					&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
				</th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailContent.form_template', ['type' => 'select', 'options' => $this->Mail->getFormTemplates($this->BcForm->value('Content.site_id'))]) ?>
					<?php echo $this->BcForm->input('MailContent.edit_mail_form', ['type' => 'hidden']) ?>
					<?php if ($this->action == 'admin_edit'): ?>
						<?php $this->BcBaser->link('<i class="bca-icon--edit"></i>' . __d('baser', '編集する'), 'javascript:void(0)', ['id' => 'EditForm']) ?>
					<?php endif ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('MailContent.form_template') ?>
					<div id="helptextFormTemplate" class="helptext">
						<ul>
							<li><?php echo __d('baser', 'メールフォーム本体のテンプレートを指定します。') ?></li>
							<li><?php echo __d('baser', '「編集する」からテンプレートの内容を編集する事ができます。') ?></li>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<th class="col-head bca-form-table__label"><?php echo $this->BcForm->label('MailContent.mail_template', __d('baser', '送信メールテンプレート名')) ?>
					&nbsp;<span class="bca-label" data-bca-label-type="required"><?php echo __d('baser', '必須') ?></span>
				</th>
				<td class="col-input bca-form-table__input">
					<?php echo $this->BcForm->input('MailContent.mail_template', ['type' => 'select', 'options' => $this->Mail->getMailTemplates($this->BcForm->value('Content.site_id'))]) ?>
					<?php echo $this->BcForm->input('MailContent.edit_mail', ['type' => 'hidden']) ?>
					<?php if ($this->action == 'admin_edit'): ?>
						<?php $this->BcBaser->link('<i class="bca-icon--edit"></i>' . __d('baser', '編集する'), 'javascript:void(0)', ['id' => 'EditMail']) ?>
					<?php endif ?>
					<i class="bca-icon--question-circle btn help bca-help"></i>
					<?php echo $this->BcForm->error('MailContent.mail_template') ?>
					<div id="helptextMailTemplate" class="helptext">
						<ul>
							<li><?php echo __d('baser', '送信するメールのテンプレートを指定します。') ?></li>
							<li><?php echo __d('baser', '「編集する」からテンプレートの内容を編集する事ができます。') ?></li>
						</ul>
					</div>
				</td>
			</tr>
			<?php echo $this->BcForm->dispatchAfterForm('option') ?>
		</table>
	</div>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="submit">
	<div class="bca-actions__main">
		<?php echo $this->BcForm->submit(__d('baser', '保存'), [
			'div' => false,
			'class' => 'button bca-btn bca-actions__item',
			'id' => 'BtnSave',
			'data-bca-btn-type' => 'save',
			'data-bca-btn-size' => 'lg',
			'data-bca-btn-width' => 'lg'
		]) ?>
	</div>
</div>

<?php echo $this->BcForm->end() ?>
