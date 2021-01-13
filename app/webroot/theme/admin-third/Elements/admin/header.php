<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 2.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ヘッダー
 */
if (!empty($this->request->params['prefix'])) {
	$loginUrl = $this->request->params['prefix'] . '/users/login';
} else {
	$loginUrl = '/users/login';
}
?>

<header id="Header" class="bca-header">
	<?php $this->BcBaser->element('toolbar') ?>
	<?php if ($this->name === 'Installations' || ('/' . $this->request->url == Configure::read('BcAuthPrefix.admin.loginAction')) || (@$this->request->params['prefix'] === 'admin' && $this->BcAdmin->isAdminGlobalmenuUsed())): ?>
		<div id="HeaderInner" hidden>

			<?php if ($this->name !== 'Installations' && ('/' . $this->request->url != Configure::read('BcAuthPrefix.admin.loginAction'))): ?>
				<div id="GlobalMenu" hidden></div>
			<?php endif ?>

			<div id="Logo" hidden>
				<?php if (!empty($user)): ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/logo_header.png', ['width' => 153, 'height' => 30, 'alt' => 'baserCMS']), ['plugin' => null, 'controller' => 'dashboard', 'action' => 'index']) ?>
				<?php else: ?>
					<?php $this->BcBaser->img('admin/logo_header.png', ['width' => 153, 'height' => 30, 'alt' => 'baserCMS']) ?>
				<?php endif ?>
			</div>

		</div>
	<?php endif ?>
</header>
