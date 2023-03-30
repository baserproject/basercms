<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser_core', 'プラグインアップロード'));
$this->BcBaser->i18nScript([
  'message1' => __d('baser_core', 'プラグインをアップロードします。よろしいですか？'),
]);
$this->BcBaser->js('admin/plugins/add.bundle', false);
?>


<p><?php echo __d('baser_core', 'ZIP 形式のプラグインファイルをお持ちの場合、こちらからアップロードできます。') ?></p>
<?php echo $this->BcAdminForm->create(null, ['type' => 'file']) ?>

<div class="submit">
  <?php echo $this->BcAdminForm->control('file', ['type' => 'file']) ?>
  <?php echo $this->BcAdminForm->submit(__d('baser_core', 'アップロード'), [
    'id' => 'BtnSave',
    'class' => 'button bca-btn',
    'div' => false,
    'data-bca-btn-status' => 'primary'
  ]) ?>
</div>

<?php echo $this->BcAdminForm->end() ?>
