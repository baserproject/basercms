<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] ツールバー
 */
// inlineで出力しないとプルダウンが動作しない
$this->BcBaser->js(['admin/vendors/outerClick', 'admin/vendors/jquery.fixedMenu', 'admin/toolbar'], true, ['defer']);
App::uses('AuthComponent', 'Controller/Component');
$loginUrl = '';
$currentAuthPrefix = Configure::read('BcAuthPrefix.' . $currentPrefix);
if (!empty($currentAuthPrefix['loginAction'])) {
	$loginUrl = preg_replace('/^\//', '', $currentAuthPrefix['loginAction']);
}
if (in_array('admin', $currentUserAuthPrefixes)) {
	$logoutAction = Configure::read('BcAuthPrefix.admin.logoutAction');
} else {
	$logoutAction = Hash::get((array)$currentAuthPrefix, 'logoutAction');
}
if (!empty($currentAuthPrefix['name']) && $currentPrefix !== 'front') {
	$authName = $currentAuthPrefix['name'];
} elseif (isset($this->BcBaser->siteConfig['formal_name'])) {
	$authName = $this->BcBaser->siteConfig['formal_name'];
} else {
	$authName = '';
}
?>

<div id="ToolBar" class="bca-toolbar">
	<div id="ToolbarInner" class="clearfix bca-toolbar__body">
		<div class="bca-toolbar__logo">
			<?php // インストール画面 ?>
			<?php if ($this->name === 'Installations'): ?>
				<?php $this->BcBaser->link(
					sprintf(
						'%s<span class="bca-toolbar__logo-text">%s</span>',
						$this->BcBaser->getImg(
							'admin/logo_icon.svg',
							[
								'alt' => '',
								'width' => '24',
								'height' => '21',
								'class' => 'bca-toolbar__logo-symbol'
							]
						), __d('baser', 'インストールマニュアル')
					),
					Configure::read('BcApp.outerLinks.installManual'),
					['target' => '_blank', 'class' => 'bca-toolbar__logo-link']
				) ?>

				<?php // バージョンアップ画面 ?>
			<?php elseif (Configure::read('BcRequest.isUpdater')): ?>
				<?php $this->BcBaser->link(
					sprintf(
						'%s<span class="bca-toolbar__logo-text">%s</span>',
						$this->BcBaser->getImg(
							'admin/logo_icon.svg',
							[
								'alt' => '',
								'width' => '24',
								'height' => '21',
								'class' => 'bca-toolbar__logo-symbol'
							]
						),
						__d('baser', 'アップデートマニュアル')
					),
					Configure::read('BcApp.outerLinks.updateManual'),
					['target' => '_blank', 'class' => 'bca-toolbar__logo-link']
				) ?>

				<?php // 通常管理画面 ?>
			<?php elseif (!empty($this->request->params['admin']) || ('/' . $this->request->url) == $loginUrl): ?>
				<?php
				$this->BcBaser->link(
					sprintf(
						'%s<span class="bca-toolbar__logo-text">%s</span>',
						$this->BcBaser->getImg(
							'admin/logo_icon.svg',
							[
								'alt' => '',
								'width' => '24',
								'height' => '21',
								'class' => 'bca-toolbar__logo-symbol'
							]
						),
						h($this->BcBaser->siteConfig['formal_name'])
					), '/',
					['class' => 'bca-toolbar__logo-link']
				)
				?>

				<?php // 公開画面 ?>
			<?php else: ?>
				<?php // 管理画面にアクセス可能な権限がある場合 ?>
				<?php if (in_array('admin', $currentUserAuthPrefixes)): ?>
					<?php
					$this->BcBaser->link(
						sprintf(
							'%s<span class="bca-toolbar__logo-text">%s</span>',
							$this->BcBaser->getImg(
								'admin/logo_icon.svg',
								[
									'alt' => '',
									'width' => '24',
									'height' => '21',
									'class' => 'bca-toolbar__logo-symbol'
								]
							),
							h($this->BcBaser->siteConfig['formal_name'])),
						[
							'plugin' => null,
							'admin' => true,
							'controller' => 'dashboard',
							'action' => 'index'
						],
						['class' => 'bca-toolbar__logo-link']
					)
					?>
					<?php // 管理画面にアクセス権限がない場合 ?>
				<?php else: ?>
					<?php
					$this->BcBaser->link(
						sprintf('<span class="bca-toolbar__logo-text">%s</span>', h($authName)), 
						Configure::read('BcAuthPrefix.' . $currentPrefix . '.loginRedirect'), 
						['class' => 'bca-toolbar__logo-link', 'title' => h($authName)]
					); 
					?>
				<?php endif ?>
			<?php endif ?>
		</div>
		<div id="ToolMenu" class="bca-toolbar__tools">
			<?php if ($this->BcBaser->existsEditLink() && !isset($this->request->query['preview'])): ?>
				<div class="bca-toolbar__tools-button bca-toolbar__tools-button-edit">
					<?php $this->BcBaser->editLink() ?>
				</div>
			<?php endif ?>
			<?php if(!empty($this->request->params['Content']['type']) && $this->request->params['Content']['type'] === 'ContentFolder'): ?>
				<div class="bca-toolbar__tools-button bca-toolbar__tools-button-add">
					<?php $this->BcBaser->link(__d('baser', '新規ページ追加'), [
						'plugin' => '',
						'admin' => true,
						'controller' => 'pages',
						'action' => 'add', $this->request->params['Content']['id']
					], ['class' => 'tool-menu']); ?>
				</div>
			<?php endif ?>
			<?php if ($this->BcBaser->existsPublishLink()): ?>
				<div class="bca-toolbar__tools-button bca-toolbar__tools-button-publish">
					<?php $this->BcBaser->publishLink() ?>
				</div>
			<?php endif ?>
			<?php
			// EVENT leftOfToolbar
			$event = $this->dispatchEvent('leftOfToolbar', [], ['layer' => 'View', 'class' => '', 'plugin' => '']);
			if ($event !== false) {
				echo ($event->result === null || $event->result === true)? '' : $event->result;
			}
		 	?>
			<?php if (!$loginUrl || $this->request->url != $loginUrl): ?>
				<div class="bca-toolbar__tools-mode">
					<?php if (Configure::read('debug') == -1 && $this->name !== "Installations"): ?>
						<span id="DebugMode" class="bca-debug-mode"
							  title="<?php echo __d('baser', 'インストールモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。') ?>"><?php echo __d('baser', 'インストールモード') ?></span>
					<?php elseif (Configure::read('debug') > 0): ?>
						<span id="DebugMode" class="bca-debug-mode"
							  title="<?php echo __d('baser', 'デバッグモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。') ?>"><?php echo __d('baser', 'デバッグモード') ?><?php echo mb_convert_kana(Configure::read('debug'), 'N') ?></span>
					<?php endif; ?>
				</div>
			<?php endif ?>
		</div>
		<div id="UserMenu" class="bca-toolbar__users">
			<ul class="clearfix">
				<?php
				/**
				 * TODO: お気に入りを表示（サイドメニューとのイベント処理・同期・スタイルの調整を検討中）
				 * <li>
				 * <a href="javascript:void(0)" class="title"><?php echo __d('baser', 'お気に入り') ? ><img src="/theme/admin-third/img/admin/btn_dropdown.png" width="8" height="11" class="bc-btn"></a>
				 *    <div id="FavoriteArea" hidden>
				 *        <?php $this->BcBaser->element('favorite_menu') ? >
				 *        <?php $this->BcBaser->element('permission') ? >
				 *    </div>
				 * </li>
				 */
				?>
				<li>
					<?php if (!empty($user)): ?>
						<?php $this->BcBaser->link(h($this->BcBaser->getUserName($user)) . ' ' . $this->BcBaser->getImg('admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']), 'javascript:void(0)', ['class' => 'title']) ?>
						<ul>
							<?php if ($this->Session->check('AuthAgent')): ?>
								<li><?php $this->BcBaser->link(__d('baser', '元のユーザーに戻る'), ['admin' => false, 'plugin' => null, 'controller' => 'users', 'action' => 'back_agent']) ?></li>
							<?php endif ?>
							<?php if (in_array('admin', $currentUserAuthPrefixes)): ?>
								<li><?php $this->BcBaser->link(__d('baser', 'アカウント設定'), ['admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['id']]) ?></li>
							<?php else: ?>
								<?php if ($currentPrefix !== 'front'): ?>
									<li><?php $this->BcBaser->link(__d('baser', 'アカウント設定'), [$currentPrefix => true, 'plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['id']]) ?></li>
								<?php endif ?>
							<?php endif ?>
							<?php
							// EVENT userMenuOfToolbar
							$event = $this->dispatchEvent('userMenuOfToolbar', [], ['layer' => 'View', 'class' => '', 'plugin' => '']);
							if ($event !== false) {
								echo ($event->result === null || $event->result === true)? '' : $event->result;
							}
							?>
							<li><?php $this->BcBaser->link(__d('baser', 'ログアウト'), $logoutAction) ?></li>
						</ul>
					<?php elseif ($this->name !== 'Installations' && $this->request->url != $loginUrl && !Configure::read('BcRequest.isUpdater')): ?>
						<?php $this->BcBaser->link(__d('baser', 'ログインしていません ') . $this->BcBaser->getImg('admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']), 'javascript:void(0)', ['class' => 'title']) ?>
						<ul>
							<?php if ($currentPrefix === 'front'): ?>
								<li><?php $this->BcBaser->link(__d('baser', 'ログイン'), ['plugin' => null, 'controller' => 'users', 'action' => 'login']) ?></li>
							<?php else: ?>
								<li><?php $this->BcBaser->link(__d('baser', 'ログイン'), [$currentPrefix => true, 'plugin' => null, 'controller' => 'users', 'action' => 'login']) ?></li>
							<?php endif ?>
						</ul>
					<?php endif ?>
				</li>
				<?php if (!empty($user) && in_array('admin', $currentUserAuthPrefixes)): ?>
					<li>
						<?php $this->BcBaser->link(
							__d('baser', 'キャッシュクリア'),
							[
								'admin' => true,
								'plugin' => false,
								'controller' => 'site_configs',
								'action' => 'del_cache'
							],
							[
								'confirm' => __d('baser', 'キャッシュクリアします。いいですか？')
							]
						) ?>　
					</li>
				<?php endif ?>
				<?php
				// EVENT rightOfToolbar
				$event = $this->dispatchEvent('rightOfToolbar', [], ['layer' => 'View', 'class' => '', 'plugin' => '']);
				if ($event !== false) {
					echo ($event->result === null || $event->result === true)? '' : $event->result;
				}
				?>
			</ul>
		</div>
	</div>
</div>
