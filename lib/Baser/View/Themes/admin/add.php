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
?>

<?php
$this->BcBaser->i18nScript([
	'message1' => __d('baser', 'テーマをアップロードし、そのまま適用します。よろしいですか？')
]);
?>

<script>
	$(function () {
		$("#BtnSave").click(function () {
			if (confirm(bcI18n.message1)) {
				$.bcUtil.showLoader();
				return true;
			}
			return false;
		});
	});
</script>


<p><?php echo __d('baser', 'ZIP 形式のテーマファイルをお持ちの場合、こちらからアップロードして適用できます。') ?></p>
<?php echo $this->BcForm->create('Theme', ['type' => 'file']) ?>

<div class="submit">
	<?php echo $this->BcForm->input('Theme.file', ['type' => 'file']) ?>
	<?php echo $this->BcForm->submit(__d('baser', '適用'), ['class' => 'button', 'div' => false, 'id' => 'BtnSave']) ?>
</div>

<?php echo $this->BcForm->end() ?>
