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

use Cake\Core\Configure;

/**
 * [ADMIN] テーマ一覧　テーブル
 * @var \BaserCore\View\BcAdminAppView $this
 * @var array $currentTheme
 * @var array $defaultDataPatterns
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<div id="CurrentTheme" class="bca-current-theme">
  <h2 class="bca-current-theme__name"><?php echo __d('baser', '現在のテーマ') ?></h2>
  <?php if ($currentTheme): ?>
    <div class="bca-current-theme__inner">
      <div class="bca-current-theme__main">
        <div class="bca-current-theme__screenshot">
          <?php if ($currentTheme->screenshot): ?>
            <?php $this->BcBaser->img(['action' => 'screenshot', $currentTheme->name], ['alt' => $currentTheme->title]) ?>
          <?php else: ?>
            <?php $this->BcBaser->img('admin/no-screenshot.png', ['alt' => $currentTheme->title]) ?>
          <?php endif ?>
        </div>
        <div class="row-tools">
          <?php if (Configure::read('BcApp.allowedThemeEdit')): ?>
            <?php $this->BcBaser->link('', ['controller' => 'theme_files', 'action' => 'index', $currentTheme->name], [
              'title' => __d('baser', 'テンプレート編集'),
              'class' => 'bca-btn-icon', 'data-bca-btn-type' =>
              'file-list', 'data-bca-btn-size' => 'lg'
            ]) ?>
          <?php endif; ?>
          <?php echo $this->BcAdminForm->postLink('', ['action' => 'copy', $currentTheme->name], [
            'title' => __d('baser', 'テーマコピー'),
            'class' => 'btn-copy bca-btn-icon',
            'data-bca-btn-type' => 'copy',
            'data-bca-btn-size' => 'lg'
          ]) ?>
        </div>
      </div>

      <div class="bca-current-theme__sub">
        <div class="theme-info">
          <h3 class="theme-name">
            <strong><?php echo h($currentTheme->title) ?></strong>&nbsp;(&nbsp;<?php echo $currentTheme->name ?>
            &nbsp;)</h3>
          <p class="theme-version"><?php echo __d('baser', 'バージョン') ?>
            ：<?php echo $currentTheme->version ?></p>
          <p class="theme-author"><?php echo __d('baser', '制作者') ?>
            ：<?php if (!empty($currentTheme->url) && !empty($currentTheme->author)): ?>
              <?php $this->BcBaser->link($currentTheme->author, $currentTheme->url, ['target' => '_blank', 'escape' => true]) ?>
            <?php else: ?>
              <?php echo h($currentTheme->author) ?>
            <?php endif ?>
          </p>
        </div>
        <?php if ($defaultDataPatterns && $this->BcBaser->isAdminUser()): ?>
          <?php echo $this->BcAdminForm->create(null, ['url' => ['action' => 'load_default_data_pattern'], 'id' => 'ThemeLoadDefaultDataPatternForm']) ?>
          <?php echo $this->BcAdminForm->control('default_data_pattern', ['type' => 'select', 'options' => $defaultDataPatterns]) ?>
          <?php echo $this->BcAdminForm->submit(__d('baser', '初期データ読込'), ['class' => 'bca-btn', 'div' => false, 'id' => 'BtnLoadDefaultDataPattern']) ?>
          <?php echo $this->BcAdminForm->end() ?>
        <?php endif ?>
        <div
          class="theme-description"><?php echo nl2br($this->BcText->autoLinkUrls($currentTheme->description)) ?></div>
      </div>
    </div>

  <?php else: ?>
    <p><?php echo __d('baser', '現在、テーマが選択されていません。') ?></p>
  <?php endif ?>
</div>

<ul class="list-panel bca-list-panel">
  <?php if (!empty($themes)): ?>
    <?php foreach($themes as $theme): ?>
      <?php $this->BcBaser->element('Themes/index_row', ['theme' => $theme, 'currentThemeName' => $currentTheme->name]) ?>
    <?php endforeach; ?>
  <?php else: ?>
    <li class="no-data"><?php echo __d('baser', '変更できるテーマがありません。') ?>
      <br><?php echo __d('baser', '<a href="https://market.basercms.net/" target="_blank">baserマーケット</a>でテーマをダウンロードしましょう。') ?>
    </li>
  <?php endif; ?>
</ul>
