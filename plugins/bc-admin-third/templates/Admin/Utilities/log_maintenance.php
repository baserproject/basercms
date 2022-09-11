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
$this->BcAdmin->setTitle(__d('baser', 'データメンテナンス'));
$this->BcAdmin->setHelp('tools_log');
?>


<div class="section bca-main__section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', 'ログ(エラーログ)の取得') ?></h2>
  <p class="bca-main__text"><?php echo __d('baser', 'ログ(エラーログ)をPCにダウンロードします。') ?></p>
  <p class="bca-main__text">
    <?php $this->BcBaser->link(
      __d('baser', 'ダウンロード'),
      ['download'],
      ['class' => 'bca-btn', 'data-bca-btn-type' => 'download']
    ) ?>
  </p>
</div>

<div class="section bca-main__section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser', 'エラーログの削除') ?></h2>

  <p class="bca-main__text"><?php echo __d('baser', 'エラーログを削除します。サーバの容量を圧迫する場合時などに利用ください。') ?><br>
    <?php echo sprintf(__d('baser', 'エラーログのサイズは、%sbytesです。'), number_format($fileSize)) ?>
  </p>
  <p class="bca-main__text">
    <?php echo $this->BcAdminForm->postLink(__d('baser', '削除'), ['delete'], [
      'class' => 'bca-btn',
      'data-bca-btn-type' => 'delete',
      'confirm' => __d('baser', 'エラーログを削除します。いいですか？')
    ]) ?>
  </p>
</div>
