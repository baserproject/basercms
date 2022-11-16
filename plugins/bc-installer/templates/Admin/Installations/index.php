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
 * [PUBLISH] インストーラー初期ページ
 * @var \BaserCore\View\BcAdminAppView $this
 */
$this->BcAdmin->setTitle(__d('baser', 'baserCMSのインストール'));
?>


<div class="step-1">

  <div class="em-box bca-em-box">
    <?php echo sprintf(__d('baser', '%s のインストールを開始します。<br>よろしければ「インストール開始」ボタンをクリックしてください。'), \Cake\Core\Configure::read('BcApp.title')) ?>
  </div>

  <div class="section bca-section">
    <p class="bca-main__text">
      <?php echo __d('baser', 'baserCMSではファイルベースのデータベースをサポートしています。<br>SQLite３ を利用すれば、インストールにデータベースサーバーは必要ありません。') ?>
    </p>
    <p class="bca-main__text">
      <small>※ <?php echo __d('baser', '膨大なデータの操作、データベースによる複雑な処理が必要な場合は、MySQL または PostgreSQL の利用を推奨します。') ?></small>
    </p>
  </div>
  <div class="submit bca-actions">
    <?php echo $this->BcAdminForm->create(null, ['url' => ['action' => 'step2'], 'type' => 'post']) ?>
    <?php echo $this->BcAdminForm->button('<span>' . __d('baser', 'インストール開始') . '</span>', [
      'class' => 'bca-btn bca-actions__item bca-loading',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
      'data-bca-btn-type' => 'save',
      'escapeTitle' => false
      ]) ?>
    <?php echo $this->BcAdminForm->end() ?>
  </div>

</div>
