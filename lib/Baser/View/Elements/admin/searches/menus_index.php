<?php
/**
 * [ADMIN] メニュー一覧　検索ボックス
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
?>
<?php echo $this->BcForm->create('Menu', array('url' => array('action' => 'index'))) ?>
<p>
	<?php echo $this->BcForm->label('Menu.status', '利用状態') ?> 
	<?php echo $this->BcForm->input('Menu.status', array(
		'type' => 'select',
		'options' => $this->BcText->booleanMarkList(), 'empty' => '指定なし'
	)); ?>　
</p>
<div class="button">
<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>
<?php echo $this->BcForm->end() ?>