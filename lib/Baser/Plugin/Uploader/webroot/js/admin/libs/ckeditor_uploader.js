/* SVN FILE: $Id$ */
/**
 * ファイルアップロードダイアログ用CKEditorスクリプト
 *
 * PHP versions 5
 *
 * Baser :  Basic Creating Support Project <http://basercms.net>
 * Copyright 2008 - 2013, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2013, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
if ( !CKEDITOR.dialog.exists( 'Image' ) ) {

	CKEDITOR.dialog.add( 'baserUploaderDialog', function( editor ) {
		return {
			title : 'ファイルプロパティ',
			minWidth : 800,
			minHeight : 510,
			whiteSpace : 'normal',
		/**
         * ダイアログ 起動イベント
         **/
			onShow : function() {
				this.editMode = false;
				var element = this.getParentEditor().getSelection().getSelectedElement();
				var selection = this.getParentEditor().getSelection();
				var ranges = selection.getRanges();
				var imgFlg = false;
				var formElements2 = $("#"+this.getContentElement('info', 'formElements2').domId).show();

				if ( element && element.getName() == 'img' ){
					this.editMode = true;
					imgFlg = true;
					
				} else if ( ranges.length == 1 ){
					var rangeRoot = ranges[0].getCommonAncestor( true );
					element = rangeRoot.getAscendant( 'a', true );
					if ( element && element.getAttribute( 'href' ) ){
						selection.selectElement( element );
						formElements2.hide();
					}
				}
				this.setupContent(element, imgFlg);
			},
		/**
         * OKボタン クリックイベント
         **/
			onOk : function() {

				var txtUrl = this.getContentElement('info', 'txtUrl');
				var url = txtUrl.getValue();
				var element;
				var imgFlg;
				
				if(!url){
					alert('画像を選択するか、URLを直接入力して下さい。');
					return false;
				}

				if(isImage(url)){
					// エディタにタグを配置
					element = editor.document.createElement( 'img' );
					element.setAttribute( 'alt', '' );
					imgFlg = true;
				}else{
					element = editor.document.createElement( 'a' );
					element.setAttribute('title', '');
					element.setAttribute('class','file-link');
					element.setAttribute('target','_blank');
					imgFlg = false;
				}
				
				// 各コントロールの完了処理を実行
				this.commitContent( element,imgFlg );

				if(imgFlg){
					var rdoSize = $("#"+this.getContentElement('info', 'rdoSize').domId);
					if(!this.editMode){
						// リンク先用に最大サイズを取得
						var size = '';
						if(this.getValueOf('info','rdoSize')){
							if(!rdoSize.find('input[type=radio]').eq(3).attr('disabled')){
								size = 'large';
							}else if(!rdoSize.find('input[type=radio]').eq(2).attr('disabled')){
								size = 'midium';
							}else if(!rdoSize.find('input[type=radio]').eq(1).attr('disabled')){
								size = 'small';
							}
						}
						var src = getFilePath(element.getAttribute( 'src' ),size);
						var linkElement = editor.document.createElement( 'a' );
						linkElement.setAttribute('href',src);
						linkElement.setAttribute('rel','colorbox');
						linkElement.setAttribute('title',element.getAttribute( 'alt' ));
						linkElement.append(element, false);
						
						if(this.getContentElement('info', 'chkCaption').getValue()) {
							var imageSettings = $.parseJSON($("#UploaderImageSettings").html());
							var width;
							if(this.getValueOf('info','rdoSize')) {
								width = imageSettings[this.getValueOf('info','rdoSize')]['width'];
							}
							var box = editor.document.createElement( 'div' );
							var caption = editor.document.createElement( 'div' );
							box.setAttribute('class', 'bc-caption');
							if(width) {
								box.setAttribute('style', 'width:' + width + 'px');
							}
							caption.setAttribute('class', 'bc-caption-text');
							caption.appendHtml(this.getContentElement('info', 'txtAlt').getValue());
							box.append(linkElement);
							box.append(caption);
							linkElement = box;
						}
							
						element = linkElement;
					}
				}

				editor.insertElement(element);
				
				$("#EditDialog").remove();

				return true;
				
			},
		/**
		 * キャンセルボタン クリックイベント
		 */
			onCancel : function() {
				$("#EditDialog").remove();
			},
		/**
         * コンテンツプロパティ
         **/
			contents : [
			{
				id : 'info',
				label : 'イメージ情報',
				title : 'イメージ情報',
				elements : [
				{	/* フォーム要素１列目 */
					id : 'formElements1',
					type : 'hbox',
					padding : 0,
					widths : [ '45%', '45%', '10%'],
					children : [
					{   /* URL */
						id : 'txtUrl',
						type : 'text',
						label : 'URL',
						style:'margin-right : 10px;',
						setup : function(element, imgFlg) {
							if(element){
								if(imgFlg){
									this.setValue( decodeURI(element.getAttribute( 'src' )) );
								}else{
									this.setValue( decodeURI(element.getAttribute( 'href' )) );
								}
							}
							var txtUrl = $("#"+this.domId).find('input');
							var dialog = this.getDialog();
							// イベントを登録
							txtUrl.bind('keyup',function(){
								var formElements2 = $("#"+dialog.getContentElement('info', 'formElements2').domId);
								if(isImage($(this).val())){
									formElements2.show(200);
								}else{
									formElements2.hide(200);
								}
							});
						},
						commit : function( element, imgFlg ) {
							if(imgFlg){
								element.setAttribute('src', decodeURI(this.getValue()));
							}else{
								element.setAttribute('href', decodeURI(this.getValue()));
							}
						}
					},
					{   /* 説明文 */
						id : 'txtAlt',
						type : 'text',
						label : '説明文',
						style:'margin-right : 10px;',
						setup : function(element, imgFlg) {
							if(element){
								if(imgFlg){
									this.setValue( element.getAttribute( 'alt' ) );
								} else {
									this.setValue( element.getText() );
								}
							}
						},
						commit : function( element, imgFlg ) {
							if(imgFlg){
								element.setAttribute('alt', this.getValue());
							}else{
								element.setAttribute('title', this.getValue());
								element.appendHtml(this.getValue());
							}
						}
					},
					{   /* キャプション */
						id : 'chkCaption',
						type : 'checkbox',
						label : 'キャプション',
						style:'margin-top:20px; display:block; '
					}
					]
				},
				{	/* フォーム要素２列目 */
					id : 'formElements2',
					type : 'hbox',
					padding : 0,
					widths : [ '25%', '25%', '25%', '25%'],
					children : [
					{   /* 横間隔 */
						id : 'txtHspace',
						type : 'text',
						label : '横間隔',
						style:'margin-right : 10px;',
						setup : function(element, imgFlg) {

							if(!imgFlg){
								return;
							}

							if(element){
								if(element.getAttribute( 'hspace' )){
									this.setValue( element.getAttribute( 'hspace' ) );
								}
							}
						},
						commit : function( element, imgFlg ) {

							if(!imgFlg){
								return;
							}
								
							var value = '0';
							if(this.getValue()){
								value = this.getValue();
							}
							if(value > 0) {
								element.setStyle('margin-left', value+'px');
								element.setStyle('margin-right', value+'px');
								element.setAttribute('hspace', value);
							}
						}
					},
					{   /* 縦間隔 */
						id : 'txtVspace',
						type : 'text',
						label : '縦間隔',
						style : 'margin-right : 10px;',
						setup : function(element, imgFlg) {

							if(!imgFlg){
								return;
							}
								
							if(element && element.getAttribute( 'vspace' )) {
								this.setValue( element.getAttribute( 'vspace' ) );
							}
							
						},
						commit : function( element, imgFlg ) {

							if(!imgFlg){
								return;
							}

							var value = '0';
							if(this.getValue()) {
								value = this.getValue();
							}
							if(value > 0) {
								element.setStyle('margin-top', value+'px');
								element.setStyle('margin-bottom', value+'px');
								element.setAttribute('vspace', value);
							}
						}

					},
					{   /* 行揃え */
						id : 'cmbAlign',
						type : 'select',
						/*style : 'width:90px',*/
						label : '行揃え',
						'default' : '',
						items :	[
						[ editor.lang.common.notSet , ''],
						[ '左' , 'left'],
						[ '下部(絶対的)' , 'absBottom'],
						[ '中央(絶対的)' , 'absMiddle'],
						[ 'ベースライン' , 'baseline'],
						[ '下' , 'bottom'],
						[ '中央' , 'middle'],
						[ '右' , 'right'],
						[ 'テキスト上部' , 'textTop'],
						[ '上' , 'top']
						],
						setup : function( element, imgFlg ) {
							if(!imgFlg){
								return;
							}
							if(element){
								this.setValue( element.getAttribute( 'align' ) );
							}
						},
						commit : function( element, imgFlg ) {

							if(!imgFlg){
								return;
							}

							element.setAttribute( 'align', this.getValue());
						}
					},
					{   /* サイズ */
						id : 'rdoSize',
						type : 'radio',
						label : 'サイズ',
						'default' : 'small',
						items :
						[
						[ '元サイズ' , ''],
						[ '小' , 'small'],
						[ '中' , 'midium'],
						[ '大' , 'large']
						],
						setup : function( element, imgFlg ) {

							var dialog = this.getDialog();
							var rdoSize = $("#"+this.domId);
							rdoSize.find('input[type=radio]').attr('disabled',true);
							rdoSize.find('input[type=radio]').eq(0).attr('disabled',false);

							rdoSize.find('input[type=radio]').click(function(){
								if($(this).attr('checked')){
									dialog.setValueOf('info','txtUrl',getFilePath(dialog.getValueOf('info','txtUrl'),dialog.getValueOf('info','rdoSize')));
								}
							});

							if(element && imgFlg){
								// 画像のサイズを取得する
								$.get(baseUrl + adminPrefix + '/uploader/uploader_files/ajax_exists_images/'+getFileName(element.getAttribute( 'src' ),''),null,function(res){
									if(res){
										rdoSize.find('input[type=radio]').eq(1).attr('disabled',!res.small);
										rdoSize.find('input[type=radio]').eq(2).attr('disabled',!res.midium);
										rdoSize.find('input[type=radio]').eq(3).attr('disabled',!res.large);
									}else{
										rdoSize.find('input[type=radio]').attr('disabled',true);
									}
								},'json');
								this.setValue(getSizeByFile(element.getAttribute( 'src' )));
							}
						},
						commit : function( element, imgFlg) {

							if(!imgFlg){
								return;
							}

						}
					}
					]
				},
				{   /* fileManager */
					id : 'fileManager',
					type : 'vbox',
					padding : 0,
					children : [],
					setup : function() {
						var fileList = $("#"+this.domId);
						
						var inner = '<div id="UploaderSearch" class="corner5" style="display:none"></div>' + 
									'<div class="inner" style="text-align:center"><img style="margin-top:120px" src="'+baseUrl+'img/admin/ajax-loader.gif" /></div>';
						fileList.html(inner);
						
						var dialog = this.getDialog();
						var listId = Math.floor(Math.random()*99999999+1);
						$.ajax({
							type: "GET",
							dataType: "html",
							url: baseUrl + adminPrefix + "/uploader/uploader_files/ajax_get_search_box/"+listId,
							success: function(res){
								$("#UploaderSearch").html(res);
								$("#UploaderSearch").slideDown();
							},
							error: function(msg,textStatus, errorThrown) {
								alert(textStatus);
							}
						});
						$.ajax({
							type: "GET",
							dataType: "html",
							url: baseUrl + adminPrefix + "/uploader/uploader_files/index/"+listId,
							success: function(res){

								// リストをセット
								fileList.find('.inner').html(res);

								// リストのロード完了イベント
								$("#FileList"+listId).bind('filelistload',function() {

									// ファイル選択イベント
									$('.selectable-file').click(function() {

										// URLの拡張子で画像かどうかを判別
										// ※ URLを直接入力する場合もあるので拡張子で判断
										var filePath = $(this).find("span.url").html();
										var fileName = $(this).find("span.name").html();
										var formElements2 = $("#"+dialog.getContentElement('info', 'formElements2').domId);
										
										if(isImage(fileName)){
											formElements2.show(200);
											var rdoSize = $("#"+dialog.getContentElement('info', 'rdoSize').domId);
											rdoSize.find('input[type=radio]').attr('disabled', true);
											rdoSize.find('input[type=radio]').eq(0).attr('disabled', false);
											if($(this).find('.small').html()){
												rdoSize.find('input[type=radio]').eq(1).attr('checked', true);
												rdoSize.find('input[type=radio]').eq(1).attr('disabled', false);
											}else{
												rdoSize.find('input[type=radio]').eq(0).attr('checked', true);
											}
											if($(this).find('.midium').html()){
												rdoSize.find('input[type=radio]').eq(2).attr('disabled', false);
											}
											if($(this).find('.large').html()){
												rdoSize.find('input[type=radio]').eq(3).attr('disabled', false);
											}
										}else{
											// 縦間隔・横間隔・行揃え・サイズを非表示
											formElements2.hide(200);
										}

										/* 対象サイズのURLと説明文をセットする */
										dialog.setValueOf('info','txtUrl',getFilePath(filePath,dialog.getValueOf('info','rdoSize')));
										dialog.setValueOf( 'info', 'txtAlt' , $(this).find('.alt').html());
										
									});
									
									$(".selectable-file").unbind('dblclick.dblclickEvent');

								});
								
								$("#FileList"+listId).bind('deletecomplete',function(){
									dialog.setValueOf( 'info', 'txtUrl', '');
									dialog.setValueOf( 'info', 'txtAlt', '');
								});
								
							},
							error: function(msg,textStatus, errorThrown) {
								alert(textStatus);
							}
						});
					}
				}
				]
			}
			]
		};
	});
}
/**
 * 画像ファイルかどうかを判断する
 */
