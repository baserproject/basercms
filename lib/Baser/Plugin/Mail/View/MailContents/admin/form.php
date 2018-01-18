<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] メールコンテンツ フォーム
 */
$this->BcBaser->js('Mail.admin/mail_contents/edit', false);
?>


<h2>基本項目</h2>

<?php echo $this->BcForm->create('MailContent', array('novalidate' => true)) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcForm->input('MailContent.id', array('type' => 'hidden')) ?>

<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.description', 'メールフォーム説明文') ?></th>
			<td class="col-input">
				<?php
				echo $this->BcForm->ckeditor('MailContent.description', array(
					'editorWidth' => 'auto',
					'editorHeight' => '120px',
					'editorToolType' => 'simple',
					'editorEnterBr' => @$siteConfig['editor_enter_br']
				))
				?>
<?php echo $this->BcForm->error('MailContent.description') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.sender_1', '送信先メールアドレス') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php
				echo $this->BcForm->input('MailContent.sender_1_', array(
					'type' => 'radio',
					'options' => array('0' => '管理者用メールアドレスに送信する', '1' => '別のメールアドレスに送信する'),
					'legend' => false,
					'separator' => '<br />'))
				?><br />
<?php echo $this->BcForm->input('MailContent.sender_1', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $this->BcForm->error('MailContent.sender_1') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.sender_name', '送信先名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
<?php echo $this->BcForm->input('MailContent.sender_name', array('type' => 'text', 'size' => 80, 'maxlength' => 255)) ?>
<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSenderName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('MailContent.sender_name') ?>
				<div id="helptextSenderName" class="helptext">自動返信メールの送信者に表示します。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.subject_user', '自動返信メール件名<br />[ユーザー宛]') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
<?php echo $this->BcForm->input('MailContent.subject_user', array('type' => 'text', 'size' => 80)) ?>
<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSubjectUser', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('MailContent.subject_user') ?>
				<div id="helptextSubjectUser" class="helptext">ユーザー宛の自動返信メールの件名に表示します。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.subject_admin', '自動送信メール件名<br />[管理者宛]') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
<?php echo $this->BcForm->input('MailContent.subject_admin', array('type' => 'text', 'size' => 80)) ?>
<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSubjectAdmin', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('MailContent.subject_admin') ?>
				<div id="helptextSubjectAdmin" class="helptext">管理者宛の自動送信メールの件名に表示します。</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.redirect_url', 'リダイレクトURL') ?></th>
			<td class="col-input">
<?php echo $this->BcForm->input('MailContent.redirect_url', array('type' => 'text', 'size' => 80, 'maxlength' => 255)) ?>
<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpRedirectUrl', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('MailContent.redirect_url') ?>
				<div id="helptextRedirectUrl" class="helptext">
					<ul>
						<li>メール送信後、別のURLにリダイレクトする場合、ここにURLを指定します。</li>
						<li>httpからの完全なURLを指定してください。</li>
					</ul>
				</div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>	
</div>

<h2 class="btn-slide-form"><a href="javascript:void(0)" id="formOption">オプション</a></h2>
<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table slide-body" id="formOptionBody">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.publish_begin', 'フォーム受付期間') ?></th>
			<td class="col-input">
				&nbsp;&nbsp;
				<?php echo $this->BcForm->dateTimePicker('MailContent.publish_begin', array('size' => 12, 'maxlength' => 10), true) ?>
				&nbsp;〜&nbsp;
				<?php echo $this->BcForm->dateTimePicker('MailContent.publish_end', array('size' => 12, 'maxlength' => 10), true) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div class="helptext">
					<p>公開期間とは別にフォームの受付期間を設定する事ができます。受付期間外にはエラーではなく受付期間外のページを表示します。</p>
				</div>
				<?php echo $this->BcForm->error('MailContent.publish_begin') ?>
				<?php echo $this->BcForm->error('MailContent.publish_end') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.save_info', 'データベース保存') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('MailContent.save_info', array('type' => 'radio', 'options' => array(1 => '送信情報をデータベースに保存する', 0 => '送信情報をデータベースに保存しない'))) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'saveInfo', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('MailContent.save_info') ?>
				<div id="saveInfo" class="helptext">
					<ul>
						<li>メールフォームから送信された情報をデータベースに保存するかどうかを指定できます。</li>
						<li>メールフォームから送信された情報をデータベースに保存したくない場合は、保存しないを指定してください。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.auth_capthca', 'イメージ認証') ?></th>
			<td class="col-input">
<?php echo $this->BcForm->input('MailContent.auth_captcha', array('type' => 'checkbox', 'label' => '利用する')) ?>
<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpAuthCaptcha', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('MailContent.auth_captcha') ?>
				<div id="helptextAuthCaptcha" class="helptext">
					<ul>
						<li>メールフォーム送信の際、表示された画像の文字入力させる事で認証を行ないます。</li>
						<li>スパムなどいたずら送信が多いが多い場合に設定すると便利です。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.ssl_on', 'SSL通信') ?></th>
			<td class="col-input">
<?php echo $this->BcForm->input('MailContent.ssl_on', array('type' => 'checkbox', 'label' => '利用する')) ?>
<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSslOn', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('MailContent.ssl_on', 'SSL通信を利用するには、' . $this->BcBaser->getLink('システム設定', array('controller' => 'site_configs', 'action' => 'form', 'plugin' => null), array('target' => '_blank')) . 'で、
						事前にSSL通信用のWebサイトURLを指定してください。', array('escape' => false))
?>
				<div id="helptextSslOn" class="helptext">
					管理者ページでSSLを利用する場合は、事前にSSLの申込、設定が必要です。また、SSL通信で利用するURLをシステム設定で指定している必要があります。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.sender_2', 'BCC用送信先メールアドレス') ?></th>
			<td class="col-input">
<?php echo $this->BcForm->input('MailContent.sender_2', array('type' => 'text', 'size' => 80, 'maxlength' => 255)) ?>
<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpSender2', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('MailContent.sender_2') ?>
				<div id="helptextSender2" class="helptext">
					<ul><li>BCC（ブラインドカーボンコピー）用のメールアドレスを指定します。</li>
						<li>複数の送信先を指定するには、カンマで区切って入力します。</li></ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.widget_area', 'ウィジェットエリア') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
<?php echo $this->BcForm->input('MailContent.widget_area', array('type' => 'select', 'options' => $this->BcForm->getControlsource('WidgetArea.id'), 'empty' => 'サイト基本設定に従う')) ?>
<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpWidgetArea', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('MailContent.widget_area') ?>
				<div id="helptextWidgetArea" class="helptext">
					メールコンテンツで利用するウィジェットエリアを指定します。<br />
					ウィジェットエリアは「<?php $this->BcBaser->link('ウィジェットエリア管理', array('plugin' => null, 'controller' => 'widget_areas', 'action' => 'index')) ?>」より追加できます。
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.form_template', 'メールフォームテンプレート名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('MailContent.form_template', array('type' => 'select', 'options' => $this->Mail->getFormTemplates($this->BcForm->value('Content.site_id')))) ?>
				<?php echo $this->BcForm->input('MailContent.edit_mail_form', array('type' => 'hidden')) ?>
<?php if ($this->action == 'admin_edit'): ?>
	&nbsp;<?php $this->BcBaser->link('編集する', 'javascript:void(0)', array('id' => 'EditForm', 'class' => 'button-small')) ?>&nbsp;
<?php endif ?>
<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpFormTemplate', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('MailContent.form_template') ?>
				<div id="helptextFormTemplate" class="helptext">
					<ul>
						<li>メールフォーム本体のテンプレートを指定します。</li>
						<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('MailContent.mail_template', '送信メールテンプレート名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('MailContent.mail_template', array('type' => 'select', 'options' => $this->Mail->getMailTemplates($this->BcForm->value('Content.site_id')))) ?>
				<?php echo $this->BcForm->input('MailContent.edit_mail', array('type' => 'hidden')) ?>
<?php if ($this->action == 'admin_edit'): ?>
	&nbsp;<?php $this->BcBaser->link('編集する', 'javascript:void(0)', array('id' => 'EditMail', 'class' => 'button-small')) ?>&nbsp;
<?php endif ?>
<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpMailTemplate', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('MailContent.mail_template') ?>
				<div id="helptextMailTemplate" class="helptext">
					<ul>
						<li>送信するメールのテンプレートを指定します。</li>
						<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
					</ul>
				</div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm('option') ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<!-- button -->
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
</div>

<?php echo $this->BcForm->end() ?>
