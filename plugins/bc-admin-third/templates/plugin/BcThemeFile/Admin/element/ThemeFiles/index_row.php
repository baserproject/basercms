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
 * [ADMIN] テーマファイル一覧　行
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $fullpath
 * @var string $theme
 * @var string $plugin
 * @var string $type
 * @var string $path
 * @var \BcThemeFile\Model\Entity\ThemeFile $themeFile
 * @var bool $isDefaultTheme
 * @checked
 * @unitTest
 * @noTodo
 */

$writable = true;
if ((is_dir($fullpath) && !is_writable($fullpath)) || $isDefaultTheme) {
  $writable = false;
}
$params = explode('/', $path);
array_push($params, $themeFile->name);
?>


<tr>
  <td class="bca-table-listup__tbody-td bca-table-listup__tbody-td--select">
    <?php if ($this->BcBaser->isAdminUser() && !$isDefaultTheme): ?>
      <?php echo $this->BcAdminForm->control('batch', [
          'type' => 'checkbox',
          'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>',
          'class' => 'batch-targets bca-checkbox__input',
          'value' => $themeFile->fullpath,
          'escape' => false,
          'hiddenField' => false
      ]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td" data-bca-text-size="md">
    <?php if ($themeFile->type === 'image'): ?>
      <?php $this->BcBaser->link(
        $this->BcBaser->getImg(array_merge(['action' => 'img_thumb', 100, 100, $theme, $plugin, $type], $params), ['alt' => $themeFile->name]),
        array_merge(['action' => 'img', $theme, $plugin, $type], explode('/', $path), [$themeFile->name]), [
          'rel' => 'colorbox',
          'title' => $themeFile->name,
          'style' => 'display:block;padding:5px;important;float:left;background-color:#FFFFFF',
          'escape' => false
      ]) ?>&nbsp;
      <?php echo $themeFile->name ?>
    <?php elseif ($themeFile->type === 'folder'): ?>
      <?php $this->BcBaser->link(
        '<i class="bca-icon--folder" data-bca-icon-size="md"></i>' . h($themeFile->name),
        array_merge(['action' => 'index', $theme, $plugin, $type], $params), [
          'class' => '',
          'escape' => false
      ]) ?>/
    <?php else: ?>
      <?php if ($writable): ?>
        <?php $this->BcBaser->link(
          '<i class="bca-icon--file" data-bca-icon-size="md"></i>' . h($themeFile->name),
          array_merge(['action' => 'edit', $theme, $type], $params), [
            'class' => '',
            'escape' => false
        ]) ?>
      <?php else: ?>
        <?php $this->BcBaser->link(
          '<i class="bca-icon--file" data-bca-icon-size="md"></i>' . h($themeFile->name),
          array_merge(['action' => 'view', $theme, $type], $params), [
            'class' => '',
            'escape' => false
        ]) ?>
      <?php endif ?>
    <?php endif ?>
  </td>
  <td class="row-tools bca-table-listup__tbody-td bca-table-listup__tbody-td--actions">
    <?php if ($writable): ?>

      <?php if ($themeFile->type === 'folder'): ?>
        <?php echo $this->BcAdminForm->postLink('', array_merge(['action' => 'copy_folder', $theme, $type], $params), [
          'title' => __d('baser', 'コピー'),
          'class' => 'btn-copy bca-btn-icon',
          'data-bca-btn-type' => 'copy',
          'data-bca-btn-size' => 'lg'
        ]) ?>
        <?php $this->BcBaser->link('', array_merge(['action' => 'edit_folder', $theme, $type], $params), [
          'title' => __d('baser', '編集'),
          'class' => 'bca-btn-icon',
          'data-bca-btn-type' => 'edit',
          'data-bca-btn-size' => 'lg'
        ]) ?>
        <?php echo $this->BcAdminForm->postLink('', array_merge(['action' => 'delete_folder', $theme, $type], $params), [
          'confirm' => __d('baser', 'フォルダ {0} を本当に削除してもよろしいですか？', $themeFile->name),
          'title' => __d('baser', '削除'),
          'class' => 'btn-delete bca-btn-icon',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'lg'
        ]) ?>
      <?php else: ?>
        <?php echo $this->BcAdminForm->postLink('', array_merge(['action' => 'copy', $theme, $type], $params), [
          'title' => __d('baser', 'コピー'),
          'class' => 'btn-copy bca-btn-icon',
          'data-bca-btn-type' => 'copy',
          'data-bca-btn-size' => 'lg'
        ]) ?>
        <?php $this->BcBaser->link('', array_merge(['action' => 'edit', $theme, $type], $params), [
          'title' => __d('baser', '編集'),
          'escape' => false,
          'class' => 'bca-btn-icon',
          'data-bca-btn-type' => 'edit',
          'data-bca-btn-size' => 'lg'
        ]) ?>
        <?php echo $this->BcAdminForm->postLink('', array_merge(['action' => 'delete', $theme, $type], $params), [
          'confirm' => __d('baser', 'ファイル {0} を本当に削除してもよろしいですか？', $themeFile->name),
          'title' => __d('baser', '削除'),
          'class' => 'btn-delete bca-btn-icon',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'lg'
        ]) ?>
      <?php endif ?>
    <?php else: ?>
      <?php if ($themeFile->type === 'folder'): ?>
        <?php $this->BcBaser->link('', array_merge(['action' => 'view_folder', $theme, $plugin, $type], $params), [
          'class' => 'button-s bca-btn-icon',
          'data-bca-btn-type' => 'preview',
          'data-bca-btn-size' => 'lg'
        ]) ?>
      <?php else: ?>
        <?php $this->BcBaser->link('', array_merge(['action' => 'view', $theme, $plugin, $type], $params), [
          'class' => 'button-s bca-btn-icon',
          'data-bca-btn-type' => 'preview',
          'data-bca-btn-size' => 'lg'
        ]) ?>
      <?php endif ?>
    <?php endif ?>
  </td>

  <?php echo $this->BcListTable->dispatchShowRow($themeFile) ?>

</tr>