function isImage(url){
	ret = url.match(/.*?\.([a-zA-Z0-9]*?)$/);
	if(ret){
		ext = ret[1];
		if(ext == 'png' || ext == 'gif' || ext == 'jpg'){
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}
/**
 * ファイル名を取得する
 */
function getFileName(url,size){
	var ret,file,ext,fileName;
	ret = url.match(/\/([^\/]*?)\.([a-zA-Z0-9]*?)$/);
	if(ret){
		file = decodeURI(ret[1].replace(/__[a-z]*?$/, ''));
		ext = ret[2];
		if(size && isImage(url)){
			fileName = file + '__' + size + '.' + ext;
		}else{
			fileName = file + '.' + ext;
		}
		return fileName;
	}else{
		return url;
	}
}
/**
 * ファイルパスを取得する
 */
function getFilePath(url,size){
	var ret,fileName;
	fileName = getFileName(url,size);
	ret = url.match(/^(.*\/)([^\/]*?)\.([a-zA-Z0-9]*?)$/);
	if(ret){
		return ret[1]+fileName;
	}else{
		return url;
	}
}
/**
 * ファイル名からファイルのサイズを取得
 */
function getSizeByFile(url){
	var ret = url.match(/__([a-z]*?)\.[a-zA-Z0-9]*?$/);
	if(ret){
		return ret[1];
	}else{
		return '';
	}
}