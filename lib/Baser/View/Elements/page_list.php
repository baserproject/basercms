<?php
/**
 * [PUBLISH] ページリスト
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

/**
 * エレメント：local_navi より呼び出す
 */

$pages = $this->BcBaser->getPageList($categoryId);
$current = str_replace($this->request->base, '', $this->request->here);
?>
<ul class="clearfix">
	<?php
	if (!empty($pages)) {
		foreach ($pages as $key => $page) {
			$class = '';
			$no = sprintf('%02d', $key + 1);
			$classies = array('page-' . $no);
			if ($key == 0) {
				$classies[] = 'first';
			} elseif ($key == count($pages) - 1) {
				$classies[] = 'last';
			}
			if ($current == $page['url']) {
				$classies[] = 'current';
			}
			if ($classies) {
				$class = ' class="' . implode(' ', $classies) . '"';
			}
			if ($this->request->base == '/index.php' && $page['url'] == '/') {
				echo '<li' . $class . '>' . str_replace('/index.php', '', $this->BcBaser->getLink($page['title'], $page['url'])) . '</li>';
			} else {
				echo '<li' . $class . '>' . $this->BcBaser->getLink($page['title'], $page['url']) . '</li>';
			}
		}
	}
	?>
</ul>