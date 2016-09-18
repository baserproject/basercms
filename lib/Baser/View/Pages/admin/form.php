<?php
/**
 * [ADMIN] ページ登録・編集フォーム
 * 
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 2.0.0
 * @license			http://basercms.net/license/index.html
 */
$this->BcBaser->css('admin/ckeditor/editor', array('inline' => true));
$this->BcBaser->link('&nbsp;', array('action' => 'preview', $previewId), array('style' => 'display:none', 'id' => 'LinkPreview'));
$pageTypes = array();
if($reflectMobile || $reflectSmartphone) {
	$pageTypes = array('1' => 'PC');	
}
if($reflectMobile) {
	$pageTypes['2'] = 'モバイル';
}
if($reflectSmartphone) {
	$pageTypes['3'] = 'スマートフォン';
}
?>


<script type="text/javascript">
$(window).load(function() {
	$("#PageName").focus();
});
$(function(){
	
	$("input[type=text]").each(function(){
		$(this).keypress(function(e){
			if(e.which && e.which === 13) {
				return false;
			}
			return true;
		});
	});
	
	pageCategoryIdChangeHandler();
/**
 * プレビューボタンクリック時イベント
 */
	$("#BtnPreview").click(function(){
		var contents = $("#PageContents").val();
		if(typeof editor_contents_tmp != "undefined") {
			$("#PageContents").val(editor_contents_tmp.getData());
		}
		$.ajax({
			type: "POST",
			url: $("#PreviewUrl").html(),
			data: $("#PageForm").serialize(),
			success: function(result){
				if(result) {
					$("#LinkPreview").trigger("click");
				} else {
					alert('プレビューの読み込みに失敗しました。');
				}
			}
		});
		$("#PageContents").val(contents);
		return false;
	});
/**
 * フォーム送信時イベント
 */
	$("#BtnSave").click(function(){
		if($("#PageReflectMobile").attr('checked')){
			if(!confirm('このページを元にモバイルページを作成します。いいですか？\n\n'+
						' ※ モバイルカテゴリの同階層に保存します。\n'+
						' ※ 既に存在する場合は上書きします。')){
				return false;
			}
		}
		if($("#PageReflectSmartphone").attr('checked')){
			if(!confirm('このページを元にスマートフォンページを作成します。いいですか？\n\n'+
						' ※ スマートフォンカテゴリの同階層に保存します。\n'+
						' ※ 既に存在する場合は上書きします。')){
				return false;
			}
		}
		if(typeof editor_contents_tmp != "undefined") {
			editor_contents_tmp.execCommand('synchronize');
		}
		$("#PageMode").val('save');
		$("#PageForm").submit();
		return false;
	});
/**
 * カテゴリ変更時イベント
 */
	$("#PagePageCategoryId").change(pageCategoryIdChangeHandler);
	$('input[name="data[Page][page_type]"]').click(pageTypeChengeHandler);

/**
 * 連動機能変更時イベント
 */
	$("#PageUnlinkedMobile").change(setStateReflectMobile);
	$("#PageUnlinkedSmartphone").click(setStateReflectSmartphone);
	setStateReflectMobile();
	setStateReflectSmartphone(); 
});
/**
 * モバイル反映欄の表示設定
 */
