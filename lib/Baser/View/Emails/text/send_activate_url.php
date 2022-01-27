<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.4.3
 * @license			https://basercms.net/license/index.html
 */

/**
 * [EMAIL] パスワードのリセットメール
 */
$user = $this->get('users');
?>
<?php echo date('Y-m-d H:i:s') ?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
◆◇ <?php echo __d('baser', 'パスワード変更リクエストを受け付けました')?> ◇◆
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

パスワード変更リクエストを受け付けました。下記のリセットURLを
ブラウザで開き、パスワード再発行を完了してください。

<?= sprintf("%s?key=%s\n", $this->get('action_url'), $user['activate_key']) ?>
<?= sprintf("アカウント名 : %s\n", $user['name']) ?>

リセットURLは<?= date('m月d日H時i分', strtotime($this->get('expire'))) ?>まで有効です。
