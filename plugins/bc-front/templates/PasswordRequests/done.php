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
$this->BcAdmin->setTitle(__d('baser_core', 'パスワードのリセット完了'));
?>

<h2 class="bs-contents-title"><?php echo $this->BcBaser->getContentsTitle() ?></h2>

<div class="section">
  <p><?php echo __d('baser_core', 'パスワードを変更しました。') ?></p>
  <p>
    <?php $this->BcBaser->link(
      __d('baser_core', 'ログイン'), ['controller' => 'users', 'action' => 'login'],
      ['class' => 'bca-btn', 'data-bca-btn-size' => 'lg', 'data-bca-btn-width' => 'lg']
    ) ?>
  </p>
</div>
