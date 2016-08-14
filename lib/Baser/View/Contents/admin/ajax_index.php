<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */
header('Content-type: text/html; charset=utf-8');
?>


<?php if(!empty($datas)): ?>
<div id="ContentsTreeList" style="display:none">
<?php $this->BcBaser->element('contents/index_list'); ?>
</div>
<?php elseif($this->action == 'admin_trash_index'): ?>
<div class="em-box">ゴミ箱は空です</div>
<?php endif ?>