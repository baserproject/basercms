<?php
/**
 * [ADMIN] ツールバー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
App::uses('AuthComponent', 'Controller/Component');
$this->BcBaser->js(array('admin/outerClick', 'admin/jquery.fixedMenu'));
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
<script type="text/javascript">
$(function(){
	$('#BcUserMenu').fixedMenu();
	$('#SystemMenu h2').click(function(){
		if($(this).next().css('display')=='none') {
			$(this).next().slideDown(200);
		} else {
			$(this).next().slideUp(200);
		}
	});
	$('#SystemMenu ul:first').show();
	$("#BcUserMenu ul li div ul li").each(function(){
		if(!$(this).html().replace(/(^\s+)|(\s+$)/g, "")) {
			$(this).remove();
		}
	});
	$("#BcUserMenu ul li div ul").each(function(){
		if(!$(this).html().replace(/(^\s+)|(\s+$)/g, "")) {
			$(this).prev().remove();
			$(this).remove();
		}
	});
});
</script>

<div id="ToolBar">
	<div id="ToolbarInner" class="clearfix">
		<div id="ToolMenu">
			<ul>
				<?php if ($this->name == 'Installations'): ?>
					<li><?php $this->BcBaser->link('インストールマニュアル', 'http://basercms.net/manuals/introductions/4.html', array('target' => '_blank')) ?></li>
				<?php elseif (Configure::read('BcRequest.isUpdater')): ?>
					<li><?php $this->BcBaser->link('アップデートマニュアル', 'http://basercms.net/manuals/introductions/8.html', array('target' => '_blank')) ?></li>
				<?php elseif (!empty($this->request->params['admin']) || ('/' . $this->request->url) == $loginUrl): ?>	
					<li><?php $this->BcBaser->link($this->BcBaser->siteConfig['formal_name'], '/') ?></li>
				<?php else: ?>
					<?php if (in_array('admin', $currentUserAuthPrefixes)): ?>
						<li><?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_logo.png', array('alt' => 'baserCMS管理システム', 'class' => 'bc-btn')), array('plugin' => null, 'admin' => true, 'controller' => 'dashboard', 'action' => 'index'), array('title' => 'baserCMS管理システム')) ?></li>
					<?php else: ?>
						<li><?php $this->BcBaser->link($authName, Configure::read('BcAuthPrefix.' . $currentPrefix . '.loginRedirect'), array('title' => $authName)) ?></li>
					<?php endif ?>
				<?php endif ?>
				<?php if ($this->BcBaser->existsEditLink()): ?>
					<li><?php $this->BcBaser->editLink() ?></li>
				<?php endif ?>
				<?php if ($this->BcBaser->existsPublishLink()): ?>
					<li><?php $this->BcBaser->publishLink() ?></li>
				<?php endif ?>
				<?php if (!$loginUrl || $this->request->url != $loginUrl): ?>
					<?php if (Configure::read('debug') == -1 && $this->name != "Installations"): ?>
						<li>&nbsp;&nbsp;<span id="DebugMode" title="インストールモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。">インストールモード</span>&nbsp;&nbsp;</li>
					<?php elseif (Configure::read('debug') > 0): ?>
						<li>&nbsp;&nbsp;<span id="DebugMode" title="デバッグモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。">デバッグモード<?php echo mb_convert_kana(Configure::read('debug'), 'N') ?></span>&nbsp;&nbsp;</li>
					<?php endif; ?>
				<?php endif ?>
			</ul>
		</div>
		<div id="BcUserMenu">
			<ul class="clearfix">
				<li>
					<?php if (!empty($user)): ?>
						<?php $this->BcBaser->link($this->BcBaser->getUserName($user) . ' ' . $this->BcBaser->getImg('admin/btn_dropdown.png', array('width' => 8, 'height' => 11, 'class' => 'bc-btn')), 'javascript:void(0)', array('class' => 'title')) ?>
						<ul>
							<?php if ($this->Session->check('AuthAgent')): ?>
								<li><?php $this->BcBaser->link('元のユーザーに戻る', array('admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'back_agent')) ?></li>
							<?php endif ?>
							<?php if (in_array('admin', $currentUserAuthPrefixes)): ?>
								<li><?php $this->BcBaser->link('アカウント設定', array('admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['id'])) ?></li>
							<?php else: ?>
                                <?php if ($currentPrefix != 'front'): ?>
								<li><?php $this->BcBaser->link('アカウント設定', array($currentPrefix => true, 'plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['id'])) ?></li>
								<?php endif ?>
							<?php endif ?>
							<li><?php $this->BcBaser->link('ログアウト', $logoutAction) ?></li>
						</ul>
					<?php elseif ($this->name != 'Installations' && $this->request->url != $loginUrl && !Configure::read('BcRequest.isUpdater')): ?>
						<?php $this->BcBaser->link('ログインしていません ' . $this->BcBaser->getImg('admin/btn_dropdown.png', array('width' => 8, 'height' => 11, 'class' => 'bc-btn')), 'javascript:void(0)', array('class' => 'title')) ?>
						<ul>
							<?php if ($currentPrefix == 'front'): ?>
								<li><?php $this->BcBaser->link('ログイン', array('plugin' => null, 'controller' => 'users', 'action' => 'login')) ?></li>
							<?php else: ?>
								<li><?php $this->BcBaser->link('ログイン', array($currentPrefix => true, 'plugin' => null, 'controller' => 'users', 'action' => 'login')) ?></li>
							<?php endif ?>
						</ul>
					<?php endif ?>
				</li>
				<?php if (!empty($user) && in_array('admin', $currentUserAuthPrefixes)): ?>
					<li>
						<?php $this->BcBaser->link('システムナビ' . ' ' . $this->BcBaser->getImg('admin/btn_dropdown.png', array('width' => 8, 'height' => 11, 'class' => 'bc-btn')), 'javascript:void(0)', array('class' => 'title')) ?>
						<div id="SystemMenu"><div>
								<?php $adminSitemap = Configure::read('BcApp.adminNavi') ?>
								<?php foreach ($adminSitemap as $key => $package): ?>
									<?php if (empty($package['name'])): ?>
										<?php $package['name'] = $key ?>
									<?php endif ?>
									<h2><?php echo $package['name'] ?></h2>
									<?php if (!empty($package['contents'])): ?>
										<ul class="clearfix">
											<?php foreach ($package['contents'] as $contents): ?>
												<li><?php $this->BcBaser->link($contents['name'], $contents['url'], array('title' => $contents['name'])) ?></li>
											<?php endforeach ?>
										</ul>
									<?php endif ?>
								<?php endforeach ?>
							</div></div>
					</li>
				<?php endif ?>
			</ul>
		</div>
	</div>
</div>
