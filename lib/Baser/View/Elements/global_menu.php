<?php
/**
 * [ADMIN] グロバールメニュー
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

if (Configure::read('BcRequest.isMaintenance')) {
	return;
}
$prefix = '';
if (Configure::read('BcRequest.agent')) {
	$prefix = '/' . Configure::read('BcRequest.agentAlias');
}
?>


<ul class="global-menu clearfix">
	<?php if (empty($menuType)) $menuType = '' ?>
	<?php $globalMenus = $this->BcBaser->getMenus() ?>
	<?php if (!empty($globalMenus)): ?>
		<?php foreach ($globalMenus as $key => $globalMenu): ?>
			<?php if ($globalMenu['Menu']['status']): ?>

				<?php
				$no = sprintf('%02d', $key + 1);
				$classies = array('menu' . $no);
				if ($this->BcArray->first($globalMenus, $key)) {
					$classies[] = 'first';
				} elseif ($this->BcArray->last($globalMenus, $key)) {
					$classies[] = 'last';
				}
				if ($this->BcBaser->isCurrentUrl($globalMenu['Menu']['link'])) {
					$classies[] = 'current';
				}
				$class = ' class="' . implode(' ', $classies) . '"';
				?>

				<?php if (!Configure::read('BcRequest.agent') && $this->base == '/index.php' && $globalMenu['Menu']['link'] == '/'): ?>
					<?php /* PC版トップページ */ ?>
					<li<?php echo $class ?>>
						<?php echo str_replace('/index.php', '', $this->BcBaser->link($globalMenu['Menu']['name'], $globalMenu['Menu']['link'])) ?>
					</li>
				<?php else: ?>
					<li<?php echo $class ?>>
						<?php $this->BcBaser->link($globalMenu['Menu']['name'], $prefix . $globalMenu['Menu']['link']) ?>
					</li>
				<?php endif ?>
			<?php endif ?>
		<?php endforeach ?>
	<?php endif ?>
</ul>
