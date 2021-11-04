<?php
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
