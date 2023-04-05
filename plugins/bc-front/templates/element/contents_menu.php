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
 * サイトマップ
 *
 * カテゴリの階層構造を表現する為、再帰呼び出しを行う
 * $this->BcBaser->contentsMenu() で呼び出す
 *
 * @var \BaserCore\View\BcFrontAppView $this
 */

if (!isset($level)) {
	$level = 1;
}
if (!isset($currentId)) {
	$currentId = null;
}
?>


<?php if (isset($tree)): ?>
	<ul class="menu ul-level-<?php echo $level ?>">
		<?php if (isset($tree)): ?>
			<?php foreach($tree as $content): ?>
				<?php if ($content->title): ?>
					<?php
					if (!empty($content->exclude_menu)) {
						continue;
					}
					$liClass = 'menu-content li-level-' . $level;
					if ($content->id == $currentId) {
						$liClass .= ' current';
					}
					$options = ['escape' => true];
					if (!empty($content->blank_link)) {
						$options['target'] = '_blank';
					}
					?>
					<li class="<?php echo $liClass ?>">
						<?php $this->BcBaser->link($content->title, $content->url, $options) ?>
						<?php if (!empty($content['children'])): ?>
							<?php $this->BcBaser->element('contents_menu', ['tree' => $content['children'], 'level' => $level + 1, 'currentId' => $currentId]) ?>
						<?php endif ?>
					</li>
				<?php endif ?>
			<?php endforeach; ?>
		<?php endif ?>
	</ul>
<?php endif ?>
