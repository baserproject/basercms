<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * フッター
 *
 * BcBaserHelper::footer() で呼び出す
 * （例）<?php $this->BcBaser->footer() ?>
 *
 * @var \BaserCore\View\BcFrontAppView $this
 */
?>


<footer class="bs-footer">
	<p class="bs-footer__copyright"> Copyright(C)
		<?php $this->BcBaser->copyYear(2008) ?>
		baserCMS Users Community All rights Reserved.
		<span class="bs-footer__banner">
			<a href="https://basercms.net/" target="_blank" class="bs-footer__banner-link"><?php echo $this->BcHtml->image('BaserCore.baser.power.gif', ['alt' => 'baserCMS : Based Website Development Project']) ?></a>&nbsp;
			<a href="https://cakephp.org/" target="_blank" class="bs-footer__banner-link"><?php echo $this->BcHtml->image('cake.power.gif', ['alt' => 'CakePHP(tm) : Rapid Development Framework']) ?></a>
		</span>
	</p>
</footer>
