<?php
/**
 * [ADMIN] ユーザー一覧　検索ボックス
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
$priorities = array('0.1' => '0.1', '0.2' => '0.2', '0.3' => '0.3', '0.4' => '0.4', '0.5' => '0.5',
	'0.6' => '0.6', '0.7' => '0.7', '0.8' => '0.8', '0.9' => '0.9', '1.0' => '1.0');
$categories = am(array('none' => 'カテゴリなし'), BcUtil::unserialize($this->BcBaser->siteConfig['content_categories']));
$types = BcUtil::unserialize($this->BcBaser->siteConfig['content_types']);
?>

<?php echo $this->BcForm->create('Content', array('url' => array('action' => 'index'))) ?>
<?php echo $this->BcForm->hidden('Content.open', array('value' => true)) ?>
<p>
	<span><?php echo $this->BcForm->label('Content.type', 'タイプ') ?> <?php echo $this->BcForm->input('Content.type', array('type' => 'select', 'options' => $types, 'empty' => '指定なし')) ?></span>
	<span><?php echo $this->BcForm->label('Content.category', 'カテゴリー') ?> <?php echo $this->BcForm->input('Content.category', array('type' => 'select', 'options' => $categories, 'empty' => '指定なし')) ?></span>
	<span><?php echo $this->BcForm->label('Content.keyword', 'キーワード') ?> <?php echo $this->BcForm->input('Content.keyword', array('type' => 'text', 'size' => '30')) ?></span>
	<span><?php echo $this->BcForm->label('Content.status', '公開状態') ?> 
		<?php echo $this->BcForm->input('Content.status', array('type' => 'select', 'options' => $this->BcText->booleanMarkList(), 'empty' => '指定なし')) ?></span>　
	<span><?php echo $this->BcForm->label('Content.priority', '優先度') ?> 
		<?php echo $this->BcForm->input('Content.priority', array('type' => 'select', 'options' => $priorities, 'empty' => '指定なし')) ?></span>
</p>
<div class="button">
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_search.png', array('alt' => '検索', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchSubmit')) ?> 
	<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_clear.png', array('alt' => 'クリア', 'class' => 'btn')), "javascript:void(0)", array('id' => 'BtnSearchClear')) ?> 
</div>
<?php $this->BcForm->end() ?>