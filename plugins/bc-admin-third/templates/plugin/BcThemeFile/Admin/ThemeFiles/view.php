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
 * [ADMIN] テーマファイル表示
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BcThemeFile\Model\Entity\ThemeFile $themeFile
 * @var \BcThemeFile\Form\ThemeFileForm $themeFileForm
 * @var string $currentPath
 * @var bool $isWritable
 * @var string $path
 * @var string $theme
 * @var string $plugin
 * @var string $pageTitle
 * @var string $type
 * @var bool $isDefaultTheme
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcBaser->js('BcThemeFile.admin/theme_files/form.bundle', false);
$this->BcAdmin->setTitle($pageTitle);
$this->BcAdmin->setHelp('theme_files_form');
// コピー可能である条件
// - デフォルトテーマを表示している
// - 現在利用しているテーマがデフォルトテーマではない
// - 編集可能の設定となっている
$isCopyable = ($isDefaultTheme &&
  \Cake\Utility\Inflector::camelize(Cake\Core\Configure::read('BcApp.coreFrontTheme'), '-') !== \BaserCore\Utility\BcUtil::getCurrentTheme() &&
  Cake\Core\Configure::read('BcThemeFile.allowedThemeEdit')
);
?>


<?php echo $this->BcAdminForm->create($themeFileForm, [
  'id' => 'ThemeFileForm'
]) ?>

<?php $this->BcBaser->element('ThemeFiles/form') ?>

<div class="submit bca-actions">
  <div class="bca-actions__main">
  <?php $this->BcBaser->link(__d('baser_core', '一覧に戻る'),
    array_merge(['action' => 'index', $theme, $plugin, $type], explode('/', dirname($path))), [
      'class' => 'button bca-btn',
      'data-bca-btn-type' => 'back-to-list'
  ]); ?>
  <?php // プラグインのアセットの場合はコピーできない ?>
  <?php //if($isDefaultTheme && !(($type == 'css' || $type == 'js' || $type == 'img') && $plugin)): ?>
  <?php // テーマ編集が許可されていない場合コピー不可 ?>
  <?php if ($isCopyable): ?>
    &nbsp;&nbsp;
    <?php echo $this->BcAdminForm->postLink(__d('baser_core', '現在のテーマにコピー'),
      array_merge(['action' => 'copy_to_theme', $theme, $plugin, $type], explode('/', $path)), [
        'block' => true,
        'confirm' => __d('baser_core',
          "本当に現在のテーマ「{0}」にコピーしてもいいですか？\n既に存在するファイルは上書きされます。",
          Cake\Utility\Inflector::camelize(\BaserCore\Utility\BcUtil::getCurrentTheme())
        ),
        'class' => 'bca-submit-token bca-btn',
        'data-bca-btn-type' => 'save',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
    ]) ?>
  <?php endif; ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>

<?php echo $this->fetch('postLink') ?>
