<?php
/**
 * パンくずナビゲーション
 */
if ($this->viewPath == 'home') {
	echo '<strong>'. __('ホーム'). '</strong>';
} else {
	$crumbs = $this->BcBaser->getCrumbs();
	if (!empty($crumbs)) {
		foreach ($crumbs as $key => $crumb) {
			if ($this->BcArray->last($crumbs, $key)) {
				if ($this->viewPath != 'home' && $crumb['name']) {
					$this->BcBaser->addCrumb(h($crumb['name']));
				} elseif ($this->name == 'CakeError') {
					$this->BcBaser->addCrumb('<strong>404 NOT FOUND</strong>');
				}
			} else {
				$this->BcBaser->addCrumb(h($crumb['name']), $crumb['url']);
			}
		}
	}
	$this->BcBaser->crumbs(' &gt; ', __('ホーム'));
}
