<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * [ADMIN] パンくずナビゲーション
 */
if ($this->viewPath != 'dashboard') {
	$this->BcBaser->addCrumb('<span itemprop="title" class="bca-icon--home"><span class="bca-icon-label">ホーム</span></span>', array('plugin' => null, 'controller' => 'dashboard'));
}
$crumbs = $this->BcBaser->getCrumbs();
if ($this->name == 'CakeError') {
	$this->BcBaser->addCrumb('404 NOT FOUND');
}
if (!empty($crumbs)) {
	foreach ($crumbs as $key => $crumb) {
		if ($this->BcArray->last($crumbs, $key + 1)) {
			if ($crumbs[$key + 1]['name'] == $crumb['name']) {
				continue;
			}
		}
		if ($this->BcArray->last($crumbs, $key)) {
			if ($this->viewPath != 'home' && $crumb['name']) {
				$this->BcBaser->addCrumb($crumb['name']);
			}
		} else {
			$this->BcBaser->addCrumb(strip_tags($crumb['name']), $crumb['url']);
		}
	}
}
?>

<?php if (!empty($user)): ?>
<nav id="Crumb" class="bca-crumb">
	<ol>
		<?php $this->BcBaser->crumbs("\n", false, true) ?>
	</ol>
<!-- / .bca-crumb  --></nav>
<?php endif; ?>
