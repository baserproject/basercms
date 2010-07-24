<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] テーマファイル一覧
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
$writable = true;
if((is_dir($fullpath) && !is_writable($fullpath)) || $theme == 'core'){
	$writable = false;
}
?>
<script type="text/javascript">
$(function(){
	$("#ThemeFileFile").change(function(){
		$("#ThemeFileUpload").submit();
	});
});
</script>

<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>ここでは各テーマファイルの閲覧、編集、削除等を行う事ができます。<br />
		なお、テーマ「core」は、BaserCMSの核となるテーマで、内包しているテーマファイルの編集、削除は行えませんが、現在のテーマへコピーする事ができます。</p>
	<ul>
		<li>上層のフォルダへ移動するには、
			<?php $baser->img('up.png',array('alt'=>'上へ','width'=>18)) ?>
			ボタンをクリックします。（現在の位置がテーマフォルダの最上層の場合は表示されません）</li>
		<li>新しいフォルダを作成するには、「フォルダ新規作成」ボタンをクリックします。</li>
		<li>新しいテーマファイルを作成するには、「ファイル新規作成」ボタンをクリックします。</li>
		<li>ご自分のパソコン内のファイルをアップロードするには、「選択」ボタンをクリックし、対象のファイルを選択します。</li>
		<li>テーマファイルをコピーするには、対象ファイルの「コピー」ボタンをクリックします。</li>
		<li>テーマファイルを閲覧、編集する場合は、対象ファイルの「編集」をクリックします。</li>
		<li>テーマファイルを削除するには、対象ファイルの「削除」ボタンをクリックします。</li>
		<li>テーマファイルを現在のテーマにコピーするには、対象ファイル・フォルダの「表示」をクリックし、その後表示される画面下の「現在のテーマにコピー」をクリックします。（core テーマのみ）</li>
	</ul>
	<p>テーマファイルの種類は次の６つとなります。</p>
	<ul>
		<li>レイアウト：Webページの枠組となるテンプレートファイル</li>
		<li>エレメント：共通部品となるテンプレートファイル</li>
		<li>コンテンツ：Webページのコンテンツ部分のテンプレートファイル</li>
		<li>CSS：カスケーディングスタイルシートファイル</li>
		<li>イメージ：写真や背景等の画像ファイル</li>
		<li>Javascript：Javascriptファイル</li>
	</ul>
</div>
<p><strong>現在の位置：<?php echo $currentPath ?>
	<?php if(!$writable): ?>
	　<span style="color:#FF3300">[書込不可]</span>
	<?php endif ?>
	</strong> </p>
<p>
	<?php if($path): ?>
	<?php $baser->link($baser->getImg('up.gif',array('alt'=>'上へ')),array('action'=>'index', $theme,$plugin, $type, dirname($path))) ?>
	<?php endif ?>
</p>
<table cellpadding="0" cellspacing="0" class="admin-col-table-01" id="TableUsers">
	<tr>
		<th>操作</th>
		<th>フォルダ名／テーマファイル名</th>
	</tr>
	<?php if(!empty($themeFiles)): ?>
		<?php $count=0; ?>
		<?php foreach($themeFiles as $themeFile): ?>
			<?php if ($count%2 === 0): ?>
				<?php $class=' class="altrow"' ?>
			<?php else: ?>
				<?php $class=''; ?>
			<?php endif; ?>
	<tr<?php echo $class; ?>>
		<td class="operation-button">
			<?php if($themeFile['type']=='folder'): ?>
			<?php $baser->link('開く',array('action'=>'index', $theme, $plugin, $type, $path, $themeFile['name']), array('class'=>'btn-green-s button-s')) ?>
			<?php endif ?>
			<?php if($writable): ?>
			<?php $baser->link('コピー',array('action'=>'copy', $theme, $type, $path, $themeFile['name']), array('class'=>'btn-red-s button-s')) ?>
				<?php if($themeFile['type']=='folder'): ?>
			<?php $baser->link('編集',array('action'=>'edit_folder', $theme, $type, $path, $themeFile['name']), array('class'=>'btn-orange-s button-s')) ?>
				<?php else: ?>
			<?php $baser->link('編集',array('action'=>'edit', $theme, $type, $path, $themeFile['name']), array('class'=>'btn-orange-s button-s')) ?>
				<?php endif ?>
			<?php $baser->link('削除', array('action'=>'del', $theme, $type, $path, $themeFile['name']), array('class'=>'btn-gray-s button-s'), sprintf('%s を本当に削除してもいいですか？', $themeFile['name']),false); ?>
			<?php else: ?>
				<?php if($themeFile['type']=='folder'): ?>
			<?php $baser->link('表示',array('action'=>'view_folder', $theme, $plugin, $type, $path, $themeFile['name']), array('class'=>'btn-gray-s button-s')) ?>
				<?php else: ?>
			<?php $baser->link('表示',array('action'=>'view', $theme, $plugin, $type, $path, $themeFile['name']), array('class'=>'btn-gray-s button-s')) ?>
				<?php endif ?>
			<?php endif ?></td>
		<td>
			<?php if(preg_match('/.+?(\.png|\.gif|\.jpg)$/is',$themeFile['name'])): ?>
			<?php $baser->link($baser->getImg(array('action'=>'img_thumb',100, 100, $theme, $plugin, $type, $path, $themeFile['name']),array('alt'=>$themeFile['name'])),array('action'=>'img',$theme,$plugin,$type, $path,$themeFile['name']),array('rel'=>'colorbox','title'=>$themeFile['name'],'style'=>'display:block;padding:10px;float:left;background-color:#FFFFFF'),null,false) ?>
			<?php echo $themeFile['name'] ?>
			<?php elseif($themeFile['type'] == 'folder'): ?>
			<?php $baser->img('folder.gif',array('alt'=>$themeFile['name'])) ?>
			<?php echo $themeFile['name'] ?>/
			<?php else: ?>
			<?php $baser->img('file.gif',array('alt'=>$themeFile['name'])) ?>
			<?php echo $themeFile['name'] ?>
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
<div class="align-center">
	<?php if($writable): ?>
	<?php echo $formEx->create('ThemeFile',array('id'=>'ThemeFileUpload','url'=>array('action'=>'upload',$theme,$plugin,$type,$path),'enctype' => 'multipart/form-data')) ?> <?php echo $formEx->file('ThemeFile.file',array()) ?> <?php echo $formEx->end() ?>
	<?php $baser->link('フォルダ新規作成',array('action'=>'add_folder',$theme,$type,$path),array('class'=>'btn-orange button')) ?>
	<?php endif ?>
	<?php if(($path || $type != 'etc') && $type != 'img' && $writable): ?>
	<?php $baser->link('ファイル新規作成',array('action'=>'add',$theme,$type,$path),array('class'=>'btn-red button')) ?>
	<?php endif ?>
</div>
