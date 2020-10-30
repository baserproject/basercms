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

use Cake\Core\Configure;
use BaserCore\View\AppView;

/**
 * toolbar
 * @var AppView $this
 */

// JSの出力について、ツールバーはフロントエンドでも利用するため、inlineに出力する
$this->BcBaser->js('admin/toolbar.bundle');
$isCurrentUserAdminAvailable = $this->BcAuth->isCurrentUserAdminAvailable();
$authName = $this->BcAuth->getCurrentName();
$loginUrl = $this->BcAuth->getCurrentLoginUrl();
$currentPrefix = $this->BcAuth->getCurrentPrefix();
$user = $this->BcAuth->getCurrentLoginUser();
?>


<div id="ToolBar" class="bca-toolbar">
	<div id="ToolbarInner" class="clearfix bca-toolbar__body">
		<div class="bca-toolbar__logo">
			<?php
				// インストール画面
				if ($this->name == 'Installations'): ?>
				<?php $this->BcBaser->link(
						$this->BcBaser->getImg('admin/logo_icon.svg', ['alt' => '', 'width' => '24', 'height' => '21', 'class' => 'bca-toolbar__logo-symbol']) .
				        '<span class="bca-toolbar__logo-text">' . __d('baser', 'インストールマニュアル') . '</span>',
                        'https://basercms.net/manuals/introductions/4.html', ['target' => '_blank', 'class' => 'bca-toolbar__logo-link']) ?>
			<?php
				// バージョンアップ画面
				elseif (Configure::read('BcRequest.isUpdater')): ?>
				<?php $this->BcBaser->link(
						$this->BcBaser->getImg('admin/logo_icon.svg', ['alt' => '', 'width' => '24', 'height' => '21', 'class' => 'bca-toolbar__logo-symbol']) .
						'<span class="bca-toolbar__logo-text">' . __d('baser', 'アップデートマニュアル') . '</span>',
                        'https://basercms.net/manuals/introductions/8.html', ['target' => '_blank', 'class' => 'bca-toolbar__logo-link']) ?>
			<?php
				// 通常
				elseif (!empty($this->request->getParam('admin')) || ($this->request->getRequestTarget()) == $this->BcAuth->getCurrentLoginUrl()): ?>
				<?php
					$this->BcBaser->link(
						$this->BcBaser->getImg('admin/logo_icon.svg', ['alt' => '', 'width' => '24', 'height' => '21', 'class' => 'bca-toolbar__logo-symbol'])
							.'<span class="bca-toolbar__logo-text">'
							.$this->BcBaser->siteConfig['formal_name']
							.'</span>', '/'
						,
						['class' => 'bca-toolbar__logo-link', 'escapeTitle' => false]
					)
				?>
			<?php
				else: ?>
				<?php
					# 管理画面にアクセス可能な権限がある場合
					if ($isCurrentUserAdminAvailable): ?>
					<?php
					$this->BcBaser->link(
						$this->BcBaser->getImg('admin/logo_icon.svg', ['alt' => '', 'width' => '24', 'height' => '21', 'class' => 'bca-toolbar__logo-symbol'])
							.'<span class="bca-toolbar__logo-text">'
							.$this->BcBaser->siteConfig['formal_name']
							.'</span>', ['admin' => true, 'controller' => 'dashboard', 'action' => 'index']
						,
						['class' => 'bca-toolbar__logo-link', 'escapeTitle' => false]
					)
					?>
				<?php
				# 管理画面にアクセス権限がない場合
				else: ?>
					<?php $this->BcBaser->link($authName, $this->BcAuth->getCurrentLoginRedirectUrl(), ['title' => $authName]) ?>
				<?php endif ?>
			<?php endif ?>
		</div>
		<div id="ToolMenu" class="bca-toolbar__tools">
			<?php if ($this->BcBaser->existsEditLink() && !isset($this->request->getQuery['preview'])): ?>
				<div class="bca-toolbar__tools-button bca-toolbar__tools-button-edit">
					<?php $this->BcBaser->editLink() ?>
				</div>
			<?php endif ?>
			<?php if ($this->BcBaser->existsPublishLink()): ?>
				<div class="bca-toolbar__tools-button bca-toolbar__tools-button-publish">
					<?php $this->BcBaser->publishLink() ?>
				</div>
			<?php endif ?>
			<?php if (!$loginUrl || $this->request->getUri() != $loginUrl): ?>
				<div class="bca-toolbar__tools-mode">
					<?php if (Configure::read('debug') == -1 && $this->name != "Installations"): ?>
						<span id="DebugMode" class="bca-debug-mode" title="<?php echo __d('baser', 'インストールモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。') ?>"><?php echo __d('baser', 'インストールモード') ?></span>
					<?php elseif (Configure::read('debug') > 0): ?>
						<span id="DebugMode" class="bca-debug-mode" title="<?php echo __d('baser', 'デバッグモードです。運営を開始する前にシステム設定よりノーマルモードに戻しましょう。') ?>"><?php echo __d('baser', 'デバッグモード') ?><?php echo mb_convert_kana(Configure::read('debug'), 'N') ?></span>
					<?php endif; ?>
				</div>
			<?php endif ?>
		</div>
		<div id="UserMenu" class="bca-toolbar__users">
			<ul class="clearfix">
				<li>
					<?php if ($this->BcAuth->isAdminLogin()): ?>
						<?php $this->BcBaser->link(h($this->BcBaser->getUserName($user)) . ' ' . $this->BcBaser->getImg('admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']), 'javascript:void(0)', ['class' => 'title', 'escapeTitle' => false]) ?>
						<ul>
							<?php // TODO: SessionHelper をどうするか検討
							    //if ($this->Session->check('AuthAgent')): ?>
								<!--<li><?php $this->BcBaser->link(__d('baser', '元のユーザーに戻る'), ['admin' => false, 'controller' => 'users', 'action' => 'back_agent']) ?></li>-->
							<?php //endif ?>
							<?php if ($isCurrentUserAdminAvailable): ?>
								<li><?php $this->BcBaser->link(__d('baser', 'アカウント設定'), ['admin' => true, 'controller' => 'users', 'action' => 'edit', $user['id']]) ?></li>
							<?php else: ?>
                                <?php if ($currentPrefix != 'front'): ?>
								<li><?php $this->BcBaser->link(__d('baser', 'アカウント設定'), [$currentPrefix => true, 'controller' => 'users', 'action' => 'edit', $user['id']]) ?></li>
								<?php endif ?>
							<?php endif ?>
							<li><?php $this->BcBaser->link(__d('baser', 'ログアウト'), $this->BcAuth->getCurrentLogoutUrl()) ?></li>
						</ul>
					<?php elseif ($this->name != 'Installations' && $this->request->getRequestTarget() != $loginUrl && !Configure::read('BcRequest.isUpdater')): ?>
						<?php $this->BcBaser->link(__d('baser', 'ログインしていません ') . $this->BcBaser->getImg('admin/btn_dropdown.png', ['width' => 8, 'height' => 11, 'class' => 'bc-btn']), 'javascript:void(0)', ['class' => 'title', 'escapeTitle' => false]) ?>
						<ul>
							<?php if ($currentPrefix == 'front'): ?>
								<li><?php $this->BcBaser->link(__d('baser', 'ログイン'), ['controller' => 'users', 'action' => 'login']) ?></li>
							<?php else: ?>
								<li><?php $this->BcBaser->link(__d('baser', 'ログイン'), [$currentPrefix => true, 'controller' => 'users', 'action' => 'login']) ?></li>
							<?php endif ?>
						</ul>
					<?php endif ?>
				</li>
				<?php if ($this->BcAuth->isAdminLogin() && $isCurrentUserAdminAvailable): ?>
					<li>
						<?php $this->BcBaser->link(__d('baser', 'キャッシュクリア'), ['admin' => true, 'controller' => 'Utilities', 'action' => 'clear_cache'], ['confirm' => __d('baser', 'キャッシュクリアします。いいですか？')]) ?>　
					</li>
				<?php endif ?>
			</ul>
		</div>
	</div>
</div>
