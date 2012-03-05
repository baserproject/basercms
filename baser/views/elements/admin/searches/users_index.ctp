<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ユーザー一覧　検索ボックス
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			baserCMS v 1.7.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<?php echo $formEx->create('User', array('url' => array('action' => 'index'))) ?>
<p>
	<?php echo $formEx->label('User.user_group_id', 'ユーザーグループ') ?> 
	<?php echo $formEx->input('User.user_group_id', array('type' => 'select', 'options' => $formEx->getControlSource('User.user_group_id'), 'empty' => '指定なし')) ?>
</p>
<div class="button">
	<?php $baser->link($baser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
	<?php $baser->link($baser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>
<?php echo $form->end() ?>