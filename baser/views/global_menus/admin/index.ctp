<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] グローバルメニュー一覧
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi 
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php $baser->js('sorttable',false) ?>
<script type="text/javascript">
$(function(){
	$("#GlobalMenuSearchBody").show();
});
</script>

<?php echo $formEx->create('Sort', array('action' => 'update_sort', 'url' => am(array('controller' => 'global_menus'), $this->passedArgs))) ?>
<?php echo $formEx->input('Sort.id', array('type' => 'hidden')) ?>
<?php echo $formEx->input('Sort.offset', array('type' => 'hidden')) ?>
<?php echo $formEx->end() ?>

<div id="pageMessage" class="message" style="display:none"></div>

<h2>
	<?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?>
	<?php $baser->img('ajax-loader-s.gif',array('id'=>'ListAjaxLoader')) ?>
</h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>公開ページ、管理画面のグローバルメニューの管理ができます。<br />
		管理画面をカスタマイズする場合には有用ですが、公開ページでは、このグローバルメニューを必ずしも利用しなければならないというわけではありません。
		凝ったデザインのグローバルメニューが必要な場合や、
		グローバルメニューを可変させる必要がない場合等は、直接HTMLのコードを書いた方が柔軟に対応できるかもしれません。</p>
	<ul>
		<li>一覧は、タイプ・公開状態により絞り込みができます。</li>
		<li>公開ページでグローバルメニューを出力するには、テンプレート上に次のコードを記述します。リストタグで出力されます。<br />
			&lt;?php $baser->element('global_menu') ?&gt;</li>
		<li>画面一番下の「並び替えモード」をクリックすると、表示される<?php $baser->img('sort.png',array('alt'=>'並び替え')) ?>マークをドラッグアンドドロップして行の並び替えができます。</li>
	</ul>
</div>

<!-- search -->
<h3><a href="javascript:void(0);" class="slide-trigger" id="GlobalMenuSearch">検索</a></h3>
<div class="function-box corner10" id="GlobalMenuSearchBody" style="display:none">
	<?php echo $formEx->create('GlobalMenu',array('url' => array('action' => 'index'))) ?>
	<p><small>タイプ</small>
		<?php echo $formEx->input('GlobalMenu.menu_type', array(
				'type'		=> 'select', 
				'options'	=> $formEx->getControlSource('menu_type'))) ?>
		<small>利用状態</small>
		<?php echo $formEx->input('GlobalMenu.status', array(
				'type'		=> 'select',
				'options'	=> $textEx->booleanMarkList(),'empty' => '指定なし')) ?>　
	<?php echo $formEx->submit('検　索', array('div' => false, 'class' => 'btn-orange button')) ?></p>
	<?php echo $formEx->end() ?>
</div>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01 sort-table" id="TableGlobalMenus">
	<tr>
		<th style="width:100px">操作</th>
		<th>NO</th>
		<th>タイプ</th>
		<th>メニュー名<br />リンクURL</th>
		<th>登録日<br />更新日</th>
	</tr>
<?php if(!empty($listDatas)): ?>
	<?php $count=0; ?>
	<?php foreach($listDatas as $listData): ?>
		<?php if (!$listData['GlobalMenu']['status']): ?>
			<?php $class=' class="disablerow sortable"'; ?>
		<?php elseif ($count%2 === 0): ?>
			<?php $class=' class="altrow sortable"'; ?>
		<?php else: ?>
			<?php $class=' class="sortable"'; ?>
		<?php endif; ?>
	<tr id="Row<?php echo $count+1 ?>" <?php echo $class; ?>>
		<td class="operation-button">
		<?php if($sortmode): ?>
			<span class="sort-handle"><?php $baser->img('sort.png',array('alt'=>'並び替え')) ?></span>
			<?php echo $formEx->input('Sort.id'.$listData['GlobalMenu']['id'], array(
							'type'	=> 'hidden',
							'class'	=> 'id',
							'value'	=> $listData['GlobalMenu']['id'])) ?>
		<?php endif ?>
			<?php $baser->link('編集', array('action' => 'edit', $listData['GlobalMenu']['id']), array('class' => 'btn-orange-s button-s'), null, false) ?>
			<?php $baser->link('削除', 
					array('action' => 'delete', $listData['GlobalMenu']['id']),
					array('class'=>'btn-gray-s button-s'),
					sprintf('%s を本当に削除してもいいですか？', $listData['GlobalMenu']['name']),
					false); ?>
		</td>
		<td><?php echo $listData['GlobalMenu']['no']; ?></td>
		<td><?php echo $textEx->listValue('GlobalMenu.menu_type', $listData['GlobalMenu']['menu_type']); ?></td>
		<td><?php $baser->link($listData['GlobalMenu']['name'], array('action' => 'edit', $listData['GlobalMenu']['id'])); ?><br />
			<?php $baser->link($listData['GlobalMenu']['link'], $listData['GlobalMenu']['link'], array('target'=>'_blank')); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$listData['GlobalMenu']['created']); ?><br />
			<?php echo $timeEx->format('y-m-d',$listData['GlobalMenu']['modified']); ?></td>
	</tr>
		<?php $count++; ?>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="7"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
<?php endif; ?>
</table>

<div class="align-center">
	<?php $baser->link('新規登録', array('action' => 'add'), array('class' => 'btn-red button')) ?>
<?php if(!$sortmode): ?>
	<?php $baser->link('並び替えモード', array('sortmode' => 1), array('class' => 'btn-orange button')) ?>
<?php else: ?>
	<?php $baser->link('ノーマルモード', array('sortmode' => 0), array('class' => 'btn-orange button')) ?>
<?php endif ?>
</div>