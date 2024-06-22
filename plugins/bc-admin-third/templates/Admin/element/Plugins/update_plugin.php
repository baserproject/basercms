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
 * プラグインアップデート
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \Cake\Datasource\EntityInterface $plugin
 * @var string $programVersion
 * @var string $availableVersion
 * @var bool $isUpdatable
 */
$this->BcAdmin->setTitle(__d('baser_core', '{0}｜アップデート', $plugin->title . __d('baser_core', 'プラグイン')));
?>


<div class="bca-panel-box">
  <h2 class="bca-main__heading" data-bca-heading-size="lg">
    <?php echo __d('baser_core', 'アップデート実行') ?>
  </h2>
  <?php if ($isUpdatable): ?>
    <p><?php echo __d('baser_core', '「アップデート実行」をクリックしてプラグインのアップデートを完了させてください。') ?></p>
  <?php else: ?>
    <p><?php echo __d('baser_core', '利用可能なアップデートはありません。') ?></p>
  <?php endif ?>

  <?php echo $this->BcAdminForm->create($plugin, [
    'id' => 'PluginUpdateForm'
  ]) ?>
  <?php echo $this->BcAdminForm->control('update', ['type' => 'hidden', 'value' => true]) ?>
  <?php echo $this->BcAdminForm->control('currentVersion', ['type' => 'hidden', 'value' => $programVersion]) ?>
  <?php echo $this->BcAdminForm->control('targetVersion', ['type' => 'hidden', 'value' => $availableVersion]) ?>

  <div class="bca-actions">
    <div class="bca-actions__before">
      <?php echo $this->BcHtml->link(__d('baser_core', '一覧に戻る'), [
        'plugin' => 'BaserCore',
        'controller' => 'Plugins',
        'action' => 'index',
      ], [
        'class' => 'bca-btn bca-actions__item',
        'data-bca-btn-type' => 'back-to-list'
      ]) ?>
    </div>
    <div class="bca-actions__main">
      <?php echo $this->BcAdminForm->submit(__d('baser_core', 'アップデート実行'), [
        'class' => 'bca-btn bca-actions__item',
        'data-bca-btn-size' => 'lg',
        'data-bca-btn-width' => 'lg',
        'data-bca-btn-type' => 'save',
        'id' => 'BtnUpdate',
        'div' => false
      ]) ?>
    </div>
  </div>
  <?php echo $this->BcAdminForm->end() ?>
</div>

