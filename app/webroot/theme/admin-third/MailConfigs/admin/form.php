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
 * [ADMIN] メール設定 フォーム
 */
$this->BcBaser->js('Mail.admin/mail_configs/form', false);
?>
<!-- form -->
<?php echo $this->BcForm->create('MailConfig', ['url' => ['action' => 'form']]) ?>

<?php echo $this->BcFormTable->dispatchBefore() ?>

<?php echo $this->BcForm->input('MailConfig.id', ['type' => 'hidden']) ?>
<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table bca-form-table">
		<tr>
			<th class="bca-form-table__label"><?php echo $this->BcForm->label('MailConfig.site_name', __d('baser', '署名：Webサイト名')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailConfig.site_name', ['type' => 'text', 'size' => 35, 'maxlength' => 255, 'autofocus' => true]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailConfig.site_name') ?>
				<div id="helptextSiteName" class="helptext"><?php echo __d('baser', '自動送信メールの署名に挿入されます。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="bca-form-table__label"><?php echo $this->BcForm->label('MailConfig.site_url', __d('baser', '署名：WebサイトURL')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailConfig.site_url', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailConfig.site_url') ?>
				<div id="helptextSiteUrl" class="helptext"><?php echo __d('baser', '自動送信メールの署名に挿入されます。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="bca-form-table__label"><?php echo $this->BcForm->label('MailConfig.site_email', __d('baser', '署名：Eメール')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailConfig.site_email', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailConfig.site_email') ?>
				<div id="helptextSiteEmail" class="helptext">
					<ul>
						<li><?php echo __d('baser', '自動送信メールの署名に挿入されます。') ?></li>
						<li><?php echo __d('baser', 'メールの送信先ではありません。') ?></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="bca-form-table__label"><?php echo $this->BcForm->label('MailConfig.site_tel', __d('baser', '署名：電話番号')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailConfig.site_tel', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailConfig.site_tel') ?>
				<div id="helptextSiteTel" class="helptext"><?php echo __d('baser', '自動送信メールの署名に挿入されます。') ?></div>
			</td>
		</tr>
		<tr>
			<th class="bca-form-table__label"><?php echo $this->BcForm->label('MailConfig.site_fax', __d('baser', '署名：FAX番号')) ?></th>
			<td class="col-input bca-form-table__input">
				<?php echo $this->BcForm->input('MailConfig.site_fax', ['type' => 'text', 'size' => 35, 'maxlength' => 255]) ?>
				<i class="bca-icon--question-circle btn help bca-help"></i>
				<?php echo $this->BcForm->error('MailConfig.site_fax') ?>
				<div id="helptextSiteFax" class="helptext"><?php echo __d('baser', '自動送信メールの署名に挿入されます。') ?></div>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<?php echo $this->BcFormTable->dispatchAfter() ?>

<div class="submit bca-actions">
	<div class="bca-actions__main">
		<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn bca-actions__item',
			'data-bca-btn-type' => 'save',
			'data-bca-btn-size' => 'lg',
			'data-bca-btn-width' => 'lg',
		]) ?>
	</div>
</div>

<?php echo $this->BcForm->end() ?>