function pageCategoryIdChangeHandler() {

	var pageType = 1;
	var previewWidth;
	
	if($("#ReflectMobileOn").html() || $("#ReflectSmartphoneOn").html()) { 

		var pageCategoryId = $("#PagePageCategoryId").val();

		if($('input[name="data[Page][page_type]"]:checked').val() == 2 && !pageCategoryId) {
			pageCategoryId = $("#RootMobileId").html();
		} else if($('input[name="data[Page][page_type]"]:checked').val() == 3 && !pageCategoryId) {
			pageCategoryId = $("#RootSmartphoneId").html();
		}

		// モバイルカテゴリ判定
		if($('input[name="data[Page][page_type]"]:checked').val() == 2) {
			pageType = 2;
		} else if($('input[name="data[Page][page_type]"]:checked').val() == 3) {
			pageType = 3;
		}

		// モバイルカテゴリを選択した場合は表示しない
		if(pageType != 2 && $("#Action").html() == 'admin_edit'){
			$.ajax({
				type: "GET",
				url: $("#CheckAgentPageAddableUrl").html()+'/mobile/'+pageCategoryId,
				beforeSend: function() {
					$("#AjaxLoader").show();
				},
				success: function(result){
					if(result) {
						changeStateMobile(pageType, true);
					} else {
						changeStateMobile(pageType, false);
					}
				},
				complete: function() {
					$("#AjaxLoader").hide();
				}
			});
		}else{
			changeStateMobile(pageType, false);
		}
		// スマートフォンカテゴリを選択した場合は表示しない
		if(pageType != 3 && $("#Action").html() == 'admin_edit'){
			$.ajax({
				type: "GET",
				url: $("#CheckAgentPageAddableUrl").html()+'/smartphone/'+pageCategoryId,
				beforeSend: function() {
					$("#AjaxLoader").show();
				},
				success: function(result){
					if(result) {
						changeStateSmartphone(pageType, true);
					} else {
						changeStateSmartphone(pageType, false);
					}
				},
				complete: function() {
					$("#AjaxLoader").hide();
				}
			});
		}else{
			changeStateSmartphone(pageType, false);
		}

	}
	
	// プレビューをモバイル用にリサイズする
	if(pageType == 2) {
		previewWidth = '270px';
	}else if(pageType == 3) {
		previewWidth = '350px';
	} else {
		previewWidth = '90%';
	}

	$("#LinkPreview").colorbox({width: previewWidth, height:"90%", iframe:true});
	
}

/**
 * モバイルコピー機能の表示設定
 */
function setStateReflectMobile() {

	if (!$("#PageUnlinkedMobile").size() || $("#PageUnlinkedMobile").attr('checked')) {
		changeStateReflectMobile(true);
	} else {
		changeStateReflectMobile(false);
	}
	
}
/**
 * スマホコピー機能の表示設定
 */
function setStateReflectSmartphone() {

	if (!$("#PageUnlinkedSmartphone").size() || $("#PageUnlinkedSmartphone").attr('checked')) {
		changeStateReflectSmartphone(true);
	} else {
		changeStateReflectSmartphone(false);
	}
  
} 
/**
 * モバイルオプション表示切り替え
 */
function changeStateMobile(pageType, use) {
 
	if(use) {
		if(pageType == 2 || pageType == 3) {
			if($("#PageUnlinkedMobile").attr('checked')) {
				$("#RowMobile").show();
				$("#DivUnlinkedMobile").hide();
			} else {
				$("#RowMobile").hide();
			}
		} else {
			$("#RowMobile").show();
		}
	}else{
		$("#RowMobile").hide();
	}

} 

/**
 * スマートフォンオプション表示切り替え
 */
function changeStateSmartphone(pageType, use) {
  
	if(use) {
		if(pageType == 2 || pageType == 3) {
			if($("#PageUnlinkedSmartphone").attr('checked')) {
				$("#RowSmartphone").show();
				$("#DivUnlinkedSmartphone").hide();
			} else {
				$("#RowSmartphone").hide();
			}
		} else {
			$("#RowSmartphone").show();
		}
	}else{
		$("#RowSmartphone").hide();
	}
  
} 

/**
 * モバイルコピー機能表示切り替え
 */
function changeStateReflectMobile(use) {

	if(use) {
		$("#DivReflectMobile").show();
	}else{
		$("#PageReflectMobile").attr('checked', false);
		$("#DivReflectMobile").hide();
	}
  
} 

/**
 * スマートフォンコピー機能表示切り替え
 */
function changeStateReflectSmartphone(use) {

	 if(use) {
		 $("#DivReflectSmartphone").show();
	 }else{
		 $("#PageReflectSmartphone").attr('checked', false);
		 $("#DivReflectSmartphone").hide();
	 }
   
 } 

function pageTypeChengeHandler() {
	
	var pageType = $('input[name="data[Page][page_type]"]:checked').val();
	var options = {};
	if($("#PageId").val()) {
		options = {
			"data[Option][own]":true,
			"data[Option][empty]": '指定しない',
			"data[Option][currentPageCategoryId]": $("#PageCategoryId").html(),
			"data[Option][currentOwnerId]": $("#PageCategoryOwnerId").html()
		};
	} else {
		options = {
			"data[Option][own]":true,
			"data[Option][empty]": '指定しない'
		};
	}
	$.bcToken.check(function(){
		$.extend(options, {
			_Token: {
				key: $.bcToken.key
			}
		});
		$.ajax({
			type: "POST",
			data: options,
			url: $("#AjaxCategorySourceUrl").html()+'/'+pageType,
			beforeSend: function() {
				$("#CategoryAjaxLoader").show();
			},
			success: function(result){
				if(result) {
					$("#PagePageCategoryId option").remove();
					$("#PagePageCategoryId").append($(result).find('option'));
					$("#PagePageCategoryId").val('');
					pageCategoryIdChangeHandler();
				}
			},
			complete: function() {
				$("#CategoryAjaxLoader").hide();
			}
		});
	}, {loaderType: 'none'});

}
</script>

