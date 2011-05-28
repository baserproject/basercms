<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] ページカテゴリ一覧
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<h2><?php $baser->contentsTitle() ?>&nbsp;
	<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>

<!-- help -->
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ページカテゴリは、ページを分類分けする際に利用し、URLにおいても階層構造の表現が可能となります。<br />
		また、各カテゴリは子カテゴリを持つ事ができるようになっています。</p>
	<div class="example-box">
		<div class="head">（例）カテゴリ「company」に属する、ページ名「about」として作成したページを表示させる為のURL</div>
		<p>http://[BaserCMS設置URL]/company/about</p>
	</div>
</div>

<?php if(Configure::read('Baser.mobile')): ?>
<!-- search -->
<h3><a href="javascript:void(0);" class="slide-trigger" id="PageFilter">検索</a></h3>
<div class="function-box corner10" id="PageFilterBody">
	<?php echo $formEx->create('PageCategory', array('url' => array('action' => 'index'))) ?>
	<p>
		<small>タイプ</small>
		<?php echo $formEx->input('PageCategory.type', array(
				'type'		=> 'select',
				'options'	=> array('pc' => 'PC', 'mobile' => 'モバイル'),
				'escape'	=> false)) ?>　
		<?php echo $formEx->submit('検　索', array('div' => false, 'class' => 'btn-orange button')) ?> </p>
	<?php $formEx->end() ?>
</div>
<?php endif ?>

<!-- list -->
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TablePageCategoryCategories">
	<tr>
		<th style="width:100px">操作</th>
		<th>NO</th>
		<th>ページカテゴリ名</th>
		<th>ページカテゴリタイトル</th>
		<th>登録日<br />更新日</th>
	</tr>
<?php if(!empty($dbDatas)): ?>
	<?php $count=0; ?>
	<?php foreach($dbDatas as $key => $dbData): ?>
		<?php if ($count%2 === 0): ?>
			<?php $class=' class="altrow"'; ?>
		<?php else: ?>
			<?php $class=''; ?>
		<?php endif; ?>
	<tr<?php echo $class; ?> id="Row<?php echo $dbData['PageCategory']['id'] ?>">
		<td class="operation-button">
		<?php if($dbData['PageCategory']['name']!='mobile'): ?>
			<?php if($key != 0): ?>
			<?php $baser->link('▲', array('controller' => 'page_categories', 'action' => 'up', $dbData['PageCategory']['id'])) ?>
			<?php endif ?>
			<?php if(count($dbDatas) != ($key + 1)): ?>
			<?php $baser->link('▼', array('controller' => 'page_categories', 'action' => 'down', $dbData['PageCategory']['id'])) ?>
			<?php endif ?>
			<?php $baser->link('編集', array('action' => 'edit', $dbData['PageCategory']['id']), array('class' => 'btn-orange-s button-s'), null, false) ?>
			<?php $baser->link('削除', 
					array('action' => 'delete', $dbData['PageCategory']['id']),
					array('class'=>'btn-gray-s button-s'),
					sprintf('%s を本当に削除してもいいですか？\n\nこのカテゴリに関連するページは、どのカテゴリにも関連しない状態として残ります。', $dbData['PageCategory']['name']),
					false); ?>
		<?php endif ?>
		</td>
		<td><?php echo $dbData['PageCategory']['id']; ?></td>
		<td>
		<?php if($dbData['PageCategory']['name']!='mobile'): ?>
			<?php $baser->link($dbData['PageCategory']['name'], array('action' => 'edit', $dbData['PageCategory']['id'])); ?>
		<?php else: ?>
			<?php echo $dbData['PageCategory']['name'] ?>
		<?php endif ?>
		</td>
		<td><?php echo $dbData['PageCategory']['title']; ?></td>
		<td><?php echo $timeEx->format('y-m-d', $dbData['PageCategory']['created']); ?><br />
			<?php echo $timeEx->format('y-m-d', $dbData['PageCategory']['modified']); ?></td>
	</tr>
		<?php $count++; ?>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
<?php endif; ?>
</table>

<div class="align-center">
	<?php $baser->link('新規登録', array('action' => 'add'), array('class' => 'btn-red button')) ?>
</div>
