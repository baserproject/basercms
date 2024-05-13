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
 * [ADMIN] プラグイン一覧　行
 *
 * @var AppView $this
 * @var array $corePlugins
 * @var \BaserCore\Model\Entity\Plugin $plugin
 * @checked
 * @unitTest
 * @noTodo
 */

/**
 * @var AppView $this
 * @var int $count
 */

use BaserCore\View\AppView;

$classies = ['sortable'];
if (!$plugin->status) {
  $classies[] = 'disablerow';
}
$class = ' class="' . implode(' ', $classies) . '"';
?>


<tr id="Row<?= h($count) ?>" <?= $class ?>>
  <td class="row-tools bca-table-listup__tbody-td" nowrap>
    <?php if ($this->BcBaser->isAdminUser()): ?>
      <?php echo $this->BcAdminForm->control('batch_targets.' . $plugin->id, [
        'type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">' . __d('baser_core', 'チェックする') . '</span>',
        'class' => 'batch-targets bca-checkbox__input',
        'escape' => false,
        'value' => $plugin->id?? 0
      ]) ?>
    <?php endif ?>
    <?php if ($this->request->getQuery('sortmode')): ?>
      <span class="sort-handle"><i class="bca-btn-icon-text"
                                  data-bca-btn-type="draggable"></i><?php echo __d('baser_core', 'ドラッグ可能') ?></span>
      <?php echo $this->BcAdminForm->control('id' . $plugin->id, ['type' => 'hidden', 'class' => 'id', 'value' => $plugin->id]) ?>
    <?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td" style="min-width:150px;">
    <?php if ($plugin->old_version): ?>
      <div class="annotation-text"><small><?php echo __d('baser_core', '新しいバージョンにアップデートしてください') ?></small></div>
    <?php elseif ($plugin->update): ?>
      <div class="annotation-text"><small><?php echo __d('baser_core', 'アップデートを完了させてください') ?></small></div>
    <?php endif ?>
    <?php echo h($plugin->name) ?><?php if ($plugin->title): ?>（<?php echo h($plugin->title) ?>）<?php endif ?>
  </td>
  <td class="bca-table-listup__tbody-td"><?php echo h($plugin->version) ?></td>
  <?php if(!$this->request->getQuery('sortmode')): ?>
  <td class="bca-table-listup__tbody-td" style="min-width:200px;"><?php echo h($plugin->description) ?></td>
  <td class="bca-table-listup__tbody-td">
    <?php if ($plugin->author): ?>
      <?php if (!$plugin->url): ?>
        <?php echo h($plugin->author) ?>
      <?php else: ?>
        <?php $this->BcBaser->link($plugin->author, $plugin->url, ['target' => '_blank', 'escape' => true]) ?>
      <?php endif ?>
    <?php endif ?>
  </td>
  <?php endif ?>
  <td class="bca-table-listup__tbody-td" style="width:10%;white-space: nowrap">
    <?php echo $this->BcTime->format($plugin->created, 'yyyy-MM-dd') ?><br/>
    <?php echo $this->BcTime->format($plugin->modified, 'yyyy-MM-dd') ?>
  </td>
  <td class="bca-table-listup__tbody-td">
    <?php if ($plugin->update): ?>
      <?php $this->BcBaser->link('', ['controller' => 'plugins', 'action' => 'update', $plugin->name], [
        'aria-label' => __d('baser_core', 'このプラグインをアップデートする'),
        'title' => __d('baser_core', 'アップデート'),
        'class' => 'btn-update bca-btn-icon',
        'data-bca-btn-type' => 'update',
        'data-bca-btn-size' => 'lg'
      ]); ?>
    <?php endif ?>
    <?php if ($plugin->adminLink && $plugin->status && !$plugin->update && !$plugin->old_version): ?>
      <?php $this->BcBaser->link('', $plugin->adminLink, [
        'aria-label' => __d('baser_core', 'このプラグインの設定を行う'),
        'title' => __d('baser_core', '管理'),
        'class' => 'btn-setting  bca-btn-icon',
        'data-bca-btn-type' => 'setting',
        'data-bca-btn-size' => 'lg'
      ]); ?>
    <?php endif; ?>
    <?php if ($plugin->status): ?>
      <?= $this->BcAdminForm->postLink(
        '',
        ['action' => 'detach', $plugin->name],
        ['block' => true,
          'confirm' => __d('baser_core', "本当に無効にしてもいいですか？\nプラグインフォルダ内のファイル、データベースに保存した情報は削除されずそのまま残ります。"),
          'title' => __d('baser_core', '無効'),
          'class' => 'btn-delete bca-btn-icon',
          'data-bca-btn-type' => 'unpublish',
          'data-bca-btn-size' => 'lg']
      ) ?>
    <?php elseif (!$plugin->status && !$plugin->update && !$plugin->old_version): ?>
      <?php $this->BcBaser->link('',
        ['action' => 'install', $plugin->name], [
          'aria-label' => __d('baser_core', 'インストールする'),
          'title' => __d('baser_core', 'インストール'),
          'class' => 'bca-btn-icon',
          'data-bca-btn-type' => 'download',
          'data-bca-btn-size' => 'lg'
        ]); ?>
    <?php endif ?>
    <?php if (!$plugin->status): ?>
      <?= $this->BcAdminForm->postLink(
        '',
        ['action' => 'uninstall', $plugin->name],
        [
          'confirm' => __d('baser_core', "本当に削除してもいいですか？\nプラグインフォルダ内のファイル、データベースのデータも全て削除されます。"),
          'title' => __d('baser_core', '削除'),
          'class' => 'btn-delete bca-btn-icon',
          'data-bca-btn-type' => 'delete',
          'data-bca-btn-size' => 'lg']
      ) ?>
    <?php endif; ?>
  </td>
</tr>
