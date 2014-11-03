<?php
/**
 * [ADMIN] ページカテゴリー管理メニュー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<tr>
	<th>カテゴリー管理メニュー</th>
	<td>
		<ul class="cleafix">
			<li><?php $this->BcBaser->link('カテゴリ一覧', array('controller' => 'page_categories', 'action' => 'index')) ?></li>
			<?php if ($newCatAddable): ?>
				<li><?php $this->BcBaser->link('カテゴリ新規追加', array('controller' => 'page_categories', 'action' => 'add')) ?></li>
			<?php endif; ?>
		</ul>
	</td>
</tr>
