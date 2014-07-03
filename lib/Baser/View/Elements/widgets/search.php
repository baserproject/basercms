<?php
/**
 * [PUBLISH] サイト内検索フォームウィジェット
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

if (!empty($this->passedArgs['num'])) {
	$url = array('plugin' => null, 'controller' => 'contents', 'num' => $this->passedArgs['num']);
} else {
	$url = array('plugin' => null, 'controller' => 'contents');
}
?>


<div class="widget widget-site-search widgetsite-search-<?php echo $id ?>">
	<?php if ($name && $use_title): ?>
		<h2><?php echo $name ?></h2>
	<?php endif ?>
	<?php echo $this->BcForm->create('Content', array('type' => 'get', 'action' => 'search', 'url' => $url)) ?>
	<?php if (BcUtil::unserialize($this->BcBaser->siteConfig['content_categories'])) : ?>
		<?php echo $this->BcForm->input('Content.c', array('type' => 'select', 'options' => BcUtil::unserialize($this->BcBaser->siteConfig['content_categories']), 'empty' => 'カテゴリー： 指定しない　')) ?>
	<?php endif ?>
	<?php echo $this->BcForm->input('Content.q') ?>
	<?php echo $this->BcForm->submit('検索', array('div' => false)) ?>
	<?php echo $this->BcForm->end() ?>
</div>