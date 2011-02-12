<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] メールコンテンツ フォーム
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.mail.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<script type="text/javascript">
$(function(){
	$('input[name="data[MailContent][sender_1_]"]').click(mailContentSender1ClickHandler);
	$("#MailContentSender1").hide();

	if($('input[name="data[MailContent][sender_1_]"]:checked').val()===undefined){
		if($("#MailContentSender1").val()!=''){
			$("#MailContentSender11").attr('checked',true);
		}else{
			$("#MailContentSender10").attr('checked',true);
		}
	}
	$("#EditLayout").click(function(){
		if(confirm('メールフォーム設定を保存して、レイアウトテンプレート '+$("#MailContentLayoutTemplate").val()+' の編集画面に移動します。よろしいですか？')){
			$("#MailContentEditLayout").val(1);
			$("#MailContentEditMailForm").val('');
			$("#MailContentEditMail").val('');
			$("#MailContentEditForm").submit();
		}
	});
	$("#EditForm").click(function(){
		if(confirm('メールフォーム設定を保存して、メールフォームテンプレート '+$("#MailContentFormTemplate").val()+' の編集画面に移動します。よろしいですか？')){
			$("#MailContentEditLayout").val('');
			$("#MailContentEditMailForm").val(1);
			$("#MailContentEditMail").val('');
			$("#MailContentEditForm").submit();
		}
	});
	$("#EditMail").click(function(){
		if(confirm('メールフォーム設定を保存して、送信メールテンプレート '+$("#MailContentMailTemplate").val()+' の編集画面に移動します。よろしいですか？')){
<<<<<<< HEAD
			$("#MailContentEditLayout").val('');
			$("#MailContentEditMailForm").val('');
			$("#MailContentEditMail").val(1);
=======
			$("#MailContentEditLayout").val(false);
			$("#MailContentEditMailForm").val(false);
			$("#MailContentEditMail").val(true);
>>>>>>> Trunkでの変更をマージ 929-933
			$("#MailContentEditForm").submit();
		}
	});
	mailContentSender1ClickHandler();
});

function mailContentSender1ClickHandler(){
	if($('input[name="data[MailContent][sender_1_]"]:checked').val()=='1'){
		$("#MailContentSender1").slideDown(100);
	}else{
		$("#MailContentSender1").slideUp(100);
	}
}
</script>

<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>メールフォームコンテンツの基本設定を登録します。<br />
		各項目のヘルプメッセージを確認し登録を完了させてください。<br />
		メールフォームごとにデザインを変更する事もできます。その場合、画面下の「オプション」をクリックし、テンプレート名を変更します。<br />
		<small>※ テンプレート名を変更した場合は、新しい名称のテンプレートを作成しアップロードする必要があります。</small><br />
		<a href="http://basercms.net/manuals/designers/6.html" target="_blank">≫ メールフォームのテンプレートを変更する</a></p>
</div>

<?php if($this->action == 'admin_edit'): ?>
<p><strong>このメールフォームのURL：<?php $baser->link($baser->getUri('/'.$mailContent['MailContent']['name'].'/index'),'/'.$mailContent['MailContent']['name'].'/index',array('target'=>'_blank')) ?></strong></p>
<?php endif ?>

<h3>基本項目</h3>
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $formEx->create('MailContent') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('MailContent.id', 'NO') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp; </td>
	</tr>
	<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.name', 'メールフォームアカウント名') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.name', array('size'=>40,'maxlength'=>255)) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('MailContent.name') ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li>メールフォームのURLに利用します。<br />
						(例)メールフォームIDが test の場合・・・http://[BaserCMS設置URL]/test/index</li>
					<li>半角英数字、ハイフン、アンダースコアで入力して下さい。</li>
				</ul>
			</div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.title', 'メールフォームタイトル') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.title', array('size'=>40,'maxlength'=>255)) ?><?php echo $formEx->error('MailContent.title') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.sender_1', '送信先メールアドレス') ?></th>
		<td class="col-input"><?php echo $formEx->radio('MailContent.sender_1_',array('0'=>'管理者用メールアドレスに送信する','1'=>'別のメールアドレスに送信する'),array('legend'=>false,'separator'=>'<br />')) ?><br />
			<?php echo $formEx->text('MailContent.sender_1', array('size'=>40,'maxlength'=>255)) ?> <?php echo $formEx->error('MailContent.sender_1') ?>&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.sender_name', '送信先名') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.sender_name', array('size'=>40,'maxlength'=>255)) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpSenderName','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('MailContent.sender_name') ?>
			<div id="helptextSenderName" class="helptext">自動返信メールの送信者に表示します。</div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.subject_user', '自動返信メール<br />件名<br />[ユーザー宛]') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('MailContent.subject_user', array('cols'=>35,'rows'=>2)) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpSubjectUser','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('MailContent.subject_user') ?>
			<div id="helptextSubjectUser" class="helptext">ユーザー宛の自動返信メールの件名に表示します。</div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.subject_admin', '自動送信メール<br />件名<br />[管理者宛]') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('MailContent.subject_admin', array('cols'=>35,'rows'=>2)) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpSubjectAdmin','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('MailContent.subject_admin') ?>
			<div id="helptextSubjectAdmin" class="helptext">管理者宛の自動送信メールの件名に表示します。</div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('MailContent.redirect_url', 'リダイレクトURL') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.redirect_url', array('size'=>40,'maxlength'=>255)) ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpRedirectUrl','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('MailContent.redirect_url') ?>
			<div id="helptextRedirectUrl" class="helptext">
				<ul>
					<li>メール送信後、別のURLにリダイレクトする場合、ここにURLを指定します。</li>
					<li>httpからの完全なURLを指定して下さい。</li>
				</ul>
			</div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.auth_capthca', 'イメージ認証') ?></th>
		<td class="col-input">
			<?php echo $formEx->radio('MailContent.auth_captcha',$textEx->booleanDoList('利用'),array('legend'=>false)) ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAuthCaptcha','class'=>'help','alt'=>'ヘルプ')) ?>
			<?php echo $formEx->error('MailContent.auth_captcha') ?>
			<div id="helptextAuthCaptcha" class="helptext">
				<ul>
					<li>メールフォーム送信の際、表示された画像の文字入力させる事で認証を行ないます。</li>
					<li>スパムなどいたずら送信が多いが多い場合に設定すると便利です。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.ssl_on', 'SSL通信') ?></th>
		<td class="col-input">
			<?php echo $formEx->radio('MailContent.ssl_on',$textEx->booleanDoList('利用'),array('legend'=>false)) ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpSslOn','class'=>'help','alt'=>'ヘルプ')) ?>
			<?php echo $formEx->error('MailContent.ssl_on', 'SSL通信を利用するには、'.$baser->getLink('システム設定', array('controller'=>'site_configs', 'action'=>'form', 'plugin'=>null), array('target'=>'_blank')).'で、事前にSSL通信用のWebサイトURLを指定してください。', array('escape'=>false)) ?>
			<div id="helptextSslOn" class="helptext">
				管理者ページでSSLを利用する場合は、事前にSSLの申込、設定が必要です。また、SSL通信で利用するURLをシステム設定で指定している必要があります。
			</div>
		</td>
	</tr>
