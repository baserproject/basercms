window.onload = function() {
    let field = ckeditor_field;
    let initStatus = false;
    let styleInitStatus = false;
    const initialStyle = initial_style;
    const editorStyle = editor_style;
    const editorUrl = editor_url;
    const editorStylesSet = editor_styles_set;
    const editorEnterBr = parseInt(editor_enter_br);
    const themeEditorCsses = theme_editor_csses;
    const editorDomId = editor_dom_id;
    const editorOptions = editor_options;
    const editorUseDraft = Boolean(use_draft);
    const draftAreaId = draft_area_id;
    const publishAreaId = publish_area_id;
    const editorReadOnlyPublish = editor_readonly_publish;
    const fieldCamelize = field_camelize;
    const editorDisableDraft = editor_disable_draft;
    const editorDisablePublish = editor_disable_publish;

    $(window).on('load', function() {
        if (!initStatus) {
            CKEDITOR.addStylesSet('basercms', initialStyle);
            initStatus = true;
        }
        if (!styleInitStatus && editorStyle) {
            editorStyle.map((key, editor) => CKEDITOR.addStylesSet(key, editor));
            styleInitStatus = true;
        }
        if (editorUrl) {
            CKEDITOR.config.templates_files = [editorUrl];
        }
        CKEDITOR.config.allowedContent = true;
        CKEDITOR.config.extraPlugins = 'draft,showprotected';
        CKEDITOR.config.stylesCombo_stylesSet = editorStylesSet;
        CKEDITOR.config.protectedSource.push( /<\?[\s\S]*?\?>/g );
        // 空「i」タグを消さないようにする
        CKEDITOR.dtd.$removeEmpty["i"] = false;
        // 空「span」タグを消さないようにする
        CKEDITOR.dtd.$removeEmpty["span"] = false;
        if (editorEnterBr) {
            CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
        }
        // TODO ucmitz: contentcssに複数入ってるか確認する
        themeEditorCsses.map((css) => CKEDITOR.config.contentsCss = [css]);
        field = CKEDITOR.replace(editorDomId, editorOptions);
        field.on('pluginsLoaded', function(event) {
            if (editorUseDraft) {
                if (draftAreaId) {
                    field.draftDraftAreaId = draftAreaId;
                }
                if (publishAreaId) {
                    field.draftPublishAreaId = publishAreaId;
                }
                if (editorReadOnlyPublish) {
                    field.draftReadOnlyPublish = true;
                }
            }
        });
        if (editorUseDraft) {
            field.on('instanceReady', function(event) {
                if (editorDisableDraft) {
                    field.execCommand('changePublish');
                    field.execCommand('disableDraft');
                }
                if (editorDisablePublish) {
                    field.execCommand('changeDraft');
                    field.execCommand('disablePublish');
                }
                field.on('beforeCommandExec', function( ev ){
                    if(ev.data.name === 'changePublish' || ev.data.name === 'copyPublish') {
                        $(`#DraftMode${fieldCamelize}`).val('publish');
                    } else if(ev.data.name === 'changeDraft' || ev.data.name === 'copyDraft') {
                        $(`#DraftMode${fieldCamelize}`).val('draft');
                    }
                });
            });
        }
        field.on('instanceReady', function(event) {
            if(field.getCommand('maximize').uiItems.length > 0) {
                // ツールバーの表示を切り替え
                $field.getCommand('maximize').on( 'state' ,
                (e) => {
                    if(this.state == 1) {
                        $("#ToolBar").hide();
                    } else {
                        $("#ToolBar").show();
                    }
                });
            }
        });
    });
}
