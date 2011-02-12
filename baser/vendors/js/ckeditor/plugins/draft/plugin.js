/* SVN FILE: $Id$ */
/**
 * Draft Plugin
 *
 * 編集領域を草稿モードと本稿モードに切り替える事ができる
 * ※ jQueryが必須
 * ※ 現時点では、保存前にフィールドへの同期が必要
 * （例）editor.execCommand('synchronize');
 *
 * TODO キー押下時のイベントを拾って反映させるか検討が必要
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
CKEDITOR.plugins.add('draft',
{
/**
 * 初期化処理
 */
	init : function( editor )
	{
		editor.addCommand( 'changeDraft', CKEDITOR.plugins.draft.commands.changeDraft );
		editor.addCommand( 'changePublish', CKEDITOR.plugins.draft.commands.changePublish );
		editor.addCommand( 'copyDraft', CKEDITOR.plugins.draft.commands.copyDraft );
		editor.addCommand( 'copyPublish', CKEDITOR.plugins.draft.commands.copyPublish );
		editor.addCommand( 'disableDraft', CKEDITOR.plugins.draft.commands.disableDraft );
		editor.addCommand( 'disablePublish', CKEDITOR.plugins.draft.commands.disablePublish );
		editor.addCommand( 'synchronize', CKEDITOR.plugins.draft.commands.synchronize );
		editor.addCommand( 'changeReadOnly', CKEDITOR.plugins.draft.commands.changeReadOnly );
		// モード（publish Or draft）
		editor.draftMode = '';
		// 本稿用フィールドID
		editor.draftPublishAreaId = 'Publish';
		// 草稿用フィールドID
		editor.draftDraftAreaId = 'Draft';
		// 本稿読み込み専用
		editor.draftReadOnlyPublish = false;
		// 草稿利用可否
		editor.draftDraftAvailable = true;
		// 本稿利用可否
		editor.draftPublishAvailable = true;

		CKEDITOR.plugins.readyOnly.readyOnlyCommands = { readonly:1,maximize:1,showblocks:1,showborders:1,preview:1,save:1,copy:1,print:1,selectAll:1,about:1, changeDraft:1, changePublish:1, copyPublish:1, 'copyDraft':1 };
		if ( editor.ui.addButton )
		{
			editor.ui.addButton( 'Draft', { label : '草　稿', command : 'changeDraft'});
			editor.ui.addButton( 'Publish', { label : '本　稿', command : 'changePublish'});
			editor.ui.addButton( 'CopyDraft', { label : '草稿を本稿にコピー', command : 'copyDraft'});
			editor.ui.addButton( 'CopyPublish', { label : '本稿を草稿にコピー', command : 'copyPublish'});
		}
		editor.on('pluginsLoaded', function(event) {
			event.editor.draftMode = 'publish';
		});
		editor.on('instanceReady', function(event) {
			if(editor.draftReadOnlyPublish) {
				if(event.editor.draftMode == 'publish') {
					editor.readOnly(true);
				}
				event.editor.getCommand('copyDraft').setState(CKEDITOR.TRISTATE_DISABLED);
			}
		});
		editor.on('mode', function(event) {
			event.editor.getCommand('copyPublish').setState(CKEDITOR.TRISTATE_OFF);
			if(editor.draftReadOnlyPublish) {
				event.editor.getCommand('copyDraft').setState(CKEDITOR.TRISTATE_DISABLED);
			} else {
				event.editor.getCommand('copyDraft').setState(CKEDITOR.TRISTATE_OFF);
			}
			if(event.editor.draftDraftAvailable) {
				event.editor.getCommand('changeDraft').setState(CKEDITOR.TRISTATE_OFF);
			} else {
				event.editor.getCommand('changeDraft').setState(CKEDITOR.TRISTATE_DISABLED);
				event.editor.getCommand('copyPublish').setState(CKEDITOR.TRISTATE_DISABLED);
				event.editor.getCommand('copyDraft').setState(CKEDITOR.TRISTATE_DISABLED);
			}
			if(event.editor.draftPublishAvailable) {
				event.editor.getCommand('changePublish').setState(CKEDITOR.TRISTATE_OFF);
			} else {
				event.editor.getCommand('changePublish').setState(CKEDITOR.TRISTATE_DISABLED);
				event.editor.getCommand('copyPublish').setState(CKEDITOR.TRISTATE_DISABLED);
				event.editor.getCommand('copyDraft').setState(CKEDITOR.TRISTATE_DISABLED);
			}

			CKEDITOR.plugins.draft.commands.setBackGroundColor.exec(editor);
			if(event.editor.draftMode == 'draft') {
				event.editor.getCommand('changeDraft').setState(CKEDITOR.TRISTATE_ON);
			} else if(event.editor.draftMode == 'publish') {
				event.editor.getCommand('changePublish').setState(CKEDITOR.TRISTATE_ON);
			}
			if(event.editor.mode == 'source') {
				event.editor.addCommand( 'synchronize', CKEDITOR.plugins.draft.commands.synchronize );
			}
		});
	}
});

