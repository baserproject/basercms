<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ウィジェットエリア一覧
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>

<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ウィジェットとは簡単にWEBページの指定した場所に部品の追加・削除ができる仕組みです。<br />
		その部品の一つ一つをウィジェットと呼び、ウィジェットが集まった一つのグループをウィジェットエリアと呼びます。</p>
	<p>全体で利用するウィジェットエリアは、「<?php $baser->link("サイト基本設定",array('controller'=>'site_configs','action'=>'form')) ?>」で設定できます。また、標準プラグインである、ブログ、メールではそれぞれ別のウィジェットエリアを個別に指定する事もできます。</p>
	<ul>
	<li>新しいウィジェットエリアを作成するには、「新規登録」ボタンをクリックします。</li>
	<li>既存のウィジェットエリアを編集するには、対象のウィジェットエリアの操作欄にある「編集」ボタンをクリックします。</li>
	</ul>
	<p><small>※ なお、ウィジェットエリアを作成、編集する際には、サーバーキャッシュが削除されますので、一時的に公開ページの表示速度が遅くなってしまいますのでご注意ください。</small></p>
</div>

<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableWidgetAreas">
	<tr>
		<th>操作</th>
		<th>NO</th>
		<th>ウィジェットエリア名</th>
		<th>登録ウィジェット数</th>
		<th>登録日</th>
		<th>更新日</th>
	</tr>
	<?php if(!empty($widgetAreas)): ?>
		<?php $count=0; ?>
		<?php foreach($widgetAreas as $widgetArea): ?>
			<?php if ($count%2 === 0): ?>
				<?php $class=' class="altrow"'; ?>
			<?php else: ?>
				<?php $class=''; ?>
			<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button"><?php $baser->link('編集',array('action'=>'edit', $widgetArea['WidgetArea']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php $baser->link('削除', array('action'=>'delete', $widgetArea['WidgetArea']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $widgetArea['WidgetArea']['name']),false); ?></td>
		<td><?php echo $widgetArea['WidgetArea']['id']; ?></td>
		<td><?php $baser->link($widgetArea['WidgetArea']['name'],array('action'=>'edit', $widgetArea['WidgetArea']['id'])); ?></td>
		<td><?php echo $widgetArea['WidgetArea']['count']; ?></td>
		<td><?php echo $timeEx->format('y-m-d',$widgetArea['WidgetArea']['created']); ?></td>
		<td><?php echo $timeEx->format('y-m-d',$widgetArea['WidgetArea']['modified']); ?></td>
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
	<?php $baser->link('新規登録',array('action'=>'add'),array('class'=>'btn-red button')) ?>
</div>