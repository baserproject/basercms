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
 * コンテンツ更新情報
 *
 * @var \BaserCore\View\BcAdminAppView $this
 * @var string $createdDate
 * @var string $modifiedDate
 */
?>


<?php if (!$this->BcBaser->isHome()): ?>
	<div class="bc-update-info clearfix">
		<dl>
			<?php if ($createdDate): ?>
				<dt><?php echo __d('baser_core', '作成日') ?></dt><dd><?php echo $createdDate ?></dd>
			<?php endif ?>
			<?php if ($modifiedDate): ?>
				<dt><?php echo __d('baser_core', '最終更新日') ?></dt><dd><?php echo $modifiedDate ?></dd>
			<?php endif ?>
		</dl>
	</div>
<?php endif ?>
