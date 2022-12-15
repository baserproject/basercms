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
 * @var bool $isDefaultTheme
 * @var string $theme
 */
$types = [
  'layout' => __d('baser', 'レイアウト'),
  'element' => __d('baser', 'エレメント'),
  'email' => __d('baser', 'Eメール'),
  'etc' => __d('baser', 'コンテンツ'),
  'css' => 'CSS',
  'img' => __d('baser', 'イメージ'),
  'js' => 'Javascript'
];
if ($isDefaultTheme) {
  $plugins = [0 => ['name' => 'BaserCore', 'title' => __d('baser', 'コア')]];
  $plugins = array_merge($plugins, \BaserCore\Utility\BcUtil::getEnablePlugins());
} else {
  $plugins = [0 => ['name' => $theme, 'title' => $theme]];
  $plugins = array_merge($plugins, \BaserCore\Utility\BcUtil::getEnablePlugins());
}
?>


<script>
$(function (){
    $('#ThemeFilesMenu').accordion({
        collapsible: true,
        heightStyle: "content"
    });
})
</script>
<style>
.ui-accordion-header {
  border: 1px solid #eee!important;
}
</style>


<div class="bca-main__submenu" id="ThemeFilesMenu">
  <?php foreach($plugins as $plugin): ?>
    <?php if(!\BaserCore\Utility\BcUtil::getExistsTemplateDir($plugin['name'], '', 'front')) continue; ?>
    <h2 class="bca-main__submenu-title">
      <?php echo $plugin['title'] ?>
    </h2>
    <ul class="bca-main__submenu-list clearfix">
      <?php foreach($types as $key => $type): ?>
        <li class="bca-main__submenu-list-item">
          <?php if($theme !== $plugin['name']): ?>
            <?php $this->BcBaser->link(sprintf(__d('baser', '%s 一覧'), $type), ['action' => 'index', $theme, $plugin['name'], $key]) ?>
          <?php else: ?>
            <?php $this->BcBaser->link(sprintf(__d('baser', '%s 一覧'), $type), ['action' => 'index', $theme, $key]) ?>
          <?php endif ?>
        </li>
      <?php endforeach ?>
    </ul>
  <?php endforeach ?>
</div>
