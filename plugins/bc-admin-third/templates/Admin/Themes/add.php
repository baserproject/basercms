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
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser', 'テーマアップロード'));
$this->BcBaser->js('admin/themes/add.bundle', false);
$this->BcBaser->i18nScript([
  'message1' => __d('baser', "テーマをアップロードします。よろしいですか？\n※ アップロード後、改めてテーマの適用作業が必要です。")
]);
?>


<p><?php echo __d('baser', 'ZIP 形式のテーマファイルをお持ちの場合、こちらからアップロードして適用できます。') ?></p>
<?php echo $this->BcAdminForm->create(null, ['type' => 'file']) ?>

<div class="submit">
  <?php echo $this->BcAdminForm->control('file', ['type' => 'file']) ?>
  <?php echo $this->BcAdminForm->submit(__d('baser', 'アップロード'), ['class' => 'button bca-btn', 'div' => false, 'id' => 'BtnSave']) ?>
</div>

<?php echo $this->BcAdminForm->end() ?>