<div class="display-none">
	<div id="PreviewUrl"><?php $this->BcBaser->url(array('action' => 'create_preview', $previewId)) ?></div>
	<div id="CheckAgentPageAddableUrl"><?php $this->BcBaser->url(array('action' => 'check_agent_page_addable')) ?></div>
	<div id="AjaxCategorySourceUrl"><?php $this->BcBaser->url(array('action' => 'ajax_category_source')) ?></div>
	<div id="PageCategoryId"><?php echo $this->BcForm->value('PageCategory.id') ?></div>
	<div id="PageCategoryOwnerId"><?php echo $this->BcForm->value('PageCategory.owner_id') ?></div>
	<div id="RootMobileId"><?php echo $rootMobileId ?></div>
	<div id="RootSmartphoneId"><?php echo $rootSmartphoneId ?></div>
	<div id="ReflectMobileOn"><?php echo $reflectMobile ?></div>
	<div id="ReflectSmartphoneOn"><?php echo $reflectSmartphone ?></div>
	<div id="Action"><?php echo $this->request->action ?></div>
</div>

<?php if ($this->request->action == 'admin_edit'): ?>
	<div class="em-box align-left">
		<?php if ($this->BcForm->value('Page.status')): ?>
			<strong>このページのURL：<?php $this->BcBaser->link($this->BcBaser->getUri($url), $url) ?></strong>
		<?php else: ?>
			<strong>このページのURL：<?php echo $this->BcBaser->getUri($url) ?></strong>
		<?php endif ?>
		<br />
		<strong>プレビュー用URL：<?php $this->BcBaser->link(
				$this->BcBaser->getUri(array('action'=>'preview', $this->data['Page']['id'])),
				$this->BcBaser->getUri(array('action'=>'preview', $this->data['Page']['id'])),
				array('target' => '_blank')
		); ?></strong>
	</div>
<?php endif ?>

<?php echo $this->BcForm->create('Page', array('id' => 'PageForm')) ?>
<?php echo $this->BcForm->input('Page.mode', array('type' => 'hidden')) ?>
<?php echo $this->BcForm->input('Page.sort', array('type' => 'hidden')) ?>

<!-- form -->
<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table">
		<?php if ($this->request->action == 'admin_edit'): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('Page.id', 'NO') ?></th>
				<td class="col-input">
					<?php echo $this->BcForm->value('Page.id') ?>
					<?php echo $this->BcForm->input('Page.id', array('type' => 'hidden')) ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ($categories): ?>
			<tr>
				<th class="col-head"><?php echo $this->BcForm->label('Page.page_category_id', 'カテゴリー') ?></th>
				<td class="col-input">
					<?php if ($pageTypes): ?>
						<?php
						echo $this->BcForm->input('Page.page_type', array(
							'type' => 'radio',
							'options' => $pageTypes))
						?></span>　
					<?php else: ?>
						<?php echo $this->BcForm->input('Page.page_type', array('type' => 'hidden')) ?></span>
					<?php endif ?>
					<?php
					echo $this->BcForm->input('Page.page_category_id', array(
						'type' => 'select',
						'options' => $categories,
						'escape' => false))
					?>
			<?php $this->BcBaser->img('admin/ajax-loader-s.gif', array('id' => 'CategoryAjaxLoader', 'class' => 'display-none', 'style' => 'vertical-align:middle')) ?>
			<?php echo $this->BcForm->error('Page.page_category_id') ?>
				</td>
			</tr>
<?php else: ?>
					<?php echo $this->BcForm->hidden('Page.page_category_id') ?>
				<?php endif ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Page.name', 'ページ名') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
