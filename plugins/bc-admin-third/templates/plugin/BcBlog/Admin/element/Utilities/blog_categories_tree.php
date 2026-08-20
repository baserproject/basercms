<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * [ADMIN] ユーティリティ ブログカテゴリのツリー構造チェック・リセット
 * @var \BaserCore\View\BcAdminAppView $this
 * @checked
 * @noTodo
 * @unitTest
 */
?>


<div class="section bca-main__section">
  <h2 class="bca-main__heading" data-bca-heading-size="lg"><?php echo __d('baser_core', 'ブログカテゴリ') ?></h2>
  <p class="bca-main__text">
    <?php echo __d('baser_core', 'ブログカテゴリのツリー構造で並べ替えがうまくいかなくなった場合に、ツリー構造をリセットして正しいデータの状態に戻します。リセットを実行した場合、階層構造はリセットされてしまうのでご注意ください。') ?>
  </p>
  <?php echo $this->BcAdminForm->postLink(
    __d('baser_core', 'ツリー構造をチェックする'),
    ['plugin' => 'BcBlog', 'controller' => 'BlogCategories', 'action' => 'verity_tree'],
    ['class' => 'bca-btn']
  ) ?>&nbsp;&nbsp;
  <?php echo $this->BcAdminForm->postLink(
    __d('baser_core', 'ツリー構造リセット'),
    ['plugin' => 'BcBlog', 'controller' => 'BlogCategories', 'action' => 'reset_tree'],
    ['class' => 'bca-btn', 'confirm' => __d('baser_core', 'ブログカテゴリのツリー構造をリセットします。本当によろしいですか？')]
  ) ?>
</div>
