<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\View\AppView;

/**
 * プラグイン一覧　行
 * @var AppView $this
 */
?>


<tr>
	<td class="row-tools bca-table-listup__tbody-td">
		<div>
			<?php $this->BcBaser->link('', $data['link'], [
				'target' => '_blank',
				'aria-label' => __d('baser', 'ダウンロードサイトへ移動する'),
				'title' => __d('baser', 'ダウンロードサイトへ移動する'),
				'class' => 'btn-download bca-btn-icon',
				'data-bca-btn-type' => 'download',
				'data-bca-btn-size' => 'lg'
			]) ?>
		</div>
	</td>
	<td class="bca-table-listup__tbody-td">
		<?php echo h($data['title']) ?>
	</td>
	<td class="bca-table-listup__tbody-td"><?php echo $data['version'] ?></td>
	<td class="bca-table-listup__tbody-td"><?php echo h($data['description']) ?></td>
	<td class="bca-table-listup__tbody-td"><?php $this->BcBaser->link($data['author'], $data['authorLink'], ['target' => '_blank', 'escape' => true]) ?></td>
	<td class="bca-table-listup__tbody-td" style="width:10%;white-space: nowrap">
		<?php echo $this->BcTime->format($data['created'], 'YYYY-MM-dd') ?><br/>
		<?php echo $this->BcTime->format($data['modified'], 'YYYY-MM-dd') ?>
	</td>
</tr>
