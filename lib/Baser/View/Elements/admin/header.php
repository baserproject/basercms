<?php
/**
 * [ADMIN] ヘッダー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
if (!empty($this->request->params['prefix'])) {
	$loginUrl = $this->request->params['prefix'] . '/users/login';
} else {
	$loginUrl = '/users/login';
}
?>
<div id="Header" class="clearfix">
	<?php $this->BcBaser->element('toolbar') ?>
	<?php if ($this->name == 'Installations' || ('/' . $this->request->url == Configure::read('BcAuthPrefix.admin.loginAction')) || (@$this->request->params['prefix'] == 'admin' && $this->BcAdmin->isAdminGlobalmenuUsed())): ?>
		<div class="clearfix" id="HeaderInner">

			<?php if ($this->name != 'Installations' && ('/' . $this->request->url != Configure::read('BcAuthPrefix.admin.loginAction'))): ?>
				<div id="GlobalMenu">
					<ul class="clearfix">
						<li id="GlobalMenu1"><?php $this->BcBaser->link('固定ページ管理', array('plugin' => '', 'controller' => 'pages', 'action' => 'index')) ?></li>
						<li id="GlobalMenu2"><?php $this->BcBaser->link('ウィジェット管理', array('plugin' => '', 'controller' => 'widget_areas', 'action' => 'index')) ?></li>
						<li id="GlobalMenu3"><?php $this->BcBaser->link('テーマ管理', array('plugin' => '', 'controller' => 'themes', 'action' => 'index')) ?></li>
						<li id="GlobalMenu4"><?php $this->BcBaser->link('プラグイン管理', array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')) ?></li>
						<li id="GlobalMenu5"><?php $this->BcBaser->link('システム管理', array('plugin' => '', 'controller' => 'site_configs', 'action' => 'form')) ?></li>
					</ul>
				</div>
			<?php endif ?>

			<div id="Logo">
				<?php if (!empty($user)): ?>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/logo_header.png', array('width' => 153, 'height' => 30, 'alt' => 'baserCMS')), array('plugin' => null, 'controller' => 'dashboard', 'action' => 'index')) ?>
				<?php else: ?>
					<?php $this->BcBaser->img('admin/logo_header.png', array('width' => 153, 'height' => 30, 'alt' => 'baserCMS')) ?>
				<?php endif ?>
			</div>

		</div>
	<?php endif ?>
<!-- / #Header .clearfix --></div>