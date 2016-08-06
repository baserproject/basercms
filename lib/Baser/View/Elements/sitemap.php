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
 * $this->BcBaser->sitemap() で呼び出す
 */
		 
if (!isset($level)) {
	$level = 1;
}
?>


<?php if (isset($tree)): ?>
<ul class="sitemap ul-level-<?php echo $level ?>">
	<?php if (isset($tree)): ?>
		<?php foreach ($tree as $content): ?>
			<?php if ($content['Content']['title']): ?>
				<li class="sitemap-content li-level-<?php echo $level ?>"><?php $this->BcBaser->link($content['Content']['title'], $content['Content']['url']) ?></li>
			<?php endif ?>
			<?php if (!empty($content['children'])): ?>
				<?php $this->BcBaser->element('sitemap', array('tree' => $content['children'], 'level' => $level + 1)) ?>
			<?php endif ?>
		<?php endforeach; ?>
	<?php endif ?>
</ul>
<?php endif ?>