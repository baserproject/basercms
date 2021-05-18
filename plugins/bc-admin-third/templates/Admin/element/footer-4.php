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
 * [ADMIN] フッター
 */
?>
<div id="Footer" class="bca-footer"
	 data-loggedin="<?php if (BcUtil::isAdminUser()): ?>true<?php else: ?>false<?php endif; ?>">
	<?php if (BcUtil::isAdminUser()): //ログイン後 ?>
		<div class="bca-footer__inner--full">
			<div class="bca-footer__main">
				<div class="bca-footer__baser-version">baserCMS <strong><?php echo h($baserVersion) ?></strong></div>
				<ul class="bca-footer__banner">
					<li class="bca-footer__banner__item"><?php $this->BcBaser->link($this->BcBaser->getImg('baser.power.gif', ['alt' => 'baserCMS Power']), 'https://basercms.net/', ['target' => '_blank', 'title' => 'baserCMS Power']) ?></li>
					<li class="bca-footer__banner__item"><?php $this->BcBaser->link($this->BcBaser->getImg('cake.power.gif', ['alt' => 'CakePHP Power']), 'https://cakephp.org/', ['target' => '_blank', 'title' => 'CakePHP Power']) ?></li>
				</ul>
			</div>
			<div class="bca-footer__sub">
				<div class="bca-footer__copyright">Copyright &copy; baserCMS Users
					Community <?php $this->BcBaser->copyYear(2009) ?> All rights reserved.
				</div>
			</div>
		</div>
	<?php else: //未ログイン ?>
		<div class="bca-footer__inner">
			<div class="bca-footer__header">
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/logo_icon.svg', ['alt' => '', 'class' => 'bca-footer__logo']), ['controller' => 'dashboard', 'action' => 'index']) ?>
				<div class="bca-footer__baser-version">baserCMS</div>
			</div>

			<ul class="bca-footer__sns">
				<li class="bca-footer__sns__item"><a href="http://www.facebook.com/basercms" target="_blank"
													 class="bca-footer__sns__link bca-footer__sns__link--facebook">Facebook</a>
				</li>
				<li class="bca-footer__sns__item"><a href="http://twitter.com/basercms" target="_blank"
													 class="bca-footer__sns__link bca-footer__sns__link--twitter">Twitter</a>
				</li>
			</ul>

			<ul class="bca-footer__links">
				<li class="bca-footer__links__item"><a href="https://basercms.net/"
													   target="_blank"><?php echo __d('baser', 'baserCMS 公式サイト') ?></a>
				</li>
				<li class="bca-footer__links__item"><a href="https://basercms.net/community/index"
													   target="_blank"><?php echo __d('baser', 'baserCMS ユーザーコミュニティ') ?></a>
				</li>
				<li class="bca-footer__links__item"><a href="https://forum.basercms.net/"
													   target="_blank"><?php echo __d('baser', 'baserCMS ユーザーズフォーラム') ?></a>
				</li>
				<li class="bca-footer__links__item"><a href="https://github.com/baserproject/basercms"
													   target="_blank"><?php echo __d('baser', 'baserCMS GitHub') ?></a>
				</li>
			</ul>

			<ul class="bca-footer__banner">
				<li class="bca-footer__banner__item"><?php $this->BcBaser->link($this->BcBaser->getImg('baser.power.gif', ['alt' => 'baserCMS Power']), 'https://basercms.net/', ['target' => '_blank', 'title' => 'baserCMS Power']) ?></li>
				<li class="bca-footer__banner__item"><?php $this->BcBaser->link($this->BcBaser->getImg('cake.power.gif', ['alt' => 'CakePHP Power']), 'https://cakephp.org/', ['target' => '_blank', 'title' => 'CakePHP Power']) ?></li>
			</ul>

			<div class="bca-footer__copyright">Copyright &copy; baserCMS Users
				Community <?php $this->BcBaser->copyYear(2009) ?> All rights reserved.
			</div>
		</div>
	<?php endif; ?>
	<!-- / #Footer --></div>
