<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * [ADMIN] グロバールメニュー
 * @var BcAppView $this
 */
if (!isset($level)) {
	$level = 1;
}
if (!isset($currentId)) {
	$currentId = null;
}
?>


<?php if (isset($tree)): ?>
	<ul class="global-menu ul-level-<?php echo $level ?><?php echo ($level > 1)? ' sub-nav-group' : ' nav-menu' ?>">
		<?php if (isset($tree)): ?>
			<?php $i = 1 ?>
			<?php foreach($tree as $content): ?>
				<?php if ($content['Content']['title']): ?>
					<?php
					if (!empty($content['Content']['exclude_menu'])) {
						continue;
					}
					$liClass = 'nav-item-' . $i . ' li-level-' . $level;
					if ($content['Content']['id'] == $currentId || $this->BcBaser->isContentsParentId($currentId, $content['Content']['id'])) {
						$liClass .= ' current';
					}
					$options = ['escape' => true];
					if (!empty($content['Content']['blank_link'])) {
						$options['target'] = '_blank';
					}
					?>
					<li class="nav-item <?php echo $liClass ?>">
						<?php $this->BcBaser->link($content['Content']['title'], $this->BcBaser->getContentsUrl($content['Content']['url'], false, null, false), $options) ?>
						<?php if (!empty($content['children'])): ?>
							<div class="sub-nav">
								<?php $this->BcBaser->element('contents_menu', ['tree' => $content['children'], 'level' => $level + 1, 'currentId' => $currentId]) ?>
							</div>
						<?php endif ?>
					</li>
				<?php endif ?>
				<?php $i++ ?>
			<?php endforeach; ?>
		<?php endif ?>
	</ul>
<?php endif ?>
