<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * @var \BaserCore\View\BcAdminAppView $this
 */
$this->BcAdmin->setTitle(__d('baser', 'テーマアップロード'));
$this->BcBaser->i18nScript([
  'message1' => __d('baser', "テーマをアップロードします。よろしいですか？\n※ アップロード後、改めてテーマの適用作業が必要です。")
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
<?php echo $this->BcAdminForm->create('Theme', ['type' => 'file']) ?>

<div class="submit">
  <?php echo $this->BcAdminForm->control('Theme.file', ['type' => 'file']) ?>
  <?php echo $this->BcForm->submit(__d('baser', '適用'), ['class' => 'button bca-btn', 'div' => false, 'id' => 'BtnSave']) ?>
</div>

<?php echo $this->BcAdminForm->end() ?>
