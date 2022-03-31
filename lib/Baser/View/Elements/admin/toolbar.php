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
 * [ADMIN] ツールバー
 *
 * @var BcAppView $this
 */
// JSの出力について、ツールバーはフロントエンドでも利用するため、inlineに出力する
$this->BcBaser->js(['admin/vendors/outerClick', 'admin/vendors/jquery.fixedMenu', 'admin/toolbar']);
App::uses('AuthComponent', 'Controller/Component');
$loginUrl = '';
$currentAuthPrefix = Configure::read('BcAuthPrefix.' . $currentPrefix);
if (!empty($currentAuthPrefix['loginAction'])) {
	$loginUrl = preg_replace('/^\//', '', $currentAuthPrefix['loginAction']);
}
if (in_array('admin', $currentUserAuthPrefixes)) {
	$logoutAction = Configure::read('BcAuthPrefix.admin.logoutAction');
} else {
	$logoutAction = $currentAuthPrefix['logoutAction'];
}
if (!empty($currentAuthPrefix['name']) && $currentPrefix != 'front') {
	$authName = $currentAuthPrefix['name'];
} elseif (isset($this->BcBaser->siteConfig['formal_name'])) {
	$authName = $this->BcBaser->siteConfig['formal_name'];
} else {
	$authName = '';
}
?>


