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

<h2><?php $baser->contentsTitle() ?></h2>
<h3>基本項目</h3>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $formEx->create('MailContent') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('MailContent.id', 'NO') ?></th>
		<td class="col-input">
			<?php echo $formEx->text('MailContent.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp;
		</td>
	</tr>
<?php endif; ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.name', 'メールフォームアカウント名') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.name', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $html->image('help.png',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $formEx->error('MailContent.name') ?>
            <div id="helptextName" class="helptext">
            <ul>
                <li>メールフォームのURLに利用します。<br />(例)メールフォームIDが test の場合・・・http://example/test/form</li>
                <li>半角英数字で入力して下さい。</li>
            </ul>
            </div>
            &nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.title', 'メールフォームタイトル') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.title', array('size'=>40,'maxlength'=>255)) ?><?php echo $formEx->error('MailContent.title') ?>&nbsp;</td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.sender_1', '送信先メールアドレス') ?></th>
		<td class="col-input">
            <?php echo $formEx->radio('MailContent.sender_1_',array('0'=>'管理者用メールアドレスに送信する','1'=>'別のメールアドレスに送信する'),array('legend'=>false,'separator'=>'<br />')) ?><br />
            <?php echo $formEx->text('MailContent.sender_1', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $formEx->error('MailContent.sender_1') ?>&nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.sender_name', '送信先名') ?></th>
		<td class="col-input">
            <?php echo $formEx->text('MailContent.sender_name', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $html->image('help.png',array('id'=>'helpSenderName','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $formEx->error('MailContent.sender_name') ?>
            <div id="helptextSenderName" class="helptext">自動返信メールの送信者に表示します。</div>
            &nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.subject_user', '自動返信メール<br />件名<br />[ユーザー宛]') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('MailContent.subject_user', array('cols'=>35,'rows'=>2)) ?>
            <?php echo $html->image('help.png',array('id'=>'helpSubjectUser','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $formEx->error('MailContent.subject_user') ?>
            <div id="helptextSubjectUser" class="helptext">ユーザー宛の自動返信メールの件名に表示します。</div>
            &nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.subject_admin', '自動送信メール<br />件名<br />[管理者宛]') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('MailContent.subject_admin', array('cols'=>35,'rows'=>2)) ?>
            <?php echo $html->image('help.png',array('id'=>'helpSubjectAdmin','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $formEx->error('MailContent.subject_admin') ?>
            <div id="helptextSubjectAdmin" class="helptext">管理者宛の自動送信メールの件名に表示します。</div>
            &nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('MailContent.redirect_url', 'リダイレクトURL') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.redirect_url', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $html->image('help.png',array('id'=>'helpRedirectUrl','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $formEx->error('MailContent.redirect_url') ?>
            <div id="helptextRedirectUrl" class="helptext">
                <ul>
                    <li>メール送信後、別のURLにリダイレクトする場合、ここにURLを指定します。</li>
                    <li>httpからの完全なURLを指定して下さい。</li>
                </ul>
            </div>
            &nbsp;
        </td>
	</tr>
</table>


<h3><a href="javascript:void(0)" id="formOption" class="slide-trigger">オプション</a></h3>


<table cellpadding="0" cellspacing="0" class="admin-row-table-01 slide-body" id="formOptionBody">
	<tr>
		<th class="col-head"><?php echo $formEx->label('MailContent.sender_2', 'CC用送信先メールアドレス') ?></th>
		<td class="col-input">
            <?php echo $formEx->text('MailContent.sender_2', array('size'=>40,'maxlength'=>255)) ?><?php echo $formEx->error('MailContent.sender_2') ?>&nbsp;
            <?php echo $html->image('help.png',array('id'=>'helpSender2','class'=>'help','alt'=>'ヘルプ')) ?>
            <div id="helptextSender2" class="helptext">CC（カーボンコピー）用のメールアドレスを指定します。</div>
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.layout_template', 'レイアウトテンプレート名') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.layout_template', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $html->image('help.png',array('id'=>'helpLayoutTemplate','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $formEx->error('MailContent.layout_template') ?>
            <div id="helptextLayoutTemplate" class="helptext">
                <ul>
                    <li>メールフォームの外枠のテンプレート名を指定します。</li>
                    <li>新しいテンプレートを利用するには、<br />/app/plugins/mail/views/layouts に、拡張子ctpで保存してここで指定します。</li>
                </ul>
            </div>
            &nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.form_template', 'メールフォームテンプレート名') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.form_template', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $html->image('help.png',array('id'=>'helpFormTemplate','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $formEx->error('MailContent.form_template') ?>
            <div id="helptextFormTemplate" class="helptext">
                <ul>
                    <li>メールフォーム本体のテンプレート名を指定します。</li>
                    <li>半角で入力してください。</li>
                </ul>
            </div>
            &nbsp;
        </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('MailContent.mail_template', '送信メールテンプレート名') ?></th>
		<td class="col-input"><?php echo $formEx->text('MailContent.mail_template', array('size'=>40,'maxlength'=>255)) ?>
            <?php echo $html->image('help.png',array('id'=>'helpMailTemplate','class'=>'help','alt'=>'ヘルプ')) ?>
            <?php echo $formEx->error('MailContent.mail_template') ?>
            <div id="helptextMailTemplate" class="helptext">
                <ul>
                    <li>送信するメールのテンプレート名を指定します。</li>
                    <li>半角で入力してください。</li>
                </ul>
            </div>
            &nbsp;
        </td>
	</tr>
</table>

<div class="submit">
<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
<?php else: ?>
	<?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削　除',array('action'=>'delete', $formEx->value('MailContent.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $formEx->value('MailContent.name')),false); ?>
<?php endif ?>
</div>