</table>
<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
	<tr>
		<th class="col-head"><?php echo $formEx->label('MailContent.sender_2', 'CC用送信先メールアドレス') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.sender_2', array('size'=>40,'maxlength'=>255)) ?><?php echo $formEx->error('MailContent.sender_2') ?>&nbsp; <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpSender2','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextSender2" class="helptext">
				<ul><li>CC（カーボンコピー）用のメールアドレスを指定します。</li>
					<li>複数の送信先を指定するには、カンマで区切って入力します。</li></ul>
			</div></td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.widget_area', 'ウィジェットエリア') ?></th>
		<td class="col-input">
			<?php echo $formEx->select('MailContent.widget_area', $formEx->getControlsource('WidgetArea.id') ,null,array(),'サイト基本設定に従う') ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpWidgetArea','class'=>'help','alt'=>'ヘルプ')) ?>
			<?php echo $formEx->error('MailContent.widget_area') ?>
			<div id="helptextWidgetArea" class="helptext">
				メールコンテンツで利用するウィジェットエリアを指定します。<br />
				ウィジェットエリアは「<?php $baser->link('ウィジェットエリア管理',array('plugin'=>null,'controller'=>'widget_areas','action'=>'index')) ?>」より追加できます。
			</div>
		</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.layout_template', 'レイアウトテンプレート名') ?></th>
		<td class="col-input">
			<?php echo $formEx->select('MailContent.layout_template',$mail->getLayoutTemplates(),null,array(),false) ?>
			<?php echo $formEx->hidden('MailContent.edit_layout') ?>
			<?php if($this->action == 'admin_edit'): ?>
			<?php $baser->link('≫ 編集する','javascript:void(0)',array('id'=>'EditLayout')) ?>
			<?php endif ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpLayoutTemplate','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('MailContent.layout_template') ?>
			<div id="helptextLayoutTemplate" class="helptext">
				<ul>
					<li>メールフォームの外枠のテンプレートを指定します。</li>
					<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
				</ul>
			</div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.form_template', 'メールフォームテンプレート名') ?></th>
		<td class="col-input">
			<?php echo $formEx->select('MailContent.form_template',$mail->getFormTemplates(),null,array(),false) ?>
			<?php echo $formEx->hidden('MailContent.edit_mail_form') ?>
			<?php if($this->action == 'admin_edit'): ?>
			<?php $baser->link('≫ 編集する','javascript:void(0)',array('id'=>'EditForm')) ?>
			<?php endif ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpFormTemplate','class'=>'help','alt'=>'ヘルプ')) ?> <?php echo $formEx->error('MailContent.form_template') ?>
			<div id="helptextFormTemplate" class="helptext">
				<ul>
					<li>メールフォーム本体のテンプレートを指定します。</li>
					<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
				</ul>
			</div>
			&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.mail_template', '送信メールテンプレート名') ?></th>
		<td class="col-input">
			<?php echo $formEx->select('MailContent.mail_template',$mail->getMailTemplates(),null,array(),false) ?>
			<?php echo $formEx->hidden('MailContent.edit_mail') ?>
			<?php if($this->action == 'admin_edit'): ?>
			<?php $baser->link('≫ 編集する','javascript:void(0)',array('id'=>'EditMail')) ?>
			<?php endif ?>
			<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpMailTemplate','class'=>'help','alt'=>'ヘルプ')) ?>
			<?php echo $formEx->error('MailContent.mail_template') ?>
			<div id="helptextMailTemplate" class="helptext">
				<ul>
					<li>送信するメールのテンプレートを指定します。</li>
					<li>「編集する」からテンプレートの内容を編集する事ができます。</li>
				</ul>
			</div>
			&nbsp; </td>
	</tr>
</table>
<div class="submit">
	<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
	<?php else: ?>
	<?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削　除',array('action'=>'delete', $formEx->value('MailContent.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？\n\n※ 現在このメールフォームに設定されているフィールドは全て削除されます。', $formEx->value('MailContent.name')),false); ?>
	<?php endif ?>
</div>
