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
 * [ADMIN] テーマファイル管理メニュー
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var bool $isDefaultTheme
 * @var string $theme
 * @var string $plugin
 * @checked
 * @noTodo
 * @unitTest
 */
$types = [
  'layout' => __d('baser_core', 'レイアウト'),
  'element' => __d('baser_core', 'エレメント'),
  'email' => __d('baser_core', 'Eメール'),
  'etc' => __d('baser_core', 'コンテンツ'),
  'css' => 'CSS',
  'img' => __d('baser_core', 'イメージ'),
  'js' => 'Javascript'
];
if ($isDefaultTheme) {
  $plugins = [0 => ['name' => 'BaserCore', 'title' => __d('baser_core', 'コア')]];
  $plugins = array_merge($plugins, \BaserCore\Utility\BcUtil::getEnablePlugins());
} else {
  $plugins = [0 => ['name' => $theme, 'title' => $theme]];
  $plugins = array_merge($plugins, \BaserCore\Utility\BcUtil::getEnablePlugins());
}
$this->BcBaser->js('BcThemeFile.admin/theme_files/submenus.bundle', false);
$this->BcBaser->css('BcThemeFile.admin/style', false);
?>


<div class="bca-main__submenu" id="ThemeFilesMenu">
  <?php foreach($plugins as $k => $pluginVal): ?>
    <?php if (!\BaserCore\Utility\BcUtil::getExistsTemplateDir($theme, $pluginVal['name'], '', 'front') &&
              !\BaserCore\Utility\BcUtil::getExistsWebrootDir($theme, $pluginVal['name'], '', 'front')) continue; ?>

           <?php
            if($pluginVal['name'] == $plugin) {
              $activeClass = 'selected-plugin';
            }else {
              $activeClass = '';
            }
           ?>
    <h2 class="bca-main__submenu-title <?php echo $activeClass ?>" data-id="<?php echo ($k -1) ?>">
      <?php echo $pluginVal['title'] ?>
    </h2>
    <ul class="bca-main__submenu-list clearfix">
      <?php foreach($types as $key => $type): ?>
        <li class="bca-main__submenu-list-item">
          <?php if ($theme !== $pluginVal['name']): ?>
            <?php $this->BcBaser->link(sprintf(__d('baser_core', '%s 一覧'), $type), ['action' => 'index', $theme, $pluginVal['name'], $key]) ?>
          <?php else: ?>
            <?php $this->BcBaser->link(sprintf(__d('baser_core', '%s 一覧'), $type), ['action' => 'index', $theme, $key]) ?>
          <?php endif ?>
        </li>
      <?php endforeach ?>
    </ul>
  <?php endforeach ?>
</div>
