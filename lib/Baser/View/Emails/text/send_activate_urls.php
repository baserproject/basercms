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
$users = $this->get('users');
?>

<?php echo date('Y-m-d H:i:s') ?>　
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
◆◇ <?php echo __d('baser', 'パスワード変更リクエストを受け付けました')?> ◇◆
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

パスワード変更リクエストを受け付けました。リクエストいただいた
メールアドレスに紐付いたアカウントが複数あるため、各アカウントの
リセットURLを発行いたします。

<?php
	foreach ($users as $user) {
		echo sprintf("%s?key=%s\n", $this->get('action_url'), $user['activate_key']);
		echo sprintf("アカウント名 : %s\n\n", $user['name']);
	}
?>
パスワードをリセットしたいアカウントのリセットURLをクリックして
パスワード再発行を完了してください。
リセットURLは<?= date('m月d日H時i分', strtotime($this->get('expire'))) ?>まで有効です。
