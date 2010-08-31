<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] テーマ一覧
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
	<p>ここではテーマを切り替えたり、テーマファイルを閲覧、編集したりとテーマの管理を行う事ができます。<br />
		なお、テーマ「core」は、BaserCMSの核となるテーマで、内包しているテーマファイルの編集、削除は行えませんが、現在のテーマへコピーする事ができます。</p>
	<ul>
		<li>テーマを切り替えるには、対象テーマの「適用」ボタンをクリックします。</li>
		<li>テーマを丸ごとコピーするには、対象テーマの「コピー」ボタンをクリックします。</li>
		<li>テーマファイルを閲覧、編集する場合は、対象テーマの「管理」ボタンをクリックします。</li>
		<li>テーマを削除するには、対象テーマの「削除」ボタンをクリックします。</li>
	</ul>
</div>
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableThemes">
	<tr>
		<th>操作</th>
		<th>テーマ名</th>
		<th>タイトル</th>
		<th>説明</th>
		<th>制作者</th>
	</tr>
	<?php if(!empty($themes)): ?>
		<?php $count=0; ?>
		<?php foreach($themes as $theme): ?>
			<?php if($theme['name']==$baser->siteConfig['theme']): ?>
				<?php $class=' class="activerow"' ?>
			<?php elseif ($count%2 === 0): ?>
				<?php $class=' class="altrow"' ?>
			<?php else: ?>
				<?php $class=''; ?>
			<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button" style="width:170px">
			<?php if($theme['name'] != 'core'): ?>
			<p>
			<?php if($theme['name']!=$baser->siteConfig['theme']): ?>
			<?php $baser->link('適用',array('action'=>'apply', $theme['name']), array('class'=>'btn-green-s button-s')) ?>
			<?php endif ?>
			<?php $baser->link('コピー',array('action'=>'copy', $theme['name']), array('class'=>'btn-red-s button-s')) ?>
			<?php $baser->link('編集',array('action'=>'edit', $theme['name']), array('class'=>'btn-orange-s button-s')) ?>
			</p>
			<?php endif ?>
			<p><?php $baser->link('管理',array('controller'=>'theme_files','action'=>'index', $theme['name']), array('class'=>'btn-red-s button-s')) ?>
			<?php if($theme['name'] != 'core'): ?>
			<?php $baser->link('削除', array('action'=>'del', $theme['name']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $theme['name']),false); ?>
			<?php endif ?></p></td>
		<td><?php echo $theme['name'] ?></td>
		<td>
			<?php if($theme['name']!='core' && $theme['screenshot']): ?>
				<?php $baser->link($baser->getImg('/themed/'.$theme['name'].'/screenshot.png',array('alt'=>$theme['title'],'width'=>'80px','align'=>'left','style'=>'margin-right:10px;border:1px solid #e2e2e2')),'/themed/'.$theme['name'].'/screenshot.png',array('rel'=>'colorbox')) ?>
			<?php endif ?>
			<?php echo $theme['title'] ?>
		</td>
		<td style="width:150px"><?php echo $theme['description'] ?></td>
		<td><?php if(!empty($theme['url']) && !empty($theme['author'])): ?>
			<?php $baser->link($theme['author'],$theme['url'],array('target'=>'_blank')) ?>
			<?php else: ?>
			<?php echo $theme['author'] ?>
			<?php endif ?></td>
	</tr>
			<?php $count++; ?>
		<?php endforeach; ?>
	<?php else: ?>
	<tr>
		<td colspan="8"><p class="no-data">データが見つかりませんでした。</p></td>
	</tr>
	<?php endif; ?>
</table>
