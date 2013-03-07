<?php
/* SVN FILE: $Id$ */
/**
 * [ADMIN] フィード設定一覧
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.views
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
?>
<?php if($this->action != 'admin_add'): ?>

<div class="section">
	
	<h2 id="headFeedDetail">フィード一覧</h2>

	<table cellpadding="0" cellspacing="0" class="list-table" id="ListTable">
		<thead>
			<tr>
				<th scope="col"  style="width:160px" class="list-tool">
					<div>
						<?php $bcBaser->link($bcBaser->getImg('admin/btn_add.png', array('width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn')), array('controller' => 'feed_details', 'action' => 'add', $bcForm->value('FeedConfig.id'))) ?>
					</div>
<?php if($bcBaser->isAdminUser()): ?>
					<div>
						<?php echo $bcForm->checkbox('ListTool.checkall', array('title' => '一括選択')) ?>
						<?php echo $bcForm->input('ListTool.batch', array('type' => 'select', 'options' => array('del' => '削除'), 'empty' => '一括処理')) ?>
						<?php echo $bcForm->button('適用', array('id' => 'BtnApplyBatch', 'disabled' => 'disabled')) ?>
					</div>
<?php endif ?>
				</th>
				<th scope="col">フィード名</th>
				<th scope="col">カテゴリフィルター</th>
				<th scope="col">キャッシュ時間</th>
				<th scope="col">登録日<br />更新日</th>
			</tr>
		</thead>
		<tbody>
		<?php if(!empty($feedConfig['FeedDetail'])): ?>
			<?php foreach($feedConfig['FeedDetail'] as $feedDetail): ?>
				<?php $bcBaser->element('feed_details/index_row', array('data' => $feedDetail)) ?>
			<?php endforeach; ?>
		<?php else: ?>
			<tr>
				<td colspan="6"><p class="no-data">データが見つかりませんでした。「追加する」ボタンをクリックしてフィード詳細を登録してください。</p></td>
			</tr>
		<?php endif; ?>
		</tbody>
	</table>

</div>

<?php endif ?>