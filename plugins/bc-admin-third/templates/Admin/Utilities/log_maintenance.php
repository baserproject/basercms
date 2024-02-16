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
 * [ADMIN] ログメンテナンス
 * @var \BaserCore\View\BcAdminAppView $this
 * @var int $fileSize
 * @checked
 * @noTodo
 * @unitTest
 */
$this->BcAdmin->setTitle(__d('baser_core', 'データメンテナンス'));
$this->BcAdmin->setHelp('tools_log');
?>


<?php if ($zipEnable): ?>
  <div class="section bca-main__section">
    <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', 'ログの取得') ?></h2>
    <p class="bca-main__text"><?php echo __d('baser_core', 'ログをPCにダウンロードします。') ?></p>
    <p class="bca-main__text">
      <?php $this->BcBaser->link(
        __d('baser_core', 'ダウンロード'),
        ['download'],
        ['class' => 'bca-btn', 'data-bca-btn-type' => 'download']
      ) ?>
    </p>
  </div>
<?php endif; ?>

<div class="section bca-main__section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', 'ログの削除') ?></h2>

  <p class="bca-main__text"><?php echo __d('baser_core', 'ログを削除します。サーバの容量を圧迫する場合時などに利用ください。') ?><br>
    <?php echo sprintf(__d('baser_core', 'ログのサイズは、%sMBです。'), number_format($fileSize / 1000000, 2)) ?>
  </p>
  <p class="bca-main__text">
    <?php echo $this->BcAdminForm->postLink(__d('baser_core', '削除'), ['delete'], [
      'class' => 'bca-btn',
      'data-bca-btn-type' => 'delete',
      'confirm' => __d('baser_core', 'ログを削除します。いいですか？'),
      'data-bca-btn-color' => "danger"
    ]) ?>
  </p>
</div>