CKEDITOR.plugins.draft =
{
/**
 * コマンド
 */
	commands :
	{
	/**
	 * 草稿に切り替える
	 */
		changeDraft :
		{
			exec : function( editor ) {
				if(editor.getCommand('changeDraft').state==CKEDITOR.TRISTATE_OFF) {
					editor.getCommand('changeDraft').setState(editor.getCommand('changeDraft').state!=CKEDITOR.TRISTATE_ON?CKEDITOR.TRISTATE_ON:CKEDITOR.TRISTATE_OFF);
					editor.getCommand('changePublish').setState(editor.getCommand('changePublish').state!=CKEDITOR.TRISTATE_ON?CKEDITOR.TRISTATE_ON:CKEDITOR.TRISTATE_OFF);
					$('#'+editor.draftPublishAreaId).val(editor.getData());
					editor.setData($('#'+editor.draftDraftAreaId).val());
					editor.draftMode = 'draft';
					editor.readOnly(false);			
				}
				CKEDITOR.plugins.draft.commands.setBackGroundColor.exec(editor);
			},
			canUndo : false
		},
	/**
	 * 本稿に切り替える
	 */
		changePublish :
		{
			exec : function( editor ) {
				if(editor.getCommand('changePublish').state==CKEDITOR.TRISTATE_OFF) {
					editor.getCommand('changePublish').setState(editor.getCommand('changePublish').state!=CKEDITOR.TRISTATE_ON?CKEDITOR.TRISTATE_ON:CKEDITOR.TRISTATE_OFF);
					editor.getCommand('changeDraft').setState(editor.getCommand('changeDraft').state!=CKEDITOR.TRISTATE_ON?CKEDITOR.TRISTATE_ON:CKEDITOR.TRISTATE_OFF);
					$('#'+editor.draftDraftAreaId).val(editor.getData());
					editor.setData($('#'+editor.draftPublishAreaId).val());
					editor.draftMode = 'publish';
					if (editor.draftReadOnlyPublish) {
						editor.readOnly(true);
					}
				}
				CKEDITOR.plugins.draft.commands.setBackGroundColor.exec(editor);
			},
			state: CKEDITOR.TRISTATE_ON,
			canUndo : false
		},
	/**
	 * 草稿を本稿にコピーする
	 */
		copyDraft :
		{
			exec : function( editor ) {
				if(editor.getCommand('changeDraft').state == CKEDITOR.TRISTATE_ON){
					$('#'+editor.draftDraftAreaId).val(editor.getData());
				}
				$('#'+editor.draftPublishAreaId).val(($('#'+editor.draftDraftAreaId).val()));
				if(editor.getCommand('changeDraft').state == CKEDITOR.TRISTATE_ON){
					editor.getCommand('changePublish').exec(editor);
				} else {
					editor.setData($("#"+editor.draftPublishAreaId).val());
				}
				CKEDITOR.plugins.draft.commands.setBackGroundColor.exec(editor);
			},
			canUndo : false
		},
	/**
	 * 本稿を草稿にコピーする
	 */
		copyPublish :
		{
			exec : function( editor ) {
				if(editor.getCommand('changePublish').state == CKEDITOR.TRISTATE_ON){
					$('#'+editor.draftPublishAreaId).val(editor.getData());
				}
				$('#'+editor.draftDraftAreaId).val(($('#'+editor.draftPublishAreaId).val()));
				if(editor.getCommand('changePublish').state == CKEDITOR.TRISTATE_ON){
					editor.getCommand('changeDraft').exec(editor);
				} else {
					editor.setData($("#"+editor.draftDraftAreaId).val());
				}
				CKEDITOR.plugins.draft.commands.setBackGroundColor.exec(editor);
			},
			canUndo : false
		},
	/**
	 * 草稿機能を無効にする
	 */
		disableDraft :
		{
			exec : function( editor ) {
				editor.getCommand('changePublish').setState(CKEDITOR.TRISTATE_ON);
				editor.getCommand('changeDraft').setState(CKEDITOR.TRISTATE_DISABLED);
				editor.getCommand('copyPublish').setState(CKEDITOR.TRISTATE_DISABLED);
				editor.getCommand('copyDraft').setState(CKEDITOR.TRISTATE_DISABLED);
				editor.draftMode = 'publish';
				editor.draftDraftAvailable = false;
			},
			canUndo : false
		},
	/**
	 * 本稿機能を無効にする
	 */
		disablePublish :
		{
			exec : function( editor ) {
				editor.getCommand('changePublish').setState(CKEDITOR.TRISTATE_DISABLED);
				editor.getCommand('changeDraft').setState(CKEDITOR.TRISTATE_ON);
				editor.getCommand('copyPublish').setState(CKEDITOR.TRISTATE_DISABLED);
				editor.getCommand('copyDraft').setState(CKEDITOR.TRISTATE_DISABLED);
				editor.draftMode = 'draft';
				editor.draftPublishAvailable = false;
			},
			canUndo : false
		},
	/**
	 * CKEditorの編集内容をフィールドに反映する
	 * 保存前に呼び出す必要あり
	 */
		synchronize :
		{
			exec : function ( editor ) {
				if(editor.getCommand('changeDraft').state == CKEDITOR.TRISTATE_ON) {
					$("#"+editor.draftDraftAreaId).val(editor.getData());
				}else if(editor.getCommand('changePublish').state == CKEDITOR.TRISTATE_ON) {
					$("#"+editor.draftPublishAreaId).val(editor.getData());
				}
			},
			canUndo : false
		},
	/**
	 * 草稿モードに合わせて背景色を変更する
	 */
		setBackGroundColor :
		{
			exec : function (editor) {
				var color;
				if(editor.draftMode == 'draft') {
					color = '#EEF';
				} else {
					color = '#FFF';
				}
				if(editor.mode == 'wysiwyg') {
					setTimeout(function(){
						$('#cke_contents_'+editor.name+' iframe').contents().find('body').css('background-color',color);
					}, 200);
				} else if(editor.mode == 'source') {
					$('#cke_contents_'+editor.name+' textarea').css('background-color', color);
				}
			},
			canUndo : false
		}
	}
};