<?php echo $this->BcForm->input('Page.name', array('type' => 'text', 'size' => 40, 'maxlength' => 50)) ?>
<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
<?php echo $this->BcForm->error('Page.name') ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>ページ名はURLに利用します。</li>
						<li>.htmlなどの拡張子は不要です。</li>
						<li>日本語の入力が可能です。</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Page.title', 'タイトル') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Page.title', array('type' => 'text', 'size' => 40, 'maxlength' => 255, 'counter' => true, 'class' => 'full-width')) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpTitle', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('Page.title') ?>
				<div id="helptextTitle" class="helptext">
					<ul>
						<li>タイトルはTitleタグに利用し、ブラウザのタイトルバーに表示されます。</li>
						<li>タイトルタグの出力するには、レイアウトテンプレートに次のように記述します。<br />
							&lt;?php $this->BcBaser->title() ?&gt;<br />
							<small>※ タイトルには、サイト基本設定で設定されたWEBサイト名が自動的に追加されます。<br />
								トップページの場合など、WEBサイト名のみをタイトルバーに表示したい場合は空にします。</small></li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Page.description', '説明文') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Page.description', array('type' => 'textarea', 'cols' => 60, 'rows' => 2, 'maxlength' => 255, 'counter' => true)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpDescription', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('Page.description') ?>
				<div id="helptextDescription" class="helptext">
					<ul>
						<li>説明文はMetaタグのdescription属性に利用されます。</li>
						<li>他のページと重複しない説明文を推奨します。</li>
						<li>Metaタグを出力する場合は、レイアウトテンプレートに次のように記述します。<br />
							&lt;?php $this->BcBaser->description() ?&gt;<br />
							<small>※ 省略した場合、上記タグではサイト基本設定で設定された説明文が出力されます。</small></li>
					</ul>
				</div>
			</td>
		</tr>
	</table>
</div>

<div class="section editor-area">
	<?php echo $this->BcForm->editor('Page.contents', array_merge(array(
		'editor' => @$siteConfig['editor'],
		'editorUseDraft' => true,
		'editorDraftField' => 'draft',
		'editorWidth' => 'auto',
		'editorHeight' => '480px',
		'editorEnterBr' => @$siteConfig['editor_enter_br']
			), $editorOptions)); ?>
	<?php echo $this->BcForm->error('Page.contents') ?>
</div>

<div class="section">
	<table cellpadding="0" cellspacing="0" class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Page.status', '公開状態') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Page.status', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('公開'))) ?>
				<?php echo $this->BcForm->error('Page.status') ?>
				&nbsp;&nbsp;
				<?php echo $this->BcForm->dateTimePicker('Page.publish_begin', array('size' => 12, 'maxlength' => 10), true) ?>
				&nbsp;〜&nbsp;
				<?php echo $this->BcForm->dateTimePicker('Page.publish_end', array('size' => 12, 'maxlength' => 10), true) ?><br />
				<?php echo $this->BcForm->input('Page.exclude_search', array('type' => 'checkbox', 'label' => 'サイト内検索の検索結果より除外する')) ?>
				<?php echo $this->BcForm->error('Page.publish_begin') ?>
				<?php echo $this->BcForm->error('Page.publish_end') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Page.author_id', '作成者') ?></th>
			<td class="col-input">
				<?php if (isset($user) && $user['user_group_id'] == Configure::read('BcApp.adminGroupId')): ?>
					<?php echo $this->BcForm->input('Page.author_id', array('type' => 'select', 'options' => $users)) ?>
					<?php echo $this->BcForm->error('Page.author_id') ?>
				<?php else: ?>
					<?php if (isset($users[$this->BcForm->value('Page.author_id')])): ?>
						<?php echo $users[$this->BcForm->value('Page.author_id')] ?>
					<?php endif; ?>
				<?php echo $this->BcForm->hidden('Page.author_id') ?>
				<?php endif; ?>
			</td>
		</tr>
		<?php echo $this->BcForm->dispatchAfterForm() ?>
	</table>
</div>

<h2 class="btn-slide-form"><a href="javascript:void(0)" id="formOption">オプション</a></h2>

