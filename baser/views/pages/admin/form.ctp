<?php
/* SVN FILE: $Id$ */
/**
 * [管理画面] ページ フォーム
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
<script type="text/javascript">
$(function(){
	pageCategoryIdChangeHandler();
/**
 * プレビューボタンクリック時イベント
 */
	$("#BtnPreview").click(function(){
		var action = $("#PageForm").attr('action');
		$("#PageForm").attr('action','<?php echo $this->base ?>/admin/pages/preview');
		$("#PageForm").attr('target','_blank');
		$("#PageForm").submit();
		$("#PageForm").attr('action',action);
		$("#PageForm").attr('target','_self');
		return false;
	});
/**
 * フォーム送信時イベント
 */
	$("#PageForm").submit(function(){
		if($("#PageReflectMobile").attr('checked')){
			if(!confirm('このページを元にモバイルページを作成します。いいですか？\n\n'+
						' ※ 「mobile」フォルダの同階層に保存します。\n'+
						' ※ 既に存在する場合は上書きします。')){
				return false;
			}
		}
	});
/**
 * カテゴリ変更時イベント
 */
	$("#PagePageCategoryId").change(pageCategoryIdChangeHandler);
});
/**
 * モバイル反映欄の表示設定
 */

function pageCategoryIdChangeHandler() {
	var mobileCategoryIds = [<?php echo implode(',', $mobileCategoryIds) ?>];
	var pageCategoryId = $("#PagePageCategoryId").val();
	var mobile = false;
	if(pageCategoryId){
		for (id in mobileCategoryIds){
			if(id == pageCategoryId){
				mobile = true;
				break;
			}
		}
	}
	// mobileが1である前提
	if(!mobile){
		$("#RowReflectMobile").show();
	}else{
		$("#PageReflectMobile").attr('checked', false);
		$("#RowReflectMobile").hide();
	}
}
</script>

<h2>
	<?php $baser->contentsTitle() ?>
	&nbsp;<?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpAdmin','class'=>'slide-trigger','alt'=>'ヘルプ')) ?></h2>
<div class="help-box corner10 display-none" id="helpAdminBody">
	<h4>ユーザーヘルプ</h4>
	<p>WEBページとして表示させる「ページ」の登録を行います。</p>
	<ul>
		<li>ワード感覚でWEBページの作成を行う事ができます。</li>
		<li>タイトル・説明文には、ページを特徴づけるキーワードを入れましょう。<br />
			検索エンジン対策として有用です。</li>
		<li>ページを作成してもすぐに公開しない場合は、公開状態を「公開しない」にしておきます。</li>
		<li>「公開しない」にしたページを確認するには、画面下の「確認」ボタンをクリックします。</li>
	</ul>
</div>

<?php if($this->action == 'admin_edit'): ?>
	<?php if($formEx->value('Page.status')): ?>
<p><strong>このページのURL：<?php $baser->link($baser->getUri('/'.$url),'/'.$url,array('target'=>'_blank')) ?></strong></p>
	<?php else: ?>
<p><strong>このページのURL：<?php echo $baser->getUri('/'.$url) ?></strong></p>
	<?php endif ?>
<?php endif ?>

