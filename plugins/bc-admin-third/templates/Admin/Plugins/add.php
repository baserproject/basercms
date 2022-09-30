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

$this->BcBaser->i18nScript([
  'message1' => __d('baser', 'プラグインをアップロードし、そのままインストールします。よろしいですか？'),
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


<p><?php echo __d('baser', 'ZIP 形式のプラグインファイルをお持ちの場合、こちらからアップロードしてインストールできます。') ?></p>
<?php echo $this->BcAdminForm->create('Plugin', ['type' => 'file']) ?>

<div class="submit">
  <?php echo $this->BcAdminForm->control('Plugin.file', ['type' => 'file']) ?>
  <?php echo $this->BcAdminForm->submit(__d('baser', 'インストール'), ['class' => 'button bca-btn', 'div' => false, 'data-bca-btn-status' => 'primary']) ?>
</div>

<?php echo $this->BcAdminForm->end() ?>
