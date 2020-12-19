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

use BaserCore\View\AppView;

/**
 * @var AppView $this
 */
?>

<div class="section">
	<p><?php echo __d('baser', 'パスワードを変更しました。')?></p>
    <p><?php echo $this->BcBaser->link(__d('baser', 'ログイン'), ['controller' => 'users', 'action' => 'login']); ?> </p>
</div>
