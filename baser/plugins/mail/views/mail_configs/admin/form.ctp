<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] メール設定 フォーム
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

<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpAdmin', 'class' => 'slide-trigger', 'alt' => 'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>メールフォームプラグインの基本設定を登録します。<br />
		各項目のヘルプメッセージを確認し登録を完了させてください。<br />
	<ul>
		<li>文字コードは基本的に変更する必要はありません。</li>
		<li>SMTPの設定は、サーバーがsendmailをサポートしていない場合等に入力します。</li>
	</ul>
</div>

<!-- form -->
<h3>基本項目</h3>
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>

<?php echo $formEx->create('MailConfig', array('action' => 'form')) ?>
<?php echo $formEx->input('MailConfig.id', array('type' => 'hidden')) ?>

<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th><?php echo $formEx->label('MailConfig.site_name', '署名：WEBサイト名') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('MailConfig.site_name', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpSiteName', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('MailConfig.site_name') ?>
			<div id="helptextSiteName" class="helptext">自動送信メールの署名に挿入されます。</div>
		</td>
	</tr>
	<tr>
		<th><?php echo $formEx->label('MailConfig.site_url', '署名：WEBサイトURL') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('MailConfig.site_url', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpSiteUrl', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('MailConfig.site_url') ?>
			<div id="helptextSiteUrl" class="helptext">自動送信メールの署名に挿入されます。</div>
		</td>
	</tr>
	<tr>
		<th><?php echo $formEx->label('MailConfig.site_email', '署名：Eメール') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('MailConfig.site_email', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpSiteEmail', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('MailConfig.site_email') ?>
			<div id="helptextSiteEmail" class="helptext">
				<ul>
					<li>自動送信メールの署名に挿入されます。</li>
					<li>メールの送信先ではありません。</li>
				</ul>
			</div>
		</td>
	</tr>
	<tr>
		<th><?php echo $formEx->label('MailConfig.site_tel', '署名：電話番号') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('MailConfig.site_tel', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpSiteTel', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('MailConfig.site_tel') ?>
			<div id="helptextSiteTel" class="helptext">自動送信メールの署名に挿入されます。</div>
		</td>
	</tr>
	<tr>
		<th><?php echo $formEx->label('MailConfig.site_fax', '署名：FAX番号') ?></th>
		<td class="col-input">
			<?php echo $formEx->input('MailConfig.site_fax', array('type' => 'text', 'size' => 35, 'maxlength' => 255)) ?>
			<?php echo $html->image('img_icon_help_admin.gif', array('id' => 'helpSiteFax', 'class' => 'help', 'alt' => 'ヘルプ')) ?>
			<?php echo $formEx->error('MailConfig.site_fax') ?>
			<div id="helptextSiteFax" class="helptext">自動送信メールの署名に挿入されます。</div>
		</td>
	</tr>
</table>

<div class="align-center"><?php echo $formEx->submit('更　新', array('div' => false, 'class' => 'btn-orange button')) ?></div>

<?php echo $formEx->end() ?>