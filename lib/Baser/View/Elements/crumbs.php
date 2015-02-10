<?php
/**
 * [PUBLISH] ナビゲーション
 *
 * ページタイトルが直属のカテゴリ名と同じ場合は、直属のカテゴリ名を省略する
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

if ($this->BcBaser->isHome()) {
	echo '<strong>ホーム</strong>';
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
					$this->BcBaser->addCrumb('<strong>' . $crumb['name'] . '</strong>');
				}
			} else {
				$this->BcBaser->addCrumb($crumb['name'], $crumb['url']);
			}
		}
	}
	elseif (empty($crumbs)) {
		if ($this->name == 'CakeError') {
			$this->BcBaser->addCrumb('<strong>404 NOT FOUND</strong>');
		}
	}
	$this->BcBaser->crumbs(' &gt; ', 'ホーム');
}