<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use Cake\Core\Configure;
?>


<header id="Header" class="bca-header">
<!--	--><?php //$this->BcBaser->element('admin/toolbar') ?>
	<?php if ($this->name == 'Installations' || ('/' . $this->request->getPath() == Configure::read('BcAuthPrefix.admin.loginAction')) || (@$this->request->params['prefix'] == 'admin' && $this->BcAdmin->isAdminGlobalmenuUsed())): ?>
		<div id="HeaderInner" hidden>

			<?php if ($this->name != 'Installations' && ('/' . $this->request->url != Configure::read('BcAuthPrefix.admin.loginAction'))): ?>
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
