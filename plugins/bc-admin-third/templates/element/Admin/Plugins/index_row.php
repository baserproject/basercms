<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

/**
 * [ADMIN] プラグイン一覧　行
 *
 * @var AppView $this
 * @var bool $sortmode
 * @var array $corePlugins
 * @var \BaserCore\Model\Entity\Plugin $plugin
 */

use BaserCore\View\AppView;

$classies = ['sortable'];
if (!$plugin->status) {
    $classies[] = 'disablerow';
}
$class = ' class="' . implode(' ', $classies) . '"';
?>


<tr<?php echo $class; ?>>
    <td class="row-tools bca-table-listup__tbody-td">
        <?php if ($this->BcBaser->isAdminUser()): ?>
            <?php echo $this->BcForm->control('ListTool.batch_targets.' . $plugin->id, ['type' => 'checkbox', 'label' => '<span class="bca-visually-hidden">' . __d('baser', 'チェックする') . '</span>', 'class' => 'batch-targets bca-checkbox__input', 'value' => $plugin->id]) ?>
        <?php endif ?>
        <?php if ($sortmode): ?>
            <span class="sort-handle"><i class="bca-btn-icon-text"
                                         data-bca-btn-type="draggable"></i><?php echo __d('baser', 'ドラッグ可能') ?></span>
            <?php echo $this->BcForm->control('Sort.id' . $plugin->id, ['type' => 'hidden', 'class' => 'id', 'value' => $plugin->id]) ?>
        <?php endif ?>
    </td>
    <td class="bca-table-listup__tbody-td">
        <?php if ($plugin->old_version): ?>
            <div class="annotation-text"><small><?php echo __d('baser', '新しいバージョンにアップデートしてください') ?></small></div>
        <?php elseif ($plugin->update): ?>
            <div class="annotation-text"><small><?php echo __d('baser', 'アップデートを完了させてください') ?></small></div>
        <?php endif ?>
        <?php echo h($plugin->name) ?><?php if ($plugin->title): ?>（<?php echo h($plugin->title) ?>）<?php endif ?>
    </td>
    <td class="bca-table-listup__tbody-td"><?php echo $plugin->version ?></td>
    <td class="bca-table-listup__tbody-td"><?php echo h($plugin->description) ?></td>
    <td class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($plugin->author, $plugin->url, ['target' => '_blank']) ?></td>
    <td class="bca-table-listup__tbody-td" style="width:10%;white-space: nowrap">
        <?php echo $this->BcTime->format($plugin->created, 'YYYY-MM-dd') ?><br/>
        <?php echo $this->BcTime->format($plugin->modified, 'YYYY-MM-dd') ?>
    </td>
    <td class="bca-table-listup__tbody-td">
        <?php if ($plugin->update): ?>
            <?php $this->BcBaser->link('', ['controller' => 'updaters', 'action' => 'plugin', $plugin->name], [
                'aria-label' => __d('baser', 'このプラグインをアップデートする'),
                'title' => __d('baser', 'アップデート'),
                'class' => 'btn-update bca-btn-icon',
                'data-bca-btn-type' => 'update',
                'data-bca-btn-size' => 'lg'
            ]); ?>
        <?php endif ?>
        <?php if ($plugin->admin_link && $plugin->status && !$plugin->update && !$plugin->old_version): ?>
            <?php $this->BcBaser->link('', $plugin->admin_link, [
                'aria-label' => 'このプラグインの設定を行う',
                'title' => __d('baser', '管理'),
                'class' => 'btn-setting  bca-btn-icon',
                'data-bca-btn-type' => 'setting',
                'data-bca-btn-size' => 'lg'
            ]); ?>
        <?php endif; ?>
        <?php if ($plugin->status): ?>
            <?php $this->BcBaser->link('', ['action' => 'ajax_delete', $plugin->name], [
                'aria-label' => __d('baser', 'このプラグインを無効にする'),
                'title' => __d('baser', '無効'),
                'class' => 'btn-delete bca-btn-icon',
                'data-bca-btn-type' => 'unpublish',
                'data-bca-btn-size' => 'lg'
            ]); ?>
        <?php elseif (!$plugin->status && !$plugin->update && !$plugin->old_version): ?>
            <?php $this->BcBaser->link('',
                ['action' => 'install', $plugin->name], [
                    'aria-label' => __d('baser', 'インストールする'),
                    'title' => __d('baser', 'インストール'),
                    'class' => 'bca-btn-icon',
                    'data-bca-btn-type' => 'download',
                    'data-bca-btn-size' => 'lg'
                ]); ?>
        <?php endif ?>
        <?php if (!$plugin->status): ?>
            <?php if (!in_array($plugin->name, $corePlugins)): ?>
                <?php $this->BcBaser->link('',
                    ['action' => 'ajax_delete_file', $plugin->name], [
                        'aria-label' => __d('baser', 'このプラグインを削除する'),
                        'title' => __d('baser', '削除'),
                        'class' => 'btn-delete bca-btn-icon',
                        'data-bca-btn-type' => 'delete',
                        'data-bca-btn-size' => 'lg'
                    ]); ?>
            <?php endif ?>
        <?php endif; ?>
    </td>
</tr>
