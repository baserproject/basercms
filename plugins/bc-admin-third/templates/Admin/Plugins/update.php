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

use BaserCore\View\BcAdminAppView;

/**
 * プラグインアップデート
 *
 * @var BcAdminAppView $this
 * @var \Cake\Datasource\EntityInterface $plugin
 * @var bool $isCore
 * @var bool $isUpdatable
 * @var bool $coreDownloaded
 * @var bool $isDevelopmentVersion
 */
$this->BcAdmin->setTitle(__d('baser_core', 'baserCMSコア｜アップデート'));
$this->BcBaser->i18nScript([
  'confirmMessage1' => __d('baser_core', 'アップデートを実行します。よろしいですか？'),
  'updateMessage1' => __d('baser_core', '続いてアップデートを実行します。しばらくお待ちください。'),
]);
$this->BcBaser->js('admin/plugins/update.bundle', false, [
  'id' => 'AdminPluginsUpdateScript',
  'defer' => true,
  'data-plugin' => $plugin->name,
  'data-isUpdatable' => $isUpdatable
]);
?>


<div class="bca-plugin-update">
  <?php if ($isDevelopmentVersion): ?>
    <div class="bca-panel-box">
      <h2 class="bca-main__heading" data-bca-heading-size="lg">
        <?php echo __d('baser_core', '手動アップデート手順') ?>
      </h2>
      <ol>
        <li>plugins 内の、baserCMSコアのプラグイン群を手動で入れ替え</li>
        <li>BcUpdateSupporterプラグインで、アップデートスクリプトを実行</li>
        <li>BcUpdateSupporterプラグインで、データベースのバージョンを更新</li>
      </ol>
    </div>
  <?php endif ?>

  <?php $this->BcBaser->element('Plugins/update_now_status') ?>

  <?php $this->BcBaser->element('Plugins/update_alert'); ?>

  <?php if ($isCore): ?>
    <?php if ($coreDownloaded): ?>
      <?php $this->BcBaser->element('Plugins/update_core'); ?>
    <?php else: ?>
      <?php $this->BcBaser->element('Plugins/update_download_core'); ?>
    <?php endif ?>
  <?php else: ?>
    <?php $this->BcBaser->element('Plugins/update_plugin'); ?>
  <?php endif ?>

  <?php $this->BcBaser->element('Plugins/update_log'); ?>

</div>
