<?php
/**
 * [ADMIN] 統合コンテンツ一覧
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */
?>


<ul>
	<?php foreach($datas as $data): ?>
		<?php $this->BcBaser->element('admin/contents/index_row_tree', array('data' => $data)) ?>
	<?php endforeach ?>
</ul>

