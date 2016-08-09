<?php
/**
 * [PUBLISH] 固定ページローカルナビゲーション
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

if($this->request->params['Content']['type'] == 'ContentFoler') {
	$parentId = $this->request->params['Content']['id'];
	$title = $this->request->params['Content']['title'];
} else {
	$parent = $this->BcContents->getParent($this->request->params['Content']['id']);
	$parentId = $parent['Content']['id'];
	$title = $parent['Content']['title'];
}
?>


<div class="local-navi">
	<h2><?php echo h($title) ?></h2>
	<?php $this->BcBaser->contentsMenu($parentId, 1, $this->request->params['Content']['id']) ?>
</div>