<div id ="formOptionBody" class="slide-body section">
	<table cellpadding="0" cellspacing="0" class="form-table">
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Page.code', 'コード') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Page.code', array(
					'type' => 'textarea',
					'cols' => 36,
					'rows' => 5,
					'style' => 'font-size:14px;font-family:Verdana,Arial,sans-serif;'
				)); ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div class="helptext">
					固定ページの本文には、ソースコードに切り替えてPHPやJavascriptのコードを埋め込む事ができますが、ユーザーが間違って削除してしまわないようにこちらに入力しておく事もできます。<br />
					入力したコードは、自動的にコンテンツ本体の上部に差し込みます。
				</div>
				<?php echo $this->BcForm->error('Page.code') ?>
			</td>
		</tr>

		<?php if (Configure::read('BcApp.mobile')): ?>
		<tr id="RowMobile">
			<th class="col-head"><?php echo $this->BcForm->label('Page.unlinked_mobile', 'モバイル') ?></th> 
			<td class="col-input">
			<?php if (@$this->BcBaser->siteConfig['linked_pages_mobile']): ?>
				<div id="DivUnlinkedMobile">
					<?php echo $this->BcForm->input('Page.unlinked_mobile', array('type' => 'checkbox', 'label' => 'このページだけ連動しない')) ?>
				</div>
			<?php endif; ?>
				<div id="DivReflectMobile">
					<?php echo $this->BcForm->input('Page.reflect_mobile', array('type' => 'checkbox', 'label' => 'モバイルページとしてコピー')) ?>
					<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
					<div class="helptext">
						<ul>
							<li>このページのデータを元にモバイルページとしてコピーする場合はチェックを入れます。</li>
							<li>モバイルページはモバイルカテゴリの同階層に保存します。</li>
							<li>モバイルページが既に存在する場合は上書きします。</li>
						</ul>
					</div>
					<?php if (!empty($mobileExists)): ?>
					<br />&nbsp;<?php $this->BcBaser->link('≫ モバイルページの編集画面に移動', array($mobileExists)) ?>
					<?php endif; ?> 
				</div>
			</td>
		</tr>
		<?php endif ?>


		<?php if (Configure::read('BcApp.smartphone')): ?>
		<tr id="RowSmartphone">
			<th class="col-head"><?php echo $this->BcForm->label('Page.unlinked_smartphone', 'スマートフォン') ?></th> 
			<td class="col-input">

			<?php if (@$this->BcBaser->siteConfig['linked_pages_smartphone']): ?>
				<div id="DivUnlinkedSmartphone">
					<?php echo $this->BcForm->input('Page.unlinked_smartphone', array('type' => 'checkbox', 'label' => 'このページだけ連動しない')) ?>
				</div>
			<?php endif; ?>
				<div id="DivReflectSmartphone">
					<?php echo $this->BcForm->input('Page.reflect_smartphone', array('type' => 'checkbox', 'label' => 'スマートフォンページとしてコピー')) ?>
					<?php echo $this->Html->image('admin/icn_help.png', array('class' => 'btn help', 'alt' => 'ヘルプ')) ?>
					<div class="helptext">
						<ul>
							<li>このページのデータを元にスマートフォンページとしてコピーする場合はチェックを入れます。</li>
							<li>スマートフォンページはスマートフォンカテゴリ内の同階層に保存します。</li>
							<li>スマートフォンページが既に存在するする場合は上書きします。</li>
						</ul>
					</div>
					<?php if (!empty($smartphoneExists)): ?>
					<br />&nbsp;<?php $this->BcBaser->link('≫ スマートフォンページの編集画面に移動', array($smartphoneExists)) ?>
					<?php endif; ?> 
				</div>
			</td>
		</tr>
		<?php endif; ?>
		<?php echo $this->BcForm->dispatchAfterForm('option') ?>
	</table>
</div>
<div class="submit">
	<?php if ($this->request->action == 'admin_add'): ?>
		<?php echo $this->BcForm->button('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
		<?php echo $this->BcForm->button('保存前確認', array('div' => false, 'class' => 'button', 'id' => 'BtnPreview')) ?>
	<?php elseif ($this->request->action == 'admin_edit'): ?>
		<?php if ($editable): ?>
			<?php echo $this->BcForm->button('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
		<?php endif ?>
		<?php echo $this->BcForm->button('保存前確認', array('div' => false, 'class' => 'button', 'id' => 'BtnPreview')) ?>
	<?php if ($editable): ?>
		<?php $this->BcBaser->link('削除', array('action' => 'delete', $this->BcForm->value('Page.id')), array('class' => 'submit-token button'), sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('Page.name')), false); ?>
	<?php endif; ?>
<?php endif; ?>
</div>

<?php echo $this->BcForm->end(); ?>
