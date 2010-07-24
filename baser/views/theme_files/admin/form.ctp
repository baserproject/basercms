<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] テーマファイル登録・編集
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
$imgExts = array('png','gif','jpg');
?>

<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>テーマファイルを分類する為のフォルダの作成・編集・削除が行えます。</p>
	<ul>
		<li>テーマファイルを作成・編集するには、ファイル名、内容を入力して「作成」または、「更新」ボタンをクリックします。</li>
		<li>テーマファイルを削除するには、「削除」ボタンをクリックします。</li>
		<li>現在のテーマにコピーするには、「現在のテーマにコピー」ボタンをクリックします。（core テーマのみ）</li>
	</ul>
	<p><small>※ 画像ファイルの編集は行えません。新しい画像をアップロードするには、一覧よりアップロードしてください</small></p>
</div>
<p><strong>現在の位置：<?php echo $currentPath ?></strong></p>
<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php if($this->action == 'admin_add'): ?>
<?php echo $formEx->create('ThemeFile',array('id'=>'ThemeFileForm','url'=>array('action'=>'add',$theme,$plugin, $type,$path))) ?>
<?php elseif($this->action == 'admin_edit'): ?>
<?php echo $formEx->create('ThemeFile',array('id'=>'ThemeFileForm','url'=>array('action'=>'edit',$theme,$plugin, $type,$path))) ?>
<?php endif ?>
<?php echo $formEx->hidden('ThemeFile.parent') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('ThemeFile.name', 'ファイル名') ?></th>
		<td class="col-input">
			<?php if($this->action != 'admin_view'): ?>
			<?php echo $formEx->text('ThemeFile.name', array('size'=>30,'maxlength'=>255)) ?> .<?php echo $formEx->value('ThemeFile.ext') ?><?php echo $formEx->hidden('ThemeFile.ext') ?> <?php echo $html->image('img_icon_help_admin.gif',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li>テーマファイル名は半角で入力してください。</li>
				</ul>
			</div>
			<?php echo $formEx->error('ThemeFile.name') ?>
			<?php else: ?>
			<?php echo $formEx->text('ThemeFile.name', array('size'=>30,'readonly'=>'readonly')) ?> .<?php echo $formEx->value('ThemeFile.ext') ?><?php echo $formEx->hidden('ThemeFile.ext') ?>
			<?php endif ?></td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('ThemeFile.contents', '内容') ?></th>
		<td class="col-input">
			<?php if(in_array($formEx->value('ThemeFile.ext'),$imgExts) && ($this->action != 'admin_edit' || $this->action != 'admin_view')): ?>
			<div class="align-center" style="margin:20px auto">
				<?php $baser->link($baser->getImg(array('action'=>'img_thumb',550, 550, $theme,$plugin,$type, $path),array('alt'=>basename($path))),array('action'=>'img', $theme,$plugin,$type, $path),array('rel'=>'colorbox','title'=>basename($path))) ?>
			</div>
			<?php else: ?>
				<?php if($this->action != 'admin_view'): ?>
			<?php echo $formEx->textarea('ThemeFile.contents',array('cols'=>80, 'rows'=>20)) ?> <?php echo $formEx->error('ThemeFile.contents') ?>&nbsp;
				<?php else: ?>
			<?php echo $formEx->textarea('ThemeFile.contents',array('cols'=>80, 'rows'=>20, 'readonly'=>'readonly')) ?>
				<?php endif ?>
			<?php endif ?></td>
	</tr>
</table>
<div class="submit">
	<?php if($this->action == 'admin_add'): ?>
	<?php $baser->link('一覧に戻る', array('action'=>'index',$theme, $plugin, $type, $path), array('class'=>'btn-gray button')); ?>
	<?php else: ?>
	<?php $baser->link('一覧に戻る', array('action'=>'index',$theme, $plugin, $type, dirname($path)), array('class'=>'btn-gray button')); ?>
	<?php endif ?>
	<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->end(array('label'=>'作　成','div'=>false,'class'=>'btn-red button')) ?>
	<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php $baser->link('削　除',array('action'=>'del',$theme, $plugin, $type , $path) , array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', basename($path)),false); ?>
	<?php else: ?>
	<?php // プラグインのアセットの場合はコピーできない ?>
		<?php if($theme == 'core' && !(($type=='css' || $type=='js' || $type=='img') && $plugin)): ?>
	<?php $baser->link('現在のテーマにコピー',array('action'=>'copy_to_theme',$theme, $plugin, $type , $path) , array('class'=>'btn-red button'), '本当に現在のテーマ「'.Inflector::camelize($theme).'」にコピーしてもいいですか？\n既に存在するファイルは上書きされます。'); ?>
		<?php endif ?>
	<?php endif ?>
</div>
