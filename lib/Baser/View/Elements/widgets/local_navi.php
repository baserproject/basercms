<?php
/**
 * [PUBLISH] ローカルナビゲーションウィジェット
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * $this->BcBaser->widgetArea('ウィジェットエリアNO') で呼び出す
 * 管理画面で設定されたウィジェットエリアNOは、 $widgetArea で参照できる
 */

if (!isset($this->BcPage)) {
	return;
}
$pageCategory = $this->BcPage->getCategory();
if (!$pageCategory) {
	return;
}
?>


<div class="widget widget-local-navi widget-local-navi-<?php echo $id ?>">
	<?php if ($use_title): ?>
		<h2><?php echo $pageCategory['title'] ?></h2>
	<?php endif ?>
	<?php $this->BcBaser->element('page_list', array('categoryId' => $pageCategory['id'])) ?>
</div>