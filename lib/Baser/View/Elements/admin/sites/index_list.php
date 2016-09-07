<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * サブサイト一覧
 */
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


<!-- pagination -->
<?php $this->BcBaser->element('pagination') ?>

<!-- ListTable -->
<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
	<thead>
		<tr>
			<th class="list-tool">
				<div>
					<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', array('width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn')), array('action' => 'add')) ?>
				</div>
			</th>
	<th><?php echo $this->Paginator->sort('id', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' NO', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' NO'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
	<th><?php echo $this->Paginator->sort('display_name', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' サイト名', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' サイト名'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
	<th><?php echo $this->Paginator->sort('name', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 識別名称', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 識別名称'), array('escape' => false, 'class' => 'btn-direction')) ?><br>
	<?php echo $this->Paginator->sort('alias', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' エイリアス', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' エイリアス'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
	<th><?php echo $this->Paginator->sort('status', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 公開状態', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 公開状態'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
	<th><?php echo $this->Paginator->sort('device', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' デバイス', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' デバイス'), array('escape' => false, 'class' => 'btn-direction')) ?><br>
	<?php echo $this->Paginator->sort('lang', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 言語', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 言語'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
	<th><?php echo $this->Paginator->sort('main_site_id', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' メインサイト', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' メインサイト'), array('escape' => false, 'class' => 'btn-direction')) ?><br>
		<?php echo $this->Paginator->sort('theme', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' テーマ', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' テーマ'), array('escape' => false, 'class' => 'btn-direction')) ?></th>
	<th>
		<?php echo $this->Paginator->sort('created', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 登録日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 登録日'), array('escape' => false, 'class' => 'btn-direction')) ?><br />
		<?php echo $this->Paginator->sort('modified', array('asc' => $this->BcBaser->getImg('admin/blt_list_down.png', array('alt' => '昇順', 'title' => '昇順')) . ' 更新日', 'desc' => $this->BcBaser->getImg('admin/blt_list_up.png', array('alt' => '降順', 'title' => '降順')) . ' 更新日'), array('escape' => false, 'class' => 'btn-direction')) ?>
	</th>
</tr>
</thead>
<tbody>
	<?php if (!empty($datas)): ?>
		<?php foreach ($datas as $key => $data): ?>
			<?php $this->BcBaser->element('sites/index_row', array('data' => $data, 'count' => ($key + 1), 'langs' => $langs, 'devices' => $devices)) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="8"><p class="no-data">データがありません。</p></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>

<!-- list-num -->
<?php $this->BcBaser->element('list_num') ?>
