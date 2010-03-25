<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] フィード設定一覧
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
 * @package			baser.plugins.feed.views
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<h2><?php $baser->contentsTitle() ?>&nbsp;<?php echo $html->image('help.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>フィードリーダープラグインでは、RSSフィードを読み込み、整形した上で出力する事ができます。</p>
	<ul>
		<li>フィード設定は複数登録する事ができ、任意の場所に貼り付ける事ができます。</li>
		<li>他サイトのRSSフィードも読み込む事ができます。</li>
		<li>設定ごとにデザインを変更する事ができます。</li>
		<li>新しいフィード設定を登録するには、画面下の「新規登録」ボタンをクリックします。</li>
	</ul>
</div>


<!-- pagination -->
<?php $baser->pagination('default',array(),null,false) ?>


<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableFeedConfigs">
<tr>
	<th>操作</th>
	<th><?php echo $paginator->sort(array('asc'=>'NO ▼','desc'=>'NO ▲'),'id'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'フィード設定名 ▼','desc'=>'フィード設定名 ▲'),'name'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'テンプレート ▼','desc'=>'テンプレート ▲'),'template'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'表示件数 ▼','desc'=>'表示件数 ▲'),'display_number'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'登録日 ▼','desc'=>'登録日 ▲'),'created'); ?></th>
	<th><?php echo $paginator->sort(array('asc'=>'更新日 ▼','desc'=>'更新日 ▲'),'modified'); ?></th>
</tr>
<?php if(!empty($feedConfigs)): ?>
<?php $count=0; ?>
<?php foreach($feedConfigs as $feedConfig): ?>
	<?php if ($count%2 === 0): ?>
		<?php $class=' class="altrow"'; ?>
	<?php else: ?>
		<?php $class=''; ?>
	<?php endif; ?>
	<tr<?php echo $class; ?>>
        <td class="operation-button">
            <?php $baser->link('確認','/admin/feed/feed_configs/preview/'.$feedConfig['FeedConfig']['id'],array("target"=>"_blank",'class'=>'btn-green-s button-s')) ?>
			<?php $baser->link('編集',array('action'=>'edit', $feedConfig['FeedConfig']['id']),array('class'=>'btn-orange-s button-s'),null,false) ?>
			<?php $baser->link('削除', array('action'=>'delete', $feedConfig['FeedConfig']['id']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $feedConfig['FeedConfig']['name']),false); ?>
		</td>
		<td><?php echo $feedConfig['FeedConfig']['id']; ?></td>
		<td><?php $baser->link($feedConfig['FeedConfig']['name'],array('action'=>'edit', $feedConfig['FeedConfig']['id'])) ?></td>
		<td><?php echo $feedConfig['FeedConfig']['template']; ?></td>
		<td><?php echo $feedConfig['FeedConfig']['display_number'] ?></td>
		<td><?php echo $timeEx->format('Y-m-d',$feedConfig['FeedConfig']['created']); ?></td>
		<td><?php echo $timeEx->format('Y-m-d',$feedConfig['FeedConfig']['modified']); ?></td>
	</tr>
	<?php $count++; ?>
<?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="7"><p class="no-data">データが見つかりませんでした。</p></td></tr>
<?php endif; ?>
</table>

<?php $baser->pagination('default',array(),null,false) ?>

<div class="align-center"><?php $baser->link('新規登録',array('action'=>'add'),array('class'=>'btn-red button')) ?></div>