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
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $message エラーメッセージ
 * @var string $url URL
 */
$this->layout = 'error';
$this->BcAdmin->setTitle(__d('baser', 'ページが存在しません'));
?>


<h2><?php echo h($message) ?></h2>
<p class="error">
  <strong><?php echo __d('baser', 'エラー') ?>: </strong>
  <?php printf(
    __d('baser', 'アドレス %s に送信されたリクエストは無効です。'),
    "<strong>'{$url}'</strong>"
  ); ?>
</p>
