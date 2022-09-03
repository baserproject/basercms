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
use Cake\Utility\Inflector;

/**
 * [ADMIN] テーマ一覧　行
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \BaserCore\Model\Entity\Plugin $theme
 * @var string $currentThemeName
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<li>
  <p class="theme-name"><strong><?php echo h($theme->title) ?></strong>&nbsp;(&nbsp;<?php echo $theme->name ?>&nbsp;)
  </p>
  <p class="bca-current-theme__screenshot">
    <a class="theme-popup" href="<?php echo '#Contents' . Inflector::camelize($theme->name) ?>">
      <?php if ($theme->screenshot): ?>
        <?php $this->BcBaser->img(['action' => 'screenshot', $theme->name], ['alt' => $theme->title]) ?>
      <?php else: ?>
        <?php $this->BcBaser->img('admin/no-screenshot.png', ['alt' => $theme->title]) ?>
      <?php endif ?>
    </a>
  </p>
  <p class="row-tools">
    <?php if ($theme->name !== $currentThemeName): ?>
      <?php $this->BcBaser->link('', ['action' => 'apply', $theme->name], ['title' => __d('baser', '適用'), 'class' => 'submit-token bca-btn-icon', 'data-bca-btn-type' => 'apply', 'data-bca-btn-size' => 'lg']) ?>
    <?php endif ?>
    <?php if (Configure::read('BcApp.allowedThemeEdit')): ?>
      <?php $this->BcBaser->link('', ['controller' => 'theme_files', 'action' => 'index', $theme->name], ['title' => __d('baser', 'テンプレート編集'), 'class' => 'bca-btn-icon', 'data-bca-btn-type' => 'file-list', 'data-bca-btn-size' => 'lg']) ?>
    <?php endif; ?>
    <?php $this->BcBaser->link('', ['action' => 'ajax_copy', $theme->name], ['title' => __d('baser', 'テーマコピー'), 'class' => 'btn-copy bca-btn-icon', 'data-bca-btn-type' => 'copy', 'data-bca-btn-size' => 'lg']) ?>
    <?php $this->BcBaser->link('', ['action' => 'ajax_delete', $theme->name], ['title' => __d('baser', 'テーマ削除'), 'class' => 'btn-delete bca-btn-icon', 'data-bca-btn-type' => 'delete', 'data-bca-btn-size' => 'lg']) ?>
  </p>
  <p class="theme-version"><?php echo __d('baser', 'バージョン') ?>：<?php echo $theme->version ?></p>
  <p class="theme-author"><?php echo __d('baser', '制作者') ?>：
    <?php if (!empty($theme->url) && !empty($theme->author)): ?>
      <?php $this->BcBaser->link($theme->author, $theme->url, ['target' => '_blank', 'escape' => true]) ?>
    <?php else: ?>
      <?php echo h($theme->author) ?>
    <?php endif ?>
  </p>
  <div style='display:none'>
    <div id="<?php echo 'Contents' . Inflector::camelize($theme->name) ?>" class="theme-popup-contents clearfix">
      <div class="bca-current-theme__screenshot">
        <?php if ($theme->screenshot): ?>
          <?php $this->BcBaser->img(['action' => 'screenshot', $theme->name], ['alt' => $theme->title]) ?>
        <?php else: ?>
          <?php $this->BcBaser->img('admin/no-screenshot.png', ['alt' => $theme->title]) ?>
        <?php endif ?>
      </div>
      <div class="theme-name">
        <strong><?php echo h($theme->title) ?></strong>&nbsp;(&nbsp;<?php echo $theme->name ?>&nbsp;)
      </div>
      <div class="theme-version"><?php echo __d('baser', 'バージョン') ?>：<?php echo $theme->version ?></div>
      <div class="theme-author">
        <?php echo __d('baser', '制作者') ?>：
        <?php if (!empty($theme->url) && !empty($theme->author)): ?>
          <?php $this->BcBaser->link($theme->author, $theme->url, ['target' => '_blank', 'escape' => true]) ?>
        <?php else: ?>
          <?php echo h($theme->author) ?>
        <?php endif ?>
      </div>
      <div class="theme-description"><?php echo nl2br($this->BcText->autoLinkUrls($theme->description)) ?></div>
    </div>
  </div>
</li>
