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
 * パスワード再発行完了メール
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @checked
 * @noTodo
 * @unitTest
 * @var string $url
 * @var string $limit
 */
?>

<?php echo date('Y-m-d H:i:s') ?>　
<?php echo __d('baser_core', 'パスワードの再発行手続きを受け付けました。') . "\n"; ?>
<?php echo __d('baser_core', '下記のURLにアクセスし新しいパスワードを登録してください。') . "\n"; ?>

URL: <?php echo $url . "\n" ?>
有効時間: <?php echo $limit . "\n" ?>
