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
 * [ADMIN] メール設定 フォーム
 */
$this->BcBaser->js('Mail.admin/mail_configs/form', false);
?>


<h2>基本項目</h2>

<?php echo $this->BcForm->create('MailConfig', ['url' => ['action' => 'form']]) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcForm->input('MailConfig.id', ['type' => 'hidden']) ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<tr>
			<th><?php echo $this->BcForm->label('MailConfig.site_name', '署名：WEBサイト名') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('MailConfig.site_name', ['type' => 'text', 'size' => 35, 'maxlength' => 255, 'autofocus' => true]) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpSiteName', 'class' => 'btn help', 'alt' => 'ヘルプ']) ?>
				<?php echo $this->BcForm->error('MailConfig.site_name') ?>
				<div id="helptextSiteName" class="helptext">自動送信メールの署名に挿入されます。</div>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('MailConfig.site_url', '署名：WEBサイトURL') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('MailConfig.site_url', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpSiteUrl', 'class' => 'btn help', 'alt' => 'ヘルプ']) ?>
				<?php echo $this->BcForm->error('MailConfig.site_url') ?>
				<div id="helptextSiteUrl" class="helptext">自動送信メールの署名に挿入されます。</div>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('MailConfig.site_email', '署名：Eメール') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('MailConfig.site_email', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpSiteEmail', 'class' => 'btn help', 'alt' => 'ヘルプ']) ?>
				<?php echo $this->BcForm->error('MailConfig.site_email') ?>
				<div id="helptextSiteEmail" class="helptext">
					<ul>
						<li>自動送信メールの署名に挿入されます。</li>
						<li>メールの送信先ではありません。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('MailConfig.site_tel', '署名：電話番号') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('MailConfig.site_tel', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpSiteTel', 'class' => 'btn help', 'alt' => 'ヘルプ']) ?>
				<?php echo $this->BcForm->error('MailConfig.site_tel') ?>
				<div id="helptextSiteTel" class="helptext">自動送信メールの署名に挿入されます。</div>
			</td>
		</tr>
		<tr>
			<th><?php echo $this->BcForm->label('MailConfig.site_fax', '署名：FAX番号') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('MailConfig.site_fax', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
				<?php echo $this->BcHtml->image('admin/icn_help.png', ['id' => 'helpSiteFax', 'class' => 'btn help', 'alt' => 'ヘルプ']) ?>
				<?php echo $this->BcForm->error('MailConfig.site_fax') ?>
				<div id="helptextSiteFax" class="helptext">自動送信メールの署名に挿入されます。</div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit"><?php echo $this->BcForm->submit('保存', ['div' => false, 'class' => 'button', 'id' => 'BtnSave']) ?></div>

<?php echo $this->BcForm->end() ?>
