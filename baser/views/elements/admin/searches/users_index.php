<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザー一覧　検索ボックス
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
?>

<?php echo $bcForm->create('User', array('url' => array('action' => 'index'))) ?>
<p>
	<?php echo $bcForm->label('User.user_group_id', 'ユーザーグループ') ?> 
	<?php echo $bcForm->input('User.user_group_id', array('type' => 'select', 'options' => $bcForm->getControlSource('User.user_group_id'), 'empty' => '指定なし')) ?>
</p>
<div class="button">
	<?php $bcBaser->link($bcBaser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
	<?php $bcBaser->link($bcBaser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>
<?php echo $form->end() ?>