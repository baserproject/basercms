<?php
/**
 * [ADMIN] 統合コンテンツ一覧
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<ul>
	<?php foreach($datas as $data): ?>
		<?php $this->BcBaser->element('admin/contents/index_row', array('data' => $data)) ?>
	<?php endforeach ?>
</ul>

