<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * サブサイト一覧
 *
 * @var BcAppView $this
 */

$this->BcListTable->setColumnNumber(8);
$agents = Configure::read('BcAgent');
$devices = [];
foreach($agents as $key => $agent) {
	$devices[$key] = $agent['name'];
}
$languages = Configure::read('BcLang');
$langs = [];
foreach($languages as $key => $lang) {
	$langs[$key] = $lang['name'];
}
?>


<div class="bca-data-list__top">
	<div class="bca-data-list__sub">
		<!-- pagination -->
		<?php $this->BcBaser->element('pagination') ?>
	</div>
</div>

<!-- ListTable -->
<table cellpadding="0" cellspacing="0" class="list-table sort-table bca-table-listup" id="ListTable">
	<thead class="bca-table-listup__thead">
	<tr>
		<th class="bca-table-listup__thead-th">
			<?php
			echo $this->Paginator->sort('id',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'No'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'No')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
			?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?php
			echo $this->Paginator->sort('display_name',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'サイト名'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'サイト名')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
			?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?php
			echo $this->Paginator->sort('name',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '識別名称'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '識別名称')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
			?>
			<br>
			<?php
			echo $this->Paginator->sort('alias',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'エイリアス'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'エイリアス')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
			?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?php
			echo $this->Paginator->sort('status',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '公開状態'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '公開状態')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
			?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?php echo $this->Paginator->sort('device',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'デバイス'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'デバイス')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
			?>
			<br>
			<?php
			echo $this->Paginator->sort('lang',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '言語'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '言語')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
			?>
		</th>
		<th class="bca-table-listup__thead-th">
			<?php
			echo $this->Paginator->sort('main_site_id',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'メインサイト'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'メインサイト')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
			?>
			<br>
			<?php
			echo $this->Paginator->sort('theme',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', 'テーマ'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', 'テーマ')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
			?>
		</th>
		<?php echo $this->BcListTable->dispatchShowHead() ?>
		<th class="bca-table-listup__thead-th">
			<?php
			echo $this->Paginator->sort('created',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '登録日'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '登録日')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
			?>
			<br/>
			<?php
			echo $this->Paginator->sort('modified',
				['asc' => '<i class="bca-icon--asc"></i>' . __d('baser', '更新日'), 'desc' => '<i class="bca-icon--desc"></i>' . __d('baser', '更新日')],
				['escape' => false, 'class' => 'btn-direction bca-table-listup__a']
			);
			?>
		</th>
		<th class="list-tool bca-table-listup__thead-th"><?php echo __d('baser', 'アクション') ?></th>
	</tr>
	</thead>
	<tbody class="bca-table-listup__tbody">
	<?php if (!empty($datas)): ?>
		<?php foreach($datas as $key => $data): ?>
			<?php $this->BcBaser->element('sites/index_row', ['data' => $data, 'count' => ($key + 1), 'langs' => $langs, 'devices' => $devices]) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="<?php echo $this->BcListTable->getColumnNumber() ?>" class="bca-table-listup__tbody-td"><p
					class="no-data"><?php echo __d('baser', 'データがありません。') ?></p></td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>

<div class="bca-data-list__bottom">
	<div class="bca-data-list__sub">
		<!-- pagination -->
		<?php $this->BcBaser->element('pagination') ?>
		<!-- list-num -->
		<?php $this->BcBaser->element('list_num') ?>
	</div>
</div>
