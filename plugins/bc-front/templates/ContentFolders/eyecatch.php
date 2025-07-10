<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

/**
 * コンテンツフォルダ
 *
 * @var \BaserCore\View\BcFrontAppView $this
 * @var array $children 子コンテンツ
 */
$this->BcUpload->setTable('BaserCore.Contents');
?>


<h2 class="bs-contents-title"><?php $this->BcBaser->contentsTitle(); ?></h2>
<?php if ($children): ?>
	<ul class="eyecatch-list clearfix">
		<?php foreach($children as $child): ?>
			<li>
				<?php $this->BcBaser->link($this->BcUpload->uploadImage('eyecatch', $child, [
					'imgsize' => 'thumb',
					'link' => false,
					'noimage' => 'noimage.png'
				]), $child->url, ['escape' => false]) ?>
				<p><?php $this->BcBaser->link($child->title, $child->url) ?></p>
			</li>
		<?php endforeach ?>
	</ul>
<?php else: ?>
  <p class="bs-contents-no-data"><?php echo __d('baser_core', '配下のコンテンツがありません。'); ?></p>
<?php endif ?>
