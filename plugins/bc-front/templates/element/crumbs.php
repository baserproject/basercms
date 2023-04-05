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
 * パンくずナビゲーション
 *
 * ページタイトルが直属のカテゴリ名と同じ場合は、直属のカテゴリ名を省略する
 * @var \BaserCore\View\BcFrontAppView $this
 */
if (!isset($separator)) {
	$separator = '&nbsp;&gt;&nbsp;';
}
if (!isset($home)) {
	$home = __d('baser_core', 'ホーム');
}
$crumbs = $this->BcBaser->getCrumbs();
if (!empty($crumbs)) {
	foreach($crumbs as $key => $crumb) {
		if ($this->BcArray->last($crumbs, $key)) {
			if ($crumb['name']) {
				$this->BcBaser->addCrumb(h($crumb['name']));
			}
		} else {
			$this->BcBaser->addCrumb(h($crumb['name']), $crumb['url']);
		}
	}
} elseif (empty($crumbs)) {
	if ($this->name == 'CakeError') {
		$this->BcBaser->addCrumb('404 NOT FOUND');
	}
}
?>


<div class="bs-crumbs">
	<?php if (empty($onSchema)): ?>
		<?php
		if ($this->BcBaser->isHome()) {
			echo $home;
		} else {
			$this->BcBaser->crumbs($separator, $home);
		}
		?>
	<?php else: ?>
		<ul itemscope itemtype="https://schema.org/BreadcrumbList">
			<?php if ($this->BcBaser->isHome()): ?>
				<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"><span
						itemprop="name"><?php echo $home ?></span>
					<meta itemprop="position" content="1"/>
				</li>
			<?php else: ?>
				<?php $this->BcBaser->crumbs($separator, $home, true) ?>
			<?php endif ?>
		</ul>
	<?php endif ?>
</div>
