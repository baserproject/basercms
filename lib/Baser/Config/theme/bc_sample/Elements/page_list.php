<?php
/**
 * 固定ページリスト
 * 呼出箇所：ウィジェット
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