<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link			https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.4.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * フッター
 *
 * BcBaserHelper::footer() で呼び出す
 * （例）<?php $this->BcBaser->footer() ?>
 *
 * @var BcAppView $this
 */
?>


<footer class="bs-footer">
	<p class="bs-footer__copyright"> Copyright(C)
		<?php $this->BcBaser->copyYear(2008) ?>
		baserCMS Users Community All rights Reserved.
		<span class="bs-footer__banner">
			<a href="https://basercms.net/" target="_blank" class="bs-footer__banner-link"><?php echo $this->BcHtml->image('baser.power.gif', ['alt' => 'baserCMS : Based Website Development Project']) ?></a>&nbsp;
			<a href="https://cakephp.org/" target="_blank" class="bs-footer__banner-link"><?php echo $this->BcHtml->image('cake.power.gif', ['alt' => 'CakePHP(tm) : Rapid Development Framework']) ?></a>
		</span>
	</p>
</footer>