<p><small><span class="required">*</span> 印の項目は必須です。</small></p>
<?php echo $formEx->create('Page',array('id'=>'PageForm')) ?>
<?php echo $formEx->hidden('Page.sort') ?>
<table cellpadding="0" cellspacing="0" class="admin-row-table-01">
	<?php if($this->action == 'admin_edit'): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Page.id', 'NO') ?></th>
		<td class="col-input"><?php echo $formEx->text('Page.id', array('size'=>20,'maxlength'=>255,'readonly'=>'readonly')) ?>&nbsp; </td>
	</tr>
	<?php endif; ?>
	<?php $categories = $formEx->getControlSource('page_category_id') ?>
	<?php if($categories): ?>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Page.page_category_id', 'カテゴリ') ?></th>
		<td class="col-input"><?php echo $formEx->select('Page.page_category_id',$categories,null,array('escape'=>false)) ?> <?php echo $formEx->error('Page.page_category_id') ?>&nbsp;</td>
	</tr>
	<?php else: ?>
	<?php echo $formEx->hidden('Page.page_category_id') ?>
	<?php endif ?>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Page.name', 'ページ名') ?></th>
		<td class="col-input"><?php echo $formEx->text('Page.name', array('size'=>40,'maxlength'=>255)) ?> <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpName','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextName" class="helptext">
				<ul>
					<li>ページ名はURLに利用します。</li>
					<li>.htmlなどの拡張子は不要です。</li>
					<li>日本語の入力が可能です。</li>
				</ul>
			</div>
			<?php echo $formEx->error('Page.name') ?></td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Page.title', 'タイトル') ?></th>
		<td class="col-input"><?php echo $formEx->text('Page.title', array('size'=>40,'maxlength'=>255)) ?> <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpTitle','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextTitle" class="helptext">
				<ul>
					<li>タイトルはTitleタグに利用し、ブラウザのタイトルバーに表示されます。</li>
					<li>タイトルタグの出力するには、レイアウトテンプレートに次のように記述します。<br />
						&lt;?php $baser->title() ?&gt;<br />
						<small>※ タイトルには、サイト基本設定で設定されたWEBサイト名が自動的に追加されます。<br />
						トップページの場合など、WEBサイト名のみをタイトルバーに表示したい場合は空にします。</small></li>
				</ul>
			</div>
			<?php echo $formEx->error('Page.title') ?></td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Page.description', '説明文') ?></th>
		<td class="col-input"><?php echo $formEx->textarea('Page.description', array('cols'=>60,'rows'=>2)) ?> <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpDescription','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextDescription" class="helptext">
				<ul>
					<li>説明文はMetaタグのdescription属性に利用されます。</li>
					<li>他のページと重複しない説明文を推奨します。</li>
					<li>Metaタグを出力する場合は、レイアウトテンプレートに次のように記述します。<br />
						&lt;?php $baser->description() ?&gt;<br />
						<small>※ 省略した場合、上記タグではサイト基本設定で設定された説明文が出力されます。</small></li>
				</ul>
			</div>
			<?php echo $formEx->error('Page.description') ?></td>
	</tr>
	<tr>
		<th class="col-head"><?php echo $formEx->label('Page.contents', '本文') ?></th>
		<td class="col-input"><?php echo $ckeditor->textarea('Page.contents',array('cols'=>60, 'rows'=>20)) ?> <?php echo $formEx->error('Page.contents') ?>&nbsp; </td>
	</tr>
	<tr>
		<th class="col-head"><span class="required">*</span>&nbsp;<?php echo $formEx->label('Page.status', '公開状態') ?></th>
		<td class="col-input"><?php echo $formEx->radio('Page.status', $textEx->booleanDoList("公開"),array("legend"=>false,"separator"=>"&nbsp;&nbsp;")) ?> <?php echo $formEx->error('Page.status') ?> &nbsp; </td>
	</tr>
	<tr id="RowReflectMobile" style="display: none">
		<th class="col-head"><?php echo $formEx->label('Page.status', 'モバイル') ?></th>
		<td class="col-input">
			<?php echo $formEx->checkbox('Page.reflect_mobile', array('label'=>'モバイルページとしてコピー')) ?> <?php echo $html->image('img_icon_help_admin.png',array('id'=>'helpReflectMobile','class'=>'help','alt'=>'ヘルプ')) ?>
			<div id="helptextReflectMobile" class="helptext">
				<ul>
					<li>このページのデータを元にモバイルページとしてコピーする場合はチェックを入れます。</li>
					<li>モバイルページは「mobile」フォルダ内の同階層に保存します。</li>
					<li>モバイルページが既に存在するする場合は上書きします。</li>
				</ul>
			</div>
			<?php if(!empty($mobileExists)): ?>
			<br />&nbsp;<?php $baser->link('≫ モバイルページの編集画面に移動',array($mobileExists)) ?>
			<?php endif ?>
		</td>
	</tr>
</table>
<div class="submit">
	<?php if($this->action == 'admin_add'): ?>
	<?php echo $formEx->end(array('label'=>'登　録','div'=>false,'class'=>'btn-red button')) ?>
	<?php echo $formEx->end(array('label'=>'保存前確認','div'=>false,'class'=>'btn-green button','id'=>'BtnPreview')) ?>
	<?php elseif ($this->action == 'admin_edit'): ?>
	<?php echo $formEx->end(array('label'=>'更　新','div'=>false,'class'=>'btn-orange button')) ?>
	<?php echo $formEx->end(array('label'=>'保存前確認','div'=>false,'class'=>'btn-green button','id'=>'BtnPreview')) ?>
	<?php $baser->link('削　除',array('action'=>'delete', $formEx->value('Page.id')), array('class'=>'btn-gray button'), sprintf('%s を本当に削除してもいいですか？', $formEx->value('Page.name')),false); ?>
	<?php endif ?>
</div>
