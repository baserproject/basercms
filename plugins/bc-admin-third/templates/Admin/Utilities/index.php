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
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */

$this->BcAdmin->setTitle(__d('baser_core', 'ユーティリティトップ'));
$this->BcBaser->js('admin/utilities/index.bundle', false);
?>


<div class="section bca-main__section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', 'サーバーキャッシュ削除') ?></h2>
  <p class="bca-main__text">
    <?php echo __d('baser_core', 'baserCMSは、表示速度向上のため、サーバーサイドのキャッシュ機構を利用しています。<br>これによりテンプレートを直接編集した際など、変更内容が反映されない場合がありますので、その際には、サーバーサイドのキャッシュを削除します。') ?>
  </p>
  <?php echo $this->BcAdminForm->postLink(__d('baser_core', 'サーバーキャッシュを削除する'), ['controller' => 'utilities', 'action' => 'clear_cache'], [
    'class' => 'bca-submit-token bca-btn',
    'data-bca-btn-type' => 'clear',
    'confirm' => __d('baser_core', 'サーバーキャッシュを削除します。いいですか？')
  ]) ?>
</div>

<div class="section bca-main__section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', 'コンテンツ管理') ?></h2>
  <p class="bca-main__text">
    <?php echo __d('baser_core', 'コンテンツ管理のツリー構造で並べ替えがうまくいかなくなった場合に、ツリー構造をリセットして正しいデータの状態に戻します。リセットを実行した場合、階層構造はリセットされてしまうのでご注意ください。') ?>
  </p>
  <?php echo $this->BcAdminForm->postLink(
    __d('baser_core', 'ツリー構造をチェックする'),
    ['controller' => 'utilities', 'action' => 'verity_contents_tree'],
    ['class' => 'bca-btn']
  ) ?>&nbsp;&nbsp;
  <?php echo $this->BcAdminForm->postLink(
    __d('baser_core', 'ツリー構造リセット'),
    ['controller' => 'utilities', 'action' => 'reset_contents_tree'],
    ['class' => 'bca-btn', 'confirm' => __d('baser_core', 'コンテンツ管理のツリー構造をリセットします。本当によろしいですか？')]
  ) ?>
</div>

<div class="section bca-main__section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', 'アセットファイル') ?></h2>
  <p class="bca-main__text">
    <?php echo __d('baser_core', 'テーマ・プラグインのアセットファイルへのシンボリックリンクを再作成します。別環境への移設などで、テーマやプラグインの画像、CSS、Javascriptへのリンク切れが発生した際に利用します。') ?>
  </p>
  <?php echo $this->BcAdminForm->postLink(__d('baser_core', 'シンボリックリンクを再作成する'),
    [
      'controller' => 'utilities',
      'action' => 'assets_symlink'
    ],
    [
      'class' => 'bca-submit-token bca-btn',
      'data-bca-btn-type' => 'clear',
      'confirm' => __d('baser_core', 'テーマ・プラグインへのシンボリックリンクを再作成します。本当によろしいですか？')
    ]) ?>
</div>

<?php echo $this->BcAdminForm->secure() ?>
<div class="section bca-main__section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', 'スペシャルサンクスクレジット') ?></h2>
  <p class="bca-main__text"><?php echo __d('baser_core', 'baserCMSの開発や運営、普及にご協力頂いた方々をご紹介します。') ?></p>
  <?php $this->BcBaser->link(__d('baser_core', 'クレジットを表示'), 'javascript:void(0)', ['class' => 'bca-btn', 'id' => 'BtnCredit']) ?>
</div>
