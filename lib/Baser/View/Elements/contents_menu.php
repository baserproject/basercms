<?php
/**
 * [PUBLISH] サイトマップ
 * 
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * カテゴリの階層構造を表現する為、再帰呼び出しを行う
 * $this->BcBaser->contentsMenu() で呼び出す
 */
		 
if (!isset($level)) {
	$level = 1;
}
if(!isset($currentId)) {
	$currentId = null;
}
?>


<?php if (isset($tree)): ?>
<ul class="ul-level-<?php echo $level ?><?php echo ($level > 1) ? ' sub-nav-group': ' nav-menu'?>">
	<?php if (isset($tree)): ?>
		<?php foreach ($tree as $content): ?>
			<?php if ($content['Content']['title']): ?>
				<?php
					$liClass = 'menu-content li-level-' . $level;
					if($content['Content']['id'] == $currentId) {
						$liClass .= ' current';
					}
				?>
				<li class="nav-item <?php echo $liClass ?>"><?php $this->BcBaser->link($content['Content']['title'], $content['Content']['url']) ?>
			<?php endif ?>
			<?php if (!empty($content['children'])): ?>
			<div class="sub-nav">
				<?php $this->BcBaser->element('contents_menu', array('tree' => $content['children'], 'level' => $level + 1, 'currentId' => $currentId)) ?>
			</div>
			<?php endif ?>
			</li>
		<?php endforeach; ?>
	<?php endif ?>
</ul>
<?php endif ?>