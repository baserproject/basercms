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
 * パンくずナビゲーション
 *
 * BcBaserHelper::crumbsList() で呼び出す
 * （例）<?php $this->BcBaser->crumbsList() ?>
 *
 * @var BcAppView $this
 */
?>


<div class="bs-crumbs">
<?php
if ($this->BcBaser->isHome()) {
	echo '<strong>'. __('ホーム').'</strong>';
} else {
	$crumbs = $this->BcBaser->getCrumbs();
	if (!empty($crumbs)) {
		foreach ($crumbs as $key => $crumb) {
			if ($this->BcArray->last($crumbs, $key + 1)) {
				if ($crumbs[$key + 1]['name'] == $crumb['name']) {
					continue;
				}
			}
			if ($this->BcArray->last($crumbs, $key)) {
				if ($this->viewPath != 'home' && $crumb['name']) {
					$this->BcBaser->addCrumb('<strong>' . h($crumb['name']) . '</strong>');
				}
			} else {
				$this->BcBaser->addCrumb(h($crumb['name']), $crumb['url']);
			}
		}
	}
	elseif (empty($crumbs)) {
		if ($this->name == 'CakeError') {
			$this->BcBaser->addCrumb('<strong>404 NOT FOUND</strong>');
		}
	}
	$this->BcBaser->crumbs(' &gt; ', __('ホーム'));
}
?>
</div>
