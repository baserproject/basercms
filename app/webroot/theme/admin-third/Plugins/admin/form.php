<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] プラグイン　フォーム
 *
 * @var BcAppView $this
 */
$this->BcBaser->i18nScript([
	'message1' => __d('baser', 'プラグインのデータを初期化します。よろしいですか？'),
]);
?>


<?php echo $this->BcForm->input('ResetDbUrl', ['type' => 'hidden', 'value' => $this->BcBaser->getUrl(['action' => 'reset_db'])]) ?>
<script>
	$(function () {
		$("#BtnReset").click(function () {
			if (confirm(bcI18n.message1)) {
				$("#PluginAdminInstallForm").attr('action', $("#ResetDbUrl").val());
				$.bcUtil.showLoader();
			} else {
				return false;
			}
		});
		$("#BtnSave").click(function () {
			$.bcUtil.showLoader();
		});
	});
</script>

<?php if ($installMessage): ?>
	<div id="UpdateMessage"><?php echo $installMessage ?></div>
<?php endif ?>

<?php echo $this->BcForm->create('Plugin', ['url' => [$this->request->data['Plugin']['name']]]) ?>
<?php echo $this->BcForm->input('Plugin.name', ['type' => 'hidden']) ?>
<?php echo $this->BcForm->input('Plugin.title', ['type' => 'hidden']) ?>
<?php echo $this->BcForm->input('Plugin.status', ['type' => 'hidden']) ?>
<?php echo $this->BcForm->input('Plugin.version', ['type' => 'hidden']) ?>

<div class="em-box bca-em-box">
	<?php echo h($this->BcForm->value('Plugin.name')) . ' ' . $this->BcForm->value('Plugin.version') ?>
	<?php if ($this->BcForm->value('Plugin.title')): ?>
		（<?php echo h($this->BcForm->value('Plugin.title')) ?>）
	<?php endif ?>
</div>

<div class="align-center">
	<?php echo $this->BcForm->input('Plugin.permission', ['type' => 'radio', 'options' => ['1' => __d('baser', '全てのユーザーで利用'), '2' => __d('baser', '管理ユーザーのみ利用')]]) ?>
</div>

<div>
	<?php echo $this->BcForm->error('Plugin.name') ?>
	<?php echo $this->BcForm->error('Plugin.title') ?>
</div>


<div class="bca-actions">
	<?php if ($dbInited): ?>
		<div class="bca-actions__main">
			<?php echo $this->BcForm->submit(__d('baser', '有効化'), ['div' => false, 'class' => 'button bca-btn', 'id' => 'BtnSave', 'data-bca-btn-status' => 'primary']) ?>
		</div>
		<div class="bca-actions__sub">
			<?php echo $this->BcForm->submit(__d('baser', 'プラグインのデータを初期化する'), ['div' => false, 'class' => 'button bca-btn', 'id' => 'BtnReset']) ?>
		</div>
	<?php else: ?>
		<div class="bca-actions__main">
			<?php echo $this->BcForm->submit(__d('baser', 'インストール'), ['div' => false, 'class' => 'button bca-btn', 'id' => 'BtnSave', 'data-bca-btn-status' => 'primary']) ?>
		</div>
	<?php endif; ?>
</div>

<?php echo $this->BcForm->end() ?>