<div id="ToolBar">
	<div id="ToolbarInner" class="clearfix">
		<div id="ToolMenu">
			<ul>
				<?php if ($this->name == 'Installations'): ?>
					<li><?php $this->BcBaser->link(__d('baser', 'インストールマニュアル'), Configure::read('BcApp.outerLinks.installManual'), ['target' => '_blank', 'class' => 'tool-menu']) ?></li>
				<?php elseif (Configure::read('BcRequest.isUpdater')): ?>
					<li><?php $this->BcBaser->link(__d('baser', 'アップデートマニュアル'), Configure::read('BcApp.outerLinks.updateManual'), ['target' => '_blank', 'class' => 'tool-menu']) ?></li>
				<?php elseif (!empty($this->request->params['admin']) || ('/' . $this->request->url) == $loginUrl): ?>
					<li><?php $this->BcBaser->link($this->BcBaser->siteConfig['formal_name'], '/', ['escape' => true]) ?></li>
				<?php else: ?>
					<?php if (in_array('admin', $currentUserAuthPrefixes)): ?>
						<li><?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_logo.png', ['alt' => __d('baser', 'baserCMS管理システム'), 'class' => 'bc-btn']), ['plugin' => null, 'admin' => true, 'controller' => 'dashboard', 'action' => 'index'], ['title' => __d('baser', 'baserCMS管理システム')]) ?></li>
					<?php else: ?>
						<li><?php $this->BcBaser->link(h($authName), Configure::read('BcAuthPrefix.' . $currentPrefix . '.loginRedirect'), ['title' => h($authName)]) ?></li>
					<?php endif ?>
				<?php endif ?>
				<?php if ($this->BcBaser->existsEditLink() && !isset($this->request->query['preview'])): ?>
					<li><?php $this->BcBaser->editLink() ?></li>
				<?php endif ?>
				<?php if ($this->BcBaser->existsPublishLink()): ?>
					<li><?php $this->BcBaser->publishLink() ?></li>
				<?php endif ?>
				<?php if (!$loginUrl || $this->request->url != $loginUrl): ?>
					<?php if (Configure::read('debug') == -1 && $this->name != "Installations"): ?>
						<li>&nbsp;&nbsp;<span id="DebugMode"
											  title="<?php echo __d('baser', 'インストールモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。') ?>"><?php echo __d('baser', 'インストールモード') ?></span>&nbsp;&nbsp;
						</li>
					<?php elseif (Configure::read('debug') > 0): ?>
						<li>&nbsp;&nbsp;<span id="DebugMode"
											  title="<?php echo __d('baser', 'デバッグモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。') ?>"><?php echo __d('baser', 'デバッグモード') ?><?php echo mb_convert_kana(Configure::read('debug'), 'N') ?></span>&nbsp;&nbsp;
						</li>
					<?php endif; ?>
				<?php endif ?>
			</ul>
		</div>
		<div id="UserMenu">
			<ul class="clearfix">
				<li>
					<?php if (!empty($user)): ?>
						<?php $this->BcBaser->link(h($this->BcBaser->getUserName($user)) . ' ' . $this->BcBaser->getImg('admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']), 'javascript:void(0)', ['class' => 'title']) ?>
						<ul>
							<?php if ($this->Session->check('AuthAgent')): ?>
								<li><?php $this->BcBaser->link(__d('baser', '元のユーザーに戻る'), '/users/back_agent') ?></li>
							<?php endif ?>
							<?php if (in_array('admin', $currentUserAuthPrefixes)): ?>
								<li><?php $this->BcBaser->link(__d('baser', 'アカウント設定'), ['admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['id']]) ?></li>
							<?php else: ?>
								<?php if ($currentPrefix != 'front'): ?>
									<li><?php $this->BcBaser->link(__d('baser', 'アカウント設定'), [$currentPrefix => true, 'plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['id']]) ?></li>
								<?php endif ?>
							<?php endif ?>
							<li><?php $this->BcBaser->link(__d('baser', 'ログアウト'), $logoutAction) ?></li>
						</ul>
					<?php elseif ($this->name != 'Installations' && $this->request->url != $loginUrl && !Configure::read('BcRequest.isUpdater')): ?>
						<?php $this->BcBaser->link(__d('baser', 'ログインしていません ') . $this->BcBaser->getImg('admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']), 'javascript:void(0)', ['class' => 'title']) ?>
						<ul>
							<?php if ($currentPrefix == 'front'): ?>
								<li><?php $this->BcBaser->link(__d('baser', 'ログイン'), ['plugin' => null, 'controller' => 'users', 'action' => 'login']) ?></li>
							<?php else: ?>
								<li><?php $this->BcBaser->link(__d('baser', 'ログイン'), [$currentPrefix => true, 'plugin' => null, 'controller' => 'users', 'action' => 'login']) ?></li>
							<?php endif ?>
						</ul>
					<?php endif ?>
				</li>
				<?php if (!empty($user) && in_array('admin', $currentUserAuthPrefixes) && Configure::read('BcApp.adminNavi')): ?>
					<li>
						<?php $this->BcBaser->link(__d('baser', 'システムナビ') . ' ' . $this->BcBaser->getImg('admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']), 'javascript:void(0)', ['class' => 'title']) ?>
						<div id="SystemMenu">
							<div>
								<?php
								$adminSitemap = Configure::read('BcApp.adminNavi');
								$isAdminGlobalmenuUsed = $this->BcAdmin->isAdminGlobalmenuUsed();
								?>
								<?php foreach($adminSitemap as $key => $package): ?>
									<?php
									if (!$isAdminGlobalmenuUsed && $key == 'core') {
										continue;
									}
									?>
									<?php if (empty($package['name'])): ?>
										<?php $package['name'] = $key ?>
									<?php endif ?>
									<h2><?php echo $package['name'] ?></h2>
									<?php if (!empty($package['contents'])): ?>
										<ul class="clearfix">
											<?php foreach($package['contents'] as $contents): ?>
												<?php
												$options = ['title' => $contents['name'], 'escape' => true];
												if (!empty($contents['options'])) {
													$options = array_merge($options, $contents['options']);
												}
												?>
												<li><?php $this->BcBaser->link($contents['name'], $contents['url'], $options) ?></li>
											<?php endforeach ?>
										</ul>
									<?php endif ?>
								<?php endforeach ?>
							</div>
						</div>
					</li>
				<?php endif ?>
			</ul>
		</div>
	</div>
</div>
