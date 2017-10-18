<?php
/**
 * グローバルメニュー（スマホ用）
 * 呼出箇所：全ページ
 *
 * BcBaserHelper::globalMenu() で呼び出す
 * （例）<?php $this->BcBaser->globalMenu() ?>
 */
if (Configure::read('BcRequest.isMaintenance')) {
	return;
}
?>


<ul class="global-menu">
	<?php if (empty($menuType)) $menuType = '' ?>
	<?php $globalMenus = $this->BcContents->getTree(3) ?>
	<?php if (!empty($globalMenus)): ?>
		<?php foreach ($globalMenus as $key => $globalMenu): ?>
			<?php if ($globalMenu['Content']['url']): ?>

				<?php
				$no = sprintf('%02d', $key + 1);
				$classies = array('menu' . $no);
				if ($this->BcArray->first($globalMenus, $key)) {
					$classies[] = 'first';
				} elseif ($this->BcArray->last($globalMenus, $key)) {
					$classies[] = 'last';
				}
				if ($this->BcBaser->isCurrentUrl($globalMenu['Content']['url'])) {
					$classies[] = 'current';
				}
				$class = ' class="' . implode(' ', $classies) . '"';
				?>

				<?php if (empty($this->request->params['Site']['name']) && $this->base == '/index.php' && $globalMenu['Content']['url'] == '/'): ?>
					<?php /* PC版トップページ */ ?>
					<li<?php echo $class ?>>
						<?php echo str_replace('/index.php', '', $this->BcBaser->link($globalMenu['Content']['title'], $this->BcBaser->getContentsUrl($globalMenu['Content']['url']))) ?>
					</li>
				<?php else: ?>
					<li<?php echo $class ?>>
						<?php $this->BcBaser->link($globalMenu['Content']['title'], $globalMenu['Content']['url']) ?>
					</li>
				<?php endif ?>
			<?php endif ?>
		<?php endforeach ?>
	<?php endif ?>
</ul>
