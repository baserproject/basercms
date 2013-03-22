<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページカテゴリー一覧　検索ボックス
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
$pageType = array('1' => 'PC', '2' => 'モバイル', '3' => 'スマートフォン');
?>


<?php echo $bcForm->create('PageCategory', array('url' => array('action' => 'index'))) ?>
<p>
	<span><?php echo $bcForm->label('PageCategory.type', 'タイプ') ?> <?php echo $bcForm->input('PageCategory.type', array('type' => 'radio', 'options' => $pageType)) ?></span>
</p>
<div class="button">
	<?php $bcBaser->link($bcBaser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
	<?php $bcBaser->link($bcBaser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>
<?php echo $bcForm->end() ?>